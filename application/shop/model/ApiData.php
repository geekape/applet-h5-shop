<?php

namespace app\shop\model;

use app\common\model\Base;
use think\Db;

/**
 * DiyPage模型
 */
class ApiData extends Base
{

    function initialize()
    {
        parent::initialize();
    }

    // 首页
    function index()
    {
        // 幻灯片
        $data['slideshow'] = D('Slideshow')->getShopList(WPID);

        // 推荐分类
        $data['category'] = D('Category')->getRecommendList();

        // 推荐商品
        $data['goods'] = D('ShopGoods')->getRecommendList();

        $data['diyData'] = D('DiyPage')->getInfoByPage('index');

        return $data;
    }

    /**
     * @api {POST} index.php/shop/api/category
     * @apiName 全部分类
     * @apiGroup 商城
     *
     * @apiParam {int} wpid 商城ID
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     * @apiSuccess {int} time 接口处理时间
     * @apiSuccess {array} data 分类数据
     * @apiSuccess {array} top_list 一级分类
     * @apiSuccess {array} sub_list 二级分类（全部）
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 1,
     * "msg": "操作成功",
     * "data": [
     * "top_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号"],...],
     * "sub_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号", "pid"=>"上级分类编号"],...],
     * ]
     * }
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 0,
     * "msg": "错误原因",
     * }
     */
    function category()
    {
        $list_data = D('Category')->getShopCategory(WPID);

        $top_list = $sub_list = [];
        foreach ($list_data as $vo) {
            if ($vo['pid'] == 0) {
                $top_list[$vo['id']] = $vo;
            } else {
                $sub_list[$vo['pid']][$vo['id']] = $vo;
            }
        }
        $data['top_list'] = $top_list;
        $data['sub_list'] = $sub_list;

        return $data;
    }

    /**
     * @api {POST} index.php/shop/api/center
     * @apiName 个人中心
     * @apiGroup 商城
     *
     * @apiParam {int} wpid 商城ID
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     * @apiSuccess {int} time 接口处理时间
     * @apiSuccess {array} data 分类数据
     * @apiSuccess {array} top_list 一级分类
     * @apiSuccess {array} sub_list 二级分类（全部）
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 1,
     * "msg": "操作成功",
     * "data": [
     * "top_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号"],...],
     * "sub_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号", "pid"=>"上级分类编号"],...],
     * ]
     * }
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 0,
     * "msg": "错误原因",
     * }
     */
    function center()
    {
        $data['myinfo'] = $GLOBALS['myinfo'];
        $data['diyData'] = D('DiyPage')->getInfoByPage('userCenter');

        return $data;
    }

    /**
     * @api {POST} index.php/shop/api/lists
     * @apiName 商品列表
     * @apiGroup 商城
     *
     * @apiParam {int} wpid 商城ID
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     * @apiSuccess {int} time 接口处理时间
     * @apiSuccess {array} data 分类数据
     * @apiSuccess {array} top_list 一级分类
     * @apiSuccess {array} sub_list 二级分类（全部）
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 1,
     * "msg": "操作成功",
     * "data": [
     * "top_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号"],...],
     * "sub_list": ["分类编号"=>["icon"=>"分类图标地址","title"=>"分类标题", "id"=>"分类编号", "pid"=>"上级分类编号"],...],
     * ]
     * }
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 0,
     * "msg": "错误原因",
     * }
     */
    function lists()
    {
        $map['wpid'] = WPID;
        // 按关键词搜索
        $search_key = I('search_key');
        if (!empty($search_key)) {
            $map['title'] = [
                'like',
                "%{$search_key}%"
            ];
        }

        // 按分类搜索
        $cate_id = I('cate_id');
        if (!empty($cate_id)) {
            $ccmap['wpid'] = WPID;
            $ccmap['category_first|category_second'] = ['in', $cate_id];
            $goodsIds = db('goods_category_link')->where(wp_where($ccmap))->column('goods_id');
            if ($goodsIds) {
                $map['id'] = array(
                    'in',
                    $goodsIds
                );
            } else {
                $map['id'] = 0;
            }
        }

        // 按价格区间搜索
        $min_price = I('min_price/d', 0);
        $max_price = I('max_price/d', 0);
        if ($min_price > 0 && $max_price > 0) {
            $map['sale_price'] = [
                'BETWEEN',
                [
                    $min_price,
                    $max_price
                ]
            ];
        } elseif ($min_price > 0) {
            $map['sale_price'] = [
                '>=',
                $min_price
            ];
        } elseif ($max_price > 0) {
            $map['sale_price'] = [
                '<=',
                $max_price
            ];
        }

        $tab_goods_id = input('tab_goods_id/d', 0);
        $tab = input('tab');
        addWeixinLog($tab,'shopgoodslists');

        $data['empty_tip'] = '暂无相关商品';
        if ($tab_goods_id > 0) {
            $data['empty_tip'] = '暂无同款商品';
            if (empty($tab)) {
                $map['id'] = 0;
            } else {
                $map['tab'] = $tab;
                $map['id'] = [
                    '<>',
                    $tab_goods_id
                ];
            }
        }

        $data['goods'] = D('ShopGoods')->getList($map);
        return $data;
    }

    function goods_detail()
    {
        $id = I('id');
        $goods = D('ShopGoods')->getInfo($id);
        $goods = D('ShopGoods')->getGoodsDetail($goods);

        // 判断是否收藏
        $cmap['uid'] = intval(session('mid_' . get_pbid()));
        $cmap['goods_id'] = $id;

        $collData = D('Shop/Collect')->where(wp_where($cmap))->count();

        if ($collData > 0) {
            $goods['is_collect'] = 1;
        } else {
            $goods['is_collect'] = 0;
        }
        $data['goods'] = $goods;
        $data['templateFile'] = 'detail';
        // 如果在其他商品下单时有选择优惠券但没购买，清空选择的值
        session('order_sn_id', null);
        return $data;
    }

