<?php
namespace yoo\base;

use yoo\helpers\FormDataHelper;
use yoo\helpers\UUIDHelper;

/**
 * FormData
 * 表单数据
 * --------
 * @author Verdient。
 */
class FormData extends \yoo\base\Component
{
	/**
	 * @var const TEXT
	 * 文本类型
	 * ---------------
	 * @author Verdient。
	 */
	const TEXT = 1;

	/**
	 * @var const FILE
	 * 文件类型
	 * ---------------
	 * @author Verdient。
	 */
	const FILE = 2;

	/**
	 * @var $_elements
	 * 元素
	 * ---------------
	 * @author Verdient。
	 */
	protected $_elements = [];

	/**
	 * @var $_boundary
	 * 分隔符
	 * ---------------
	 * @author Verdient。
	 */
	protected $_boundary;

	/**
	 * getBoundary()
	 * 获取分隔符
	 * -------------
	 * @return String
	 * @author Verdient。
	 */
	public function getBoundary(){
		if(!$this->_boundary){
			$this->_boundary = '----------' . str_replace('-', '', UUIDHelper::uuid1());
		}
		return $this->_boundary;
	}

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
	 * addTexts(Array $data)
	 * 批量添加文本
	 * ---------------------
	 * @param Array $data 待添加的数据
	 * -----------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addTexts(Array $data){
		foreach($data as $name => $value){
			$this->addText($name, $value);
		}
		return $this;
	}

	/**
	 * addFiles(Array $data)
	 * 批量添加文件
	 * ---------------------
	 * @param Array $data 待添加的数据
	 * -----------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addFiles(Array $data){
		foreach($data as $name => $path){
			$this->addFile($name, $path);
		}
		return $this;
	}

	/**
	 * addText(String $name, String $value)
	 * 添加文本
	 * ------------------------------------
	 * @param String $name 名称
	 * @param String $value 内容
	 * ------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addText($name, $value){
		return $this->addElement($name, $value, static::TEXT);
	}

	/**
	 * addFile(String $name, String $path)
	 * 添加文件
	 * -----------------------------------
	 * @param String $name 名称
	 * @param String $value 内容
	 * ------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addFile($name, $path){
		return $this->addElement($name, $path, static::FILE);
	}

	/**
	 * addElement(String $name, String $value, Integer $type)
	 * 添加元素
	 * ------------------------------------------------------
	 * @param String $name 名称
	 * @param String $value 内容
	 * @param Integer $type 类型
	 * ------------------------
	 * @return FormData
	 * @author Verdient。
	 */
	public function addElement($name, $value, $type){
		$this->_elements[$name] = [$type, $value];
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
	 * __toString()
	 * 转换为Stirng
	 * ------------
	 * @return String
	 * @author Verdient。
	 */
	public function __toString(){
		$texts = [];
		$files = [];
		foreach($this->getElements() as $name => $value){
			if($value[0] === static::TEXT){
				$texts[$name] = $value[1];
			}else if($value[0] === static::FILE){
				$files[$name] = $value[1];
			}
		}
		return FormDataHelper::build($this->getBoundary(), $texts, $files);
	}

	/**
	 * __toString()
	 * 转换为Stirng
	 * ------------
	 * @return String
	 * @author Verdient。
	 */
	public function toString(){
		return $this->__toString();
	}
}