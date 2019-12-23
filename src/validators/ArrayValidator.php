<?php
namespace yoo\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * ArrayValidator
 * 数组验证器
 * --------------
 * @version 1.0.0
 * @author Verdient。
 */
class ArrayValidator extends Validator
{
	/**
	 * @var $enableString
	 * 允许字符串
	 * ------------------
	 * @author Verdient。
	 */
	public $enableString = false;

	/**
	 * @var $delimiter
	 * 分隔符
	 * ---------------
	 * @author Verdient。
	 */
	public $delimiter = ',';

	/**
	 * @var public $min
	 * 最少包含几个元素
	 * ----------------
	 * @author Verdient。
	 */
	public $min = null;

	/**
	 * @var public $max
	 * 最多包含几个元素
	 * ----------------
	 * @author Verdient。
	 */
	public $max = null;

	/**
	 * @var public $tooBig
	 * 过小提示信息
	 * -------------------
	 * @author Verdient。
	 */
	public $tooSmall = null;

	/**
	 * @var public $tooBig
	 * 过大提示信息
	 * --------------------
	 * @author Verdient。
	 */
	public $tooBig = null;

	/**
	 * @var public $multidimensional
	 * 多维数组
	 * -----------------------------
	 * @author Verdient。
	 */
	public $multidimensional = false;

	/**
	 * @var public $unique
	 * 是否去重
	 * -------------------
	 * @author Verdient。
	 */
	public $unique = null;

	/**
	 * @var public $indexedOnly
	 * 是否只索引数组
	 * ------------------------
	 * @author Verdient。
	 */
	public $indexedOnly = false;

	/**
	 * init()
	 * 初始化
	 * ------
	 * @inheritdoc
	 * ------------
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		if($this->message === null){
			$this->message = Yii::t('yii', '{attribute} must be an array.');
		}
		if($this->min !== null && $this->tooSmall === null){
			$this->tooSmall = Yii::t('yii', '{attribute} must contain less than {min} elements.');
		}
		if($this->max !== null && $this->tooBig === null){
			$this->tooBig = Yii::t('yii', '{attribute} must contain more than than {max} elements.');
		}
		if($this->min !== null && !is_integer($this->min)){
			throw new InvalidConfigException('min must be an integer');
		}
		if($this->max !== null && !is_integer($this->max)){
			throw new InvalidConfigException('max must be an integer');
		}
		if(!is_bool($this->multidimensional)){
			throw new InvalidConfigException('multidimensional must be an boolean');
		}
		if(!is_null($this->unique) && !is_bool($this->unique)){
			throw new InvalidConfigException('unique must be an boolean');
		}
		if(!is_bool($this->indexedOnly)){
			throw new InvalidConfigException('indexedOnly must be an boolean');
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
		$isIndexed = false;
		if(is_string($model->$attribute) && $this->enableString === true){
			$model->$attribute = explode($this->delimiter, $model->$attribute);
		}
		if(!is_array($model->$attribute)){
			return $this->addError($model, $attribute, $this->message);
		}
		if($this->indexedOnly === true){
			if(!$isIndexed = ArrayHelper::isIndexed($model->$attribute, true)){
				return $this->addError($model, $attribute, $this->message);
			}
		}
		if($this->multidimensional !== true){
			foreach($model->$attribute as $element){
				if(is_array($element)){
					return $this->addError($model, $attribute, $this->message);
				}
			}
		}
		if($this->min !== null || $this->max !== null){
			$count = count($model->$attribute);
			if($count < $this->min){
				return $this->addError($model, $attribute, $this->tooSmall, ['min' => $this->min]);
			}
			if($count > $this->max){
				return $this->addError($model, $attribute, $this->tooBig, ['max' => $this->max]);
			}
		}
		if($this->unique === null || $this->unique === true){
			if($isIndexed === true || ArrayHelper::isIndexed($model->$attribute, true)){
				$model->$attribute = array_values(array_unique($model->$attribute, SORT_REGULAR));
			}else if($this->unique === true){
				$model->$attribute = array_unique($model->$attribute);
			}
		}
	}
}