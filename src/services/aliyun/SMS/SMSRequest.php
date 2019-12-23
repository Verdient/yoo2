<?php
namespace yoo\services\aliyun\SMS;

use yii\base\InvalidConfigException;
use yoo\helpers\UUIDHelper;

/**
 * SMSRequest
 * 短信请求
 * ----------
 * @author Verdient。
 */
class SMSRequest extends \yoo\base\RESTRequest
{
	/**
	 * @var String $appKey
	 * App标识
	 * -------------------
	 * @author Verdient。
	 */
	public $appKey;

	/**
	 * @var String $appSecret
	 * App秘钥
	 * ----------------------
	 * @author Verdient。
	 */
	public $appSecret;

	/**
	 * @var String $action
	 * 目标方法
	 * -------------------
	 * @author Verdient。
	 */
	public $action;

	/**
	 * @var String $version
	 * 版本
	 * --------------------
	 * @author Verdient。
	 */
	public $version = '2017-05-25';

	/**
	 * @var String $regionId
	 * 区域编号
	 * ---------------------
	 * @author Verdient。
	 */
	public $regionId = 'cn-hangzhou';

	/**
	 * @var String $signatureMethod
	 * 签名方法
	 * ----------------------------
	 * @author Verdient。
	 */
	public $signatureMethod = 'HMAC-SHA1';

	/**
	 * @var String $signatureVersion
	 * 签名版本
	 * -----------------------------
	 * @author Verdient。
	 */
	public $signatureVersion = '1.0';

	/**
	 * @var String $format
	 * 响应格式
	 * -------------------
	 * @author Verdient。
	 */
	public $format = 'json';

	/**
	 * _prepareSend()
	 * 准备发送
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	protected function _prepareSend(){
		$signatureNonce = UUIDHelper::uuid1();
		$this->addQuery('Action', $this->action);
		$this->addQuery('AccessKeyId', $this->appKey);
		$this->addQuery('RegionId', $this->regionId);
		$this->addQuery('Timestamp', gmdate('Y-m-d\TH:i:s\Z'));
		$this->addQuery('SignatureVersion', $this->signatureVersion);
		$this->addQuery('Version', $this->version);
		$this->addQuery('Format', $this->format);
		$this->addQuery('SignatureMethod', $this->signatureMethod);
		$this->addQuery('SignatureNonce', $signatureNonce);
		$this->addQuery('Signature', $this->getSignature());
		return parent::_prepareSend();
	}

	/**
	 * _prepareResponse(Array $response)
	 * 准备响应
	 * ---------------------------------
	 * @param Array $response 响应
	 * ---------------------------
	 * @inheritdoc
	 * -----------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	protected function _prepareResponse($response){
		return new SMSResponse($response);
	}

	/**
	 * getSignature()
	 * 获取签名
	 * --------------
	 * @return String
	 * @author Verdient。
	 */
	public function getSignature(){
		switch(strtoupper($this->signatureMethod)){
			case 'HMAC-SHA1':
				return $this->getSha1Signature();
			default:
				throw new InvalidConfigException('Unknown sign method: ' . $this->signatureMethod);
		}
	}

	/**
	 * getSha1Signature()
	 * 获取SHA1签名
	 * -----------------
	 * @return String
	 * @author Verdient。
	 */
	public function getSha1Signature(){
		return base64_encode(hash_hmac('sha1', $this->getSignatureString(), $this->appSecret . '&', true));
	}

	/**
	 * getSignatureString()
	 * 获取签名字符串
	 * --------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getSignatureString(){
		$data = array_merge($this->getQuery(), $this->getBody());
		if(isset($data['Signature'])){
			unset($data['Signature']);
		}
		ksort($data);
		$canonicalized = '';
		foreach($data as $key => $value){
			$canonicalized .= '&' . $this->encode($key) . '=' . $this->encode($value);
		}
		return strtoupper($this->method) . '&%2F&' . $this->encode(substr($canonicalized, 1));
	}

	/**
	 * encode(String $value)
	 * 编码
	 * ---------------------
	 * @param String $value 值
	 * -----------------------
	 * @return String
	 * @author Verdient。
	 */
	public function encode($value){
		$result = urlencode($value);
		$result = str_replace(['+', '*'], ['%20', '%2A'], $result);
		$result = preg_replace('/%7E/', '~', $result);
		return $result;
	}
}