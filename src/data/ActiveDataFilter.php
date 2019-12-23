<?php
namespace yoo\data;

/**
 * ActiveDataFilter
 * 动态数据过滤器
 * ----------------
 * @author Verdient。
 */
class ActiveDataFilter extends \yii\data\ActiveDataFilter
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
		$this->conditionBuilders = array_merge($this->conditionBuilders, [
			'+' => 'buildCalculateCondition',
			'-' => 'buildCalculateCondition',
			'*' => 'buildCalculateCondition',
			'/' => 'buildCalculateCondition',
			'&' => 'buildCalculateCondition',
			'|' => 'buildCalculateCondition',
			'~' => 'buildCalculateCondition',
			'^' => 'buildCalculateCondition',
			'BETWEEN' => 'buildBetweenCondition'
		]);

		$this->filterControls = array_merge($this->filterControls, [
			'+' => '+',
			'-' => '-',
			'*' => '*',
			'/' => '/',
			'&' => '&',
			'|' => '|',
			'~' => '~',
			'^' => '^',
			'between' => 'BETWEEN'
		]);

		$this->conditionValidators = array_merge($this->conditionValidators, [
			'+' => 'validateCalculateCondition',
			'-' => 'validateCalculateCondition',
			'*' => 'validateCalculateCondition',
			'/' => 'validateCalculateCondition',
			'&' => 'validateCalculateCondition',
			'|' => 'validateCalculateCondition',
			'~' => 'validateCalculateCondition',
			'^' => 'validateCalculateCondition',
			'between' => 'validateBetweenCondition'
		]);

		$this->operatorTypes = array_merge($this->operatorTypes, [
			'+' => [self::TYPE_INTEGER, self::TYPE_FLOAT],
			'-' => [self::TYPE_INTEGER, self::TYPE_FLOAT],
			'*' => [self::TYPE_INTEGER, self::TYPE_FLOAT],
			'/' => [self::TYPE_INTEGER, self::TYPE_FLOAT],
			'&' => '*',
			'|' => '*',
			'~' => '*',
			'^' => '*',
			'BETWEEN' => [self::TYPE_INTEGER, self::TYPE_FLOAT, self::TYPE_DATETIME, self::TYPE_DATE, self::TYPE_TIME]
		]);
	}

	/**
	 * validateBetweenCondition(String $operator, Mixed $condition[, String $attribute = null])
	 * 校验范围条件
	 * ----------------------------------------------------------------------------------------
	 * @param String $operator 操作符
	 * @param Array $condition 条件
	 * @param String $attribute 属性
	 * ------------------------------
	 * @author Verdient。
	 */
	public function validateBetweenCondition($operator, $condition, $attribute = null){
		if($attribute === null){
			$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireAttribute', ['operator' => $operator]));
		}else{
			if(is_array($condition)){
				if(count($condition) !== 2){
					$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireMultipleOperands', ['operator' => $operator]));
				}else{
					foreach($condition as $v){
						$this->validateAttributeValue($attribute, $v);
					}
				}
			}else{
				$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireMultipleOperands', ['operator' => $operator]));
			}
		}
	}

	/**
	 * validateCalculateCondition(String $operator, Mixed $condition[, String $attribute = null])
	 * 校验计算条件
	 * ------------------------------------------------------------------------------------------
	 * @param String $operator 操作符
	 * @param Array $condition 条件
	 * @param String $attribute 属性
	 * ------------------------------
	 * @author Verdient。
	 */
	public function validateCalculateCondition($operator, $condition, $attribute = null){
		if($attribute === null){
			$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireAttribute', ['operator' => $operator]));
		}else{
			if(is_array($condition)){
				if(!in_array(count($condition), [1, 2, 3])){
					$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireMultipleOperands', ['operator' => $operator]));
				}else{
					if(isset($condition[2])){
						if(!in_array($condition[2], ['<', '>', '=', '!=', '<>', '>=', '<='])){
							$this->addError($this->filterAttributeName, $this->parseErrorMessage('unsupportedOperatorType', ['attribute' => $attribute, 'operator' => $condition[2]]));
						}
						unset($condition[2]);
					}
					foreach($condition as $v){
						$this->validateAttributeValue($attribute, $v);
					}
				}
			}else{
				$this->addError($this->filterAttributeName, $this->parseErrorMessage('operatorRequireMultipleOperands', ['operator' => $operator]));
			}
		}
	}

	/**
	 * validateOperatorCondition(String $operator, Mixed $condition[, String $attribute = null])
	 * 校验操作条件
	 * -----------------------------------------------------------------------------------------
	 * @param String $operator 操作符
	 * @param Array $condition 条件
	 * @param String $attribute 属性
	 * ------------------------------
	 * @author Verdient。
	 */
	protected function validateOperatorCondition($operator, $condition, $attribute = null){
		if(isset($this->conditionValidators[$operator])){
			$method = $this->conditionValidators[$operator];
			return $this->$method($operator, $condition, $attribute);
		}
		return parent::validateOperatorCondition($operator, $condition, $attribute);
	}

	/**
	 * buildCalculateCondition(String $operator, Mixed $condition)
	 * 构建计算条件
	 * -----------------------------------------------------------
	 * @param String $operator 操作符
	 * @param Array $condition 条件
	 * @param String $attribute 属性
	 * -----------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function buildCalculateCondition($operator, $condition, $attribute){
		if(isset($this->queryOperatorMap[$operator])){
			$operator = $this->queryOperatorMap[$operator];
		}
		$result = [$operator, $attribute];
		foreach($condition as $v){
			$result[] = $this->filterAttributeValue($attribute, $v);
		}
		return $result;
	}

	/**
	 * buildBetweenCondition(String $operator, Mixed $condition)
	 * 构建范围条件
	 * ---------------------------------------------------------
	 * @param String $operator 操作符
	 * @param Array $condition 条件
	 * @param String $attribute 属性
	 * -----------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function buildBetweenCondition($operator, $condition, $attribute){
		if(isset($this->queryOperatorMap[$operator])){
			$operator = $this->queryOperatorMap[$operator];
		}
		return [
			$operator,
			$attribute,
			$this->filterAttributeValue($attribute, $condition[0]),
			$this->filterAttributeValue($attribute, $condition[1])
		];
	}
}