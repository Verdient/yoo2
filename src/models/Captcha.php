<?php
namespace yoo\models;

/**
 * Captcha
 * 验证码
 * -------
 * @author Verdient。
 */
abstract class Captcha extends \yoo\db\ActiveRecord
{
	/**
	 * @var const STATUS_UNUSED
	 * 未使用
	 * ------------------------
	 * @author Verdient。
	 */
	const STATUS_UNUSED = 2;

	/**
	 * @var const STATUS_USED
	 * 已使用
	 * ----------------------
	 * @author Verdient。
	 */
	const STATUS_USED = 3;

	/**
	 * @var const STATUS_EXPIRED
	 * 已过期
	 * -------------------------
	 * @author Verdient。
	 */
	const STATUS_EXPIRED = 4;

	/**
	 * @var const CHANNEL_SMS
	 * 短信
	 * ----------------------
	 * @author Verdient。
	 */
	const CHANNEL_SMS = 1;

	/**
	 * @var const CHANNEL_EMAIL
	 * 电子邮件
	 * ------------------------
	 * @author Verdient。
	 */
	const CHANNEL_EMAIL = 2;

	/**
	 * validateCaptcha(String $receiver, String $captcha[, Integer $type = null, Boolean $remove = true])
	 * 校验验证码
	 * --------------------------------------------------------------------------------------------------
	 * @param String $receiver 接收者
	 * @param String $captcha 验证码
	 * @param Integer $type 类型
	 * @param Boolean $remove 验证后是否删除
	 * -----------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function validateCaptcha($receiver, $captcha, $type = null, $remove = true){
		$rows = static::find()
			->where([
				'status' => static::STATUS_UNUSED,
				'receiver' => $receiver,
				'captcha' => $captcha
			])
			->andFilterWhere(['type' => $type])
			->all();
		if(empty($rows)){
			return false;
		}
		if($remove === true){
			foreach($rows as $row){
				$row->delete();
			}
		}
		return true;
	}
}