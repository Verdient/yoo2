<?php
namespace yoo\filters;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

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
	 * @var $key
	 * 签名秘钥
	 * ---------
	 * @author Verdient。
	 */
	public $key;

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
	}

	/**
	 * beforeAction()
	 * 登录
	 * --------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function beforeAction($action){
		if(!$authentication = $this->getAuthentication()){
			throw new BadRequestHttpException($this->name . ' can not be blank');
		}
		if($this->signatureClass->validate($this->_getSignData(), $authentication, $this->key)){
			return true;
		}
		throw new UnauthorizedHttpException($this->name . ' is incorrect');
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
		ksort($post);
		ksort($get);
		$data = [
			'post' => $post,
			'get' => $get
		];
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}