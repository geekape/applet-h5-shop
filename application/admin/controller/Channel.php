<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 后台频道控制器
 */

class Channel extends Admin {

    /**
     * 频道列表
     */
    public function index(){
        $pid = I ( 'pid', 0);
        /* 获取频道列表 */
        $map  = array('status' => array('gt', -1), 'pid'=>$pid);
        $list = M( 'Channel' )->where( wp_where($map) )->order('sort asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '导航管理';
        return $this->fetch();
    }

    /**
     * 添加频道
     */
    public function add(){
        if(request()->isPost()){
            $Channel = D('Channel');
            $data = input('post.');
            if($data){
                $id = $Channel->insertGetId($data);
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_channel', 'channel', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Channel->getError());
            }
        } else {
            $pid = I ( 'pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M( 'Channel' )->where( wp_where(array('id'=>$pid) ))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增导航';
            return $this->fetch('edit');
        }
    }

    /**
     * 编辑频道
     */
    public function edit($id = 0){
        if(request()->isPost()){
            $Channel = D('Channel');
            $data = input('post.');
            if($data){
                if($Channel->isUpdate(true)->save($data)){
                    //记录行为
                    action_log('update_channel', 'channel', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Channel->getError());
            }
        } else {
            $info = [];
            /* 获取数据 */
            $info = M( 'Channel' )->where('id', $id)->find();

            if(false === $info){
                $this->error('获取配置信息错误');
            }

            $pid = I ( 'pid', 0);
            //获取父导航
            if(!empty($pid)){
            	$parent = M( 'Channel' )->where( wp_where(array('id'=>$pid) ))->field('title')->find();
            	$this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑导航';
            return $this->fetch();
        }
    }

    /**
     * 删除频道
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array(array('id', 'in', $id) );
        if(M( 'Channel' )->where( wp_where($map) )->delete()){
            //记录行为
            action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            $ids = I ( 'ids');
            $pid = I ( 'pid');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
			$map2 = [];
            if(!empty($ids)){
                $map2[] = array('id','in',$ids);
            }else{
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M( 'Channel' )->where( wp_where($map) )->where( wp_where($map2) )->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            return $this->fetch();
        }elseif (request()->isPost()){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M( 'Channel' )->where( wp_where(array('id'=>$value) ))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
