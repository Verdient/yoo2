<?php
namespace yoo\components\cUrl\builder;

/**
 * Builder
 * 构建器
 * -------
 * @author Verdient。
 */
abstract class Builder extends \yii\base\BaseObject
{
	/**
	 * @var $_elements
	 * 元素
	 * ---------------
	 * @author Verdient。
	 */
	protected $_elements = [];

	/**
	 * getElements()
	 * 获取元素
	 * -------------
	 * @return Array
	 * @author Verdient。
	 */
	public function getElements(){
		return $this->_elements;
	}

	/**
	 * setElements(Array $elements)
	 * 设置元素
	 * ----------------------------
	 * @param Array $elements 元素
	 * --------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function setElements($elements){
		$this->_elements = $elements;
		return $this;
	}

	/**
	 * addElement(String $name, String $value)
	 * 添加元素
	 * ---------------------------------------
	 * @param String $name 名称
	 * @param String $value 内容
	 * ------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addElement($name, $value){
		$this->_elements[$name] = $value;
		return $this;
	}

	/**
	 * removeElement(String $name)
	 * 移除元素
	 * ---------------------------
	 * @param String $name 名称
	 * ------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function removeElement($name){
		unset($this->_elements[$name]);
		return $this;
	}

	/**
	 * toString()
	 * 转为字符串
	 * ----------
	 * @author Verdient。
	 */
	abstract public function toString();

	/**
	 * headers()
	 * 附加的头部
	 * ---------
	 * @author Verdient。
	 */
	abstract public function headers();
}