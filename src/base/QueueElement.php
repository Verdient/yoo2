<?php
namespace yoo\base;

/**
 * QueueElement
 * 队列元素
 * ------------
 * @author Verdient。
 */
class QueueElement extends \yii\base\BaseObject
{
	/**
	 * @var Object $element
	 * 元素
	 * --------------------
	 * @author Verdient。
	 */
	public $element;

	/**
	 * @var Integer $processAt
	 * 处理时间
	 * -----------------------
	 * @author Verdient。
	 */
	public $processAt = 0;

	/**
	 * @var Integer $retryInterval
	 * 重试间隔
	 * ---------------------------
	 * @author Verdient。
	 */
	public $retryInterval = 0;

	/**
	 * @var Boolean $ladderRetry
	 * 阶梯式重试
	 * -------------------------
	 * @author Verdient。
	 */
	public $ladderRetry = false;

	/**
	 * @var Integer $maxRetry
	 * 最大重试次数
	 * ----------------------
	 * @author Verdient。
	 */
	public $maxRetry = 5;

	/**
	 * @var Boolean $_succeed
	 * 是否已成功
	 * ----------------------
	 * @author Verdient。
	 */
	protected $_succeed = false;

	/**
	 * @var Integer $_failCount
	 * 失败计数
	 * ------------------------
	 * @author Verdient。
	 */
	protected $_failCount = 0;

	/**
	 * getFailCount()
	 * 获取失败计数
	 * --------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function getFailCount(){
		return $this->_failCount;
	}

	/**
	 * getSucceed()
	 * 获取是否成功
	 * ------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getSucceed(){
		return $this->_succeed;
	}

	/**
	 * can()
	 * 是否能够处理
	 * ----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function can(){
		return ($this->processAt <= 0 || $this->processAt <= time()) && !$this->getIsFinished();
	}

	/**
	 * setFailed()
	 * 置为失败
	 * -----------
	 * @return Self
	 * @author Verdient。
	 */
	public function setFailed(){
		$this->_succeed = false;
		$this->_failCount++;
		$delay = $this->retryInterval;
		if($this->ladderRetry === true){
			$delay = $delay * $this->_failCount;
		}
		$this->processAt = time() + $delay;
		return $this;
	}

	/**
	 * setSucceed()
	 * 置为成功
	 * ------------
	 * @return Self
	 * @author Verdient。
	 */
	public function setSucceed(){
		$this->_succeed = true;
		return $this;
	}

	/**
	 * getIsFinished()
	 * 是否已结束
	 * ---------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getIsFinished(){
		return $this->_succeed === true || $this->getIsTerminated();
	}

	/**
	 * getIsTerminated()
	 * 是否已终结
	 * -----------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getIsTerminated(){
		return $this->_failCount > $this->maxRetry;
	}
}