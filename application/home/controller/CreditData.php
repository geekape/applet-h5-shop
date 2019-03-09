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
class CreditData extends Home {
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( CONTROLLER_NAME );
		$nav = [];
		$res ['title'] = '积分配置';
		$res ['url'] = U ( 'CreditConfig/lists' );
		$res ['class'] = $act == 'creditconfig' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '积分记录';
		$res ['url'] = U ( 'CreditData/lists' );
		$res ['class'] = $act == 'creditdata' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
		$_GET ['sidenav'] = 'home_creditconfig_lists';
	}
	public function lists() {
		$top_more_button [] = array (
				'title' => '导入数据',
				'url' => U ( 'import' ) 
		);
		
		$this->assign ( 'top_more_button', $top_more_button );
		$model = $this->getModel ( 'credit_data' );
		
		$map ['wpid'] = get_wpid ();
		$_REQUEST=input('param.');
		if (! empty ( $_GET ['uid'] )) {
		    $uidArr=wp_explode($_GET['uid']);
			$map ['uid'] =array('in',$uidArr);
		} elseif (! empty ( $_REQUEST ['nickname'] )) {
		    $uids =D('common/User' )->searchUser ( $_REQUEST ['nickname'] );
		    
		    if($uids){
		       
			$map ['uid'] = array (
					'in',
					$uids
			);
		}else{
		   
		    $map1['openid']=array(
		        'like',
		        '%'.$_REQUEST ['nickname'].'%'
		        
		    );

		    $map1['pbid'] = get_pbid ();
		   
		   // dump($map1);
		    $uids1 =M( 'public_follow' )->where ( wp_where( $map1 ) )->column( 'uid' );
		    $where['uid'] = array (
		        'in',
		        $uids1
		    );
		  // $where['nickname']= null;
		  $uids2 =M( 'user' )->where( wp_where($where) )->column('uid');
		//dump($uids2);
		   $map['uid'] = array (
		       'in',
		       $uids2
		   );
		}
		}
		if (! isset ( $map ['uid'] )) {
			$map ['uid'] = array (
					'exp',
					'>0' 
			);
		}
		
		if (! empty ( $_REQUEST ['credit_name'] )) {
			$map ['credit_name'] = safe ( $_REQUEST ['credit_name'] );
		}
		
		if (! empty ( $_REQUEST ['start_time'] ) && ! empty ( $_REQUEST ['end_time'] )) {
			$map ['cTime'] = array (
					'between',
					'"' . intval ( $_REQUEST ['start_time'] ) . ',' . intval ( $_REQUEST ['start_time'] ) . '"' 
			);
		} elseif (! empty ( $_REQUEST ['start_time'] )) {
			$map ['cTime'] = array (
					'egt',
					intval ( $_REQUEST ['start_time'] ) 
			);
		} elseif (! empty ( $_REQUEST ['end_time'] )) {
			$map ['cTime'] = array (
					'elt',
					intval ( $_REQUEST ['end_time'] ) 
			);
		}
		//dump($map);
		session ( 'common_condition', $map );

		$list_data = $this->_get_model_list ( $model );
		
		foreach ( $list_data ['list_data'] as &$vo ) {
		    if(get_nickname ( $vo ['uid'] )){
			$vo ['uid'] = get_nickname ( $vo ['uid'] );
		    }else{
		    $vo ['uid'] = D('common/Follow')->getOpenidByUid($vo ['uid']);
		    }
		}
		
		$this->assign ( $list_data );
		
		
		return $this->fetch();
	}
	public function add() {
		$model = $this->getModel ( 'credit_data' );
		if (request()->isPost()) {
			$Model = D ($model ['name']);
			// 获取模型的字段信息
			$data = input('post.');
			$data = $this->checkData($data, $model);
			if (false!==($id = $Model->save($data))) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clearCache') && $Model->clearCache( $id, 'edit' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model );
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			return $this->fetch( 'common@base/add' );
		}
	}
	public function edit($id = 0) {
		$model = $this->getModel ( 'credit_data' );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M( $model ['name'] )->where('id', $id)->find();
		$data || $this->error ( '数据不存在！' );
		
		if (request()->isPost()) {
		    $data = input('post.');
			$act = 'update';
// 			if ($data ['wpid'] == 0) {
// 				$data ['wpid'] = get_wpid ();
// 				unset ( $data ['id'] );
// 				$act = 'insert';
// 			}
			$Model = D ($model ['name']);
			// 获取模型的字段信息
			
			$data = $this->checkData($data, $model);
			if ($Model->$act ($data)!==false) {
				// dump($Model->getLastSql());
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				// dump($Model->getLastSql());
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			return $this->fetch( 'common@base/edit' );
		}
	}
	function del() {
		$model = $this->getModel ( 'credit_data' );
		return parent::common_del ( $model );
	}
	function credit_data() {
		$model = $this->getModel ( 'credit_data' );
		
		$map ['wpid'] = get_wpid ();
		session ( 'common_condition', $map );
		
		return parent::common_lists ( $model, 'common@base/lists' );
	}
	function import() {
		$model = $this->getModel ( 'import' );
		if (request()->isPost()) {
			$column = array (
					'A' => 'uid',
					'B' => 'credit_title',
					'C' => 'score',
					'D' => 'cTime' 
			);
			
			$attach_id = I ( 'attach', 0 );
			$dateCol = array (
					'D' 
			);
			$res = importFormExcel ( $attach_id, $column, $dateCol );
			if ($res ['status'] == 0) {
				$this->error ( $res ['data'] );
			}
			$total = count ( $res ['data'] );
			$uidStr='';
			foreach ( $res ['data'] as $vo ) {
			    $uidStr.=$vo['uid'].',';
				if (empty ( $vo ['credit_title'] )) {
					$vo ['credit_title'] = '手动导入';
				}
				if (empty ( $vo ['cTime'] )) {
					$vo ['cTime'] = time ();
				} else {
					$vo ['cTime'] = strtotime ( $vo ['cTime'] );
				}
				
				add_credit ( 'auto_add', $vo, 0 );
			}
			$msg = "共导入" . $total . "条记录";
			// dump($arr);
			// $msg = trim( $msg, ', ' );
			// dump($msg);exit;
			
			$this->success ( $msg, U ( 'lists' ,array('uid'=>$uidStr)) );
		} else {
			$fields = get_model_attribute ( $model );
			$this->assign ( 'fields', $fields );
			
			$this->assign ( 'post_url', U ( 'import' ) );
			$this->assign ( 'import_template', 'score_import.xls' );
			return $this->fetch( 'common@base/import' );
		}
	}
}