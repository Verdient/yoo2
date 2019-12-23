<?php
namespace yoo\services\jiguang\jPush;

/**
 * JPush
 * 极光推送
 * -------
 * @author Verdient。
 */
class JPush extends \yoo\base\RESTComponent
{
	/**
	 * @var String $appKey
	 * App标识
	 * -------------------
	 * @author Verdient。
	 */
	public $appKey;

	/**
	 * @var String $masterSecret
	 * 秘钥
	 * -------------------------
	 * @author Verdient。
	 */
	public $masterSecret;

	/**
	 * getRequest(String $method)
	 * 获取请求对象
	 * --------------------------
	 * @param String $method 请求的方法
	 * -------------------------------
	 * @return JPushRequest
	 * @author Verdient。
	 */
	public function getRequest($method){
		return new JPushRequest([
			'url' => $this->getUrl($method),
			'appKey' => $this->appKey,
			'masterSecret' => $this->masterSecret
		]);
	}

	/**
	 * push(Array $options)
	 * 推送
	 * --------------------
	 * @param Array $options 参数
	 * --------------------------
	 * @return JPushResponse
	 * @author Verdient。
	 */
	public function push($options){
		return $this->getRequest('push')->setBody($options)->send();
	}
}