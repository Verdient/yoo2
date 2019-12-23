<?php
namespace yoo\web;

use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * User
 * 用户
 * ----
 * @author Verdient。
 */
class User extends \yii\web\User
{
	public function loginRequired($checkAjax = true, $checkAcceptHeader = true){
		$request = Yii::$app->getRequest();
		$canRedirect = !$checkAcceptHeader || $this->checkRedirectAcceptable();
		if ($this->enableSession
			&& $request->getIsGet()
			&& (!$checkAjax || !$request->getIsAjax())
			&& $canRedirect
		) {
			$this->setReturnUrl($request->getUrl());
		}
		if ($this->loginUrl !== null && $canRedirect) {
			$loginUrl = (array) $this->loginUrl;
			if ($loginUrl[0] !== Yii::$app->requestedRoute) {
				return Yii::$app->getResponse()->redirect($this->loginUrl);
			}
		}
		throw new UnauthorizedHttpException(Yii::t('yii', 'Login Required'));
	}
}