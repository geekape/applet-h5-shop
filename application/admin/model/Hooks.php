<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

namespace app\admin\model;
use app\common\model\Base;


/**
 * 应用模型
 * @author yangweijie <yangweijiester@gmail.com>
 * @date    2013-08-14 11:31:21
 */

class Hooks extends Base {
	protected $table = DB_PREFIX. 'hooks';
	
    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
    protected $_validate = array(
        array('name','require','钩子名称必须！'), //默认情况下用正则进行验证
        array('description','require','钩子描述必须！'), //默认情况下用正则进行验证
    );

    /**
     * 文件模型自动完成
     * @var array
     */
//    protected $_auto = array(
//        array('update_time', NOW_TIME, MODEL_BOTH),
//        );

    /**
     * 更新应用里的所有钩子对应的应用
     */
    public function updateHooks($addons_name){
        $addons_class = get_addon_class($addons_name);//获取应用名
        if(!class_exists($addons_class)){
            $this->error = "未实现{$addons_name}应用的入口文件";
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->column('name');
        $common = array_intersect($hooks, $methods);
        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 更新单个钩子处的应用
     */
    public function updateAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->value('addons');
        if($o_addons)
            $o_addons = str2arr($o_addons);
        if($o_addons){
            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }
        $flag = D('Hooks')->where("name='{$hook_name}'")
        ->setField('addons',arr2str($addons));
        if(false === $flag)
            D('Hooks')->where("name='{$hook_name}'")->setField('addons',arr2str($o_addons));
        return $flag;
    }

    /**
     * 去除应用所有钩子里对应的应用数据
     */
    public function removeHooks($addons_name){
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->column('name');
        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 去除单个钩子里对应的应用数据
     */
    public function removeAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->value('addons');
        $o_addons = str2arr($o_addons);
        if($o_addons){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $flag = D('Hooks')->where("name='{$hook_name}'")
                          ->setField('addons',arr2str($addons));
        if(false === $flag)
            D('Hooks')->where("name='{$hook_name}'")
                      ->setField('addons',arr2str($o_addons));
        return $flag;
    }
}
