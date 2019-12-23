<?php
namespace yoo\behaviors;

use yii\base\Behavior;
use yoo\helpers\UUIDHelper;

/**
 * RESTBehavior
 * REST行为
 * ------------
 * @author Verdient。
 */
class RESTBehavior extends Behavior
{
	/**
	 * generateRequestId()
	 * 生成请求编号
	 * -------------------
	 * @return String
	 * @author Verdient。
	 */
	public function generateRequestId(){
		return UUIDHelper::uuid1();
	}
}