<?php
namespace yoo\base;

use yii\helpers\ArrayHelper;
use yoo\components\cUrl\CUrlResponse;

/**
 * RESTResponse
 * REST响应
 * ------------
 * @author Verdient。
 */
class RESTResponse extends CUrlResponse
{
	/**
	 * getSuccess()
	 * 获取是否成功
	 * ------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getSuccess(){
		return $this->getProcessSuccess();
	}

	/**
	 * getRequestSuccess()
	 * 获取请求是否成功
	 * -------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getRequestSuccess(){
		return !$this->hasCUrlError();
	}

	/**
	 * getRequestFail()
	 * 获取请求是否失败
	 * ----------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getRequestFail(){
		return !$this->getRequestSuccess();
	}

	/**
	 * getProcessSuccess()
	 * 获取处理是否成功
	 * ------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getProcessSuccess(){
		if($this->getRequestSuccess()){
			$statusCode = (int) $this->statusCode;
			return $statusCode >= 200 && $statusCode < 300;
		}
		return false;
	}

	/**
	 * getProcessFail()
	 * 获取处理是否失败
	 * ----------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getProcessFail(){
		return !$this->getProcessSuccess();
	}

	/**
	 * hasError()
	 * 是否有错误
	 * ----------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function hasError(){
		return !$this->getProcessSuccess();
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
			if($error = parent::getError()){
				return $error;
			}
			return $this->getContent();
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
			if(ArrayHelper::isIndexed($error) && isset($error[0])){
				$error = $error[0];
			}
			if(isset($error['message'])){
				return $error['message'];
			}
		}
		return null;
	}
}