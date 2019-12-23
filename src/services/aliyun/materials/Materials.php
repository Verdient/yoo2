<?php
namespace yoo\services\aliyun\materials;

/**
 * Materials
 * 发起认证请求
 * -----------
 * @author Jx.
 */
class Materials extends \yoo\base\RESTComponent
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
	 * @return MaterialsRequest
	 * @author Jx.
	 */
	public function getRequest($action){
		return new MaterialsRequest([
			'accessKeyId' => $this->accessKeyId,
			'accessSecret' => $this->accessSecret,
			'url' => $this->getUrl($action),
			'bodySerializer' => 'urlencoded'
		]);
	}

	/**
	 * materials()
	 * 发起请求
	 * -----------
	 * @return MaterialsRequest
	 * @author Jx.
	 */
	public function materials(){
		return $this->getRequest('materials')->send();
	}
}