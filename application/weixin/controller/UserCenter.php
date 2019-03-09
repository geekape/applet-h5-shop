<?php
namespace app\weixin\controller;

use app\common\controller\WebBase;

class UserCenter extends WebBase
{

    var $syc_wechat = true;

    // 是否需要与微信端同步，目前只有认证的订阅号和认证的服务号可以同步
    function initialize()
    {
        parent::initialize();
        $this->syc_wechat = public_interface('user_group');
        
        $type = input('type/d', 0);
        $this->assign('type', $type);
        
        $res['title'] = '微信用户';
        $res['url'] = U('weixin/UserCenter/lists');
        $res['class'] = $type == 0 ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '取消关注用户';
        $res['url'] = U('weixin/UserCenter/lists', array(
            'type' => 1
        ));
        $res['class'] = $type == 1 ? 'current' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);
    }

    function lists_choose()
    {
        return $this->lists(1);
    }

    /**
     * 微信用户列表数据
     */
    public function lists($isAjax = 0)
    {
        $model = $this->getModel('user');
        $isAjax == 0 && $isAjax = I('isAjax');
        $this->assign('isAjax', $isAjax);
        
        $isRadio = I('isRadio');
        $this->assign('isRadio', $isRadio);
        
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        
        $map['u.status'] = array(
            'gt',
            0
        );
        $map['f.pbid'] = get_pbid();
        
        // 搜索类型
        $search_type = input('search_type', 0);
        $this->assign('search_type', $search_type);
        
        $key = input('key');
        $this->assign('search_key', $key);
        if (! empty($key)) {
            $is_opneid = $search_type == 2 ? true : false;
            $uidstr = D('common/User')->searchUser($key, $is_opneid);
            if ($uidstr) {
                $map['u.uid'] = array(
                    'in',
                    $uidstr
                );
            } else {
                $map['u.uid'] = 0;
            }
        }
        
        $group_id = input('group_id/d', 0);
        $this->assign('group_id', $group_id);
        if ($group_id > 0) {
            $uids = M('auth_group_access')->where('group_id', $group_id)->column('uid');
            if (empty($uids)) {
                $map['f.uid'] = 0;
            } else {
                $map['f.uid'] = array(
                    'in',
                    $uids
                );
            }
        }
        
        $param = [];
        // 时间
        $s_time = input('s_time');
        $this->assign('s_time', $s_time);
        $s_time = strtotime($s_time);
        empty($s_time) && $s_time = 0;
        
        $e_time = input('e_time', '');
        $this->assign('e_time', $e_time);
        if ($e_time) {
            $e_time = strtotime($e_time);
        } else {
            $e_time = NOW_TIME;
        }
        if ($s_time || $e_time) {
            $map['reg_time'] = [
                'between',
                [
                    $s_time,
                    $e_time
                ]
            ];
        }
        
        // 标签
        $tag_id = input('tag_id', '');
        if ($tag_id) {
            $param['tag_id'] = $tag_id;
            $uidstr = D('common/User')->searchUserS($param);
            if ($uidstr) {
                $map['u.uid'] = array(
                    'in',
                    $uidstr
                );
            }
        }
        $this->assign('tag_id', $tag_id);
        
        // 性别
        $sex = input('sex', '');
        if ($sex) {
            $map['sex'] = $sex;
        }
        $this->assign('sex', $sex);
        
        $type = input('type/d', 0);
        $map['f.has_subscribe'] = 1 - $type;
        
        $row = empty($model['list_row']) ? 20 : $model['list_row'];
        $order = 'u.uid desc';
        // 读取模型数据列表

        $px = DB_PREFIX;
        $data = M('public_follow')->alias('f')
            ->join(DB_PREFIX . 'user u ', 'f.uid=u.uid')
            ->field('u.uid,f.openid')
            ->where(wp_where($map))
            ->order($order)
            ->paginate($row);
        $list_data = $this->parsePageData($data, $model, $list_data, false);
        
        foreach ($list_data['list_data'] as $k => $d) {
            $user = getUserInfo($d['uid']);
            $user['openid'] = $d['openid'];
            $user['group'] = isset($user['groups']) ? implode(', ', getSubByKey($user['groups'], 'title')) : '';
            $list_data['list_data'][$k] = array_merge($d, $user);
        }
        
        // 用户组
        $gmap['pbid'] = get_pbid();
        $auth_group = M('auth_group')->where(wp_where($gmap))->select();
        $this->assign('auth_group', $auth_group);
        
        $tagmap['pbid'] = get_pbid();
        $tags = M('user_tag')->where(wp_where($tagmap))->select();
        $this->assign('tags', $tags);
        
        $this->assign('syc_wechat', $this->syc_wechat);
        if ($this->syc_wechat) {
            $this->assign('normal_tips', '请定期手动点击“一键同步微信公众号粉丝”按钮同步微信数据');
        }
        $this->assign($list_data);
        if ($isAjax) {
            $this->assign('isRadio', $isRadio);
            return $this->fetch('lists_data');
        } else {
            return $this->fetch();
        }
    }

    function getUserRemark()
    {
        $uid = I('uid');
        $remark = '';
        $user = get_userinfo($uid);
        $pbid = get_pbid();
        if ($user['remarks'][$pbid]) {
            $remark = $user['remarks'][$pbid];
        }
        echo $remark;
    }

    function detail()
    {
        $uid = I('uid');
        $userInfo = getUserInfo($uid);
        // dump($userInfo);
        $strgroup = '';
        foreach ($userInfo['groups'] as $v) {
            $strgroup .= $v['title'] . ',';
        }
        $len = strlen($strgroup) - 1;
        $str = substr($strgroup, 0, $len);
        $userInfo['groupstr'] = $str;
        
        $userInfo['openid'] = isset($userInfo['pbids'][PBID]) ? $userInfo['pbids'][PBID] : '';
        
        if ($userInfo['reg_time'] > 0) {
            $day = ceil((NOW_TIME - $userInfo['reg_time']) / 86400);
            $year = floor($day / 365);
            $day = $day % 365;
            
            $userInfo['reg_time'] = '';
            if ($year > 0) {
                $userInfo['reg_time'] = $year . '年 ';
            }
            $userInfo['reg_time'] .= $day . '天';
        } else {
            $userInfo['reg_time'] = '';
        }
        
        $this->assign('info', $userInfo);
        
        return $this->fetch();
    }

    function set_login()
    {
        $model = $this->getModel('user');
        $map['uid'] = $id = I('uid');
        
        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        
        if (IS_POST) {
            if (empty(input('post.login_name')) || empty(input('post.login_password'))) {
                $this->error('账号信息不能为空');
            }
            
            $save['login_name'] = I('login_name');
            $old_uid = M('user')->where(wp_where($save))->value('uid');
            if ($old_uid > 0 && $old_uid != $id) {
                $this->error('该账号已经存在，请更换后再试');
            }
            // 手工升级会员时，用户经历值也增加到该会员级别的条件经历值
            if (is_install("Shop")) {
                $membership_condition = M('shop_membership')->where('id', input('post.membership'))->value('condition');
            }
            
            $save['leven'] = 1;
            $save['manager_id'] = $this->mid;
            $save['is_audit'] = 1;
            $save['is_init'] = 1;
            $save['status'] = 1;
            $save['login_password'] = I('login_password');
            $save['password'] = think_weiphp_md5($save['login_password']);
            $save['membership'] = input('post.membership');
            // 获取模型的字段信息
            if (M('user')->where(wp_where($map))->update($save) !== false) {
                D('common/User')->getUserInfo($id, true);
                
                $this->success('保存' . $model['title'] . '成功！', U('lists'));
            } else {
                $this->error('保存失败');
            }
        } else {
            $fields = get_model_attribute($model);
            
            $extra = $this->getMembershipData();
            if (! empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'membership') {
                        $vo['extra'] .= "\r\n" . $extra;
                    }
                }
            }
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            $this->assign('post_url', U('set_login', $map));
            
            return $this->fetch('edit');
        }
    }

    // 获取会员等级
    function getMembershipData()
    {
        $map['uid'] = $this->mid;
        $map['wpid'] = get_wpid();
        $uid = I('uid');
        $extra = '';
        if (is_install("Shop")) {
            $list = M('shop_membership')->where(wp_where($map))->select();
        }
        return $extra;
    }

    // 用户绑定
    public function edit()
    {
        $is_admin_edit = false;
        if (! empty($_REQUEST['id'])) {
            $map['id'] = intval($_REQUEST['id']);
            $is_admin_edit = true;
            $msg = '编辑';
            $html = 'edit';
        } else {
            $msg = '绑定';
            $openid = $map['openid'] = get_openid();
            $html = 'moblieForm';
        }
        $wpid = $map['wpid'] = get_wpid();
        $model = $this->getModel('user');
        
        if (IS_POST) {
            $data = I('post.');
            $is_admin_edit && $data['status'] = 2;
            $Model = D($model['name']);
            
            $data = $this->checkData($data, $model);
            $res = $Model->where(wp_where($map))->update($data);
            if ($res !== false) {
                $url = '';
                $bind_backurl = cookie('__forward__');
                $config = getAddonConfig('UserCenter');
                $jumpurl = $config['jumpurl'];
                
                if (! empty($bind_backurl)) {
                    $url = $bind_backurl;
                    cookie('__forward__', null);
                } elseif (! empty($jumpurl)) {
                    $url = $jumpurl;
                } elseif (! $is_admin_edit) {
                    $url = U('wei_site/Wap/index', $map);
                }
                
                $this->success('操作成功！', $url);
            } else {
                // lastsql();
                // dump($map);exit;
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            // dump($fields);
            if (! $is_admin_edit) {
                $fieldArr = array(
                    'nickname',
                    'sex',
                    'mobile',
                    'email'
                ); // headimgurl
                foreach ($fields as $k => $vo) {
                    if (! in_array($vo['name'], $fieldArr)) {
                        unset($fields[$k]);
                    }
                }
                
                $this->assign('button_name', '用户绑定');
            }
            
            // 获取数据
            $data = M($model['name'])->where(wp_where($map))->find();
            
            $pbid = get_pbid();
            if (isset($data['pbid']) && $pbid != $data['pbid']) {
                $this->error('非法访问！');
            }
            
            // 自动从微信接口获取用户信息
            empty($openid) || $info = getWeixinUserInfo($openid, $pbid);
            if (is_array($info)) {
                if (empty($data['headimgurl']) && ! empty($info['headimgurl'])) {
                    $data['headimgurl'] = $info['headimgurl'];
                }
                $data = array_merge($info, $data);
            }
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            $this->assign('post_url', U('edit'));
            
            return $this->fetch($html);
        }
    }

    public function userCenter()
    {
        return $this->fetch();
    }

    function config()
    {
        // 使用提示
        $normal_tips = '如需用户关注时提示先绑定，请进入‘欢迎语’插件按提示进行配置提示语';
        $this->assign('normal_tips', $normal_tips);
        if (IS_POST) {
            $config = I('config');
            $credit['score'] = intval($config['score']);
            $pbid = get_pbid();
            D('common/Credit')->updateSubscribeCredit($pbid, $credit, 1);
        }
        
        return parent::config();
    }

    // 设置用户组
    public function changeGroup()
    {
        $uids = array_unique((array) I('ids', 0));
        
        if (empty($uids)) {
            $this->error('请选择用户!');
        }
        $group_id = I('group_id', 0);
        if (empty($group_id)) {
            $this->error('请选择用户组!');
        }
        D('home/AuthGroup')->move_group($uids, $group_id);
        foreach ($uids as $uid) {
            D('common/User')->getUserInfo($uid, true);
        }
        echo 1;
    }

    // 设置用户标签
    public function changeTag()
    {
        $uids = array_unique((array) I('ids', 0));
        
        if (empty($uids)) {
            $this->error('请选择用户!');
        }
        $tags = array_filter(explode(',', I('tags')));
        if (empty($tags)) {
            $this->error('请选择用户标签!');
        }
        
        M('user_tag_link')->whereIn('uid', $uids)->delete();
        foreach ($uids as $uid) {
            foreach ($tags as $tid) {
                $data['uid'] = $uid;
                $data['tag_id'] = $tid;
                $data['cTime'] = NOW_TIME;
                M('user_tag_link')->insert($data);
            }
            D('common/User')->getUserInfo($uid, true);
        }
        echo 1;
    }

    // 预先同步好用户组数据
    function syc_auth_group()
    {
        return redirect(U('home/AuthGroup/updateWechatGroup', array(
            'need_return' => 1
        )));
    }

    // 第一步：获取全部用户的ID，并先保存到public_follow表中，新的用户UID暂时为0，后面的步骤补充
    function syc_openid()
    {
        $map['pbid'] = $save['pbid'] = get_pbid();
        
        $next_openid = I('next_openid');
        if (! $next_openid) {
            $res = M('public_follow')->where(wp_where($map))->setField('has_subscribe', 0);
        }
        // 获取openid列表
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . get_access_token() . '&next_openid=' . $next_openid;
        $data = wp_file_get_contents($url);
        $data = json_decode($data, true);
        
        if (! isset($data['count']) || $data['count'] == 0) {
            // 拉取完毕
            return $this->jump(U('syc_user'), '同步用户数据中，请勿关闭');
        }
        
        $map['openid'] = array(
            'in',
            $data['data']['openid']
        );
        $map['pbid'] = $save['pbid'] = get_pbid();
        $pdata['syc_status'] = 0;
        $pdata['has_subscribe'] = 1;
        $res = M('public_follow')->where(wp_where($map))->update($pdata);
        if ($res != $data['count']) {
            // 更新的数量不一致，可能有增加的用户openid
            $openids = (array) M('public_follow')->where(wp_where($map))->column('openid');
            $diff = array_diff($data['data']['openid'], $openids);
            if (! empty($diff)) {
                foreach ($diff as $id) {
                    $save['openid'] = $id;
                    $save['uid'] = 0;
                    $save['syc_status'] = 0;
                    $save['has_subscribe'] = 1;
                    $res = M('public_follow')->insertGetId($save);
                }
            }
        }
        
        $param2['next_openid'] = $data['next_openid'];
        $url = U('syc_openid', $param2);
        return $this->jump($url, '同步用户OpenID中，请勿关闭');
    }

    // 第二步：同步用户信息
    function syc_user()
    {
        $map['pbid'] = $map2['pbid'] = $map5['pbid'] = get_pbid();
        $map['syc_status'] = 0;
        $map['has_subscribe'] = 1;
        $list = M('public_follow')->where(wp_where($map))
            ->field('uid,openid')
            ->limit(100)
            ->select();
        
        if (empty($list)) {
            return $this->jump(U('syc_user_group'), '用户分组信息同步中');
        }
        
        foreach ($list as $vo) {
            $param['user_list'][] = array(
                'openid' => $vo['openid']
            );
            $openids[] = $vo['openid'];
            $uids[$vo['openid']] = $vo['uid'];
        }
        // 先把关注状态设置未关注
        $map2['openid'] = array(
            'in',
            $openids
        );
        // M( 'public_follow' )->where ( wp_where( $map2 ) )->setField ( 'has_subscribe', 0 );
        
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . get_access_token();
        $data = post_data($url, $param);
        $userDao = D('common/User');
        $config = getAddonConfig('UserCenter');
        isset($config['score']) || $config['score'] = 0;
        
        $countdata['list_count'] = count($list);
        $countdata['wp_data_count'] = count($data['user_info_list']);
        if ($countdata['list_count'] != $countdata['wp_data_count']) {
            $countdata['listopenid'] = $param;
            $countdata['wp_op'] = $data;
            $countdata['access_token'] = get_access_token();
            foreach ($param['user_list'] as $p) {
                $single_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . get_access_token() . '&openid=' . $p['openid'] . '&lang=zh_CN';
                $tempArr = json_decode(wp_file_get_contents($single_url), true);
                if (empty($tempArr)) {
                    $tempArr = outputCurl($single_url);
                    $tempArr = json_decode($tempArr, true);
                    // addWeixinLog($tempArr,'syc_userlistscount5');
                }
                if (empty($tempArr)){
                	continue;
                }
                $uid = isset($uids[$tempArr['openid']])?intval($uids[$tempArr['openid']]):0;
                if ($uid == 0) { // 新增加的用户
                    $tempArr['score'] = intval($config['score']);
                    $tempArr['reg_time'] = $tempArr['subscribe_time'];
                    $tempArr['status'] = 1;
                    $tempArr['is_init'] = 1;
                    $tempArr['is_audit'] = 1;
                    
                    $uid = D('common/User')->addUser($tempArr);
                    
                    $map5['openid'] = $tempArr['openid'];
                    $uid > 0 && M('public_follow')->where(wp_where($map5))->setField('uid', $uid);
                } else { // 更新的用户
                    $userDao->updateInfo($uid, $tempArr);
                }
            }
            M('public_follow')->where(wp_where($map2))->setField('syc_status', 1);
            return $this->jump(U('syc_user'), '同步用户数据中，请勿关闭');
        }
        // addWeixinLog($countdata,'syc_userlistscount2');
        
        foreach ($data['user_info_list'] as $u) {
            if ($u['subscribe'] == 0) {
                continue;
            }
            
            $uid = intval($uids[$u['openid']]);
            if ($uid == 0) { // 新增加的用户
                $u['score'] = intval($config['score']);
                $u['reg_time'] = $u['subscribe_time'];
                $u['status'] = 1;
                $u['is_init'] = 1;
                $u['is_audit'] = 1;
                unset($u['id']);
                
                $uid = D('common/User')->addUser($u);
                
                $map5['openid'] = $u['openid'];
                $uid > 0 && M('public_follow')->where(wp_where($map5))->setField('uid', $uid);
            } else { // 更新的用户
                $userDao->updateInfo($uid, $u);
            }
            
            $openidArr[] = $u['openid'];
        }
        M('public_follow')->where(wp_where($map2))->setField('syc_status', 1);
        
        return $this->jump(U('syc_user?uid=' . $uid), '同步用户数据中，请勿关闭');
    }

    // 第三步：同步用户组信息
    function syc_user_group()
    {
        $map['pbid'] = $map2['pbid'] = get_pbid();
        $map['syc_status'] = 1;
        $map['uid'] = array(
            'gt',
            0
        );
        $uids = M('public_follow')->where(wp_where($map))
            ->limit(100)
            ->column('uid');
        
        if (empty($uids)) {
            return $this->jump(U('lists'), '用户分组信息同步完毕');
        }
        
        $list = M('user')->whereIn('uid', $uids)
            ->field('uid,groupid')
            ->select();
        foreach ($list as $vo) {
            $userArr[$vo['uid']] = $vo['groupid'];
        }
        
//         $auth_map['manager_id'] = $this->mid;
        $auth_map['pbid'] = get_pbid();
        $groups = M('auth_group')->where(wp_where($auth_map))
            ->field('id,wechat_group_id')
            ->select();
        foreach ($groups as $g) {
            $groupArr[$g['id']] = $g['wechat_group_id'];
            $wechatArr[$g['wechat_group_id']] = $g['id'];
        }
        
        M('auth_group_access')->whereIn('uid', $uids)->delete();
        
        $list = M('auth_group_access')->whereIn('uid', $uids)->select();
        
        $access = [];
        foreach ($list as $vo) {
            $access[$vo['uid']] = $vo['group_id'];
        }
        
        foreach ($uids as $uid) {
            $new_groupid = isset($userArr[$uid]) ? $userArr[$uid] : 0;
            $access_id = isset($access[$uid]) ? $access[$uid] : 0;
            $old_groupid = isset($groupArr[$access_id]) ? $groupArr[$access_id] : 0;
            
            if (isset($access[$uid]) && $new_groupid == $old_groupid) {
                continue;
            }
            $save['group_id'] = isset($wechatArr[$new_groupid]) ? $wechatArr[$new_groupid] : 0;
            if (isset($access[$uid])) {
                $amap['uid'] = $uid;
                $amap['group_id'] = $access_id;
                $res = M('auth_group_access')->where(wp_where($amap))->update($save);
            } else {
                $save['uid'] = $uid;
                $access[$uid] = M('auth_group_access')->insertGetId($save);
            }
            D('common/User')->getUserInfo($uid,true);
        }
        M('public_follow')->where(wp_where($map2))
            ->whereIn('uid', $uids)
            ->setField('syc_status', 2);
        
        return $this->jump(U('syc_user_group?uid=' . $uid), '用户分组信息同步中，请勿关闭');
    }

    function set_remark()
    {
        $map['uid'] = I('uid/d', 0);
        if (empty($map['uid'])) {
            $this->error('用户信息出错');
        }
        
        $param['remark'] = I('remark');
        if (empty($param['remark'])) {
            $this->error('备注不能为空');
        }
        
        $map['pbid'] = get_pbid();
        
        $info = M('public_follow')->where(wp_where($map))->find();
        if (! $info) {
            $this->error('用户信息出错啦');
        }
        
        $res = M('public_follow')->where(wp_where($map))->update($param);
        if ($res !== false) { // 同步到微信端
            D('common/User')->getUserInfo($map['uid'], true);
            if (config('USER_REMARK')) {
                $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=' . get_access_token();
                $param['openid'] = $info['openid'];
                $result = $this->post_data($url, $param);
            }
        } else {
            $this->error('保存数据库失败');
        }
        
        $this->success('设置成功');
    }

    function clear_score()
    {
        $pbid = get_pbid();
        $map['pbid'] = $pbid;
        $users = M('public_follow')->where(wp_where($map))->column('uid');
        $scoresave['score'] = 0;
        $userdao = D('common/User');
        // 每个用户积分清0
        foreach ($users as $uid) {
            if (empty($uid)) {
                continue;
            }
            $uidArr[$uid] = $uid;
            // $userdao->updateInfo ( $uid, $scoresave );
        }
        $userMap['score'] = array(
            'neq',
            0
        );
        $userdao->where(wp_where($userMap))
            ->whereIn('uid', $uidArr)
            ->update($scoresave);
        foreach ($uidArr as $u) {
            $key = 'getUserInfo_' . $u;
            S($key, null);
        }
        // 积分记录
        $creditMap['wpid'] = $wpid;
        M('credit_data')->where(wp_where($creditMap))->delete();
        // 会员卡设置等级
        if (is_install('card')){
        	$firstlevel = M('card_level')->where(wp_where($map))
        	->whereIn('uid', $uidArr)
        	->order('score asc')
        	->value('id');
        	// 会员卡等级都设为体验卡
        	$cardMap1[] = array(
        			'uid',
        			'in',
        			$uidArr
        	);
        	$cardMap1[] = array(
        			'level',
        			'gt',
        			0
        	);
        	$savecardlev['level'] = intval($firstlevel);
        	M('card_member')->where(wp_where($cardMap1))->update($savecardlev);
        }
       
        echo 1;
    }

    function jump($url, $msg)
    {
        $this->assign('url', $url);
        $this->assign('msg', $msg);
        return $this->fetch('loading');
    }
}
