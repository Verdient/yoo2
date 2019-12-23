<?php
namespace yoo\web;

/**
 * XmlResponseFormatter
 * XML响应格式化器
 * ---------------------
 * @author Verdient。
 */
class XmlResponseFormatter extends \yii\web\XmlResponseFormatter
{
	/**
	 * format(Response $response)
	 * 格式化
	 * --------------------------
	 * @param Response $response 响应对象
	 * ---------------------------------
	 * @return Boolean
	 */
	public function format($response){
		$data = $response->data;
		if(is_array($data) || (is_object($data) && $data instanceof Arrayable)){
			parent::format($response);
			return true;
		}
		return false;
	}
}