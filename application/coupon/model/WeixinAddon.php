<?php

namespace app\coupon\model;

use app\home\model\Weixin;

/**
 * Coupon的微信模型
 */
class WeixinAddon extends Weixin {
	function reply($dataArr, $keywordArr = []) {
		$map ['wpid'] = get_wpid ();
		$keywordArr ['aim_id'] && $map ['id'] = $keywordArr ['aim_id'];
		$data = M( 'coupon' )->where ( wp_where( $map ) )->find ();
		
		// 其中wpid和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
		$param ['wpid'] = get_wpid ();
		$param ['openid'] = get_openid ();
		$param ['id'] = $data ['id'];
		$url = U ( 'Coupon/Wap/prev', $param );
		
		$articles [0] = array (
				'Title' => $data ['title'],
				'Url' => $url 
		);
		
		$now = time ();
		if (empty ( $data ['end_time'] ) || $data ['end_time'] > $now) {
			$articles [0] ['Description'] = $data ['intro'];
			$articles [0] ['PicUrl'] = ! empty ( $data ['cover'] ) ? get_cover_url ( $data ['cover'] ) : SITE_URL . '/coupon/cover_pic.jpg';
		} else {
			$articles [0] ['Description'] = $data ['end_tips'];
			$articles [0] ['PicUrl'] = ! empty ( $data ['end_cover'] ) ? get_cover_url ( $data ['end_cover'] ) : SITE_URL . '/coupon/cover_pic_over.png';
		}
		
		$this->replyNews ( $articles );
	}
	/*
	 * 个人中心里的链接配置参数
	 * 只配置一个链接时 personal是一维数组 如 array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0);
	 * 如果要配置多个链接是personal是二维数组 如
	 * array(
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0),
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0),
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0)
	 * );
	 */
	function personal() {
		$links = array (
				'url' => U ( 'Coupon/Wap/personal' ), // 链接地址
				'title' => '我的优惠券', // 链接名称
				'icon' => '', // 图标，选填
				'group' => '我的互动', // 在个人中心里的分组名，选填
				'new_count' => 0 
		);
		
		// new_count 为新消息的数目，如果大于0，会在个人空间里的链接旁边显示新消息数目
		// 下面实现获取new_count的功能
		
		return $links;
	}
}
        	