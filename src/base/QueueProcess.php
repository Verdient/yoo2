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
							$queueElement->setFailed();
						}else{
							$queueElement->setSucceed();
						}
					}catch(\Exception $e){
						$queueElement->setFailed();
						Yii::error($e);
					}
				}
				if(!$queueElement->getIsFinished() && !$queueElement->getIsTerminated()){
					$this->push($queueElement);
				}
			}else{
				throw new InvalidValueException('queue element must instance of QueueElement');
			}
		}
		return $count;
	}
}