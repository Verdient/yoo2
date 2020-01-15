<?php
namespace yoo\filters;

use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Authentication
 * 认证
 * --------------
 * @author Verdient。
 */
abstract class Authentication extends \yoo\base\ActionFilter
{
	/**
	 * @var $name
	 * 认证字段名称
	 * ----------
	 * @author Verdient。
	 */
	public $name = 'Authentication';

	/**
	 * @var $source
	 * 来源
	 * ------------
	 * @param header 头部
	 * @param query 查询字符串
	 * @param body 消息体
	 * @param all 全部
	 * ----------------------
	 * @author Verdient。
	 */
	public $source = 'all';

	/**
	 * @var String $blankMessage
	 * 为空的提示消息
	 * -------------------------
	 * @author Verdient。
	 */
	public $blankMessage = 'The authentication information cannot be empty';

	/**
	 * @var String $errorMessage
	 * 错误信息
	 * -------------------------
	 * @author Verdient。
	 */
	public $errorMessage = 'Authentication failed';

	/**
	 * @var Array $_enabledSource
	 * 允许的来源
	 * --------------------------
	 * @author Verdient。
	 */
	protected $_enabledSource = ['header', 'query', 'body', 'all'];

	/**
	 * @var Boolean $strict
	 * 严格模式
	 * --------------------
	 * @author Verdient。
	 */
	public $strict = false;

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
		if(!is_string($this->name)){
			throw new InvalidConfigException('name must be a string, ' . gettype($this->name) . ' given');
		}
		if(!in_array($this->source, $this->_enabledSource)){
			throw new InvalidConfigException('source must be one of the following [' . implode(', ', $this->_enabledSource) . '], ' . $this->source . ' is unsupported');
		}
	}

	/**
	 * beforeAction(Action $action)
	 * 执行登录前的操作
	 * ----------------------------
	 * @param Action $action 动作对象
	 * -----------------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function beforeAction($action){
		$authentication = $this->getAuthentication();
		$result = false;
		if($authentication){
			$result = $this->authentication($authentication);
		}else if($this->strict === true){
			throw new BadRequestHttpException($this->blankMessage);
		}
		if($this->strict === true && !$result){
			throw new UnauthorizedHttpException($this->errorMessage);
		}
		return true;
	}

	/**
	 * authentication(String $authentication)
	 * 认证
	 * ------------------------------------
	 * @param Action $authentication 认证信息
	 * -------------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	abstract public function authentication($authentication);

	/**
	 * getAuthentication()
	 * 获取认证字符串
	 * --------------------
	 * @return String|Null
	 * @author Verdient。
	 */
	public function getAuthentication(){
		return $this->getParam($this->name);
	}

	/**
	 * getParam(String $name)
	 * 获取参数
	 * ----------------------
	 * @param String $name 名称
	 * ------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getParam($name){
		$request = $this->getRequest();
		$param = null;
		switch($this->source){
			case 'all':
				$headers = $request->getHeaders();
				$param = $headers->get($name) ?: $request->post($name) ?: $request->get($name);
			break;
			case 'header':
				$param = $headers->get($name);
			break;
			case 'query':
				$param = $request->post($name);
			break;
			case 'body':
				$param = $request->get($name);
			break;
		}
		return $param;
	}
}