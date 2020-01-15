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
	 * @return Response
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
	 * sendEmail(String $receiver, String $content)
	 * 发送电子邮件
	 * ------------------------------------------------------
	 * @param String $receiver 接收者
	 * @param String $content 邮件正文
	 * @param String $subject 标题
	 * @param String $from 发信人
	 * @param Array $cc 抄送
	 * @param Array $bcc 密送
	 * @param Array $readReceiptTo 阅读回执
	 * -----------------------------------
	 * @author Verdient。
	 */
	public function sendEmail($receiver, $content, $subject = null, $from = null, $cc = null, $bcc = null, $readReceiptTo = null){
		return $this
			->prepareRequest('sendEmail')
			->addBody('receiver', $receiver)
			->addBody('content', $content)
			->addFilterBody('subject', $subject)
			->addFilterBody('from', $from)
			->addFilterBody('cc', $cc)
			->addFilterBody('bcc', $bcc)
			->addFilterBody('read_receipt_to', $readReceiptTo)
			->send();
	}
}