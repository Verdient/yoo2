<?php
namespace yoo\validators;

use Yii;
use yoo\models\Captcha;

/**
 * CaptchaValidator
 * 验证码校验器
 * ----------------
 * @author Verdient。
 */
class CaptchaValidator extends \yii\validators\Validator
{
	/**
	 * @var String|Array $mark
	 * 标识
	 * -----------------------
	 * @author Verdient。
	 */
	public $mark = false;

	/**
	 * @var String $targetAttribute
	 * 目标属性
	 * ----------------------------
	 * @author Verdient。
	 */
	public $targetAttribute = 'mobile';

	/**
	 * @var String|Array $type
	 * 类型
	 * -----------------------
	 * @author Verdient。
	 */
	public $type;

	/**
	 * @var Boolean $remove
	 * 验证后是否移除
	 * --------------------
	 * @author Verdient。
	 */
	public $remove = true;

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
			$this->message = 'Captcha error';
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
		$targetAttribute = $this->targetAttribute;
		$mark = $this->mark ?: $model->$targetAttribute;
		if(!YII_DEBUG){
			if(is_array($mark)){
				foreach($mark as $row){
					if(is_array($this->type)){
						foreach($this->type as $type){
							if(Captcha::validateCaptcha($row, $value, $type, $this->remove)){
								return;
							}
						}
					}else{
						if(Captcha::validateCaptcha($row, $value, $this->type, $this->remove)){
							return;
						}
					}
				}
			}else{
				if(is_array($this->type)){
					foreach($this->type as $type){
						if(Captcha::validateCaptcha($mark, $value, $type, $this->remove)){
							return;
						}
					}
				}else{
					if(Captcha::validateCaptcha($mark, $value, $this->type, $this->remove)){
						return;
					}
				}
			}
			$this->addError($model, $attribute, $this->message);
		}
	}
}