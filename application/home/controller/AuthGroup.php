<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class AuthGroup extends Home
{

    public $model;

    public $syc_wechat = true;

    // 是否需要与微信端同步，目前只有认证的订阅号和认证的服务号可以同步
    public $qr_code = true;

    // 是否有创建微信带参数的二维码权限
    public function initialize()
    {
        parent::initialize();
        
        $act = strtolower(ACTION_NAME);
        $nav = [];
        $res['title'] = '用户组配置';
        $res['url'] = U('AuthGroup/lists');
        $res['class'] = $act == 'lists' ? 'current' : '';
        $nav[] = $res;
        
        $this->assign('nav', $nav);
        $this->model = $this->getModel('AuthGroup');
        
        $this->syc_wechat = config('USER_LIST');
        $this->qr_code = config('QRCODE');
    }

    public function lists()
    {
        $normal_tips = '';
        if ($this->syc_wechat) {
            $this->updateWechatGroup();
            
            $normal_tips = '温馨提示：当前用户组数据会与微信端的用户组实时同步，需要删除用户组请到微信后台删除。';
            // 搜索按钮
            $search_url = U('AuthGroup/lists', array(
                'mdm' => input('mdm')
            ));
            $this->assign('search_url', $search_url);
            
            
        }
        $map['pbid'] = get_pbid();
        $map['is_del'] = 0;
        session('common_condition', $map);
        
        $groupidArr = [];
        $list_data = $this->_get_model_list($this->model, 'id asc');
        foreach ($list_data['list_data'] as $dd) {
            $groupidArr[$dd['id']] = $dd['id'];
        }
        $uidsArr = [];
        if (! empty($groupidArr)) {
            $gmap['group_id'] = array(
                'in',
                $groupidArr
            );
            // 获取关注用户的分组数量
            $uidsArr = M('auth_group_access')->where(wp_where($gmap))->select();
        }
        
        foreach ($uidsArr as $uu) {
            if ($uu['uid'] > 0) {
                $group_uid[$uu['group_id']][] = $uu['uid'];
            }
        }
        foreach ($groupidArr as $gg) {
            if (isset($group_uid[$gg]) && ! empty($group_uid[$gg])) {
                $fmap['uid'] = array(
                    'in',
                    $group_uid[$gg]
                );
                $fmap['has_subscribe'] = 1;
                $hasSub[$gg] = M('public_follow')->where(wp_where($fmap))->count();
            }
        }
        if ($this->qr_code) {
            foreach ($list_data['list_data'] as &$vo) {
                if (isset($hasSub[$vo['id']])) {
                    $vo['count'] = '<a href="' . U('weixin/UserCenter/lists', array(
                        'group_id' => $vo['id']
                    )) . '"/>' . intval($hasSub[$vo['id']]) . '</a>';
                } else {
                    $vo['count'] = '';
                }
                
                if (! empty($vo['qr_code'])) {
                    $vo['qr_code'] = "<a target='_blank' href='{$vo['qr_code']}'><img src='{$vo['qr_code']}' class='list_img'></a>";
                    continue;
                }
                
                $res = D('home/QrCode')->add_qr_code('QR_LIMIT_SCENE', 'UserCenter', $vo['id']);
                if (! ($res < 0)) {
                    $map2['id'] = $vo['id'];
                    M('auth_group')->where(wp_where($map2))->setField('qr_code', $res);
                    $vo['qr_code'] = $res;
                    $vo['qr_code'] = "<a target='_blank' href='{$vo['qr_code']}'><img src='{$vo['qr_code']}' class='list_img'></a>";
                }
            }
            $normal_tips .= '当用户微信扫分组里的二维码时，用户会自动移到该分组中';
        } else {
            // 删除二维码一栏
            unset($list_data['list_grids'][2]);
        }
        if ($this->syc_wechat) {
            $grid = array_pop($list_data['list_grids']);
            unset($grid['href'][2]); // 去掉删除按钮
            
            $grid['href'][] = [
                'title' => '查看详情',
                'url' => 'toGroupDetail&group_id=[id]'
            ];
            $list_data['list_grids']['urls'] = $grid;
        }
        // dump($list_data);
        $this->assign($list_data);
        $this->assign('normal_tips', $normal_tips);
        if ($this->syc_wechat){
        	$this->assign('check_all', false);
        	$this->assign('del_button', false);
        }
        return $this->fetch('common@base/lists');
    }

    public function toGroupdetail()
    {
        $group_id = I('group_id/d', 0);
        return redirect(U('weixin/UserCenter/lists', array(
            'group_id' => $group_id
        )));
    }

    public function add()
    {
        $model = $this->model;
        if (request()->isPost()) {
            $data = input('post.');
            $data['type'] = 1; // 目前只能增加微信管理组
            $data['pbid'] = get_pbid();
            $has = $this->checkTitle($data['title']);
            if ($has > 0) {
                $this->error('该分组名已经存在！');
            }
            $Model = D($model['name']);
            // 获取模型的字段信息
            
            $data = $this->checkData($data, $model);
            $id = $Model->insertGetId($data);
            if ($id) {
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $model['title'];
            
            return $this->fetch('common@base/add');
        }
    }

    public function checkTitle($title, $id = 0)
    {
        $tLen = strlen($title);
        if ($tLen > 30) {
            $this->error('分组名称不能超过30个字符，或10个汉字！');
        }
        $zStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $title);
        $zLen = strlen($zStr);
        $zStr = preg_replace('/[^A-Za-z0-9]/u', '', $title);
        $yLen = strlen($zStr);
        if ($zLen + $yLen != $tLen) {
            $this->error('分组名称不能有特殊字符！');
        }
        $map['title'] = $title;
        $map['pbid'] = get_pbid();
        if ($id) {
            $map['id'] = array(
                'neq',
                $id
            );
        }
        $count = M('auth_group')->where(wp_where($map))->count();
        return intval($count);
    }

    public function edit($id = 0)
    {
        $model = $this->model;
        $id || $id = I('id');
        
        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        
        if (request()->isPost()) {
            $act = 'save';
            $has = $this->checkTitle(input('post.title'), $id);
            if ($has > 0) {
                $this->error('该分组名已经存在！');
            }
            $Model = D($model['name']);
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkData($data, $model);
            $res = $Model->isUpdate(true)->save($data, [
                'id' => $id
            ]);
            if ($res !== false) {
                
                $title = I('title');
                if ($this->syc_wechat && $title != $data['title'] && ! empty($data['wechat_group_id'])) {
                    // 修改的用户组名同步到微信端
                    $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token=' . get_access_token();
                    
                    $param['group']['id'] = $data['wechat_group_id'];
                    $param['group']['name'] = $title;
                    $param = json_url($param);
                    post_data($url, $param);
                }
                //更新用户缓存
                $uidsArr = M('auth_group_access')->where('group_id',$id)->column('uid');
                foreach ($uidsArr as $uid){
                	D('common/User')->getUserInfo($uid,true);
                }
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model['title'];
            
            return $this->fetch('common@base/edit');
        }
    }

    public function del()
    {
        $ids = I('ids');
        $model = $this->model;
        
        ! empty($ids) || $ids = I('id');
        ! empty($ids) || $ids = array_filter(array_unique((array) I('ids', 0)));
        ! empty($ids) || $this->error('请选择要操作的数据!');
        
        $Model = M($model['name']);
        
        // 插件里的操作自动加上Token限制
        $map = [];
        $pbid = get_pbid();
        if (! empty($pbid)) {
            $map['pbid'] = $map2['f.pbid'] = $pbid;
        }
        
        if ($this->syc_wechat) {
            $res = $Model->where(wp_where($map))
                ->whereIn('id', $ids)
                ->setField('is_del', 0);
        } else {
            $res = $Model->where(wp_where($map))
                ->whereIn('id', $ids)
                ->delete();
        }
        
        if ($res) {
            $px = DB_PREFIX;
            $follow_list = M()->table($px . 'auth_group_access')
                ->alias('a')
                ->join($px . 'public_follow f', 'a.uid=f.uid')
                ->where(wp_where($map2))
                ->whereIn('a.group_id', $ids)
                ->field('DISTINCT f.openid')
                ->select();
            if (! empty($follow_list)) {
                $map3['uid'] = [
                    'in',
                    $ids
                ];
                // 微信端用户归组到未分组
                if ($this->syc_wechat) {
                    $gmap['pbid'] = get_pbid();
                    $gmap['wechat_group_id'] = 0;
                    $gid = M('auth_group')->where(wp_where($gmap))->value('id');
                    
                    M('auth_group_access')->where(wp_where($map3))
                        ->whereIn('group_id', $ids)
                        ->setField('group_id', $gid);
                    
                    $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . get_access_token();
                    foreach ($follow_list as $follow) {
                        $param['openid'] = $follow['openid'];
                        $param['to_groupid'] = 0;
                        $param = json_url($param);
                        post_data($url, $param);
                    }
                } else {
                    M('auth_group_access')->where(wp_where($map3))
                        ->whereIn('group_id', $ids)
                        ->delete();
                }
            }
            
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 与微信的用户组保持同步
    public function updateWechatGroup()
    {
        // 先取当前用户组数据
        $map['pbid'] = get_pbid();
        $map['type'] = 1;
        $group_list = M('auth_group')->where(wp_where($map))
            ->field('id,title,wechat_group_id,wechat_group_name,wechat_group_count')
            ->select();
        $ournew = $groups = [];
        foreach ($group_list as $g) {
            if ($g['wechat_group_id'] == - 1) {
                $ournew[] = $g;
            } else {
                $groups[$g['wechat_group_id']] = $g;
            }
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . get_access_token();
        $data = wp_file_get_contents($url);
        $data = json_decode($data, true);
        if (! isset($data['errcode']) && $data) {
            foreach ($data['groups'] as $d) {
                $save['wechat_group_id'] = $map['wechat_group_id'] = $d['id'];
                $save['wechat_group_name'] = $d['name'];
                $save['wechat_group_count'] = $d['count'];
                
                if (isset($groups[$d['id']])) {
                    // 更新本地数据
                    $old = $groups[$d['id']];
                    if ($old['title'] != $d['name']) {
                        $old['wechat_group_name'] = $old['title'];
                        $save['wechat_group_name'] = $old['title'];
                        // 修改微信端的数据
                        $updateUrl = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=" . get_access_token();
                        $newGroup['group']['id'] = $d['id'];
                        $newGroup['group']['name'] = $save['wechat_group_name'];
                        post_data($updateUrl, $newGroup);
                    }
                    if ($old['wechat_group_name'] != $d['name'] || $old['wechat_group_count'] != $d['count']) {
                        // $save['title']=$save['wechat_group_name'];
                        M('auth_group')->where(wp_where($map))->update($save);
                    }
                    unset($groups[$d['id']]);
                } else {
                    // 增加本地数据
                    $save = array_merge($save, $map);
                    $save['title'] = $d['name'];
                    $save['qr_code'] = '';
                    M('auth_group')->insert($save);
                }
            }
            foreach ($ournew as $v) {
                $map2['id'] = $map3['group_id'] = $v['id'];
                // 增加微信端的数据
                $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=' . get_access_token();
                if (strlen($v['title']) > 30) {
                    $v['title'] = substr($v['title'], 0, 30);
                    $save['title'] = $v['title'];
                }
                $param['group']['name'] = $v['title'];
                
                $res = post_data($url, $param);
                if (! empty($res['group']['id'])) {
                    $info['wechat_group_id'] = $save['wechat_group_id'] = $res['group']['id'];
                    $save['wechat_group_name'] = $res['group']['name'];
                    M('auth_group')->where(wp_where($map2))->update($save);
                }
            }
            foreach ($groups as $v) {
                $map2['id'] = $map3['group_id'] = $v['id'];
                $wechat_group_id = intval($v['wechat_group_id']);
                if ($wechat_group_id == - 1) {
                    // // 增加微信端的数据
                    $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=' . get_access_token();
                    if (strlen($v['title']) > 30) {
                        $v['title'] = substr($v['title'], 0, 30);
                        $save['title'] = $v['title'];
                    }
                    $param['group']['name'] = $v['title'];
                    
                    $res = post_data($url, $param);
                    if (! empty($res['group']['id'])) {
                        $info['wechat_group_id'] = $save['wechat_group_id'] = $res['group']['id'];
                        $save['wechat_group_name'] = $res['group']['name'];
                        M('auth_group')->where(wp_where($map2))->update($save);
                    }
                } else {
                    // 删除本地数据
                    M('auth_group')->where(wp_where($map2))->delete();
                    M('auth_group_access')->where(wp_where($map3))->delete();
                }
            }
        }
        
        if (isset($_GET['need_return'])) {
            return redirect(U('weixin/UserCenter/syc_openid'));
        }
    }

    public function qrcode()
    {
        $id = intval($_GET['id']);
        $res = D('home/QrCode')->add_qr_code('QR_LIMIT_SCENE', 'UserCenter', $id);
    }

    public function follows()
    {
        return redirect(U('weixin/UserCenter/lists', array(
            'group_id' => I('id')
        )));
    }

    public function export()
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        
        $umap['u.status'] = array(
            'gt',
            0
        );
        $umap['f.pbid'] = get_pbid();
        
        $type = input('type/d', 0);
        $umap['f.has_subscribe'] = 1 - $type;
        
        $gid = I('id', 0);
        if ($gid) {
            $map['group_id'] = $gid;
            $uids = M('auth_group_access')->where(wp_where($map))->column('uid');
            if (! empty($uids)) {
                $umap['u.uid'] = array(
                    'in',
                    $uids
                );
            } else {
                $umap['u.uid'] = 0;
            }
        }
        
        $order = 'u.uid asc';
        $px = DB_PREFIX;
        $field = 'u.uid,nickname,truename,mobile,sex,province,city,score,f.openid';
        $data = M()->table($px . 'public_follow')
            ->alias('f')
            ->join($px . 'user u', 'f.uid=u.uid')
            ->field($field)
            ->where(wp_where($umap))
            ->order($order)
            ->select();
        
        $sexArr = array(
            0 => '保密',
            1 => '男',
            2 => '女'
        );
        foreach ($data as $k => &$vo) {
            $vo['sex'] = intval($vo['sex']);
            $vo['sex'] = $sexArr[$vo['sex']];
            $vo['nickname'] = $vo['nickname'];
        }
        
        $ht = array(
            '用户编号',
            '昵称',
            '姓名',
            '联系电话',
            '性别',
            '省份',
            '城市',
            '金币值',
//             '经验值',
            'OpenID'
        );
        $dataArr[0] = $ht;
        $dataArr = array_merge($dataArr, (array) $data);
        outExcel($dataArr, 'group_user_' . $gid);
    }

    // 移动用户到所在分组
    public function tongbu_follow()
    {
        $map['pbid'] = get_pbid();
        
        $list = M('auth_group')->where(wp_where($map))->select();
        foreach ($list as $v) {
            $arr[$v['id']] = $v['wechat_group_id'];
        }
        
        $id = I('id/d', 0);
        $map['id'] = array(
            'gt',
            $id
        );
        $map['has_subscribe'] = 1;
        $map['pbid'] = get_pbid();
        $follow_list = M('public_follow')->where(wp_where($map))
            ->order('id asc')
            ->limit(5)
            ->select();
        if (! $follow_list) {
            echo 'update over!';
            exit();
        }
        
        $access_token = get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $access_token;
        foreach ($follow_list as $follow) {
            $param['openid'] = $follow['openid'];
            $param['to_groupid'] = intval($arr[$follow['group']]);
            $param = json_url($param);
            $res = post_data($url, $param);
            
            $has_subscribe = $res['errcode'] == 43004 ? 0 : 1;
            M('public_follow')->where('id=' . $follow['id'])->setField('has_subscribe', $has_subscribe);
        }
        
        $param2['id'] = $follow['id'];
        $url = U('tongbu_follow', $param2);
        
        $url = U('tongbu_follow');
        $this->success('同步用户数据中，请勿关闭', $url);
        
        // echo 'update follow_id: ' . $follow ['id'] . ', please wait!';
        // echo '<script>window.location.href="' . $url . '";</script>';
    }
}
