<?php
        	
namespace app\shop\model;
use app\home\model\Weixin;
        	
/**
 * Shop的微信模型
 */
class WeixinAddonModel extends Weixin{
	function reply($dataArr, $keywordArr = []) {
		$config = getAddonConfig ( 'Shop' ); // 获取后台插件的配置参数	
		//dump($config);

	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	