<?php
namespace yoo\web;

use yii\web\HttpException;

/**
 * ServiceUnavailableException
 * 服务不可用异常
 * ---------------------------
 * @author Verdient。
 */
class ServiceUnavailableException extends HttpException
{
	/**
	 * __construct(String $message[, Integer $code = 0, Exception $previous = null])
	 * 构造函数
	 * -----------------------------------------------------------------------------
	 * @param String $message 提示信息
	 * @param Integer $code 错误代码
	 * @param Exception $previous 前一个异常
	 * ------------------------------------
	 * @author Verdient。
	 */
	public function __construct($message = null, $code = 0, \Exception $previous = null){
		parent::__construct(503, $message, $code, $previous);
	}
}