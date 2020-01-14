<?php
namespace yoo\support;

/**
 * notification
 * 提醒
 * ------------
 * @author Verdient。
 */
class Notification extends Support
{
	/**
	 * @var Array $routes
	 * 路由集合
	 * ------------------
	 * @author Verdient。
	 */
	public $routes = [
		'sendSMS' => 'message/sms',
		'sendEmail' => 'message/email'
	];

	/**
	 * sendSMS(String $id, String $receiver, Array $params[, Integer $platform])
	 * 发送短信
	 * -------------------------------------------------------------------------
	 * @param String $id 编号
	 * @param String $receiver 接收者
	 * @param Array $params 参数
	 * @param Integer $platform 平台
	 * -----------------------------
	 * @author Verdient。
	 */
	public function sendSMS($id, $receiver, $params, $platform = null){
		return $this
			->prepareRequest('sendSMS')
			->addBody('id', $id)
			->addBody('receiver', $receiver)
			->addBody('params', $params)
			->addFilterBody('platform', $platform)
			->send();
	}

	/**
	 * sendEmail(String $id, String $receiver, Array $params)
	 * 发送电子邮件
	 * ------------------------------------------------------
	 * @param String $id 编号
	 * @param String $receiver 接收者
	 * @param Array $params 参数
	 * -----------------------------
	 * @author Verdient。
	 */
	public function sendEmail($id, $receiver, $params){

	}
}