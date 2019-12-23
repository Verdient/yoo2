<?php
namespace yoo\services\aliyun\verifyToken;

use Yii;

/**
 * VerifyTokenResponse
 * 认证请求响应
 * -------------------
 * @author Jx.
 */
class VerifyTokenResponse extends \yoo\base\RESTResponse
{
	/**
	 * getProcessSuccess()
	 * 获取是否处理成功
	 * -------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getProcessSuccess(){
		if(parent::getProcessSuccess()){
			$content = $this->getContent();
			return isset($content['Data']);
		}
		return false;
	}

	/**
	 * getErrorCode()
	 * 获取错误码
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @return String
	 * @author Jx.
	 */
	public function getErrorCode(){
		if($this->hasError()){
			return $this->getStatusCode();
		}
		return null;
	}

	/**
	 * getErrorMessage()
	 * 获取错误信息
	 * -----------------
	 * @inheritdoc
	 * -----------
	 * @return String
	 * @author Verdient。
	 */
	public function getErrorMessage(){
		if($this->hasError()){
			$error = $this->getContent();
			return isset($error['Message']) ? $error['Message'] : null;
		}
		return null;
	}
}