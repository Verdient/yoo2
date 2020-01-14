<?php
namespace yoo\support;

use yoo\components\Signature;
use yoo\helpers\TimeHelper;

/**
 * Request
 * 请求
 * -------
 * @author Verdient。
 */
class Request extends \yoo\base\RESTRequest
{
	/**
	 * @var String $appKey
	 * App标识
	 * -------------------
	 * @author Verdient。
	 */
	public $appKey = null;

	/**
	 * @var String $appSecret
	 * App秘钥
	 * ----------------------
	 * @author Verdient。
	 */
	public $appSecret = null;

	/**
	 * @var $bodySerializer
	 * 消息体序列化器
	 * --------------------
	 * @author Verdient。
	 */
	public $bodySerializer = 'json';

	/**
	 * @var Integer $_timestamp
	 * 时间戳
	 * ------------------------
	 * @author Verdient。
	 */
	protected $_timestamp = null;

	/**
	 * getTimestamp()
	 * 获取时间戳
	 * --------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function getTimestamp(){
		if($this->_timestamp === null){
			$this->_timestamp = TimeHelper::timestamp(true);
		}
		return $this->_timestamp;
	}

	/**
	 * beforeSend()
	 * 发送前的准备工作
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function beforeSend(){
		$this->addHeader('App-Key', $this->appKey);
		$this->addHeader('App-Secret', $this->appSecret);
		$data = $this->_getSignData();
		$data .= $this->getTimestamp();
		$signature = (new Signature())->sign($data, $this->appSecret);
		$this->addHeader('Signature', $signature);
		$this->addHeader('Request-At', $this->getTimestamp());
	}

	/**
	 * _getSignData()
	 * 获取签名数据
	 * --------------
	 * @return String
	 * @author Verdient。
	 */
	protected function _getSignData(){
		$post = $this->getBody();
		$get = $this->getQuery();
		ksort($post);
		ksort($get);
		$data = [
			'post' => $post,
			'get' => $get
		];
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}