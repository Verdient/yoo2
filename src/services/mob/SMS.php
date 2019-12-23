<?php
namespace yoo\services\mob;

use yii\base\InvalidConfigException;
use yoo\base\RESTComponent;

/**
 * SMS
 * 短信
 * ---
 * @author Verdient。
 */
class SMS extends RESTComponent
{
	/**
	 * @var $appKey
	 * App标识
	 * ------------
	 * @author Verdient。
	 */
	public $appKey = null;

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		if(!$this->appKey){
			throw new InvalidConfigException('appKey must be set');
		}
	}

	/**
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return MobRequest
	 * @author Verdient。
	 */
	public function getRequest(){
		$request = new MobRequest([
			'appKey' => $this->appKey
		]);
		return $request;
	}

	/**
	 * validateCaptcha(Array $options)
	 * 验证短信验证码
	 * -------------------------------
	 * @param Array $options 参数
	 * -------------------------
	 * @return MobResponse
	 */
	public function validateCaptcha(Array $options){
		$request = $this->getRequest();
		$request->url = $this->_requestUrl['validateCaptcha'];
		$request->setBody($options);
		return $request->send();
	}
}