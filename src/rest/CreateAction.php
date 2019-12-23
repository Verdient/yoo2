<?php
namespace yoo\rest;

use Yii;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction
 * 创建动作
 * ------------
 * @author Verdient。
 */
class CreateAction extends \yii\rest\CreateAction
{
	/**
	 * run()
	 * 执行
	 * -----
	 * @return Object
	 * @author Verdient。
	 */
	public function run(){
		if($this->checkAccess){
			call_user_func($this->checkAccess, $this->id);
		}
		$model = new $this->modelClass([
			'scenario' => $this->scenario,
		]);
		$model->load(Yii::$app->getRequest()->getBodyParams(), '');
		if($model->validate()){
			if($model->getIsNewRecord()){
				$model->insert(false);
			}
			$response = Yii::$app->getResponse();
			$response->setStatusCode(201);
			$id = implode(',', array_values($model->getPrimaryKey(true)));
			$response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
		}elseif(!$model->hasErrors()){
			throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
		}
		return $model;
	}
}