    // 加入收藏
    public function addToCollect()
    {
        $goods_id = I('goods_id');
        $uid = session('mid_' . get_pbid());
        $data = D('Collect')->addToCollect($uid, $goods_id);
        return $data;
    }

    // 加入购物车
    public function addToCart()
    {
        $goods['goods_id'] = I('goods_id');
        $info = D('ShopGoods')->getInfo($goods['goods_id']);
        if ($info['is_delete'] == 1) {
            return $this->error('该商品已删除');
        } elseif ($info['is_show'] == 0) {
            return $this->error('该商品已下架');
        } elseif ($info['stock_active'] == 0) {
            return $this->error('该商品已售罄');
        }

        $goods['price'] = $info['sale_price'];
        $goods['wpid'] = $info['wpid'];

        $goods['num'] = I('buyCount', 1, 'intval');

        return D('Cart')->addToCart($goods);
    }

    // 购物车
    public function cart()
    {
        $mid = session('mid_' . get_pbid());
        $list = D('Cart')->getMyCart($mid, true);
        // diy
        $data['diyData'] = D('DiyPage')->getInfoByPage('cart');
        // dump($list);
        $data['lists'] = $list;
        $data['wpid'] = WPID;
        // dump($list);
        return $data;
    }

    private function confirm_order_goods($dao, $id, $num, &$data)
    {
        $shop_goods_id = $id;

        if ($data['event_type'] == COLLAGE_EVENT_TYPE) {
            $goods = D('Collage/CollageGoods')->getInfo($id);

            $shop_goods_id = $goods['shop_goods']['id'];

            $goods = array_merge($goods['shop_goods'], $goods); // 合并两个商品表的信息，优先选择拼团里的商品配置
            if ($data['is_original'] == 1) { // 原价购买
                $goods['sale_price'] = $goods['market_price'];
            }
        } elseif ($data['event_type'] == HAGGLE_EVENT_TYPE) {
            $event_order_id = input('event_id/d');

            $event_order = D('haggle/Order')->getInfo($event_order_id);
            $event = D('haggle/Haggle')->getInfo($event_order['haggle_id']);
            $shop_goods_id = $event['shop_goods_id'];
            $event_goods = [
                'title' => $event['title'],
                'shop_goods_id' => $event['shop_goods_id'],
                'market_price' => $event['market_price'],
                'sale_price' => $event_order['sale_price'],
                'stock' => $event['stock'],
                'stock_active' => $event['stock_active'],
                'express' => $event['express'],
                'send_type' => $event['send_type'],
                'stock_id' => $event['stock_id']
            ];

            $goods = array_merge($event['shop_goods'], $event_goods); // 合并两个商品表的信息，优先选择拼团里的商品配置

            // 被好友砍掉的价格
            $data['member_count'] = $event_order['member_count'];
            $data['dec_price'] = $event_order['market_price'] - $event_order['sale_price'];
            $data['event_market_pirce'] = $event['market_price'];
            $data['sale_price'] = $event_order['sale_price'];
        } else {
            $goods = $dao->getInfo($id);
        }

        $goods['num'] = $num;
        $detialurl = U('detail', [
            'id' => $id
        ]);

        $has_free = $this->freeRepeatOrder($data);
        if ($has_free && isset($goods['stock_id'])) { // 重新获取库存
            $goods['stock_active'] = D('shop/Stock')->where('stock_id', $goods['stock_id'])->value('stock_active');
        }
        if ($goods['stock_active'] < $num) {
            return $this->error('抱歉，' . $goods['title'] . ' 库存不足！', $detialurl);
        } elseif ($goods['stock_active'] <= 0) {
            return $this->error('抱歉，' . $goods['title'] . ' 已售罄！', $detialurl);
        } elseif ($goods['is_delete'] == 1) {
            return $this->error('抱歉，' . $goods['title'] . ' 商品已删除！', $detialurl);
        } elseif ($goods['is_show'] == 0) {
            return $this->error('抱歉，' . $goods['title'] . ' 商品已下架！', $detialurl);
        }

        $total_price = $goods['num'] * $goods['sale_price'];
        return [
            'goods' => [
                'id' => $id,
                'cover' => $goods['cover'],
                'title' => $goods['title'],
                'sale_price' => $goods['sale_price'],
                'market_price' => $goods['market_price'],
                'num' => $goods['num'],
                'express' => $goods['express'],
                'shop_goods_id' => $shop_goods_id
            ],
            'total_price' => wp_money_format($total_price),
            'send_type' => $goods['send_type'],
            'code' => 1,
            'msg' => ''
        ];
    }

