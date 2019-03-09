<?php

namespace app\coupon\model;

use app\common\model\Base;

/**
 * Coupon模型
 */
class ApiData extends Base
{
    
    // 开始领取页面
    function prev()
    {
        $data = $this->_detail();
        $target_id = I('id/d', 0);
        $list = D('common/SnCode')->getMyList($this->mid, $target_id);
        $sn_id = input('sn_id/d', 0);
        if (empty($sn_id)) {
            foreach ($list as $v) {
                if ($v ['id'] == $sn_id) {
                    $res = $v;
                }
            }
            $list = array (
                    $res
            );
        }
        
        $data ['my_sn_list'] = $list;
        
        $tpl = isset($_GET ['has_get']) ? 'has_get' : 'prev';
        
        $info = get_followinfo($this->mid);
        $config = getAddonConfig('UserCenter');
        if ($config ['need_bind'] && (empty($info ['mobile']) || empty($info ['truename']))) {
            $forward = cookie('__forward__');
            empty($forward) && cookie('__forward__', $_SERVER ['REQUEST_URI']);
            $url = U('weixin/Wap/bind_prize_info');
        } else {
            $url = U('set_sn_code', array (
                    'id' => $data ['id']
            ));
        }
        
        $data ['url'] = $url;
        $data ['templateFile'] = $tpl;
        
        return $data;
    }
    function qr_code()
    {
        $id = I('sn_id');
        $map2 ['uid'] = $this->mid;
        
        $info = D('common/SnCode')->getInfoById($id);
        if ($info ['uid'] != $this->mid) {
            $this->error('非法访问');
        }
        
        $data ['info'] = $info;
        // dump ( $info );
        
        return $data;
    }
    function do_pay()
    {
        $cTime = I('cTime/d', 0);
        if ($cTime > 0 && (NOW_TIME * 1000 - $cTime) > 30000) {
            return $this->error('二维码已过期');
        }
        
        $id = I('sn_id');
        $info = D('common/SnCode')->getInfoById($id);
        if (empty($info)) {
            return $this->error('扫描的二维码不对');
        }
        
        $data ['info'] = $info;
        $coupon = D('Coupon')->getInfo($info ['target_id']);
        $data ['coupon'] = $coupon;
        
        $check = D('servicer/Servicer')->checkRule($this->mid, 2);
        if (! $check) {
            return $this->error('你需要工作授权才能核销');
        }
        
        return $data;
    }
    function do_pay_ok()
    {
        $msg = '';
        $dao = D('common/SnCode');
        
        $id = I('sn_id');
        $data ['info'] = $info = $dao->getInfoById($id);
        $coupon = D('Coupon')->getInfo($info ['target_id']);
        
        $check = D('servicer/Servicer')->checkRule($this->mid, 2);
        if (! $check) {
            $msg = '你需要工作授权才能核销';
        }
        
        if (empty($msg)) {
            if ($info ['is_use']) {
                $msg = '该券已经使用过，请不要重复使用';
            }
        }
        
        if (empty($msg)) {
            $info ['is_use'] = $save ['is_use'] = 1;
            $save ['can_use'] = 0;
            $info ['use_time'] = $save ['use_time'] = time();
            $save ['admin_uid'] = $this->mid;
            
            $res = $dao->updateInfo($id, $save);
            
            unset($map);
            $map ['is_use'] = 1;
            $map ['target_id'] = $info ['target_id'];
            $data ['use_count'] = $save2 ['use_count'] = intval($dao->where(wp_where($map))->count());
            
            D('Coupon')->updateInfo($info ['target_id'], $save2);
            
            $msg = '核销成功';
        }
        
        $data ['msg'] = $msg;
        $data ['coupon'] = $coupon;
        
        return $data;
    }
    
