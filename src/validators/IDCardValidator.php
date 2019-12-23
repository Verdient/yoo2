<?php
namespace yoo\validators;

/**
 * IDCardValidator
 * 身份证号验证器
 * ---------------
 * @author Verdient。
 */
class IDCardValidator extends Validator
{
	/**
	 * @var $pattern
	 * 正则表达式
	 * -------------
	 * @author Verdient。
	 */
	public $pattern = '/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}(0[1-9]|(1[0-2]))(0[1-9]|([1|2])\d|3[0-1])((\d{4})|\d{3}[X])$)$/';

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
			$this->message = '{attribute} is not a valid id card number.';
		}
	}

	/**
	 * validateValue(Mixed $value)
	 * 验证属性
	 * ---------------------------
	 * @param Mixed $value 验证值
	 * --------------------------
	 * @inheritdoc
	 * -----------
	 * @return Null/Array
	 * @author Verdient。
	 */
	protected function validateValue($value){
		$value = strtoupper($value);
		if(preg_match($this->pattern, $value)){
			if(strlen($value) == 18){
				$value = str_split($value);
				$idCardWi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
				$idCardY = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
				$idCardWiSum = 0;
				for($i = 0; $i < 17; $i++){
					$idCardWiSum += $value[$i] * $idCardWi[$i];
				}
				$idCardMod = $idCardWiSum % 11;
				if($value[17] == $idCardY[$idCardMod]){
					return null;
				}
			}
		}
		return [$this->message, []];
	}
}