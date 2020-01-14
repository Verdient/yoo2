<?php
namespace yoo\components\cUrl;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yoo\components\cUrl\builder\Builder;

/**
 * CUrl
 * cURL
 * ----
 * @author Verdient。
 */
class CUrl extends \yoo\base\Component
{
	/**
	 * const CURLOPT_QUERY
	 * 查询参数
	 * -------------------
	 * @author Verdient。
	 */
	const CURLOPT_QUERY = 'query';

	/**
	 * @var $autoParse
	 * 是否自动解析响应体
	 * ---------------
	 * @author Verdient。
	 */
	public $autoParse = true;

	/**
	 * @var Array $builders
	 * 构建器
	 * --------------------
	 * @author Verdient。
	 */
	public $builders = [];

	/**
	 * @var const BUILT_IN_BUILDERS
	 * 内建构造器
	 * ----------------------------
	 * @author Verdient。
	 */
	const BUILT_IN_BUILDERS = [
		'json' => 'yoo\components\cUrl\builder\JsonBuilder',
		'urlencoded' => 'yoo\components\cUrl\builder\UrlencodedBuilder'
	];

	/**
	 * @var $_curl
	 * cUrl实例
	 * -----------
	 * @author Verdient。
	 */
	protected $_curl = null;

	/**
	 * @var Array $_options
	 * 参数
	 * --------------------
	 * @author Verdient。
	 */
	protected $_options = [];

	/**
	 * @var $_defaultOptions
	 * 默认参数
	 * ---------------------
	 * @author Verdient。
	 */
	protected $_defaultOptions = [
		CURLOPT_USERAGENT => 'Yii2-CUrl-Agent',
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_TIMEOUT => 30,
		CURLOPT_CONNECTTIMEOUT => 30,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_HTTPHEADER => [],
		self::CURLOPT_QUERY => []
	];

