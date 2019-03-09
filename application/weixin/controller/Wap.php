<?php

namespace app\weixin\controller;

use app\common\controller\WapBase;

// 手机H5版的控制器
class Wap extends WapBase
{
    function vue_login()
    {
        if (isWeixinBrowser()) {
            $openid = get_openid();

            if (!empty($openid) && !is_numeric($openid) && $this->mid > 0 && empty($GLOBALS['myinfo']['nickname'])) {
                cookie('__forward__', $_SERVER ['REQUEST_URI']);
                return $this->redirect(U('bind'));
            }
        } else { //预览的情况下
            $openid = '-3';
        }

        $this->assign('openid', $openid);
        $this->assign('uid', $this->mid);
        $this->assign('sid', session_id());

        $pbid = get_pbid();
        $this->assign('pbid', $pbid);

        $info = get_pbid_appinfo($pbid);
        $this->assign('public_name', $info['public_name']);


        return $this->fetch();
    }

    // 一键绑定
    function bind()
    {
        if ((defined('IN_WEIXIN') && IN_WEIXIN) || isset($_GET['is_stree']) || !config('USER_OAUTH')) {
            return false;
        }

        $isWeixinBrowser = isWeixinBrowser();
        if (!$isWeixinBrowser) {
            $this->error('请在微信里打开');
        }
        $info = get_pbid_appinfo();
        $param['appid'] = $info['appid'];
        $callback = U('bind');
        $state = input('state','');
        if ($state != 'weiphp') {
            $param['redirect_uri'] = $callback;
            $param['response_type'] = 'code';
            $param['scope'] = 'snsapi_userinfo';
            $param['state'] = 'weiphp';
            $info['is_bind'] && $param['component_appid'] = config('COMPONENT_APPID');
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($param) . '#wechat_redirect';
            header('Location: ' . $url);
            exit();
        } elseif ($state == 'weiphp') {
            if (empty($_GET['code'])) {
                exit('code获取失败');
            }

            $param['code'] = I('code');
            $param['grant_type'] = 'authorization_code';

            if ($info['is_bind']) {
                $param['appid'] = I('appid');
                $param['component_appid'] = config('COMPONENT_APPID');
                $param['component_access_token'] = D('public_bind/PublicBind')->_get_component_access_token();

                $url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?' . http_build_query($param);
            } else {
                $param['secret'] = $info['secret'];

                $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
            }

            $content = wp_file_get_contents($url);
            $content = json_decode($content, true);
            if (!empty($content['errmsg'])) {
                exit($content['errmsg']);
            }

//             $suburl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . get_access_token() . '&openid=' . $content['openid'] . '&lang=zh_CN';
            $suburl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $content['access_token'] . '&openid=' . $content['openid'] . '&lang=zh_CN';
            $data = wp_file_get_contents($suburl);
            $data = json_decode($data, true);
            $subscribe = isset($data['subscribe'])?$data['subscribe']:0;

            if (!empty($data['errmsg'])) {
                exit($data['errmsg']);
            }

            $data['status'] = 2;
            empty($data['headimgurl']) && $data['headimgurl'] = ADDON_PUBLIC_PATH . '/default_head.png';

            $uid = D('common/Follow')->init_follow($content['openid'], $info['id']);
            D('common/User')->updateInfo($uid, $data);
            if ($subscribe) {
                D('common/Follow')->set_subscribe($content['openid'], 1);
            }

            $url = cookie('__forward__');
            if ($url) {
                cookie('__forward__', null);
            } else {
                $url = U('user_center');
            }

            return redirect($url);
        }
        
    }

    // 绑定领奖信息
    function bind_prize_info()
    {
        // dump ( $this->mid );
        $map['id'] = $this->mid;
        // dump($this->mid);
        if (IS_POST) {
            $data['mobile'] = I('mobile');
            $data['truename'] = I('truename');

            D('common/Follow')->updateInfo($this->mid, $data);

            $url = cookie('__forward__');
            if ($url) {
                cookie('__forward__', null);
            } else {
                $url = U('userCenter');
            }

            return redirect($url);
        } else {
            $info = get_followinfo($this->mid);
            $this->assign('info', $info);
            $this->assign('meta_title', '领奖联系信息');
            return $this->fetch();
        }
    }

