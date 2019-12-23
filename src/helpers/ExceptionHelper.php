<?php
namespace yoo\helpers;

/**
 * ExceptionHelper
 * 异常助手
 * ---------------
 * @author Verdient。
 */
class ExceptionHelper
{
	/**
	 * isException(Mixed $data)
	 * 是否是异常
	 * ------------------------
	 * @param Mixed $data 数据
	 * -----------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function isException($data){
		return $data instanceof \Exception || $data instanceof \ParseError || $data instanceof \Error || $data instanceof \ErrorException;
	}
}