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
	 * @var const STATUS_IN_PROGRESS
	 * 处理中
	 * -----------------------------
	 * @author Verdient。
	 */
	const STATUS_IN_PROGRESS = 1;

	/**
	 * @var const STATUS_SUCCEED
	 * 已成功
	 * -------------------------
	 * @author Verdient。
	 */
	const STATUS_SUCCEED = 2;

	/**
	 * @var const STATUS_RETRYING
	 * 重试中
	 * --------------------------
	 * @author Verdient。
	 */
	const STATUS_RETRYING = 3;

	/**
	 * @var const STATUS_FAILED
	 * 已失败
	 * ------------------------
	 * @author Verdient。
	 */
	const STATUS_FAILED = 4;

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
	public $ladderRetry = true;

	/**
	 * @var Integer $maxRetry
	 * 最大重试次数
	 * ----------------------
	 * @author Verdient。
	 */
	public $maxRetry = 0;

	/**
	 * @var Boolean $_status
	 * 状态
	 * ---------------------
	 * @author Verdient。
	 */
	protected $_status = false;

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
	 * getIsSucceed()
	 * 获取是否成功
	 * --------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getIsSucceed(){
		return $this->_status === static::STATUS_SUCCEED;
	}

	/**
	 * can()
	 * 是否能够处理
	 * ----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function can(){
		return !$this->getIsFinished() && ($this->processAt <= time());
	}

	/**
	 * setFailed()
	 * 置为失败
	 * -----------
	 * @return Self
	 * @author Verdient。
	 */
	public function setFailed(){
		if(!$this->getIsFinished()){
			$this->_failCount++;
			if($this->_failCount > $this->maxRetry){
				$this->_status = static::STATUS_FAILED;
			}else{
				$this->_status = static::STATUS_RETRYING;
				$delay = $this->retryInterval;
				if($this->ladderRetry === true){
					$delay = $delay * $this->_failCount;
				}
				$this->processAt = time() + $delay;
			}
		}
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
		if(!$this->getIsFinished()){
			$this->_status = static::STATUS_SUCCEED;
		}
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
		return in_array($this->_status, [static::STATUS_SUCCEED, static::STATUS_FAILED]);
	}
}