<?php
namespace app\common\model;

use app\common\model\Base;

// Weixin模型
class Transfer extends Base
{

    protected $table = DB_PREFIX . 'redbag_recode';

    public function initialize()
    {
        parent::initialize();
        $this->config = $this->get_config();
    }

    // 支付字段限制规则
    private $rules = [
        'mch_appid' => [
            'require' => 1
        ],
        'mchid' => [
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
        'partner_trade_no' => [
            'require' => 1
        ],
        'openid' => [
            'require' => 1
        ],
        'check_name' => [
            'require' => 1,
            'in' => [
                'NO_CHECK',
                'FORCE_CHECK',
                'OPTION_CHECK'
            ]
        ],
        're_user_name' => [],
        'amount' => [
            'require' => 1
        ],
        'desc' => [
            'require' => 1
        ],
        'spbill_create_ip' => [
            'require' => 1,
            'len' => 32
        ]
    ];

    /*
     * var $config = [
     * 'wxappid' => 'wxb4d1ab77291e77e3',
     * 'secert' => 'fdf36cf3c9cc16b6d6e030496d04f9aa',
     * 'mch_id' => '1400015202',
     * 'public_name' => '雅式展览服务',
     * 'partner_key' => 'TheAdsaleGroup1978TheJavaRoad321'
     * ];
     */
    var $config = [];

    function get_config($wpid = 0)
    {
        $wpid = $wpid > 0 ? $wpid : get_wpid();
        if (empty($wpid)) {
            return false;
        }
        $data = D('Common/Publics')->getInfo($wpid);
        $config['wxappid'] = $data['appid'];
        $config['secret'] = $data['secret'];
        $config['mch_id'] = $data['mch_id'];
        $config['public_name'] = $data['public_name'];
        $config['partner_key'] = $data['partner_key'];
        $config['cert_pem'] = $data['cert_pem'];
        $config['key_pem'] = $data['key_pem'];
        return $config;
    }

    // 现金支付
    public function add_pay($openid, $money, $more_param = [], $cron = true)
    {
        $return['status'] = 0;
        
        $recode1['mch_appid'] = $recode['wxappid'] = isset($more_param['wxappid']) ? $more_param['wxappid'] : $this->config['wxappid'];
        $recode1['openid'] = $recode['re_openid'] = $openid;
        $recode1['amount'] = $recode['total_amount'] = $money;
        $res = $this->check_input($recode1);
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
        $recode['mch_id'] = isset($more_param['mch_id']) ? $more_param['mch_id'] : $this->config['mch_id'];
        $recode['mch_billno'] = $recode['mch_id'] . date('Ymd') . $this->getRandStr(); // mch_id+yyyymmdd+10位一天内不能重复的数字
                                                                                       // $this->allowField ( true )->isUpdate ( false )->data ( $recode )->save ();
                                                                                       // $id = $this->id;
        $recode['wpid'] = get_wpid();
        $recode['uid'] = isset($more_param['uid']) ? $more_param['uid'] : get_mid();
        $id = $this->insertGetId($recode);
        if (! $id) {
            $return['msg'] = '支付记录保存到数据库失败';
            return $return;
        }
        
        if (! $cron) { // 支付马上下发
            $recode['id'] = $id;
            $res = $this->do_send($recode, false);
            $res['id'] = $id;
            return $res;
        } else {
            $return['status'] = 1;
            $return['msg'] = '支付已保存';
            $return['id'] = $id;
            return $return;
        }
    }

    // 查询支付结果
    function query_order($appid, $partner_trade_no, $mch_id = '')
    {
        $return['status'] = 0;
        if (empty($appid)) {
            $return['msg'] = 'appid不能为空';
            return $return;
        }
        if (empty($partner_trade_no)) {
            $return['msg'] = '订单号不能为空';
            return $return;
        }
        
        $param['appid'] = $appid;
        $param['partner_trade_no'] = $partner_trade_no;
        
        $param['mch_id'] = $mch_id;
        $param = $this->init_config($param);
        
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $res_data = post_data($url, $param, 'xml');
        
        return $res_data;
    }

    // 支付配置信息初始化
    private function init_config($param = [], $need_key = false)
    {
        // 如果连appid的值都没有，肯定有错，不再处理
        if (! isset($param['appid']) || empty($param['appid'])) {
            return $param;
        }
        
        // 获取配置信息
        $config = D('common/Publics')->getInfoByAppid($param['appid']);
        
        // 如果没有商户号，自动从配置中读取
        if (! isset($param['mch_id']) || empty($param['mch_id'])) {
            $param['mch_id'] = $config['mch_id'];
        }
        
        if (! isset($param['nonce_str']) || empty($param['nonce_str'])) {
            $param['nonce_str'] = uniqid();
        }
        
        $param['sign'] = make_sign($param, $config['partner_key']);
        $need_key && $param['partner_key'] = $config['partner_key'];
        
        return $param;
    }

    public function do_send($recode = [], $check_input = true)
    {
        $config = $this->get_config($recode['wpid']);
        $return['status'] = 0;
        // 预判断是否要发支付，此类错误不记日志
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
       
        $recode['wxappid'] = isset($more_param['mch_appid']) ? $more_param['mch_appid'] : $config['wxappid'];
        $param['mch_appid'] = $recode['wxappid'];
        
        // 获取配置信息
        // $config = D('common/Publics')->getInfoByAppid($param['mch_appid']);
        // dump($config);
        
        // 如果没有商户号，自动从配置中读取
        if (! isset($param['mchid']) || empty($param['mchid'])) {
            $param['mchid'] = $config['mch_id'];
        }
        if (! isset($param['nonce_str']) || empty($param['nonce_str'])) {
            $param['nonce_str'] = uniqid();
        }
        if (! isset($param['spbill_create_ip']) || empty($param['spbill_create_ip'])) {
            $param['spbill_create_ip'] = get_server_ip();
        }
        if (! isset($param['check_name']) || empty($param['check_name'])) {
            $param['check_name'] = 'NO_CHECK';
        }
        if (! isset($param['desc']) || empty($param['desc'])) {
            $publicinfo = get_pbid_appinfo();//get_pbid_appinfo($recode['wpid']);
            $param['desc'] = $publicinfo['public_name'] . '的支付';
        }
        
        $param['partner_trade_no'] = $recode['mch_billno'];
        $param['openid'] = $recode['re_openid'];
        $param['amount'] = $recode['total_amount'];
        
        $param['sign'] = make_sign($param, $config['partner_key']);
        
        $res = $this->check_param($param);
        if ($res['status'] == 0) {
            $this->update_recode($recode['id'], $res['msg']);
            return $res;
        }
        // 获取证书路径
        $useCert = get_cert_pem($config);
        if (empty($useCert)) {
            $return['msg'] = '证书获取失败';
            $this->update_recode($recode['id'], $return['msg']);
            return $return;
        }
        
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        addWeixinLog($useCert, 'sendpay_cert');
        $res_data = $this->post_data($url, $param, 'xml', true, $useCert);
        addWeixinLog($res_data, 'sendpay_data_' . $recode['id']);
        
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
        $return['msg'] = '支付下发成功，请注意查收';
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
            $return['msg'] = '该支付不支持重复下发';
            return $return;
        }
        
        /*
         * 接口调用规则：
         * ◆ 给同一个实名用户付款，单笔单日限额2W/2W
         * ◆ 不支持给非实名用户打款
         * ◆ 一个商户同一日付款总额限额100W
         * ◆ 单笔最小金额默认为1元
         * ◆ 每个用户每天最多可付款10次，可以在商户平台--API安全进行设置 ---不判断此项
         * ◆ 给同一个用户付款时间间隔不得低于15秒
         */
        if ($recode['total_amount'] > 2000000) {
            $return['msg'] = '给同一个实名用户付款，单笔限额2W';
            return $return;
        }
        if ($recode['total_amount'] < 100) {
            $return['msg'] = '单笔最小金额默认为1元';
            return $return;
        }
        $key = 'send_check_appid_' . $recode['wxappid'];
        $check = S($key);
        if ($check === false) {
            S($key, $recode['total_amount'], 86400);
        } else {
            $amount = $check + $recode['total_amount'];
            if ($amount > 100000000) {
                $return['msg'] = '一个商户同一日付款总额限额100W';
                return $return;
            } else {
                S($key, $amount, 86400);
            }
        }
        
        $key = 'send_check_openid_' . $recode['re_openid'];
        $check = S($key);
        if ($check === false) {
            S($key, [
                'time' => NOW_TIME,
                'amount' => $recode['total_amount']
            ], 86400);
        } else {
            $amount = $check['amount'] + $recode['total_amount'];
            if (($check['time'] + 15) >= NOW_TIME) {
                $return['msg'] = '给同一个用户付款时间间隔不得低于15秒';
                return $return;
            } elseif ($amount > 2000000) {
                $return['msg'] = '给同一个实名用户付款，单日限额2W';
                return $return;
            } else {
                S($key, [
                    'time' => NOW_TIME,
                    'amount' => $amount
                ], 86400);
            }
        }
        
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
            case 'NOTENOUGH': // 帐号余额不足
                $status = 2;
                $wait_time = 3600; // 延时一小时再发
                break;
            case 'SYSTEMERROR': // 系统繁忙，请稍后再试。
                $status = 2;
                $wait_time = 65; // 延时1分钟多点再发
                break;
            case 'NOAUTH': // 没有授权请求此api
            case 'AMOUNT_LIMIT': // 付款金额不能小于最低限额
            case 'PARAM_ERROR': // 参数缺失，或参数格式出错，参数不合法等
            case 'OPENID_ERROR': // Openid格式错误或者不属于商家公众账号
            case 'NAME_MISMATCH': // 请求参数里填写了需要检验姓名，但是输入了错误的姓名
            case 'SIGN_ERROR': // 没有按照文档要求进行签名
            case 'XML_ERROR': // Post请求数据不是合法的xml格式内容
            case 'FATAL_ERROR': // 两次请求商户单号一样，但是参数不一致
            case 'CA_ERROR': // 请求没带证书或者带上了错误的证书
            case 'V2_ACCOUNT_SIMPLE_BAN': // 用户微信支付账户未知名，无法付款
            default:
                $status = 3;
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
        // $map['id'] = $id;
        isset($save['status']) || $save['status'] = $status;
        empty($msg) || $save['remark'] = $msg;
        empty($md5) || $save['log_md5'] = $md5;
        $res = $this->where('id', $id)->update($save);
        // $this->where($map)->update($save);
    }

