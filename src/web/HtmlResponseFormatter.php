<?php
namespace yoo\web;

use yii\helpers\VarDumper;

/**
 * HtmlResponseFormatter
 * HTML响应格式化器
 * ---------------------
 * @author Verdient。
 */
class HtmlResponseFormatter extends \yii\web\HtmlResponseFormatter
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
		if(is_string($response->data)){
			parent::format($response);
			return true;
		}else{
			$response->content = VarDumper::dumpAsString($response->data);
			return true;
		}
		return false;
	}
}