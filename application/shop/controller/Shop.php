<?php

namespace app\shop\controller;

use app\shop\controller\Base;

class Shop extends Base
{
    public $model;

    public function initialize()
    {
        parent::initialize();
        $this->model = $this->getModel('shop');
    }
    public function lists()
    {
        return redirect(U('summary', $this->get_param));
    }

    public function edit()
    {
        $id = WPID;
        $model = $this->model;

        if (request()->isPost()) {
            $map ['wpid'] = WPID;
            $Model = D($model ['name']);
            if (input('post.mobile')) {
                $this->isTel(input('post.mobile'));
            }
            // if (input('post.api_key')){
            // $this->isUrl(input('post.api_key'));
            // }
            $data = I('post.');
            $data = $this->checkData($data, $this->model);

            $Model->isUpdate(true)->save($data);

            // 清空缓存
            method_exists($Model, 'clearCache') && $Model->clearCache($data ['id'], 'edit');
            $this->success('保存' . $model ['title'] . '成功！');
        } else {
            $fields = get_model_attribute($model);

            // 获取数据
            $data = D('Shop')->getInfo($id);
            if (empty($data)) {
                return redirect(U('add'));
            }

            $this->assign('fields', $fields);
            $this->assign('data', $data);

            return $this->fetch();
        }
    }

    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $Model = D($this->model ['name']);
            $data = $this->checkData($data, $this->model);
            $data ['id'] = WPID; // 简化操作，一个公众号对应一个商城，wpid直接等于wpid
            $id = $Model->insertGetId($data);
            if ($id) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');

