<?php
namespace yoo\db\mysql;

/**
 * QueryBuilder()
 * 查询构建器
 * --------------
 * @author Verdient。
 */
class QueryBuilder extends \yii\db\mysql\QueryBuilder
{
	/**
	 * defaultConditionClasses()
	 * 默认条件类
	 * -------------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function defaultConditionClasses(){
		return array_merge(parent::defaultConditionClasses(), [
			'+' => 'yoo\db\conditions\CalculateCondition',
			'-' => 'yoo\db\conditions\CalculateCondition',
			'*' => 'yoo\db\conditions\CalculateCondition',
			'/' => 'yoo\db\conditions\CalculateCondition',
			'&' => 'yoo\db\conditions\CalculateCondition',
			'|' => 'yoo\db\conditions\CalculateCondition',
			'~' => 'yoo\db\conditions\CalculateCondition',
			'^' => 'yoo\db\conditions\CalculateCondition'
		]);
	}

	/**
	 * defaultExpressionBuilders()
	 * 默认表达式构建器
	 * ---------------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function defaultExpressionBuilders(){
		return array_merge(parent::defaultExpressionBuilders(), [
			'yoo\db\conditions\CalculateCondition' => 'yoo\db\conditions\CalculateConditionBuilder'
		]);
	}
}