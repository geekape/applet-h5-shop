<?php

namespace app\sms\model;

use app\common\model\Base;

/**
 * Sms模型
 */
class Sms extends Base
{
    var $config;

    //$from_type发送短信的用途 'card':会员卡手机认证
    function sendSms($to)
    {
        $this->config = getAddonConfig('Sms');
        if (strlen($to) != 11) {
            $res['result'] = 0;
            $res['msg'] = "请检查手机号是否填写正确";
        } else {
            if ($this->config ['type'] == 1 || true) { //TODO 目前只支持云之讯
                //云之讯
                $res = $this->_sendUcpassSms($to);
            } else if ($this->config ['type'] == 3) {
                $res = $this->_sendTencentSms($to);
            } else {
                $res = "配置参数出错";
            }
        }
        return $res;
    }

    function checkSms($phone, $code)
    {
        $this->config = getAddonConfig('Sms');
        $map['phone'] = $phone;
        $sms = M('sms')->where($map)->order('id desc')->find();
        if ($sms && $code == $sms['code']) {
            $expire = (int)($this->config['expire']);
            if (NOW_TIME - $sms['cTime'] > $expire) {
                $res['result'] = 0;
                $res['msg'] = "验证码已过期，请重新发送";
            } else {
                $res['result'] = 1;
                $res['msg'] = "验证码成功";
            }
        } else {
            $res['result'] = 0;
            $res['msg'] = "验证失败";
        }
        return $res;
    }

// 短信锁
    private function smsLock($tel, $time = 60)
    {

        // 锁定时间
        $locktime = $time;
        // 创建目录
        if (!is_dir(DATA_PATH . '/smslock/'))
            mkdirs(DATA_PATH . '/smslock/', 0777, true);
        // 锁文件
        $lockfile = DATA_PATH . '/smslock/' . $tel . '.txt';
        // 文件验证时间间隔
        if (file_exists($lockfile)) {
            if ((time() - filemtime($lockfile)) < $locktime) {
                return false;
            } else {
                $k = fopen($lockfile, "a+");
                fwrite($k, ',' . time());
                fclose($k);
            }
        } else {
            touch($lockfile);
        }
        return true;
    }

    //云之讯服务
    function _sendUcpassSms($mobile)
    {
        require_once 'Ucpaas.php';
        //初始化必填
        $options['accountsid'] = $this->config['accountSid'];
        $options['token'] = $this->config['authToken'];
        $ucpass = new \Ucpaas($options);

        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appid = $this->config['appId']; //应用的ID，可在开发者控制台内的短信产品下查看

        $templateid = (int)($this->config['cardTemplateId']); //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
        $param[] = rand(1111, 9999);
        $param[] = $this->config['expire'];
        $uid = '';

        $is_lock = $this->smsLock($mobile);
        if ($is_lock) {
            $resStr = $ucpass->SendSms($appid, $templateid, implode(',', $param), $mobile, $uid);
            $res = json_decode($resStr, true);
        } else {
            $result['result'] = 0;
            $result['msg'] = "获取验证码太频繁";
            return $result;
        }

        if (isset($res['code']) && $res['code'] == "000000") {
            $data['phone'] = $mobile;
            $data['plat_type'] = $this->config['type'];
            //$data['from_type'] = $from_type;
            $data['code'] = $param[0];
            $data['status'] = 0;
            $data['smsId'] = $res['smsid'];
            $data['cTime'] = time();
            $this->insert($data);
            $result['result'] = 1;
            $result['msg'] = "发送成功";
        } else {
            $result['result'] = 0;
            $result['msg'] = "发送失败";
        }
        return $result;
    }

