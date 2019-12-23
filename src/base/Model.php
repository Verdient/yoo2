<?php
namespace yoo\base;

use yoo\traits\ModelTrait;

/**
 * Model
 * 模型
 * -----
 * @author Verdient。
 */
class Model extends \yii\base\Model
{
	use ModelTrait;

	/**
	 * @var $instance
	 * 实例
	 * --------------
	 * @author Verdient。
	 */
	public $instance;

	/**
	 * @var const STATUS_DEFAULT
	 * 默认状态
	 * -------------------------
	 * @author Verdient
	 */
	const STATUS_DEFAULT = 1;

	/**
	 * fields()
	 * 字段设置
	 * --------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function fields(){
		$fields = parent::fields();
		unset($fields['instance']);
		return $fields;
	}
}