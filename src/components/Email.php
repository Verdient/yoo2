<?php
namespace yoo\components;

use Yii;
use yii\di\Instance;
use yii\swiftmailer\Mailer;

/**
 * Email
 * 电子邮件组件
 * ----------
 * @author Verdient。
 */
class Email extends \yoo\base\Component
{
	/**
	 * @var $mailer
	 * 邮递组件
	 * ------------
	 * @author Verdient。
	 */
	public $mailer = 'mailer';

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
		$this->mailer = Instance::ensure($this->mailer, Mailer::className());
	}

	/**
	 * send(String|Array $receiver, String $message[, Array $options = []])
	 * 发送邮件
	 * --------------------------------------------------------------------
	 * @param String|Array $receiver 接收者
	 * @param String $message 要发送的内容
	 * @param Array $options 配置参数 [
	 *   from => 发件人
	 *   subject => 邮件主题
	 *   cc => 抄送
	 *   bcc => 密送
	 *   readReceiptTo => 邮件回执
	 * ]
	 * -----------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function sendText($receiver, $message, $options = []){
		$message = $this->mailer->compose(null)->setTo($receiver)->setTextBody($message);
		foreach(['from', 'subject', 'cc', 'bcc', 'readReceiptTo'] as $name){
			if(isset($options[$name]) && !empty($options[$name])){
				$method = 'set' . ucfirst($name);
				$message->$method($options[$name]);
			}
		}
		try{
			return $message->send();
		}catch(\Exception $e){
			Yii::error($e, __METHOD__);
			return false;
		}
	}

	/**
	 * sendCaptcha(String $receiver, String $captcha)
	 * 发送验证码
	 * ----------------------------------------------
	 * @param String $receiver 接收者
	 * @param String $captcha 验证码
	 * ------------------------------
	 * @return Null|String
	 * @author Verdient。
	 */
	public function sendCaptcha($receiver, $captcha){
		$this->sendText($receiver, Yii::t('message', 'Your captcha {captcha}, which is valid for 5 minutes, please do not leak it to others!', ['captcha' => $captcha]), ['subject' => Yii::t('message', 'Captcha')]);
		return null;
	}
}