    //阿里云短信
    function _sendAliSms2($to)
    {

        /* $this->endPoint = "http://1608798122171618.mns.cn-shenzhen.aliyuncs.com/"; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
       $this->accessId = "9NEgHXhjtuUKVptt";//"YourAccessId"
       $this->accessKey = "RUxWtrb3DC1GmXj0e8jPgBdwV8vD39"; //YourAccessKey
       $batchSmsAttributes = new BatchSmsAttributes("圆梦云科技", "SMS_72905097");  */
        $signname = "圆梦云科技";
        $templateCode = 'SMS_72905097';
        $recNum = $to;
        $code = rand(pow(10, 3), pow(10, 4) - 1);
        $rand = rand(pow(10, 9), pow(10, 10) - 1);
        $time = date('YYYY-MM-DDThh:mm:ssZ', time());
        $ParamString = json_encode(array('code' => $code));
        $AccessKeyId = "9NEgHXhjtuUKVptt";
        $AccessKeySecret = "RUxWtrb3DC1GmXj0e8jPgBdwV8vD39";

        $url = 'https://sms.aliyuncs.com/';
        $params['Action'] = 'SingleSendSms';//操作接口名，系统规定参数，取值：SingleSendSms
        $params['Format'] = 'JSON';
        $params['AccessKeyId'] = $AccessKeyId;
        $params['ParamString'] = $ParamString;
        $params['RecNum'] = $to;//目标手机号
        $params['SignatureMethod'] = 'HMAC-SHA1';//签名方式
        $params['SignatureNonce'] = time();//唯一随机数
        $params['SignatureVersion'] = '1.0';//签名算法版本，目前版本是1.0
        $params['SignName'] = $signname;//管理控制台中配置的短信签名（状态必须是验证通过）
        $params['TemplateCode'] = $templateCode;//管理控制台中配置的审核通过的短信模板的模板CODE（状态必须是验证通过）
        $params['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");//请求的时间戳。日期格式按照ISO8601标准表示，
        //并需要使用UTC时间。格式为YYYY-MM-DDThh:mm:ssZ
        $params['Version'] = '2016-09-27';//API版本号，当前版本2016-09-27
        ksort($params);
        $PostData = '';
        $para = '';

        foreach ($params as $k => $v) {
            $PostData .= $this->percentEncode($k) . '=' . $this->percentEncode($v) . '&';
            $para .= $k . '=' . $v . '&';
        }
        $params['Signature'] = $this->percentEncode(base64_encode(hash_hmac('sha1', 'GET&%2F&' . $this->percentEncode(substr($PostData, 0, -1)), $AccessKeySecret . '&', true)));
        $PostData .= 'Signature=' . $params['Signature'];
        //$params['Signature'] = make_sign($params,$AccessKeySecret);
        //dump($params);
        $url = 'https://sms.aliyuncs.com?' . $PostData;
        //$res = post_data($url, $params);
        //dump($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $res = curl_exec($ch);
        if (!$res) {
            //dump(curl_error($ch));
            //dump($res);exit;
            return curl_error($ch);
            echo 0;
        } else {
            //dump($res);
            exit;
        }
    }

    function _sendTencentSms($to)
    {//腾讯云短信
        //$to是你要发送的手机号码
        //导入sdk
        require_once VENDOR_PATH . "SmsSender.php";
        //获取你的后台配置的那些参数
        $this->config = D('Common/AddonConfig')->get('Sms');
        $sign = $this->config['sign'];
        $templId = $this->config['TtemplateId'];
        // 请根据实际 appid 和 appkey 进行开发，以下只作为演示 sdk 使用
        //$appid = 1400034293;
        //$appkey = "27bef0f7d2d56e8bac6d3ce239d701dd";
        $appid = $this->config['appid'];
        $appkey = $this->config['appkey'];
        try {

            $singleSender = new \SmsSender\SmsSingleSender($appid, $appkey);
            //四位验证码
            $code = rand(0000, 9999);
            $params[] = $code;
            $result = $singleSender->sendWithParam("86", $to, $templId, $params, $sign, "", "");
            $rsp = json_decode($result, true);
            //这里的data是我用来保存发送记录的
            $data['phone'] = $to;
            $data['plat_type'] = $this->config['type'];
            //$data['from_type'] = $from_type;
            $data['code'] = $code;
            $data['status'] = 0;
            $data['smsId'] = $rsp['sid'];
            $data['cTime'] = time();
            $this->insert($data);
            $res['result'] = 1;
            $res['msg'] = "发送成功";

        } catch (\Exception $e) {
            $res['result'] = 0;
            $res['msg'] = "发送失败";
        }

        return $res;
    }


    function getSign()
    {//腾讯云获取签名
        //生成sig
        $appid = 1400034293;
        $appkey = "27bef0f7d2d56e8bac6d3ce239d701dd";
        $strRand = rand(pow(10, 9), pow(10, 10) - 1); //url中的random字段的值
        $param['time'] = $strTime = time(); //unix时间戳
        $param['sig'] = $sig = hash('sha256', 'appkey=' . $appkey . '&random=' . $strRand . '&time=' . $strTime);
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/add_sign?sdkappid=' . $appid . '&random=' . $strRand;

        $param['remark'] = '';
        $param['text'] = '圆梦云科技';

        $param = json_encode($param, JSON_UNESCAPED_UNICODE);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $res = curl_exec($ch);

        if (!$res) {
            return curl_error($ch);
        } else {
            $res = json_decode($res, true);
            if ($res['result'] == 0) {
                //dump($res['data']['text']);exit;
                return $res['data']['text'];
            } else {
                //dump($res);exit;
                return false;
            }
        }


    }

    function getTemplate()
    {//腾讯云获取模板
        $appid = 1400034293;
        $appkey = "27bef0f7d2d56e8bac6d3ce239d701dd";
        $strRand = rand(pow(10, 9), pow(10, 10) - 1); //url中的random字段的值
        $param['time'] = $strTime = time(); //unix时间戳
        $param['sig'] = $sig = hash('sha256', 'appkey=' . $appkey . '&random=' . $strRand . '&time=' . $strTime);
        $param['title'] = '';
        $param['text'] = '您好，您的验证码是{1},请于10分钟内输入正确的验证码';
        $param['remark'] = '';
        $param['type'] = 0;
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/add_template?sdkappid=' . $appid . '&random=' . $strRand;
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

        $res = curl_exec($ch);
        if (!$res) {
            //return curl_error($ch);
            $err = curl_error($ch);
            echo $err;
        } else {
            $res = json_decode($res, true);
            if ($res['result'] == 0) {
                //return $res['data']['id'];
                $id = $res['data']['id'];
                echo $id;
            } else {
                return false;
            }
        }

    }

    function _sendAliSms($to)
    {//
        include_once VENDOR_PATH . 'alisms/php/api_sdk/Dysmsapi/Request/aliSms/QuerySendDetailsRequest.php';
        include_once VENDOR_PATH . 'alisms/php/api_sdk/core/Profile/DefaultProfile.php';
        include_once VENDOR_PATH . 'alisms/php/api_sdk/core/DefaultAcsClient.php';
        //此处需要替换成自己的AK信息
        $accessKeyId = "9NEgHXhjtuUKVptt";
        $accessKeySecret = "RUxWtrb3DC1GmXj0e8jPgBdwV8vD39";
        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        $profile = \DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient = new \DefaultAcsClient($profile);

        $request = new SendSmsRequest;
        //必填-短信接收号码
        $request->setPhoneNumbers($to);
        //必填-短信签名
        $request->setSignName("圆梦云科技");
        //必填-短信模板Code
        $request->setTemplateCode("SMS_72905097");
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $code = rand(1000, 9999);
        $para = json_encode(array('code' => $code));
        $request->setTemplateParam($para);
        //选填-发送短信流水号
        $request->setOutId("1234");

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        //var_dump($acsResponse);
    }

    function percentEncode($str)
    {
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}
