<?php
namespace yoo\services\aliyun\verifyToken;

/**
 * VerifyToken
 * 发起认证请求
 * -----------
 * @author Jx.
 */
class VerifyToken extends \yoo\base\RESTComponent
{
	/**
	 * @var String $accessKeyId
	 * 授权编号
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
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return VerifyTokenRequest
	 * @author Jx.
	 */
	public function getRequest($action){
		return new VerifyTokenRequest([
			'accessKeyId' => $this->accessKeyId,
			'accessSecret' => $this->accessSecret,
			'url' => $this->getUrl($action),
			'bodySerializer' => 'urlencoded'
		]);
	}

	/**
	 * verifyToken(Array $params)
	 * 发起认证请求
	 * --------------------------
	 * @param Array $params  请求参数
	 * -----------------------------
	 * @return VerifyTokenRequest
	 * @author Jx.
	 */
	public function verifyToken($params){
		$bodyParams = [
			'Name' => $params['name'],
			'IdentificationNumber' => $params['idcard'],
			'FaceRetainedPic' => $params['facepic']
		];
		return $this->getRequest('verifyToken')->setBody(['Binding' => json_encode($bodyParams)])->send();
	}
}