<?php
namespace yoo\components;

/**
 * Signature
 * 签名
 * ---------
 * @author Verdient。
 */
class Signature extends \yoo\base\Component
{
	/**
	 * @var $method
	 * 签名方法
	 * ------------
	 * @author Verdient。
	 */
	public $method = 'sha256';

	/**
	 * @var $key
	 * 秘钥
	 * ---------
	 * @author Verdient。
	 */
	public $key = null;

	/**
	 * sign(String $value[, String $key = null, String $method = null])
	 * 签名
	 * ----------------------------------------------------------------
	 * @param String $value 待签名的值
	 * @param String $key 秘钥
	 * @param String $method 签名的方法
	 * -------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function sign($value, $key = null, $method = null){
		$key = $key ?: $this->key;
		$method = strtolower($method ?: $this->method);
		$result = hash($method, $value . $key);
		return $result;
	}

	/**
	 * validate(String $value, String $sign, String $key[, String $method = null])
	 * 验证签名
	 * ---------------------------------------------------------------------------
	 * @param String $value 待签名的内容
	 * @param String $sign 签名
	 * @param String $key 签名秘钥
	 * @param String $method 签名方法
	 * -------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function validate($value, $sign, $key, $method = null){
		$key = $key ?: $this->key;
		return $this->sign($value, $key, $method) === $sign;
	}
}