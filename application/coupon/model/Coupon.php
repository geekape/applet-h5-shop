<?php

namespace app\coupon\model;

use app\common\model\Base;

/**
 * Coupon模型
 */
class Coupon extends Base
{
    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }

    function getInfo($id, $update = false, $data = [])
    {
        $key = cache_key('id:' . $id, $this->name);
        $info = S($key);
        $info = false;
        if ($info === false || $update) {
            if (empty($data)) {
                $info = $this->findById($id);
            } else {
                $info = $data;
            }
            
            S($key, $info, 86400);
        }
        if (!empty($info)){
        	$info['background_img']=empty($info['background'])?'':get_cover_url($info['background']);
        }
        
        // dump($info);exit;
        return $info;
    }

    function updateCollectCount($id, $update = false)
    {
        $key = 'Coupon_updateCollectCount_' . $id;
        $cache = S($key);
        
        $info = $this->getInfo($id);
        if (! $cache || $cache >= 100 || $update) {
            $info['collect_count'] = D('common/SnCode')->getCollectCount($id);
            
            // 更新数据库
            $this->where('id', $id)->setField("collect_count", $info['collect_count']);
            
            $cache = 1;
        } else {
            // 更新缓存
            $info['collect_count'] += 1;
            $cache += 1;
        }
        S($key, $cache, 300);
        $this->getInfo($id, true, $info);
    }

    function updateInfo($id, $save = [])
    {
        $map['id'] = $id;
        $res = $this->save($save, $map);
        if ($res) {
            $this->getInfo($id, true);
        }
        return $res;
    }

    // 通用的清缓存的方法
    function clearCache($ids, $act_type = '', $uid = 0, $more_param = [])
    {
        is_array($ids) || $ids = explode(',', $ids);
        
        foreach ($ids as $id) {
            $this->updateCollectCount($id, true);
            $this->getInfo($id, true);
        }
    }

    // 素材相关
    function getSucaiList($search = '')
    {
        $map['wpid'] = get_wpid();
        $map['uid'] = session('mid_' . get_pbid());
        empty($search) || $map['title'] = array(
            'like',
            "%$search%"
        );
        
        $page_data = $this->where(wp_where($map))
            ->field('id')
            ->order('id desc')
            ->paginate();
        $list = dealPage($page_data);
        foreach ($list['list_data'] as &$v) {
            $data = $this->getInfo($v['id']);
            $v['title'] = $data['title'];
        }
        
        return $list;
    }

    function getPackageData($id)
    {
        $info = get_pbid_appinfo();
        $param['publicid'] = $info['id'];
        $param['id'] = $id;
        $data['jumpURL'] = U("Coupon/Wap/set_sn_code", $param);
        
        $data['info'] = $this->getInfo($id);
        // 店铺地址
        $maps['coupon_id'] = $id;
        $list = M('stores_link')->where(wp_where($maps))->select();
        $wpids = getSubByKey($list, 'wpid');
        if (! empty($wpids)) {
            $shop_list = M('stores')->whereIn('id', $wpids)->select();
            $data['shop_list'] = $shop_list;
        }
        return $data;
    }

    // 赠送优惠券
    function sendCoupon($id, $uid, $openid = '')
    {
        // $param ['id'] = $id;
        $info = $this->getInfo($id);
        
        $flat = true;
        // if ($info ['collect_count'] >= $info ['num']) {
        // $flat = false;
        // }
        if (! empty($info['start_time']) && $info['start_time'] > NOW_TIME) {
            $flat = false;
        } elseif (! empty($info['end_time']) && $info['end_time'] < NOW_TIME) {
            $flat = false;
        }
        
        // $list = D('common/SnCode' )->getMyList ( $uid, $id );
        // $my_count = count ( $list );
        
        // if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
        // $flat = false;
        // }
        // 判断用户是否有领取会员卡
        $cardId = D('card/CardMember')->checkHasMemberCard($uid);
        if (empty($cardId)) {
            // $msg = '您还未领取会员卡，还不能领取该优惠券！';
            $flat = false;
        }
        if (! $flat) {
            return false;
        }
        
        $data['target_id'] = $id;
        $data['uid'] = $uid;
        $data['sn'] = uniqid();
        $data['cTime'] = NOW_TIME;
        $data['wpid'] = $info['wpid'];
        $data['openid'] = get_openid();
        // 金额
        $data['prize_title'] = $$info['money'];
        
        $sn_id = D('common/SnCode')->delayAdd($data);
        // $sn_id = D('common/SnCode' )->insertGetId( $data );
        if ($sn_id) {
            // D('common/SnCode')->clear($id, $uid);
        }
        return $sn_id;
    }

    function getSelectList()
    {
        $map['end_time'] = array(
            'gt',
            NOW_TIME
        );
        $map['wpid'] = get_wpid();
        $map['is_del'] = 0;
        $list = $this->where(wp_where($map))
            ->field('id,title')
            ->order('id desc')
            ->select();
        return $list;
    }
}
