<?php
namespace yoo\validators;

use Yii;

/**
 * CompareValidator
 * 对比校验器
 * ----------------
 * @author Verdient。
 */
class CompareValidator extends \yii\validators\CompareValidator
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
		if($this->message === null){
			$this->message = Yii::t('yii', '{attribute} is invalid');
		}
	}

	/**
	 * validateAttribute(Object $model, String $attribute)
	 * 校验属性
	 * ---------------------------------------------------
	 * @param Object $model 要校验的对象
	 * @param String $attribute 属性
	 * --------------------------------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	public function validateAttribute($model, $attribute){
		$value = $model->$attribute;
		if(is_array($value)){
			$this->addError($model, $attribute, Yii::t('yii', '{attribute} is invalid.'));
			return;
		}
		if($this->compareValue !== null || $this->compareAttribute === null){
			$compareLabel = $compareValue = $compareValueOrAttribute = $this->compareValue;
		}else{
			$compareAttribute = $this->compareAttribute;
			$compareValue = $model->$compareAttribute;
			$compareLabel = $compareValueOrAttribute = $model->getAttributeLabel($compareAttribute);
		}
		if($model->isAttributeChanged($attribute) && !$this->compareValues($this->operator, $this->type, $value, $compareValue)){
			$this->addError($model, $attribute, $this->message, [
				'compareAttribute' => $compareLabel,
				'compareValue' => $compareValue,
				'compareValueOrAttribute' => $compareValueOrAttribute === null ? 'null' : $compareValueOrAttribute,
			]);
		}
	}
}