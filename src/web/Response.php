<?php
namespace yoo\web;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use yii\helpers\VarDumper;
use yii\web\NotAcceptableHttpException;
use yii\web\ResponseFormatterInterface;
use yii\web\UnsupportedMediaTypeHttpException;

/**
 * Response
 * 响应
 * --------
 * @author Verdient。
 */
class Response extends \yii\web\Response
{
	/**
	 * @var Array $_formats
	 * 格式集合
	 * --------------------
	 * @author Verdient。
	 */
	protected $_formats = [];

	/**
	 * @var Array $contentTypes
	 * 消息体类型
	 * ------------------------
	 * @author Verdient。
	 */
	public $contentTypes = [];

	/**
	 * @var Boolean $autoFormat
	 * 是否自动格式化
	 * ------------------------
	 * @author Verdient。
	 */
	public $autoFormat = true;

	/**
	 * @var Boolean $_isDownload
	 * 是否是下载
	 * -------------------------
	 * @author Verdient。
	 */
	protected $_isDownload = false;

	/**
	 * @var Array $_acceptContentTypes
	 * 接受的消息体类型
	 * -------------------------------
	 * @author Verdient。
	 */
	protected $_acceptContentTypes = [];

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
		$this->contentTypes = array_merge($this->defaultContentType(), $this->contentTypes);
	}

	/**
	 * setDownloadHeaders(String $attachmentName, String $mimeType = null, Boolean $inline = false, Integer $contentLength = null)
	 * 设置下载头部
	 * ---------------------------------------------------------------------------------------------------------------------------
	 * @param String $attachmentName 附件名称
	 * @param String $mimeType MIME类型
	 * @param Boolean $inline 是否内联元素
	 * @param Integer $contentLength 内容长度
	 * -------------------------------------
	 * @return Response
	 * @author Verdient。
	 */
	public function setDownloadHeaders($attachmentName, $mimeType = null, $inline = false, $contentLength = null){
		$this->_isDownload = true;
		return parent::setDownloadHeaders($attachmentName, $mimeType, $inline, $contentLength);
	}

	/**
	 * defaultContentType()
	 * 默认消息体类型
	 * --------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function defaultContentType(){
		return [
			'application/json' => static::FORMAT_JSON,
			'application/jsonp' => static::FORMAT_JSONP,
			'application/xml' => static::FORMAT_XML,
			'application/octet-stream' => static::FORMAT_RAW,
			'text/plain' => static::FORMAT_RAW,
			'text/html' => static::FORMAT_HTML
		];
	}

	/**
	 * defaultFormatters()
	 * 默认格式化器
	 * -------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function defaultFormatters(){
		return [
			self::FORMAT_JSON => [
				'class' => 'yoo\web\JsonResponseFormatter',
			],
			self::FORMAT_XML => [
				'class' => 'yoo\web\XmlResponseFormatter',
			],
			self::FORMAT_JSONP => [
				'class' => 'yoo\web\JsonResponseFormatter',
				'useJsonp' => true,
			],
			self::FORMAT_HTML => [
				'class' => 'yoo\web\HtmlResponseFormatter',
			]
		];
	}

	/**
	 * getAcceptContentTypes()
	 * 获取接受的消息体类型
	 * -----------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function getAcceptContentTypes(){
		if(empty($this->_acceptContentTypes)){
			$request = Yii::$app->getRequest();
			$this->_acceptContentTypes = $request->parseAcceptHeader($request->headers->get('Accept'));
			if(empty($this->_acceptContentTypes)){
				$this->_acceptContentTypes = ['*/*' => ['q' => 1]];
			}
		}
		return $this->_acceptContentTypes;
	}

	/**
	 * prepareFormat()
	 * 准备格式
	 * ---------------
	 * @author Verdient。
	 */
	protected function prepareFormat(){
		if($this->autoFormat === true && $this->_isDownload === false){
			if(empty($this->_formats)){
				foreach($this->getAcceptContentTypes() as $contentType => $params){
					if($contentType === '*/*'){
						foreach($this->defaultFormatters() as $fotmat => $formater){
							$this->_formats[$fotmat] = $params;
						}
						$this->_formats[static::FORMAT_RAW] = $params;
					}
					if(isset($this->contentTypes[$contentType])){
						$this->_formats[$this->contentTypes[$contentType]] = $params;
					}
				}
				if(empty($this->_formats)){
					throw new UnsupportedMediaTypeHttpException('Unsupported Media Type: ' . implode(', ', array_keys($this->getAcceptContentTypes())));
				}
			}
		}else{
			$this->_formats = [static::FORMAT_RAW => ['q' => 1]];
		}
	}

	/**
	 * prepare()
	 * 准备
	 * ---------
	 * @inheritdoc
	 * -----------
	 * @author Verdient。
	 */
	protected function prepare(){
		if($this->stream !== null){
			return;
		}
		$this->prepareFormat();
		$acceptable = false;
		foreach($this->_formats as $format => $params){
			if($format !== static::FORMAT_RAW){
				if($this->tryFormat($format, $params) !== false){
					$this->format = $format;
					$acceptable = true;
					break;
				}
			}else{
				$this->format = static::FORMAT_RAW;
				if(is_array($this->data)){
					$this->data = VarDumper::dumpAsString($this->data);
				}
				if(!empty($data)){
					$this->content = $this->data;
				}
				$acceptable = true;
				break;
			}
		}
		if($acceptable === false){
			throw new NotAcceptableHttpException('Not Acceptable');
		}
		if(is_array($this->content)){
			throw new InvalidArgumentException('Response content must not be an array.');
		}else if(is_object($this->content)){
			if(method_exists($this->content, '__toString')){
				$this->content = $this->content->__toString();
			}else{
				throw new InvalidArgumentException('Response content must be a string or an object implementing __toString().');
			}
		}
	}

	/**
	 * tryFormat()
	 * 尝试格式化
	 * -----------
	 * @return Boolean
	 * @author Verdient。
	 */
	protected function tryFormat($format, $params){
		if(isset($this->formatters[$format])){
			$formatter = $this->formatters[$format];
			if(!is_object($formatter)){
				$this->formatters[$format] = $formatter = Yii::createObject($formatter);
			}
			if($formatter instanceof ResponseFormatterInterface){
				return $formatter->format($this);
			}else{
				throw new InvalidConfigException("The '{$format}' response formatter is invalid. It must implement the ResponseFormatterInterface.");
			}
		}
		return false;
	}
}