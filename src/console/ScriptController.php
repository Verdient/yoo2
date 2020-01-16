<?php
namespace yoo\console;

/**
 * ScriptController
 * 脚本控制器
 * ----------------
 * @author Verdient。
 */
abstract class ScriptController extends Controller
{
	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		set_time_limit(0);
	}

	/**
	 * runAction(String $id[, Array $params = []])
	 * 执行动作
	 * -------------------------------------------
	 * @param String $id 动作编号
	 * @param Array $params 参数
	 * -------------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function runAction($id, $params = []){
		while(true){
			$result = parent::runAction($id, $params);
			if($result === false){
				break;
			}
		}
		return $result;
	}
}