                $this->success('添加' . $this->model ['title'] . '成功！', U('lists?model=' . $this->model ['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($this->model);
            $this->assign('fields', $fields);
            // dump($fields);

            $data = [];
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    protected function top()
    {
        // $normal_tips = '若出现“redirect_uri 参数错误”,请检查微信公众平台里的“网页授权获取用户基本信息”是否配置好“授权回调页面域名”';
        // $this->assign('normal_tips', $normal_tips);
        $info = D('Shop')->getInfo(WPID);
        $this->assign('info', $info);

        $map ['wpid'] = WPID;
        $map ['is_delete'] = array(
            'eq',
            0
        );
        $goodsDao = D('ShopGoods');
        $idsArr = $goodsDao->where(wp_where($map))->column('id');

        $count ['sale_count'] = 0;
        $count ['wait_count'] = 0;
        $count ['total_count'] = 0;

        foreach ($idsArr as $id) {
            $goodsInfo = $goodsDao->getInfo($id);
            // dump($goodsInfo);
            if ($goodsInfo ['is_show'] == 1 && $goodsInfo ['stock'] > 0) {
                $count ['sale_count']++;
            } elseif ($goodsInfo ['is_show'] == 2 && $goodsInfo ['stock'] > 0) {
                $count ['wait_count']++;
            }
            $count ['total_count']++;
        }

        $count ['down_count'] = intval($count ['total_count'] - $count ['sale_count'] - $count ['wait_count']);
        $this->assign('count', $count);
        // dump ( $count );
        unset($map ['is_delete']);
        $order = M('shop_order')->where(wp_where($map))->field('sum( is_new ) as new_count, count(1) as total_count')->find();
        $this->assign('order', $order);
    }

    public function summary()
    {
        $publicid = get_pbid();
        $time = NOW_TIME - 86400 * 30;
        $px = DB_PREFIX;

        $sql = "SELECT count(1) as cc,FROM_UNIXTIME(subscribe_time, '%m%d') as hh FROM `{$px}user` as u left join `{$px}public_follow` as f on u.uid = f.uid WHERE f.pbid='{$publicid}' and u.subscribe_time>'$time' GROUP BY hh";
        $list = M()->query($sql);
        $log_data = [];
        $list = !empty($list) ? $list : [];
        foreach ($list as $vo) {
            $log_data [$vo ['hh']] = $vo ['cc'];
        }

        for ($i = 30; $i >= 0; $i--) {
            $t = NOW_TIME - $i * 86400;
            $hh = date('md', $t);
            $highcharts ['xAxis'] [] = ( string )$hh;
            $highcharts ['series'] [] = isset($log_data [$hh]) ? intval($log_data [$hh]) : 0;
        }

        $highcharts ['xAxis'] = "'" . implode("','", $highcharts ['xAxis']) . "'";
        $highcharts ['series'] = implode(',', $highcharts ['series']);
        $this->assign('highcharts', $highcharts);

        $this->top();
        $this->assign('series', '粉丝增加数量');

        $count = M('public_follow')->where('pbid', $publicid)->field('count(1) as total, sum(has_subscribe) as has_count')->find();
        $count ['un_count'] = $count ['total'] - $count ['has_count'];
        $this->assign('follow_count', $count);

        return $this->fetch();
    }

    public function order_count()
    {
        $publicid = get_pbid();
        $time = NOW_TIME - 86400;
        $px = DB_PREFIX;

        $sql = "SELECT count(1) as cc,FROM_UNIXTIME(cTime, '%H') as hh FROM `{$px}shop_order` WHERE  cTime>'$time' AND wpid='{$publicid}' GROUP BY hh";
        $list = M()->query($sql);
        $list = !empty($list) ? $list : [];
        $order_data = [];
        foreach ($list as $vo) {
            $order_data [$vo ['hh']] = $vo ['cc'];
        }
        for ($i = 23; $i >= 0; $i--) {
            $hh = date('H', NOW_TIME - $i * 3600);
            $highcharts ['xAxis'] [] = $hh;
            $highcharts ['series'] [] = isset($order_data [$hh]) ? intval($order_data [$hh]) : 0;
        }

        $highcharts ['xAxis'] = implode(',', $highcharts ['xAxis']);
        $highcharts ['series'] = implode(',', $highcharts ['series']);
        $this->assign('highcharts', $highcharts);

        $this->top();
        $this->assign('series', '24小时订单数量');
        return $this->fetch('visit_count');
    }

    public function preview()
    {
        $pbid = get_pbid();
        $info = get_pbid_appinfo($pbid);
        $this->assign('public_info', $info);
        $previewUrl = WAP_URL . '?pbid=' . $pbid . '&uid=' . $this->mid;
        $this->assign('url', $previewUrl);
        return $this->fetch('common@base/preview');
    }

    public function _check_get_shop()
    {
        $config = get_info_config('Shop');
        // 开启分销制度
        if ($config ['need_distribution'] == 1) {
            if ($config ['set_require'] == 1) {
                // 分销条件：商品数：count 总金额：money 积分数：score
                foreach ($config ['add_conditon'] as $cc) {
                    $map1 [$cc] = $config [$cc . '_value'];
                }
                // 消费总金额
                $data = D('Shop/Order')->getTotalData($this->mid);
                $userScore = get_userinfo($this->mid, 'score');
                $count_reach = 1;
                $money_reach = 1;
                $score_reach = 1;

                $isAllGoods = $config ['is_all_goods'];
                $idsAndNum = $data ['goods_id_num'];
                if ($isAllGoods == 0 && $map1 ['count']) {
                    $data ['goods_count'] >= $map1 ['count'] ? $count_reach = 1 : $count_reach = 0;
                } elseif ($isAllGoods && in_array('count', $config ['add_conditon'])) {
                    $goodsIds = wp_explode($config ['buy_num'], ',');
                    foreach ($goodsIds as $vo) {
                        $goods = wp_explode($vo, ':');
                        if ($idsAndNum [$goods [0]] < $goods [1]) {
                            $count_reach = 0;
                            break;
                        }
                    }
                }

                if ($map1 ['money']) {
                    $data ['total_money'] >= $map1 ['money'] ? $money_reach = 1 : $money_reach = 0;
                }
                if ($map1 ['score']) {
                    $userScore >= $map1 ['score'] ? $score_reach = 1 : $score_reach = 0;
                }
                if ($count_reach && $money_reach && $score_reach) {
                    // 满足条件
                    return 1;
                }
            } else {
                // 无条件，后台手动添加
                return 0;
            }
        }
        return 0;
    }

    public function user_account()
    {
        $uid = $this->mid;
        $map ['wpid'] = get_wpid();
        $config = get_info_config('Shop');
        if ($config ['need_distribution']) {
            $shopInfo = D('Shop/Shop')->where(wp_where(array(
                'manager_id' => $uid
            )))->find();
            $pArr = wp_explode($shopInfo ['parent_shop']);
            // 分销用户级别
            $count = count($pArr);
            $level = $config ['level'];
            if ($count) {
                // 总提现金额
                $map ['profit_shop'] = $shopInfo ['id'];
                $totalProfit = M('shop_distribution_profit')->where(wp_where($map))->value('sum( profit )');
                $totalProfit = round($totalProfit, 4);

                // 已提现金额
                $map1 ['uid'] = $this->mid;
                $map1 ['wpid'] = get_wpid();
                $map1 ['cashout_status'] = 1;
                $hasProfit = M('shop_cashout_log')->where(wp_where($map1))->value('sum( cashout_amount )');
                $hasProfit = round($hasProfit, 4);
                $this->assign('has_profit', $hasProfit);

                // 待结算金额
                $map2 ['uid'] = $this->mid;
                $map2 ['wpid'] = get_wpid();
                $map2 ['cashout_status'] = 0;
                $waitProfit = M('shop_cashout_log')->where(wp_where($map2))->value('sum( cashout_amount )');
                $waitProfit = round($waitProfit, 4);
                $this->assign('wait_profit', $waitProfit);

                // 可提现金额
                $canProfit = $totalProfit - $hasProfit - $waitProfit;
                $this->assign('can_profit', $canProfit);

                // 账号
                $map3 ['uid'] = $this->mid;
                $map3 ['wpid'] = get_wpid();
                $account = M('shop_cashout_account')->where(wp_where($map3))->find();
                $this->assign('cashout_account', $account);

                // 提成设定
                if ($level == 1) {
                    $this->assign('level1', $config ['level1']);
                } elseif ($level == 2) {
                    $this->assign('level1', $config ['level1']);
                    $this->assign('level2', $config ['level2']);
                } else {
                    $this->assign('level1', $config ['level1']);
                    $this->assign('level2', $config ['level2']);
                    $this->assign('level3', $config ['level3']);
                }
            }
        }
        return $this->fetch();
    }

    // 编辑提现帐号
    public function set_account()
    {
        $data ['account'] = I('account');
        $data ['name'] = I('name');
        $data ['type'] = I('type');
        $map ['uid'] = $this->mid;
        $map ['wpid'] = get_wpid();
        $info = M('shop_cashout_account')->where(wp_where($map))->find();
        $res = 0;
        if ($info) {
            $res = M('shop_cashout_account')->where(wp_where($map))->update($data);
        } else {
            $data ['uid'] = $this->mid;
            $data ['wpid'] = get_wpid();
            $res = M('shop_cashout_account')->insert($data);
        }
        echo $res;
    }

    // 提现记录列表
    public function cashout_lists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);

        $cashoutStatus = I('cashout_status');
        if ($cashoutStatus) {
            $cashoutStatus == 1 && $map ['cashout_status'] = 1;
            $cashoutStatus == 2 && $map ['cashout_status'] = 2;
            $cashoutStatus == 3 && $map ['cashout_status'] = 0;
        }
        $map ['uid'] = $this->mid;
        $map ['wpid'] = get_wpid();
        session('common_condition', $map);

        $model = $this->getModel('shop_cashout_log');
        $accont_model = $this->getModel('shop_cashout_account');
        $list_data = $this->_get_model_list($model);
        foreach ($list_data ['list_data'] as &$vo) {
            $cashoutAccount = json_decode($vo ['cashout_account'], true);
            $vo ['type'] = $cashoutAccount ['type'];
            $vo ['cashout_account'] = $cashoutAccount ['account'];
            $vo ['name'] = $cashoutAccount ['name'];
        }
        $this->assign($list_data);
        return $this->fetch();
    }

    // 添加提现记录
    public function cashout_add()
    {
        $map ['uid'] = $this->mid;
        $map ['wpid'] = get_wpid();
        $info = M('shop_cashout_account')->where(wp_where($map))->find();
        $data ['cashout_account'] = json_encode($info);

        $data ['cashout_amount'] = I('cashout_amount');
        $data ['remark'] = I('remark');
        $data ['ctime'] = NOW_TIME;
        $data ['uid'] = $this->mid;
        $data ['wpid'] = get_wpid();

        $res = M('shop_cashout_log')->insert($data);
        echo intval($res);
    }

    // 获取优惠券列表
    public function get_card_coupon()
    {
        $type = I('type');
        $map ['end_time'] = array(
            'gt',
            NOW_TIME
        );
        $map ['wpid'] = get_wpid();
        $list = M('coupon')->where(wp_where($map))->field('id,title')->order('id desc')->select();
        $this->ajaxReturn($list);
    }

    public function isTel($tel, $type = '')
    {
        $regxArr = array(
            'sj' => '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',
            'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            '400' => '/^400(-\d{3,4}){2}$/'
        );
        if ($type && isset($regxArr [$type])) {
            return preg_match($regxArr [$type], $tel) ? true : false;
        }
        foreach ($regxArr as $regx) {
            if (preg_match($regx, $tel)) {
                return true;
            }
        }
        $this->error('联系电话错误');
    }

    public function isUrl($url)
    {
        $regex = '/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/';
        $res = preg_match($regex, $url);
        if ($res) {
            return true;
        }
        $this->error('无效APPKEY');
    }
}
