<?php

namespace app\common\model;

use app\common\model\Base;

/**
 * 插件配置操作集成
 */
class Menu extends Base
{

    protected $table = DB_PREFIX . 'menu';

    // 取后台管理对当前用户配置的菜单
    private function _get_menu($addonList)
    {
        $mod = strtolower(MODULE_NAME);
        if ($mod == 'admin') {
            $menu_map['place'] = 1;
        } else {
            $menu_map['place'] = 0;
        }
        $menu_map['is_hide'] = 0;

        $menus = $this->where(wp_where($menu_map))
            ->order('sort asc, id asc')
            ->select();

        // 组装数据
        foreach ($menus as $k => &$m) {
            if ($m['url_type'] == 0) {
                $m['url'] = isset($addonList[$m['addon_name']]['addons_url']) ? $addonList[$m['addon_name']]['addons_url'] : '###';
            } elseif (strpos($m['url'], 'http://') !== false || strpos($m['url'], 'https://') !== false) {
                $m['url'] = $m['url'];
            } else {
                $m['url'] = U($m['url']);
            }
        }
        // 侧边栏数据
        foreach ($menus as $k => $m) {
            if ($m['menu_type'] == 0) {
                continue;
            }
            $param['side'] = $cate['id'] = $m['id'];
            $cate['title'] = $m['title'];
            $param['top'] = $cate['pid'] = intval($m['pid']);

            $cate['url'] = $m['url'];
            $cate['url'] .= '?&mdm=' . $cate['pid'] . '_' . $cate['id'];
            $cate['addon_name'] = $m['addon_name'];
            $cate['target'] = $m['target'];

            $res['core_side_menu'][$cate['pid']][] = $cate;
            $res['default_data'][$cate['url']] = $param;
            empty($m['addon_name']) || $res['default_data'][$cate['addon_name']] = $param;
        }
        // 顶部栏数据
        foreach ($menus as $k => $m) {
            if ($m['menu_type'] != 0) {
                continue;
            }
            // dump($m);
            $param['top'] = $cate['id'] = $m['id'];
            $cate['title'] = $m['title'];
            $cate['pid'] = 0;

            $cate['url'] = $m['url'];

            if ($m['url_type'] == 0) {
                $cate['url'] = $m['url'];

                if (empty($cate['url']) && !empty($res['core_side_menu'][$m['id']])) {
                    $cate['url'] = $res['core_side_menu'][$m['id']][0]['url'];
                }
                $cate['url'] .= '?&mdm=' . $cate['id'];
            } else {
                $cate['url'] = $m['url'];
                if (isset($res['core_side_menu'][$m['id']][0]['id'])) {
                    $cate['url'] .= '?&mdm=' . $m['id'] . '_' . $res['core_side_menu'][$m['id']][0]['id'];
                } else {
                    $cate['url'] .= '?&mdm=' . $m['id'];
                }
            }

            $cate['addon_name'] = $m['addon_name'];
            $cate['target'] = $m['target'];

            $res['core_top_menu'][] = $cate;

            $param['side'] = isset($res['core_side_menu'][$m['id']][0]['id']) ? $res['core_side_menu'][$m['id']][0]['id'] : '';
            $res['default_data'][$cate['url']] = $param;
            empty($m['addon_name']) || $res['default_data'][$cate['addon_name']] = $param;
        }

        return $res;
    }

    public function getMenu()
    {
        $key = cache_key('is_hide:0', $this->table);
        $menus = S($key);
        if ($menus === false || true) {
            // 第一步：获取所有微信插件的入口URL
            $addonList = D('home/Addons')->getWeixinList(false);
            // dump($addonList);
            // 第二步：获取导航数据
            $menus = $this->_get_menu($addonList);
            // dump($menus);exit;
            // 第三步：获取用户登录进入时的初始化URL
            $menus['init_url'] = '';
            foreach ($menus['core_top_menu'] as $t) {
                $menus['init_url'] = $t['url'];
                break;
            }
            // S($key, $menus, 86400);
        }

        // 第四步：初始化导航高亮参数
        $default = session('menu_default');
        $isAjax = input('isAjax');
        if (IS_GET && empty($isAjax) && (!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'uploadify.swf') === false))) {
            if (isset($_GET['mdm'])) {
                $mdm = explode('_', input('mdm'));
                $default['top'] = intval($mdm[0]);
                $default['side'] = isset($mdm[1]) ? intval($mdm[1]) : '';
            } else {
                $current_url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
                foreach ($menus['default_data'] as $k => $v) {
                    if (stripos($k, $current_url) !== false) {
                        $default = $v;
                    }
                }
            }
            if (empty($default['top']) && !empty($menus['core_top_menu'])) {
                $default['top'] = intval($menus['core_top_menu'][0]['id']);
            }
            if (empty($default['side']) && !empty($menus['core_side_menu'][$default['top']])) {
                $default['side'] = $menus['core_side_menu'][$default['top']][0]['id'];
            }
            $default['top'] = intval($default['top']);

            session('menu_default', $default);
        }

        // 第五步：设置导航高亮
        $menus['now_top_menu_name'] = '';
        foreach ($menus['core_top_menu'] as &$top) {
            $top['class'] = '';

            if ($top['id'] == $default['top']) {
                $top['class'] = 'active';
                $menus['now_top_menu_name'] = $top['title'];
            }
        }
        foreach ($menus['core_side_menu'] as &$side) {
            foreach ($side as &$s) {
                $s['class'] = '';
                if (isset($default['side']) && $s['id'] == $default['side']) {
                    $s['class'] = 'active';
                }
            }
        }
        $index_2 = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/*');
        if (isset($menus['core_side_menu'][$default['top']]) && !empty($menus['core_side_menu'][$default['top']])) {
            $menus['core_side_menu'] = $menus['core_side_menu'][$default['top']];
        } else {
            $menus['core_side_menu'] = '';
        }
        // dump($menus);
        return $menus;
    }

    public function updateMenuData($data, $map)
    {
        return $this->save($data, $map);
    }

    public function addData($data)
    {
        return $this->save($data);
    }

    public function delData($map)
    {
        return $this->where(wp_where($map))->delete();
    }

    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
        $key = 'menu_lists';
        S($key, null);
    }
}
