<?php
namespace app\public_bind\controller;

use app\common\controller\WebBase;

class PublicBind extends WebBase
{

    public function setTicket()
    {
        require_once(env('vendor_path') . 'WXBiz/wxBizMsgCrypt.php');
        // 第三方发送消息给公众平台
        $encodingAesKey = config('ENCODING_AES_KEY');
        $token = SYSTEM_TOKEN;
        $appId = config('COMPONENT_APPID');
        
        $timeStamp = empty($_GET['timestamp']) ? "" : trim($_GET['timestamp']);
        $nonce = empty($_GET['nonce']) ? "" : trim($_GET['nonce']);
        $msg_sign = empty($_GET['msg_signature']) ? "" : trim($_GET['msg_signature']);
        $encryptMsg = file_get_contents('php://input');
        
        addWeixinLog($encryptMsg, 'setTicket');
        addWeixinLog($_GET, 'setTicket_GET');
        $pc = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        
        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $encryptMsg, $msg);
        if ($errCode == 0) {
            $data = $this->_xmlToArr($msg);
            addWeixinLog($data, 'setTicket_data');
            if (isset($data['ComponentVerifyTicket'])) {
                $map['name'] = 'public_bind';
                $config = M('apps')->where(wp_where($map))->value('config');
                $config = (array) json_decode($config, true);
                
                $config['ComponentVerifyTicket'] = $data['ComponentVerifyTicket'];
                $save['config'] = json_encode($config);
                M('apps')->where(wp_where($map))->update($save);
            } elseif ($data['InfoType'] == 'unauthorized') {
                // 在公众号后台取消授权后，同步把系统里的公众号删除掉，并更新相关用户缓存
                $map['appid'] = $data['AuthorizerAppid'];
                $publics = M('publics')->where(wp_where($map))->find();
                if (isset($publics['id'])) {
                    D('common/User')->where('wpid', $publics['id'])->setField('wpid', 0);
                    D('common/User')->getUserInfo($publics['uid'], true);
                }
            }
            
            echo 'success';
        } else {
            addWeixinLog('解密后失败：' . $errCode, 'setTicket_error');
        }
    }

    public function bind()
    {
        $res = D('public_bind/PublicBind')->bind();
        if (! $res['status']) {
            $this->error($res['msg']);
            exit();
        }
        $this->assign('jumpURL', $res['jumpURL']);
        
        return $this->fetch();
    }

    // 用户授权后,获取公众号信息、或小程序信息
    public function after_auth()
    {
        // auth_code=xxx&expires_in=600
        $uid = intval(session('mid_'.get_pbid()));
        if (empty($uid)) {
            $this->error('用户信息不正确');
        }
        $auth_code = I('auth_code');
        $auth_info = D('public_bind/PublicBind')->getAuthInfo($auth_code);
        $public_info = D('public_bind/PublicBind')->getPublicInfo($auth_info['authorization_info']['authorizer_appid']);
        if (isset($public_info['authorizer_info']['MiniProgramInfo']) && ! empty($public_info['authorizer_info']['MiniProgramInfo'])) {
            // 小程序
            $data['app_type'] = 1;
        } else {
            // 公众号
            $data['app_type'] = 0;
        }
        $map['public_id'] = $data['public_id'] = $public_info['authorizer_info']['user_name'];
        $data['public_name'] = $public_info['authorizer_info']['nick_name'] ? $public_info['authorizer_info']['nick_name'] : $public_info['authorizer_info']['user_name'];
        $data['wechat'] = $public_info['authorizer_info']['alias'];
        $data['headface_url'] = $public_info['authorizer_info']['head_img'];
        if ($public_info['authorizer_info']['service_type_info']['id'] > 0) {
            // 服务号
            $data['type'] = 2;
        } else {
            // 订阅号
            $data['type'] = 0;
        }
        if ($public_info['authorizer_info']['verify_type_info']['id'] != - 1) {
            // 已认证
            $data['type'] += 1;
        }
        $data['appid'] = $public_info['authorization_info']['authorizer_appid'];
        $data['uid'] = $uid;
        
        $data['is_bind'] = 1;
        $data['encodingaeskey'] = config('ENCODING_AES_KEY');
        $data['cTime'] = NOW_TIME;
        $data['authorizer_refresh_token'] = $auth_info['authorization_info']['authorizer_refresh_token'];
        
        $info = M('publics')->where(wp_where($map))->find();
        if ($info) {
            M('publics')->where(wp_where($map))->update($data);
            D('common/Publics')->clearCache($info['id']);
            $pbid = $info['id'];
        } else {
            $pbid = M('publics')->insertGetId($data);
        }
        $old_uid = D('common/User')->where('wpid', $pbid)->value('uid');
        if ($old_uid > 0 && $uid != $old_uid) {
            // 切换了用户
            D('common/User')->where('uid', $old_uid)->setField('wpid', 0);
            D('common/User')->clearCache($old_uid);
        }
        
        D('common/User')->where('uid', $uid)->setField('wpid', $pbid);
        D('common/User')->clearCache($uid);
        
        $url = U('weixin/publics/lists');
        
        $key1 = 'pre_auth_code';
        S($key1, null);
        // 授权完成，进入平台
        return redirect($url);
    }

    public function _xmlToArr($xml)
    {
        $res = @simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $res = json_decode(json_encode($res), true);
        return $res;
    }
}
