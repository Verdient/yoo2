<?php
namespace yoo\base;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * RESTComponent
 * REST 组件
 * -------------
 * @author Verdient。
 */
class RESTComponent extends \yoo\base\Component
{
	/**
	 * @var $protocol
	 * 协议
	 * --------------
	 * @author Verdient。
	 */
	public $protocol = 'http';

	/**
	 * @var $host
	 * 主机
	 * ----------
	 * @author Verdient。
	 */
	public $host = null;

	/**
	 * @var $port
	 * 端口
	 * ----------
	 * @author Verdient。
	 */
	public $port = null;

	/**
	 * @var $routePrefix
	 * 路由前缀
	 * -----------------
	 * @author Verdient。
	 */
	public $routePrefix = null;

	/**
	 * @var $routes
	 * 路由集合
	 * ------------
	 * @author Verdient。
	 */
	public $routes = [];

	/**
	 * @var String $_requestPath
	 * 请求路径
	 * -------------------------
	 * @author Verdient。
	 */
	protected $_requestPath;

	/**
	 * @var $_requestUrl
	 * 请求地址
	 * -----------------
	 * @author Verdient。
	 */
	protected $_requestUrl = [];

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function init(){
		if(!$this->host){
			throw new InvalidConfigException('host must be set');
		}
		if($this->protocol == 'http' && $this->port == 80){
			$this->port = null;
		}
		if($this->protocol == 'https' && $this->port == 443){
			$this->port = null;
		}
		$this->_requestPath = $this->protocol . '://' . $this->host . ($this->port ? (':' . $this->port) : '');
		foreach($this->routes as $name => $route){
			$this->_requestUrl[$name] = $this->_requestPath  . '/' . ($this->routePrefix ? $this->routePrefix . '/' : ''). $route;
		}
	}

	/**
	 * behaviors()
	 * 添加行为
	 * -----------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function behaviors(){
		return ArrayHelper::merge(parent::behaviors(), [
			'REST' => 'yoo\behaviors\RESTBehavior'
		]);
	}

	/**
	 * getRequestPath()
	 * 获取请求路径
	 * ----------------
	 * @return String
	 * @author Verdient。
	 */
	public function getRequestPath(){
		return $this->_requestPath;
	}

	/**
	 * getUrl(String $method)
	 * 获取URL地址
	 * ----------------------
	 * @param String $method 方法
	 * --------------------------
	 * @return String|Null
	 * @author Verdient。
	 */
	public function getUrl($method){
		return isset($this->_requestUrl[$method]) ? $this->_requestUrl[$method] : null;
	}
}