<?php
namespace yoo\behaviors;

use yii\base\Controller;
use yii\di\Instance;
use yoo\helpers\ExceptionHelper;

/**
 * TransactionBehavior
 * 事务行为
 * -------------------
 * @author Verdient。
 */
class TransactionBehavior extends \yii\base\Behavior
{
	/**
	 * @var $db
	 * 数据库
	 * --------
	 * @author Verdient。
	 */
	public $db = [];

	/**
	 * @var $isolationLevel
	 * 隔离等级
	 * --------------------
	 * @author Verdient。
	 */
	public $isolationLevel = null;

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
		foreach($this->db as &$db){
			$db = Instance::ensure($db);
		}
	}

	/**
	 * events()
	 * 事件设置
	 * --------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function events(){
		return [
			Controller::EVENT_BEFORE_ACTION => 'beginTransaction',
			Controller::EVENT_AFTER_ACTION => 'endTransacion'
		];
	}

	/**
	 * beginTransaction(Event $event)
	 * 开始事务
	 * ------------------------------
	 * @param Event $event 事务对象
	 * ---------------------------
	 * @author Verdient。
	 */
	public function beginTransaction($event){
		foreach($this->db as $db){
			$db->beginTransaction($this->isolationLevel);
		}
	}

	/**
	 * endTransacion(Event $event)
	 * 结束事务
	 * ---------------------------
	 * @param Event $event 事务对象
	 * ---------------------------
	 * @author Verdient。
	 */
	public function endTransacion($event){
		$method = 'commit';
		if(ExceptionHelper::isException($event->result)){
			$method = 'rollback';
		}
		foreach($this->db as $db){
			$transaction = $db->getTransaction();
			if($transaction && $transaction->getIsActive()){
				$transaction->$method();
			}
		}
	}
}