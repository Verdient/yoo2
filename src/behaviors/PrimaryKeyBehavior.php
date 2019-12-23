<?php
namespace yoo\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yoo\helpers\UUIDHelper;

/**
 * PrimaryKeyBehavior
 * 主键行为
 * ------------------
 * @author Verdient。
 */
class PrimaryKeyBehavior extends Behavior
{
	/**
	 * events()
	 * 附加事件
	 * --------
	 * @return Array
	 * @author Verdient。
	 */
	public function events(){
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'appendPrimaryKey'
		];
	}

	/**
	 * appendPrimaryKey()
	 * 附加主键
	 * ------------------
	 * @author Verdient。
	 */
	public function appendPrimaryKey(){
		$owner = $this->owner;
		$tableSchema = $owner->getTableSchema();
		$primaryKey = $tableSchema->primaryKey;
		$attributes = $owner->getAttributes();
		if(isset($primaryKey[0]) && !isset($attributes[$primaryKey[0]])){
			$primaryKey = $primaryKey[0];
			$columnSchema = $tableSchema->columns[$primaryKey];
			if($columnSchema->type === 'string'){
				$owner->$primaryKey = UUIDHelper::uuid1();
			}
		}
	}
}