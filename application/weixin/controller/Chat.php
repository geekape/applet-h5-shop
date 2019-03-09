<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\weixin\controller;

use app\common\controller\WebBase;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Chat extends WebBase
{

    public function index()
    {
        // 获取用户列表
        $map['wpid'] = WPID;
        $map['to_uid'] = $this->mid;
        $list = D('common/Chat')->where(wp_where($map))
            ->order('is_read asc, id desc')
            ->group('uid')
            ->limit(100)
            ->field('uid,is_read')
            ->select();
        foreach ($list as &$vo) {
            $user = getUserInfo($vo['uid']);
            
            $vo['name'] = $user['nickname'];
            $vo['head'] = $user['headimgurl'];
        }
        
        $this->assign('user_lists', $list);
        
        return $this->fetch();
    }
    
}
