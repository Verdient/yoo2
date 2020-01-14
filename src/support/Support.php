<?php
namespace yoo\support;

use yii\base\InvalidConfigException;

/**
 * Support
 * 支持
 * -------
 * @author Verdient。
 */
abstract class Support extends \yoo\base\RESTComponent
{
	/**
	 * @var String $appKey
	 * App标识
	 * -------------------
	 * @author Verdient。
	 */
	public $appKey = null;

	/**
	 * @var String $appSecret
	 * App秘钥
	 * ----------------------
	 * @author Verdient。
	 */
	public $appSecret = null;

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
		foreach(['appKey', 'appSecret'] as $attribute){
			if(!$this->$attribute){
				throw new InvalidConfigException($attribute . ' must be set');
			}
		}
	}

	/**
	 * prepareRequest()
	 * 准备请求
	 * ----------------
	 * @return Request
	 * @author Verdient。
	 */
	public function prepareRequest($method){
		return new Request([
			'url' => $this->getUrl($method),
			'appKey' => $this->appKey,
			'appSecret' => $this->appSecret
		]);
	}
}