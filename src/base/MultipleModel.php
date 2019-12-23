<?php
namespace yoo\base;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * MultipleModel
 * 多模型
 * -------------
 * @author Verdient。
 */
class MultipleModel extends Model
{
	/**
	 * @var const MODE_SINGLE
	 * 单类模式
	 * ----------------------
	 * @author Verdient。
	 */
	const MODE_SINGLE = 'single';

	/**
	 * @var const MODE_MULTIPLE
	 * 多类模式
	 * ------------------------
	 * @author Verdient。
	 */
	const MODE_MULTIPLE = 'multiple';

	/**
	 * @var String $class
	 * 类
	 * ------------------
	 * @author Verdient。
	 */
	public $class = null;

	/**
	 * @var Boolean $strict
	 * 是否启用严格模式
	 * --------------------
	 * @author Verdient。
	 */
	public $strict = true;

	/**
	 * @var Array $_models
	 * 模型集合
	 * -------------------
	 * @author Verdient。
	 */
	protected $_models = [];

	/**
	 * @var String $_mode
	 * 模式
	 * ------------------
	 * @author Verdient。
	 */
	protected $_mode = null;

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		if(!$this->class){
			throw new InvalidParamException('model must be set');
		}
		if(is_string($this->class)){
			$this->_mode = static::MODE_SINGLE;
			$model = new $this->class;
			if(!$model instanceof Model){
				throw new InvalidParamException('model must instance of ' . Model::className());
			}
			$this->_models[] = $model;
		}else if(is_array($this->class)){
			$this->_mode = static::MODE_MULTIPLE;
			foreach($this->class as $name => $class){
				$model = new $class;
				if(!$model instanceof Model){
					throw new InvalidParamException('model must instance of ' . Model::className());
				}
				$this->_models[$name] = $model;
			}
		}else{
			throw new InvalidParamException('model must be a string or array');
		}
	}

	/**
	 * getModels()
	 * 获取模型集合
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function getModels(){
		return $this->_models;
	}

	/**
	 * validate()
	 * 校验
	 * ----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function validate($attributeNames = NULL, $clearErrors = true){
		foreach($this->_models as $name => $model){
			if(!$model->validate()){
				$this->addError($name, $model->getErrors());
			}
		}
		return !$this->hasErrors();
	}

	/**
	 * setScenario(String|Array $value)
	 * 设置场景
	 * --------------------------------
	 * @param String|Array 值
	 * ----------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function setScenario($value){
		parent::setScenario($value);
		if(is_string($value)){
			foreach($this->_models as $model){
				$model->setScenario($value);
			}
		}else if(is_array($value)){
			foreach($value as $name => $scenario){
				if(isset($this->_models[$name])){
					$this->_models[$name]->setScenario($scenario);
				}
			}
		}else{
			throw new InvalidParamException('scenario must be a string or array');
		}
		return $this;
	}

	/**
	 * load()
	 * 将数据载入模型
	 * -------------
	 * @param Array $data 数据
	 * @param String $formName 表单名称
	 * -------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function load($data, $formName = null){
		if($formName !== null){
			$data = isset($data[$formName]) ? $data[$formName] : [];
		}
		if($this->_mode === static::MODE_MULTIPLE && $this->strict !== true){
			$models = array_keys($this->_models);
			$submits = array_keys($data);
			$diff = array_diff($models, $submits);
			foreach($diff as $key){
				unset($this->_models[$key]);
			}
		}
		if(is_array($data)){
			foreach($data as $name => $value){
				if(!isset($this->_models[$name])){
					if($this->_mode === static::MODE_SINGLE){
						$this->_models[$name] = (new $this->class)->setScenario($this->_models[0]->getScenario());
					}else{
						continue;
					}
				}
				if($this->_mode === static::MODE_MULTIPLE && !empty($value) && ArrayHelper::isIndexed($value)){
					$model = $this->_models[$name];
					unset($this->_models[$name]);
					$index = 0;
					foreach($value as $row){
						$this->_models[$name . '@' . $index] = clone $model;
						$this->_models[$name . '@' . $index]->load($row);
						$index ++;
					}
				}else{
					$this->_models[$name]->load($value);
				}
			}
		}
		return $this;
	}

	/**
	 * save([Boolean $runValidation = true])
	 * 保存
	 * -------------------------------------
	 * @param Boolean $runValidation 是否运行校验
	 * -----------------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function save($runValidation = true){
		if($runValidation === true && !$this->validate()){
			return false;
		}
		$transaction = Yii::$app->db->beginTransaction();
		$result = true;
		try{
			foreach($this->_models as $model){
				if(!$model->save(false)){
					$result = false;
				}
			}
		}catch(\Exception $e){
			$transaction->rollBack();
			throw $e;
		}
		if($result){
			$transaction->commit();
		}else{
			$transaction->rollBack();
		}
		return $result;
	}

	/**
	 * toArray([Array $fields = [], Array $expand = [], Boolean $recursive = true])
	 * 转换为数组
	 * ----------------------------------------------------------------------------
	 * @param Array $fields 字段
	 * @param Array $expand 扩展字段
	 * @param Array $recursive 是否递归
	 * -------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function toArray(array $fields = [], array $expand = [], $recursive = true){
		$result = [];
		foreach($this->getModels() as $model){
			$result[] = $model->toArray($fields, $expand, $recursive);
		}
		return $result;
	}
}