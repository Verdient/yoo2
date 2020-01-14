<?php
namespace yoo\helpers;

use Ramsey\Uuid\Uuid;

/**
 * UUIDHelper
 * UUID助手
 * ----------
 * @author Verdient。
 */
class UUIDHelper
{
	/**
	 * uuid1(Integer|String $node, Integer $clockSeq)
	 * 生成V1版本UUID
	 * ----------------------------------------------
	 * @param Integer|String $node 节点标识
	 * @param Integer $clockSeq 时钟防撞
	 * -----------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function uuid1($node = null, $clockSeq = null){
		return Uuid::uuid1()->toString($node, $clockSeq);
	}

	/**
	 * uuid4()
	 * 生成V4版本UUID
	 * -------------
	 * @return String
	 * @author Verdient。
	 */
	public static function uuid4(){
		return Uuid::uuid4()->toString();
	}
}