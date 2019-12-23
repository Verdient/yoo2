<?php
namespace yoo\rest;

use Yii;
use yii\base\Arrayable;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Link;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\UnsupportedMediaTypeHttpException;
use yoo\helpers\ExceptionHelper;

/**
 * Serializer
 * 序列化器
 * ----------
 * @author Verdient。
 */
class Serializer extends \yii\rest\Serializer
{
	/**
	 * @var Boolean $allowUserAssign
	 * 允许用户指定处理方式
	 * -----------------------------
	 * @author Verdient。
	 */
	public $allowUserAssign = true;

	/**
	 * @var String $resultEnvelope
	 * 结果标签
	 * ---------------------------
	 * 仅在responseFormat为code时有效
	 * ----------------------------
	 * @author Verdient。
	 */
	public $resultEnvelope = 'result';

	/**
	 * @var String $errorEnvelope
	 * 错误标签
	 * ---------------------------
	 * 仅在errorFormat为single时有效
	 * ---------------------------
	 * @author Verdient。
	 */
	public $errorEnvelope = null;

	/**
	 * @var String $collectionEnvelope
	 * 数据集标签
	 * --------------------------------
	 * @author Verdient。
	 */
	public $collectionEnvelope = null;

	/**
	 * @var String $metaEnvelope
	 * 元数据标签
	 * -------------------------
	 * 仅在collectionEnvelope不为空时生效
	 * -------------------------------
	 * @author Verdient。
	 */
	public $metaEnvelope = null;

	/**
	 * @var String $linksEnvelope
	 * 链接标签
	 * ---------------------------
	 * 仅在collectionEnvelope不为空时生效
	 * --------------------------------
	 * @author Verdient。
	 */
	public $linksEnvelope = null;

	/**
	 * @var String $responseFormat
	 * 响应格式
	 * ---------------------------
	 * @param String RESTful RESTful形式返回
	 * @param String code 消息体code返回
	 * ------------------------------------
	 * @author Verdient。
	 */
	public $responseFormat = 'RESTful';

	/**
	 * @var String $errorFormat
	 * 错误格式
	 * ------------------------
	 * @param String single 只返回单个错误
	 * @param String keyValue 键值对形式
	 * @param String paralleling 并列
	 * ---------------------------------
	 * @author Verdient。
	 */
	public $errorFormat = 'paralleling';

	/**
	 * @var Integer $errorStatusCode
	 * 错误状态码
	 * -----------------------------
	 * @author Verdient。
	 */
	public $errorStatusCode = 422;

	/**
	 * @var Boolean $appendMessage
	 * 是否附加消息
	 * ---------------------------
	 * 仅在responseFormat为code时有效
	 * ----------------------------
	 * @author Verdient。
	 */
	public $appendMessage = false;

	/**
	 * @var Boolean $withDebug
	 * 是否返回DEBUG信息
	 * -----------------------
	 * @author Verdient。
	 */
	public $withDebug = true;

	/**
	 * @var String $responseFormatHeader
	 * 响应格式头名称
	 * ----------------------------------
	 * @author Verdient。
	 */
	public $responseFormatHeader = 'Serializer-Response-Format';

	/**
	 * @var String $errorFormatHeader
	 * 错误格式头名称
	 * ------------------------------
	 * @author Verdient。
	 */
	public $errorFormatHeader = 'Serializer-Error-Format';

	/**
	 * @var String $resultEnvelopeHeader
	 * 结果标签头名称
	 * ---------------------------------
	 * @author Verdient。
	 */
	public $resultEnvelopeHeader = 'Serializer-Result-Envelope';

	/**
	 * @var String $errorEnvelopeHeader
	 * 错误标签头名称
	 * --------------------------------
	 * @author Verdient。
	 */
	public $errorEnvelopeHeader = 'Serializer-Error-Envelope';

