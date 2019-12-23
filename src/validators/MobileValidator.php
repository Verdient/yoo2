<?php
namespace yoo\validators;

/**
 * MobileValidator Model
 * 手机号验证器 模型
 * ---------------------
 * @author Verdient。
 */
class MobileValidator extends Validator
{
	/**
	 * @var $pattern
	 * 正则表达式
	 * -------------
	 * @author Verdient。
	 */
	public $pattern = '/^(13[0-9]|14[57]|15[012356789]|16[68]|17[0135678]|18[0-9]|19[189])[0-9]{8}$/';

	/**
	 * @var Boolean $enableInternational
	 * 是否允许国际号码
	 * ---------------------------------
	 * @author Verdient。
	 */
	public $enableInternational = true;

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
		if($this->message === null){
			$this->message = '{attribute} is not a valid mobile';
		}
	}

	/**
	 * validateAttribute(Object $model, String $attribute)
	 * 验证属性
	 * ---------------------------------------------------
	 * @param Object $model 模型实例对象
	 * @param String $attribute 字段名称
	 * --------------------------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function validateAttribute($model, $attribute){
		parent::validateAttribute($model, $attribute);
		if(mb_substr($model->$attribute, 0, 3) === '+86'){
			$model->$attribute = mb_substr($model->$attribute, 3);
		}
	}

	/**
	 * validateValue(Mixed $value)
	 * 验证属性
	 * ---------------------------
	 * @param Mixed $value 验证值
	 * --------------------------
	 * @author Verdient。
	 */
	public function validateValue($value){
		if($this->enableInternational){
			$inland = false;
			if(mb_substr($value, 0, 3) === '+86'){
				$inland = true;
				$value = mb_substr($value, 3);
			}else if(mb_substr($value, 0, 1) === '+'){
				$value = mb_substr($value, 1);
			}
			if(!is_numeric($value)){
				return [$this->message, []];
			}
			if(!$inland){
				return null;
			}
		}
		if(!preg_match($this->pattern, $value)){
			return [$this->message, []];
		}
		return null;
	}
}