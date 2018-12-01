<?php

namespace app\common\model;

use app\common\model\Base;

/**
 * 插件配置操作集成
 */
class AddonStatus extends Base
{
    protected $table = DB_PREFIX. 'apps';
    /**
     * 保存配置
     */
    function set($addon, $status)
    {
        $map ['id'] = get_pbid();
        if (empty($map ['id'])) {
            return false;
        }
        $info = get_pbid_appinfo($map ['id']);
        if (! $info) {
            $map ['uid'] = session('mid');
            $addon_status [$addon] = intval($status);
            $map ['addon_status'] = json_encode($addon_status);
            $info ['id'] = M('publics')->insertGetId($map);
        } else {
            $addon_status = json_decode($info ['addon_status'], true);
            $addon_status [$addon] = intval($status);
            M('publics')->where(wp_where($map))->setField('addon_status', json_encode($addon_status));
        }
        D('common/Publics')->clearCache($info ['id']);
        // dump(M( 'publics' )->getLastSql());exit;
        return $info ['id'];
    }
    /**
     * 获取插件配置
     * 获取的优先级：当前用户插件权限》当前公众号设置》后台默认配置》安装文件上的配置
     */
    function getList($is_admin = false)
    {
        // 当前公众号的设置
        $map ['pbid'] = get_pbid();
        if (empty($map ['pbid'])) {
            return [];
        }
        
        $info = get_pbid_appinfo($map ['pbid']);
        
        $wpid_status = [];
        if (! empty($info['addon_status'])) {
            $wpid_status = json_decode($info['addon_status'], true);
        }

        return $wpid_status;
    }
    
    // 获取当前公众号已授权的插件列表
    function getPublicAddons($mp_id)
    {
        $info = D('common/Publics')->getInfo($mp_id);

        $map =isset($map) ? $map :[];
        $data = M('Apps')->where(wp_where($map))->order('id DESC')->select();
        
        return $data;
    }
}
