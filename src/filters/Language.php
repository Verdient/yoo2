<?php
namespace yoo\filters;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Language
 * 语言
 * --------
 * @author Verdient。
 */
class Language extends \yoo\base\ActionFilter
{
	/**
	 * @var $name
	 * 字段名称
	 * ----------
	 * @author Verdient。
	 */
	public $name = 'Accept-Language';

	/**
	 * @var Array $languages
	 * 支付的语言集合
	 * ---------------------
	 * @author Verdient。
	 */
	public $languages = [];

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
		if(!is_string($this->name)){
			throw new InvalidConfigException('name must be a string, ' . gettype($this->name) . ' given');
		}
		if(empty($this->languages)){
			$this->languages[] = Yii::$app->language;
		}
	}

	/**
	 * beforeAction(Action $action)
	 * 执行登录前的操作
	 * ----------------------------
	 * @param Action $action 动作对象
	 * -----------------------------
	 * @inheritdoc
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function beforeAction($action){
		if($language = $this->getLanguage()){
			Yii::$app->language = $language;
		}
		return true;
	}

	/**
	 * getAuthentication()
	 * 获取认证字符串
	 * --------------------
	 * @return String|Null
	 * @author Verdient。
	 */
	public function getLanguage(){
		return Yii::$app->getRequest()->getPreferredLanguage($this->languages);
	}
}