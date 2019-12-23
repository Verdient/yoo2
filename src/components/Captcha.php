<?php
namespace yoo\components;

use yii\di\Instance;
use yoo\helpers\RandomHelper;

/**
 * Captcha
 * 验证码
 * -------
 * @author Verdient。
 */
class Captcha extends \yoo\base\Component
{
	/**
	 * @var Mixed $sms
	 * 短信组件
	 * ---------------
	 * @author Verdient。
	 */
	public $sms = 'sms';

	/**
	 * @var Mixed $email
	 * 电子邮件组件
	 * -----------------
	 * @author Verdient。
	 */
	public $email = 'email';

	/**
	 * @var String $prefix
	 * 前缀
	 * -------------------
	 * @author Verdient。
	 */
	public $prefix = 'captcha_for_';

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
		$this->sms = Instance::ensure($this->sms);
		$this->email = Instance::ensure($this->email);
	}

	/**
	 * sendSms(String $mobile[, String $captcha = null])
	 * 发送短信
	 * -------------------------------------------------
	 * @param String $mobile 手机号码
	 * @param String $captcha 验证码
	 * -----------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function sendSms($mobile, $captcha = null){
		$captcha = $captcha ?: RandomHelper::number(100000, 999999);
		$error = $this->sms->sendCaptcha($mobile, $captcha);
		return $error ? [$error, null] : [null, $captcha];
	}

	/**
	 * sendEmail(String $email[, String $captcha = null])
	 * 发送电子邮件
	 * --------------------------------------------------
	 * @param String $email 电子邮件
	 * @param String $captcha 验证码
	 * ------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function sendEmail($email, $captcha = null){
		$captcha = $captcha ?: RandomHelper::number(100000, 999999);
		$error = $this->email->sendCaptcha($email, $captcha);
		return $error ? [$error, null] : [null, $captcha];
	}
}