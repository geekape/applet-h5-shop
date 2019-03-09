<?php

namespace app\weixin\model;

use app\common\model\Base;

// Weixin模型
class Payment extends Base
{

    // JSAPI支付
    public function jsapi_pay($appid, $product = [], $callback = '')
    {
        $goods = $product;
        $goods['appid'] = $appid;

        $goods['trade_type'] = 'JSAPI'; // 此处值固定
        $goods['spbill_create_ip'] = get_server_ip();

        // 生成订单
        $order = $this->add_order($goods, $callback); // 必传的参数有：appid,body,out_trade_no,total_fee
        if ($order['status'] == 0) {
            return $order;
        }
        // add_debug_log($order, '888');
        // 组装jsapi参数
        $return['appId'] = $appid;
        $return['timeStamp'] = NOW_TIME;
        $return['nonceStr'] = uniqid();
        $return['package'] = 'prepay_id=' . $order['prepay_id'];
        $return['signType'] = 'MD5';
        add_debug_log($return, 'Payment_return');
        add_debug_log($order, 'partner_key');
        $return['paySign'] = make_sign($return, $order['partner_key']);
        $return['status'] = 1;
        // add_debug_log($return, '999');
        return $return;
    }

    // 原生扫码支付
    public function scan_pay($appid, $product = [], $callback = '', $type = 2)
    {
        if ($type == 1) {
            return $this->add_scan_by_one($appid, $product, $callback);
        } else {
            return $this->add_scan_by_two($appid, $product, $callback);
        }
    }

    // 刷卡支付
    public function micro_pay($appid, $product = [], $callback = '')
    {
        // body out_trade_no total_fee auth_code
    }

    // 小程序支付
    public function weiapp_pay($appid, $product = [], $callback = '')
    {
        add_debug_log($appid, 'weiapp_pay');
        $goods = $product;
        $goods['appid'] = $appid;

        $goods['trade_type'] = 'JSAPI'; // 此处值固定
        // $goods['spbill_create_ip'] = get_server_ip();

        // 生成订单
        $order = $this->add_order($goods, $callback); // 必传的参数有：appid,body,out_trade_no,total_fee
        if ($order['status'] == 0) {
            add_debug_log($order, '555');
            return $order;
        }
        // add_debug_log($order, '888');
        // 组装jsapi参数
        $return['appId'] = $appid;
        $return['timeStamp'] = (string)NOW_TIME;
        $return['nonceStr'] = uniqid();
        $return['package'] = 'prepay_id=' . $order['prepay_id'];
        $return['signType'] = 'MD5';
        add_debug_log($return, '$return');
        add_debug_log($order['partner_key'], 'partner_key');
        $return['paySign'] = make_sign($return, $order['partner_key']);
        $return['status'] = 1;
        // add_debug_log($return, '999');
        return $return;
    }

    // 现金红包
    public function redbag($appid, $openid, $money, $param = [], $act = [], $cron = true)
    {
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($openid)) {
            $return['msg'] = 'openid不能为空';
            return $return;
        }
        if (empty($money)) {
            $return['msg'] = '红包金额不能为空';
            return $return;
        }

        $recode['appid'] = $openid;
        $recode['openid'] = $openid;
        $recode['money'] = $money;
        isset($act['act_id']) && $recode['act_id'] = $act['act_id'];
        isset($act['act_mod']) && $recode['act_mod'] = $act['act_mod'];
        $recode['status'] = $cron ? 1 : 0;
        $recode['cTime'] = NOW_TIME;
        $recode['more_param'] = serialize($param);
        $id = M('redbag_recode')->strict(false)->insertGetId($recode);
        if (!$id) {
            $return['msg'] = '红包记录保存到数据库失败';
            return $return;
        }

