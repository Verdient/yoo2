<?php
namespace yoo\console;

use Yii;
use yoo\helpers\TimeHelper;

/**
 * ConsoleController
 * 命令行控制器
 * -----------------
 * @author Verdient。
 */
abstract class ConsoleController extends Controller
{
	/**
	 * @var $maxExecuteTime
	 * 最大执行时间
	 * --------------------
	 * @author Verdient。
	 */
	public $maxExecuteTime = 600;

	/**
	 * @var $_startAt
	 * 开始时间
	 * --------------
	 * @author Verdient。
	 */
	protected $_startAt = 0;

	/**
	 * beforeAction(Action $action)
	 * 动作前的操作
	 * ----------------------------
	 * @param Action $action 动作对象
	 * -----------------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function beforeAction($action){
		parent::beforeAction($action);
		$this->_startAt = TimeHelper::timestamp(true);
		set_time_limit($this->maxExecuteTime);
		return true;
	}

	/**
	 * afterAction(Action $action, Mixed $result)
	 * 动作后的操作
	 * ------------------------------------------
	 * @param Action $action 动作对象
	 * @param Mixed $result 结果
	 * ------------------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function afterAction($action, $result){
		$const = TimeHelper::timestamp(true) - $this->_startAt;
		Yii::trace('[' . $action->controller->id . '/' .$action->id . '] run cost ' . $const . ' milliseconds', __METHOD__);
		$this->stdout('[' . $action->controller->id . '/' .$action->id . '] run cost ' . $const . ' milliseconds' . "\n");
		return parent::afterAction($action, $result);
	}
}