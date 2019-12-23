<?php
namespace yoo\behaviors;

use yii\helpers\Json;

/**
 * AttributeTypecastBehavior
 * 字段类型映射行为
 * -------------------------
 * @version 1.0.0
 * @author Verdient。
 */
class AttributeTypecastBehavior extends \yii\behaviors\AttributeTypecastBehavior
{
	/**
	 * @type const TYPE_JSON
	 * JSON
	 * ---------------------
	 * @author Verdient。
	 */
	const TYPE_JSON = 'json';

	/**
	 * @var const public $typecastAfterValidate
	 * 校验后映射
	 * ----------------------------------------
	 * @author Verdient。
	 */
	public $typecastAfterValidate = false;

	/**
	 * @var const public $typecastBeforeSave
	 * 保存前映射
	 * -------------------------------------
	 * @author Verdient。
	 */
	public $typecastBeforeSave = true;

	/**
	 * @var const public $typecastAfterFind
	 * 查询后映射
	 * ------------------------------------
	 * @author Verdient。
	 */
	public $typecastAfterFind = true;

	/**
	 * typecastValue(Mixed $value, String $type)
	 * 类型映射
	 * -----------------------------------------
	 * @param Mixed $value 属性值
	 * @param String $type 类型
	 * --------------------------
	 * @author Verident。
	 */
	protected function typecastValue($value, $type){
		if(is_scalar($type)){
			if(is_object($value) && method_exists($value, '__toString')){
				$value = $value->__toString();
			}
			switch($type){
				case self::TYPE_INTEGER:
					return (int) $value;
				case self::TYPE_FLOAT:
					return (float) $value;
				case self::TYPE_BOOLEAN:
					return (bool) $value;
				case self::TYPE_STRING:
					return (string) $value;
				case self::TYPE_JSON:
					if(is_array($value) || is_object($value)){
						return empty($value) ? null : Json::encode($value);
					}
					$typecastValue = json_decode($value);
					if(json_last_error() == JSON_ERROR_NONE){
						return $typecastValue;
					}
					return json_encode($value);
				default:
					throw new InvalidParamException("Unsupported type '{$type}'");
			}
		}
		return call_user_func($type, $value);
	}
}