        if (!$cron) { // 红包马上下发
            $recode['id'] = $id;
            $this->send_redbag($recode);
        }
    }

    function send_redbag($recode = [])
    {
        $param['appid'] = $param['wxappid'] = $recode['appid'];

        $param = $this->init_config($param, true);
        $partner_key = $param['partner_key'];
        unset($param['partner_key'], $param['appid']);

        $param['mch_billno'] = $param['mch_id'] . date(Ymd) . $this->getRandStr(); // mch_id+yyyymmdd+10位一天内不能重复的数字
        if (isset($recode['send_name']) && !empty($recode)) {
        }
        $param['wxappid'] = $recode['appid'];
        $param['wxappid'] = $recode['appid'];
        $param['wxappid'] = $recode['appid'];
        $param['wxappid'] = $recode['appid'];
        $param['wxappid'] = $recode['appid'];
        $param['wxappid'] = $recode['appid'];
    }

    // 生成10位一天内不能重复的数字
    private function getRandStr()
    {
        $arr = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z'
        );
        $key = array_rand($arr);
        return substr(time(), -5) . substr(microtime(), 2, 4) . $arr[$key];
    }

    // 原生扫码支付模式一
    private function add_scan_by_one($appid, $product = [], $callback = '')
    {
        if (!isset($product['product_id']) || empty($product['product_id'])) {
            $return['status'] = 0;
            $return['msg'] = '商品product_id参数不能为空';
            return $return;
        }

        $param['appid'] = $param2['appid'] = $appid;
        $param['product_id'] = $product['product_id'];
        $param['time_stamp'] = NOW_TIME;
        $param = $this->init_config($param);

        $param2['long_url'] = 'weixin://wxpay/bizpayurl?' . http_build_query($param);
        $param2 = $this->init_config($param2);

        $url = 'https://api.mch.weixin.qq.com/tools/shorturl';
        $res_data = post_data($url, $param2, 'xml');

        $return['status'] = 1;
        if ($res_data['return_code'] == 'FAIL') {
            $return['status'] = 0;
            $return['msg'] = $res_data['return_msg'];
        } elseif ($res_data['result_code'] == 'FAIL') {
            $return['status'] = 0;
            $return['msg'] = $res_data['err_code'] . ': ' . $res_data['err_code_des'];
        }

        // 记录订单信息到数据库
        $res = $this->insert_scan($appid, $product, $callback, $res_data);
        if (!$res || $return['status'] == 0) {
            $return['status'] = 0;
            $return['msg'] = '订单数据写入数据库失败';
            return $return;
        }

        $return['msg'] = '下单成功';
        $return = array_merge($return, $res_data);
        return $return;
    }

    // 原生扫码支付模式二
    private function add_scan_by_two($appid, $product = [], $callback = '')
    {
        $goods = $product;
        $goods['appid'] = $appid;

        $goods['trade_type'] = 'NATIVE'; // 此处值固定
        $goods['spbill_create_ip'] = get_server_ip();

        // 生成订单
        $order = $this->add_order($goods, $callback); // 必传的参数有：appid,body,out_trade_no,total_fee
        return $order;
    }

    // 记录扫码记录到数据库
    private function insert_scan($appid, $product = [], $callback = '', $res_data = [])
    {
        $data['appid'] = $appid;
        $data['callback'] = $callback;
        $data['product_id'] = $product['product_id'];
        $data['out_trade_no'] = $product['out_trade_no'];
        $data['total_fee'] = $product['total_fee'];

        $data['cTime'] = NOW_TIME;
        unset($product['product_id'], $product['out_trade_no'], $product['total_fee']);
        $data['product'] = serialize($product);
        $data['shorturl_res'] = serialize($res_data);

        $res = M('payment_scan')->insertGetId($data);
        return $res;
    }

    /*
     * 对外提供支付服务的接口
     * @param array $param 支付参数，必传的参数有：appid,body,out_trade_no,total_fee,openid
     * @param string $callback 回调的模型地址，
     * 格式如：home/Service/payok 用户支付后先通知到notice.php，然后由它调用 D('home/Service')->payok($notice)的方式实现回调
     * @return static
     */
    public function add_order($param, $callback)
    {
        $param = $this->init_add_order($param);
        $param = $this->init_config($param, true);
        $partner_key = $param['partner_key'];
        unset($param['partner_key']);
        add_debug_log($param, $partner_key);
        $res = $this->check_order($param, $callback);
        if ($res['status'] == 0) {
            return $res;
        }
        // dump($param);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res_data = post_data($url, $param, 'xml');
        // dump($res_data);
        $return['status'] = 1;
        if ($res_data['return_code'] == 'FAIL') {
            $return['status'] = 0;
            $return['msg'] = $res_data['return_msg'];
            return $return;
        } elseif ($res_data['result_code'] == 'FAIL') {
            $return['status'] = 0;
            $return['msg'] = $res_data['err_code'] . ': ' . $res_data['err_code_des'];
            return $return;
        }

        // 记录订单信息到数据库
        $res = $this->insert_order($param, $res_data, $callback);
        if (!$res) {
            $return['status'] = 0;
            $return['msg'] = '订单数据写入数据库失败';
            return $return;
        }

        if ($return['status'] == 0) {
            return $return;
        } else {
            $return['msg'] = '下单成功';
            $return = array_merge($res_data, $return);
            $return['partner_key'] = $partner_key;
            return $return;
        }
    }

    // 订单查询
    function query_order($appid, $out_trade_no, $mch_id = '', $transaction_id = '')
    {
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($out_trade_no) && empty($transaction_id)) {
            $return['msg'] = 'out_trade_no和transaction_id不能同时为空';
            return $return;
        }

        $param['appid'] = $appid;
        if (!empty($transaction_id)) {
            $param['transaction_id'] = $transaction_id;
        } else {
            $param['out_trade_no'] = $out_trade_no;
        }
        $param['mch_id'] = $mch_id;
        $param = $this->init_config($param);

        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $res_data = post_data($url, $param, 'xml');

        if ($res_data['return_code'] == 'FAIL') {
            $return['msg'] = $res_data['return_msg'];
            return $return;
        } elseif ($res_data['result_code'] == 'FAIL') {
            $return['msg'] = $res_data['err_code'] . ': ' . $res_data['err_code_des'];
            return $return;
        } elseif ($res_data['trade_state'] != 'SUCCESS') {
            $arr = [
                'REFUND' => '转入退款',
                'NOTPAY' => '未支付',
                'CLOSED' => '已关闭',
                'REVOKED' => '已撤销（刷卡支付）',
                'USERPAYING' => '用户支付中',
                'PAYERROR--支付失败(其他原因，如银行返回失败)'
            ];
            $desc = isset($arr[$res_data['trade_state']]) ? $arr[$res_data['trade_state']] : '';
            $return['msg'] = $res_data['trade_state'] . ': ' . $desc;
            return $return;
        }

        $map['appid'] = $res_data['appid'];
        $map['out_trade_no'] = $res_data['out_trade_no'];
        $payment = $this->where(wp_where($map))->find();
        // dump($payment);
        if ($payment['total_fee'] != $res_data['total_fee']) {
            $return['msg'] = '订单金额和支付金额不一致';
            return $return;
        }

        // 更新订单状态
        $save['after_pay_res'] = serialize($res_data);
        $save['is_pay'] = 1;
        $this->where(wp_where($map))->update($save);

        return $res_data;
    }

    // 关闭订单
    public function close_order($appid, $out_trade_no, $mch_id = '')
    {
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($out_trade_no)) {
            $return['msg'] = 'out_trade_no和transaction_id不能同时为空';
            return $return;
        }

        $param['appid'] = $appid;
        $param['out_trade_no'] = $out_trade_no;

        $param['mch_id'] = $mch_id;
        $param = $this->init_config($param);

        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';
        $res_data = post_data($url, $param, 'xml');

        return $res_data;
    }

    // 申请退款
    public function refund($appid, $out_trade_no, $total_fee = 0, $refund_desc = '',$refund_money=-1)
    {
        // 测试期间关闭支付功能
        // return true;
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($out_trade_no)) {
            $return['msg'] = 'out_trade_no和transaction_id不能同时为空';
            return $return;
        }

        $config = D('common/Publics')->getInfoByAppid($appid);

        $param['appid'] = $appid;
        $param['out_trade_no'] = $out_trade_no;
        $param['out_refund_no'] = 'refund_'.uniqid().'_' . $out_trade_no;
        $param['total_fee'] =  $total_fee;
        $param ['refund_fee'] = $refund_money == - 1 ? $total_fee : $refund_money;

        $param = $this->init_config($param, false, $config);

        // 获取证书路径
        $useCert = get_cert_pem($config);
        if (empty($useCert)) {
            $return['msg'] = '缺少支付证书';
            return $return;
        }

        // dump($useCert);
        // exit();
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $res_data = post_data($url, $param, 'xml', true, $useCert);
        return $res_data;
    }

    // 记录订单信息到数据库
    private function insert_order($param = [], $res_data = [], $callback = '')
    {
        $data['out_trade_no'] = $param['out_trade_no'];
        $data['total_fee'] = $param['total_fee'];
        $data['appid'] = $param['appid'];
        $data['wpid'] = get_wpid();
        $data['openid'] = isset($param['openid']) ? $param['openid'] : '';
        $data['callback'] = $callback;
        $data['prepay_id'] = $res_data['prepay_id'];
        $data['code_url'] = isset($res_data['code_url']) ? $res_data['code_url'] : '';
        $data['return_code'] = $res_data['return_code'];
        $data['return_msg'] = $res_data['return_msg'];
        $data['result_code'] = $res_data['result_code'];
        $data['err_code_des'] = isset($res_data['err_code']) ? $res_data['err_code'] . ': ' . $res_data['err_code_des'] : '';
        $data['cTime'] = NOW_TIME;

        unset($param['out_trade_no'], $param['total_fee'], $param['appid'], $param['openid']);
        unset($res_data['prepay_id'], $res_data['code_url'], $res_data['return_code'], $res_data['return_msg'], $res_data['result_code'], $res_data['err_code'], $res_data['err_code_des']);
        $data['param'] = serialize($param);
        $data['res_data'] = serialize($res_data);
        // 验证订单号是否存在,存在则delete 重新添加
        $map['out_trade_no'] = $data['out_trade_no'];
        $this->where(wp_where($map))->delete();
        $res = $this->insertGetId($data);
        return $res;
    }

    // 支付配置信息初始化
    private function init_config($param = [], $need_key = false, $config = [])
    {
        // 如果连appid的值都没有，肯定有错，不再处理
        if (!isset($param['appid']) || empty($param['appid']))
            return $param;

        // 获取配置信息
        if (empty($config)) {
            $config = D('common/Publics')->getInfoByAppid($param['appid']);
        }

        // 如果没有商户号，自动从配置中读取
        if (!isset($param['mch_id']) || empty($param['mch_id'])) {
            $param['mch_id'] = $config['mch_id'];
        }

        if (!isset($param['nonce_str']) || empty($param['nonce_str'])) {
            $param['nonce_str'] = uniqid();
        }

        if (!isset($param['partner_key']) || empty($param['partner_key'])) {
            $partner_key = $config['partner_key'];
        } else {
            $partner_key = $param['partner_key'];
            unset($param['partner_key']);
        }

        $param['sign'] = make_sign($param, $partner_key);
        if ($need_key) {
            $param['partner_key'] = $partner_key;
        }

        return $param;
    }

    // 支付配置信息初始化
    private function init_add_order($param = [])
    {
        if (!isset($param['spbill_create_ip']) || empty($param['spbill_create_ip'])) {
            $param['spbill_create_ip'] = get_server_ip();
        }
        if ($param['spbill_create_ip'] == '::1') {
            $param['spbill_create_ip'] = '115.29.168.253';//取不到值，给个默认值，开发者可以修改成自己的服务器IP
        }

        if (!isset($param['notify_url']) || empty($param['notify_url'])) {
            $param['notify_url'] = SITE_URL . '/notice.php';
            // $param['notify_url'] = 'https://www.01moban.com/notice.php';
        }
        if (!isset($param['trade_type']) || empty($param['trade_type'])) {
            $param['trade_type'] = 'JSAPI';
        }

        return $param;
    }

    // 数据验证
    private function check_order($param = [], $callback = '')
    {
        $return['status'] = 0;
        if (empty($callback)) {
            $return['msg'] = 'callback回调参数不能为空';
            return $return;
        }

        $rules = [
            'appid' => [
                'require' => 1,
                'len' => 32
            ],
            'mch_id' => [
                'require' => 1,
                'len' => 32
            ],
            'device_info' => [
                'len' => 32
            ],
            'nonce_str' => [
                'require' => 1,
                'len' => 32
            ],
            'sign' => [
                'require' => 1,
                'len' => 32
            ],
            'sign_type' => [
                'len' => 32,
                'in' => [
                    'HMAC-SHA256',
                    'MD5'
                ]
            ],
            'body' => [
                'require' => 1,
                'len' => 128
            ],
            'detail' => [
                'len' => 6000
            ],
            'attach' => [
                'len' => 127
            ],
            'out_trade_no' => [
                'require' => 1,
                'len' => 32
            ],
            'total_fee' => [
                'require' => 1
            ],
            'spbill_create_ip' => [
                'require' => 1,
                'len' => 16
            ],
            'time_start' => [
                'len' => 14
            ],
            'time_expire' => [
                'len' => 14
            ],
            'goods_tag' => [
                'len' => 32
            ],
            'notify_url' => [
                'require' => 1,
                'len' => 256
            ],
            'trade_type' => [
                'require' => 1,
                'len' => 16,
                'in' => [
                    'JSAPI',
                    'NATIVE',
                    'APP'
                ]
            ],
            'product_id' => [
                'len' => 32
            ],
            'limit_pay' => [
                'len' => 32,
                'in' => [
                    'no_credit'
                ]
            ],
            'openid' => [
                'len' => 32
            ]
        ];

        foreach ($rules as $key => $val) {
            if (isset($val['require'])) {
                if (!isset($param[$key])) {
                    $return['msg'] = '缺少必填参数：' . $key;
                    return $return;
                } elseif (empty($param[$key])) {
                    $return['msg'] = $key . '的值不能为空';
                    return $return;
                }
            }
            if (isset($param[$key])) {
                $len = mb_strlen($param[$key], 'UTF-8');
                if (isset($val['len']) && $len > $val['len']) {
                    $return['msg'] = $key . '的长度不能超过' . $val['len'] . '字节';
                    return $return;
                }
                if (isset($val['in']) && !in_array($param[$key], $val['in'])) {
                    $return['msg'] = $key . '的值不正确，可选的值有：' . implode(', ', $val['in']);
                    return $return;
                }
            }
        }

        // trade_type=JSAPI时openid必须
        if ($param['trade_type'] == 'JSAPI' && empty($param['openid'])) {
            $return['msg'] = 'openid的值不能为空';
            return $return;
        }
        // 判断订单号out_trade_no是否唯一
        $map['appid'] = $param['appid'];
        $map['out_trade_no'] = $param['out_trade_no'];
        $check = $this->where(wp_where($map))->value('id');

        $return['status'] = 1;
        $return['msg'] = '检测通过';
        return $return;
    }
}
