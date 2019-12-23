<?php
namespace yoo\components\excel;

use Yii;
use yii\base\InvalidParamException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

/**
 * Export
 * 导出Excel
 * --------
 * @author Verdient。
 */
class Export extends \yoo\base\Component
{
	/**
	 * @var $path
	 * 文件保存的路径
	 * --------------
	 * @author Verdient。
	 */
	public $path = '@export';

	/**
	 * getWriter(String $format, Spreadsheet $spreadsheet)
	 * 获取写入套件
	 * ---------------------------------------------------
	 * @param String $format 格式
	 * @param Spreadsheet Spreadsheet
	 * ------------------------------
	 * @throws InvalidParamException
	 * @return Writer
	 * @author Verdient。
	 */
	public function getWriter($format, $spreadsheet){
		switch(strtolower($format)){
			case 'xlsx':
				return new Xlsx($spreadsheet);
			case 'xls':
				return new Xls($spreadsheet);
			case 'csv':
				return new Csv($spreadsheet);
			default:
				throw new InvalidParamException('Unknown format: ' . $format);
		}
	}

	/**
	 * save(String $format, Array $content, String $name)
	 * 保存
	 * --------------------------------------------------
	 * @param String $format 格式
	 * @param Array $content 内容
	 * @param String $name 文件名称
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function save($format, $content, $name){
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet()->fromArray($content);
		$path = Yii::getAlias($this->path) . DIRECTORY_SEPARATOR . $name . '.' . $format;
		$writer = $this->getWriter($format, $spreadsheet);
		$writer->save($path);
		return $path;
	}

	/**
	 * contents(String $format, Array $content)
	 * 获取文件内容
	 * ----------------------------------------
	 * @param String $format 格式
	 * @param Array $content 内容
	 * @param String $name 文件名称
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function contents($format, $content){
		ob_start();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet()->fromArray($content);
		$writer = $this->getWriter($format, $spreadsheet);
		$writer->save('php://output');
		return ob_get_clean();
	}

	/**
	 * xls(Array $content, String $name)
	 * XLS
	 * ---------------------------------
	 * @param Array $content 内容
	 * @param String $name 文件名称
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function xls($content, $name){
		return $this->contents('xls', $content, $name);
	}

	/**
	 * xlsx(Array $content, String $name)
	 * XLSX
	 * ----------------------------------
	 * @param Array $content 内容
	 * @param String $name 文件名称
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function xlsx($content, $name){
		return $this->save('xlsx', $content, $name);
	}

	/**
	 * csv(Array $content, String $name)
	 * CSV
	 * ----------------------------------
	 * @param Array $content 内容
	 * @param String $name 文件名称
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public function csv($content, $name){
		return $this->save('csv', $content, $name);
	}
}