    // 订单确认
    public function confirm_order()
    {
        // 订单信息
        $goods_id = input('goods_id', 0);
        $shop_order_id = input('shop_order_id/d', 0);
        $confirm_order_from = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('shop/wap/index');
        if ((IS_POST || !empty($goods_id)) && $shop_order_id<=0) {
            session('confirm_order_from', $confirm_order_from);
            $data['event_type'] = $event_type = input('event_type/d', 0);
            $data['event_type_title'] = D('shop/Order')->from_type($event_type);
            $data['event_id'] = input('event_id/d', 0);
            $data['is_original'] = $is_original = input('is_original/d', 0);
            $data['pay_type'] = 0;

            $dao = D('ShopGoods');
            $is_weiapp = input('is_weiapp/d', 0);

            $data['cart_ids'] = input('cart_ids', 0);
            if ($data['cart_ids'] != 0) {
                //购物车
                $goods_ids = input('goods_ids');
                if (!is_array($goods_ids)) {
                    $goods_ids = explode(',', $goods_ids);
                }
                $numArr = input('buyCount');
                if (!is_array($numArr)) {
                    $numArr = json_decode($numArr, true);
                }
            } else {
                //直接购买
                $goods_ids = explode(',', $goods_id);
                $numArr = [];
                foreach ($goods_ids as $gid) {
                    $numArr[$gid] = 1;
                }
            }

            $total_price = $total_express = $expessArr = [];
            foreach ($goods_ids as $id) {
                if (!isset($numArr[$id])) {
                    $numArr[$id] = 1;
                }

                $res = $this->confirm_order_goods($dao, $id, $numArr[$id], $data);
                if ($res['code'] == 0) {
                    return $res;
                }
                $send_type = explode(',', $res['send_type']);
                if (count($send_type) == 2) {
                    $type = 3;
                } else {
                    $type = $res['send_type'];
                }
                $list[$type][] = $res['goods'];

                // 总价计算
                if (isset($total_price[$type])) {
                    $total_price[$type] += $res['total_price'];
                } else {
                    $total_price[$type] = $res['total_price'];
                }

                // 邮费计算
                if ($type == 2) {
                    $total_express[$type] = 0;
                } elseif (!isset($expessArr[$id])) {
                    $expessArr[$id] = 1;

                    if (isset($total_express[$type])) {
                        $total_express[$type] += $res['goods']['express'];
                    } else {
                        $total_express[$type] = $res['goods']['express'];
                    }
                }
            }

            $data['goods_id'] = isset($res['goods']['id']) ? $res['goods']['id'] : 0; // 给单个商品回传商品ID
            $data['lists'] = $list;
            $data['total_price'] = $total_price;
            $data['total_express'] = $total_express;
            session('confirm_order', $data);
            if ($is_weiapp == 1) {
                return $data;
            }
        } elseif ($shop_order_id > 0) {
            session('confirm_order_from', $confirm_order_from);
            $data = D('shop/Order')->findById($shop_order_id);
            $data['event_id'] = input('event_id/d', 0);
            $data['shop_order_id'] = $shop_order_id;


            $goods = json_decode($data['goods_datas'], true);

            $data['goods_id'] = isset($goods['id']) ? $goods['id'] : 0; // 给单个商品回传商品ID
            
//             $send_type = strpos($data['extra'], ',') === false ? $data['send_type'] : 3;
            $send_type = explode(',', $data['extra']);
            if (count($send_type) == 2) {
            	$send_type = 3;
            } else {
            	$send_type = $data['send_type'];
            }
            
            $data['lists'][$send_type] = $goods;

            $total_price = $data['total_price'];
            unset($data['total_price']);

            $data['total_price'][$send_type] = $total_price;
            $data['total_express'][$send_type] = $data['mail_money'];

            session('confirm_order', $data);
        } else {
            $data = session('confirm_order');
        }
        if (empty($data) || !isset($data['event_type'])) {
            return redirect('index'); // 报错时回跳时参数如果丢失，不再继续，直接返回商城首页
        }

        // 前端显示控制
        $data['show_1'] = $data['show_2'] = 0;
        isset($data['total_price'][3]) && $data['show_1'] = $data['show_2'] = 1;
        isset($data['total_price'][1]) && $data['show_1'] = 2;
        isset($data['total_price'][2]) && $data['show_2'] = 2;

        // 收货地址
        $uid = session('mid_' . get_pbid());
        if (empty($uid)){
        	$this->error('找不到用户');
        }
        $data['address'] = D('Address')->getMyAddress($uid);

        if (empty($data['address'])) {
            return redirect(U('add_address?is_order=1'));
        }

        // 自动获取自提门店
        $data['allow_stores'] = D('shop/GoodsStoreLink')->getOrderStores($data);
        $store_id = input('store_id/d', 0);
        if ($store_id > 0) {
            D('Stores')->setDefault($uid, $store_id);
            $data['default_store'] = D('Stores')->getDefaultStore($uid);
        } else {
            $data['default_store'] = [
                'id' => 0,
                'name' => ''
            ];
        }


        // 可用的优惠券
        if ($data['event_type'] == SHOP_EVENT_TYPE) {
            $coupons = D('common/SnCode')->getMyAll($uid, true, '', 1);
        } else {
            $coupons = [];
        }

        // 获取
        $strCouponId = '';
        $id = I('goods_id');
        $couponDao = D('coupon/Coupon');
        $hasGet = [];
        foreach ($coupons as &$v) {
            if (!isset($hasGet[$v['target_id']])) {
                $hasGet[$v['target_id']] = $couponDao->getInfo($v['target_id']);
            }

            $v = array_merge($hasGet[$v['target_id']], $v);
        }
        $sn_id = I('sn_id/d', 0);
        if ($sn_id > 0) {
            session('order_sn_id', $sn_id);
        } else {
            $sn_id = session('order_sn_id');
        }
        $count = 0;
        $sn_info = [];
        $data['coupon_price'] = 0;
        if (!empty($coupons)) {
            $total_sale_price = $total_market_price = 0;
            $goods_ids = $coupon_check = $cate_list = [];
            foreach ($data['lists'] as $lists) {
                foreach ($lists as $g) {
                    $goods_ids[] = $id = empty($g['shop_goods_id']) ? $g['id'] : $g['shop_goods_id'];
                    $total_sale_price += $g['sale_price'];
                    $total_market_price += $g['market_price'];

                    $coupon_check[$id]['price'] = $g['sale_price'];
                }
            }
            if (!empty($goods_ids)) {
                $cate_list = M('goods_category_link')->whereIn('goods_id', $goods_ids)
                    ->field('goods_id,category_first,category_second')
                    ->select();
                foreach ($cate_list as $c) {
                    $coupon_check[$c['goods_id']]['cate'][] = $c['category_first'] . '_' . $c['category_second'];
                }
            }

            foreach ($coupons as $cp) {
                if (empty($cp['money'])) {
                    continue;
                }

                if ($cp['over_time'] > 0 && $cp['over_time'] < NOW_TIME) {
                    continue;
                }

                // 指定商品分类
                $goods_category = json_decode($cp['goods_category'], true);
                if (!empty($goods_category)) {
                    $cate_arr = [];
                    foreach ($goods_category as $cate) {
                        $cate_arr[] = $cate['category_first'] . '_' . $cate['category_second'];
                    }
                    // dump($cate_arr);
                    $check_arr = $coupon_check;
                    // dump($check_arr);
                    foreach ($check_arr as $k => $gd) {
                        isset($gd['cate']) || $gd['cate'] = [];

                        $in = array_intersect($cate_arr, $gd['cate']);
                        if (empty($in)) { // 没有交集，删除不在指定的分类内的商品
                            unset($check_arr[$k]);
                        }
                    }
                    // dump($check_arr);
                    if (empty($check_arr)) { // 所有的商品都不在指定分类里，该优惠券不可用
                        continue;
                    }
                } else {
                    $check_arr = $coupon_check;
                }

                // 订单满多少可使用
                if ($cp['order_money'] > 0) {
                    if ($cp['only_goods'] == 1) { // 单件
                        $check = true;
                        foreach ($check_arr as $check) {
                            if ($check['price'] >= $cp['order_money']) {
                                $check = false; // 只有有商品（被分类过虑后的商品）满足单品要求就可用
                                break;
                            }
                        }
                        if ($check) {
                            // dump(444);
                            continue;
                        }
                    } else { // 整单
                        $total_price = array_sum($data['total_price']);
                        if ($total_price < $cp['order_money']) {
                            // dump(555);
                            continue;
                        }
                    }
                }

                if ($cp['can_use'] && $cp['is_use'] == 0) {
                    $count += 1;
                    $strCouponId .= $cp['target_id'] . ',';
                }
                if ($cp['id'] == $sn_id) {
                    $sn_info = $cp;
                    $data['coupon_price'] = $cp['money'];
                }
            }
        }
        $data['sn_id'] = $sn_id;
        $data['str_coupon_id'] = $strCouponId;
        // dump($data['total_price']);
        $data['coupon_num'] = $count;
        $data['sn_info'] = $sn_info;
        $data['is_cart_goods'] = input('is_cart_goods/d', 0);

        $data['my_score'] = 0; // 用户积分，用于前端判断是否足够使用积分支付
        if ($data['pay_type'] == 90) {
            $data['my_score'] = D('common/User')->where('uid', $this->mid)->value('score'); // 实时从数据库取，不走缓存
        }
        

        return $data;
    }

