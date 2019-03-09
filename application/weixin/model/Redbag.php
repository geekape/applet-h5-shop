<?php
namespace app\weixin\model;

use app\common\model\Base;


// Weixin模型
class Redbag extends Base
{

    protected $table = DB_PREFIX. 'redbag_recode';
    // 红包字段限制规则
    private $rules = [
        'wxappid' => [
            'require' => 1,
            'len' => 32
        ],
        'mch_id' => [
            'require' => 1,
            'len' => 32
        ],
        'mch_billno' => [
            'require' => 1,
            'len' => 28
        ],
        'nonce_str' => [
            'require' => 1,
            'len' => 32
        ],
        'sign' => [
            'require' => 1,
            'len' => 32
        ],
        'send_name' => [
            'require' => 1,
            'len' => 32
        ],
        're_openid' => [
            'require' => 1,
            'len' => 32
        ],
        'total_amount' => [
            'require' => 1
        ],
        'total_num' => [
            'require' => 1
        ],
        'wishing' => [
            'require' => 1,
            'len' => 128
        ],
        'client_ip' => [
            'require' => 1,
            'len' => 15
        ],
        'act_name' => [
            'require' => 1,
            'len' => 32
        ],
        'remark' => [
            'require' => 1,
            'len' => 256
        ],
        'scene_id' => [
            'len' => 32,
            'in' => [
                'PRODUCT_1',
                'PRODUCT_2',
                'PRODUCT_3',
                'PRODUCT_4',
                'PRODUCT_5',
                'PRODUCT_6',
                'PRODUCT_7',
                'PRODUCT_8'
            ]
        ],
        'risk_info' => [
            'len' => 128
        ],
        'consume_mch_id' => [
            'len' => 32
        ]
    ];
    // 现金红包
    public function add_redbag($appid, $openid, $money, $more_param = [], $cron = true)
    {
        $return['status'] = 0;
        
        $recode['wxappid'] = $appid;
        $recode['re_openid'] = $openid;
        $recode['total_amount'] = $money;
        
        $res = $this->check_input($recode);
        if ($res['status'] == 0) {
            $return['msg'] = $res['msg'];
            return $return;
        }
        
        isset($more_param['act_id']) && $recode['act_id'] = $more_param['act_id'];
        isset($more_param['act_mod']) && $recode['act_mod'] = $more_param['act_mod'];
        $recode['status'] = $cron ? 1 : 0;
        $recode['cTime'] = NOW_TIME;
        
        unset($more_param['act_id'], $more_param['act_mod']);
        $recode['more_param'] = serialize($more_param);
        $this->allowField(true)
            ->isUpdate(false)
            ->data($recode)
            ->save();
        $id = $this->id;
        if (! $id) {
            $return['msg'] = '红包记录保存到数据库失败';
            return $return;
        }
        
        if (! $cron) { // 红包马上下发
            $recode['id'] = $id;
            $res = $this->send_redbag($recode, false);
            return $res;
        } else {
            $return['status'] = 1;
            $return['msg'] = '红包已保存';
            return $return;
        }
    }
    // 查询支付结果
    function query_order($appid, $mch_billno, $mch_id = '')
    {
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($mch_billno)) {
            $return['msg'] = '订单号不能为空';
            return $return;
        }
        
        $param['appid'] = $appid;
        $param['mch_billno'] = $mch_billno;
        $param['bill_type'] = 'MCHT';
        
        $param['mch_id'] = $mch_id;
        $param = $this->init_config($param);
        
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $res_data = post_data($url, $param, 'xml');
        
