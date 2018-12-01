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
 * 日志后台管理页面
 */
class Logs extends Admin
{

    public function initialize()
    {
        parent::initialize();
    }

    // 接口日志管理
    public function index()
    {
    	$type = 1;
    	$onoff = config('REQUEST_LOG');
    	$list = D('Logs')->getLogs($type);
    	$this->assign('_page', $list->render());
    	//$this->assign('_total', $total);
    	$list = $list->toArray();
    	$this->assign('_list',$list['data']);
    	$this->assign('meta_title','接口日志');
    	$this->assign('type',$type);
    	$this->assign('onoff',$onoff);
    	return $this->fetch();
    }

    /*
     * 设置日志开关
     */
    public function setOnOff(){
		$type = input('type');
		$logcof = input('logcof') == 0 ? 0 :1;
    	switch ($type){
    		case 1:
    			$logname = 'REQUEST_LOG';
    			break;
    		default:;
    	}
		$res = M( 'config' )->where('name',$logname)->setField('value', $logcof);
		if ($res){
			S('DB_CONFIG_DATA', null);
			$this->success('设置成功');
		}else {
			$this->error('设置失败');
		}
    }
}
