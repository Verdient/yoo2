<?php
namespace yoo\services\aliyun\SMS;

/**
 * SMS
 * 短信
 * ---
 * @author Verdient。
 */
class SMS extends \yoo\base\RESTComponent
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
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return SMSRequest
	 * @author Verdient。
	 */
	public function getRequest($action, $method = 'post'){
		return new SMSRequest([
			'action' => $action,
			'method' => $method,
			'appKey' => $this->appKey,
			'appSecret' => $this->appSecret,
			'url' => $this->getRequestPath(),
			'bodySerializer' => 'urlencoded'
		]);
	}

	/**
	 * sendText(Array $options)
	 * 发送文本
	 * ------------------------
	 * @param $options 参数
	 * -------------------
	 * @return SMSResponse
	 * @author Verdient。
	 */
	public function sendText($options){
		return $this->getRequest('SendSms')->setQuery($options)->send();
	}

	/**
	 * sendCaptcha(String $mobile, String $captcha)
	 * 发送验证码
	 * --------------------------------------------
	 * @param String $mobile 手机号码
	 * @param String $captcha 验证码
	 * -----------------------------
	 * @return Null|String
	 * @author Verdient。
	 */
	public function sendCaptcha($mobile, $captcha){
		if(mb_substr($mobile, 0, 1) === '+'){
			if(mb_substr($mobile, 0, 3) === '+86'){
				$mobile = mb_substr($mobile, 3);
			}else{
				$mobile = mb_substr($mobile, 1);
			}
			$name = 'HUIKANG';
			$code = 'SMS_169636245';
		}else{
			$name = 'APCOIN';
			$code = 'SMS_166690108';
		}
		$response = $this->sendText([
			'TemplateCode' => $code,
			'TemplateParam' => json_encode([
				'code' => (string) $captcha
			]),
			'PhoneNumbers' => $mobile,
			'SignName' => $name
		]);
		if($response->getProcessSuccess()){
			return null;
		}else{
			return $response->getErrorMessage();
		}
	}
}