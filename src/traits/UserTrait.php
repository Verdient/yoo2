<?php
namespace yoo\traits;

use Yii;

/**
 * UserTrait
 * 用户特性
 * ---------
 * @author Verdient。
 */
trait UserTrait
{
	/**
	 * getOperatorIdentity()
	 * 获取操作人编号
	 * ---------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getOperatorIdentity(){
		return static::operatorIdentity();
	}

	/**
	 * operatorIdentity()
	 * 操作人编号
	 * ------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public static function operatorIdentity(){
		$identity = static::identity();
		return $identity ? $identity->id : null;
	}

	/**
	 * userComponent()
	 * 用户组件
	 * ---------------
	 * @return User/Null
	 * @author Verdient。
	 */
	public static function userComponent(){
		if(method_exists(Yii::$app, 'getUser')){
			return Yii::$app->getUser();
		}
		return null;
	}

	/**
	 * getUserComponent()
	 * 获取用户对象
	 * ------------------
	 * @return User
	 * @author Verdient。
	 */
	public function getUserComponent(){
		return static::userComponent();
	}

	/**
	 * isGuset()
	 * 是否是访客
	 * ---------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function isGuset(){
		$user = static::userComponent();
		return $user ? $user->getIsGuest() : true;
	}

	/**
	 * getIsGuest()
	 * 获取是否是访客用户
	 * ---------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function getIsGuest(){
		return static::isGuset();
	}

	/**
	 * identity()
	 * 认证信息
	 * ----------
	 * @return User
	 * @author Verdient。
	 */
	public static function identity(){
		$user = static::userComponent();
		return $user ? $user->getIdentity() : null;
	}

	/**
	 * getIdentity()
	 * 获取认证信息
	 * -------------
	 * @return User
	 * @author Verdient。
	 */
	public function getIdentity(){
		return static::identity();
	}
}