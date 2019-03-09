<?php
namespace app\weixin\controller;

use app\common\controller\WebBase;

// PC运营管理端的控制器
class Notice extends WebBase
{

    function index()
    {
        $res_data = wp_file_get_contents('php://input');
        add_debug_log($res_data, 'notice_index');
        
        $data = FromXml($res_data);
        if (isset($data['product_id'])) {
            // 这是属于扫码支付里的模式一的回调
            $this->scan_callback($data);
        } else {
            // 支付后的异步通知
            $this->pay_after_callback($data);
        }
    }

    function test()
    {
        $res_data = '<xml><appid><![CDATA[wxe669eed18e05f95b]]></appid>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1481485072]]></mch_id>
<nonce_str><![CDATA[5937cff32b9d5]]></nonce_str>
<openid><![CDATA[o6L790BCjs8WT_y9Yfnmzw2o14sQ]]></openid>
<out_trade_no><![CDATA[zufang1496829939588]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[097237E187541D8EA0B7E481D067DBE8]]></sign>
<time_end><![CDATA[20170607180549]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[4007372001201706074709828849]]></transaction_id>
</xml>';
        
        $data = FromXml($res_data);
        if (isset($data['product_id'])) {
            // 这是属于扫码支付里的模式一的回调
            $this->scan_callback($data);
        } else {
            // 支付后的异步通知
            $this->pay_after_callback($data);
        }
    }

    private function pay_after_callback($data)
    {
        // dump($data);
        if ($data['return_code'] == 'FAIL') {
            $this->return_error($data['return_msg']);
        } elseif ($data['result_code'] == 'FAIL') {
            $this->return_error($data['err_code'] . ': ' . $data['err_code_des']);
        }
        
        $map['appid'] = $data['appid'];
        $map['out_trade_no'] = $data['out_trade_no'];
        $payment = M('payment')->where(wp_where($map))->find();
        if ($payment['is_pay'] == 1) {
            // 已经处理过的不再处理
            $this->return_success(); 
        }
        // dump($payment);
        if ($payment['total_fee'] != $data['total_fee']) {
            // $this->return_error('订单金额和支付金额不一致'); //TODO 调试期间先关闭
        }
        
        // 更新订单状态
        $save['after_pay_res'] = serialize($data);
        $save['is_pay'] = 1;
        M('payment')->where(wp_where($map))->update($save);
        
        // 回调处理
        if (empty($payment['callback'])) {
            $this->return_error('callback参数出错');
        }
        $arr = explode('/', $payment['callback']);
        if (count($arr) != 3) {
            $this->return_error('callback格式出错');
        }
        
        $mod = $arr[0] . '/' . $arr[1];
        $act = $arr[2];
        $res = D($mod)->$act($data);
        if ($res['status'] == 0) {
            $this->return_error($res['msg']);
        } else {
            $this->return_success();
        }
    }

    private function scan_callback($data)
    {
        $res['appid'] = $data['appid'];
        $res['mch_id'] = $data['mch_id'];
        $res['nonce_str'] = $data['nonce_str'];
        
        // 通过product_id获取商品信息
        $map['product_id'] = $data['product_id'];
        $map['appid'] = $data['appid'];
        $scan_info = M('payment_scan')->where(wp_where($map))->find();
        
        $goods = $res;
        $goods['out_trade_no'] = $scan_info['out_trade_no'];
        $goods['total_fee'] = $scan_info['total_fee'];
        $product = unserialize($scan_info['product']);
        if (! empty($product)) {
            $goods = array_merge($goods, $product);
        }
        
        $goods['openid'] = $data['openid'];
        $goods['trade_type'] = 'NATIVE'; // 此处值固定
        $goods['spbill_create_ip'] = get_server_ip();
        
        // 生成订单
        $order = D('Payment')->add_order($goods, $scan_info['callback']); // 必传的参数有：appid,body,out_trade_no,total_fee
        add_debug_log($order, 'order');
        $res['return_code'] = 'SUCCESS';
        if ($order['status'] == 0) {
            $this->return_error($order['msg']);
        } else {
            $res['result_code'] = 'SUCCESS';
        }
        
        $res['prepay_id'] = $order['prepay_id'];
        
        $partner_key = D('common/Publics')->getInfoByAppid($data['appid'], 'partner_key');
        $res['sign'] = make_sign($res, $partner_key);
        // dump($res);
        
        $xml = ToXml($res);
        
        $save['order_data'] = serialize($data);
        $save['order_res'] = $xml;
        M('payment_scan')->where(wp_where($map))->update($save);
        // add_debug_log($xml, 'scan_callback');
        echo $xml;
        exit();
    }

    private function return_error($msg)
    {
        add_debug_log($msg, 'return_error');
        $xml = '<xml>
  <return_code><![CDATA[FAIL]]></return_code>
  <return_msg><![CDATA[' . $msg . ']]></return_msg>
</xml>';
        echo ($xml);
        exit();
    }

    private function return_success()
    {
        $xml = '<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>';
        echo ($xml);
        exit();
    }
}
