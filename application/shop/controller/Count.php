<?php

namespace app\shop\controller;

use app\shop\controller\Base;

class Count extends Base
{

    public function initialize()
    {
        parent::initialize();

        $param['mdm'] = input('mdm');

        $type = I('type/d', 0);

        $param['type'] = 0;
        $res['title'] = '流量分析';
        $res['url'] = U('Shop/Count/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 0 ? 'current' : '';
        $nav[] = $res;

        /*
         * $param ['type'] = 1;
         * $res ['title'] = '订单统计';
         * $res ['url'] = U ( 'Shop/Count/order_count', $param );
         * $res ['class'] = ACTION_NAME == 'order_count' && $type == 1 ? 'current' : '';
         * $nav [] = $res;
         */
//         $param['type'] = 2;
//         $res['title'] = '积分统计';
//         $res['url'] = U('Shop/Count/score_count', $param);
//         $res['class'] = ACTION_NAME == 'score_count' && $type == 2 ? 'current' : '';
//         $nav[] = $res;

        $this->assign('nav', $nav);

        $this->field_type = array(
            'string' => 'varchar',
            'textarea' => 'varchar',
            'radio' => 'varchar',
            'checkbox' => 'varchar',
            'select' => 'varchar',
            'picture' => 'int',
            'datetime' => 'int'
        );
        $this->assign('now_day', date('Y-m-d'));
    }

    // 会员门店更新
    public function _update_store()
    {
        $map['wpid'] = get_wpid();
        $member = M('card_member')->where($map)->column('phone', 'id');
        $sapDao = D('Card/Sap');
        foreach ($member as $key => $vo) {
            $sapMember = $sapDao->checkMemberByMobile($vo);
            $save['store_name'] = $sapMember['shpName'];
            $save['shop_code'] = $sapMember['shpCode'];
            M('card_member')->where('id', $key)->update($save);
        }
    }

    public function order_count()
    {
        $storeLists = D('Card/Sap')->getStore(true);
        $this->assign('store_lists', $storeLists);
        // $this->_update_store();
        return $this->fetch();
    }

    // 订单门店统计图
    public function store_charts()
    {
        $store = I('store', 0, 'string');
        // 0全部 1待支付 2订金支付 3已支付 4已完成
        $state = I('state/d', 0);
        $startTime = I('stime');
        $endTime = I('etime');
        if ($state == 1) {
            $map1['o.pay_status'] = $map['pay_status'] = 0;
        } elseif ($state == 2) {
            $map1['o.pay_status'] = $map['pay_status'] = 2;
        } elseif ($state == 3) {
            $map1['o.pay_status'] = $map['pay_status'] = 1;
        } elseif ($state == 4) {
            // 0:待支付 1:待商家确认 2:待发货 3:配送中 4:确认已收货 5:确认已收款 6:待评价 7:已评价
            $map1['o.status_code'] = $map['status_code'] = array(
                'egt',
                4
            );
        } elseif ($state == 5) {
            // 已评价
            $map1['o.status_code'] = $map['status_code'] = 7;
        } elseif ($state == 6) {
            // 待商家确认
            $map1['o.status_code'] = $map['status_code'] = 1;
        } elseif ($state == 7) {
            // 确认已收款
            $map1['o.status_code'] = $map['status_code'] = 5;
        }
        $startTime = strtotime($startTime);
        $map1['o.cTime'] = $map['cTime'] = array(
            'egt',
            $startTime
        );
        if (!empty($endTime)) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $map1['o.cTime'] = $map['cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        }
        $all = $this->_get_xAxis($startTime, $endTime);
        $xAxis = $all['xaxis'];
        $sdayArr = $dayArr = $all['count'];
        if (empty($store)) {
            $data = M('shop_order')->where(wp_where($map))->column('cTime', 'id');
        } else {
            $map1['c.shop_code'] = $store;
            $storedata = M('shop_order')->alias('o')
                ->field('o.id,o.cTime')
                ->join(DB_PREFIX . 'card_member c', 'o.uid=c.`uid`')
                ->where(wp_where($map1))
                ->select();
            foreach ($storedata as $v) {
                $data[$v['id']] = $v['cTime'];
            }
        }
        foreach ($data as $key => $rv) {
            $day = time_format($rv, 'Y-m-d');
            $dayArr[$day]['count']++;
            $ooids[$key] = $key;
        }
        // 积分统计
        if (empty($ooids)) {
            $scoremap['order_id'] = 0;
        } else {
            $scoremap['order_id'] = array(
                'in',
                $ooids
            );
        }
        $scoremap['is_use_score'] = 1;
        $scoreData = M('shop_order_goods')->where(wp_where($scoremap))
            ->field('id,use_score,cTime')
            ->select();
        foreach ($scoreData as $sv) {
            $day = time_format($sv['cTime'], 'Y-m-d');
            $sdayArr[$day]['count'] += $sv['use_score'];
        }
        $oArr['data'] = getSubByKey($dayArr, 'count');
        $oArr['name'] = '订单数';
        $charArr[] = $oArr;
        $sArr['data'] = getSubByKey($sdayArr, 'count');
        $sArr['name'] = '兑换积分';
        $charArr[] = $sArr;

        $count = count($dayArr);
        $highcharts['title'] = '订单统计数据';
        $highcharts['xAxis'] = $xAxis;
        $highcharts['series'] = $charArr;
        $highcharts['x_space'] = floor($count / 14);
        $highcharts['x_space'] = $highcharts['x_space'] < 1 ? 1 : $highcharts['x_space'];
        // dump($highcharts);die;
        $this->ajaxReturn($highcharts, 'JSON');
    }

    public function _get_xAxis($startTime, $endTime = '', $addField = '')
    {
        empty($endTime) && $endTime = NOW_TIME;
        $endTime = strtotime(time_format($endTime, 'Y-m-d') . ' 23:59:59');
        // 生成时间数组
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $thisDate = time_format($i, 'Y-m-d');
            $xAxis[] = time_format(strtotime($thisDate), 'm/d');
            $dayArr[$thisDate]['count'] = 0;
            if ($addField) {
                $dayArr[$thisDate][$addField] = 0;
            }
        }
        $data['xaxis'] = $xAxis;
        $data['count'] = $dayArr;
        return $data;
    }

    public function shop_export()
    {
        // dump(input('stime'));
        $param['stime'] = strtotime(I('stime', 0, 'string'));
        $param['etime'] = strtotime(I('etime', 0, 'string'));
        // dump($param);
        $res = D('Count')->getCharts($param);

        $timeList = $this->getTimeList2($param['stime'], $param['etime']);
        // dump($res);
        // dump($timeList);
        // exit();
        $dataArr[] = array(
            '日期',
            '商品流量总数'
        );

        foreach ($timeList as $k => $v) {
            $dataArr[] = array(
                'date' => $timeList[$k],
                'count' => $res[$k]
            );
        }
        outExcel($dataArr, '商品流量统计');
    }

    public function getTimeList2($startTime, $endTime)
    {
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $xAxis[] = time_format($i, 'Y-m-d');
        }
        return $xAxis;
    }

