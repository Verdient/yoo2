<?php
namespace yoo\base;

use Yii;
use yii\base\InvalidValueException;
use yii\di\Instance;

/**
 * QueueProcess
 * 队列处理
 * ------------
 * @author Verdient。
 */
class QueueProcess extends \yii\base\BaseObject
{
	/**
	 * @var Object $queue
	 * 队列
	 * ------------------
	 * @author Verdient。
	 */
	public $queue = 'queue';

	/**
	 * @var String $queueName
	 * 队列名称
	 * ----------------------
	 * @author Verdient。
	 */
	public $queueName = 'queue';

	/**
	 * @var Integer $batchSize
	 * 批处理大小
	 * -----------------------
	 * @author Verdient。
	 */
	public $batchSize = 100;

	/**
	 * @var Boolean $incessancy
	 * 持续处理
	 * ------------------------
	 * @author Verdient。
	 */
	public $incessancy = true;

	/**
	 * @var Integer $emptySleep
	 * 为空时的休息时间
	 * ------------------------
	 * @author Verdient。
	 */
	public $emptySleep = 1;

	/**
	 * @var Callable $_succeed
	 * 成功时的回调函数
	 * ----------------------
	 * @author Verdient。
	 */
	protected $_succeed = null;

	/**
	 * @var Callable $_failed
	 * 失败时的回调函数
	 * ----------------------
	 * @author Verdient。
	 */
	protected $_failed = null;

	/**
	 * @var Callable $_error
	 * 出错时的回调函数
	 * ---------------------
	 * @author Verdient。
	 */
	protected $_error = null;

	/**
	 * @var Callable $_retry
	 * 重试时的回调函数
	 * ---------------------
	 * @author Verdient。
	 */
	protected $_retry = null;

	/**
	 * @var Callable $_finished
	 * 结束时的回调函数
	 * ------------------------
	 * @author Verdient。
	 */
	protected $_finished = null;

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
		$this->queue = Instance::ensure($this->queue);
		if($this->incessancy === true){
			set_time_limit(0);
		}
	}

	/**
	 * succeedListener(Callable $callback)
	 * 成功监听器
	 * -----------------------------------
	 * @author Verdient。
	 */
	public function succeedListener($callback){
		if(is_callable($callback)){
			$this->_succeed = $callback;
		}
	}

	/**
	 * failedListener(Callable $callback)
	 * 失败监听器
	 * ----------------------------------
	 * @author Verdient。
	 */
	public function failedListener($callback){
		if(is_callable($callback)){
			$this->_failed = $callback;
		}
	}

	/**
	 * errorListener(Callable $callback)
	 * 错误监听器
	 * ---------------------------------
	 * @author Verdient。
	 */
	public function errorListener($callback){
		if(is_callable($callback)){
			$this->_error = $callback;
		}
	}

	/**
	 * retryListener(Callable $callback)
	 * 重试监听器
	 * ---------------------------------
	 * @author Verdient。
	 */
	public function retryListener($callback){
		if(is_callable($callback)){
			$this->_retry = $callback;
		}
	}

	/**
	 * finishedListener(Callable $callback)
	 * 结束监听器
	 * ------------------------------------
	 * @author Verdient。
	 */
	public function finishedListener($callback){
		if(is_callable($callback)){
			$this->_finished = $callback;
		}
	}

	/**
	 * push(QueueElement $queueElement)
	 * 将元素推入队列
	 * --------------------------------
	 * @param QueueElement $queueElement 队列元素
	 * -----------------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function push(QueueElement $queueElement){
		$this->queue->push($this->queueName, serialize($queueElement));
		return $this;
	}

	/**
	 * process(Callable $callback)
	 * 处理
	 * ---------------------------
	 * @param Callable $callback 回调函数
	 * ---------------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function process($callback){
		if($this->incessancy){
			while(true){
				if($this->_process($callback) === 0 && $this->emptySleep > 0){
					sleep($this->emptySleep);
				}
			}
		}else{
			return $this->_process($callback);
		}
	}

	/**
	 * _setSucceed(QueueElement $queueElement)
	 * 设置成功
	 * --------0------------------------------
	 * @param QueueElement $queueElement 队列元素
	 * -----------------------------------------
	 * @author Verdient。
	 */
	protected function _setSucceed($queueElement){
		$queueElement->setSucceed();
		if(is_callable($this->_succeed)){
			call_user_func($this->_succeed, $queueElement);
		}
		if(is_callable($this->_finished)){
			call_user_func($this->_finished, true, $queueElement);
		}
	}

	/**
	 * _setFailed(QueueElement $queueElement)
	 * 设置失败
	 * --------------------------------------
	 * @param QueueElement $queueElement 队列元素
	 * -----------------------------------------
	 * @author Verdient。
	 */
	protected function _setFailed($queueElement){
		$queueElement->setFailed();
		if(!$queueElement->getIsFinished()){
			if(is_callable($this->_retry)){
				call_user_func($this->_retry, $queueElement);
			}
		}else{
			if(is_callable($this->_failed)){
				call_user_func($this->_failed, $queueElement);
			}
			if(is_callable($this->_finished)){
				call_user_func($this->_finished, false, $queueElement);
			}
		}
	}

	/**
	 * _process(Callable $callback)
	 * 处理
	 * ----------------------------
	 * @param Callable $callback 回调函数
	 * ---------------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	protected function _process($callback){
		$count = 0;
		foreach($this->queue->each($this->queueName, $this->batchSize) as $queueElement){
			$count++;
			$queueElement = unserialize($queueElement);
			if($queueElement instanceof QueueElement){
				if($queueElement->can()){
					try{
						if(!call_user_func($callback, $queueElement->element)){
							$this->_setFailed($queueElement);
						}else{
							$this->_setSucceed($queueElement);
						}
					}catch(\Exception $e){
						$this->_setFailed($queueElement);
						if(is_callable($this->_error)){
							call_user_func($this->_error, $e, $queueElement);
						}
					}
				}
				if(!$queueElement->getIsFinished()){
					$this->push($queueElement);
				}
			}else{
				throw new InvalidValueException('queue element must instance of QueueElement');
			}
		}
		return $count;
	}
}