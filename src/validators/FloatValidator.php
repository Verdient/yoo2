<?php
namespace yoo\validators;

use Yii;
use yii\base\InvalidConfigException;

/**
 * FloatValidator
 * 浮点数验证器
 * --------------
 * @author Verdient。
 */
class FloatValidator extends Validator
{
	/**
	 * @var public $decimal
	 * 小数点位数
	 * --------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $decimal = false;

	/**
	 * @var public $min
	 * 最小值
	 * --------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $min = null;

	/**
	 * @var public $max
	 * 最大值
	 * --------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $max = null;

	/**
	 * @var public $tooSmall
	 * 过小提示信息
	 * ---------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $tooSmall = null;

	/**
	 * @var public $tooBig
	 * 过大提示信息
	 * -------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $tooBig = null;

	/**
	 * @var public $wrongDecimal
	 * 小数点后位数错误提示信息
	 * -------------------------
	 * @method Config
	 * @author Verdient。
	 */
	public $wrongDecimal = null;

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
			$this->message ='{attribute} must be a float.';
		}
		if($this->min !== null && $this->tooSmall === null){
			$this->tooSmall = '{attribute} must be no less than {min}.';
		}
		if($this->max !== null && $this->tooBig === null){
			$this->tooBig = '{attribute} must be no greater than {max}.';
		}
		if($this->wrongDecimal === null){
			$this->wrongDecimal = '{attribute} most {decimal} decimal places.';
		}
	}

	/**
	 * validateAttribute(Object $model, String $attribute)
	 * 验证属性
	 * ---------------------------------------------------
	 * @param Object $model 模型实例对象
	 * @param String $attribute 字段名称
	 * ---------------------------------
	 * @author Verdient。
	 */
	public function validateAttribute($model, $attribute){
		$model->$attribute = (string) $model->$attribute;
		$first = mb_substr($model->$attribute, 0, 1);
		$last = mb_substr($model->$attribute, -1, 1);
		if($first === '.'){
			$model->$attribute = mb_substr($model->$attribute, 1);
		}
		if($last === '.'){
			$model->$attribute = mb_substr($model->$attribute, 0, -1);
		}
		if(!is_numeric($model->$attribute) || !is_float((float) $model->$attribute)){
			return $this->addError($model, $attribute, $this->message);
		}
		if($this->decimal !== false){
			if(!is_integer($this->decimal)){
				throw new InvalidConfigException('decimal must be an integer');
			}
			if(!preg_match('/^-?[0-9]+(.[0-9]{1,' . $this->decimal . '})?$/', (string) $model->$attribute)){
				return $this->addError($model, $attribute, $this->wrongDecimal, ['decimal' => $this->decimal]);
			}
		}
		if($this->min !== null && $model->$attribute < $this->min){
			return $this->addError($model, $attribute, $this->tooSmall, ['min' => $this->min]);
		}
		if($this->max !== null && $model->$attribute > $this->max){
			return $this->addError($model, $attribute, $this->tooBig, ['max' => $this->max]);
		}
	}
}