    // 第一步绑定手机号
    function bind_mobile()
    {
        if (IS_POST) {
            $map['mobile'] = I('mobile');
            $dao = D('common/Follow');
            // 判断是否已经注册过
            $user = $dao->where(wp_where($map))->find();
            if (!$user) {
                $uid = $dao->init_follow_by_mobile($map['mobile']);
                $dao->where(wp_where($map))->setField('status', 0);

                $user = $dao->where(wp_where($map))->find();
            }

            $map2['openid'] = get_openid();
            if ($map2['openid'] != -1) {
                $map2['pbid'] = get_pbid();
                $uid2 = M('public_follow')->where(wp_where($map2))->value('uid');
                if (!$uid2) {
                    $map2['mobile'] = $map['mobile'];
                    $map2['uid'] = $user['id'];
                    M('public_follow')->insert($map2);
                }
            } else {
                $uid = M('public_follow')->where(wp_where($map))->value('uid');
                if (!$uid) {
                    $data['mobile'] = $map['mobile'];
                    $data['uid'] = $user['id'];
                    M('public_follow')->insert($data);
                }
            }

            session('mid_' . get_pbid(), $user['id']);

            if ($user['status'] == 1) {
                $url = cookie('__forward__');
                if ($url) {
                    cookie('__forward__', null);
                } else {
                    $url = U('userCenter');
                }
            } else {
                $url = U('bind_info');
            }

            $this->success('绑定成功', $url);
        } else {
            if ($this->mid > 0) {
                return redirect(U('userCenter'));
            }
            $this->assign('meta_title', '绑定手机');
            return $this->fetch();
        }
    }

