<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: ouyangessen
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use app\ask\Info;

/**
 * 权限管理控制器
 * Class AuthManagerController
 */
class Rules extends Admin {
    /*
     * 新增/编辑角色
     */
    public function createTag(){
        $this->redirect('editTag');
    }
    public function editTag(){
        $id = input('id');
        if(request()->isPost()){
            $title = input('title');
            empty($title) && $this->error('角色名不能为空');
            $data = array(
                'title' => $title,
                'mTime' => NOW_TIME,
                'type' => 1
            );

            if($id){
                $re = M( 'user_tag' )->where('id', $id)->update($data);
            }else{
                $re = M( 'user_tag' )->insert($data);
            }
            if ($re!==false){
                $url = U('index');
                $this->success('添加成功',$url);
            }else{
                $this->error('添加失败'.M( 'user_tag' )->getError());
            }
        }else{

            if ($id){
                $tagInfo = D('Rules')->getTagInfo($id);
                $this->assign('id',$id);
                $this->assign('tagInfo',$tagInfo);
            }

            return $this->fetch('edit');
        }
    }
    /*
     * 删除角色
     */
    public function deleteTag(){
        $ids = (array)input('ids');
        empty($ids) && $this->error('参数错误');
        $re = M( 'user_tag' )->where( wp_where(array(array('id', 'in',$ids) )))->delete();
        if ($re){
            $url = U('index');
            $this->success('删除成功',$url);
        }else{
            $this->error('删除失败'.M( 'user_tag' )->getError());
        }
    }
    /*
     * 删除用户角色关系
     */
    public function deleteUserTag(){
        $ids = (array)input('ids');
        $id = input('id');
        empty($ids) && $this->error('参数错误');
        $re = M( 'user_tag_link' )->where( wp_where(array(array('id', 'in',$ids) )))->delete();
        if ($re){
            $url = U('members',array('id'=>$id));
            $this->success('删除成功',$url);
        }else{
            $this->error('删除失败'.M( 'user_tag_link' )->getError());
        }
    }
    /*
     * 角色成员
     */
    public function members(){
        $id = input('id');
        empty($id) && $this->error('参数错误');
        $tagInfo = D('rules')->getTagInfo($id);
        $this->assign('tagInfo',$tagInfo);
        $users = M( 'user_tag_link' )->field('uid,id')->where('tag_id',$id)->select();
        if(!empty($users)){
            foreach ($users as $v){
                $tmp = getUserInfo($v['uid']);
                $tmp['id'] = $v['id'];
                $usersInfo [] = $tmp;
            }
            $this->assign('_list',$usersInfo);
        }
        $this->assign('id',$id);
        return $this->fetch();
    }
    /*
     * 角色成员编辑
     */
    public function changeTag(){
        $type = input('type');
        $tag_id = input('tag_id');

        $id = array_unique ( ( array ) I ( 'id', 0 ) );

        if (empty ( $id )) {
            $this->error ( '请选择用户!' );
        }
        $group_id = I ( 'group_id', 0 );
        if (empty ( $tag_id )) {
            $this->error ( '请选择角色!' );
        }
        $data ['uid'] = array (
            'in',
            $id
        );
        $data ['tag_id'] = $tag_id;
        M( 'user_tag_link' )->where ( wp_where( $data ) )->delete ();

        $type = I ( 'type', 0 );
        if ($type != 0)
            die ( 1 );
        $data['cTime'] = NOW_TIME;
        foreach ( $id as $uid ) {
            $data ['uid'] = $uid;
            $res = M( 'user_tag_link' )->insertGetId( $data );

            // 更新用户缓存
            D('common/User' )->getUserInfo ( $uid, true );
        }
        echo 1;
    }
    /*
     * 角色权限节点显示
     */
    public function rules(){
        $id = input('id');
        empty($id) && $this->error('参数错误');
        //查找应用以及子权限节点
        $map['status'] = 1;
        $data = M( 'apps' )->where( wp_where($map) )
        ->order('id DESC')
        ->select();
        $this->initInfo($data);
        $list = D('common/AuthRule')->getAll();
        foreach ($data as &$v){
            foreach ($list as $k=>$lv){
                if(strtolower($v['name']) == strtolower($lv['mod'])){
                    $v['auth'][] = $lv;
                    unset($list[$k]);
                }
            }
        }//dump($data);
        $this->assign('data',$data);
        $tagInfo = M( 'user_tag' )->where('id', $id)->find();
        $this->assign('tagInfo',$tagInfo);
        $this->assign('rule_arr',explode(',', $tagInfo['rule']));
        return $this->fetch();
    }
    /*
     * 更新权限节点
     */
    public function updateRule(){
        $rid = input('rid');
        empty($rid) && $this->error('参数错误');
        $flag = input('flag');
        $id = input('id');
        $mod =  input('mod');
        D('common/AuthRule')->updateRule($rid,$flag,$id,$mod);
        //清空缓存
        S('AuthRule_getList',null);
    }
    /*
     * 初始化自定义权限配置
     */
    private function initInfo($data){
        $dir = env('app_path') ;
        $AuthRule = D('common/AuthRule');
        $datas = [];
        $map = array('type'=>'custom_app');
        //取出所有app中最新的info权限配置
        if(!empty($data) && is_array($data)){
            foreach ($data as $v){
                $mod = strtolower($v['name']);
                $file_path = $dir .$mod.'/info.php';
                if (file_exists($file_path)) {

                    $class = '\\app\\'.$mod.'\\Info';
                    $info = new $class;
                    if(isset($info->auth_rule) && !empty($info->auth_rule)){

                        foreach ($info->auth_rule as $k=>$v){
                            if( $k == '' || $v == '' || $mod == '' )  continue;
                            $datas[] = array(
                                'title' =>$v,
                                'name' => $k,
                                'mod' => $mod,
                                'type' =>  'custom_app'
                            );
                        }
                    }
                }
            }
        }
        if (empty($datas)){//删除所有custom_app
            $AuthRule->where( wp_where($map) )->delete();
            return;
        }
        //数据表中custom_app数据
        $custom_app = $AuthRule->getAll(array('type'=>'custom_app'));

        if(!empty($custom_app)){
            //过滤
            foreach ($datas as $sk=>$sv){
                foreach ($custom_app as $ck=>$cv){
                    if ($sv['name'] == $cv['name'] && $sv['title'] == $cv['title']){
                        unset($datas[$sk]);
                        unset($custom_app[$ck]);
                        continue;
                    }
                }
            }
            if (!empty($custom_app)){
                //删除
                foreach ($custom_app as $v){
                    $ids[] = $v['id'];
                }
                $AuthRule::destroy($ids);
                //待补充去除user_tag中rule多余的id
            }
        }
        empty($datas) || $AuthRule->saveAll($datas);
    }
	/**
	 * 后台节点配置的url作为规则存入auth_rule
	 * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function updateRules() {
		// 需要新增的节点必然位于$nodes
		$nodes = $this->returnNodes ( false );

		// 需要更新和删除的节点必然位于$rules
		$rules = M( 'AuthRule' )->order ( 'name' )->select ();

		// 构建insert数据
		$data = []; // 保存需要插入和更新的新节点
		foreach ( $nodes as $value ) {
			$temp ['name'] = $value ['url'];
			$temp ['title'] = $value ['title'];
			$temp ['status'] = 1;
			$data [strtolower ( $temp ['name'] )] = $temp; // 去除重复项
		}

		$update = []; // 保存需要更新的节点
		$ids = []; // 保存需要删除的节点的id
		foreach ( $rules as $index => $rule ) {
			$key = strtolower ( $rule ['name'] );
			if (isset ( $data [$key] )) { // 如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
				$data [$key] ['id'] = $rule ['id']; // 为需要更新的节点补充id值
				$update [] = $data [$key];
				unset ( $data [$key] );
				unset ( $rules [$index] );
				unset ( $rule ['condition'] );
				$diff [$rule ['id']] = $rule;
			} elseif ($rule ['status'] == 1) {
				$ids [] = $rule ['id'];
			}
		}
		if (count ( $update )) {
			foreach ( $update as $k => $row ) {
				if ($row != $diff [$row ['id']]) {
					M( 'AuthRule' )->where ( 'id', $row ['id'] )->update( $row );
				}
			}
		}
		if (count ( $ids )) {
			M( 'AuthRule' )->whereIn ('id',$ids )->update( array (
					'status' => - 1
			) );
			// 删除规则是否需要从每个用户组的访问授权表中移除该规则?
		}
		if (count ( $data )) {
			M( 'AuthRule' )->insertAll ( array_values ( $data ) );
		}
		if (M( 'AuthRule' )->getDbError ()) {
			trace ( '[' . __METHOD__ . ']:' . M( 'AuthRule' )->getDbError () );
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 权限管理首页--角色列表
	 */
	public function index() {
		$map ['type'] = 1;
		$list = $this->lists_data ( 'UserTag', $map, 'id desc' );
		$this->assign ( '_list', $list );
		$this->assign ( '_use_tip', true );
		$this->meta_title = '权限管理';
		return $this->fetch();
	}
	/**
	 * 微信接口节点管理首页
	 */
	public function wechat() {
		$list = $this->lists_data ( 'PublicAuth', '' );
		$list = int_to_string ( $list );
		$this->assign ( '_list', $list );
		$this->assign ( '_use_tip', true );
		$this->meta_title = '微信接口';
		return $this->fetch();
	}

