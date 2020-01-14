<?php
namespace yoo\components\cUrl\builder;

/**
 * UrlencodedBuilder
 * Urlencoded构建器
 * -----------------
 * @author Verdient。
 */
class UrlencodedBuilder extends Builder
{
	/**
	 * toString()
	 * 转为字符串
	 * ----------
	 * @return String
	 * @author Verdient。
	 */
	public function toString(){
		return http_build_query($this->getElements());
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
			'Content-Type' => 'application/x-www-form-urlencoded'
		];
	}
}