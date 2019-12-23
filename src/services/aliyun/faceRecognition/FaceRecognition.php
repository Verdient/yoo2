<?php
namespace yoo\services\aliyun\faceRecognition;

/**
 * FaceRecognition
 * 人脸识别
 * ---------------
 * @author Jx.
 */
class FaceRecognition extends \yoo\base\RESTComponent
{
	/**
	 * @var String $accessKeyId
	 * 授权编号
	 * ------------------------
	 * @author Jx.
	 */
	public $accessKeyId;

	/**
	 * @var String $accessSecret
	 * 授权秘钥
	 * -------------------------
	 * @author Jx.
	 */
	public $accessSecret;

	/**
	 * getRequest()
	 * 获取请求对象
	 * ------------
	 * @return FaceRecognitionRequest
	 * @author Jx.
	 */
	public function getRequest($action){
		return new FaceRecognitionRequest([
			'accessKeyId' => $this->accessKeyId,
			'accessSecret' => $this->accessSecret,
			'url' => $this->getUrl($action),
			'bodySerializer' => 'urlencoded'
		]);
	}

	/**
	 * compareFaces(String $sourceImageValue, String $targetImageValue)
	 * 人脸识别
	 * ----------------------------------------------------------------
	 * @param String $sourceImageValue 用户人脸照
	 * @param String $targetImageValue 用户证件照
	 * -----------------------------------------
	 * @return FourElementsResponse
	 * @author Jx.
	 */
	public function compareFaces($sourceImageValue, $targetImageValue){
		return $this->getRequest('compareFaces')->setBody(['SourceImageValue' => $sourceImageValue, 'TargetImageValue' => $targetImageValue])->send();
	}
}