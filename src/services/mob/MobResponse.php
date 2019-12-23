<?php
namespace yoo\services\mob;

use yoo\base\RESTResponse;

/**
 * MobResponse
 * Mob响应
 * -----------
 * @author Verdient。
 */
class MobResponse extends RESTResponse
{
	/**
	 * getContent()
	 * 获取消息体
	 * ------------
	 * @inheritdoc
	 * -----------
	 * @return Array|String|Null
	 * @author Verdient。
	 */
	public function getContent(){
		parent::getContent();
		if(!is_array($this->_content)){
			$this->_content = json_decode($this->_content, true);
		}
		return $this->_content;
	}

	/**
	 * getProcessSuccess()
	 * 获取处理是否成功
	 * ------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getProcessSuccess(){
		if($this->getRequestSuccess()){
			$content = $this->getContent();
			return isset($content['status']) && $content['status'] == 200;
		}
		return false;
	}

	/**
	 * hasError()
	 * 是否有错误
	 * ----------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function hasError(){
		if(parent::hasError()){
			return true;
		}
		if($this->getProcessFail()){
			$content = $this->getContent();
			if(isset($content['error']) && $content['error']){
				return true;
			}
		}
		return false;
	}

	/**
	 * getError()
	 * 获取错误
	 * ----------
	 * @return Array|Null
	 * @author Verdient。
	 */
	public function getError(){
		if($this->hasError()){
			$content = $this->getContent();
			if(isset($content['error']) && $content['error']){
				return [
					'code' => $content['status'],
					'type' => 'process failed',
					'message' => $content['error']
				];
			}
			return parent::getError();
		}
		return null;
	}
}