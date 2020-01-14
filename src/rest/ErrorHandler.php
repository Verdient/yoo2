<?php
namespace yoo\rest;

use Yii;
use yii\di\Instance;
use yii\web\HttpException;

/**
 * ErrorHandler
 * 错误处理
 * ------------
 * @author Verdient。
 */
class ErrorHandler extends \yoo\web\ErrorHandler
{
	/**
	 * @var $serializer
	 * 序列化器
	 * ----------------
	 * @author Verdient。
	 */
	public $serializer = [
		'class' => 'yoo\rest\Serializer'
	];

	/**
	 * @var Mixed $reporter
	 * 上报组件
	 * --------------------
	 * @author Verdient。
	 */
	public $reporter = 'reporter';

	/**
	 * @var String $ip
	 * IP地址
	 * ---------------
	 * @author Verdient。
	 */
	public $ip = null;

	/**
	 * getReporter()
	 * 获取上报组件
	 * -------------
	 * @return Exception
	 * @author Verdient。
	 */
	public function getReporter(){
		if(!is_object($this->reporter)){
			$this->reporter = Instance::ensure($this->reporter);
		}
		return $this->reporter;
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
		try{
			$response = Yii::$app->getResponse();
			$response->data = Yii::createObject($this->serializer)->serialize($exception);
			$response->send();
		}catch(\Exception $exception){
			$response = Yii::$app->getResponse();
			$response->data = Yii::createObject($this->serializer)->serialize($exception);
			$response->send();
		}
		if(!$exception instanceof HttpException){
			register_shutdown_function([$this, 'reportException'], $exception);
		}
	}

	/**
	 * reportException(Exception $exception)
	 * 上报异常
	 * -------------------------------------
	 * @param Exception $exception 异常
	 * -------------------------------
	 * @author Verdient。
	 */
	public function reportException($exception){
		if(YII_ENV_PROD && !YII_DEBUG){
			$type = get_class($exception);
			$message = $exception->getMessage();
			$file = $exception->getFile();
			$line = $exception->getLine();
			$occurredAt = time();
			$this->getReporter()->reportPHP($type, $message, $file, $line, $this->ip, $occurredAt);
		}
	}
}