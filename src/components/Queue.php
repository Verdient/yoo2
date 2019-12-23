<?php
namespace yoo\components;

use yii\di\Instance;

/**
 * Queue
 * 队列
 * -----
 * @author Verdient。
 */
class Queue extends \yoo\base\Component
{
	/**
	 * @var $redis
	 * Redis 组件
	 * -----------
	 * @author Verdient。
	 */
	public $redis = 'redis';

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
		$this->redis = Instance::ensure($this->redis);
	}

	/**
	 * push(String $name, String $value[, Boolean $head = false])
	 * 推入队列
	 * ----------------------------------------------------------
	 * @param String $name 名称
	 * @param String $value 内容
	 * @param Boolean $head 是否推入头部
	 * -------------------------------
	 * @return Integer|False
	 * @author Verdient。
	 */
	public function push($name, $value, $head = false){
		if($value === null){
			return false;
		}
		return $head ? $this->redis->lpush($name, $value) : $this->redis->rpush($name, $value);
	}

	/**
	 * pop(String $name[, Boolean $head = true])
	 * 从队列中弹出
	 * -----------------------------------------
	 * @param String $name 名称
	 * @param Boolean $head 是否从头部弹出
	 * ---------------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public function pop($name, $head = true){
		return $head ? $this->redis->lpop($name) : $this->redis->rpop($name);
	}

	/**
	 * batchPop(String $name, Integer $size, [, Boolean $head = true])
	 * 批量从队列中弹出
	 * ---------------------------------------------------------------
	 * @param String $name 名称
	 * @param Boolean $head 是否从头部弹出
	 * ---------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function batchPop($name, $size, $head = true){
		if($head === true){
			$this->redis->multi();
			$end = $size - 1;
			$this->redis->lrange($name, 0, $end);
			$this->redis->ltrim($name, $size, -1);
			$rows = $this->redis->exec()[0];
		}else{
			$this->redis->multi();
			$start = -$size;
			$this->redis->lrange($name, $start, -1);
			$this->redis->ltrim($name, 0, $start - 1);
			$rows = $this->redis->exec()[0];
			$rows = array_reverse($rows);
		}
		return $rows;
	}

	/**
	 * length(String $name)
	 * 获取队列长度
	 * --------------------
	 * @param String $name 名称
	 * -----------------------
	 */
	public function length($name){
		return (int) $this->redis->llen($name);
	}

	/**
	 * each(String $name[, Integer $bathSize = 100, Boolean $head = true])
	 * 单个迭代
	 * -------------------------------------------------------------------
	 * @param String $name 队列名称
	 * @param Integer $bathSize 批大小
	 * @param Boolean $head 是否从头部弹出
	 * ---------------------------------
	 * @return BatchResult
	 * @author Verdient。
	 */
	public function each($name, $bathSize = 100, $head = true){
		foreach($this->batch($name, $bathSize, $head) as $rows){
			foreach($rows as $row){
				yield $row;
			}
		}
	}

	/**
	 * batch(String $name[, Integer $bathSize = 100, Boolean $head = true])
	 * 批量迭代
	 * --------------------------------------------------------------------
	 * @param String $name 队列名称
	 * @param Integer $bathSize 批大小
	 * @param Boolean $head 是否从头部弹出
	 * ---------------------------------
	 * @return BatchResult
	 * @author Verdient。
	 */
	public function batch($name, $bathSize = 100, $head = true){
		while(!empty($rows = $this->batchPop($name, $bathSize, $head))){
			yield $rows;
		}
	}
}