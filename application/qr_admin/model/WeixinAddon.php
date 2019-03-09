<?php
        	
namespace app\qr_admin\model;
use app\home\model\Weixin;
        	
/**
 * QrAdmin的微信模型
 */
class WeixinAddon extends Weixin{
	function reply($dataArr, $keywordArr = []) {
		$config = getAddonConfig ( 'QrAdmin' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	