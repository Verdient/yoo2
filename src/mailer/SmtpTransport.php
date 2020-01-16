<?php
namespace yoo\mailer;

/**
 * SmtpTransport
 * SMTP传输
 * -------------
 * @author Verdient。
 */
class SmtpTransport extends \Swift_SmtpTransport
{
	/**
	 * ping()
	 * 检测是否在线
	 * ----------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function ping(){
		try{
			return parent::ping();
		}catch(\Exception $e){
			return false;
		}
	}

	/**
	 * stop()
	 * 停止
	 * ------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function stop(){
		if($this->started){
			if($evt = $this->eventDispatcher->createTransportChangeEvent($this)){
				$this->eventDispatcher->dispatchEvent($evt, 'beforeTransportStopped');
				if($evt->bubbleCancelled()){
					return;
				}
			}
			try{
				$this->executeCommand("QUIT\r\n", [221]);
			}catch(\Exception $e){
			}
			try{
				$this->buffer->terminate();
				if($evt){
					$this->eventDispatcher->dispatchEvent($evt, 'transportStopped');
				}
			}catch(\Swift_TransportException $e){
				$this->throwException($e);
			}
		}
		$this->started = false;
	}

	/**
	 * start()
	 * 开始
	 * -------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function start(){
		try{
			return parent::start();
		}catch(\Exception $e){
			return parent::start();
		}
	}
}