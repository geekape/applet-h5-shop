<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Order extends Base
{

    protected $table = DB_PREFIX . 'shop_order';

    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }

    function getInfo($id, $update = false, $data = [])
    {
        $key = cache_key('id:' . $id, $this->table);
        $info = S($key);
        if ($info === false || $update || true) {
            if (empty($data)) {
                $info = $this->findById($id);
            } else {
                $info = $data;
            }
            
            if (! empty($info)) {
                $info['event_type_db'] = $info['event_type'];
                $info['event_type'] = $this->from_type($info['event_type']);
                
                $info['common'] = $info['pay_type'] == 0 ? '微信支付' : '积分支付';
                $code = array(
                    "sf" => '顺丰',
                    "sto" => '申通',
                    "yt" => '圆通',
                    "yd" => '韵达',
                    "tt" => '天天',
                    "ems" => 'EMS',
                    "zto" => '中通',
                    "ht" => '汇通',
                    "qf" => '全峰'
                );
                
                if ($info['status_code'] == 0 && $info['pay_status'] != 0) {
                    $info['status_code'] = $saveStatus['status_code'] = 1;
                    $this->where(wp_where(array(
                        'id' => $id
                    )))->update($saveStatus);
                }
                $info['send_code_name'] = isset($code[$info['send_code']]) ? $code[$info['send_code']] : '';
                
                $info['status_code_name'] = $this->_status_code_name($info['status_code']);
                
                $info['status'] = $info['pay_status'] == 0 ? '未支付' : '已支付';
                if (! empty($info['goods_datas'])) {
                    $goods = json_decode($info['goods_datas'], true);
                } else {
                    $goods = [];
                }
                
                $info['goods'] = $goods;
                
                $info['refund_title'] = $this->refund_name($info['refund']);
                
                unset($info['goods_datas']);
            }
            S($key, $info);
        }
        
        return $info;
    }

    function refund_name($refund)
    {
        $refundArr = [
            '0' => '未退款',
            '1' => '申请退款中',
            '2' => '已退款',
            '3' => '拒绝退款'
        ];
        return $refundArr[$refund];
    }

    function from_type($event_type, $extra = '')
    {
        $res = '';
        if ($event_type == 1) {
            $res = $extra == 1 ? '拼团-原价' : '拼团';
        } elseif ($event_type == 2) {
            $res = '秒杀';
        } elseif ($event_type == 3) {
            $res = '砍价';
        }
        return $res;
    }

    function _status_code_name($code)
    {
        $status_code = array(
            0 => '待支付',
            1 => '待商家确认',
            2 => '待发货',
            3 => '配送中',
            4 => '确认已收货',
            5 => '确认已收款',
            6 => '待评价',
            7 => '已评论'
        );
        return $status_code[$code];
    }

    function getOrderList()
    {
        $list = $this->where('uid', session('mid_' . get_pbid()))
            ->where('is_lock=1 or (is_lock=0 and refund>0)')
            ->order('id desc')
            ->select();
        $list = isset($list) ? $list : [];
        
        foreach ($list as &$v) {
            $v['goods_datas'] = json_decode($v['goods_datas'], true); // 订单数据
            $v['event_type'] = $this->from_type($v['event_type'], $v['extra']);
            $v['refund_title'] = $this->refund_name($v['refund']);
        }
        
        return $list;
    }

    function getOrderListByNo($out_trade_no)
    {
        if (is_numeric($out_trade_no)) {
            return $this->where('id', $out_trade_no)->select();
        } else {
            return $this->where('out_trade_no', $out_trade_no)->select();
        }
    }

    function getSendInfo($id)
    {
        $info = $this->getInfo($id);
        $map['id'] = $info['wpid'];
        $api_key = M('shop')->where(wp_where($map))->value('api_key');
        empty($api_key) && $api_key = '02727dd96ccf4c4eabb091d85cb7fa10';
        
        $url = 'http://v.juhe.cn/exp/index?key=' . $api_key . '&com=' . $info['send_code'] . '&no=' . $info['send_number'];
        $data = wp_file_get_contents($url);
        $data = json_decode($data, true);
        
        if ($data['resultcode'] == 200) {
            $save['order_id'] = $id;
            $save['status_code'] = 3;
            $save['extend'] = 1;
            M('shop_order_log')->where(wp_where($save))->delete();
            
            $Alldata = [];
            foreach ($data['result']['list'] as $vo) {
                $save['cTime'] = strtotime($vo['datetime']);
                $save['remark'] = $vo['zone'] . ' ' . $vo['remark'];
                
                $Alldata[] = $save;
            }
            if (! empty($Alldata)) {
                M('shop_order_log')->insertAll($Alldata);
            }
        }
        
        return $data;
    }

    function updateId($id, $save)
    {
        $map['id'] = $id;
        $this->where(wp_where($map))->update($save);
        $info = $this->getInfo($id, true);
        return $info;
    }

    function setStatusCode($id, $code, $has_save = false)
    {
        if ($has_save == false) {
            $save['status_code'] = $code;
            $res = $this->updateId($id, $save);
        }
        
        $data['order_id'] = $id;
        $data['status_code'] = $code;
        $data['cTime'] = NOW_TIME;
        
        $info = $this->getInfo($id);
        
        if ($code > 2) {
            if ($info['event_type_db'] == HAGGLE_EVENT_TYPE) {
                D('haggle/Order')->where('order_id', $id)
                    ->where('is_pay', '<', 2)
                    ->setField('is_pay', 2);
            } elseif ($info['event_type_db'] == COLLAGE_EVENT_TYPE) {
                D('collage/Order')->where('order_id', $id)
                    ->where('is_pay', '<', 2)
                    ->setField('is_pay', 2);
            } elseif ($info['event_type_db'] == SECKILL_EVENT_TYPE) {
                D('seckill/Order')->where('order_id', $id)
                    ->where('is_pay', '<', 2)
                    ->setField('is_pay', 2);
            }
        }
        
        switch ($code) {
            case '1':
                $data['remark'] = '您提交了订单，请等待商家确认';
                break;
            case '2':
                $data['remark'] = '商家已经确认订单，开始拣货';
                break;
            case '3':
                if ($info['send_type'] == 2) {
                    $data['remark'] = '商家把您的订单设为已自提';
                } else {
                    $data['remark'] = '您的订单已经发货，发货快递： ' . $info['send_code_name'] . ', 快递单号： ' . $info['send_number'];
                }
                
                $data['extend'] = '0';
                break;
            case '4':
                $data['remark'] = '确认已收货';
                break;
            case '5':
                $data['remark'] = '确认已收款';
                break;
            case '6':
                $data['remark'] = '订单已完成，请评价这次服务';
                $data['extend'] = '2';
                break;
            case '7':
                $data['remark'] = '评论完成，欢迎下次再来';
                break;
        }
        
        M('shop_order_log')->insert($data);
        
        return true;
    }

    function autoSetFinish()
    {
        $over_time = NOW_TIME - 15 * 24 * 3600; // 15天后自动设置为已收货或已评论
        $this->finishSend($over_time);
        $this->finishComment($over_time);
    }

    private function finishSend($over_time)
    {
        $map['status_code'] = $map2['status_code'] = 3;
        $map['pay_status'] = 1;
        
        $order_ids = $this->where(wp_where($map))->column('id');
        if (empty($order_ids)) {
            return false;
        }
        
        $map2['order_id'] = array(
            'in',
            $order_ids
        );
        $map2['extend'] = '0';
        $map2['cTime'] = array(
            'lt',
            $over_time
        );
        $order_ids = M('shop_order_log')->where(wp_where($map2))->column('order_id');
        if (empty($order_ids)) {
            return false;
        }
        
        M('shop_order_log')->where(wp_where($map2))->setField('extend', 1);
        foreach ($order_ids as $id) {
            $this->setStatusCode($id, 6);
        }
    }

    private function finishComment($over_time)
    {
        // 15天后，自动取消该订单评价的资格
        $map3['status_code'] = 6;
        $map3['pay_status'] = 1;
        
        $order_ids = $this->where(wp_where($map3))->column('id');
        if (empty($order_ids)) {
            return false;
        }
        
        $map4['order_id'] = array(
            'in',
            $order_ids
        );
        $map4['extend'] = 2;
        $map4['cTime'] = array(
            'lt',
            $over_time
        );
        $order_ids = M('shop_order_log')->where(wp_where($map4))->column('order_id');
        if (empty($order_ids)) {
            return false;
        }
        
        M('shop_order_log')->where(wp_where($map4))->setField('extend', 1);
        foreach ($order_ids as $id) {
            $this->setStatusCode($id, 7);
        }
    }

    // 获取用户有效订单的商品总数量，总消费金额
    function getTotalData($uid)
    {
        $map['uid'] = $uid;
        $map['wpid'] = get_wpid();
        // 完成订单
        $map['status_code'] = array(
            'in',
            '4,5,6,7'
        );
        $totals = $this->where(wp_where($map))
            ->field("sum( total_price ) as totals")
            ->select();
        $goodsDataJson = $this->where(wp_where($map))->column('goods_datas');
        $goodsNum = 0;
        $goodsIdNums = null;
        foreach ($goodsDataJson as $vo) {
            $goodsData = json_decode($vo, true);
            foreach ($goodsData as $goods) {
                $goodsNum += $goods['num'];
                if ($goodsIdNums[$goods['id']]) {
                    $goodsIdNums[$goods['id']] += $goods['num'];
                } else {
                    $goodsIdNums[$goods['id']] = intval($goods['num']);
                }
            }
        }
        $data['total_money'] = empty($totals[0]['totals']) ? 0 : $totals[0]['totals'];
        $data['goods_count'] = $goodsNum;
        $data['goods_id_num'] = $goodsIdNums;
        return $data;
    }

    // 获取有效订单的各商品销售量
    function getGoodsSaleCount()
    {
        // $map['wpid']=$shopId;
        // $map['wpid']=get_wpid();
        // 完成订单
        $map['pay_status'] = array(
            'in',
            '2,3,4'
        );
        $goodsDataJson = $this->value('goods_datas');
        $goodsIdNums = null;
        if (! empty($goodsDataJson) && is_array($goodsDataJson)) {
            foreach ($goodsDataJson as $vo) {
                $goodsData = json_decode($vo, true);
                foreach ($goodsData as $goods) {
                    if ($goodsIdNums[$goods['id']]) {
                        $goodsIdNums[$goods['id']] += $goods['num'];
                    } else {
                        $goodsIdNums[$goods['id']] = intval($goods['num']);
                    }
                }
            }
        }
        return $goodsIdNums;
    }

    // 查询订单支付状况，并更新其状态值
    function refreshPayStatus($uid = 0)
    {
        // 查询支付未成功的订单
        if ($uid > 0) {
            $map['uid'] = $uid;
        }
        $map['pay_status'] = array(
            'neq',
            1
        );
        $twoTime = time() - 48 * 60 * 60;
        $map['cTime'] = array(
            'egt',
            $twoTime
        );
        $map['wpid'] = get_wpid();
        $datas = $this->where(wp_where($map))->select();
        
        $paymentDao = D('weixin/Payment');
        $stockdao = D('shop/Stock');
        
        $appid = D('common/Publics')->getInfoById(WPID, 'appid');
        foreach ($datas as $vo) {
            if (empty($vo['out_trade_no'])) {
                continue;
            }
            $save = [];
            $result = $paymentDao->query_order($appid, $vo['out_trade_no']);
            if ($result && $result['status'] == 1) {
                $price = floatval($vo['total_price']) + floatval($vo['mail_money']);
                
                $paymoney = $result['total_fee'] / 100;
                $issuccess = 1;
                if ($price != $paymoney && false) { // TODO 调试期间由于所有订单设为1分，因此要关闭此功能
                                                    // 支付金额不对
                    $issuccess = 0;
                    $save['order_state'] = 2; // 异常
                    $extArr = json_decode($vo['extra'], true);
                    $extArr['order_state_msg'] = '应支付金额' . $price . '元，实际支付金额' . $paymoney . '元';
                    $save['extra'] = json_encode($extArr);
                }
                $save['pay_time'] = strtotime($result['time_end']);
                //
                $goodsData = json_decode($vo['goods_datas'], true);
                
                if ($vo['is_lock'] == 0 && $issuccess) {
                    // 锁定库存已被释放，重新锁定
                    foreach ($goodsData as $goods) {
                        $stockdao->beforeOrder($goods['num'], $goods['id'], $vo['event_type']);
                    }
                    $save['is_lock'] = 1;
                }
                
                // 全额支付
                $save['pay_status'] = 1;
                
                $this->updateId($vo['id'], $save);
                if ($save['pay_status'] == 1 && $issuccess) {
                    // 订金全额支付 确认已收款
                    $this->setStatusCode($vo['id'], 5);
                    
                    // 用户支付后库存处理
                    $stockdao->afterPaymentByOrder($vo);
                }
            }
        }
    }

    // 查询未支付订单是否使用代金券或积分并退还
    function giveBackExtr($order_id = 0, $wpid = '', $uid = 0)
    {
        // 查询支付未成功的订单
        if ($uid > 0) {
            $map['uid'] = $uid;
        }
        if ($order_id > 0) {
            $map['id'] = $order_id;
        } else {
            $twoTime = time() - 48 * 60 * 60;
            $map['cTime'] = array(
                'egt',
                $twoTime
            );
        }
        $map['pay_status'] = array(
            'neq',
            1
        );
        $map['wpid'] = empty($wpid) ? get_wpid() : $wpid;
        $map['extra'] = array(
            'neq',
            ''
        );
        $datas = $this->where(wp_where($map))
            ->field('id,uid,extra')
            ->select();
        $snDao = D('common/SnCode');
        foreach ($datas as $key => $dat) {
            $ext = $dat['extra'];
            $dd = json_decode($ext, true);
            if ($dd['score_info']['is_add'] > 0) {
                // 清空用户金币
                $credit['score'] = 0 - $dd['score_info']['score'];
                $credit['title'] = '商品订单删除，积分返回';
                $credit['uid'] = $dat['uid'];
                $cres = add_credit('auto_add', $credit, 0);
                $dd['score_info']['is_add'] = 0;
            }
            if ($dd['sn_info']['is_use'] > 0) {
                $data['is_use'] = 0;
                $data['use_time'] = 0;
                $data['can_use'] = 1;
                $res = $snDao->updateId($dd['sn_info']['sn_id'], $data);
                $dd['sn_info']['is_use'] = 0;
            }
            if ($dd['reward_info']['is_add'] > 0) {
                // 清空用户金币
                $credit['score'] = 0 - $dd['reward_info']['score'];
                $credit['title'] = '商品订单删除';
                $credit['uid'] = $dat['uid'];
                $cres = add_credit('auto_add', $credit, 0);
                if ($dd['reward_info']['sn_id'] > 0) {
                    $snDao->where(wp_where(array(
                        'id' => $dd['reward_info']['sn_id']
                    )))->delete();
                }
                $dd['reward_info']['is_add'] = 0;
            }
            $save['extra'] = json_encode($dd);
            $this->updateId($key, $save);
        }
    }

    function makeOrder($param)
    {
        $param = explode(',', $param['data']);
        $data['uid'] = session('mid_' . get_pbid());
        foreach ($param as $v) {
            $v = explode(':', $v);
            
            $goods['goods_id'] = (int) $v[0];
            $goods['spec_option_ids'] = (int) $v[1];
            $goods['num'] = (int) $v[2];
            
            $data['goods_datas'][] = $goods;
            
            D('shop/Cart')->delCart($goods);
            
            $makeorder[] = $goods;
            session('confirm_order', $makeorder);
        }
        
        $where['uid'] = $data['uid'] = session('mid_' . get_pbid());
        $where['is_use'] = 1;
        $data['address_id'] = D('shop/Address')->where(wp_where($where))->value('id');
        $data['pay_status'] = 0;
        $data['cTime'] = time();
        $data['goods_datas'] = json_encode($data['goods_datas']);
        
        $res = M('shop_order')->insertGetId($data);
        
        session('confirm_order_id', $res);
        
        return $res ? true : false;
    }

    function getOrder()
    {
        $order = session('confirm_order');
        $total = 0;
        foreach ($order as &$v) {
            $where['goods_id'] = $map['id'] = $v['goods_id'];
            $goods = D('shop/ShopGoods')->where(wp_where($map))
                ->find()
                ->toArray();
            $v['goodsPrice'] = $goods['sale_price'];
            $v['goodsTitle'] = $goods['title'];
            $v['img'] = get_cover_url($goods['cover'], 300, 300);
            if ($v['spec_option_ids']) {
                $where1['id'] = $where['spec_option_ids'] = $v['spec_option_ids'];
                $v['goodsPrice'] = M('shop_goods_sku_data')->where(wp_where($where))->value('sale_price');
                $v['goodsDesc'] = M('shop_spec_option')->where(wp_where($where1))->value('name');
            }
            $total += $v['goodsPrice'] * $v['num'];
        }
        $res['order'] = $order;
        $res['total'] = $total;
        return $res;
    }

    function orderDetail($param)
    {
        $map['id'] = $param['order_id'];
        $map['uid'] = session('mid_' . get_pbid());
        
        $order = $this->where(wp_where($map))->find();
        $order = isset($order) ? $order->toArray() : [];
        
        if (empty($order)) {
            return false;
        } else {
            $goods_data = json_decode($order['goods_datas'], true);
            $list['order_number'] = $order['order_number'];
            $list['pay_status'] = $order['pay_status'];
            $list['orderId'] = $order['id'];
            $list['address_id'] = isset($list['address_id']) ? $list['address_id'] : '';
            if ($list['address_id']) {
                $map1['id'] = $list['address_id'];
            } else {
                $map1['uid'] = session('mid_' . get_pbid());
                $map1['is_use'] = 1;
            }
            
            $address = D('shop/Address')->where(wp_where($map1))->find();
            
            $list['addr'] = isset($address) ? $address->toArray() : [];
            
            foreach ($goods_data as $vo) {
                $data['desc'] = ' ';
                $where2['goods_id'] = $where['id'] = $vo['goods_id'];
                $goods = D('shop/ShopGoods')->where(wp_where($where))->find(); // 商品数据
                $goods = isset($goods) ? $goods->toArray() : [];
                $data['goods_id'] = $vo['goods_id'];
                $data['title'] = isset($goods['title']) ? $goods['title'] : '';
                $data['price'] = isset($goods['sale_price']) ? $goods['sale_price'] : '';
                $data['img'] = get_cover_url($goods['cover'], 300, 300);
                $data['num'] = $vo['num'];
                if ($vo['spec_option_ids']) { // 规格数据
                    $ids = explode(',', $vo['spec_option_ids']);
                    foreach ($ids as $k => $vo1) {
                        $where1['id'] = $ids[$k];
                        $spec_name = M('shop_spec_option')->where(wp_where($where1))->value('name');
                        $data['desc'] .= ' ' . $spec_name;
                    }
                    $price = M('shop_goods_sku_data')->where(wp_where($where2))->value('sale_price');
                    $data['price'] = isset($price) ? $price : $data['price'];
                }
                $list['goods'][] = $data;
            }
            
            return $list;
        }
    }

    function setAddress($param)
    {
        $map['id'] = $param['orderId'];
        $res = $this->where(wp_where($map))->setField('address_id', $param['address_id']);
        
        return $res;
    }

    // 订单支付功能后
    function payOk($data)
    {
        if (substr($data['out_trade_no'], 0, 2) == 'no') {
            $map['out_trade_no'] = $data['out_trade_no'];
        } else {
            $map['order_number'] = $data['out_trade_no'];
            $ids = $this->where('order_number', $data['out_trade_no'])->column('id');
            $map['id'] = [
                'in',
                $ids
            ];
        }
        
        $lists = $this->where(wp_where($map))
            ->field('id,event_type,event_id,is_original,extra,goods_datas,uid,wpid,mail_money,total_price,order_number')
            ->select();
        
        $save['pay_status'] = 1; // 已支付
        $save['status_code'] = $code = 2; // 待发货
        $save['pay_time'] = time();
        $save['notice_erp'] = NOW_TIME; // 增加订单时通知ERP
        $save['pay_money'] = $total_fee = wp_money_format($data['total_fee'] / 100); // 实付价格
        
        $uid = 0;
        $goods_title_arr = [];
        foreach ($lists as $vo) {
            $this->updateId($vo['id'], $save);
            
            D('shop/Stock')->afterPaymentByOrder($vo);
            
            $this->setStatusCode($vo['id'], $code, true);
            if ($vo['is_original'] == 0 && $vo['event_type'] == 1) {
                D('collage/Order')->payOk($data, $vo);
            } elseif ($vo['event_type'] == 2) {
                D('seckill/Order')->payOk($data, $vo);
            } elseif ($vo['event_type'] == 3) {
                D('haggle/Order')->payOk($data, $vo);
            }
            $uid = $vo['uid'];
            $wpid = $vo['wpid'];
            $order_number = $vo['order_number'];
            
            $goods = json_decode($vo['goods_datas'], true);
            foreach ($goods as $g) {
                $goods_title_arr[] = $g['title'];
            }
        }
        $goods_title = implode(', ', $goods_title_arr);
        // 发送模板消息
        if ($uid > 0) {
            $this->sendSuccessTempMsg($uid, $wpid, $total_fee, $goods_title, $order_number);
        }
        return [
            'status' => 1
        ];
    }

    function sendSuccessTempMsg($uid, $wpid, $total_fee, $goods_title, $order_number)
    {
    	//获取公众号信息
    	$pbid = M('public_follow')->where('uid',$uid)->value('pbid');
    	
//         $shop = D('shop/Shop')->getInfoByWpid($wpid);
        $info = D('common/Publics')->getInfoById($pbid);
        if (empty($info['order_payok_messageid'])) { // 还没配置模板ID，不用发通知
            return false;
        }
        $param['data']['first']['value'] = '您的订单已支付成功';
        $param['data']['first']['color'] = "#173177";
        
        $param['data']['keyword1']['value'] = wp_money_format($total_fee);
        $param['data']['keyword1']['color'] = "#173177";
        
        $param['data']['keyword2']['value'] = $goods_title;
        $param['data']['keyword2']['color'] = "#173177";
        
        $param['data']['keyword3']['value'] = $order_number;
        $param['data']['keyword3']['color'] = "#173177";
        
        $param['data']['remark']['value'] = '欢迎再次光临！！！';
        $param['data']['remark']['color'] = "#173177";
        
//         $jumpUrl = U('shop/wap/my_order?wpid=' . $wpid);
        $jumpUrl = WAP_URL.'?pbid='.$pbid;
        addWeixinLog($param,'sendsuccesstempmsg_'.$uid);
        $rrr= D('common/TemplateMessage')->replyData($uid, $param, $info['order_payok_messageid'], $jumpUrl);
        addWeixinLog($rrr,'sendsuccesstempmsg_res_'.$info['order_payok_messageid']);
    }

    // 取消未支付的订单
    function unLock($event_order)
    {
        $id = isset($event_order['order_id']) ? $event_order['order_id'] : $event_order['id'];
        $is_pay = isset($event_order['is_pay']) ? $event_order['is_pay'] : $event_order['pay_status'];
        
        $this->where('id', $id)->update([
            'pay_status' => 3,
            'is_lock' => 0
        ]);
        $order = $this->getInfo($id, true);
        if (empty($order)) {
            return false;
        }
        
        $stockDao = D('shop/Stock');
        foreach ($order['goods'] as $goods) {
            if ($is_pay == 0) {
                $stockDao->canelUnPayOrder($goods['num'], $goods['id'], $order['event_type_db']);
            } else {
                $stockDao->canelOrder($goods['num'], $goods['id'], $order['event_type_db']);
            }
        }
        
        if ($order['event_type_db'] == COLLAGE_EVENT_TYPE) {
            D('collage/Order')->where('order_id', $order['id'])->setField('is_pay', 3);
        } elseif ($order['event_type_db'] == HAGGLE_EVENT_TYPE) {
            // D('haggle/Order')->where('id', $order['event_id'])->setField('is_pay', 3); //砍价不需要，活动期间都可以支付，作废状态由活动自行维护
        } elseif ($order['event_type_db'] == SECKILL_EVENT_TYPE) {
            D('seckill/Order')->where('order_id', $order['id'])->setField('is_pay', 3);
        }
    }

    // 取消已支付的订单
    function rebackPay($order, $refund_desc = '取消订单')
    {
        if ($order['pay_status'] != 1) {
            return false;
        }
        
        // 增加订单时通知ERP
        $this->where('id', $order['id'])->setField('notice_erp', NOW_TIME);
        
        // 执行退款,退订单实际支付的钱
//         $total_fee = ($order['total_price'] + $order['mail_money']) * 100;
        $total_fee = $order['pay_money'] * 100;
        $refund_fee = ($order['total_price'] - $order['dec_money']) * 100;
        if ($order['pay_type'] == 90) { // 退还用户积分
            $credit = [
                'uid' => $order['uid'],
                'score' => $total_fee,
                'title' => '订单退款'
            ];
            return add_credit('payment', $credit);
        } else { // 微信退款
        	//获取当前订单支付的公众号或小程序
        	$pbid = M('public_follow')->where('uid',$order['uid'])->value('pbid');
            $appid = get_pbid_appinfo($pbid, 'appid');//get_pbid_appinfo($order['wpid'], 'appid');
//             $total_fee = 1; // TODO 测试期间实际只支付1分，因此退款也只能退一分
            //单位分
//             addWeixinLog($total_fee,'rebackpaymoneydatanew');

            return D('weixin/Payment')->refund($appid, $order['out_trade_no'], $total_fee, $refund_desc,$refund_fee);
        }
    }

    function orderListByErp($order_id = 0)
    {
        $orderDao = D('shop/Order');
        
        $this->autoSetFinish();
        $this->refreshPayStatus();
        
        if (is_array($order_id)) {
            $map['id'] = [
                'in',
                $order_id
            ];
        } elseif ($order_id > 0) {
            $map['id'] = $order_id;
        } else {
            $order_number = input('order_number');
            if (! empty($order_number)) {
                $map['order_number'] = $order_number;
            } else {
                $map['update_at'] = [
                    'gt',
                    date('Y-m-d H:i:s', input('update_at/d', 0))
                ];
            }
        }
        
        $list_data = D('shop/Order')->where(wp_where($map))
            ->field('is_new,wpid,erp_lock_code,extra,order_state', true)
            ->select();
        
        $address_ids = $store_ids = $data = $gids = [];
        foreach ($list_data as $v) {
            $address_ids[$v['address_id']] = $v['address_id'];
            $store_ids[$v['stores_id']] = $v['stores_id'];
            $goods = json_decode($v['goods_datas'], true);
            foreach ($goods as $gd) {
                $gids[] = empty($gd['shop_goods_id']) ? $gd['id'] : $gd['shop_goods_id'];
            }
        }
        
        // 获取地址
        $address_lists = M('shop_address')->whereIn('id', $address_ids)
            ->field('uid,city,is_use', true)
            ->select();
        foreach ($address_lists as $vo) {
            $address_arr[$vo['id']] = $vo;
        }
        $address_empty = [
            'id' => 0,
            'truename' => '',
            'mobile' => '',
            'address' => '',
            'address_detail' => ''
        ];
        // 获取门店
        $store_lists = M('stores')->whereIn('id', $store_ids)
            ->field('id,name,address,phone')
            ->select();
        foreach ($store_lists as $vo) {
            $store_arr[$vo['id']] = $vo;
        }
        $store_empty = [
            'id' => 0,
            'name' => '',
            'address' => '',
            'phone' => ''
        ];
        
        $proArr = M('shop_goods')->whereIn('id', $gids)->column('productid', 'id');
        
        foreach ($list_data as $vo) {
            $vo['update_at'] = strtotime($vo['update_at']);
            $goods = json_decode($vo['goods_datas'], true);
            foreach ($goods as &$g) {
                $gid = empty($g['shop_goods_id']) ? $g['id'] : $g['shop_goods_id'];
                $g['productid'] = isset($proArr[$gid]) ? $proArr[$gid] : '';
                unset($g['market_price'], $g['express'], $g['shop_goods_id'], $g['id']);
            }
            $vo['goods_datas'] = $goods;
            
            $vo['address'] = isset($address_arr[$vo['address_id']]) ? $address_arr[$vo['address_id']] : $address_empty;
            $vo['stores'] = isset($store_arr[$vo['stores_id']]) ? $store_arr[$vo['stores_id']] : $store_empty;
            
            unset($vo['address_id'], $vo['stores_id']);
            $data[] = $vo;
        }
        return $data;
    }

    function addOrder($save)
    {
        $id = $this->strict(false)->insertGetId($save);
        if ($id > 0) {
            $param = json_decode($save['goods_datas'], true);
            
            $data = [];
            $data['order_id'] = $id;
            foreach ($param as $p) {
                $data['goods_id'] = $p['shop_goods_id'];
                
                M('shop_order_goods')->insert($data);
            }
        }
        return $id;
    }

    function updateOrder($id, $save)
    {
        $res = $this->strict(false)
            ->where('id', $id)
            ->update($save);
        
        return $res;
    }
}
