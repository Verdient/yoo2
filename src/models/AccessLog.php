<?php
namespace yoo\models;

use yoo\helpers\TimeHelper;

/**
 * AccessLog
 * 访问日志
 * ---------
 * @author Verdient。
 */
class AccessLog extends \yoo\db\ActiveRecord
{
	/**
	 * generate(String $controller, String $action[, Integer $timestamp = null])
	 * 创建
	 * -------------------------------------------------------------------------
	 * @param String $controller 控制器
	 * @param String $action 动作
	 * @param Integer $timestamp 时间戳
	 * -------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function generate($controller, $action, $timestamp = null){
		if(!$timestamp){
			$timestamp = TimeHelper::timestamp(true);
		}
		(new static([
			'status' => static::STATUS_REGULAR,
			'timestamp' => $timestamp,
			'controller' => $controller,
			'action' => $action,
		]))->save(false);
	}
}