	/**
	 * 创建新规则
	 */
	public function createRule() {
		$this->meta_title = '新增规则';
		$temp = $_GET ['group'] == 'wechat' ? 'wechat_edit' : 'edit';
		return $this->fetch( $temp );
	}

	/**
	 * 编辑规则
	 */
	public function editRule() {
		$auth_group = M( 'AuthRule' )->find ( input('id') );
		$this->assign ( 'auth_rule', $auth_group );
		$this->meta_title = '编辑规则';
		return $this->fetch( 'edit' );
	}

	/**
	 * 访问授权页面
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function access() {
		$this->updateRules ();
		$auth_group = M( 'AuthGroup' )->where ('status','>=',0 )->field ( 'id,title,rules' )->find();
		$node_list = $this->returnNodes ();
		$map = array (
				'status' => 1
		);
		$main_rules = M( 'AuthRule' )->where ( wp_where( $map ) )->column( 'id','name' );
		$map = array (
				'status' => 1
		);
		$child_rules = M( 'AuthRule' )->where ( wp_where( $map ) )->column( 'id', 'name' );

		$this->assign ( 'main_rules', $main_rules );
		$this->assign ( 'auth_rules', $child_rules );
		$this->assign ( 'node_list', $node_list );
		$this->assign ( 'auth_group', $auth_group );
		$gid = input('group_id/d', 0);
		$this->assign ( 'this_group', $auth_group [$gid] );
		$this->meta_title = '访问授权';
		return $this->fetch( 'managergroup' );
	}

	/**
	 * 规则数据写入/更新
	 */
	public function writeRule() {
		$dao = D ( 'AuthRule' );
		$data = input('post.');
		if ($data) {
			if (empty ( $data ['id'] )) {
				$r = $dao->insertGetId();
			} else {
				$r = $dao->isUpdate(true)->save();
			}
			if ($r === false) {
				$this->error ( '操作失败' . $dao->getError () );
			} else {
				$url = $data ['group'] == 'wechat' ? U ( 'wechat' ) : U ( 'index' );
				$this->success ( '操作成功!', $url );
			}
		} else {
			$this->error ( '操作失败' . $dao->getError () );
		}
	}

