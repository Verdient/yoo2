<?php
namespace yoo\components\cUrl\builder;

use yii\helpers\Json;

/**
 * JsonBuilder
 * JSON构建器
 * -----------
 * @author Verdient。
 */
class JsonBuilder extends Builder
{
	/**
	 * toString()
	 * 转为字符串
	 * ----------
	 * @return String
	 * @author Verdient。
	 */
	public function toString(){
		return Json::encode($this->getElements());
	}

	/**
	 * headers()
	 * 附加的头部
	 * ---------
	 * @return Array
	 * @author Verdient。
	 */
	public function headers(){
		return [
			'Content-Type' => 'application/json'
		];
	}
}