    // 过期提示页面
    function over()
    {
        $data = $this->_detail();
        return $data;
    }
    function show_error($error, $info = [])
    {
        $data ['info'] = $info;
        $data ['code']=0;
        $data ['error'] = $error;
        S('set_sn_code_lock', 0); // 解锁
        $data ['templateFile'] = 'over';
        
        return $data;
    }
    function show()
    {
        // dump ( $this->mid );
        $id = I('id/d', 0);
        
        $sn_id = I('sn_id/d', 0);
        
        $list = D('common/SnCode')->getMyList($this->mid, $id);
        $my_count = 0;
        $sn = [ ];
        if ($sn_id > 0) {
            foreach ($list as $vo) {
                $my_count += 1;
                $vo ['id'] == $sn_id && $sn = $vo;
            }
        } else {
            $sn = $list [0];
        }
        /*
         * if (empty ( $sn )) {
         * $param ['source'] = 'Coupon';
         * $param ['id'] = $id;
         * return redirect ( U ( 'Sucai/Sucai/show', $param ) );
         *
         * // $this->error ( '非法访问' );
         * exit ();
         * }
         */
        $maps ['coupon_id'] = $id;
        $list = M('stores_link')->where(wp_where($maps))->select();
        $wpids = getSubByKey($list, 'wpid');
        if (! empty($wpids)) {
            $shop_list = M('stores')->whereIn('id', $wpids)->select();
            $data ['shop_list'] = $shop_list;
        }
        if (empty($sn)) {
            $sn ['id'] = 0;
        }
        $data ['sn'] = $sn;
        // dump($sn);
        
        $data ['data'] = $this->_detail($my_count);
        $data ['templateFile'] = 'show';
        
        return $data;
    }
    function _detail($my_count = false)
    {
        $id = I('id/d', 0);
        $data = D('Coupon')->getInfo($id);
        $data ['data'] = $data;
        // dump ( $data );
        
        // 领取条件提示
        $follower_condtion [1] = '关注后才能领取';
        $follower_condtion [2] = '用户绑定后才能领取';
        $follower_condtion [3] = '领取会员卡后才能领取';
        $tips = condition_tips($data ['addon_condition']);
        
        $condition = [ ];
        $data ['max_num'] > 0 && $condition [] = '每人最多可领取' . $data ['max_num'] . '张';
        $data ['credit_conditon'] > 0 && $condition [] = '积分中金币值达到' . $data ['credit_conditon'] . '分才能领取';
        $data ['credit_bug'] > 0 && $condition [] = '领取后需扣除金币值' . $data ['credit_bug'] . '分';
        isset($follower_condtion [$data ['follower_condtion']]) && $condition [] = $follower_condtion [$data ['follower_condtion']];
        empty($tips) || $condition [] = $tips;
        
        $data ['condition'] = $condition;
        // dump ( $condition );
        
        $data ['error'] = $this->_get_error($data, $my_count);
        
        return $data;
    }
    function _get_error($data, $my_count = false)
    {
        $error = '';
        
        // 抽奖记录
        $my_count === false && $my_count = count(D('common/SnCode')->getMyList($this->mid, $data ['id']));
        
        // 权限判断
        $follow = get_followinfo($this->mid);
        // $is_admin = is_login ();
        
        if (! empty($data ['end_time']) && $data ['end_time'] <= NOW_TIME) {
            $error = '您来晚啦';
        } elseif ($data ['max_num'] > 0 && $data ['max_num'] <= $my_count) {
            $error = '您的领取名额已用完啦';
        }
        
        // if ($data ['follower_condtion'] > intval ( $follow ['status'] ) && ! $is_admin) {
        // switch ($data ['follower_condtion']) {
        // case 1 :
        // $error = '关注后才能领取';
        // break;
        // case 2 :
        // $error = '用户绑定后才能领取';
        // break;
        // case 3 :
        // $error = '领取会员卡后才能领取';
        // break;
        // }
        // } else if ($data ['credit_conditon'] > intval ( $follow ['score'] ) && ! $is_admin) {
        // $error = '您的金币值不足';
        // } else if ($data ['credit_bug'] > intval ( $follow ['score'] ) && ! $is_admin) {
        // $error = '您的金币值不够扣除';
        // } else if (! empty ( $data ['addon_condition'] )) {
        // addon_condition_check ( $data ['addon_condition'] ) || $error = '权限不足';
        // }
        // dump ( $error );
        
        return $error;
    }
    
