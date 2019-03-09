<?php

namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class ShopGoods extends Base {
	protected $table = DB_PREFIX . 'shop_goods';
	function initialize() {
		parent::initialize ();
		$this->openCache = true;
	}
	public function getInfo($id, $update = false, $data = [], $thum = true) {
		$key = cache_key ( 'id:' . $id, $this->table );
		$info = S ( $key );
		if ($info === false || $update || true) {
			if (empty ( $data )) {
				$info = $this->findById ( $id );
			} else {
				$info = $data;
			}
			if (empty ( $info )) {
				return [ ];
			}
			
			// 商品详情
			$info ['content'] = M ( 'shop_goods_content' )->where ( 'goods_id', $id )->value ( 'content' );
// 			$info ['content'] = str_replace ( 'src="/uploads/', 'src="' . SITE_URL . '/uploads/', $info ['content'] );
			
			// 商品封面
			$info ['cover_db'] = $info ['cover'];
			$info ['cover'] = get_cover_url ( $info ['cover'], 330, 360 );
			
			if (! isset ( $info ['imgs_url'] ) && ! empty ( $info ['imgs'] )) {
				$imgs = wp_explode ( $info ['imgs'] );
				
				foreach ( $imgs as $img ) {
					if ($thum === false) {
						$imgs_url [] = get_cover_url ( $img );
					} else {
						$imgs_url [] = get_cover_url ( $img, 750, 560 );
					}
				}
				// dump($imgs_url);
				$info ['imgs_url'] = array_filter ( $imgs_url );
			}
			S ( $key, $info );
		}
		if (! empty ( $info )) {
			// 库存价格信息
			$stock = D ( 'shop/Stock' )->getInfoByGoodsId ( $id, SHOP_EVENT_TYPE );
			$info = array_merge ( $stock, $info );
		}
		// dump($info);
		return $info;
	}
	function getGoodsDetail($goods) {
		isset ( $goods ['shop_goods'] ) && $goods = $goods ['shop_goods'];
		
		$shop_goods_id = $goods ['id'];
		// 商品详情
		if (isset ( $goods ['diy_id'] ) && $goods ['diy_id'] > 0) {
			$goods ['diyData'] = D ( 'Shop/DiyPage' )->getInfo ( $goods ['diy_id'] );
		} else {
			$goods ['diyData'] ['config'] = '';
		}
		
		if (! isset ( $goods ['diyData'] ['config'] ) || empty ( $goods ['diyData'] ['config'] )) {
			$goods ['is_diy'] = 0;
		} else {
			$goods ['is_diy'] = 1;
		}
		
		// 获取商品评论信息
		$comments = D ( 'Shop/GoodsComment' )->getShopComment ( $shop_goods_id );
		foreach ( $comments as &$cc ) {
			$name = get_username ( $cc ['uid'] );
			if ($name) {
				$cc ['username'] = hideStr ( $name, 1, 1, 4, $glue = "*" );
			} else {
				$cc ['username'] = '匿名';
			}
			$cc ['cTime'] = time_format ( $cc ['cTime'] );
			$cc ['headimgurl']=get_userface($cc['uid']);
		}
		$goods ['comments'] = $comments;
		$goods ['comment_count'] = count ( $comments );
		
		// 商品参数
		$goods ['goods_param'] = M ( 'goods_param_link' )->where ( 'goods_id', $goods ['id'] )->select ();
		// dump($goods);
		// 记录足迹
		$uid = session ( 'mid_' . get_pbid () );
		if ($uid > 0) {
			D ( 'shop/Track' )->addToTrack ( $uid, $shop_goods_id );
		}
		return $goods;
	}
	public function updateById($id, $data) {
		$res = $this->where ( 'id', $id )->update ( $data );
		if ($res) {
			$this->clearCache ( $id );
		}
	}
	public function getRecommendList() {
		$map ['is_recommend'] = [ 
				'>',
				0 
		];
		
		return $this->getList ( $map, 'is_recommend desc, id desc', 8 );
	}
	public function getList($map = [], $order = 'id desc', $limit = null) {
		$map ['g.is_show'] = 1;
		$map ['g.is_delete'] = 0;
		$map ['g.wpid'] = WPID;
		$map ['s.stock_active'] = [ 
				'>',
				0 
		];
		
		$obj = $this->alias ( 'g' )->join ( 'shop_goods_stock s', 's.goods_id = g.id and s.event_type=' . SHOP_EVENT_TYPE )->where ( wp_where ( $map ) )->order ( $order );
		
		if ($limit === null) {
			$list = $obj->select ();
		} else {
			$list = $obj->limit ( $limit )->select ();
		}
		
		if (empty ( $list ))
			return [ ];
		
		$rdata = [ ];
		foreach ( $list as $vo ) {
			// 商品封面
			$vo ['cover_db'] = $vo ['cover'];
			$vo ['cover'] = get_cover_url ( $vo ['cover'], 330, 360 );
			$rdata [] = $vo;
		}
		
		return $rdata;
	}
	
	// 热销度计算
	public function getRank($id, $info = []) {
		static $_max_sale_count;
		empty ( $info ) && $info = $this->getInfo ( $id );
		
		if (empty ( $_max_sale_count )) {
			$map ['wpid'] = get_wpid ();
			$map ['is_show'] = 0;
			$_max_sale_count = $this->where ( wp_where ( $map ) )->value ( 'max(sale_count)' );
		}
		
		// 30天的时间权重值
		$time_rank = 25 * (30 - (date ( 'Ymd' ) - date ( 'Ymd', $info ['show_time'] ))) / 30;
		$time_rank < 0 && $time_rank = 0;
		
		// 推荐权重
		$recommend_rank = 25 * $info ['is_recommend'];
		
		// 销量权限
		$sale_rank = 50 * $info ['sale_count'] / $_max_sale_count;
		
		return $time_rank + $recommend_rank + $sale_rank;
	}
}