    public function order_export()
    {
        $store = I('store', 0, 'string');
        // 0全部 1待支付 2订金支付 3已支付 4已完成
        $state = I('state/d', 0);
        $startTime = I('stime');
        $endTime = I('etime');
        if ($state == 1) {
            $map1['o.pay_status'] = $map['pay_status'] = 0;
        } elseif ($state == 2) {
            $map1['o.pay_status'] = $map['pay_status'] = 2;
        } elseif ($state == 3) {
            $map1['o.pay_status'] = $map['pay_status'] = 1;
        } elseif ($state == 4) {
            // 0:待支付 1:待商家确认 2:待发货 3:配送中 4:确认已收货 5:确认已收款 6:待评价 7:已评价
            $map1['o.status_code'] = $map['status_code'] = array(
                'egt',
                4
            );
        }
        $startTime = strtotime($startTime);
        $map1['o.cTime'] = $map['cTime'] = array(
            'egt',
            $startTime
        );
        if (!empty($endTime)) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $map1['o.cTime'] = $map['cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        }
        $all = $this->_get_xAxis($startTime, $endTime);
        $xAxis = $all['xaxis'];
        $dayArr = $all['count'];
        if (empty($store)) {
            $data = M('shop_order')->where(wp_where($map))->column('cTime', 'id');
        } else {
            $map1['c.shop_code'] = $store;
            $data = M('shop_order')->alias('o')
                ->field('o.id,o.cTime')
                ->join(DB_PREFIX . 'card_member c', 'o.uid=c.`uid`')
                ->where(wp_where($map1))
                ->select();
        }
        foreach ($data as $rv) {
            $day = time_format($rv, 'Y-m-d');
            $dayArr[$day]['count']++;
        }
        $fieldArr['title'] = '日期';
        $fieldArr['count'] = '总数';
        foreach ($fieldArr as $k => $vv) {
            $titleArr[] = $vv;
        }
        $dataArr[] = $titleArr;
        foreach ($dayArr as $key => $vo) {
            $arr['title'] = $key;
            $arr['count'] = $vo['count'];
            $dataArr[] = $arr;
            unset($arr);
        }
        // dump($dataArr);exit;
        outExcel($dataArr, '订单统计');
    }

    /**
     * *************积分统计******************
     */
    public function score_count()
    {
        $category = D('Category')->getShopCategory('', 1); // dump($Category);
        $this->assign('category', $category);
        // dump($category);
        return $this->fetch();
    }

    // 订单门店统计图
    public function score_charts()
    {
        $category = I('category/d', 0);
        // 0全部 1待支付 2订金支付 3已支付 4已完成
        // $state = I('state/d', 0);
        $startTime = I('stime');
        $endTime = I('etime');
        $startTime = strtotime($startTime);
        $map1['g.cTime'] = array(
            'egt',
            $startTime
        );
        if (!empty($endTime)) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $map1['g.cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        }
        if ($category) {
            $map1['c.category_first'] = $category;
        }

        $all = $this->_get_xAxis($startTime, $endTime, 'score_count');
        $xAxis = $all['xaxis'];
        $dayArr = $all['count'];
        $data = M('shop_order_goods')->alias('g')
            ->field('g.num,g.cTime')
            ->join(DB_PREFIX . 'goods_category_link c', 'g.`goods_id`=c.`goods_id`')
            ->where(wp_where($map1))
            ->select();
        foreach ($data as $rv) {
            $day = time_format($rv['cTime'], 'Y-m-d');
            $dayArr[$day]['count'] += $rv['num'];
        }
        $map1['g.is_use_score'] = 1;
        $data = M('shop_order_goods')->alias('g')
            ->field('g.use_score,g.cTime')
            ->join(DB_PREFIX . 'goods_category_link c', 'g.`goods_id`=c.`goods_id`')
            ->where(wp_where($map1))
            ->select();
        foreach ($data as $rv) {
            $day = time_format($rv['cTime'], 'Y-m-d');
            $dayArr[$day]['score_count'] += $rv['use_score'];
        }
        $gArr['data'] = getSubByKey($dayArr, 'count');
        $gArr['name'] = '兑换商品数';
        $sArr['data'] = getSubByKey($dayArr, 'score_count');
        $sArr['name'] = '兑换积分数';
        $charArr[] = $gArr;
        $charArr[] = $sArr;
        $count = count($dayArr);
        $highcharts['title'] = '购买商品统计数据';
        $highcharts['xAxis'] = $xAxis;
        $highcharts['series'] = $charArr;
        $highcharts['x_space'] = floor($count / 14);
        $highcharts['x_space'] = $highcharts['x_space'] < 1 ? 1 : $highcharts['x_space'];
        $this->ajaxReturn($highcharts, 'JSON');
    }

    public function score_export()
    {
        $category = I('category/d', 0);
        // 0全部 1待支付 2订金支付 3已支付 4已完成
        // $state = I('state/d', 0);
        $startTime = I('stime');
        $endTime = I('etime');
        $startTime = strtotime($startTime);
        $map1['g.cTime'] = array(
            'egt',
            $startTime
        );
        if (!empty($endTime)) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $map1['g.cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        }
        if ($category) {
            $map1['c.category_first'] = $category;
        }

        $all = $this->_get_xAxis($startTime, $endTime, 'score_count');
        $xAxis = $all['xaxis'];
        $dayArr = $all['count'];
        // 商品数
        $data = M('shop_order_goods')->alias('g')
            ->field('g.num,g.cTime')
            ->join( DB_PREFIX . 'goods_category_link c', 'g.`goods_id`=c.`goods_id`')
            ->where(wp_where($map1))
            ->select();
        foreach ($data as $rv) {
            $day = time_format($rv['cTime'], 'Y-m-d');
            $dayArr[$day]['count'] += $rv['num'];
        }
        // 兑换积分数
        $map1['g.is_use_score'] = 1;
        $data = M('shop_order_goods')->alias('g')
            ->field('g.use_score,g.cTime')
            ->join( DB_PREFIX . 'goods_category_link c', 'g.`goods_id`=c.`goods_id`')
            ->where(wp_where($map1))
            ->select();
        foreach ($data as $rv) {
            $day = time_format($rv['cTime'], 'Y-m-d');
            $dayArr[$day]['score_count'] += $rv['use_score'];
        }

        $fieldArr['title'] = '日期';
        $fieldArr['goods_count'] = '商品数';
        $fieldArr['score_count'] = '兑换积分数';
        foreach ($fieldArr as $k => $vv) {
            $titleArr[] = $vv;
        }
        $dataArr[] = $titleArr;
        foreach ($dayArr as $key => $vo) {
            $arr['title'] = $key;
            $arr['goods_count'] = $vo['count'];
            $arr['score_count'] = $vo['score_count'];
            $dataArr[] = $arr;
            unset($arr);
        }
        outExcel($dataArr, '积分商品统计');
    }

    /**
     * *************活动统计******************
     */
    public function action_count()
    {
        return $this->fetch();
    }

    /**
     * *************商品流量统计******************
     */
    // 通用插件的列表模型
    public function lists()
    {
        // 分类（一级）
        $Category = D('Category')->getShopCategory('', 1); // dump($Category);
        $this->assign('category', $Category);

        $goods = D('ShopGoods')->getList(); // dump($goods);
        $this->assign('goods', $goods);

        return $this->fetch();
    }

    public function category_goods()
    {
        $category = I('category/d', 0);
        $list = D('ShopGoods')->getList('', '', $order = 'id desc', 0, '', $category, $uid = 0, $goodsidArr = null, '*', $notIds = null); // dump($list);exit;
        $this->ajaxReturn($list);
    }

    // 统计图
    public function setCharts()
    {
        $param['stime'] = strtotime(I('stime', 0, 'string'));
        $param['etime'] = strtotime(I('etime', 0, 'string'));

        $res = D('Count')->getCharts($param);
        $return['time'] = $this->getTimeList($param['stime'], $param['etime']);
        $return['data'] = $res;
        $this->ajaxReturn($return);
    }

    public function getTimeList($startTime, $endTime)
    {
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $thisDate = time_format($i, 'Y-m-d');
            $xAxis[] = time_format(strtotime($thisDate), 'm/d');
        }
        return $xAxis;
    }
}
