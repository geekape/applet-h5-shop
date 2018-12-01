<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\model;

use app\common\model\Base;

/**
 * 插件模型
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Addons extends Base
{

    protected $table = DB_PREFIX . 'apps';

    /**
     * 获取应用插件列表
     *
     * @param string $addon_dir
     */
    public function getWeixinList($isAll = false, $wpid_status = [], $is_admin = false, $is_show = false)
    {
        $list = $this->getList();

        if ($is_show) {
            foreach ($list as $n => $vo) {
                if ($vo['is_show'] != 1) {
                    unset($list[$n]);
                }

            }
        }

        $isAll || $wpid_status = D('common/AddonStatus')->getList($is_admin);
        foreach ($list as $addon) {
            if (!$isAll && isset($wpid_status[$addon['name']]) && $wpid_status[$addon['name']] < 1) {
                continue;
            }
            if ($addon['has_adminlist']) {
                $addon['addons_url'] = U($addon['name'] . '/' . $addon['name'] . '/lists');
            } elseif (file_exists(env('app_path') . $addon['name'] . '/config.php')) {
                $addon['addons_url'] = U($addon['name'] . '/' . $addon['name'] . '/config');
            } else {
                $addon['addons_url'] = U($addon['name'] . '/' . $addon['name'] . '/nulldeal');
            }

            $addons[$addon['name']] = $addon;
        }

        return $addons;
    }

    public function getList($update = false)
    {
		$map['status'] = 1;
        $key = cache_key($map, $this->table);
        $list = S($key);
        if ($list === false || $update || true) {            
            $list_res      = $this->where(wp_where($map))->select();
            $list          = [];
            foreach ($list_res as $vo) {
                $vo          = $vo->toArray();
                $name        = strtolower($vo['name']);
                $list[$name] = $vo;
            }
            S($key, $list);
        }

        return $list;
    }

    public function getInfoByName($name, $update = false)
    {
        $list = $this->getList($update);
        return isset($list[$name]) ? $list[$name] : '';
    }
}
