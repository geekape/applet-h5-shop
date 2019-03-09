<?php
        	
namespace app\real_prize\model;
use app\home\model\Weixin;
        	
/**
 * RealPrize的微信模型
 */
class WeixinAddon extends Weixin{
	function reply($dataArr, $keywordArr = []) {
		$config = getAddonConfig ( 'RealPrize' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	