    // 添加或编辑地址
    public function add_address()
    {
        $uid = session('mid_' . get_pbid());
        $info = D('Address')->where('uid', $uid)->find();
        if (IS_POST) {
            $data = I('post.');
            addWeixinLog($data, 'add_address_' . $this->mid . '_' . $uid);
            if (empty($data['truename'])) {
                return $this->error('收货人姓名不能为空');
            }
            if (empty($data['mobile'])) {
                return $this->error('手机号码不能为空');
            }
            if (empty($data['address'])) {
                return $this->error('收货地址不能为空');
            }
            if (empty($data['address_detail'])) {
                return $this->error('详细地址不能为空');
            }
            $data['uid'] = $uid;
            // dump($data);exit;
            $res = D('Address')->deal($data, $info);
            if ($data['is_choose'] == 1) {
                return redirect(U('confirm_order'));
            } else {
                return redirect(U('center'));
            }
        }

        if (!$info) {
            $info = getUserInfo($uid);
        }

        return [
            'info' => $info
        ];
    }

    // 门店列表
    function shop_list()
    {
        $ids = input('ids');
        $is_choose = input('is_choose/d');
        $res['store_lists'] = [];
        if ($is_choose == 1 && $ids != -1) {
            if (empty($ids)) {
                // 没有交集的门店
                $res['store_lists'] = [];
            } else {
                $res['store_lists'] = D('shop/Stores')->getListByIds($ids);
            }
        } else {
            $res['store_lists'] = D('shop/Stores')->getList();
        }
        foreach ($res['store_lists'] as &$vo){
        	if (empty($vo['img_url']) && isset($vo['img']) && $vo['img']>0){
        		$vo['img_url']=get_cover_url($vo['img']);
        	}
        }

        return $res;
    }

    public function delCart()
    {
        $ids = I('ids');
        return D('Cart')->delCart($ids);
    }

    function service_more()
    {
        // 加载历史数据
        $lastid = input('lastid/d', 0);
        $uid = session('mid_' . get_pbid());
        $to_uid = input('to_uid/d', 0);

        $data = D('Common/Chat')->load_data($uid, $lastid, $to_uid);
        return $data;
    }

    function freeRepeatOrder($info)
    {
        // 判断是否有重复未支付订单
        if ($info['event_type'] > 0) {
            $uid = isset($info['uid']) ? $info['uid'] : session('mid_' . get_pbid());

            $order = D('shop/Order')->where('uid', $uid)
                ->where('event_type', $info['event_type'])
                ->where('event_id', $info['event_id'])
                ->where('pay_status', 0)
                ->where('is_lock', 1)
                ->find();
            if (empty($order)) {
                return false;
            }

            D('shop/Order')->unLock($order);
            return true;
        }
        return false;
    }

