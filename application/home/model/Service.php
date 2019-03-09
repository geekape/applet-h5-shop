<?php

namespace app\home\model;

use app\common\model\ServiceBase;

/**
 * 商城插件
 *
 * @author 凡星
 */
class Service extends ServiceBase
{
    public $info = '';

    public function payok($res_data, $order)
    {
        // 记录下日志
        // array (
        // 'appid' => 'wxe669eed18e05f95b',
        // 'bank_type' => 'CFT',
        // 'cash_fee' => '1',
        // 'fee_type' => 'CNY',
        // 'is_subscribe' => 'N',
        // 'mch_id' => '1481485072',
        // 'nonce_str' => '5937cff32b9d5',
        // 'openid' => 'o6L790BCjs8WT_y9Yfnmzw2o14sQ',
        // 'out_trade_no' => 'zufang1496829939588',
        // 'result_code' => 'SUCCESS',
        // 'return_code' => 'SUCCESS',
        // 'sign' => '097237E187541D8EA0B7E481D067DBE8',
        // 'time_end' => '20170607180549',
        // 'total_fee' => '1',
        // 'trade_type' => 'JSAPI',
        // 'transaction_id' => '4007372001201706074709828849',
        // )
        add_debug_log($res_data, 'payok');

        // 进行具体的业务操作
        // dump ( $res_data );

        // 发送模板消息给用户
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . get_access_token($order ['token']);
        $param ['touser'] = $res_data ['openid'];
        $param ['template_id'] = '-ekG5kJ-8x7OkTxd1shG-5-y90q8El5vj0DKVGwTZ9o';
        $param ['form_id'] = $order ['prepay_id'];

        $p = unserialize($order ['param']);

        $param ['data'] = [
            'keyword1' => [
                'value' => $res_data ['out_trade_no'],
                'color' => '#173177'
            ],
            'keyword2' => [
                'value' => $res_data ['total_fee'],
                'color' => '#173177'
            ],
            'keyword3' => [
                'value' => $res_data ['time_end'],
                'color' => '#173177'
            ],
            'keyword4' => [
                'value' => $p ['body'],
                'color' => '#173177'
            ],
            'keyword5' => [
                'value' => '已完成支付',
                'color' => '#173177'
            ],
            'keyword6' => [
                'value' => '微信支付',
                'color' => '#173177'
            ]
        ];

        post_data($url, $param);

        return [
            'status' => 1,
            'msg' => ''
        ];
    }

    public function solutionPayOk($res_data, $order)
    {
        add_debug_log($res_data, 'solutionPayOk');

        // 进行具体的业务操作
        $map ['out_trade_no'] = $res_data ['out_trade_no'];
        $buy = M('case_buy')->where($map)->field('uid,case_id')->find();
        if (!$buy)
            return [
                'status' => 0,
                'msg' => '非法参数'
            ];

        M('case_buy')->where($map)->save([
            'is_pay' => 1,
            'price' => $res_data ['total_fee']
        ]);

        $check = M('case_user')->where($buy)->find();
        if (!$check) {
            $buy ['status'] = 1;
            $buy ['start_time'] = NOW_TIME;
            $buy ['end_time'] = strtotime('+1 year', NOW_TIME); // 授权一年

            M('case_user')->add($buy);
        } else {
            $save ['status'] = 1;
            $save ['end_time'] = strtotime('+1 year', $check ['end_time']); // 在原来的基础上再授权一年

            M('case_user')->where($buy)->save($save);
        }

        return [
            'status' => 1,
            'msg' => ''
        ];
    }

}