    // 检查输入的参数
    private function check_input($data)
    {
        $return['status'] = 0;
        if (empty($data['mch_appid'])) {
            $return['msg'] = 'mch_appid不能为空';
            return $return;
        }
        if (empty($data['openid'])) {
            $return['msg'] = 'openid不能为空';
            return $return;
        }
        if (empty($data['amount'])) {
            $return['msg'] = '支付金额不能为空';
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
    private function check_param($param = [])
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
        if ($param['amount'] > 20000 && empty($param['scene_id'])) {
            $return['msg'] = '支付金额大于200时，请求参数scene_id必传';
            return $return;
        }
        
        $return['status'] = 1;
        $return['msg'] = '检测通过';
        return $return;
    }

    // 以POST方式提交数据
    function post_data($url, $param, $type = 'json', $return_array = true, $useCert = [])
    {
        if ($type == 'json' && is_array($param)) {
            $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        } elseif ($type == 'xml' && is_array($param)) {
            $param = ToXml($param);
        }
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if (class_exists('/CURLFile')) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD') && version_compare(phpversion(), '7.0') < 0) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        // 设置header
        if ($type == 'file') {
            $header[] = "content-type: multipart/form-data; charset=UTF-8";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        } elseif ($type == 'xml') {
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else {
            $header[] = "content-type: application/json; charset=UTF-8";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 使用证书：cert 与 key 分别属于两个.pem文件
        if (isset($useCert['certPath']) && isset($useCert['keyPath'])) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $useCert['certPath']);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $useCert['keyPath']);
        }
        
        $res = curl_exec($ch);
        $flat = curl_errno($ch);
        
        $msg = '';
        if ($flat) {
            $msg = curl_error($ch);
        }
        // add_request_log($url, $param, $res, $flat, $msg);
        
        if ($flat) {
            return [
                'curl_erron' => $flat,
                'curl_error' => $msg
            ];
        } else {
            if ($return_array && ! empty($res)) {
                $res = $type == 'json' ? json_decode($res, true) : FromXml($res);
            }
            
            return $res;
        }
    }
}
