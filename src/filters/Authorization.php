<?php
namespace yoo\filters;

use yii\di\Instance;

/**
 * authorization
 * 授权
 * -------------
 * @author Verdient。
 */
class Authorization extends Authentication
{
	/**
	 * @var $name
	 * 认证字段名称
	 * ----------
	 * @author Verdient。
	 */
	public $name = 'Authorization';

	/**
	 * @var Mixed $user
	 * 用户
	 * -----------------
	 * @author Verdient。
	 */
	public $user = 'user';

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * ------------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		$this->user = Instance::ensure($this->user);
	}

	/**
	 * authentication(String $authentication)
	 * 认证
	 * --------------------------------------
	 * @param String $authentication 认证信息
	 * -------------------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function authentication($authentication){
		$class = $this->user->identityClass;
		if($identity = $class::findIdentity($authentication)){
			$this->user->login($identity);
			return true;
		}
		return false;
	}
}