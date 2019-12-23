<?php
namespace yoo\filters;

use yoo\web\ServiceUnavailableException;

/**
 * Router
 * 路由过滤器
 * --------
 * @author Verdient。
 */
class Router extends \yoo\base\ActionFilter
{
	/**
	 * @var Array $routers
	 * 禁止访问的路由
	 * -------------------
	 * @author Verdient。
	 */
	public $routers = [];

	/**
	 * @var String $message
	 * 提示信息
	 * ---------------------
	 * @author Verdient。
	 */
	public $message = 'It\'s not open yet. Please stay tuned';

	/**
	 * beforeAction(Action $action)
	 * 执行登录前的操作
	 * ----------------------------
	 * @param Action $action 动作对象
	 * -----------------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function beforeAction($action){
		$controller = $action->controller->id;
		$action = $action->id;
		if(isset($this->routers[$controller])){
			$actions = $this->routers[$controller];
			if($actions === '*'){
				throw new ServiceUnavailableException($this->message);
			}else if(is_array($actions) && in_array($action, $actions)){
				throw new ServiceUnavailableException($this->message);
			}
		}
		return true;
	}
}