<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 朱亚杰 <zhuyajie@topthink.net>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;

/**
 * 权限管理控制器
 * Class AuthManagerController
 *
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
class AuthManager extends Admin {

	/**
	 * 后台节点配置的url作为规则存入auth_rule
	 * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function updateRules() {
		// 需要新增的节点必然位于$nodes
		$nodes = $this->returnNodes ( false );

		$map = array (
				'module' => 'admin'
		); // status全部取出,以进行更新
		   // 需要更新和删除的节点必然位于$rules
		$rules = M( 'AuthRule' )->where ( wp_where( $map ) )->whereIn('type','1,2')->order ( 'name' )->select ();

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
			$key = strtolower ( $rule ['name'] . $rule ['module'] . $rule ['type'] );
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
					M( 'AuthRule' )->where ( 'id', $row ['id'])->update( $row );
				}
			}
		}
		if (count ( $ids )) {
			M( 'AuthRule' )->whereIn ( 'id', $ids )->update( array (
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
	 * 用户组管理首页
	 */
	public function index() {
		$map ['manager_id'] = 0;
		$map ['type'] = array (
				'exp',
				'!=4'
		);
		$list = $this->lists_data ( 'AuthGroup', $map, 'id desc' );
		$list = int_to_string ( $list );

		$type_arr = array (
				0 => '普通用户组',
				1 => '微信用户组',
				2 => '等级用户组',
				3 => '认证用户组'
		);
		// 4=>'公众号分组' 这类程序写死，不可增加，删除等操作
		foreach ( $list as &$v ) {
			$v ['type'] = $type_arr [$v ['type']];
		}

		$this->assign ( '_list', $list );

		$this->assign ( '_use_tip', true );
		$this->meta_title = '用户组管理';
		return $this->fetch();
	}
	/**
	 * 公众号组管理首页
	 */
	public function wechat() {
		// 获取微信权限节点
		//$list = M( 'public_auth' )->select ();
		//$this->assign ( 'list_data', $list );
		// dump ( $list );

		$this->meta_title = '用户组管理';
		$wx_type = M( 'user_tag' )->where('type',2)->select();
		//dump($wx_type);
		$this->assign('wx_type',$wx_type);
		$list = M( 'auth_rule' )->where('type','public_interface')->order('id asc')->select ();
		//dump($list);
		$this->assign ( 'list_data', $list );
		return $this->fetch();
	}
	function set_switch() {
		$tid = I ( 'tid' );
		$rid = I ( 'rid' );
		$val = I ( 'val' );
		$val = $val == 'true' ? 1 : 0;

		if ($tid && $rid && $val!==''){
		    $map = [
		        'id'=>$tid,
		        'type'=>2
		    ];
		    $rules = M( 'user_tag' )->where( wp_where($map) )->value('rule');
		    $rules = explode(',', $rules);
		    if($val){//添加
		        array_push($rules, $rid);
                $rules = array_filter(array_unique($rules));
		    }else{//删除
		        if(is_array($rules)){
		            foreach ($rules as $k=>$rv){
		                if($rv == $rid){
		                    unset($rules[$k]);
		                }
		            }
		        }
		    }
		    $rules = implode(',', $rules);
		    $res = M( 'user_tag' )->setField('rule', $rules );

		    $returnData ['status'] = $res;
		}
		if (isset($res) && $res) {
			/*S ( 'PUBLIC_AUTH_0', NULL );
			S ( 'PUBLIC_AUTH_1', NULL );
			S ( 'PUBLIC_AUTH_2', NULL );
			S ( 'PUBLIC_AUTH_3', NULL );*/
		    public_interface();
			$returnData ['info'] = "设置保存成功";
		} else {
			$returnData ['info'] = "设置保存失败";
		}
		return json_encode($returnData);
	}
	/**
	 * 创建管理员用户组
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function createGroup() {
		if (empty ( $this->auth_group )) {
			$this->assign ( 'auth_group', array (
					'title' => null,
					'id' => null,
					'description' => null,
					'rules' => null
			) ); // 排除notice信息
		}
		$this->meta_title = '新增用户组';
		return $this->fetch( 'editgroup' );
	}

	/**
	 * 编辑管理员用户组
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function editGroup() {
		$auth_group = M( 'AuthGroup' )->find ( input('id') );
		$this->assign ( 'auth_group', $auth_group );
		$this->meta_title = '编辑用户组';
		return $this->fetch();
	}

	/**
	 * 访问授权页面
	 */
	public function access() {
		$map ['id'] = I ( 'group_id/d', 0 );
		$auth_group = M( 'AuthGroup' )->where ( wp_where( $map ) )->field ( 'id,title,rules' )->find();

		$map2 ['status'] = 1;
		$rules = M( 'AuthRule' )->where ( wp_where( $map2 ) )->field ( true )->select ();
		foreach ( $rules as $vo ) {
			$node_list [$vo ['group']] [] = $vo;
		}
		
		$gid = input('group_id/d', 0);
		$this->assign ( 'node_list', $node_list );
		$this->assign ( 'auth_group', $auth_group );
		$this->assign ( 'this_group', $auth_group [$gid] );
		$this->meta_title = '访问授权';
		return $this->fetch( 'managergroup' );
	}

	/**
	 * 管理员用户组数据写入/更新
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function writeGroup() {
		$data = input('post.');
		if (input('?post.rules')) {
			$post = input('post.rules');
			sort ( $post );
			$data['rules'] = implode ( ',', array_unique ( $post ) );
		}

		$AuthGroup = D ( 'AuthGroup' );
		
		if ($data) {
			if (empty ( $data ['id'] )) {
				$r = $AuthGroup->insertGetId($data);
			} else {
				$r = $AuthGroup->isUpdate(true)->save($data);
			}
			if ($r === false) {
				$this->error ( '操作失败' . $AuthGroup->getError () );
			} else {
				$this->success ( '操作成功!', U ( 'index' ) );
			}
		} else {
			$this->error ( '操作失败' . $AuthGroup->getError () );
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
			case 'forbidgroup' :
				$this->forbid ( 'AuthGroup' );
				break;
			case 'resumegroup' :
				$this->resume ( 'AuthGroup' );
				break;
			case 'deletegroup' :
				$this->delete ( 'AuthGroup' );
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

		$auth_group = M( 'AuthGroup' )->field ( 'id,title,rules' )->select ();
		$prefix = DB_PREFIX;
		$l_table = $prefix . (AuthGroup::MEMBER);
		$r_table = $prefix . (AuthGroup::AUTH_GROUP_ACCESS);
		$model = M()->table ( $l_table )->alias('m')->join ( $r_table . ' a', 'm.uid=a.uid' );
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
			if (! M( 'User' )->where ('uid', $uid )->find ()) {
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
