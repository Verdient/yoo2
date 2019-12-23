<?php
namespace yoo\traits;

use Yii;
use yii\base\UnknownMethodException;
use yii\helpers\ArrayHelper;
use yoo\traits\UserTrait;

/**
 * ModelTrait
 * 模型特性
 * ----------
 * @author Verdient。
 */
trait ModelTrait
{
	use CommonTrait;
	use UserTrait;

	/**
	 * load(Array $data[, String $formName = ''])
	 * 将数据载入模型
	 * ------------------------------------------
	 * @param Array $data 要载入的数据
	 * @param String $formName 表单名称
	 * -------------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function load($data, $formName = ''){
		parent::load($data, $formName);
		return $this;
	}

	/**
	 * setScenario(String $value)
	 * 设置场景
	 * --------------------------
	 * @param String $value 场景
	 * -------------------------
	 * @return Self
	 * @author Verdient。
	 */
	public function setScenario($value){
		parent::setScenario($value);
		return $this;
	}

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
		foreach($this->events() as $event => $handler){
			$this->on($event, is_string($handler) ? [$this, $handler] : $handler);
		}
	}

	/**
	 * events()
	 * 事件
	 * --------
	 * @return Array
	 * @author Verdient。
	 */
	public function events(){
		return [];
	}

	/**
	 * __call(String $name, Array $arguments)
	 * 调用方法不存在时的方法
	 * --------------------------------------
	 * @param String $name 名字
	 * @param Array $arguments 参数
	 * ---------------------------
	 * @throws UnknownMethodException
	 * @return Mixed
	 * @author Verdient。
	 */
	public function __call($name, $arguments){
		if(strtoupper($name) === $name){
			return static::CONSTANT($name . '_');
		}
		return parent::__call($name, $arguments);
	}

	/**
	 * __callStatic(String $name, Array $arguments)
	 * 调用静态方法不存在时的方法
	 * --------------------------------------------
	 * @param String $name 名字
	 * @param Array $arguments 参数
	 * ---------------------------
	 * @throws UnknownMethodException
	 * @return Mixed
	 * @author Verdient。
	 */
	public static function __callStatic($name, $arguments){
		if(strtoupper($name) === $name){
			return static::CONSTANT($name . '_');
		}
		throw new UnknownMethodException('Calling unknown method ' . static::className() . '::' . $name . '()');
	}

	/**
	 * CONSTANT([String $prefix = null])
	 * 获取类所有的常量
	 * ---------------------------------
	 * @param String $prefix 前缀
	 * --------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public static function CONSTANT($prefix = null){
		$reflection = new \ReflectionClass(static::className());
		$consts = $reflection->getConstants();
		if($prefix === null){
			return $consts;
		}
		$result = [];
		$length = mb_strlen($prefix);
		foreach($consts as $name => $value){
			if(mb_substr($name, 0, $length) === $prefix){
				$result[] = $value;
			}
		}
		return $result;
	}

	/**
	 * addError(String $attribute, String $error = '')
	 * 新增错误
	 * -----------------------------------------------
	 * @param String $attribute 属性
	 * @param String $error 错误信息
	 * ----------------------------
	 * @author Verdient。
	 */
	public function addError($attribute, $error = ''){
		if(is_array($error) && count($error) === 2 && ArrayHelper::isIndexed($error)){
			list($error, $params) = $error;
		}else{
			$params = [];
		}
		$error = static::translateErrorMessage($error, $params);
		parent::addError($attribute, $error);
	}
}