<?php
namespace yoo\services\mob;

use yii\base\InvalidConfigException;
use yoo\base\RESTRequest;

/**
 * MobRequest
 * Mob请求
 * ----------
 * @author Verdient。
 */
class MobRequest extends RESTRequest
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
		$this->bodySerializer = function($value){
			return http_build_query($value);
		};
	}

	/**
	 * send()
	 * 发送
	 * ------
	 * @return MobResponse
	 * @author Verdient。
	 */
	public function send(){
		$this->addHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
		$this->addHeader('Accept', 'application/json');
		$this->addBody('appkey', $this->appKey);
		return new MobResponse(parent::send());
	}
}