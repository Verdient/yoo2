<?php
namespace yoo\support;

/**
 * ExceptionReporter
 * 异常上报
 * -----------------
 * @author Verdient。
 */
class ExceptionReporter extends Support
{
	/**
	 * @var Array $routes
	 * 路由集合
	 * ------------------
	 * @author Verdient。
	 */
	public $routes = [
		'reportPHP' => 'report/php',
	];

	/**
	 * reportPHP(String $id, String $receiver, Array $params[, Integer $platform])
	 * 上报PHP
	 * -------------------------------------------------------------------------
	 * @param String $id 编号
	 * @param String $receiver 接收者
	 * @param Array $params 参数
	 * @param Integer $platform 平台
	 * -----------------------------
	 * @author Verdient。
	 */
	public function reportPHP($type, $message, $file = null, $line = null, $ip = null, $occurredAt = null){
		return $this
			->prepareRequest('reportPHP')
			->addBody('type', $type)
			->addBody('message', $message)
			->addFilterBody('file', $file)
			->addFilterBody('line', $line)
			->addFilterBody('ip', $ip)
			->addFilterBody('occurred_at', $occurredAt)
			->send();
	}
}