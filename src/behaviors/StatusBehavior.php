<?php
namespace yoo\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * StatusBehavior
 * 状态行为
 * --------------
 * @author Verdient。
 */
class StatusBehavior extends Behavior
{
	/**
	 * @var $attribute
	 * 字段名称
	 * ---------------
	 * @author Verdient。
	 */
	public $attribute = 'status';

	/**
	 * @var $defaultStatus
	 * 默认状态
	 * -------------------
	 * @author Verdient。
	 */
	public $defaultStatus = 'STATUS_REGULAR';

	/**
	 * events()
	 * 附加事件
	 * --------
	 * @return Array
	 * @author Verdient。
	 */
	public function events(){
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'appendStatus'
		];
	}

	/**
	 * appendStatus()
	 * 附加状态
	 * --------------
	 * @author Verdient。
	 */
	public function appendStatus(){
		$owner = $this->owner;
		$attributes = $owner->getAttributes();
		if(!isset($attributes[$this->attribute])){
			$attribute = $this->attribute;
			$reflectionClass = new \ReflectionClass($owner);
			if($status = $reflectionClass->getConstant($this->defaultStatus)){
				$owner->$attribute = $status;
			}
		}
	}
}