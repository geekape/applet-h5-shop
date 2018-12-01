<?php
namespace app\shop\controller;

use app\common\controller\WapBase;

class Wap extends WapBase
{

    public $manager_id;

    public $appInfo;

    protected $apiModel;

    public function initialize()
    {
        parent::initialize();
        
        $this->apiModel = D('ApiData');
        
        $highlight = [
            'index' => '',
            'category' => '',
            'service' => '',
            'cart' => '',
            'center' => ''
        ];
        $act = strtolower(ACTION_NAME);
        if (isset($highlight[$act])) {
            session('shop_wap_footer', $act);
        } else {
            $act = session('shop_wap_footer');
            empty($act) && $act = 'index';
        }
        $highlight[$act] = 'yellow';
        $this->assign('highlight', $highlight);
        
        $shop = D('Shop')->getInfoByWpid();
        
        $pbid = get_pbid();
        $this->appInfo = get_pbid_appinfo($pbid);
        
        $this->assign('public_info', $this->appInfo);
        $shop['logo'] = isset($shop['logo']) ? get_cover_url($shop['logo'], 100, 100) : '';
        
        // 购物车统计
        $cart_count = count(D('Cart')->getMyCart($this->mid, true));
        $cart_count == 0 && $cart_count = '';
        $this->assign('cart_count', $cart_count);
        
        // 客服新消息统计
        $this->assign('new_msg_count', D('common/Chat')->getUserNewMsgCount($this->mid));
        
        $this->assign('wpid', WPID);
        if (! empty($shop['gps'])) {
            $gpsArr = wp_explode($shop['gps']);
            $shop['gps'] = $gpsArr[1] . ',' . $gpsArr['0'];
        }
        $this->assign('shop', $shop);
        if (empty($shop['intro'])) {
            $shareDesc = $shop['title'];
        } else {
            $shareDesc = filter_line_tab($shop['intro']);
        }
        $this->assign('share_uid', $this->manager_id);
        $this->assign('shop_share', $shareDesc);
        $paymentConfig = get_info_config('Payment');
        $this->assign('payment_config', $paymentConfig);
        
        // 自定义页面
        if (ACTION_NAME == 'index') {
            $diy_id = M('shop_page')->where('wpid', WPID)
                ->where('is_index', 1)
                ->value('id');
            if ($diy_id > 0) {
                return $this->redirect(U('diy_page?id=' . $diy_id));
            }
        }
    }

    private function _show_subscribe()
    {
        $map1['wpid'] = get_wpid();
		$map['pbid'] = get_pbid();
        $map['uid'] = $this->mid;
        $has_subscribe = intval(M('public_follow')->where(wp_where($map))->value('has_subscribe'));
        if ($has_subscribe) {
            $duserinfo = D('Shop/Distribution')->getDistributionUser($this->manager_id);
            if (! empty($duserinfo) && $duserinfo['level'] > 0 && $duserinfo['is_audit'] == 1) {
                $map1['openid'] = get_openid();
                $res1 = M('shop_statistics_follow')->where(wp_where($map1))->value('id');
                if (! $res1) {
                    $has_subscribe = 0;
                }
            }
        }
        $this->assign('has_subscribe', $has_subscribe);
    }

    public function diy_page()
    {
        $id = I('id');
        $data = D('DiyPage')->getInfo($id, true);
        $wpid = get_wpid();
        $key = 'diypage_is_index_' . $wpid;
        if ($data['is_index']) {
            S($key, $id);
        } else {
            S($key, null);
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    // 多评价
    public function more_comment()
    {
        return $this->fetch();
    }
}