        return $res_data;
    }

    public function do_send($recode = [], $check_input = true)
    {
        $return['status'] = 0;
        // 预判断是否要发红包，此类错误不记日志
        $res = $this->send_check($recode);
        if ($res['status'] == 0) {
            $return['msg'] = $res['msg'];
            return $return;
        }
        
        if ($check_input) {
            $res = $this->check_input($recode);
            if ($res['status'] == 0) {
                $return['msg'] = $res['msg'];
                $this->update_recode($recode['id'], $return['msg']);
                return $return;
            }
        }
        
        $more_param = unserialize($recode['more_param']);
        foreach ($this->rules as $field => $val) {
            if (isset($more_param[$field])) {
                $param[$field] = $more_param[$field];
            }
        }
        
        $param['wxappid'] = $recode['wxappid'];
        // dump($param);
        // 获取配置信息
        $config = D('common/Publics')->getInfoByAppid($param['wxappid']);
        // dump($config);
        
        // 如果没有商户号，自动从配置中读取
        if (! isset($param['mch_id']) || empty($param['mch_id'])) {
            $param['mch_id'] = $config['mch_id'];
        }
        if (! isset($param['nonce_str']) || empty($param['nonce_str'])) {
            $param['nonce_str'] = uniqid();
        }
        if (! isset($param['client_ip']) || empty($param['client_ip'])) {
            $param['client_ip'] = get_server_ip();
        }
        if (! isset($param['send_name']) || empty($param['send_name'])) {
            $param['send_name'] = $config['public_name']; // 默认使用公众号名
        }
        if (! isset($param['wishing']) || empty($param['wishing'])) {
            $param['wishing'] = '恭喜发财，大吉大利';
        }
        if (! isset($param['act_name']) || empty($param['act_name'])) {
            $param['act_name'] = $config['public_name'] . '的红包';
        }
        if (! isset($param['remark']) || empty($param['remark'])) {
            $param['remark'] = $param['act_name'];
        }
        $param['mch_billno'] = $param['mch_id'] . date('Ymd') . $this->getRandStr(); // mch_id+yyyymmdd+10位一天内不能重复的数字
        $param['re_openid'] = $recode['re_openid'];
        $param['total_amount'] = $recode['total_amount'];
        $param['total_num'] = 1;
        
        $param['sign'] = make_sign($param, $config['partner_key']);
        
        $res = $this->check_param( $param );
        if ($res['status'] == 0) {
            $this->update_recode($recode['id'], $res['msg']);
            return $res;
        }
        // 获取证书路径
        $useCert = get_cert_pem($config);
        if (empty($useCert)) {
            $return['msg'] = '证书获取失败';
            $this->update_recode($recode['id'], $return['msg']);
            // return $return;
        }
        
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $res_data = post_data($url, $param, 'xml', true, $useCert);
        
        $log_param = is_array($param) ? serialize($param) : $param;
        $md5 = md5($url . $log_param);
        
        if (isset($res_data['curl_erron'])) {
            $return['msg'] = $res_data['curl_erron'] . ': ' . $res_data['curl_error'];
            $this->update_recode($recode['id'], $return['msg'], 1, $md5);
            return $return;
        } elseif ($res_data['return_code'] == 'FAIL') {
            $return['msg'] = $res_data['return_msg'];
            $this->update_recode($recode['id'], $return['msg'], 3, $md5);
            return $return;
        } elseif ($res_data['result_code'] == 'FAIL') {
            $return['msg'] = $res_data['err_code'] . ': ' . $res_data['err_code_des'];
            $return['err_code'] = $res_data['err_code'];
            $save = $this->error_deal($res_data['err_code']);
            $this->update_recode($recode['id'], $return['msg'], 3, $md5, $save);
            return $return;
        }
        // 记录
        $this->update_recode($recode['id'], '', 0, $md5);
        $return['status'] = 1;
        $return['msg'] = '红包下发成功，请注意查收';
        return $return;
    }
    // 微信发放规则限制判断
    private function send_check($recode)
    {
        $return['status'] = 0;
        // 等待时间判断
        if (isset($recode['wait_time']) && $recode['wait_time'] > NOW_TIME) {
            $return['msg'] = '还未到发放时间';
            return $return;
        }
        // 有些状态不能重发
        if (isset($recode['status']) && ! in_array($recode['status'], [
            1,
            2,
            4
        ])) {
            $return['msg'] = '该红包不支持重复下发';
            return $return;
        }
        // 1.发送频率限制------默认1800/min
        $key = 'send_check_' . date('YmdHi');
        $check = S($key);
        if ($check === false) {
            S($key, 1, 90);
        } elseif ($check < 1800) {
            $check += 1;
            S($key, $check, 90);
        } else {
            $return['msg'] = '每分钟发送频率超过了1800次';
            return $return;
        }
        
        // 2.发送个数上限------按照默认1800/min算
        // == 目前是一次一个红包，暂不需要个数限制
        
        $return['status'] = 1;
        return [
            'status' => 1,
            'msg' => '判断通过'
        ];
    }
    // 异常处理
    private function error_deal($err_code)
    {
        $wait_time = 0;
        switch ($err_code) {
            case 'SENDNUM_LIMIT': // SENDNUM_LIMIT 该用户今日领取红包个数超过限制 该用户今日领取红包个数超过你在微信支付商户平台配置的上限 如有需要、请在微信支付商户平台【api安全】中重新配置 【每日同一用户领取本商户红包不允许超过的个数】。
                $status = 2;
                $wait_time = 86400; // 延时一天再发
                break;
            case 'SEND_FAILED': // 红包发放失败,请更换单号再重试 该红包已经发放失败 如果需要重新发放，请更换单号再发放
                $status = 2;
                $wait_time = 65; // 延时1分钟多点再发
                break;
            case 'FREQ_LIMIT': // 超过频率限制,请稍后再试 受频率限制 请对请求做频率控制
                $status = 2;
                $wait_time = 180; // 延时3分钟
                break;
            case 'NOTENOUGH': // 帐号余额不足，请到商户平台充值后再重试 账户余额不足 充值后重试
                $status = 2;
                $wait_time = 3600; // 延时一小时
                break;
            case 'PROCESSING': // 请求已受理，请稍后使用原单号查询发放结果 发红包流程正在处理 二十分钟后查询,按照查询结果成功失败进行处理
            case 'SYSTEMERROR': // 请求已受理，请稍后使用原单号查询发放结果 系统无返回明确发放结果 使用原单号调用接口，查询发放结果，如果使用新单号调用接口，视为新发放请求
                $status = 4;
                break;
            case 'NO_AUTH': // NO_AUTH 发放失败，此请求可能存在风险，已被微信拦截 用户账号异常，被拦截 请提醒用户检查自身帐号是否异常。使用常用的活跃的微信号可避免这种情况。
            case 'ILLEGAL_APPID': // 非法appid，请确认是否为公众号的appid，不能为APP的appid 错误传入了app的appid 接口传入的所有appid应该为公众号的appid（在mp.weixin.qq.com申请的），不能为APP的appid（在open.weixin.qq.com申请的）。
            case 'MONEY_LIMIT': // 红包金额发放限制 发送红包金额不再限制范围内 每个红包金额必须大于1元，小于200元
            case 'FATAL_ERROR': // openid和原始单参数不一致 更换了openid，但商户单号未更新 请商户检查代码实现逻辑
            case 'SIGN_ERROR': // 签名错误
            case 'XML_ERROR': // 输入xml参数格式错误 请求的xml格式错误，或者post的数据为空 检查请求串，确认无误后重试
            case 'CA_ERROR': // CA证书出错，请登录微信支付商户平台下载证书 请求携带的证书出错 到商户平台下载证书，请求带上证书后重试
            case '金额和原始单参数不一致': // 更换了金额，但商户单号未更新 请商户检查代码实现逻辑 请检查金额、商户订单号是否正确
            case 'OPENID_ERROR': // openid和appid不匹配 openid和appid不匹配 发红包的openid必须是本appid下的openid
            case 'MSGAPPID_ERROR': // 触达消息给用户appid有误 msgappid与主、子商户号的绑定关系校验失败 检查下msgappid是否填写错误，msgappid需要跟主、子商户号 有绑定关系
            case 'ACCEPTMODE_ERROR': // 主、子商户号关系校验失败 服务商模式下主商户号与子商户号关系校验失败 确认传入的主商户号与子商户号是否有受理关系
            case 'PARAM_ERROR': // 参数验证失败
            default:
                $status = 6;
                break;
        }
        return [
            'status' => $status,
            'wait_time' => $wait_time
        ];
    }
    // 记录失败信息
    private function update_recode($id, $msg = '', $status = 3, $md5 = '', $save = [])
    {
        $map['id'] = $id;
        isset($save['status']) || $save['status'] = $status;
        if (! empty($res_data)) {}
        empty($param) || $save['remark'] = $msg;
        empty($md5) || $save['log_md5'] = $md5;
        
        $this->where( wp_where($map) )->update($save);
    }
    // 检查输入的参数
    private function check_input($data)
    {
        $return['status'] = 0;
        if (empty($data['wxappid'])) {
            $return['msg'] = 'wxappid不能为空';
            return $return;
        }
        if (empty($data['re_openid'])) {
            $return['msg'] = 're_openid不能为空';
            return $return;
        }
        if (empty($data['total_amount'])) {
            $return['msg'] = '红包金额不能为空';
            return $return;
        }
        $return['status'] = 1;
        return $return;
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
        return substr(time(), - 5) . substr(microtime(), 2, 4) . $arr[$key];
    }
    // 数据验证
    private function check_param( $param = [] )
    {
        $return['status'] = 0;
        
        foreach ($this->rules as $key => $val) {
            if (isset($val['require'])) {
                if (! isset($param[$key])) {
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
                if (isset($val['in']) && ! in_array($param[$key], $val['in'])) {
                    $return['msg'] = $key . '的值不正确，可选的值有：' . implode(', ', $val['in']);
                    return $return;
                }
            }
        }
        if ($param['total_amount'] > 20000 && empty($param['scene_id'])) {
            $return['msg'] = '红包金额大于200时，请求参数scene_id必传';
            return $return;
        }
        
        $return['status'] = 1;
        $return['msg'] = '检测通过';
        return $return;
    }
}
