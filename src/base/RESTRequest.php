<?php
namespace yoo\base;

use yii\di\Instance;
use yoo\helpers\StringHelper;
use yoo\components\cUrl\CUrl;

/**
 * RESTRequest
 * REST请求
 * -----------
 * @author Verdient。
 */
class RESTRequest extends \yoo\base\Component
{
	use \yoo\traits\ModelTrait;

	/**
	 * @var $cUrl
	 * cUrl组件
	 * ----------
	 * @author Verdient。
	 */
	public $cUrl = 'cUrl';

	/**
	 * @var $method
	 * 访问方式
	 * ------------
	 * @author Verdient。
	 */
	public $method = 'post';

	/**
	 * @var $url
	 * URL地址
	 * ---------
	 * @author Verdient。
	 */
	public $url = null;

	/**
	 * @var $bodySerializer
	 * 消息体序列化器
	 * --------------------
	 * @author Verdient。
	 */
	public $bodySerializer = null;

	/**
	 * @var $cUrlOptions
	 * cUrl选项
	 * -----------------
	 * @author Verdient。
	 */
	public $cUrlOptions = [];

	/**
	 * @var $_body
	 * 消息体
	 * -----------
	 * @author Verdient。
	 */
	protected $_body = [];

	/**
	 * @var $query
	 * 查询参数
	 * -----------
	 * @author Verdient。
	 */
	protected $_query = [];

	/**
	 * @var $_header
	 * 头部信息
	 * -------------
	 * @author Verdient。
	 */
	protected $_header = [];

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
		$this->cUrl = Instance::ensure($this->cUrl, CUrl::className());
		$this->cUrl = $this->cUrl->new();
	}

	/**
	 * setBody(Array $body)
	 * 设置消息体
	 * --------------------
	 * @param Array $body 消息体
	 * ------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function setBody(Array $body){
		$this->_body = $body;
		return $this;
	}

	/**
	 * getBody()
	 * 获取消息体
	 * ---------
	 * @return Array
	 * @author Verdient。
	 */
	public function getBody(){
		return $this->_body;
	}

	/**
	 * addBody(String $key, String $value)
	 * 将内容添加到消息体中
	 * -----------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * ------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addBody($key, $value){
		$this->_body[$key] = $value;
		return $this;
	}

	/**
	 * addFilterBody(String $key, String $value)
	 * 过滤后将内容添加到消息体中
	 * -----------------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * ------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addFilterBody($key, $value){
		if(!empty($value)){
			return $this->addBody($key, $value);
		}
		return $this;
	}

	/**
	 * setQuery(Array $query)
	 * 设置查询参数
	 * ----------------------
	 * @param Array $query 查询参数
	 * ---------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function setQuery(Array $query){
		$this->_query = $query;
		return $this;
	}

	/**
	 * getQuery()
	 * 获取查询参数
	 * ----------
	 * @return Array
	 * @author Verdient。
	 */
	public function getQuery(){
		return $this->_query;
	}

	/**
	 * addQuery(String $key, String $value)
	 * 将内容添加到查询参数中
	 * ------------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * -------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addQuery($key, $value){
		$this->_query[$key] = $value;
		return $this;
	}

	/**
	 * addFilterQuery(String $key, String $value)
	 * 过滤后将内容添加到查询参数中
	 * ------------------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * -------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addFilterQuery($key, $value){
		if(!empty($value)){
			return $this->addQuery($key, $value);
		}
		return $this;
	}

	/**
	 * setHeader(Array $header)
	 * 设置消息体
	 * ------------------------
	 * @param Array $header 头部信息
	 * ----------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function setHeader(Array $header){
		$this->_header = $header;
		return $this;
	}

	/**
	 * getHeader()
	 * 获取头部信息
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function getHeader(){
		return $this->_header;
	}

	/**
	 * addHeader(String $key, String $value)
	 * 将内容添加到头部信息中
	 * -------------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * -------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addHeader($key, $value){
		$this->_header[$key] = $value;
		return $this;
	}

	/**
	 * addFilterHeader(String $key, String $value)
	 * 过滤后将内容添加到头部信息中
	 * -------------------------------------------
	 * @param String $key 名称
	 * @param String $value 内容
	 * -------------------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	public function addFilterHeader($key, $value){
		if(!empty($value)){
			return $this->addHeader($key, $value);
		}
		return $this;
	}

	/**
	 * beforeSend()
	 * 准备发送前的操作
	 * -------------
	 * @author Verdient。
	 */
	public function beforeSend(){}

	/**
	 * getResponseClass()
	 * 获取响应类
	 * ------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getResponseClass(){
		$requestClass = static::className();
		$namespace = StringHelper::dirname($requestClass);
		$baseName = StringHelper::basename($requestClass);
		$baseName = str_ireplace('Request', 'Response', $baseName);
		$responseClass = $namespace . '\\' . $baseName;
		if(class_exists($responseClass)){
			return $responseClass;
		}
		return RESTResponse::className();
	}

	/**
	 * _prepareSend()
	 * 准备发送
	 * --------------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	protected function _prepareSend(){
		$this->beforeSend();
		$this->cUrl->reset();
		$this->cUrl->setOptions($this->cUrlOptions);
		if(!empty($this->_query)){
			$this->cUrl->setQuery($this->_query);
		}
		if(!empty($this->_header)){
			$this->cUrl->setHeader($this->_header);
		}
		if(!empty($this->_body)){
			$this->cUrl->setBody($this->_body, $this->bodySerializer);
		}
		$this->cUrl->setUrl($this->url);
		return $this;
	}

	/**
	 * _prepareResponse()
	 * 准备响应
	 * ------------------
	 * @return RESTResponse
	 * @author Verdient。
	 */
	protected function _prepareResponse(){
		$responseClass = $this->getResponseClass();
		return new $responseClass($this->cUrl);
	}

	/**
	 * send()
	 * 发送
	 * ------
	 * @return RESTResponse
	 * @author Verdient。
	 */
	public function send(){
		$this->_prepareSend();
		$this->cUrl->setMethod($this->method);
		$this->cUrl->prepare();
		return $this->_prepareResponse($this->cUrl);
	}
}