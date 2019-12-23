<?php
namespace yoo\traits;

use yii\web\UploadedFile;

/**
 * WebTrait
 * 网站特性
 * --------
 * @author Verdient。
 */
trait WebTrait
{
	/**
	 * getBodyParams()
	 * 获取请求体参数
	 * ---------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getBodyParams(){
		return $this->getRequest()->getBodyParams();
	}

	/**
	 * getQueryParams()
	 * 获取查询参数
	 * ----------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getQueryParams(){
		return $this->getRequest()->getQueryParams();
	}

	/**
	 * getRequestParams()
	 * 获取请求参数
	 * ------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getRequestParams(){
		return array_merge($this->getQueryParams(), $this->getBodyParams());
	}

	/**
	 * setStatusCode(Integer $value, String $text = null)
	 * 设置状态码
	 * --------------------------------------------------
	 * @param Integer $value 状态码
	 * @param String $text 状态码释义
	 * ----------------------------
	 * @return Controller
	 * @author Verdient。
	 */
	public function setStatusCode($value, $text = null){
		$this->getResponse()->setStatusCode($value, $text);
		return $this;
	}

	/**
	 * getUploadedFile([String $name = 'file'])
	 * 获取上传的文件
	 * ----------------------------------------
	 * @param String $name 上传字段名
	 * -----------------------------
	 * @return UploadedFile
	 * @author Verdient。
	 */
	public function getUploadedFile($name){
		return UploadedFile::getInstanceByName($name);
	}
}