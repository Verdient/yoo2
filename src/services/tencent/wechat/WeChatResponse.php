<?php
namespace yoo\services\tencent\wechat;

/**
 * WeChatResponse
 * 微信响应
 * --------------
 * @author Verdient。
 */
class WeChatResponse extends \yoo\base\RESTResponse
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
			return !isset($content['errcode']);
		}
		return false;
	}

	/**
	 * getError()
	 * 获取错误
	 * ----------
	 * @inheritdoc
	 * -----------
	 * @return Array|Null
	 * @author Verdient。
	 */
	public function getError(){
		if($this->hasError()){
			return $this->getContent();
		}
		return null;
	}

	/**
	 * getErrorCode()
	 * 获取错误码
	 * --------------
	 * @inheritdoc
	 * -----------
	 * @return String
	 * @author Verdient。
	 */
	public function getErrorCode(){
		if($error = $this->getError()){
			return $error['errcode'];
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
		if($error = $this->getError()){
			return $error['errmsg'];
		}
		return null;
	}
}