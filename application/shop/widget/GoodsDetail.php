<?php

namespace app\shop\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class GoodsDetail extends base {
	public $info = array (
			'name' => 'GoodsDetail',
			'title' => '商品详情展示',
			'description' => '包括产品参数，商品详情，评价三大部分',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 0,
			'type' => 0 
	);
	public function install() {
		return true;
	}
	public function uninstall() {
		return true;
	}
	
	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 *
	 * shop_goods_id=商城shop_goods表的商品ID&param_show=产品参数是否显示（0不显示，1显示，默认1）
	 */
	public function html($goods = []) {
		$this->assign ( $goods );
		$html = $this->fetch ( 'shop@widget/goods_detail' );
		echo $html;
	}
}