    // 第二步初始化资料
    function bind_info()
    {
        $model = $this->getModel('user');

        if (IS_POST) {
            $map['id'] = $this->mid;
            $url = cookie('__forward__');
            if ($url) {
                cookie('__forward__', null);
            } else {
                $url = U('userCenter');
            }

            $save['nickname'] = I('nickname');
            $save['sex'] = I('sex');
            $save['email'] = I('email');
            $save['status'] = 2;

            $res = D('common/User')->updateInfo($this->mid, $save);
            if ($res) {
                $this->success('保存成功！', $url);
            } else {
                $this->error(D('common/User')->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            // dump($fields);
            $fieldArr = array(
                'nickname',
                'sex',
                'email'
            ); // headimgurl
            foreach ($fields as $k => $vo) {
                if (!in_array($vo['name'], $fieldArr)) {
                    unset($fields[$k]);
                }
            }

            // 获取数据
            $data = M($model['name'])->where('id', $this->mid)->find();

            $this->assign('fields', $fields);
            $this->assign('data', $data);

            $this->assign('meta_title', '填写资料');
            return $this->fetch();
        }
    }

    /**
     * 显示微信用户列表数据
     */
    function userCenter()
    {
        // dump ( $this->mid );
        if ($this->mid < 0) {
            $forward = cookie('__forward__');
            empty($forward) && cookie('__forward__', $_SERVER['REQUEST_URI']);
            return redirect(U('bind'));
        }
        // 商城版的直接在商城个人中心里
        if (is_install('Shop')) {
            return redirect(U('Shop/Wap/center'));
        }

        $info = get_followinfo($this->mid);
        $this->assign('info', $info);
        // dump ( $info );

        // 插件扩展
        $dirs = array_map('basename', glob(env('app_path') . '*', GLOB_ONLYDIR));
        if ($dirs === false || !file_exists(env('app_path'))) {
            $this->error('插件目录不可读或者不存在');
        }

        $arr = [];
        $_REQUEST['doNotInit'] = 1;
        foreach ($dirs as $d) {
            require_once env('app_path') . $d . '/model/WeixinAddonModel.class.php';
            $model = D('' . $d . '/WeixinAddon');
            if (!method_exists($model, 'personal')) {
                continue;
            }

            $lists = $model->personal();
            if (empty($lists) || !is_array($lists)) {
                continue;
            }

            if (isset($lists['url'])) {
                $arr[] = $lists;
            } else {
                $arr = array_merge($arr, $lists);
            }
        }

        foreach ($arr as $vo) {
            if (empty($vo['group'])) {
                $default_link[] = $vo;
            } else {
                $list_data[$vo['group']][] = $vo;
            }
        }
        $this->assign('default_link', $default_link);
        $this->assign('list_data', $list_data);

        // 会员页
        return $this->fetch();
    }

    // 积分记录
    function credit()
    {
        $model = $this->getModel('credit_data');

        $map['wpid'] = get_wpid();
        session('common_condition', $map);

        return parent::common_lists($model, 'common@base/lists');
    }

    function storeCenter()
    {
        if (!is_login()) {
            $forward = cookie('__forward__');
            empty($forward) && cookie('__forward__', $_SERVER['REQUEST_URI']);
            return redirect(U('home/user/login', array(
                'from' => 2
            )));
        }

        $this->mid = 382;
        $info = get_userinfo($this->mid);
        $this->assign('info', $info);
        // dump ( $info );

        // 取优惠券
        $map['shop_uid'] = $this->mid;
        $list = M('coupon')->where(wp_where($map))->select();
        $this->assign('coupons', $list);
        // dump($list);

        // 商家中心
        return $this->fetch();
    }

    // 检查公众号基础功能
    function check()
    {
        $info = M('publics')->where('id', PBID)->find();
        $type = $info['type'];

        // 获取微信权限节点
        $map2['type_' . $type] = 1;
        $auth = M('public_auth')->where(wp_where($map2))->column('title', 'name');

        $res['msg'] = '';
        // 获取access_token
        $access_token = get_access_token(PBID);
        if (empty($access_token)) {
            addAutoCheckLog('access_token', 'access_token获取失败', PBID);
        } else {
            addAutoCheckLog('access_token', '', PBID);
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $access_token;
        $res = wp_file_get_contents($url);
        $res = json_decode($res, true);
        if (isset($res['errcode']) && $res['errcode'] == '40001') {
            addAutoCheckLog('access_token_check', $res['errcode'] . ': ' . $res['errmsg'], PBID);
        } else {
            addAutoCheckLog('access_token_check', '', PBID);
        }

        // 收发消息
        $xml = '<xml><ToUserName><![CDATA[' . $info['public_id'] . ']]></ToUserName>
<FromUserName><![CDATA[oikassyZe6bdupvJ2lq-majc_rUg]]></FromUserName>
<CreateTime>1464254617</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[自动检测]]></Content>
<MsgId>6288925693437624122</MsgId>
</xml>';

        $param['id'] = $info['id'];
        $param['signature'] = 'd3e1ce50d26db638e6a03e1c8bf6b23b4fdbdd87';
        $param['timestamp'] = '1464254754';
        $param['nonce'] = '407622025';
        $param['access_token'] = get_access_token(PBID);
        $url = U('home/weixin/index', $param);
        // echo $url;
        $res = $this->curl_data($url, $xml);
        if (strpos($res, 'auto_check')) {
            addAutoCheckLog('massage', '', PBID);
        } else {
            addAutoCheckLog('massage', '收发消息失败', PBID);
        }

        $nextUrl = U('check2');
        $this->assign('nextUrl', $nextUrl); // exit;
        return $this->fetch();
    }

    function check2()
    {
        // get_openid
        $callback = GetCurUrl();
        $openid = OAuthWeixin($callback, PBID, true);
        // dump($openid);
        if (empty($openid) || $openid == '-1' || $openid == '-2') {
            addAutoCheckLog('openid', '获取openid失败', PBID);
        } else {
            addAutoCheckLog('openid', '', PBID);
        }

        addAutoCheckLog('jsapi', '', PBID);

        return $this->fetch();
    }

    function check3()
    {
        $msg = I('msg');
        addAutoCheckLog('jsapi', $msg, PBID);
    }

    function curl_data($url, $param)
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        $ch = curl_init();
        if (class_exists('/CURLFile')) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        addWeixinLog($res, 'post_data res');
        $flat = curl_errno($ch);
        if ($flat) {
            $data = curl_error($ch);
            addWeixinLog($flat, 'post_data flat');
            addWeixinLog($data, 'post_data msg');
        }

        curl_close($ch);

        return $res;
    }

    function check_res_ajax()
    {
        $map['wpid'] = PBID;
        $list = M('public_check')->where(wp_where($map))
            ->order('id asc')
            ->select();
        foreach ($list as $vo) {
            $res[$vo['na']]['msg'] = $vo['msg'];
        }

        if (empty($res)) {
            echo 0;
        } else {
            echo json_encode($res);
        }
    }
}
