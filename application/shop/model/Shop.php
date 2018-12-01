<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Shop extends Base
{

    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }

    public function getInfoByWpid($wpid = '')
    {
        empty($wpid) && $wpid = WPID;
        $data = $this->getInfo($wpid);
        return $data;
    }

    public function updateById($id, $data)
    {
        $map['id'] = $id;
        $res = $this->where(wp_where($map))->update($data);
        if ($res) {
            $this->clearCache($id);
        }
    }

    public function getShop($uid)
    {
        $map['uid'] = $uid;
        $res = $this->where(wp_where($map))->select();
        return $res;
    }

    // 处理分销用户获取的拥金
    public function do_distribution_profit($id)
    {
        $orderinfo = D('Shop/Order')->where('id', $id)->find();
        $userinfo = get_userinfo($orderinfo['uid']);
        $wpid = get_wpid();
        // 带来的粉丝必须是关注公众号的
        if (! empty($orderinfo) && $userinfo['has_subscribe'][$wpid] == 1) {
            $price = $orderinfo['total_price'];
            $followId = $orderinfo['uid'];
            
            $shop = D('Shop/Shop')->find($orderinfo['wpid']);
            // 三级分销 ：父shopid
            $parentShop = wp_explode($shop['parent_shop']);
            
            $data['uid'] = $shop['manager_id'];
            $data['wpid'] = get_wpid();
            $data['ctime'] = NOW_TIME;
            $data['order_id'] = $id;
            
            $config = get_info_config('Shop');
            if ($config['need_distribution'] == 1) {
                $count = count($parentShop);
                $maxProfit = $config['max_money'];
                $level = $config['level'];
                $lev[0] = strstr($config['level1'], '%') ? floatval($config['level1']) * 0.01 : $config['level1'];
                $lev[1] = strstr($config['level2'], '%') ? floatval($config['level2']) * 0.01 : $config['level2'];
                $lev[2] = strstr($config['level3'], '%') ? floatval($config['level3']) * 0.01 : $config['level3'];
                switch ($level) {
                    case '3':
                        // 三级
                        if ($count != 0) {
                            foreach ($parentShop as $key => $v) {
                                if (! empty($lev[$key])) {
                                    $data['distribution_percent'] = $lev[$key];
                                    $data['profit_shop'] = $v;
                                    $data['profit'] = $price * $lev[$key];
                                    if ($maxProfit) {
                                        $data['profit'] > $maxProfit && $data['profit'] = $maxProfit;
                                    }
                                    $datas[] = $data;
                                    $money += $data['profit'];
                                }
                            }
                            if (! empty($datas)) {
                                M('shop_distribution_profit')->insertAll($datas);
                                $log['remark'] = '分销提成获利 ' . $money . '元';
                                $log['type'] = 0; // 系统自动充值
                                add_money($data['uid'], $money, $log);
                            }
                        }
                        break;
                    case '2':
                        if ($count == 3) {
                            $parentShop[0] = $parentShop[1];
                            $parentShop[1] = $parentShop[2];
                            unset($parentShop[2]);
                        }
                        if ($count != 0) {
                            foreach ($parentShop as $key => $v) {
                                if (! empty($lev[$key])) {
                                    $data['distribution_percent'] = $lev[$key];
                                    $data['profit_shop'] = $v;
                                    $data['profit'] = $price * $lev[$key];
                                    if ($maxProfit) {
                                        $data['profit'] > $maxProfit && $data['profit'] = $maxProfit;
                                    }
                                    $datas[] = $data;
                                    $money += $data['profit'];
                                }
                            }
                            if (! empty($datas)) {
                                M('shop_distribution_profit')->insertAll($datas);
                                $log['remark'] = '分销提成获利 ' . $money . '元';
                                $log['type'] = 0; // 系统自动充值
                                add_money($data['uid'], $money, $log);
                            }
                        }
                        break;
                    case '1':
                        $map['uid'] = $followId;
                        $map['wpid'] = get_wpid();
                        $duid = M('shop_statistics_follow')->where(wp_where($map))->value('duid');
                        if ($duid) {
                            // 带粉分佣
                            // 判断是否已启用
                            $map['uid'] = $duid;
                            $enable = M('shop_distribution_user')->where(wp_where($map))->value('enable');
                            // 启用状态 才进行分佣
                            if ($enable == 1) {
                                if (! empty($lev[0])) {
                                    $data['uid'] = $duid;
                                    $data['distribution_percent'] = $lev[0];
                                    $data['profit'] = $price * $lev[0];
                                    $data['profit_shop'] = 0;
                                    if ($maxProfit) {
                                        $data['profit'] > $maxProfit && $data['profit'] = $maxProfit;
                                    }
                                    M('shop_distribution_profit')->insert($data);
                                    $log['remark'] = '分销提成获利 ' . $data['profit'] . '元';
                                    $log['type'] = 0; // 系统自动充值
                                    add_money($data['uid'], $data['profit'], $log);
                                }
                            }
                        } else if ($count != 0) {
                            if (! empty($lev[0])) {
                                $data['distribution_percent'] = $lev[0];
                                $data['profit_shop'] = $parentShop[$count - 1];
                                $data['profit'] = $price * $lev[0];
                                if ($maxProfit) {
                                    $data['profit'] > $maxProfit && $data['profit'] = $maxProfit;
                                }
                                M('shop_distribution_profit')->insert($data);
                                $log['remark'] = '分销提成获利 ' . $data['profit'] . '元';
                                $log['type'] = 0; // 系统自动充值
                                add_money($data['uid'], $data['profit'], $log);
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }

    // 获取粉丝对应的分佣员工
    public function getDistributionUser($uid, $update = false)
    {
        $map['wpid'] = get_wpid();
        $map['uid'] = $uid;
        $key = cache_key($map, DB_PREFIX . 'shop_statistics_follow');
        $info = S($key);
        if ($info === false || $update) {
            $info = M('shop_statistics_follow')->where(wp_where($map))->find();
            S($key, $info);
        }
        return $info;
    }

    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
        $info = $this->getInfo($id, true);
        isset($info['manager_id']) && $info['manager_id'] > 0 && $this->getDistributionUser($info['manager_id'], true);
    }
}
