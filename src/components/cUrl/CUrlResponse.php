<?php
namespace yoo\components\cUrl;

use yii\helpers\Json;

/**
 * CUrlResponse
 * cUrl响应
 * ------------
 * @author Verdient。
 */
class CUrlResponse extends \yoo\base\Component
{
	/**
	 * @var $autoParse
	 * 是否自动解析
	 * ---------------
	 * @author Verdient。
	 */
	public $autoParse = true;

	/**
	 * @var CUrl $cUrl
	 * cUrl数据
	 * ---------------
	 * @author Verdient。
	 */
	public $cUrl;

	/**
	 * @var $_rawResponse
	 * 原始响应
	 * ------------------
	 * @author Verdient。
	 */
	public $_rawResponse;

	/**
	 * @var Integer $_statusCode
	 * 状态码
	 * -------------------------
	 * @author Verdient。
	 */
	public $_statusCode = null;

	/**
	 * @var $_rawHeader
	 * 原始头部
	 * ----------------
	 * @author Verdient。
	 */
	protected $_rawHeader = null;

	/**
	 * @var $_rawContent
	 * 原始消息体
	 * -----------------
	 * @author Verdient。
	 */
	protected $_rawContent = null;

	/**
	 * @var $_content
	 * 消息体
	 * --------------
	 * @author Verdient。
	 */
	protected $_content = false;

	/**
	 * @var $_headers
	 * 头部信息
	 * --------------
	 * @author Verdient。
	 */
	protected $_headers = false;

	/**
	 * __construct(CUrl $cUrl, String $response)
	 * 构造函数
	 * -----------------------------------------
	 * @param CUrl $cUrl cURL对象
	 * @param String $response 响应原文
	 * -------------------------------
	 * @author Verdient。
	 */
	public function __construct(CUrl $cUrl, $response = null){
		$this->autoParse = $cUrl->autoParse;
		$this->_rawResponse = $response ? $response : $cUrl->send();
		$this->_statusCode = $cUrl->getStatusCode();
		if($cUrl->getOption(CURLOPT_HEADER)){
			$headerSize = $cUrl->getInfo(CURLINFO_HEADER_SIZE);
			$this->_rawHeader = mb_substr($this->_rawResponse, 0, $headerSize - 4);
			$this->_rawContent = mb_substr($this->_rawResponse, $headerSize);
		}else{
			$this->_rawContent = $this->_rawResponse;
		}
		$this->cUrl = $cUrl;
		parent::__construct();
	}

	/**
	 * getResponse()
	 * 获取响应
	 * -------------
	 * @return String
	 * @author Verdient。
	 */
	public function getRawResponse(){
		return $this->_rawResponse;
	}

	/**
	 * getContent()
	 * 获取消息体
	 * ------------
	 * @return Array|String|Null
	 * @author Verdient。
	 */
	public function getContent(){
		if($this->_content === false){
			$this->_content = null;
			if($this->_rawContent){
				$this->_content = $this->_rawContent;
				if(ord(substr($this->_content, 0, 1)) === 239 && ord(substr($this->_content, 1, 1)) === 187 && ord(substr($this->_content, 2, 1)) === 191){
					$this->_content = substr($this->_content, 3);
				}
				if($this->autoParse === true){
					$parsed = false;
					if($contentType = $this->getContentType()){
						switch($contentType){
							case 'application/json':
							$this->_content = Json::decode($this->_content);
							$parsed = true;
							break;
						}
					}
					if($parsed === false){
						$start = mb_substr($this->_content, 0, 1);
						$end = mb_substr($this->_content, -1);
						if(($start === '{' && $end === '}') || ($start === '[' && $end === ']')){
							try{
								$this->_content = Json::decode($this->_content);
								$parsed = true;
							}catch(\Exception $e){}
						}
					}
				}
			}
		}
		return $this->_content;
	}

