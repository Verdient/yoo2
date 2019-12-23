<?php
namespace yoo\components\cUrl;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yoo\base\FormData;

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
	 * @var $_options
	 * 参数
	 * --------------
	 * @author Verdient。
	 */
	protected $_options = [];

	/**
	 * @var $_curl
	 * cUrl实例
	 * -----------
	 * @author Verdient。
	 */
	protected $_curl = null;

	/**
	 * @var $_defaultOptions
	 * 默认参数
	 * ---------------------
	 * @author Verdient。
	 */
	protected $_defaultOptions = [
		CURLOPT_USERAGENT => 'Yii2-CUrl-Agent',
		CURLOPT_TIMEOUT => 30,
		CURLOPT_CONNECTTIMEOUT => 30,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_HTTPHEADER => [],
		self::CURLOPT_QUERY => [],
	];

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
		$this->setOption(CURLOPT_URL, $url);
		return $this;
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
		$this->setOption(CURLOPT_HTTPHEADER, $header);
		return $this;
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
		$this->setOption(CURLOPT_HTTPHEADER, $header);
		return $this;
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
		$this->setOption(CURLOPT_POST, true);
		if(is_array($data) && is_string($formatter)){
			switch(strtolower($formatter)){
				case 'json':
					$data = Json::encode($data);
					$this->addHeader('Content-Type', 'application/json');
					break;
				case 'urlencoded':
					$data = http_build_query($data);
					$this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
					break;
				default:
					throw new InvalidParamException('Unkrown formatter: ' . $formatter);
			}
		}else if($data instanceof FormData){
			$boundary = $data->getBoundary();
			$data = $data->toString();
			$this->addHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
		}
		if(is_callable($formatter)){
			$data = call_user_func($formatter, $data);
		}
		if(!is_string($data)){
			throw new InvalidParamException('data must be a string');
		}
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
		$this->setOption(self::CURLOPT_QUERY, $query);
		return $this;
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
		if (isset($this->_options)) {
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
	 * request(String $method)
	 * 请求
	 * -----------------------
	 * @param String $method 请求方式
	 * -----------------------------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	public function request($method){
		return $this->_httpRequest(mb_strtoupper($method));
	}

	/**
	 * _httpRequest(String $method)
	 * http请求
	 * ----------------------------
	 * @param String $method 请求方式
	 * -----------------------------
	 * @return CUrlResponse
	 * @author Verdient。
	 */
	protected function _httpRequest($method, $responseContentType = null){
		$url = $this->getOption(CURLOPT_URL);
		$this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		if($method === 'HEAD'){
			$this->setOption(CURLOPT_NOBODY, true);
			$this->unsetOption(CURLOPT_WRITEFUNCTION);
		}
		$query = $this->getOption(self::CURLOPT_QUERY);
		if(!empty($query)){
			$url = $url . '?' . http_build_query($query);
		}
		$this->setOption(CURLOPT_URL, $url);
		$this->_curl = curl_init();
		$options = $this->getOptions();
		$curlOptions = [];
		foreach($options as $key => $value){
			if(is_numeric($key)){
				$curlOptions[$key] = $value;
			}
		}
		curl_setopt_array($this->_curl, $curlOptions);
		$result = [];
		$result['autoParse'] = $this->autoParse;
		$result['raw'] = [];
		$result['raw']['response'] = curl_exec($this->_curl);
		$result['raw']['statusCode'] = $this->getInfo(CURLINFO_HTTP_CODE);
		if($this->getOption(CURLOPT_HEADER)){
			$headerSize = $this->getInfo(CURLINFO_HEADER_SIZE);
			$result['raw']['header'] = mb_substr($result['raw']['response'], 0, $headerSize - 4);
			$result['raw']['content'] = mb_substr($result['raw']['response'], $headerSize);
		}else{
			$result['raw']['header'] = null;
			$result['raw']['content'] = $result['raw']['response'];
		}
		$result['cUrl'] = [];
		$result['cUrl']['info'] = $this->getInfo();
		$result['cUrl']['version'] = curl_version();
		if($result['raw']['response'] === false){
			$result['cUrl']['errorCode'] = $this->getErrorCode();
			$result['cUrl']['errorType'] = $this->getErrorType($result['cUrl']['errorCode']);
			$result['cUrl']['errorMessage'] = $this->getErrorMessage();
			Yii::warning([
				'code' => $result['cUrl']['errorCode'],
				'type' => $result['cUrl']['errorMessage'],
				'message' => $result['cUrl']['errorMessage'],
				'info' => $this->getInfo(), 'version' => $result['cUrl']['version']
			], __METHOD__);
		}
		Yii::trace([
			'method' => $method,
			'options' => $options,
			'code' => $result['raw']['statusCode'],
			'response' => $result['raw']['response']
		], __METHOD__);
		$this->reset();
		return new CUrlResponse($result);
	}
}