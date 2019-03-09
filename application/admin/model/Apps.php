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
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Apps extends Base
{

    protected $table = DB_PREFIX. 'apps';

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result, $options)
    {}

    protected function _after_select(&$result, $options)
    {
        foreach ($result as &$record) {
            $this->_after_find($record, $options);
        }
    }

    /**
     * 获取应用列表
     *
     * @param string $addon_dir            
     */
    public function getList($addon_dir = '')
    {
        if (! $addon_dir)
            $addon_dir = env('app_path');
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
        if ($dirs === FALSE || ! file_exists($addon_dir)) {
            $this->error = '应用目录不可读或者不存在';
            return FALSE;
        }
        
        $addons = $dirArr = [];
        foreach ($dirs as $dir) {
            $dirArr[] = parse_name($dir, 0);
        }

        $map2[] = ['name','in', $dirArr];
        $list = $this->where( wp_where($map2) )
            ->field(true)
            ->order('id desc')
            ->select();
// dump($this->getLastSql());
        foreach ($list as $addon) {
            $addon = $addon->toArray();
            $addon['uninstall'] = 0;
            $addon['is_show_text'] = $addon['is_show'] == 1 ? '是' : '否';
            $addons[parse_name($addon['name'], 0)] = $addon;
        }
        
        foreach ($dirArr as $value) {
            if (! isset($addons[$value])) {

                $class = get_addon_class($value);
                if (! class_exists($class)) { // 实例化应用失败忽略执行
                    continue;
                }

                $obj = new $class();
                $addons[$value] = $obj->info;
                $addons[$value]['name'] = parse_name($addons[$value]['name'], 0);
                if ($addons[$value]) {
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
                }
            }
        }

        int_to_string($addons, array(
            'status' => array(
                - 1 => '损坏',
                0 => '禁用',
                1 => '启用',
                null => '未安装'
            )
        ));
        $addons = list_sort_by($addons, 'uninstall', 'desc');
        return $addons;
    }

    /**
     * 获取应用的后台列表
     */
    public function getAdminList()
    {
        $admin = [];
        
        return $admin;
    }

    function set_show($id, $val)
    {
        $map['id'] = $id;
        return $this->where( wp_where($map) )->setField('is_show', $val);
    }
}
