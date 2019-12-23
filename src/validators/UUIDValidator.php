<?php
namespace yoo\validators;

use yii\base\InvalidConfigException;

/**
 * UUIDValidator
 * UUID 验证器
 * -------------
 * @author Verdient。
 */
class UUIDValidator extends Validator
{
	/**
	 * @var public $version
	 * 版本
	 * --------------------
	 * @author Verdient。
	 */
	public $version = 'all';

	/**
	 * @var $enableArray
	 * 是否允许数组
	 * -----------------
	 * @author Verdient。
	 */
	public $enableArray = false;

	/**
	 * @var $patterns
	 * 正则集合
	 * --------------
	 * @author Verdient。
	 */
	public $patterns = [
		1 => '/^[0-9A-F]{8}-[0-9A-F]{4}-1[0-9A-F]{3}-[0-9A-F]{4}-[0-9A-F]{12}$/i',
		3 => '/^[0-9A-F]{8}-[0-9A-F]{4}-3[0-9A-F]{3}-[0-9A-F]{4}-[0-9A-F]{12}$/i',
		4 => '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
		5 => '/^[0-9A-F]{8}-[0-9A-F]{4}-5[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
		'all' => '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i',
	];

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
		if($this->message === null){
			$this->message = '{attribute} is not a valid uuid{version}';
		}
	}

	/**
	 * validateValue(Mixed $value)
	 * 验证属性
	 * ---------------------------
	 * @param Mixed $value 验证值
	 * --------------------------
	 * @author Verdient。
	 */
	public function validateValue($value){
		if(is_array($value) && $this->enableArray === true){
			foreach($value as $element){
				if(!$this->validateUUID($element, $this->version)){
					return [$this->message, ['version' => $this->version == 'all' ? '' : ' v' . $this->version]];
				}
			}
		}else if(!$this->validateUUID($value, $this->version)){
			return [$this->message, ['version' => $this->version == 'all' ? '' : ' v' . $this->version]];
		}
		return null;
	}


	/**
	 * validateUUID(String $value, Mixed $version)
	 * 校验UUID
	 * -------------------------------------------
	 * @param String $value 值
	 * @param Mixed $version 版本
	 * --------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	protected function validateUUID($value, $version){
		if(!isset($this->patterns[$this->version])){
			throw new InvalidConfigException('Unsupported UUID version: ' . $version);
		}
		if(!is_string($value)){
			return false;
		}
		return preg_match($this->patterns[$version], $value);
	}
}