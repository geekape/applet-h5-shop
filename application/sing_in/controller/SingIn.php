<?php

namespace app\sing_in\controller;

use app\common\controller\WebBase;

class SingIn extends WebBase
{
    public function lists()
    {

        // 关键字搜索
        $map['wpid'] = get_wpid();
        $key          = 'uid';
		$_REQUEST=input('param.');
        if (isset($_REQUEST[$key])) {
            $map[$key] = array(
                'like',
                '%' . htmlspecialchars($_REQUEST[$key]) . '%',
            );
            unset($_REQUEST[$key]);
        }

        // 读取模型数据列表
        $row  = 20;
        $name = 'signin_log';

        // 查询数据
        $data = M($name)->where(wp_where($map))->order('id DESC')->paginate($row);
		$list_data = $this->parsePageData($data, [], [], false);

        // 获取相关的用户信息
        $uids = getSubByKey($list_data['list_data'], 'uid');
        $uids = array_filter($uids);
        $uids = array_unique($uids);
        if (!empty($uids)) {
            foreach ($list_data['list_data'] as &$vo) {
                $user           = get_userinfo($vo['uid']);
                $vo['user_id']  = $user['uid'];
                $vo['mobile']   = $user['mobile'];
                $vo['nickname'] = $user['nickname'];
            }
        }

        $this->assign($list_data);

        return $this->fetch();
    }

    public function show_sing_in()
    {
        return $this->fetch();
    }
}