    // 记录中奖数据到数据库
    function set_sn_code()
    {
        $id = $param ['id'] = I('id/d', 0);
        
        $lock = S('set_sn_code_lock');
        if ($lock == 1 || isset($_GET ['format'])) {
            $param ['publicid'] = I('publicid');
            $param ['rand'] = NOW_TIME . rand(10, 99);
            
//             return $this->error('排队领取中', U('set_sn_code', $param));

            return $this->show_error('排队领取中',[]);
        } else {
            S('set_sn_code_lock', 1, 30);
        }
        
        $follow = get_followinfo($this->mid);
        $config = getAddonConfig('UserCenter');
        // S ( 'set_sn_code_lock', 0 );
        // exit ();
        if ($config) {
            if ($config ['need_bind'] && ! (defined('IN_WEIXIN') && IN_WEIXIN) && ! isset($_GET ['is_stree']) && $this->mid != 1 && (empty($follow ['mobile']) || empty($follow ['truename']))) {
                $forward = cookie('__forward__');
                empty($forward) && cookie('__forward__', $_SERVER ['REQUEST_URI']);
                S('set_sn_code_lock', 0); // 解锁
//                 return redirect(U('weixin/Wap/bind_prize_info'));
            }
        }
        $info = D('Coupon')->getInfo($id);
        $member = explode(',', $info ['member']);
        if (! in_array(0, $member) && is_install('card')) {
            // 判断是否为会员
            $card_map ['wpid'] = get_wpid();
            $card_map ['uid'] = $this->mid;
            $card = M('card_member')->where(wp_where($card_map))->find();
            if (! $card || ($member != 0 && ! in_array(- 1, $member) && ! in_array($card ['level'], $member))) {
                $msg = '您的等级未满足，还不能领取该优惠券！';
                return $this->show_error($msg, $info);
            }
        }
        // if ($info['collect_count'] >= $info['num']) {
        // $msg = empty($info['empty_prize_tips']) ? '您来晚了，优惠券已经领取完' : $info['empty_prize_tips'];
        // return $this->show_error($msg, $info);
        // }
        
        if (! empty($info ['start_time']) && $info ['start_time'] > NOW_TIME) {
            $msg = empty($info ['start_tips']) ? '活动在' . time_format($info ['start_time']) . '开始，请到时再来' : $info ['start_tips'];
            return $this->show_error($msg, $info);
        }
        if (! empty($info ['end_time']) && $info ['end_time'] < NOW_TIME) {
            $msg = empty($info ['end_tips']) ? '您来晚了，活动已经结束' : $info ['end_tips'];
            return $this->show_error($msg, $info);
        }
        
        $list = D('common/SnCode')->getMyList($this->mid, $id);
        $data ['my_sn_list'] = $list;
        $my_count = count($list);
        $error = $this->_get_error($info, $my_count);
        if (! empty($error)) {
            S('set_sn_code_lock', 0); // 解锁
            $data ['error'] = $error;
            $data ['code'] =0;
            $data ['templateFile'] = 'over';
            return $data;
        }
        
        // 判断用户是否有领取会员卡
       /*  $cardId = D('card/CardMember')->checkHasMemberCard($this->mid);
        if (empty($cardId)) {
            $msg = '您还未领取会员卡，还不能领取该优惠券！';
            $info ['need_card'] = 1;
            return $this->show_error($msg, $info);
        } */
        
        $data ['target_id'] = $id;
        $data ['uid'] = $this->mid;
        $data ['sn'] = uniqid();
        $data ['cTime'] = NOW_TIME;
        $data ['wpid'] = $info ['wpid'];
        $data ['openid'] = empty($param ['openid'])?get_openid():$param ['openid'];
        // 金额
        $data ['prize_title'] = $info ['money'];
        
        $sn_id = D('common/SnCode')->delayAdd($data);
        S('set_sn_code_lock', 0); // 解锁
                                  
        // 扣除积分
        if (! empty($info ['credit_bug'])) {
            $credit ['score'] = $info ['credit_bug'];
            add_credit('coupon_credit_bug', $credit, 5);
        }
        if (isset($_GET ['is_stree'])) {
        	$data ['error'] = '请在微信打开';
        	$data ['code'] =0;
        	return $data;
//             return false;
        }
        
        unset($param);
        $param ['id'] = $id;
        $param ['sn_id'] = $sn_id;
        return $this->success('',U('show', $param));
    }
    function coupon_detail()
    {
        // dump ( get_openid () );
        // dump ( get_wpid () );
        // dump ( $this->mid );
        $id = $param ['id'] = I('id/d', 0);
        $info = D('Coupon')->getInfo($id);
        $data ['info'] = $info;
        return $data;
    }
    function store_list()
    {
        $id = $param ['id'] = I('id/d', 0);
        $maps ['coupon_id'] = $id;
        $list = M('stores_link')->where(wp_where($maps))->select();
        $wpids = getSubByKey($list, 'wpid');
        if (! empty($wpids)) {
            $shop_list = M('stores')->whereIn('id', $wpids)->select();
            foreach ($shop_list as &$s) {
                $gpsArr = wp_explode($s ['gps']);
                $s ['gps'] = $gpsArr [1] . ',' . $gpsArr ['0'];
            }
            $data ['shop_list'] = $shop_list;
        }
        return $data;
    }
    function get_sn_status()
    {
        $id = I('sn_id/d', 0);
        $is_use = D('common/SnCode')->getInfoById($id, 'is_use');
        return $is_use;
    }
    function index()
    {
        $param ['id'] = $id = I('id');
        $uid = intval($this->mid);
        // 已领取的直接进入详情页面，不需要再领取（TODO：仅为不需要多次领取的客户使用）
        $log['input']=input();
        $log['session']=$_SESSION;
        addWeixinLog($log,'coupon_data_'.$uid.'_'.session_id());
        $info = D('Coupon')->getInfo($id);
        $mylist = D('common/SnCode')->getMyList($uid, $id);
        if (! empty($mylist [0]) ) {
        	if ( $mylist[0]['is_use']==0 || count($mylist) >= $info['max_num'] && $info['max_num'] != 0){
        		//已经领取且未使用的，或没有领取次数的
        		$param ['sn_id'] = $mylist [0] ['id'];
        		return $this->success('',U('show', $param));
        	}
        }
        $public_info = get_pbid_appinfo();
        
        $url = U("set_sn_code", $param);
        $data ['jumpURL'] = $url;
        
        $maps ['coupon_id'] = $id;
        $list = M('stores_link')->where(wp_where($maps))->select();
        $wpids = getSubByKey($list, 'wpid');
        if (! empty($wpids)) {
            $shop_list = M('stores')->whereIn('id', $wpids)->select();
            $data ['shop_list'] = $shop_list;
        }
        
       
        
        $data ['info'] = $info;
        $data ['public_info'] = $public_info;
        
        return $data;
    }
    function personal()
    {
        $cStr = I('str_coupon_id');
        $cIdArr = wp_explode($cStr, ',');
        // 用于商城使用
        $isSelect = input('from') == 'select' ? 1 : 0;
        if ($isSelect) {
            $list = M('sn_code')->where('uid', $this->mid)->where('can_use', 1)->where('is_use', 0)->where('wpid', get_wpid())->where('prize_title', 'neq', '')->whereNotNull('prize_title')->order('id desc')->select();
        } else {
            $list = D('common/SnCode')->getMyAll($this->mid, true);
        }
        
        $sDao = D('coupon/Coupon');
        $data = [ ];
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                $coupon = $sDao->getInfo($v ['target_id']);
                if (isset($coupon ['end_time'])) {
                    if ($cIdArr && ! in_array($v ['target_id'], $cIdArr)) {
                        continue;
                    }
                    if ($coupon ['end_time'] <= NOW_TIME) {
                        $v ['can_use'] = 0;
                        $v ['is_old'] = 1;
                    }
                    // 即将过期,距离结束3天前提示 3*24*3600 = 259200
                    if ($coupon ['end_time'] - NOW_TIME < 259200) {
                        $v ['near_end'] = 1;
                    } else {
                        $v ['near_end'] = 0;
                    }
                    $v ['sn_id'] = $v ['id'];
                    $v = array_merge($v, $coupon);
                    $v ['start_time'] = day_format($v ['start_time']);
                    $v ['end_time'] = day_format($v ['end_time']);
                    $data ['lists'] [$v ['is_use']] [] = $v;
                } else {
                    unset($list [$k]);
                }
            }
        }
        
        $data ['is_select'] = $isSelect;
        
        return $data;
    }
    function sn()
    {
        $map ['wpid'] = get_wpid();
        $map ['target_id'] = I('coupon_id');
        
        $key = I('search');
        if (! empty($key)) {
            $map ['sn'] = array (
                    'like',
                    '%' . $key . '%'
            );
        }
        $is_use = I('is_use');
        if ($is_use == 1) {
            $map ['is_use'] = $is_use;
        }
        
        $page_data = M('sn_code')->where(wp_where($map))->paginate();
        $list = dealPage($page_data);
        // dump($list);
        $this->assign($list);
        $data ['is_use'] = $map ['is_use'];
        
        return $data;
    }
    function sn_set()
    {
        $map ['id'] = I('id');
        $map ['wpid'] = get_wpid();
        $data = M('sn_code')->where(wp_where($map))->find();
        if (! $data) {
            $this->error('数据不存在');
        }
        
        if ($data ['is_use']) {
            $data ['is_use'] = 0;
            $data ['use_time'] = '';
        } else {
            $data ['is_use'] = 1;
            $data ['use_time'] = time();
            $data ['admin_uid'] = $this->mid;
        }
        
        $res = M('sn_code')->where(wp_where($map))->update($data);
        if ($res !== false) {
                $map2 ['target_id'] = $maps ['id'] = $data ['target_id'];
                
                $info = M('sn_code')->where(wp_where($map2))->field('sum( is_use ) as use_count,count(id) as num')->find();
                
                $save ['use_count'] = $info ['use_count'];
                $save ['collect_count'] = $info ['num'];
                M('coupon')->where(wp_where($maps))->update($save);
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }
    function lists()
    {
        // 更新延时插入的缓存
        D('common/SnCode')->delayAdd();
        
        $dao = D('Coupon');
        $order = 'id desc';
        $model = $this->getModel();
        // $map['is_show']=1;
        $map ['is_del'] = 0;
        session('common_condition', $map);
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        
        // 搜索条件
        $map = $this->_search_map($model, $list_data ['db_fields']);
        $row = empty($model ['list_row']) ? 20 : $model ['list_row'];
        
        $map ['end_time'] = array (
                'gt',
                NOW_TIME
        );
        // 获取用户的会员等级
        if (is_install('card')){
        	$levelInfo = D('Card/CardLevel')->getCardMemberLevel($this->mid);
        }else {
        	//不被会员等级限制
        	$levelInfo[0]=1;
        }
       
        // 读取模型数据列表
        // dump($map);
        $data = $dao->field('id,member')->where(wp_where($map))->order($order)->paginate($row);
        $list_data = $this->parsePageData($data, $model, $list_data);
        // lastsql();
        foreach ($list_data ['list_data'] as $d) {
            $levelArr = isset($d ['member_db']) ? explode(',', $d ['member_db']) : explode(',', $d ['member']);
            if (in_array(0, $levelArr) || in_array(- 1, $levelArr) || in_array($levelInfo ['id'], $levelArr)) {
                $datas [] = $dao->getInfo($d ['id']);
            }
        }
        
        $list_data ['list_data'] = $datas;
        
        return $list_data;
    }
}