	/**
	 * getHeaders()
	 * 获取头部
	 * ------------
	 * @return Array|Null
	 * @author Verdient。
	 */
	public function getHeaders(){
		if($this->_headers === false){
			$this->_headers = null;
			if($this->_rawHeader){
				$this->_headers = [];
				$headers = explode("\r\n", $this->_rawHeader);
				foreach($headers as $header){
					if($header){
						$header = explode(': ', $header);
						if(isset($header[1])){
							if(isset($this->_headers[$header[0]])){
								if(!is_array($this->_headers[$header[0]])){
									$this->_headers[$header[0]] = [$this->_headers[$header[0]]];
								}
								$this->_headers[$header[0]][] = $header[1];
							}else{
								$this->_headers[$header[0]] = $header[1];
							}
						}
					}
				}
			}
		}
		return $this->_headers;
	}

	/**
	 * getHeader(String $name)
	 * 获取指定的头部
	 * -----------------------
	 * @param String $name 名称
	 * -----------------------
	 * @return String|Array|Null
	 * @author Verdient。
	 */
	public function getHeader($name){
		$headers = $this->getHeaders();
		return isset($headers[$name]) ? $headers[$name] : null;
	}

	/**
	 * getCookies()
	 * 获取Cookies
	 * ------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getCookies(){
		$result = [];
		if($cookies = $this->getHeader('Set-Cookie')){
			if(!is_array($cookies)){
				$cookies = [$cookies];
			}
			foreach($cookies as $cookie){
				$cookie = $this->parseCookie($cookie);
				$result[$cookie['key']] = $cookie;
			}
		}
		return $result;
	}

	/**
	 * parseCookie(String $cookie)
	 * 解析Cookie
	 * ---------------------------
	 * @param String $cookie cookie
	 * ----------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function parseCookie($cookie){
		$cookie = explode('; ', $cookie);
		$keyValue = explode('=', $cookie[0]);
		unset($cookie[0]);
		$result['key'] = $keyValue[0];
		$result['value'] = urldecode($keyValue[1]);
		foreach($cookie as $element){
			$elements = explode('=', $element);
			$name = strtolower($elements[0]);
			if(count($elements) === 2){
				$result[$name] = $elements[1];
			}else{
				$result[$name] = true;
			}
		}
		return $result;
	}

	/**
	 * getCookie(String $name)
	 * 获取Cookie
	 * -----------------------
	 * @param String $name 名称
	 * -----------------------
	 * @return String|Null
	 * @author Verdient。
	 */
	public function getCookie($name){
		$cookies = $this->getCookies();
		return isset($cookies[$name]) ? $cookies[$name] : null;
	}

	/**
	 * getContentType()
	 * 获取消息体类型
	 * ----------------
	 * @return String
	 * @author Verdient。
	 */
	public function getContentType(){
		$header = $this->getHeaders();
		if(isset($header['Content-Type'])){
			return explode(';', $header['Content-Type'])[0];
		}
		return null;
	}

	/**
	 * getStatusCode
	 * 获取状态码
	 * -------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function getStatusCode(){
		return $this->_statusCode;
	}

	/**
	 * hasCUrlError()
	 * 是否有cUrl错误
	 * --------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function hasCUrlError(){
		return !!$this->cUrl->getErrorCode();
	}

	/**
	 * hasError()
	 * 是否有错误
	 * ----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function hasError(){
		return $this->hasCUrlError();
	}

	/**
	 * getError()
	 * 获取错误
	 * ----------
	 * @return Array|Null
	 * @author Verdient。
	 */
	public function getError(){
		if($this->hasError()){
			if($this->hasCUrlError()){
				$code = $this->cUrl->getErrorCode();
				return [
					'code' => $code,
					'type' => $this->cUrl->getErrorType($code),
					'message' => $this->cUrl->getErrorMessage()
				];
			}else{
				return [];
			}
		}
		return null;
	}

	/**
	 * getErrorMessage()
	 * 获取错误提示信息
	 * -----------------
	 * @return String
	 * @author Verdient。
	 */
	public function getErrorMessage(){
		if($this->hasError()){
			$error = $this->getError();
			if(isset($error['message'])){
				return $error['message'];
			}else{
				return 'Unknown Error';
			}
		}
		return null;
	}

	/**
	 * getErrorCode()
	 * 获取错误码
	 * --------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getErrorCode(){
		if($this->hasError()){
			$error = $this->getError();
			if(isset($error['code'])){
				return $error['code'];
			}else{
				return $this->getStatusCode();
			}
		}
		return null;
	}
}