    // 生成订单
    public function add_order()
    {
        $openid = get_openid();
        if (empty($openid) || $openid == -1) {
            return $this->error('获取openid失败,请在微信里打开!');
        }
        $info = session('confirm_order');
        //判断商品是否下架
        $dao = D('ShopGoods');
        foreach ($info['lists'] as $lists) {
        	foreach ($lists as $g) {
        		$gg = $dao->getInfo($g['shop_goods_id']);
        		if ($gg['is_delete'] == 1) {
        			return $this->error('抱歉，' . $gg['title'] . ' 商品已删除！');
        		} elseif ($gg['is_show'] == 0) {
        			return $this->error('抱歉，' . $gg['title'] . ' 商品已下架！');
        		}
        	}
        }
        //商品对应配送类型
        $goodsSendType = input('goods_send_type');
        
        $sendType = input('send_type');
        $stores_id = input('stores_id');
        if ($sendType == 2 && empty($stores_id)) {
            return $this->error('请先选择门店');
        }
        if (empty($this->mid)){
        	$this->mid=get_uid_by_openid(true,$openid);
        	if (empty($this->mid))
        		return $this->error('获取不到当前用户，请在微信里打开!');
        }

        $goods_ids = $ids = [];
        // 启动事务
        Db::startTrans();
        try {
        	// 邮寄商品一个订单，提取出自提的商品组成一个新订单
        	$ziti = $express = [];
        	$ziti_price = $express_price = $mail_money = 0;
        	
            $stockDao = D('Shop/Stock');
            foreach ($info['lists'] as $lists) {
            	foreach ($lists as $goods) {
            		if ($info['event_type'] != SECKILL_EVENT_TYPE) { // 秒杀活动已经在下单前锁定库存
            			$stockDao->beforeOrder($goods['num'], $goods['id'], $info['event_type']);
            		}
            		$goods_ids[] = $goods['id'];
            		
            		//根据商品的配送方式保存到对应的数组
            		if (isset($goodsSendType[$goods['id']])){
           				$goods['send_type']=$goodsSendType[$goods['id']] ;
            			if ($goodsSendType[$goods['id']] == 1){
            				//邮寄
            				$express[]=$goods;
							$express_price += $goods ['sale_price'] * intval ( $goods['num']);
							$mail_money += $goods['express'];
            			}else{
            				//自提
            				$ziti[]=$goods;
            				$ziti_price += $goods ['sale_price'] * intval ( $goods['num']);
            			}
            		}
            	}
            }

            /* if (isset($info['lists'][1])) {
                $express = $info['lists'][1];
                $express_price = $info['total_price'][1];
                $mail_money = $info['total_express'][1];
            }
            if (isset($info['lists'][2])) {
                $ziti = $info['lists'][2];
                $ziti_price = $info['total_price'][2];
            }
            if (isset($info['lists'][3])) {
                if ($sendType == 2) { // 自提
                    $ziti = array_merge($ziti, $info['lists'][3]);
                    $ziti_price += $info['total_price'][3];
                } else { // 邮寄
                    $express = array_merge($express, $info['lists'][3]);
                    $express_price += $info['total_price'][3];
                    $mail_money += $info['total_express'][3];
                }
            } */
            $total_price = array_sum($info['total_price']);

            $data['event_type'] = $info['event_type'];
            $data['event_id'] = $info['event_id'];
            $data['is_original'] = $info['is_original'];
            $data['pay_type'] = isset($info['pay_type']) ? $info['pay_type'] : 0; // 默认微信支付
            $data['address_id'] = D('Address')->where('uid', $this->mid)->value('id');
            $data['remark'] = I('remark');
            $data['uid'] = $this->mid;
            $data['out_trade_no'] = 'no' . date('ymdHis') . substr(uniqid(), 4);
            $data['cTime'] = NOW_TIME;
            $data['openid'] = $openid;
            $data['pay_status'] = 0;
            $data['wpid'] = get_wpid();
            $data['stores_id'] = $stores_id;
            $data['notice_erp'] = NOW_TIME; // 增加订单时通知ERP
            isset($info['extra']) && $data['extra'] = $info['extra']; // 扩展字段

            if (!empty($ziti) && empty($data['stores_id'])) {
                exception('请选择门店!!!');
            }
				
				// 使用优惠券
			$data ['dec_money'] = 0;
            $can_use_coupon = !empty($ziti) && !empty($express) ? false : true; // 不同配送方式时不能使用优惠券
            $can_use_coupon=true;//暂时可以试试
            if ($can_use_coupon && $data['pay_type'] != 90 && $data['event_type'] == SHOP_EVENT_TYPE) { // 活动不能使用优惠券
                $sn_id = I('sn_id');
//                 addWeixinLog($sn_id,'addeatadafdksf_11sncoupon');
                if ($sn_id > 0) {
                    $dao = D('common/SnCode');
                    $coupons = $dao->getMyAll($this->mid);

                    $res = 0;
                    foreach ($coupons as $cp) {
                        if ($cp['can_use'] && $cp['id'] == $sn_id) {
                            // 设置优惠券为已使用
                            $res = $dao->set_use($sn_id);

                            if ($res) {
                                $map['is_use'] = 1;
                                $map['target_id'] = $cp['target_id'];
                                $save['use_count'] = intval($dao->where(wp_where($map))->count());
                                D('coupon/Coupon')->updateInfo($cp['target_id'], $save);

                                $data['dec_money'] = $cp['prize_title'];

                                session('order_sn_id', null);
                            }
                            break;
                        }
                    }
                    // 保存代金券信息
                    $extArr['sn_info'] = array(
                        'sn_id' => $sn_id,
                        'is_use' => $res
                    );
                }
            }
            if (isset($extArr)) {
                $data['extra'] = json_encode($extArr);
            }
//             addWeixinLog($data['dec_money'],'addeatadafdksf_decmoney');
//             addWeixinLog($ziti,'addeatadafdksf_allz');
//             addWeixinLog($express,'addeatadafdksf_alle');
            $ziti_dec = $express_dec = $data ['dec_money'];
            if ($data['dec_money']>0 && !empty($ziti) && !empty($express)){
            	//计算分单使用优惠券获得可减金额比例
            	//自提可减金额
            	$ziti_dec=($ziti_price/($ziti_price+$express_price))*$data['dec_money'];
            	//保留两位数字
            	$ziti_dec = round ( $ziti_dec, 2 );
            	$ziti_dec = $ziti_dec >= $data ['dec_money'] ? $data ['dec_money'] : $ziti_dec;
            	//剩下的就给邮寄
            	$express_dec = $data ['dec_money'] - $ziti_dec;
            }

            if (!empty($ziti)) {
                $data['order_number'] = date('YmdHis') . substr(uniqid(), 4);
                $data['mail_money'] = 0;
                $data['goods_datas'] = json_encode($ziti);
                $data['total_price'] = $ziti_price > 0 ? $ziti_price : 0;
                $data['send_type'] = 2;
                $data['dec_money']=$ziti_dec;
                $data['pay_money'] = $data['total_price'] - $data['dec_money'];
                $data['pay_money'] < 0 && $data['pay_money'] = 0;
                if (isset($info['shop_order_id']) && $info['shop_order_id'] > 0) {
                    $ids[] = $id = $info['shop_order_id'];
                    D('Shop/Order')->updateOrder($id, $data);
                } else {
                    $ids[] = $id = D('Shop/Order')->addOrder($data);
                	addWeixinLog($data,'addeatadafdksf_ziti_'.$id);
                }
                if (!$id) {
                    exception('生成订单失败');
                }
                $this->add_event_order($data, $info, $id);
            }
            if (!empty($express)) {
                $data['order_number'] = date('YmdHis') . substr(uniqid(), 4);
                $data['mail_money'] = $mail_money;
                $data['goods_datas'] = json_encode($express);
                $data['total_price'] = $express_price > 0 ? $express_price : 0;
                $data['send_type'] = 1;
                $data['dec_money']=$express_dec;
                $data['pay_money'] = $data['total_price'] + $mail_money - $data['dec_money'];
                $data['pay_money'] < 0 && $data['pay_money'] = 0;
                if (isset($info['shop_order_id']) && $info['shop_order_id'] > 0) {
                    $ids[] = $id = $info['shop_order_id'];
                    D('Shop/Order')->updateOrder($id, $data);
                } else {
                    $ids[] = $id = D('Shop/Order')->addOrder($data);
                    addWeixinLog($data,'addeatadafdksf_youji_'.$id);
                }
                if (!$id) {
                    exception('生成订单失败');
                }

                $this->add_event_order($data, $info, $id);
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return [
                'code' => 0,
                'msg' => $e->getMessage()
            ];
        }

        if ($data['event_type'] == SHOP_EVENT_TYPE) {
            // 删除购物车消息
            $info['cart_ids'] != 0 && D('Cart')->delUserCart($this->mid, $goods_ids, $info['cart_ids']);
        } else {
            // 活动中，删除之前本人重复提交未支付的订单，以便释放库存
            $old_map['pay_status'] = 0;
            $old_map['is_lock'] = 1;
            $old_map['uid'] = $data['uid'];
            $old_map['event_id'] = $data['event_id'];
            $old_map['event_type'] = $data['event_type'];
            $old_map['id'] = [
                'NOT IN',
                $ids
            ];

            D('shop/Stock')->cronDealOrderStock($old_map);
        }
        return [
            'code' => 1,
            'msg' => '',
            'out_trade_no' => $data['out_trade_no']
        ];
    }

    private function add_event_order($data, $info, $order_id)
    {
        if ($data['event_type'] == COLLAGE_EVENT_TYPE && $data['is_original'] != 1) {
            // 同时写入拼团的订单表里
            $result = D('collage/Order')->addOrder($data, $info, $order_id);
            if ($result['code'] == 0) {
                exception($result['msg']);
            }
        }
    }

    public function do_pay()
    {
        $out_trade_no = I('out_trade_no');
        if (empty($out_trade_no)) {
            return $this->error('订单参数出错');
        }
        $lists = D('Shop/Order')->getOrderListByNo($out_trade_no);
        $del_count = D('shop/Stock')->checkOrderGoodsDelCount($lists);
        if ($del_count > 0) {
            return $this->error('订单商品已下架，该订单失效！');
        }

        $total_fee = 0;
        foreach ($lists as $orderInfo) {
            if ($orderInfo['is_lock'] == 0) {
                // 订单商品的库存已释放了
                return $this->error('订单已超时，请重新下订单！');
            }

            // $total_fee += $orderInfo['total_price'] + $orderInfo['mail_money'];
            $total_fee += $orderInfo['pay_money'];
        }
//         dump($lists);
        $is_weiapp = I('is_weiapp/d', 0);
        // 还没有配送信息，先去填写 TODO 小程序的情况下的跳转
        if (empty($orderInfo['address_id'])) {
            $url = U('Shop/Wap/confirm_order', [
                'event_type' => $orderInfo['event_type'],
                'event_id' => $orderInfo['event_id'],
                'shop_order_id' => $orderInfo['id']
            ]);
            return redirect($url);
        }
//         dump(111);
        if (is_numeric($out_trade_no) && $orderInfo['out_trade_no'] != $orderInfo['order_number']) {
            // 单个订单购买，把out_trade_no参数换成order_number
            $orderInfo['out_trade_no'] = $orderInfo['order_number'];
            D('Shop/Order')->updateId($orderInfo['id'], [
                'out_trade_no' => $orderInfo['order_number']
            ]);
        }

        if ($orderInfo['event_type'] == COLLAGE_EVENT_TYPE && $orderInfo['is_original'] == 0) {
            // 实时判断是否满员
            $order = D('collage/Order')->where('order_id', $orderInfo['id'])->find();
            if ($order['collage_group_id'] > 0) {
                $group = D('collage/CollageGroup')->getInfo($order['collage_group_id']);
                if ($group['status'] != 0) {
                    return $this->error('该团已满，请开新团');
                }
            }
//             dump(222);
            $data['jump_url'] = U('collage/wap/pay_done', [
                'total_fee' => $total_fee,
                'event_id' => $orderInfo['event_id'],
                'pay_type' => $orderInfo['pay_type']
            ]);
        } else {
            $data['jump_url'] = U('pay_done?total_fee=' . $total_fee . '&pay_type=' . $orderInfo['pay_type']);
        }

        // 积分支付
        if ($orderInfo['pay_type'] == 90) {
            // 积分只取整数
            $total_fee = ceil($total_fee);
            // 判断用户积分是否足够支付
            $score = D('common/User')->where('uid', $this->mid)->value('score'); // 实时从数据库取，不走缓存
            if ($score < $total_fee) {
                return $this->error('您的积分不足');
            }

            // 扣除用户积分
            $credit = [
                'uid' => $this->mid,
                'score' => 0 - $total_fee,
                'title' => '订单付款'
            ];
            // add_credit('payment', $credit);

            // 设置订单状态
            $orderInfo['total_fee'] = $total_fee;
            D('shop/Order')->payOk($orderInfo);
//             dump(333);
            return redirect($data['jump_url']);
        } else { // 微信支付
            // 测试期间关闭支付功能
//                  $orderInfo['total_fee'] = $total_fee;
//                  D('shop/Order')->payOk($orderInfo);
//                  return ['code'=>2];
//                 return redirect($data['jump_url']);

            $info = D('common/Publics')->getInfoById(PBID);
            if (empty($info['mch_id'])) {
//                 dump(444);
                return $this->error('还没配置支付信息');
            }
            $total_fee=0.01;//TODO 测试期间先固定1分钱
            $product = [
                'openid' => $orderInfo['openid'],
                'body' => '商品购买',
                'out_trade_no' => $orderInfo['out_trade_no'],
                'total_fee' => $total_fee * 100 // $total_fee * 100 
            ];
            $callback = 'shop/Order/payOk';
            add_debug_log($info, 'is_weiapp_'.$is_weiapp);
            if (!$is_weiapp) {
                $pay = D('weixin/Payment')->jsapi_pay($info['appid'], $product, $callback);
            } else {
                $pay = D('weixin/Payment')->weiapp_pay($info['appid'], $product, $callback);
            }
//             dump(555);
//             dump($pay);
            if ($pay['status'] == 0) {
                return $this->error($pay['msg']);
            }

            $data['pay'] = $pay;
        }

        $back_url = session('confirm_order_from');
        $data['back_url'] = empty($back_url) ? U('shop/wap/my_order') : $back_url;
//         dump(666);
        return $data;
    }

    function pay_done()
    {
        return [
            'data' => ''
        ];
    }

    function my_order()
    {
        $myorders = D('Shop/Order')->getOrderList();
        // dump('--全部订单--');

        $data['orderList'] = $myorders;

        D('Shop/Order')->autoSetFinish();

        // diy
        $data['diyData'] = D('DiyPage')->getInfoByPage('orderlist');

        return $data;
    }

    function order_detail()
    {
        $id = I('id/d', 0);
        if (empty($id)) {
            return $this->error('订单不存在!');
        }
        $orderDao = D('shop/Order');
        $data['info'] = $orderInfo = $orderDao->getInfo($id);
        if (!isset($orderInfo['address_id'])) {
            return $this->error('订单不存在');
        }

        $data['store_info'] = [];
        if ($orderInfo['stores_id']) {
            $data['store_info'] = M('stores')->where('id', $orderInfo['stores_id'])->find();
            if (empty($data['store_info']['img_url']) && $data['store_info']['img']>0){
            	$data['store_info']['img_url']=get_cover_url($data['store_info']['img']);
            }
        }
        $address_id = $orderInfo['address_id'];
        $data['addressInfo'] = D('Shop/Address')->getInfo($address_id);

        if ($orderInfo['status_code'] == 3) {
            // 在配送中的订单自动从接口获取快递信息
            $res = $orderDao->getSendInfo($id);
        }

        $data['log'] = M('shop_order_log')->where('order_id', $id)
            ->order('status_code desc,cTime desc')
            ->find();

        return $data;
    }

    function logistics()
    {
        $id = I('order_id/d', 0);
        if (empty($id)) {
            return $this->error('订单不存在!');
        }

        $orderDao = D('shop/Order');
        $data['info'] = $orderInfo = $orderDao->getInfo($id);

        $data['log'] = M('shop_order_log')->where('order_id', $id)
            ->order('status_code desc,cTime desc')
            ->select();

        return $data;
    }

    // 确认收货
    public function confirm_get()
    {
        $id = I('id');
        $save['is_send'] = 2;
        $res = D('Shop/Order')->updateId($id, $save);

        $res = D('Shop/Order')->setStatusCode($id, 4);

        if ($res) {
            return $this->success('设置成功');
        } else {
            return $this->error('设置失败');
        }
    }

    function tcp()
    {
        $info = M('shop')->find(WPID);
        return $info;
    }

    // 商品评价
    function comment()
    {
        if (IS_POST) {
            $sensitiveStr = config('SENSITIVE_WORDS');
            $sensitiveArr = explode(',', $sensitiveStr);
            $badkeywords = array_combine($sensitiveArr, array_fill(0, count($sensitiveArr), '***'));
            foreach ($badkeywords as $k => $v) {
                if (empty($k)) {
                    unset($badkeywords[$k]);
                }
            }

            $order_id = input('post.order_id');
            $goodsids = input('post.goodsids');

            $commentDao = D('Shop/GoodsComment');
            $goodsDao = D('Shop/ShopGoods');
            foreach ($goodsids as $goodsId) {
                $contentArr = input('post.content');
                if (!isset($contentArr[$goodsId]) || empty($contentArr[$goodsId])) {
                    return $this->error('商品评论语不能为空！');
                }
                if (get_str_length($contentArr[$goodsId]) < 5) {
                    return $this->error('商品评论语不能少于5个字！');
                }
                if ($contentArr[$goodsId] == strtr($contentArr[$goodsId], $badkeywords)) {
                    $comData = [];
                    $comData['wpid'] = get_wpid();
                    $comData['cTime'] = time();
                    $comData['uid'] = $this->mid;
                    $comData['order_id'] = $order_id;
                    $comData['content'] = $contentArr[$goodsId];
                    $comData['score'] = 0;
                    $comData['goods_id'] = $goodsId;
                    $comData['is_show'] = 0; // 默认不显示
                    $comments[] = $comData;
                } else {
                    return $this->error('商品评论语出现敏感词！');
                }
            }
            if ($comments) {
                $res = $commentDao->insertAll($comments);
            }
            if ($res) {
                D('Shop/Order')->setStatusCode($order_id, 7);
                foreach ($goodsids as $gid) {
                    $commentDao->getShopComment($gid, true);
                }
                return $this->success('评论成功！', U('my_order'));
            } else {
                return $this->error('评论失败！');
            }
        } else {
            $orderId = I('order_id/d', 0);
            if (empty($orderId)) {
                return $this->error('订单不存在!');
            }
            $data['info'] = D('Shop/Order')->getInfo($orderId);
            return $data;
        }
    }

    // 我的评价
    public function my_comment()
    {
        $map['uid'] = $this->mid;
        $map['wpid'] = get_wpid();
        $data = M('shop_goods_comment')->where(wp_where($map))
            ->order('id desc')
            ->select();
        $goodsDao = D('Shop/ShopGoods');
        foreach ($data as $key => &$vo) {
            $goods = $goodsDao->getInfo($vo['goods_id']);
            if (empty($goods)) {
                unset($data[$key]);
                continue;
            }

            $vo['goods_title'] = $goods['title'];
            $vo['goods_img'] = $goods['cover'];
            $vo['sale_price'] = $goods['sale_price'];
        }
        return [
            'lists' => $data
        ];
    }

    // 我的收藏
    public function my_collect()
    {
        $follow_id = $this->mid;
        $data['myCollect'] = D('Collect')->getMyCollect($follow_id, true);
        return $data;
    }

    function my_track()
    {
        $data['track'] = D('Track')->getMyTrack($this->mid);
        return $data;
    }

    function refund()
    {
        $data['id'] = input('id/d');
        return $data;
    }

    function doRefund()
    {
        $id = input('id/d');
        $content = input('refund_content');
        if (empty($content)) {
            return $this->error('退款理由不能为空！');
        }
        if (get_str_length($content) < 5) {
            return $this->error('退款理由不能少于5个字！');
        }

        $res = D('shop/Order')->updateId($id, [
            'refund' => 1,
            'refund_content' => $content
        ]);
        if ($res !== false) {
            return $this->success('申请提交完成', U('my_order'));
        } else {
            return $this->success('申请提交失败，请联系客服');
        }
    }

    /**
     * 接口获取用户信息
     */
    function getUserInfo()
    {
        $openid = input('openid');
        $uid = D('common/Follow')->init_follow($openid);
        $userInfo = getUserInfo($uid);
        $data['user_info'] = $userInfo;
        return $data;
    }
    /**
     * 获取活动列表
     */
    function getEventLists(){
		$type = input ( 'event_type/d', 0 );
		$notStart = [ ]; // 未开始
		$onGoing = [ ]; // 进行中
		$end = [ ]; // 结束
		$wpid = get_wpid ();
		$table = '';
		$picLab = '';
		switch ($type) {
			case 1 :
				// 拼团 collage
				$table = 'collage';
				$picLab = 'cover';
				break;
			case 2 :
				// 秒杀 seckill
				$table = 'seckill';
				$picLab = 'cover';
				break;
			case 3 :
				// 砍价 haggle
				$table = 'haggle';
				$picLab = 'share_cover';
				break;
			case 4 :
				// 优惠券 coupon
				$table = 'coupon';
				$picLab = 'background';
				break;
			default :
				break;
		}
		if ($table != '') {
			$lists = M ( $table )->where ( 'wpid', $wpid )->order ( 'id desc' )->select ();
			foreach ( $lists as $vo ) {
				$vo ['cover_img'] = isset ( $vo [$picLab] ) && $vo [$picLab] > 0 ? get_cover_url ( $vo [$picLab] ) : '';
				if ($vo ['start_time'] > NOW_TIME) {
					// 未开始
					$notStart [] = $vo;
				} elseif ($vo ['start_time'] <= NOW_TIME && $vo ['end_time'] > NOW_TIME) {
					// 进行中
					$onGoing [] = $vo;
				} elseif ($vo ['end_time'] <= NOW_TIME) {
					$end = $vo;
				}
			}
		}
		$data ['event_type'] = $type;
		$data ['not_start'] = $notStart;
		$data ['on_going'] = $onGoing;
		$data ['end'] = $end;
		return $data;
    }

}
