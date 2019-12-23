<?php
namespace yoo\services\tencent\wechat;

/**
 * WeChatRequest
 * 微信请求
 * -------------
 * @author Verdient。
 */
class WeChatRequest extends \yoo\base\RESTRequest
{
	/**
	 * _prepareResponse(Array $response)
	 * 准备响应
	 * ---------------------------------
	 * @param Array $response 响应
	 * ---------------------------
	 * @inheritdoc
	 * -----------
	 * @return RESTRequest
	 * @author Verdient。
	 */
	protected function _prepareResponse($response){
		return new WeChatResponse($response);
	}
}