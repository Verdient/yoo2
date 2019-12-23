<?php
namespace yoo\traits;

use yoo\base\InlineAction;
use yoo\helpers\ExceptionHelper;

/**
 * ControllerTrait
 * 控制器特性
 * ---------------
 * @author Verdient。
 */
trait ControllerTrait
{
	use CommonTrait;

	/**
	 * createAction(String $id)
	 * 创建动作
	 * ------------------------
	 * @param String $id 动作编号
	 * -------------------------
	 * @inheritdoc
	 * -----------
	 * @return Action
	 * @author Verdient。
	 */
	public function createAction($id){
		$action = parent::createAction($id);
		if($action instanceof \yii\base\InlineAction){
			return InlineAction::from($action);
		}
		return null;
	}

	/**
	 * afterAction(Action $action, Mixed $result)
	 * 执行动作后的操作
	 * ------------------------------------------
	 * @param Action $action 动作对象
	 * @param Mixed $result 结果
	 * ------------------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function afterAction($action, $result){
		$outcome = parent::afterAction($action, $result);
		if(ExceptionHelper::isException($result)){
			throw $result;
		}
		return $outcome;
	}
}