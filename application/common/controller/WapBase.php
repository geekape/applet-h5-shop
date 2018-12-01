<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\common\controller\Base;

/**
 * 手机H5版的控制器基类，实现手机端的初始化，权限控制和一些通用方法
 *
 * @author 凡星
 *
 */
class WapBase extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize()
    {
        parent::initialize();
        
        $info = $this->initPublic();
        $this->initUser($info);
    }

    public function _empty($name)
    {
        $mid = session('mid_' . get_pbid());
        $this->apiModel->setMid($mid);
        $data = $this->apiModel->$name();
        
        $debug = input('debug');
        if ($debug == 1) {
            dump(IS_AJAX);
            dump($data);
        }
        if (IS_AJAX || input('?is_ajax')) {
            return is_numeric($data) || is_string($data) ? $data : json($data);
        } else {
            if (is_object($data)) { // 跳转
                return $data;
            }
            
            if (isset($data['code'])) {
                if ($data['code'] == 0) {
                    $this->error($data['msg'], null);
                } else {
                    $url = isset($data['url']) ? $data['url'] : null;
                    $this->success($data['msg'], $url);
                }
            }
            $this->assign($data);
            
            $html = isset($data['templateFile']) ? $data['templateFile'] : '';
            return $this->fetch($html);
        }
    }

    public function need_login()
    {
        if (! is_login()) {
            $this->redirect('home/user/login');
        }
    }

    // 初始化公众号信息
    private function initPublic()
    {
        $pbid = get_pbid();
        if (! $pbid || $pbid == - 1) {
            $this->error('关键参数缺失');
        }
        
        $info = get_pbid_appinfo($pbid);
        if (! $info) {
            $this->error('公众号不存在或已删除!');
        }
        $status = getUserInfo($info['uid'], 'status');
        if ($status == 0 && ACTION_NAME != 'logout' && ACTION_NAME != 'login') {
            $this->error('您好，该账号已到期', U('home/user/logout'));
        }
        
        // 公众号接口权限
        $config = S('PUBLIC_AUTH_' . $info['type']);
        if (! $config) {
            $config = M('public_auth')->column('type_' . intval($info['type']) . ' as val', 'name');
            
            S('PUBLIC_AUTH_' . $info['type'], $config, 86400);
        }
        if (is_array($config)) {
            foreach ($config as $c => $v) {
                config($c, $v); // 公众号接口权限
            }
        }
        
        if (IS_GET) {
            // 设置公众号管理者信息
            if ($info['uid']) {
                $manager_id = $info['uid'];
                session('manager_id', $manager_id);
            }
            $manager = get_userinfo($manager_id);
            // 设置版权信息
            $this->assign('system_copy_right', empty($manager['copy_right']) ? config('COPYRIGHT') : $manager['copy_right']);
            $tongji_code = empty($manager['tongji_code']) ? config('TONGJI_CODE') : $manager['tongji_code'];
            
            if (MODULE_NAME != 'seckill') { // 高拼发的秒杀不参与日志记录
                $log['wpid'] = WPID;
                $log['module_name'] = MODULE_NAME;
                $log['controller_name'] = CONTROLLER_NAME;
                $log['action_name'] = ACTION_NAME;
                $log['uid'] = session('mid_' . get_pbid());
                $log['cTime'] = NOW_TIME;
                ! empty($_GET) && $log['param'] = json_encode(input('get.'));
                $log['ip'] = get_client_ip();
                $log['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                M('visit_log')->insert($log);
                
                // 自动删除7天前的日志
                $key = 'delete_vist_log_' . date('Ymd');
                $has = S($key);
                if ($has === false) {
                    S($key, 1, 86400);
                    
                    $time = strtotime('-7 day');
                    M('visit_log')->where('cTime', '<', $time)->delete();
                }
            }
            $this->assign('tongji_code', $tongji_code);
            
            // 初始化微信JSAPI需要的参数
            require_once env('vendor_path') . 'jssdk/jssdk.php';
            $jssdk = new \JSSDK($info['appid'], $info['secret']);
            $jsapiParams = $jssdk->GetsignPackage();
            addWeixinLog($jsapiParams, '888888');
            $this->assign('jsapiParams', $jsapiParams);
        }
        
        return $info;
    }

    // 初始化用户信息
    private function initUser($info)
    {
        $uid = 0;
        if (isset($_GET['is_stree'])) {
            $suid = $user['uid'] = rand(1, 10000);
        } else {
            $uid = session('mid_' . get_pbid());
        }
        // 重新跳转，去掉URL中的openid参数，以防分享出去的地址带有openid参数
        $openid = I('openid');
        // dump(input());dump($openid.'-sssss');
        if (! empty($openid) && $openid != '-1' && $openid != '-2' && request()->isGet()) {
            $wpid = session('wpid');
            $old_openid = session('openid_' . $wpid);
            get_openid($openid);
            if ($old_openid != $openid) {
                session('mid_' . get_pbid(), null);
            }
            $sreach_arr = array(
                '/openid/' . $openid,
                '&openid=' . $openid,
                '?openid=' . $openid
            );
            $url = str_replace($sreach_arr, '', $_SERVER['REQUEST_URI']);
            $this->redirect($url);
        }
        if ((! $uid || $uid <= 0)) {
            $uid = get_uid_by_openid();
            $uid > 0 && session('mid_' . get_pbid(), $uid);
        }
        
        if (! $uid) {
            $youke_uid = M('config')->where('name="FOLLOW_YOUKE_UID"')->value('value') - 1;
            $user['uid'] = $youke_uid;
            M('config')->where('name="FOLLOW_YOUKE_UID"')->setField('value', $youke_uid);
            session('mid_' . get_pbid(), $youke_uid);
        }
        
        // 当前登录者
        $GLOBALS['mid'] = $this->mid = intval($uid);
        $myinfo = get_userinfo($this->mid);
        $GLOBALS['myinfo'] = $myinfo;
        
        // 当前访问对象的uid
        $_REQUEST['uid'] = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
        $GLOBALS['uid'] = $this->uid = intval($_REQUEST['uid'] == 0 ? $this->mid : $_REQUEST['uid']);
        
        $this->assign('mid', $this->mid); // 登录者
        $this->assign('uid', $this->uid); // 访问对象
        $this->assign('myinfo', $GLOBALS['myinfo']); // 访问对象
    }
}
