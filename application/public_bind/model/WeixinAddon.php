<?php

namespace app\public_bind\model;

use app\home\model\Weixin;

/**
 * PublicBind的微信模型
 */
class WeixinAddon extends Weixin {
	function reply($dataArr, $keywordArr = []) {
		if ($dataArr ['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
			$this->replyText ( 'TESTCOMPONENT_MSG_TYPE_TEXT_callback' );
		} elseif (strpos ( $dataArr ['Content'], 'QUERY_AUTH_CODE' ) !== false) {
			$query_auth_code = str_replace ( 'QUERY_AUTH_CODE:', '', $dataArr ['Content'] );
			
			$info = D ( 'public_bind/PublicBind' )->getAuthInfo ( $query_auth_code );
			$param ['touser'] = $dataArr ['FromUserName'];
			$param ['msgtype'] = 'text';
			$param ['text'] ['content'] = $query_auth_code . '_from_api';
			$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $info ['authorization_info'] ['authorizer_access_token'];
			$res = post_data ( $url, $param );
		} else {
			$this->replyText ( $dataArr ['Event'] . 'from_callback' );
		}
	}
}
        	