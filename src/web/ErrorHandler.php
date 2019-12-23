<?php
namespace yoo\web;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yoo\behaviors\SendEmailBehavior;
use yoo\behaviors\SendSMSBehavior;
use yoo\events\ErrorEvent;
use yoo\events\MessageEvent;

/**
 * ErrorHandler
 * 错误处理
 * ------------
 * @author Verdient。
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
	/**
	 * @var $sendMessage
	 * 是否发送错误信息
	 * -----------------
	 * @author Verdient。
	 */
	public $sendMessage = false;

	/**
	 * @var $messageSubject
	 * 消息主题
	 * --------------------
	 * @author Verdient。
	 */
	public $messageSubject = 'An abnormal situation has occurred';

	/**
	 * @var $receiver
	 * 接收者
	 * --------------
	 * @author Verdient。
	 */
	public $receiver = [];

	/**
	 * behaviors()
	 * 添加行为
	 * -----------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function behaviors(){
		if($this->sendMessage === true){
			return ArrayHelper::merge(parent::behaviors(), [
				'sendSMSBehavior' => [
					'class' => SendSMSBehavior::className()
				],
				'sendEmailBehavior' => [
					'class' => SendEmailBehavior::className()
				]
			]);
		}else{
			return parent::behaviors();
		}
	}

	/**
	 * logException()
	 * 记录异常
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function logException($exception){
		$category = get_class($exception);
		if($exception instanceof HttpException){
			$category = 'yii\\web\\HttpException:' . $exception->statusCode;
			if($exception->statusCode != 500){
				return;
			}
		}elseif ($exception instanceof \ErrorException){
			$category .= ':' . $exception->getSeverity();
		}
		Yii::error($exception, $category);
	}

	/**
	 * renderException(Exception $exception)
	 * 渲染异常
	 * -------------------------------------
	 * @inheritdoc
	 * -----------
	 * @param Exception $exception 异常对象
	 * -----------------------------------
	 * @author Verdient。
	 */
	protected function renderException($exception){
		$errorEvent = new ErrorEvent($exception);
		if($this->sendMessage === true && $errorEvent->isSystemError){
			$messageEvent = new MessageEvent($errorEvent->description);
			$messageEvent->subject = $this->messageSubject;
			$messageEvent->receiver = $this->receiver;
			$this->trigger(MessageEvent::EVENT_SEND_MESSAGE, $messageEvent);
		}
		parent::renderException($exception);
	}
}