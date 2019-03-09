<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 计数池
 */
class Chat extends Base
{

    protected $table = DB_PREFIX . 'chat';

    function load_data($uid, $lastid = 0, $to_uid = 0)
    {
        $map['wpid'] = WPID;
        if ($lastid > 0) {
            $map['id'] = [
                '<',
                $lastid
            ];
        }
        $service_lists = D('servicer/Servicer')->getAllChatService();
        if (isset($service_lists[$uid])) {
            // 我的客服
            $map['uid|to_uid'] = $to_uid;
        } else {
            // 我是普通用户
            $map['uid|to_uid'] = $uid; // 获取当前用户与公众号所有的客服对话列表，不仅仅是当前客服
        }
        
        $list = $this->where(wp_where($map))
            ->limit(10)
            ->order('id desc')
            ->select();
        
        if (! empty($list)) { // 转成数组，方便下面的排序
            $list = $list->toArray();
        }
        foreach ($list as &$vo) {
            $vo['time'] = time_format($vo['create_at']);
            $vo = getUserBaseInfo($vo);
        }
        
        sort($list);
        
        // 把消息设置成已读
        if ($to_uid == 0) { // 手机用户
            $this->where('to_uid', $uid)
                ->where('wpid', WPID)
                ->where('is_read', 0)
                ->setField('is_read', 1);
        } else { // 后台管理员
            $this->where('to_uid', $uid)
                ->where('wpid', WPID)
                ->where('is_read', 0)
                ->setField('is_read', 1);
        }
        return $list;
    }

    function getUserNewMsgCount($uid)
    {
        return $this->where('to_uid', $uid)
            ->where('wpid', WPID)
            ->where('is_read', 0)
            ->count();
    }
}
?>
