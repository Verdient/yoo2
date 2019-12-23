<?php
namespace yoo\db\conditions;

use yii\base\InvalidArgumentException;
use yii\db\conditions\ConditionInterface;
use yii\db\Expression;

/**
 * CalculateCondition
 * 运算条件
 * ------------------
 * @author Verdient。
 */
class CalculateCondition implements ConditionInterface
{
	/**
	 * @var protected $_operator
	 * 操作符
	 * -------------------------
	 * @author Verdient。
	 */
	protected $_operator;

	/**
	 * @var protected $_column
	 * 字段
	 * -----------------------
	 * @author Verdient。
	 */
	protected $_column;

	/**
	 * @var protected $_intervalStart
	 * 内部起始字段
	 * ------------------------------
	 * @author Verdient。
	 */
	protected $_intervalStart;

	/**
	 * @var protected $_intervalEnd
	 * 内部结束字段
	 * ----------------------------
	 * @author Verdient。
	 */
	protected $_intervalEnd;

	/**
	 * @var protected $_operand
	 * 操作符
	 * ------------------------
	 * @author Verdient。
	 */
	protected $_operand;

	/**
	 * __construct(Mixed $column, String $operator, mixed $intervalStart, mixed $intervalEnd[, String $operand = ''])
	 * 构造函数
	 * --------------------------------------------------------------------------------------------------------------
	 * @param Mixed $column 字段
	 * @param String $operator 计算符
	 * @param Mixed $intervalStart 间隔起始
	 * @param Mixed $intervalEnd 间隔结束
	 * -----------------------------------
	 * @author Verdient。
	 */
	public function __construct($column, $operator, $intervalStart, $intervalEnd, $operand = ''){
		$this->_column = $column;
		$this->_operator = $operator;
		$this->_intervalStart = $intervalStart;
		$this->_intervalEnd = $intervalEnd;
		$this->_operand = $operand;
	}

	/**
	 * getOperator()
	 * 获取计算符
	 * -------------
	 * @return String
	 * @author Verdient。
	 */
	public function getOperator(){
		return $this->_operator;
	}

	/**
	 * getColumn()
	 * 获取字段
	 * -----------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getColumn(){
		return $this->_column;
	}

	/**
	 * getIntervalStart()
	 * 获取间隔起始
	 * ------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getIntervalStart(){
		return $this->_intervalStart;
	}

	/**
	 * getIntervalEnd()
	 * 获取间隔结束
	 * ----------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getIntervalEnd(){
		return $this->_intervalEnd;
	}

	/**
	 * getOperand()
	 * 获取操作符
	 * ------------
	 * @return Mixed
	 * @author Verdient。
	 */
	public function getOperand(){
		return $this->_operand;
	}

	/**
	 * fromArrayDefinition()
	 * 转换数组定义
	 * ---------------------
	 * @inheritdoc
	 * -----------
	 * @throws InvalidArgumentException
	 * @return AndColumnsCondition
	 * @author Verdient。
	 */
	public static function fromArrayDefinition($operator, $operands){
		if(!isset($operands[0], $operands[1])){
			throw new InvalidArgumentException("Operator '$operator' requires at least two operands.");
		}
		$column = $operands[0];
		$intervalStar = $operands[1];
		$intervalEnd = isset($operands[2]) ? $operands[2] : new Expression("`$column`");
		$operand = isset($operands[3]) ? $operands[3] : '=';
		return new static($column, $operator, $intervalStar, $intervalEnd, $operand);
	}
}
