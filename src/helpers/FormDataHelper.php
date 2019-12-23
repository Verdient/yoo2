<?php
namespace yoo\helpers;

use yii\base\InvalidParamException;
use yii\helpers\FileHelper;

/**
 * FormDataHelper
 * FormData 助手
 * --------------
 * @author Verdient。
 */
class FormDataHelper
{
	/**
	 * build(String $boundary, Array $data[, Array $files = []])
	 * 构建formData
	 * ---------------------------------------------------------
	 * @param String $boundary 分隔符
	 * @param Array $data 要发送的数据
	 * @param Array $files 要发送的文件
	 * ------------------------------
	 * @return String
	 */
	public static function build($boundary, $data, $files) {
		function convert_array_key(&$node, $prefix, &$result) {
			if(!is_array($node)){
				$result[$prefix] = $node;
			}else{
				foreach($node as $key => $value){
					convert_array_key($value, "{$prefix}[{$key}]", $result);
				}
			}
		}

		function query_multidimensional_array(&$array, $query) {
			$query = explode('][', substr($query, 1, -1));
			$temp = $array;
			foreach ($query as $key) {
				$temp = $temp[$key];
			}
			return $temp;
		}

		$body = [];

		foreach($data as $key => $value){
			if(!is_array($value)){
				$body_part = "Content-Disposition: form-data; name=\"$key\"\r\n";
				$body_part .= "\r\n$value";
				$body[] = $body_part;
			}else{
				$result = [];
				convert_array_key($value, $key, $result);
				foreach($result as $k => $v){
					$body_part = "Content-Disposition: form-data; name=\"$k\"\r\n";
					$body_part .= "\r\n$v";
					$body[] = $body_part;
				}
			}
		}

		foreach($files as $key => $value){
			if(!file_exists($value)){
				throw new InvalidParamException('file ' . $value . ' does not exist');
			}
			$type = FileHelper::getMimeType($value);
			$body_part = "Content-Disposition: form-data; name=\"$key\"; filename=\"{$value}\"\r\n";
			$body_part .= "Content-type: {$type}\r\n";
			$body_part .= "\r\n" . file_get_contents($value);
			$body[] = $body_part;
		}
		$multipart_body = "--$boundary\r\n";
		$multipart_body .= implode("\r\n--$boundary\r\n", $body);
		$multipart_body .= "\r\n--$boundary--";
		return $multipart_body;
	}
}
