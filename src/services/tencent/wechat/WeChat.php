<?php
namespace yoo\services\tencent\wechat;

use yii\di\Instance;

/**
 * WeChat
 * 微信
 * ------
 * @author Verdient。
 */
class WeChat extends \yoo\base\RESTComponent
{
	/**
	 * @var String $appID
	 * App标识
	 * ------------------
	 * @author Verdient。
	 */
	public $appID;

	/**
	 * @var String $appSecret
	 * App秘钥
	 * ----------------------
	 * @author Verdient。
	 */
	public $appSecret;

	/**
	 * @var Mixed $cache
	 * 缓存组件
	 * -----------------
	 * @author Verdient。
	 */
	public $cache = 'cache';

	/**
	 * @var String $cacheKey
	 * 缓存键名
	 * ---------------------
	 * @author Verdient。
	 */
	public $cacheKey = 'weChatService';

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
		$this->cache = Instance::ensure($this->cache);
	}

	/**
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @author Verdient。
	 */
	public function getRequest($url,$method = 'post'){
		return new WeChatRequest([
			'url' => $this->getUrl($url),
			'method' => $method,
			'bodySerializer' => 'JSON'
		]);
	}

	/**
	 * getAccessToken([String $grantType = 'client_credential'])
	 * 获取授权秘钥
	 * ---------------------------------------------------------
	 * @param String $grantType 授权类型
	 * --------------------------------
	 * @return WeChatResponse
	 * @author Verdient。
	 */
	public function getAccessToken($grantType = 'client_credential'){
		if($response = $this->cache->get($this->cacheKey . 'accessToken')){
			return unserialize($response);
		}
		$response = $this->getRequest('getAccessToken', 'get')->setQuery([
			'grant_type' => $grantType,
			'appid' => $this->appID,
			'secret' => $this->appSecret
		])->send();
		if($response->getProcessSuccess()){
			$content = $response->getContent();
			$expiresIn = $content['expires_in'] - 10;
			$this->cache->set($this->cacheKey . 'accessToken', serialize($response), $expiresIn);
		}
		return $response;
	}


	/**
	 * getCode2Session(String $code[, $grantType = authorization_code])
	 * 获取临时秘钥
	 * ----------------------------------------------------------------
	 * @param String $code 编号
	 * @param String $grantType 授权类型
	 * --------------------------------
	 * @return WeChatResponse
	 * @author Verdient。
	 */
	public function getCode2Session($code, $grantType = 'authorization_code' ){
		return $this->getRequest('code2Session', 'get')->setQuery([
			'appid' => $this->appID,
			'secret' => $this->appSecret,
			'grant_type' => $grantType,
			'js_code' => $code
		])->send();
	}

	/**
	 * getUserInfo(String $code, String $iv, String $encryptedData)
	 * 获取用户信息
	 * ------------------------------------------------------------
	 * @param String $code 编码
	 * @param String $iv 初始向量
	 * @param String $encryptedData 加密的数据
	 * --------------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getUserInfo($code, $iv, $encryptedData){
		$response = $this->getCode2Session($code);
		if($response->getProcessSuccess()){
			$content = $response->getContent();
			$result = $this->decryptData($encryptedData, $iv, $content['session_key']);
			return [null, $result];
		}else{
			return [$response->getErrorMessage(), null];
		}
	}

	/**
	 * decryptData(String $encryptedData, String $iv, String $sessionkey)
	 * 检验数据的真实性，并且获取解密后的明文
	 * ------------------------------------------------------------------
	 * @param String $encryptedData 加密的用户数据
	 * @param String $iv 初始向量
	 * @param String $sessionkey 临时秘钥
	 * ------------------------------------------
	 * @return String|False
	 * @author Jon
	 */
	public function decryptData($encryptedData, $iv, $sessionkey){
		if(strlen($sessionkey) != 24){
			return false;
		}
		if(strlen($iv) != 24){
			return false;
		}
		$aesKey = base64_decode($sessionkey);
		$aesIV = base64_decode($iv);
		$aesCipher = base64_decode($encryptedData);
		$result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, OPENSSL_RAW_DATA, $aesIV);
		$dataObj = json_decode($result);
		if($dataObj == null){
			return false;
		}
		if($dataObj->watermark->appid != $this->appID) {
			return false;
		}
		return json_decode($result, true);
	}
}