<?php
namespace yoo\services\aliyun;

use OSS\OssClient;

/**
 * OSS
 * 对象存储
 * -------
 * @author Verdient。
 */
class OSS extends \yii\base\Component
{
	/**
	 * @var $accessKeyID
	 * 授权编号
	 * -----------------
	 * @author Verdient。
	 */
	public $accessKeyID = null;

	/**
	 * @var $accessKeySecret
	 * 授权秘钥
	 * ---------------------
	 * @author Verdient。
	 */
	public $accessKeySecret = null;

	/**
	 * @var $endPoint
	 * 终端地址
	 * --------------
	 * @author Verdient。
	 */
	public $endPoint = null;

	/**
	 * @var $bucket
	 * 存储空间
	 * ------------
	 * @author Verdient。
	 */
	public $bucket = null;

	/**
	 * getClient()
	 * 获取客户端
	 * -----------
	 * @return OssClient
	 * @author Verdient。
	 */
	public function getClient(){
		return new OssClient($this->accessKeyID, $this->accessKeySecret, $this->endPoint);
	}

	/**
	 * putObject(String $name, String $content[, Array $options = []])
	 * 上传对象
	 * ---------------------------------------------------------------
	 * @param String $name 名称
	 * @param String $content 内容
	 * @param Array $options 参数
	 * --------------------------
	 * @return String|False
	 * @author Verdient。
	 */
	public function putObject($name, $content, $options = []){
		try{
			$response = $this->getClient()->putObject($this->bucket, $name, $content, $options);
			if(isset($response['info']) && isset($response['info']['url'])){
				return $response['info']['url'];
			}
		}catch(\Exception $e){
			return false;
		}
	}

	/**
	 * uploadFile(String $name, String $path[, Array $options = []])
	 * 上传文件
	 * -------------------------------------------------------------
	 * @param String $name 名称
	 * @param String $path 路径
	 * @param Array $options 参数
	 * -------------------------
	 * @return String|False
	 * @author Verdient。
	 */
	public function uploadFile($name, $path, $options = []){
		try{
			$response = $this->getClient()->uploadFile($this->bucket, $name, $path, $options);
			if(isset($response['info']) && isset($response['info']['url'])){
				return $response['info']['url'];
			}
		}catch(\Exception $e){
			return false;
		}
		return false;
	}

	/**
	 * getObject(String $name[, Array $options = []])
	 * 获取对象
	 * ----------------------------------------------
	 * @param String $name 名称
	 * @param Array $options 参数
	 * --------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getObject($name, $options = []){
		try{
			return $this->getClient()->getObject($this->bucket, $name, $options);
		}catch(\Exception $e){
			return false;
		}
	}

	/**
	 * getObjectAsFile(String $name, String $path[, Array $options = []])
	 * 获取对象
	 * ------------------------------------------------------------------
	 * @param String $name 名称
	 * @param String $path 路径
	 * @param Array $options 参数
	 * -------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getObjectAsFile($name, $path, $options = []){
		$options = array_merge($options, [
			OssClient::OSS_FILE_DOWNLOAD => $path
		]);
		try{
			return $this->getClient()->getObject($this->bucket, $name, $options) !== false;
		}catch(\Exception $e){
			return false;
		}
	}
}