<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\common\controller\WebBase;

/**
 * 后台首页控制器
 *
 */
class Publics extends WebBase{

    /**
     * 后台用户登录
     *
     */
    public function login($username = null, $password = null, $verify = null)
    {
        $username = safe($username, 'text');
        $password = safe($password, 'text');
        
        if (request()->isPost()) {
            /* 检测验证码 TODO: */
            if (config('WEB_SITE_VERIFY') && ! captcha_check($verify)) {
                $this->error('验证码输入错误！');
            }
            
            /* 登录用户 */
            $User = D('common/User');
            if ($User->login($username, $password, 'admin_login')) { // 登录用户
                $this->success('登录成功！', U('Index/index'));
            } else {
                $this->error($User->getError());
            }
        } else {
            if (is_login()) {
                $this->redirect('Index/index');
            } else {
                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout()
    {
        if (is_login()) {
            D('common/User')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }
}
