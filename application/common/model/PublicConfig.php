<?php

namespace app\common\model;

use app\common\model\Base;

/**
 * 公众号配置操作集成
 */
class PublicConfig extends Base
{

    protected $table = DB_PREFIX . 'public_config';

    /**
     * 获取插件配置
     * 获取的优先级：当前公众号设置》安装文件上的配置
     */
    public function getConfig($addon, $pkey = '', $pbid = '', $update = false)
    {
        $addon = parse_name($addon);
        empty($pkey) && $pkey = $addon . '_' . $addon;
        $pbid == '' && $pbid = get_pbid();
		
		$map = ['pkey' => $pkey, 'pbid' => $pbid];

        $key = cache_key($map, $this->table);
        $db_config = S($key);
        if ($db_config === false || $update ) {            
            $obj = $this->where(wp_where($map))->find();
            if (!empty($obj)) {
                $db_config = json_decode($obj['pvalue'], true);
            } else {
                $db_config = [];
            }

            S($key, $db_config);
        }

        // 安装文件上的配置
        $file_config = [];
        $file = env('app_path') . parse_name($addon) . '/config.php';
        if (file_exists($file)) {
            $configs = include $file;
            $configs = isset($configs[$pkey]) ? $configs[$pkey] : $configs;
            if ($configs) {
                foreach ($configs as $k => $vo) {
                    $file_config[$k] = isset($vo['value']) ? $vo['value'] : '';
                }
            }
        }

        return array_merge($file_config, $db_config);
    }

    /**
     * 保存配置
     */
    public function setConfig($pkey, $config, $pbid = '')
    {
        $pkey = parse_name($pkey);
        $map['pbid'] = empty($pbid) ? get_pbid() : $pbid;
        $map['pkey'] = $pkey;
        if (empty($map['pbid'])) {
            return false;
        }

        $info = $this->where(wp_where($map))->find();

        if (!$info) {
            $map['pvalue'] = json_encode($config);
            $map['mtime'] = NOW_TIME;

            $res = $this->insertGetId($map);
        } else {
            $addon_config = [];

            if (!empty($info['pvalue'])) {
                $addon_config = (array)json_decode($info['pvalue'], true);
            }

            if (is_array($config)) {
                $addon_config = array_merge($addon_config, $config);
            }

            $res = $this->save(['pvalue' => json_encode($addon_config), 'mtime' => NOW_TIME], $map);
        }

        $this->clearCache($pkey, $map['pbid']);

        return $res;
    }

    /*
     * 清空缓存
     */
    public function clearCache($pkey, $pbid = '', $uid = 0, $more_param = [])
    {
    	$map = ['pkey' => $pkey, 'pbid' => $pbid];
    	$key = cache_key($map, $this->table);
//         $key = 'public_config_' . $pbid . '_' . $pkey;
        S($key, null);
    }
}
