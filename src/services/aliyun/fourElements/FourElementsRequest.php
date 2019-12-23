<?php
namespace yoo\services\aliyun\fourElements;

/**
 * FourElementsRequest
 * 四要素验证请求
 * -------------------
 * @author Verdient。
 */
class FourElementsRequest extends \yoo\base\RESTRequest
{
	/**
	 * @var String $appKey
	 * 授权编号
	 * -------------------
	 * @author Verdient。
	 */
	public $appKey;

	/**
	 * @var String $appSecret
	 * 授权秘钥
	 * ----------------------
	 * @author Verdient。
	 */
	public $appSecret;

	/**
	 * @var String $appCode
	 * APP编号
	 * --------------------
	 * @author Verdient。
	 */
	public $appCode;

	/**
	 * @var String $method
	 * 访问方法
	 * -------------------
	 * @author Verdient。
	 */
	public $method = 'GET';

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
		$this->addHeader('Authorization','APPCODE ' . $this->appCode);
		return parent::_prepareSend();
	}
}