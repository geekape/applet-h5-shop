<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\weixin\controller;

use app\common\controller\WebBase;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class UserTag extends WebBase {
	public $model = '';
	//public $syc_wechat = '';
	public $err_code = [
	    '-1' => '系统繁忙',
	    '45157' => '标签重名',
	    '45158' => '标签名长度超过30个字节',
	    '45056' => '标签不能超过100个',
	    '45058' => '不能修改默认标签',
	    '45057' => '该标签粉丝数超过10w，不允许直接删除',
	    '40003' =>	'传入非法的openid',
        '45159' =>	'非法的tag_id'
	];
	function initialize() {
	    parent::initialize();
	    //$this->syc_wechat = config( 'USER_LIST' );
		$this->model = $this->getModel ( 'user_tag' );
	}
	// 通用插件的列表模型
	public function lists() {
	    $map['wpid']=get_wpid();
	    session ( 'common_condition' ,$map);
	    $this->assign('search_url',U('lists'));
		return parent::common_lists ( $this->model, 'lists' );
	}

	// 通用插件的编辑模型
	public function edit() {
	    if(request()->isAjax()){
	        $id = input('id');
	        $title = input('title');
	        if ($id =='' || $title ==  ''){
	            $this->error('参数错误');
	        }
	        $tag_info = D('common/UserTag')->getById($id);
	        if(public_interface('user_group') && $tag_info['title'] != $title && $tag_info['wid']){
	            //添加的用户组名同步到微信端
	            $access_token = get_access_token();
	            $url = 'https://api.weixin.qq.com/cgi-bin/tags/update?access_token=' . $access_token;

	            $param ['tag']= ['name' => $title,'id'=>$tag_info['wid']];
	            $param = json_url( $param );
	            $res = post_data ( $url, $param );//dump($res);
	            if(isset($res['errcode']) && $res['errcode'] != 0){
	                $this->error($this->err_code[$res['errcode']]);
	                exit;
	            }
	        }
	    }
		return parent::common_edit ( $this->model, 0, 'edit' );
	}

	// 通用插件的增加模型
	public function add($model = null, $templateFile = '') {
	    is_array ( $model ) || $model = $this->model;
	    public_interface('user_group');
		if (IS_POST) {
			$data = input('post.');
			$data['type'] = 0; // 目前只能增加微信管理组
			$data['wpid'] = get_wpid ();
			$data['mTime'] = NOW_TIME;
			$has=$this->checkTitle(input('post.title'));
			if ($has > 0){
			    $this->error('该分组名已经存在！');
			}
			$Model = D ($model ['name']);
			// 获取模型的字段信息
			$data = $this->checkData($data, $model);
			$id = $Model->insertGetId($data);
			if ($id) {

			    //if ($this->syc_wechat && $title != $data ['title'] && ! empty ( $data ['wechat_group_id'] )) {
			    if(public_interface('user_group')){
			        //添加的用户组名同步到微信端
			        $access_token = get_access_token();
			        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=' . $access_token;

			        //$param ['group'] ['id'] = $data ['wechat_group_id'];
			        $param ['tag'] ['name'] = input('post.title');
			        $param = json_url( $param );
			        $res = post_data ( $url, $param );//dump($res);
			        if(isset($res['errcode'])){
			            $Model->where('id', $id)->delete();
                        $this->error($this->err_code[$res['errcode']]);
			        }
			        $Model ->where('id', $id)->update(['wid'=>$res['tag']['id']]);
			    }
			    //}
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {//dump($_SESSION);
			$fields = get_model_attribute ( $model );
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];

			return $this->fetch( 'add' );
		}
	}

	// 通用插件的删除模型
	public function del() {
	    if(request()->isAjax()){
	        ! empty($ids) || $ids = I('id');
	        ! empty($ids) || $ids = array_filter(array_unique((array) I('ids', 0)));
	        ! empty($ids) || $this->error('请选择要操作的数据!');
	        !is_array($ids) && $ids= [$ids];

	        foreach ($ids as $v){
	            $tag_info = D('common/UserTag')->getById($v);
	            if(public_interface('user_group') && $tag_info['wid']){
	                //添加的用户组名同步到微信端
	                $access_token = get_access_token();
	                $url = 'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=' . $access_token;

	                $param ['tag']= ['id'=>$tag_info['wid']];
	                $param = json_url( $param );
	                $res = post_data ( $url, $param );//dump($res);
	                if(isset($res['errcode']) && $res['errcode'] != 0){
	                    $this->error($this->err_code[$res['errcode']]);
	                    exit;
	                }
	                M( 'user_tag_link' )->delete($v);
	            }
	        }
	    }
		return parent::common_del ( $this->model );
	}
	function checkTitle($title,$id=0){
	    $tLen = strlen($title);
	    if ($tLen > 30) {
	        $this->error('标签名称不能超过30个字符，或10个汉字！');
	    }
	    $zStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $title);
	    $zLen=strlen($zStr);
	    $zStr = preg_replace('/[^A-Za-z0-9]/u', '', $title);
	    $yLen=strlen($zStr);
	    if ($zLen + $yLen != $tLen){
	        $this->error('分组名称不能有特殊字符！');
	    }
	    $map['title']=$title;
	    $map['type']=0;
	    $map['pbid']=get_pbid();
	    if ($id){
	        $map['id']=array('neq',$id);
	    }
	    $count=M( 'user_tag' )->where( wp_where($map) )->count();
	    return intval($count);
	}
}