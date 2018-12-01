<?php
        	
namespace app\sms\model;
use app\home\model\Weixin;
        	
/**
 * Sms的微信模型
 */
class WeixinAddon extends Weixin{
	function reply($dataArr, $keywordArr = []) {
		$config = getAddonConfig ( 'Sms' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	