	/**
	 * 状态修改
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		if (empty ( input('id') )) {
			$this->error ( '请选择要操作的数据!' );
		}
		switch (strtolower ( $method )) {
			case 'forbidrule' :
				$this->forbid ( 'AuthRule' );
				break;
			case 'resumerule' :
				$this->resume ( 'AuthRule' );
				break;
			case 'deleterule' :
				$this->delete ( 'AuthRule' );
				break;
			default :
				$this->error ( $method . '参数非法' );
		}
	}

	/**
	 * 用户组授权用户列表
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function user($group_id) {
		if (empty ( $group_id )) {
			$this->error ( '参数错误' );
		}

		$auth_group = M( 'AuthGroup' )->where ( 'status','>=',0)->field ( 'id,title,rules' )->find();
		$prefix = DB_PREFIX;
		$l_table = $prefix . (AuthGroup::MEMBER);
		$r_table = $prefix . (AuthGroup::AUTH_GROUP_ACCESS);
		$model = M()->table ( $l_table )->alias('m')->join ( $r_table . ' a','m.uid=a.uid' );
		$_REQUEST = [];
		$list = $this->lists_data ( $model, array (
				'a.group_id' => $group_id,
				'm.status' => array (
						'egt',
						0
				)
		), 'm.uid asc', 'm.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status' );
		int_to_string ( $list );
		$this->assign ( '_list', $list );
		$this->assign ( 'auth_group', $auth_group );
		$gid = input('group_id/d', 0);
		$this->assign ( 'this_group', $auth_group [$gid] );
		$this->meta_title = '成员授权';
		return $this->fetch();
	}
	public function tree($tree = null) {
		$this->assign ( 'tree', $tree );
		return $this->fetch( 'tree' );
	}

	/**
	 * 将用户添加到用户组的编辑页面
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function group() {
		$uid = I ( 'uid' );
		$auth_groups = D ( 'AuthGroup' )->getGroups ();
		$user_groups = AuthGroup::getUserGroup ( $uid );
		$ids = [];
		foreach ( $user_groups as $value ) {
			$ids [] = $value ['group_id'];
		}
		$nickname = D('common/User' )->getNickName ( $uid );
		$this->assign ( 'nickname', $nickname );
		$this->assign ( 'auth_groups', $auth_groups );
		$this->assign ( 'user_groups', implode ( ',', $ids ) );
		$this->meta_title = '用户组授权';
		return $this->fetch();
	}

	/**
	 * 将用户添加到用户组,入参uid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function addToGroup() {
		$uid = I ( 'uid' );
		$gid = I ( 'group_id' );
		if (empty ( $uid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (is_numeric ( $uid )) {
			if (is_administrator ( $uid )) {
				$this->error ( '该用户为超级管理员' );
			}
			if (! M( 'User' )->where ( 'uid', $uid)->find ()) {
				$this->error ( '用户不存在' );
			}
		}

		if ($gid && ! $AuthGroup->checkGroupId ( $gid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToGroup ( $uid, $gid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( $AuthGroup->getError () );
		}
	}

	/**
	 * 将用户从用户组中移除 入参:uid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function removeFromGroup() {
		$uid = I ( 'uid' );
		$gid = I ( 'group_id' );
		if ($uid == UID) {
			$this->error ( '不允许解除自身授权' );
		}
		if (empty ( $uid ) || empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->where('id', $gid)->find()) {
			$this->error ( '用户组不存在' );
		}
		if ($AuthGroup->removeFromGroup ( $uid, $gid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}

	/**
	 * 将分类添加到用户组 入参:cid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function addToCategory() {
		$cid = I ( 'cid' );
		$gid = I ( 'group_id' );
		if (empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->where('id', $gid)->find()) {
			$this->error ( '用户组不存在' );
		}
		if ($cid && ! $AuthGroup->checkCategoryId ( $cid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToCategory ( $gid, $cid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}

	/**
	 * 将模型添加到用户组 入参:mid,group_id
	 *
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 */
	public function addToModel() {
		$mid = I ( 'id' );
		$gid = I ( 'group_id' );
		if (empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->where('id', $gid)->find()) {
			$this->error ( '用户组不存在' );
		}
		if ($mid && ! $AuthGroup->checkModelId ( $mid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToModel ( $gid, $mid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}
}
