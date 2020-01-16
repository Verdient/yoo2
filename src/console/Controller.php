<?php
namespace yoo\console;

use yii\helpers\Console;

/**
 * Controller
 * 控制器基类
 * ----------
 * @author Verdient。
 */
abstract class Controller extends \yii\console\Controller
{
	/**
	 * message(String $message)
	 * 提示信息
	 * ------------------------
	 * @param String $message 提示信息
	 * ------------------------------
	 * @author Verdient。
	 */
	public function message($message){
		if(is_array($message)){
			foreach($message as $row){
				$this->stdout($row . "\n");
			}
		}else{
			$this->stdout($message . "\n");
		}
	}

	/**
	 * success(String $message)
	 * 成功消息
	 * ------------------------
	 * @param String $message 提示信息
	 * ------------------------------
	 * @author Verdient。
	 */
	public function success($message){
		if(is_array($message)){
			foreach($message as $index => $row){
				$prefix = null;
				if($index === 0){
					$prefix = '[OK] ';
				}else{
					$prefix = str_repeat(' ', 5);
				}
				$this->stdout($prefix, Console::FG_GREEN);
				$this->stdout($row . "\n", Console::FG_GREY);
			}
		}else{
			$this->stdout('[OK] ', Console::FG_GREEN);
			$this->stdout($message . "\n", Console::FG_GREY);
		}
	}

	/**
	 * info(String $message)
	 * 提示消息
	 * ---------------------
	 * @param String $message 提示信息
	 * ------------------------------
	 * @author Verdient。
	 */
	public function info($message){
		if(is_array($message)){
			foreach($message as $index => $row){
				$prefix = null;
				if($index === 0){
					$prefix = '[INFO] ';
				}else{
					$prefix = str_repeat(' ', 7);
				}
				$this->stdout($prefix, Console::FG_YELLOW);
				$this->stdout($row . "\n", Console::FG_GREY);
			}
		}else{
			$this->stdout('[INFO] ', Console::FG_YELLOW);
			$this->stdout($message . "\n", Console::FG_GREY);
		}
	}

	/**
	 * error(String $message)
	 * 错误消息
	 * ----------------------
	 * @param String $message 提示信息
	 * ------------------------------
	 * @author Verdient。
	 */
	public function error($message){
		if(is_array($message)){
			foreach($message as $index => $row){
				$prefix = null;
				if($index === 0){
					$prefix = '[ERROR] ';
				}else{
					$prefix = str_repeat(' ', 8);
				}
				$this->stderr($prefix, Console::FG_RED);
				$this->stderr($row . "\n", Console::FG_GREY);
			}
		}else{
			$this->stderr('[ERROR] ', Console::FG_RED);
			$this->stderr($message . "\n", Console::FG_GREY);
		}
	}

	/**
	 * fatalError(String $message[, Integer $code = 1])
	 * 致命错误
	 * ------------------------------------------------
	 * @param String $message 提示信息
	 * @param Integer $code 退出代码
	 * ------------------------------
	 * @author Verdient。
	 */
	public function fatalError($message, $code = 1){
		$this->error($message);
		exit($code);
	}

	/**
	 * scrollDown([Integer $lines = 1])
	 * 向下滚动
	 * --------------------------------
	 * @param Integer $lines 行数
	 * --------------------------
	 * @author Verdient。
	 */
	public function scrollDown($lines = 1){
		return Console::scrollDown($lines);
	}
}