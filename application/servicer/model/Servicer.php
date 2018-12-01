<?php
namespace app\servicer\model;

use app\common\model\Base;

/**
 * Servicer模型
 */
class Servicer extends Base
{

    function checkRule($uid, $rule)
    {
        $map['wpid'] = get_wpid();
        $map['uid'] = $uid;
        $map['enable'] = 1;
        
        $role = $this->where(wp_where($map))->value('role');
        if (empty($role))
            return false;
        
        $role = explode(',', $role);
        return in_array($rule, $role) ? true : false;
    }

    // 获取公众号配置的全部客服
    function getAllChatService()
    {
        $map['wpid'] = get_wpid();
        $map['enable'] = 1;
        
        $lists = $this->where(wp_where($map))
            ->order('update_at asc')
            ->select();
        
        $data = [];
        foreach ($lists as $vo) {
            if (empty($vo['role']))
                continue;
            
            $role = explode(',', $vo['role']);
            if (! in_array(1, $role))
                continue;
            
            $data[$vo['uid']] = $vo;
        }
        
        return $data;
    }
}