	/**
	 * @var String $collectionEnvelopeHeader
	 * 资源集标签头名称
	 * -------------------------------------
	 * @author Verdient。
	 */
	public $collectionEnvelopeHeader = 'Serializer-Collection-Envelope';

	/**
	 * @var String $metaEnvelopeHeader
	 * 元数据集标签头名称
	 * -------------------------------
	 * @author Verdient。
	 */
	public $metaEnvelopeHeader = 'Serializer-Meta-Envelope';

	/**
	 * @var String $linksEnvelopeHeader
	 * 链接集标签头名称
	 * --------------------------------
	 * @author Verdient。
	 */
	public $linksEnvelopeHeader = 'Serializer-Links-Envelope';

	/**
	 * @var String $appendMessageHeader
	 * 是否附加消息头部名称
	 * --------------------------------
	 * @author Verdient。
	 */
	public $appendMessageHeader = 'Serializer-Append-Message';

	/**
	 * @var String $withDebugHeader
	 * 是否返回DEBUG信息头部名称
	 * ----------------------------
	 * @author Verdient。
	 */
	public $withDebugHeader = 'Serializer-With-Debug';

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
		if(!in_array($this->responseFormat, ['RESTful', 'code'])){
			throw new InvalidConfigException('responseFormat is unsupported: ' . $this->responseFormat);
		}
		if(!in_array($this->errorFormat, ['single', 'keyValue', 'paralleling'])){
			throw new InvalidConfigException('errorFormat is unsupported: ' . $this->errorFormat);
		}
		if($this->allowUserAssign){
			Yii::$app->getResponse()->getHeaders()->add('Access-Control-Request-Headers', implode(', ', [
				$this->responseFormatHeader,
				$this->errorFormatHeader,
				$this->resultEnvelopeHeader,
				$this->errorEnvelopeHeader,
				$this->collectionEnvelopeHeader,
				$this->metaEnvelopeHeader,
				$this->linksEnvelopeHeader,
				$this->appendMessageHeader,
				$this->withDebugHeader
			]));
			$headers = Yii::$app->getRequest()->getHeaders();
			if($headers->get($this->responseFormatHeader)){
				$this->responseFormat = $headers->get($this->responseFormatHeader);
			}
			if($headers->get($this->errorFormatHeader)){
				$this->errorFormat = $headers->get($this->errorFormatHeader);
			}
			if($headers->get($this->resultEnvelopeHeader)){
				$this->resultEnvelope = $headers->get($this->resultEnvelopeHeader);
			}
			if($headers->get($this->errorEnvelopeHeader)){
				$this->errorEnvelope = $headers->get($this->errorEnvelopeHeader);
			}
			if($headers->get($this->collectionEnvelopeHeader)){
				$this->collectionEnvelope = $headers->get($this->collectionEnvelopeHeader);
			}
			if($headers->get($this->metaEnvelopeHeader)){
				$this->metaEnvelope = $headers->get($this->metaEnvelopeHeader);
			}
			if($headers->get($this->linksEnvelopeHeader)){
				$this->linksEnvelope = $headers->get($this->linksEnvelopeHeader);
			}
			if($header = $headers->get($this->appendMessageHeader)){
				$this->appendMessage = $header === 'true' ? true : false;
			}
			if($header = $headers->get($this->withDebugHeader)){
				$this->withDebug = $header === 'false' ? false : true;
			}
		}
	}

	/**
	 * serialize(Mixed $data)
	 * 序列化数据
	 * ----------------------
	 * @param Mixed $data 要序列化的数据
	 * -------------------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	public function serialize($data){
		if(is_bool($data)){
			$data = ['message' => $data ? Yii::t('message', 'Success') : Yii::t('message', 'Error')];
		}
		if(is_string($data) || is_numeric($data)){
			$data = ['message' => Yii::t('message', $data)];
		}
		if($data instanceof Response){
			$data = $data->stream ?: ($data->data ?: $data->content);
		}
		$isException = ExceptionHelper::isException($data);
		$result = $isException ? $this->serializeException($data) : parent::serialize($data);
		return $this->format($result, $isException);
	}

	/**
	 * serializeDataProvider(DataProvider $data)
	 * 序列化数据提供器
	 * -----------------------------------------
	 * @param DataProvider $data 数据提供器
	 * -----------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function serializeDataProvider($data){
		if($data->hasErrors()){
			return static::serializeModelErrors($data);
		}
		$this->_addExposeHeaders();
		return parent::serializeDataProvider($data);
	}

	/**
	 * serializeModelErrors(Object $model)
	 * 序列化模型错误
	 * -----------------------------------
	 * @param Object $model 模型
	 * ------------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function serializeModelErrors($model){
		$this->response->setStatusCode($this->errorStatusCode, 'Data Validation Failed.');
		$result = [];
		switch($this->errorFormat){
			case 'single':
				$error = $model->getFirstErrors();
				if($this->errorEnvelope){
					$result = [$this->errorEnvelope => reset($error) ?: 'Unknown Error'];
				}else{
					$result = array_slice($error, 0, 1);
				}
				break;
			case 'keyValue':
				$result = $model->getFirstErrors();
				break;
			case 'paralleling':
				foreach($model->getFirstErrors() as $name => $message){
					$result[] = [
						'field' => $name,
						'message' => $message ?: 'Unknown Error',
					];
				}
				break;
			default:
				throw new BadRequestHttpException('errorFormat is unsupported: ' . $this->errorFormat);
				break;
		}
		return $result;
	}

	/**
	 * serializePagination(Pagination $pagination)
	 * 序列化分页
	 * -------------------------------------------
	 * @param Pagination $pagination 分页对象
	 * -------------------------------------
	 * @inheritdoc
	 * -----------
	 * @return Array
	 * @author Verdient。
	 */
	protected function serializePagination($pagination){
		$result = [];
		if($this->linksEnvelope){
			$result[$this->linksEnvelope] = Link::serialize($pagination->getLinks(true));
		}
		if($this->metaEnvelope){
			$result[$this->metaEnvelope] = [
				'totalCount' => $pagination->totalCount,
				'pageCount' => $pagination->getPageCount(),
				'currentPage' => $pagination->getPage() + 1,
				'perPage' => $pagination->getPageSize(),
			];
		}
		return $result;
	}

	/**
	 * serializeModels(Array $models)
	 * 序列化模型集合
	 * -----------------------------
	 * @param Array $models 模型集合
	 * ----------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function serializeModels(array $models){
		list($fields, $expand) = $this->getRequestedFields();
		foreach($models as $i => $model){
			if($model->hasErrors()){
				return $this->serializeModelErrors($model);
			}
			if($model instanceof Arrayable){
				$models[$i] = $model->toArray($fields, $expand);
			}elseif(is_array($model)){
				$models[$i] = ArrayHelper::toArray($model);
			}
		}
		return $models;
	}

	/**
	 * serializeException(Exception $exception)
	 * 序列化异常
	 * ----------------------------------------
	 * @param Exception $exception 异常对象
	 * ----------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function serializeException($exception){
		$result = [];
		if(YII_DEBUG && $this->withDebug === true){
			$previous = $exception->getPrevious();
			$result = [
				'name' => method_exists($exception, 'getName') ? $exception->getName() : 'Exception',
				'message' => mb_convert_encoding($exception->getMessage(), 'UTF-8'),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'type' => get_class($exception),
				'trace' => explode("\n", $exception->getTraceAsString()),
			];
			if($previous){
				$result['previous'] = $previous;
			}
		}else if($exception instanceof UserException){
			if($this->errorEnvelope){
				$attribute = $this->errorEnvelope;
			}else{
				$attribute = explode('\\', get_class($exception));
				$attribute = end($attribute);
			}
			$result = [$attribute => $exception->getMessage()];
		}else{
			if($this->errorEnvelope){
				$attribute = $this->errorEnvelope;
			}else{
				$attribute = explode('\\', get_class($exception));
				$attribute = end($attribute);
			}
			$result = [$attribute => method_exists($exception, 'getName') ? $exception->getName() : 'Exception'];
		}
		if($exception instanceof HttpException){
			$this->response->setStatusCode($exception->statusCode);
			if($exception instanceof UnsupportedMediaTypeHttpException || $exception instanceof NotAcceptableHttpException){
				$this->response->autoFormat = false;
			}
		}else{
			$this->response->setStatusCode(500);
		}
		return $result;
	}

	/**
	 * _addExposeHeaders()
	 * 添加头部扩展
	 * -------------------
	 * @author Verdient。
	 */
	protected function _addExposeHeaders(){
		$responseHeaders = Yii::$app->getResponse()->getHeaders();
		$exposeHeaders = trim($responseHeaders->get('Access-Control-Expose-Headers'));
		if($exposeHeaders){
			$exposeHeaders = explode(',', $exposeHeaders);
		}else{
			$exposeHeaders = [];
		}
		$responseHeaders->set('Access-Control-Expose-Headers', implode(', ', array_merge($exposeHeaders, [
			$this->totalCountHeader,
			$this->pageCountHeader,
			$this->currentPageHeader,
			$this->perPageHeader
		])));
	}

	/**
	 * normalize(Array $data)
	 * 格式化数据
	 * ----------------------
	 * @return Mixed
	 * @author Verdient。
	 */
	protected function normalize($data){
		if(is_array($data)){
			foreach($data as $key => $value){
				if(is_array($value)){
					$data[$key] = $this->normalize($value);
				}else{
					if(is_numeric($value)){
						$value = (string) $value;
						$value = explode('.', $value);
						if(count($value) === 2){
							$length = mb_strlen($value[1]);
							$str = $value[1];
							if($length > 1){
								for($i = $length; $i > 1; $i--){
									if(mb_substr($str, $i - 1, 1) === '0'){
										$str = mb_substr($str, 0, $i - 1);
									}else{
										break;
									}
								}
							}
							$data[$key] = $value[0] . '.' . $str;
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * format(Array $data, Boolean $isException)
	 * 格式化
	 * -----------------------------------------
	 * @param Array $data 待格式化的数据
	 * @param Boolean $isException 是否是异常
	 * ------------------------------------
	 * @return Array|String
	 * @author Verdient。
	 */
	protected function format($data, $isException){
		$data = $this->normalize($data);
		switch($this->responseFormat){
			case 'RESTful':
				break;
			case 'code':
				if(is_array($data) || $data instanceof Arrayable || $isException){
					if($this->appendMessage === true){
						$statusCode = $this->response->getStatusCode();
						if($statusCode < 200 || $statusCode > 299){
							if($this->errorFormat === 'single' && count($data) === 1){
								$str = reset($data);
								if(is_string($str)){
									$message = $str;
								}else{
									$message = Yii::t('message', 'Failed');
								}
							}else{
								if(isset($data['message'])){
									$message = $data['message'];
								}else{
									$message = Yii::t('message', 'Failed');
								}
							}
						}else{
							if(isset($data['message'])){
								$message = $data['message'];
							}else{
								$message = Yii::t('message', 'Success');
							}
						}
						$data = array_merge(['code' => $this->response->getStatusCode(), 'message' => $message], [$this->resultEnvelope => $data]);
					}else{
						$data = array_merge(['code' => $this->response->getStatusCode()], [$this->resultEnvelope => $data]);
					}
				}
				$this->response->setStatusCode(200);
				break;
			default:
				if($isException){
					$data = $this->serializeException(new BadRequestHttpException('responseFormat is unsupported: ' . $this->responseFormat));
				}else{
					throw new BadRequestHttpException('responseFormat is unsupported: ' . $this->responseFormat);
				}
				break;
		}
		return $data;
	}
}