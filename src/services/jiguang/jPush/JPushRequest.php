<?php
namespace yoo\services\jiguang\jPush;

/**
 * JPushRequest
 * 极光推送请求
 * ------------
 * @author Verdient。
 */
class JPushRequest extends \yoo\base\RESTRequest
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
	 * @var $bodySerializer
	 * 消息体序列化器
	 * --------------------
	 * @author Verdient。
	 */
	public $bodySerializer = 'JSON';

	/**
	 * beforeSend()
	 * 发送前的操作
	 * ------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function beforeSend(){
		$this->addHeader('Authorization', 'Basic ' . $this->getAuthorization());
	}

	/**
	 * getAuthorization()
	 * 获取认证信息
	 * ------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getAuthorization(){
		return base64_encode($this->appKey . ':' . $this->masterSecret);
	}
}