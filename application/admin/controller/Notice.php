<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 公告管理
 */
class Notice extends Admin {
	
	function lists() {
		$page_data = M(  'SystemNotice' )->paginate(15);
		$list = dealPage($page_data);	
			
        $this->assign('_list', $list);
		return $this->fetch();
	}
	
	/**
     * 新增公告
     * @author jacyxie <51daxigua@gmail.com>
     */
    public function add(){
		$postUrl = U('add');
		$this ->assign('postUrl',$postUrl);
        if(request()->isPost()){
			$post = input('post.');
			$post['create_time'] = time();
            $Model = D('SystemNotice');            
			if($post){
                $id = $Model->insertGetId($post);
                if($id){
                    $this->success('新增成功', U('lists'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error('数据不能为空');
            }
        } else {
            $this->assign('info',array('id'=>I('id')));
            return $this->fetch('edit');
        }
    }
	
	/**
     * 编辑配置
     * @author jacyxie <51daxigua@gmail.com>
     */
    public function edit(){
		$id = I('id');
		$postUrl = U('edit',array('id'=>$id));
		$this ->assign('postUrl',$postUrl);
        if(request()->isPost()){
            $Model = D('SystemNotice');
            $data = input('post.');
            if($data){
                if($Model->isUpdate(true)->save($data)!== false){
                   $this->success('更新成功', U('lists'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $info = [];
            /* 获取数据 */
            $info = M( 'SystemNotice' )->field(true)->where('id', $id)->find();
            
            if(false === $info){
                $this->error('获取公告信息错误');
            }
            $this->assign('info', $info);
            return $this->fetch();
        }
    }
	public function del(){
        $id = array_unique((array)I('id',0));
	
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array(array('id', 'in', $id) );
        if(M( 'SystemNotice' )->where( wp_where($map) )->delete()){
             $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
