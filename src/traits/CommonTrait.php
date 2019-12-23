<?php
namespace yoo\traits;

use Yii;

/**
 * CommonTrait
 * 公用特性
 * -----------
 * @author Verdient。
 */
trait CommonTrait
{
	/**
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return Request
	 * @author Verdient。
	 */
	public function getRequest(){
		return Yii::$app->getRequest();
	}

	/**
	 * getResponse()
	 * 获取响应对象
	 * -------------
	 * @return Response
	 * @author Verdient。
	 */
	public function getResponse(){
		return Yii::$app->getResponse();
	}

	/**
	 * getCache()
	 * 获取缓存组件
	 * -------------
	 * @return Cache
	 * @author Jon。
	 */
	public function getCache(){
		return Yii::$app->getCache();
	}

	/**
	 * getCacheValue()
	 * 获取缓存
	 * ---------------
	 * @return String
	 * @author Jon。
	 */
	public function getCacheValue($key){
		return $this->getCache()->get($key);
	}

	/**
	 * setCacheValue()
	 * 设置缓存
	 * ---------------
	 * @return Boolean
	 * @author Jon。
	 */
	public function setCacheValue($key, $value, $duration = null, $dependency = null){
		return $this->getCache()->set($key, $value, $duration, $dependency);
	}

	/**
	 * translate(...$args)
	 * 翻译
	 * -------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function translate(...$args){
		return Yii::t(...$args);
	}

	/**
	 * translateErrorMessage(...$args)
	 * 翻译错误信息
	 * -------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function translateErrorMessage(...$args){
		return Yii::t('errorMessage', ...$args);
	}
}