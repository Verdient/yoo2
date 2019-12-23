<?php
namespace yoo\helpers;

/**
 * RandomHelper
 * 随机助手
 * ------------
 * @author Verdient。
 */
class RandomHelper
{
	/**
	 * number(Integer $min, Integer $max)
	 * 随机数
	 * ----------------------------------
	 * @param Integer $min 下限
	 * @param Integer $max 上限
	 * ------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public static function number($min, $max){
		return mt_rand($min, $max);
	}
}