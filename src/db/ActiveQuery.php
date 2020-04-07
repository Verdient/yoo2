<?php
namespace yoo\db;

use yii\db\Expression;

/**
 * ActiveQuery
 * 动态查询
 * -----------
 * @author Verdient。
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
	/**
	 * @var $_includeDeleted
	 * 是否包含已删除的数据
	 * --------------------
	 * @author Verdient。
	 */
	protected $_includeDeleted = false;

	/**
	 * @var Boolean $_forUpdate
	 * 是否锁定数据
	 * ------------------------
	 * @author Verdient。
	 */
	protected $_forUpdate = false;

	/**
	 * includeDeleted([Boolean $flag = true])
	 * 包含已删除的数据
	 * --------------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function includeDeleted($flag = true){
		$this->_includeDeleted = $flag;
		return $this;
	}

	/**
	 * forUpdate([Boolean $flag = true])
	 * 锁定数据
	 * ----------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function forUpdate($flag = true){
		$this->_forUpdate = $flag;
		return $this;
	}

	/**
	 * createCommand([Object $db = null])
	 * 创建指令
	 * ----------------------------------
	 * @param Object $db 数据库对象
	 * ---------------------------
	 * @inheritdoc
	 * -----------
	 * @return Command
	 * @author Verdient。
	 */
	public function createCommand($db = null){
		if($this->_includeDeleted !== true){
			$modelClass = $this->modelClass;
			$this->andWhere(['!=', new Expression($modelClass::tableName() . '.`status`'), $modelClass::STATUS_DELETED]);
		}
		return parent::createCommand($db);
	}
}