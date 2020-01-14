<?php
namespace yoo\filters;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yoo\helpers\TimeHelper;

/**
 * Signature
 * 签名
 * ---------
 * @author Verdient。
 */
class Signature extends Authentication
{
	/**
	 * @var $name
	 * 认证字段名称
	 * ----------
	 * @author Verdient。
	 */
	public $name = 'Signature';

	/**
	 * @var $signatureClass
	 * 签名类
	 * --------------------
	 * @author Verdient。
	 */
	public $signatureClass;

	/**
	 * @var String/Callable $key
	 * 签名秘钥
	 * --------------------------
	 * @author Verdient。
	 */
	public $key;

	/**
	 * @var String $timestampName
	 * 时间戳名称
	 * ---------------------------
	 * @author Verdient。
	 */
	public $timestampName = 'Request-At';

	/**
	 * @var Integer $duration
	 * 有效期（毫秒）
	 * ----------------------
	 * @author Verdient。
	 */
	public $duration = 60000000;

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
		if(!$this->signatureClass){
			throw new InvalidConfigException('signatureClass must be set');
		}
		$this->signatureClass = Instance::ensure($this->signatureClass);
		if(!$this->key){
			throw new InvalidConfigException('key must be set');
		}
	}

	/**
	 * authentication(String $authentication)
	 * 认证
	 * --------------------------------------
	 * @param String $authentication 认证信息
	 * -------------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function authentication($authentication){
		$timestamp = $this->getParam($this->timestampName);
		if($timestamp && is_numeric($timestamp)){
			$diff = abs(TimeHelper::timestamp(true) - $timestamp);
			if($diff <= $this->duration){
				if(is_callable($this->key)){
					$key = call_user_func($this->key);
				}else{
					$key = $this->key;
				}
				$data = $this->_getSignData();
				$data .= $timestamp;
				if($key && $this->signatureClass->validate($data, $authentication, $key)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * _getSignData()
	 * 获取签名数据
	 * --------------
	 * @return String
	 * @author Verdient。
	 */
	protected function _getSignData(){
		$request = Yii::$app->getRequest();
		$post = $request->post();
		$get = $request->get();
		unset($post[$this->name]);
		unset($get[$this->name]);
		unset($post[$this->timestampName]);
		unset($post[$this->timestampName]);
		ksort($post);
		ksort($get);
		$data = [
			'post' => $post,
			'get' => $get
		];
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}