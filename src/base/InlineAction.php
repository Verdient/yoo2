<?php
namespace yoo\base;

/**
 * InlineAction
 * 行内动作
 * ------------
 * @author Verdient。
 */
class InlineAction extends \yii\base\InlineAction
{
	/**
	 * from(InlineAction $action)
	 * 通过来源创建动作
	 * --------------------------
	 * @param InlineAction $action 动作实例
	 * -----------------------------------
	 * @return InlineAction
	 * @author Verdient。
	 */
	public static function from($action){
		if($action instanceof \yii\base\InlineAction && !$action instanceof static){
			return new static($action->id, $action->controller, $action->actionMethod);
		}
		return $action;
	}

	/**
	 * runWithParams(Array $params)
	 * 带参数运行
	 * ----------------------------
	 * @param Array $params 参数
	 * -------------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function runWithParams($params){
		try{
			return parent::runWithParams($params);
		}catch(\Exception $e){
			return $e;
		}
	}
}