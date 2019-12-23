<?php
namespace yoo\data;


use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yoo\db\ActiveQuery;

/**
 * ActiveDataProvider
 * 动态数据提供器
 * ------------------
 * @author Verdient。
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
	/**
	 * @var ActiveQuery|QueryInterface $query
	 * 查询对象
	 * ---------------------------------------
	 * @author Verdient。
	 */
	public $query;

	/**
	 * @var $searchModel
	 * 检索模型
	 * -----------------
	 * @author Verdient。
	 */
	public $searchModel = null;

	/**
	 * expandSearchModel()
	 * 扩展检索模型
	 * -------------------
	 * @author Verdient。
	 */
	public $expandSearchModel = null;

	/**
	 * @var $allowDeleted
	 * 允许已删除的数据
	 * ------------------
	 * @author Verdient。
	 */
	public $allowDeleted = false;

	/**
	 * @var $filterSerializer
	 * 过滤器序列化器
	 * ----------------------
	 * @author Verdient。
	 */
	public $filterSerializer = null;

	/**
	 * @var $modelsSerializer
	 * 模型序列化器
	 * ----------------------
	 * @author Verdient。
	 */
	public $modelsSerializer = null;

	/**
	 * @var $_errors
	 * 错误
	 * -------------
	 * @author Verdient。
	 */
	protected $_errors = [];

	/**
	 * hasErrors()
	 * 是否存在错误
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function hasErrors($attribute = null){
		return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
	}

	/**
	 * getErrors()
	 * 获取错误
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function getErrors($attribute = null){
		if ($attribute === null) {
			return $this->_errors === null ? [] : $this->_errors;
		}
		return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
	}

	/**
	 * getFirstErrors()
	 * 获取第一个错误
	 * ----------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getFirstErrors(){
		if (empty($this->_errors)) {
			return [];
		}
		$errors = [];
		foreach ($this->_errors as $name => $es) {
			if (!empty($es)) {
				$errors[$name] = reset($es);
			}
		}
		return $errors;
	}

	/**
	 * getFirstError(String $attribute)
	 * 获取属性第一个错误
	 * --------------------------------
	 * @param String $attribute 属性
	 * -----------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function getFirstError($attribute){
		return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
	}

	/**
	 * getErrorSummary(Boolean $showAllErrors)
	 * 获取错误统计
	 * ---------------------------------------
	 * @param Boolean $showAllErrors 展示所有错误
	 * -----------------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getErrorSummary($showAllErrors){
		$lines = [];
		$errors = $showAllErrors ? $this->getErrors() : $this->getFirstErrors();
		foreach ($errors as $es) {
			$lines = array_merge((array)$es, $lines);
		}
		return $lines;
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
		$this->_errors[$attribute][] = $error;
	}

	/**
	 * addErrors(Array $items)
	 * 批量添加错误
	 * ----------------------
	 * @param Array $items 错误集合
	 * ---------------------------
	 * @author Verdient。
	 */
	public function addErrors(array $items){
		foreach ($items as $attribute => $errors) {
			if (is_array($errors)) {
				foreach ($errors as $error) {
					$this->addError($attribute, $error);
				}
			} else {
				$this->addError($attribute, $errors);
			}
		}
	}

	/**
	 * clearErrors([String $attribute = null])
	 * 清除错误
	 * ---------------------------------------
	 * @param String $attribute 属性
	 * -----------------------------
	 * @author Verdient。
	 */
	public function clearErrors($attribute = null){
		if($attribute === null){
			$this->_errors = [];
		}else{
			unset($this->_errors[$attribute]);
		}
	}

	/**
	 * setErrors(Array $errors)
	 * 清除错误
	 * -----------------------
	 * @param Array $errors 错误集合
	 * ----------------------------
	 * @author Verdient。
	 */
	public function setErrors($errors){
		$this->_errors = $errors;
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
		$queryParams = Yii::$app->getRequest()->getQueryParams();
		// var_dump(http_build_query(['filter' => [
		// 	'attribute' => [
		// 		'operator' => 'value'
		// 	]
		// ]]));exit;
		if($this->searchModel){
			$this->_prepareSearch($queryParams);
		}
		if(!$this->hasErrors()){
			if(!empty($this->expandSearchModel)){
				$this->_prepareExpandSearch($queryParams);
			}
		}
		if($this->query instanceof ActiveQuery){
			if($this->allowDeleted === true && isset($queryParams['include_deleted']) && $queryParams['include_deleted'] === 'true'){
				$this->query->includeDeleted();
			}
		}
	}

	/**
	 * setPagination(Array|Pagination|Boolean $value)
	 * 设置分页组件
	 * ----------------------------------------------
	 * @param Array|Pagination|Boolean $value 值
	 * -----------------------------------------
	 * @inheritdoc
	 * -----------
	 * @throws InvalidArgumentException
	 * @author Verdient。
	 */
	public function setPagination($value){
		if(is_array($value)){
			$value = array_merge([
				'pageSizeLimit' => [1, 100]
			], $value);
		}
		return parent::setPagination($value);
	}

	/**
	 * _prepareSearch()
	 * 准备检索
	 * ---------------
	 * @author Verdient。
	 */
	protected function _prepareSearch(Array $requestParams){
		$dataFilter = (new ActiveDataFilter([
			'searchModel' => $this->searchModel
		]));
		$dataFilter->load($requestParams, '');
		$filter = $dataFilter->build();
		if($dataFilter->hasErrors()){
			$this->setErrors($dataFilter->getErrors());
		}else if(!empty($filter)){
			if(is_callable($this->filterSerializer)){
				$filter = call_user_func($this->filterSerializer, $filter);
			}
			$this->query->andWhere($filter);
		}
	}

	/**
	 * _prepareExpandSearch(Array $requestParams)
	 * 准备扩展检索
	 * ------------------------------------------
	 * @param Array $requestParams 请求参数
	 * -----------------------------------
	 * @author Verdient。
	 */
	protected function _prepareExpandSearch(Array $requestParams){
		$model = new $this->query->modelClass;
		if(isset($requestParams['expandFilter'])){
			foreach($requestParams['expandFilter'] as $name => $filter){
				if(isset($this->expandSearchModel[$name])){
					if(is_string($this->expandSearchModel[$name])){
						$class = $this->expandSearchModel[$name];
						$extraMethod = 'get' . ucfirst($name);
						$link = $model->$extraMethod()->link;
					}else if(is_array($this->expandSearchModel[$name])){
						$config = $this->expandSearchModel[$name];
						if(!isset($config['class'])){
							throw new InvalidConfigException('expandSearchModel[' . $name . '] class must be set');
						}
						$class = $config['class'];
						if(isset($config['link'])){
							$link = $config['link'];
						}else{
							$extraMethod = 'get' . ucfirst($name);
							$link = $model->$extraMethod()->link;
						}
					}else{
						throw new InvalidConfigException('expandSearchModel[' . $name . '] must be a string or Array');
					}
					if(ArrayHelper::isIndexed($link)){
						if(!$this->_prepareIndirectExpandSearch($class, $link, $filter, $name)){
							return false;
						}
					}else{
						if(!$this->_prepareDirectExpandSearch($class, $link, $filter, $name)){
							return false;
						}
					}
				}else{
					return $this->addError('expandFilter', Yii::t('message', 'Unknown expandFilter: ' . $name));
				}
			}
		}
	}

	/**
	 * _prepareDirectExpandSearch(String $class, Array $link, Array $filter, String $name)
	 * 准备直接扩展检索
	 * -----------------------------------------------------------------------------------
	 * @param String $class 类
	 * @param Array $link 关联关系
	 * @param Array $filter 过滤器
	 * @param Array $name 名称
	 * --------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	protected function _prepareDirectExpandSearch($class, $link, $filter, $name){
		$filterWhere = $this->_getExpandSearchFilter($class, $link, $filter);
		if(is_string($filterWhere)){
			$this->addError('expandSearchModel[' . $name . ']', $filterWhere);
			return false;
		}
		if(!is_array($filterWhere)){
			return false;
		}
		if(empty($filterWhere)){
			$this->query->where('0 = 1');
			return false;
		}else{
			$this->query->andWhere(array_merge(['OR'], $filterWhere));
			return true;
		}
	}

	/**
	 * _prepareIndirectExpandSearch(String $class, Array $link, Array $filter, String $name)
	 * 准备间接扩展检索
	 * -------------------------------------------------------------------------------------
	 * @param String $class 类
	 * @param Array $link 关联关系
	 * @param Array $filter 过滤器
	 * @param Array $name 名称
	 * --------------------------
	 * @return Boolean
	 * @author Verdient。
	 */
	protected function _prepareIndirectExpandSearch($class, $link, $filter, $name){
		$filterWhere = $filter;
		foreach($link as $subLink){
			$filterWhere = $this->_getExpandSearchFilter($subLink[0], $subLink[1], $filterWhere);
			if(is_string($filterWhere)){
				$this->addError('expandSearchModel[' . $name . ']', $filterWhere);
				return false;
			}
			if(!is_array($filterWhere)){
				return false;
			}
			if(empty($filterWhere)){
				$this->query->where('0 = 1');
				return false;
			}else{
				$filterWhere = ['or' => $filterWhere];
			}
		}
		$this->query->andWhere(array_merge(['OR'], $filterWhere['or']));
	}

	/**
	 * _getExpandSearchFilter(String $class, Array $link, Array $filter)
	 * 获取扩展检索过滤器
	 * -----------------------------------------------------------------
	 * @param String $class 类
	 * @param Array $link 关联关系
	 * @param Array $filter 过滤器
	 * --------------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	protected function _getExpandSearchFilter($class, $link, $filter){
		$expandDataFilter = (new ActiveDataFilter([
			'searchModel' => $class
		]));
		$expandDataFilter->load(['filter' => $filter], '');
		$where = $expandDataFilter->build();
		if($expandDataFilter->hasErrors()){
			return $expandDataFilter->getFirstError('filter');
		}else if(!empty($where)){
			$attibutes = array_keys($link);
			$filterWhere = [];
			foreach($class::find()->select($attibutes)->where($where)->each() as $row){
				$subWhere = [];
				foreach($link as $attibute => $linkAttribute){
					$subWhere[$linkAttribute] = $row[$attibute];
				}
				$filterWhere[] = $subWhere;
			}
			return $filterWhere;
		}
	}

	/**
	 * prepareModels()
	 * 准备模型
	 * ---------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function prepareModels(){
		$models = parent::prepareModels();
		if(is_callable($this->modelsSerializer)){
			$models = call_user_func($this->modelsSerializer, $models);
		}
		return $models;
	}
}