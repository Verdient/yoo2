<?php
namespace yoo\db;

use Yii;
use yii\base\InvalidCallException;
use yii\db\StaleObjectException;
use yoo\behaviors\AttributeTypecastBehavior;
use yoo\traits\ModelTrait;

/**
 * ActiveRecord
 * 动态记录
 * ------------
 * @author Verdient。
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
	use ModelTrait;

	/**
	 * @var const STATUS_REGULAR
	 * 正常
	 * -------------------------
	 * @author Verdient。
	 */
	const STATUS_REGULAR = 1;

	/**
	 * @var const STATUS_DELETED
	 * 已删除
	 * -------------------------
	 * @author Verdient。
	 */
	const STATUS_DELETED = 100;

	/**
	 * @var $_instance
	 * 实例
	 * --------------
	 * @author Verdient。
	 */
	protected $_instance;

	/**
	 * behaviors()
	 * 添加行为
	 * -----------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function behaviors(){
		return [
			'Timestamp' => 'yii\behaviors\TimestampBehavior',
			'PrimaryKey' => 'yoo\behaviors\PrimaryKeyBehavior',
			'Status' => 'yoo\behaviors\StatusBehavior',
			'AttributeTypecast' => [
				'class' => 'yoo\behaviors\AttributeTypecastBehavior',
				'attributeTypes' => [
					'status' => AttributeTypecastBehavior::TYPE_INTEGER,
					'created_at' => AttributeTypecastBehavior::TYPE_INTEGER,
					'updated_at' => AttributeTypecastBehavior::TYPE_INTEGER
				]
			]
		];
	}

	/**
	 * getInstance()
	 * 获取实例
	 * -------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getInstance(){
		return $this->_instance;
	}

	/**
	 * setInstance(Mixed $value)
	 * 获取实例
	 * -------------------------
	 * @param Mixed $value 内容
	 * -----------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function setInstance($value){
		$this->_instance = $value;
		return $this;
	}

	/**
	 * softDelete()
	 * 软删除
	 * ------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function softDelete(){
		return false;
	}

	/**
	 * getIdLabel()
	 * 获取编号标签
	 * ------------
	 * @author Verdient。
	 */
	public function getIdLabel(){
		if(mb_strlen($this->id) > 8){
			return mb_substr($this->id, 0, 8);
		}
		return $this->id;
	}

	/**
	 * labelMap()
	 * 标签映射关系
	 * ----------
	 * @return Array
	 * @author Verdient。
	 */
	public static function labelMap(){
		return [
			'status' => [
				static::STATUS_REGULAR => 'Regular',
				static::STATUS_DELETED => 'Deleted'
			]
		];
	}

	/**
	 * label(String $attribute, String $value)
	 * 标签
	 * ---------------------------------------
	 * @param String $attribute 属性
	 * @param String $value 值
	 * -----------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function label($attribute, $value){
		$map = static::labelMap();
		if(isset($map[$attribute]) && is_array($map[$attribute])){
			$map = $map[$attribute];
			if(isset($map[$value])){
				return static::translate($attribute, $map[$value]);
			}
		}
		return static::translateErrorMessage('Unknown');
	}

	/**
	 * getStatusLabel()
	 * 获取状态标签
	 * ----------------
	 * @return String
	 * @author Verdient。
	 */
	public function getStatusLabel(){
		return static::label('status', $this->status);
	}

	/**
	 * sync()
	 * 同步
	 * ------
	 * @return Self
	 * @author Verdient。
	 */
	public function sync(){
		if(!empty($this->instance)){
			$attributes = $this->getDirtyAttributes();
			$scenario = $this->getScenario();
			if($this->instance instanceof static){
				$this->instance->setScenario($scenario);
				$this->instance->load($attributes);
			}else if(is_array($this->instance)){
				foreach($this->instance as &$instance){
					if($instance instanceof static){
						$instance->setScenario($scenario);
						$instance->load($attributes);
					}else{
						throw new InvalidCallException('instance must instanceof ' . static::class);
					}
				}
			}else{
				throw new InvalidCallException('instance must instanceof ' . static::class);
			}
		}
		return $this;
	}

	/**
	 * change([Boolean $runSync = true, Boolean $runValidation = true, Array $attributeNames = null])
	 * 编辑保存
	 * ----------------------------------------------------------------------------------------------
	 * @param Boolean $runSync 是否运行同步
	 * @param Boolean $runValidation 是否运行校验
	 * @param Array $attributeNames 要保存的字段名
	 * ------------------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function change($runSync = true, $runValidation = false, $attributeNames = null){
		if($runSync === true){
			$this->sync();
		}
		return $this->modify($runValidation, $attributeNames);
	}

	/**
	 * modify([Boolean $runValidation = true, Array $attributeNames = null])
	 * 修改
	 * ---------------------------------------------------------------------
	 * @param Boolean $runValidation 是否运行校验
	 * @param Array $attributeNames 要保存的字段名
	 * ------------------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function modify($runValidation = false, $attributeNames = null){
		if(!empty($this->instance)){
			if($this->instance instanceof static){
				$this->instance->save($runValidation, $attributeNames);
			}else if(is_array($this->instance)){
				foreach($this->instance as &$instance){
					if($instance instanceof static){
						$instance->save($runValidation, $attributeNames);
					}else{
						throw new InvalidCallException('instance must instanceof ' . static::class);
					}
				}
			}else{
				throw new InvalidCallException('instance must instanceof ' . static::class);
			}
		}
		return $this;
	}

	/**
	 * batchInsert( Array $models)
	 * 批量插入
	 * ---------------------------
	 * @param Array $models 模型集合
	 * ----------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public static function batchInsert($models){
		if(empty($models)){
			return 0;
		}
		$attributes = array_keys($models[0]);
		$data = [];
		foreach($models as $model){
			$t = [];
			foreach($attributes as $attribute){
				$t[] = $model[$attribute];
			}
			$data[] = $t;
		}
		return static::getDb()->createCommand()->batchInsert(static::tableName(), $attributes, $data)->execute();
	}

	/**
	 * upsert(Array $insertColumns, Array|Boolean $updateColumns, Array $params = [])
	 * 批量插入或更新
	 * ------------------------------------------------------------------------------
	 * @param Array $insertColumns 插入的字段
	 * @param Array|Boolean $updateColumns 更新的字段
	 * @param Array $params 其他参数
	 * --------------------------------------------
	 * @return int
	 * @author Verdient。
	 */
	public static function upsert($insertColumns, $updateColumns = true, $params = []){
		return static::getDb()->createCommand()->upsert(static::tableName(), $insertColumns, $updateColumns, $params)->execute();
	}

	/**
	 * batchUpsert(Array $models)
	 * 批量插入
	 * --------------------------
	 * @param Array $models 模型集合
	 * ----------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public static function batchUpsert($models){
		if(empty($models)){
			return 0;
		}
		$count = 0;
		foreach($models as $model){
			$count += static::upsert($model, true);
		}
		return $count;
	}

	/**
	 * resolveFields([Array $fields = [], Array $expand = []])
	 * 处理字段
	 * -------------------------------------------------------
	 * @param Array $fields 字段集合
	 * @param Array $expand 拓展字段
	 * ---------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function resolveFields(array $fields, array $expand){
		$fields = $this->extractRootFields($fields);
		$expand = $this->extractRootFields($expand);
		$result = [];
		foreach($this->fields() as $field => $definition){
			if (is_int($field)) {
				$field = $definition;
			}
			if(empty($fields) || in_array($field, $fields, true)){
				$result[$field] = $definition;
			}
		}
		if(empty($expand)){
			return $result;
		}
		$extraFields = $this->extraFields();
		if(empty($extraFields)){
			$this->addError('expand', 'no expand are available');
		}else{
			foreach($extraFields as $field => $definition){
				if(is_int($field)){
					$extraFields[$definition] = $definition;
					unset($extraFields[$field]);
				}
			}
			foreach($expand as $field){
				if(isset($extraFields[$field])){
					$result[$field] = $extraFields[$field];
				}else{
					$this->addError('expand', 'expand must in the follow value: ' . implode(',', array_keys($extraFields)));
				}
			}
		}
		return $result;
	}

	/**
	 * deleteInternal()
	 * 删除
	 * ----------------
	 * @inheritdoc
	 * -----------
	 * @return Integer|False
	 * @author Verdient。
	 */
	protected function deleteInternal(){
		if(!static::softDelete() === true){
			return parent::deleteInternal();
		}else{
			if(!$this->beforeDelete()){
				return false;
			}
			$condition = $this->getOldPrimaryKey(true);
			$lock = $this->optimisticLock();
			if($lock !== null){
				$condition[$lock] = $this->$lock;
			}
			$result = static::updateAll(['status' => static::STATUS_DELETED], $condition);
			if($lock !== null && !$result){
				throw new StaleObjectException('The object being deleted is outdated.');
			}
			$this->setOldAttributes(null);
			$this->afterDelete();
			return $result;
		}
	}

	/**
	 * deleteAll([Array $condition = null, Array $params = []])
	 * 删除所有
	 * --------------------------------------------------------
	 * @param Array $condition 条件
	 * @param Array $params 附加参数
	 * ----------------------------
	 * @inheritdoc
	 * -----------
	 * @return Integer
	 * @author Verdient。
	 */
	public static function deleteAll($condition = null, $params = []){
		if(static::softDelete() === true){
			$command = static::getDb()->createCommand();
			$command->update(static::tableName(), ['status' => static::STATUS_DELETED], $condition, $params);
			return $command->execute();
		}
		return parent::deleteAll($condition, $params);
	}

	/**
	 * find()
	 * 查找
	 * ------
	 * @inheritdoc
	 * -----------
	 * @return ActiveQuery
	 * @author Verdient。
	 */
	public static function find(){
		return Yii::createObject(ActiveQuery::class, [get_called_class()]);;
	}

	/**
	 * getDirtyAttributes([Array $names = null])
	 * 获取变化的字段
	 * -----------------------------------------
	 * @param Array $names 属性名称集合
	 * -------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getDirtyAttributes($names = null){
		$attributes = parent::getDirtyAttributes($names);
		foreach($attributes as $name => $value){
			$oldValue = $this->getOldAttribute($name);
			if(is_numeric($value)){
				$value = (String) $value;
			}
			if(is_numeric($oldValue)){
				$oldValue = (String) $oldValue;
			}
			if($value === $oldValue){
				unset($attributes[$name]);
			}
		}
		return $attributes;
	}

	/**
	 * getDirtyValues([Array $names = null])
	 * 获取属性变化情况
	 * -------------------------------------
	 * @param Array $names 属性名称集合
	 * -------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getDirtyValues($names = null){
		$updatedValues = [];
		foreach($this->getDirtyAttributes($names) as $name => $value){
			$oldValue = $this->getOldAttribute($name);
			if(is_numeric($value)){
				$value = (String) $value;
			}
			if(is_numeric($oldValue)){
				$oldValue = (String) $oldValue;
			}
			$updatedValues[$name] = [$oldValue, $value];
		}
		return $updatedValues;
	}

	/**
	 * truncate()
	 * 截断表
	 * ----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function truncate(){
		static::getDb()->createCommand()->truncateTable(static::tableName())->execute();
		return true;
	}
}