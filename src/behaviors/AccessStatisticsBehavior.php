<?php
namespace yoo\behaviors;

use yii\web\Controller;
use yoo\models\AccessLog;

/**
 * AccessStatisticsBehavior
 * 访问统计行为
 * ------------------------
 * @author Verdient。
 */
class AccessStatisticsBehavior extends \yii\base\Behavior
{
	/**
	 * events()
	 * 附加事件
	 * --------
	 * @return Array
	 * @author Verdient。
	 */
	public function events(){
		return [
			Controller::EVENT_BEFORE_ACTION => 'recording'
		];
	}

	/**
	 * recording()
	 * 记录
	 * -----------
	 * @author Verdient。
	 */
	public function recording($event){
		$action = $event->action->id;
		$controller = $event->action->controller->id;
		AccessLog::generate(time(), $controller, $action);
	}
}