	/**
	 * new()
	 * 新cURL实例
	 * ---------
	 * @author Verdient。
	 */
	public function new(){
		return new static();
	}

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
		$this->builders = array_merge($this->builders, static::BUILT_IN_BUILDERS);
	}

	/**
	 * getBuilder(Mixed $builder)
	 * 获取构建器
	 * --------------------------
	 * @param Mixed $builder 构建器
	 * ---------------------------
	 * @return Builder
	 * @author Verdient。
	 */
	public function getBuilder($builder){
		$builder = strtolower($builder);
		$builder = isset($this->builders[$builder]) ? $this->builders[$builder] : null;
		if($builder){
			$builder = new $builder;
			if(!$builder instanceof Builder){
				throw new InvalidConfigException('builder must instance of ' . Builder::className());
			}
			return $builder;
		}
		throw new InvalidParamException('Unkrown builder: ' . $builder);
	}

	/**
	 * get()
	 * get访问
	 * ------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function get(){
		return $this->request('GET');
	}

	/**
	 * head()
	 * head访问
	 * --------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function head(){
		return $this->request('HEAD');
	}

	/**
	 * post()
	 * post访问
	 * -------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function post(){
		return $this->request('POST');
	}

	/**
	 * put()
	 * put访问
	 * ------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function put(){
		return $this->request('PUT');
	}

	/**
	 * patch()
	 * patch访问
	 * ---------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function patch(){
		return $this->request('PATCH');
	}

	/**
	 * delete()
	 * delete访问
	 * ---------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function delete(){
		return $this->request('DELETE');
	}

	/**
	 * setUrl(String $url)
	 * 设置访问地址
	 * -------------------
	 * @param String $url URL
	 * ----------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setUrl($url){
		return $this->setOption(CURLOPT_URL, $url);
	}

	/**
	 * setHeader(Array $headers)
	 * 设置发送的头部信息
	 * -------------------------
	 * @param Array $headers 头部信息
	 * -----------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setHeader(Array $headers){
		$header = [];
		foreach($headers as $key => $value){
			$header[] = $key . ':' . $value;
		}
		$header = array_unique($header);
		return $this->setOption(CURLOPT_HTTPHEADER, $header);
	}

	/**
	 * addHeader(String $key, String $value)
	 * 添加头部
	 * -------------------------------------
	 * @param String $key 名称
	 * @param String $value 值
	 * -----------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function addHeader($key, $value){
		$header = $this->getOption(CURLOPT_HTTPHEADER);
		if(!$header){
			$header = [];
		}
		$header[] = $key . ':' . $value;
		$header = array_unique($header);
		return $this->setOption(CURLOPT_HTTPHEADER, $header);
	}

	/**
	 * setBody(Mixed $data, Callable / String $formatter = null)
	 * 设置发送的数据
	 * --------------------------------------------------------
	 * @param Mixed $data 发送的数据
	 * @param Callable / String $formatter 格式化器
	 * -------------------------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setBody($data, $formatter = null){
		if(is_callable($formatter)){
			$data = call_user_func($formatter, $data);
		}else if(is_array($data) && is_string($formatter)){
			$builder = $this->getBuilder($formatter);
			$builder->setElements($data);
			$data = $builder;
		}
		if($data instanceof Builder){
			foreach($data->headers() as $name => $value){
				$this->addHeader($name, $value);
			}
			$data = $data->toString();
		}
		if(!is_string($data)){
			throw new InvalidParamException('data must be a string');
		}
		$this->setOption(CURLOPT_POST, true);
		$this->setOption(CURLOPT_POSTFIELDS, $data);
		$this->addHeader('Content-Length', strlen($data));
		return $this;
	}

	/**
	 * setProxy(String $address, Integer $port)
	 * 设置代理
	 * ----------------------------------------
	 * @param String $address 地址
	 * @param Integer $port 端口
	 * ---------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setProxy($address, $port = null){
		$this->setOption(CURLOPT_PROXY, $address);
		if($port){
			$this->setOption(CURLOPT_PROXYPORT, $port);
		}
		return $this;
	}

	/**
	 * setQuery(Array $query)
	 * 设置查询信息
	 * ----------------------
	 * @param Array $query 查询信息
	 * ---------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setQuery(Array $query){
		return $this->setOption(self::CURLOPT_QUERY, $query);
	}

	/**
	 * setMethod(String $method)
	 * 设置请求方法
	 * -------------------------
	 * @param String $method 请求方法
	 * -----------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setMethod($method){
		return $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));
	}

	/**
	 * setOption(String $key, Mixed $value)
	 * 设置选项
	 * ------------------------------------
	 * @param String $key 选项名称
	 * @param Mixed $value 选项内容
	 * ----------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setOption($key, $value){
		$this->_options[$key] = $value;
		return $this;
	}

	/**
	 * setOptions(Array $options)
	 * 批量设置选项
	 * --------------------------
	 * @param String $options 选项集合
	 * -------------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function setOptions($options){
		foreach($options as $key => $value){
			$this->setOption($key, $value);
		}
		return $this;
	}

	/**
	 * unsetOption(String $key)
	 * 删除选项
	 * ------------------------
	 * @param String $key 选项名称
	 * --------------------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function unsetOption($key){
		if(isset($this->_options[$key])){
			unset($this->_options[$key]);
		}
		return $this;
	}

	/**
	 * resetOptions()
	 * 重置选项
	 * --------------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function resetOptions(){
		if(isset($this->_options)) {
			$this->_options = [];
		}
		return $this;
	}

	/**
	 * reset()
	 * 重置
	 * -------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function reset(){
		if($this->_curl !== null){
			@curl_close($this->_curl);
		}
		$this->_curl = null;
		$this->_options = [];
		return $this;
	}

	/**
	 * getOption(String $key)
	 * 获取选项内容
	 * ----------------------
	 * @param String $key 选项名称
	 * ---------------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getOption($key){
		$mergesOptions = $this->getOptions();
		return isset($mergesOptions[$key]) ? $mergesOptions[$key] : false;
	}

	/**
	 * getOptions()
	 * 获取所有的选项内容
	 * ------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getOptions(){
		return $this->_options + $this->_defaultOptions;
	}

	/**
	 * getInfo(String $opt)
	 * 获取连接资源句柄的信息
	 * ----------------------
	 * @param String $opt 选项名称
	 * --------------------------
	 * @return Array|String
	 * @author Verdient。
	 */
	public function getInfo($opt = null){
		if($this->_curl !== null && $opt === null){
			return curl_getinfo($this->_curl);
		}else if($this->_curl !== null && $opt !== null){
			return curl_getinfo($this->_curl, $opt);
		}else{
			return [];
		}
	}

	/**
	 * getErrorCode()
	 * 获取错误码
	 * --------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function getErrorCode(){
		return curl_errno($this->_curl);
	}

	/**
	 * getErrorType([Integer $errorCode = null])
	 * 获取错误类型
	 * -----------------------------------------
	 * @param Integer $errorCode 错误码
	 * -------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getErrorType($errorCode = null){
		return curl_strerror($errorCode ?: $this->getErrorCode());
	}

	/**
	 * getErrorMessage()
	 * 获取错误信息
	 * ----=------------
	 * @return String
	 * @author Verdient。
	 */
	public function getErrorMessage(){
		return curl_error($this->_curl);
	}

	/**
	 * getStatusCode()
	 * 获取状态码
	 * ---------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function getStatusCode(){
		return $this->getInfo(CURLINFO_HTTP_CODE);
	}

	/**
	 * request(String $method[, Boolean $raw = false])
	 * 请求
	 * -----------------------------------------------
	 * @param String $method 请求方式
	 * -----------------------------
	 * @return CUrlResponse|String
	 * @author Verdient。
	 */
	public function request($method, $raw = false){
		$this->setMethod($method);
		$this->prepare();
		if($raw === true){
			$response = $this->send();
			return $response;
		}else{
			return new CUrlResponse($this);
		}
	}

	/**
	 * send()
	 * 发送
	 * ------
	 * @return String
	 * @author Verdient。
	 */
	public function send(){
		return curl_exec($this->_curl);
	}

	/**
	 * prepare()
	 * 准备
	 * ---------
	 * @return CUrl
	 * @author Verdient。
	 */
	public function prepare(){
		$url = $this->getOption(CURLOPT_URL);
		$method = $this->getOption(CURLOPT_CUSTOMREQUEST);
		if($method === 'HEAD'){
			$this->setOption(CURLOPT_NOBODY, true);
			$this->unsetOption(CURLOPT_WRITEFUNCTION);
		}
		if(!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])){
			$this->unsetOption(CURLOPT_POSTFIELDS);
			$this->unsetOption(CURLOPT_POST);
		}
		$query = $this->getOption(self::CURLOPT_QUERY);
		if(!empty($query)){
			$url = $url . '?' . http_build_query($query);
		}
		$this->setOption(CURLOPT_URL, $url);
		$options = [];
		foreach($this->getOptions() as $key => $value){
			if(is_numeric($key)){
				$options[$key] = $value;
			}
		}
		$this->_curl = curl_init();
		curl_setopt_array($this->_curl, $options);
		return $this;
	}
}