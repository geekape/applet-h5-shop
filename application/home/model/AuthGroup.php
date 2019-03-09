<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\model;

use app\common\model\Base;

/**
 * 插件模型
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AuthGroup extends Base
{

    protected $table = DB_PREFIX . 'auth_group';

    // 移动用户到某个组
    public function move_group($id, $group_id)
    {
        is_array($id) || $id = explode(',', $id);
        
        $data['uid'] = $map['uid'] = array(
            'in',
            $id
        );
        // $data ['group_id'] = $group_id; //TODO 前端微信用户只能有一个微信组
        $res = M('auth_group_access')->where(wp_where($data))->delete();
        
        $data['group_id'] = $group_id;
        foreach ($id as $uid) {
            $data['uid'] = $uid;
            M('auth_group_access')->insert($data);
            // 更新用户缓存
            $uu= D('common/User')->getUserInfo($uid, true);
        }
     
        $group = $this->where('id', $group_id)->find();
        // 同步到微信端
        if (config('USER_GROUP') && ! is_null($group['wechat_group_id']) && $group['wechat_group_id'] != - 1) {
            file_log('555','group');
            $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . get_access_token();
            
            $map['pbid'] = get_pbid();
            $follow = M('public_follow')->where(wp_where($map))
                ->field('openid, uid')
                ->select();
            foreach ($follow as $v) {
                if (empty($v['openid']))
                    continue;
                
                $param['openid'] = $v['openid'];
                $param['to_groupid'] = $group['wechat_group_id'];
                $param = json_url($param);
                $res = post_data($url, $param);
                file_log($res,'group');
                unset($param);
            }
        }
        
        return $group;
    }

    public function getGroupInfo($uid)
    {}
}
