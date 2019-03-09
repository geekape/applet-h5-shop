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
class Rule extends Admin {
	
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
	 * 权限管理首页
	 */
	public function index() {
		$map ['group'] = array (
				'exp',
				"!='wechat'" 
		);
		$list = $this->lists_data ( 'AuthRule', $map, 'id desc' );
		$list = int_to_string ( $list );
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
		$auth_group = M( 'AuthGroup' )->where ( 'status' ,'>=','0' )->field ( 'id,title,rules' )->find();
		$node_list = $this->returnNodes ();
		$map = array (
				'status' => 1 
		);
		$main_rules = M( 'AuthRule' )->where ( wp_where( $map ) )->column( 'id','name' );
		$map = array (
				'status' => 1 
		);
		$child_rules = M( 'AuthRule' )->where ( wp_where( $map ) )->column( 'id', 'name' );
		
		$gid = input('group_id/d', 0);
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
		
		$auth_group = M( 'AuthGroup' )->where ( 'status' ,'>=','0' )->field ( 'id,title,rules' )->find();
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
