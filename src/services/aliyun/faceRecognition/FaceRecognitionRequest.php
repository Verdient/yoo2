<?php
namespace yoo\services\aliyun\faceRecognition;

use yii\base\InvalidConfigException;
use yoo\helpers\UUIDHelper;

/**
 * FaceRecognitionRequest
 * 人脸识别请求
 * ----------------------
 * @author Verdient。
 */
class FaceRecognitionRequest extends \yoo\base\RESTRequest
{
	/**
	 * @var String $format
	 * 返回数据格式
	 * -------------------
	 * @author Jx.
	 */
	public $format = 'JSON';

	/**
	 * @var String $version
	 * 接口版本
	 * --------------------
	 * @author Jx.
	 */
	public $version = '2018-09-16';

	/**
	 * @var String $accessKeyId
	 * 授权id
	 * ------------------------
	 * @author Jx.
	 */
	public $accessKeyId;

	/**
	 * @var String $accessSecret
	 * 授权秘钥
	 * -------------------------
	 * @author Jx.
	 */
	public $accessSecret;

	/**
	 * @var String $signature
	 * 签名
	 * ----------------------
	 * @author Jx.
	 */
	public $signature;

	/**
	 * @var String $signatureMethod
	 * 加密方式
	 * ----------------------------
	 * @author Jx.
	 */
	public $signatureMethod = 'HMAC-SHA1';

	/**
	 * @var String $signatureVersion
	 * 签名版本
	 * -----------------------------
	 * @author Jx.
	 */
	public $signatureVersion = '1.0';

	/**
	 * @var String $action
	 * 接口名称
	 * -------------------
	 * @author Jx.
	 */
	public $action = 'CompareFaces';

	/**
	 * @var String $regionId
	 * 服务所在区域
	 * ---------------------
	 * @author Jx.
	 */
	public $regionId = 'cn-hangzhou';

	/**
	 * @var String $sourceImageType
	 * 照片类别
	 * ----------------------------
	 * @author Jx.
	 */
	public $sourceImageType = 'FacePic';

	/**
	 * @var String $targetImageType
	 * 比对照片类别
	 * ----------------------------
	 * @author Jx.
	 */
	public $targetImageType = 'IDPic';

	/**
	 * _prepareSend()
	 * 准备发送
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @return RESTRequest
	 * @author Jx.
	 */
	protected function _prepareSend(){
		$signatureNonce = UUIDHelper::uuid1();
		$this->addQuery('Format', $this->format);
		$this->addQuery('Version', $this->version);
		$this->addQuery('AccessKeyId', $this->accessKeyId);
		$this->addQuery('SignatureMethod', $this->signatureMethod);
		$this->addQuery('Timestamp', gmdate('Y-m-d\TH:i:s\Z'));
		$this->addQuery('SignatureVersion', $this->signatureVersion);
		$this->addQuery('SignatureNonce', $signatureNonce);
		$this->addQuery('Action', $this->action);
		$this->addQuery('RegionId', $this->regionId);
		$this->addBody('SourceImageType', $this->sourceImageType);
		$this->addBody('TargetImageType', $this->targetImageType);
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
		return new FaceRecognitionResponse($response);
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
		return base64_encode(hash_hmac('sha1', $this->getSignatureString(), $this->accessSecret . '&', true));
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