<?php
namespace yoo\validators;

/**
 * InstanceValidator
 * 实例 校验器
 * -----------------
 * @author Verdient。
 */
class InstanceValidator extends Validator
{
	/**
	 * @var public $filter
	 * 过滤器
	 * ----------------
	 * @author Verdient。
	 */
	public $filter = [];

	/**
	 * @var public targetClass
	 * 目标类
	 * -----------------------
	 * @author Verdient。
	 */
	public $targetClass = null;

	/**
	 * @var public $targetAttribute
	 * 目标属性
	 * ----------------------------
	 * @author Verdient。
	 */
	public $targetAttribute = null;

	/**
	 * @var public $multiple
	 * 多个对象
	 * ---------------------
	 * @author Verdient。
	 */
	public $multiple = false;

	/**
	 * @var public $skipOnError
	 * 在错误时是否跳过
	 * ------------------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public $skipOnError = true;

	/**
	 * @var public $select
	 * 字段筛选
	 * ------------------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public $select = null;

	/**
	 * @var $instanceValidate
	 * 校验实例
	 * ----------------------
	 * @author Verdient。
	 */
	public $instanceValidate = null;

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * ------------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		if($this->message === null){
			$this->message = '{attribute} do not match';
		}
	}

	/**
	 * validateAttribute(Object $model, String $attribute)
	 * 验证属性
	 * ---------------------------------------------------
	 * @param Object $model 模型实例对象
	 * @param String $attribute 字段名称
	 * ---------------------------------
	 * @author Verdient。
	 */
	public function validateAttribute($model, $attribute){
		if($this->targetClass === null){
			$class = $model->className();
		}else{
			$class = $this->targetClass;
		}
		if($this->targetAttribute === null){
			$this->targetAttribute = $attribute;
		}
		$query = $class::find()->where([$this->targetAttribute => $model->$attribute]);
		if($this->select !== null){
			$query->select($this->select);
		}
		if(is_callable($this->filter)){
			call_user_func($this->filter, $query);
		}else if($this->filter !== null){
			$query->andWhere($this->filter);
		}
		if($this->multiple === true){
			$model->instance = $query->all();
		}else{
			$model->instance = $query->one();
		}
		if(empty($model->instance)){
			$this->addError($model, $attribute, $this->message);
		}else if(is_callable($this->instanceValidate) && !call_user_func($this->instanceValidate, $model->instance)){
			$this->addError($model, $attribute, $this->message);
		}
	}
}