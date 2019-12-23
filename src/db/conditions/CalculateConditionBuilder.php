<?php
namespace yoo\db\conditions;

use yii\db\ExpressionBuilderInterface;
use yii\db\ExpressionBuilderTrait;
use yii\db\ExpressionInterface;
use yii\validators\NumberValidator;

/**
 * CalculateConditionBuilder
 * 运算条件构建器
 * -------------------------
 * @author Verdient。
 */
class CalculateConditionBuilder implements ExpressionBuilderInterface
{
	use ExpressionBuilderTrait;

	/**
	 * build(ExpressionInterface $expression[, Array &$params = []])
	 * 构建
	 * -------------------------------------------------------------
	 * @param ExpressionInterface $expression 要构建的条件
	 * @param Array $params 构建参数
	 * --------------------------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function build(ExpressionInterface $expression, array &$params = []){
		$operator = $expression->getOperator();
		$column = $expression->getColumn();
		$operand = $expression->getOperand();
		if(strpos($column, '(') === false){
			$column = $this->queryBuilder->db->quoteColumnName($column);
		}
		$phName1 = $this->createPlaceholder($expression->getIntervalStart(), $params);
		$phName2 = $this->createPlaceholder($expression->getIntervalEnd(), $params);
		return "$column $operator $phName1 $operand $phName2";
	}

	/**
	 * createPlaceholder(Mixed $value, Array &$params)
	 * 创建占位符
	 * -----------------------------------------------
	 * @param Mixed $value 值
	 * @param Array $params 附带参数
	 * ----------------------------
	 * @return String
	 * @author Verdient。
	 */
	protected function createPlaceholder($value, &$params){
		if($value instanceof ExpressionInterface){
			return $this->queryBuilder->buildExpression($value, $params);
		}
		$validator = new NumberValidator;
		$validator->integerOnly = true;
		if($validator->validate($value)){
			$value = (int) $value;
		}
		return $this->queryBuilder->bindParam($value, $params);
	}
}
