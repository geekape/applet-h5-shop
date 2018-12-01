<?php
        	
namespace app\servicer\model;
use app\home\model\Weixin;
        	
/**
 * Servicer的微信模型
 */
class WeixinAddon extends Weixin{
	function reply($dataArr, $keywordArr = []) {
		$config = getAddonConfig ( 'Servicer' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	