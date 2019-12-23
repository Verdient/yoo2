<?php
namespace yoo\db\mysql;

/**
 * Schema
 * 模式
 * ------
 * @author Verdient。
 */
class Schema extends \yii\db\mysql\Schema
{
	/**
	 * createQueryBuilder()
	 * 创建查询构建器
	 * --------------------
	 * @return QueryBuilder
	 * @author Verdient。
	 */
	public function createQueryBuilder(){
		return new QueryBuilder($this->db);
	}
}