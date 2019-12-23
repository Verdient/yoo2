<?php
namespace yoo\filters;

use yii\base\InvalidConfigException;

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
	 * @var String $message
	 * 提示信息
	 * --------------------
	 * @author Verdient。
	 */
	public $message = 'The token has expired, Please login again';

	/**
	 * @var Array $_enabledSource
	 * 允许的来源
	 * --------------------------
	 * @author Verdient。
	 */
	protected $_enabledSource = ['header', 'query', 'body', 'all'];

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
		return $this->authentication($this->getAuthentication());
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
		$request = $this->getRequest();
		switch($this->source){
			case 'all':
				$headers = $request->getHeaders();
				return $headers->get($this->name) ?: $request->post($this->name) ?: $request->get($this->name);
			case 'header':
				return $headers->get($this->name);
			case 'query':
				return $request->post($this->name);
			case 'body':
				return $request->get($this->name);
		}
	}
}