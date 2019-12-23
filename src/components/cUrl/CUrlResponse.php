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
	 * @var $raw
	 * 源数据
	 * ---------
	 * @author Verdient。
	 */
	public $raw;

	/**
	 * @var $cUrl
	 * cUrl数据
	 * ----------
	 * @author Verdient。
	 */
	public $cUrl;

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
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
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
			if($this->raw['response']){
				$this->_content = $this->raw['content'];
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
			if($this->raw['header']){
				$this->_headers = [];
				$headers = explode("\r\n", $this->raw['header']);
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
		return $this->raw['statusCode'];
	}

	/**
	 * hasCUrlError()
	 * 是否有cUrl错误
	 * --------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function hasCUrlError(){
		return isset($this->cUrl['errorCode']);
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
		if($this->hasCUrlError()){
			return [
				'code' => $this->cUrl['errorCode'],
				'type' => $this->cUrl['errorType'],
				'message' => $this->cUrl['errorMessage']
			];
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
		if($this->hasCUrlError()){
			$error = $this->getError();
			if(isset($error['message'])){
				return $error['message'];
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
			}
		}
		return null;
	}
}