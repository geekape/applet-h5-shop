<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Cart extends Base
{

    protected $table = DB_PREFIX . 'shop_cart';

    function getMyCart($uid, $update = false)
    {
        $goodsDao = D('shop/ShopGoods');
        $shopDao = D('shop/Shop');
        
        $list = [];
        
        $map['uid'] = intval($uid);
        $info = $this->where(wp_where($map))->select();
        foreach ($info as &$v) {
            $v = $v->toArray();
            
            $v['goods'] = $goodsDao->getInfo($v['goods_id']);
            $v['shop'] = $shopDao->getInfo($v['wpid']);
            $v['goods_name'] = $v['goods']['title'];
            $v['shop_name'] = $v['shop']['title'];
            
            $list[] = $v;
        }
        
        return $list = isset($list) ? $list : [];
    }

    function addToCart($goods)
    {
        $goods['uid'] = $uid = intval(session('mid_'.get_pbid()));
        $res = $this->where('uid', $uid)
            ->where('goods_id', $goods['goods_id'])
            ->find();
        
        if ($res) {
            $this->where('id', $res['id'])->setInc('num', $goods['num']);
        } else {
            $goods['cTime'] = time();
            $this->insertGetId($goods);
        }
        
        return count($this->getMyCart($goods['uid'], true));
    }

    function changeCartNum($param)
    {}

    function delCart($ids)
    {
        $map['uid'] = intval(session('mid_'.get_pbid()));
        
        $map['id'] = [
            'in',
            wp_explode($ids)
        ];
        $res = $this->where(wp_where($map))->delete();
        
        return $res;
    }

    function delUserCart($uid, $goods_ids, $ids = '')
    {
        empty($ids) || $map[] = array(
            'id',
            'in',
            $ids
        );
        empty($goods_ids) || $map[] = array(
            'goods_id',
            'in',
            $goods_ids
        );
        $map[] = [
            'uid',
            '=',
            $uid
        ];
        
        $res = $this->where(wp_where($map))->delete();
        
        $this->getMyCart($uid, true);
        return $res;
    }

    function addnum($param)
    { // 购物车加数量
        $uid = intval(session('mid_'.get_pbid()));
        if ($uid) {
            $map['goods_id'] = $param['goods_id'];
            if ($param['spec_option_ids'] > 0) {
                $map['spec_option_ids'] = $param['spec_option_ids'];
            }
            $this->where(wp_where($map))->setInc('num');
        } else {
            
            $list = session('cart_list');
            
            $uniqid = $param['goods_id'] . ':' . $param['spec_option_ids'];
            
            foreach ($list as &$v) {
                
                if ($v['uniqid'] && $uniqid) {
                    $v['num'] ++;
                }
            }
            session('cart_list', $list);
        }
    }

    function subnum($param)
    { // 购物车减数量
        $uid = intval(session('mid_'.get_pbid()));
        if ($uid) {
            $map['goods_id'] = $param['goods_id'];
            $map['spec_option_ids'] = isset($param['spec_option_ids']) ? $param['spec_option_ids'] : '';
            $this->where(wp_where($map))->setDec('num');
        } else {
            $list = session('cart_list');
            $uniqid = $param['goods_id'] . ':' . $param['spec_option_ids'];
            foreach ($list as &$v) {
                if ($v['uniqid'] && $uniqid) {
                    $v['num'] --;
                }
            }
            session('cart_list', $list);
        }
    }

    function getCartCount()
    {
        $uid = intval(session('mid_'.get_pbid()));
        // dump($_SESSION);
        if ($uid) {
            $where['uid'] = $uid;
            $res = $this->where(wp_where($where))->count();
            $count = isset($res) ? $res : 0;
        } else {
            $cart = session('cart_list');
            // dump(session('cart_list'));
            $count = isset($cart) ? count($cart) : 0;
        }
        
        return $count;
    }

    function saveSession()
    {
        $list = session('cart_list');
        $uid = intval(session('mid_'.get_pbid()));
		if(!$uid) return false;
        
        $res = $this->getMyCart($uid);
        $myList = isset($res['add']) ? $res['add'] : [];
        $list = isset($list) ? $list : [];
        foreach ($list as $k => $v) {
            $unqid = $v['uniqid'];
            if (isset($myList[$unqid])) {
                $num = $myList[$unqid]['num'] + $v['num'];
                $map['id'] = $myList[$unqid]['id'];
                $res = $this->where(wp_where($map))->setField('num', $num);
            } else {
                $v['cTime'] = time();
                $v['uid'] = intval(session('mid_'.get_pbid()));
                unset($list[$k]['uniqid']);
                $res = $this->insertGetId($v);
            }
        }
        return $res;
    }
}
