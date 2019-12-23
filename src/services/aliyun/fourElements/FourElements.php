<?php
namespace yoo\services\aliyun\fourElements;

/**
 * FourElements
 * 四要素验证
 * ------------
 * @author Verdient。
 */
class FourElements extends \yoo\base\RESTComponent
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
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return FourElementsRequest
	 * @author Verdient。
	 */
	public function getRequest($action){
		return new FourElementsRequest([
			'appKey' => $this->appKey,
			'appSecret' => $this->appSecret,
			'appCode' => $this->appCode,
			'url' => $this->getUrl($action)
		]);
	}

	/**
	 * validate(String $bankcard, String $idcard, String $mobile, String $name)
	 * 校验
	 * ------------------------------------------------------------------------
	 * @param String $bankcard 银行卡号
	 * @param String $idcard 身份证号
	 * @param String $mobile 手机号码
	 * @param String $name 姓名
	 * -------------------------------
	 * @return FourElementsResponse
	 * @author Verdient。
	 */
	public function validate($bankcard, $idcard, $mobile, $name){
		return $this->getRequest('validate')->setQuery(['bankcard' => $bankcard, 'idcard' => $idcard, 'mobile' => $mobile, 'name' => $name])->send();
	}
}