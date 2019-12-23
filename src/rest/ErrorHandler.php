<?php
namespace yoo\rest;

use Yii;

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
	}
}