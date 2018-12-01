<?php

// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/* 解析应用数据列表定义规则 */
function get_addonlist_field($data, $grid, $addon)
{
    // 获取当前字段数据
    foreach ($grid['field'] as $field) {
        $array = explode('|', $field);
        $temp = $data[$array[0]];
        // 函数支持
        if (isset($array[1])) {
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]] = $temp;
    }
    if (! empty($grid['format'])) {
        $value = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data2) {
            return $data2[$match[1]];
        }, $grid['format']);
    } else {
        $value = implode(' ', $data2);
    }
    
    // 链接支持
    if (! empty($grid['href'])) {
        $links = explode(',', $grid['href']);
        foreach ($links as $link) {
            $array = explode('|', $link);
            $href = $array[0];
            if (preg_match('/^\[([a-z_]+)\]$/', $href, $matches)) {
                $val[] = $data2[$matches[1]];
            } else {
                $show = isset($array[1]) ? $array[1] : $value;
                // 替换系统特殊字符串
                $href = str_replace(array(
                    '[DELETE]',
                    '[EDIT]',
                    '[ADDON]'
                ), array(
                    'del?ids=[id]&name=[ADDON]',
                    'edit?id=[id]&name=[ADDON]',
                    $addon
                ), $href);
                
                // 替换数据变量
                $href = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data) {
                    return $data[$match[1]];
                }, $href);
                
                $val[] = '<a href="' . U($href) . '">' . $show . '</a>';
            }
        }
        $value = implode(' ', $val);
    }
    return $value;
}

// 获取属性类型信息
function get_attribute_type($type = '')
{
    static $_type = array(
        'num' => array(
            '数字',
            'int(10) NULL'
        ),
        'string' => array(
            '字符串',
            'varchar(255) NULL'
        ),
        'textarea' => array(
            '文本框',
            'text NULL'
        ),
        'date' => array(
            '日期',
            'int(10) NULL'
        ),
        'datetime' => array(
            '时间',
            'int(10) NULL'
        ),
        'bool' => array(
            '布尔',
            'tinyint(2) NULL'
        ),
        'select' => array(
            '枚举',
            'char(50) NULL'
        ),
        'radio' => array(
            '单选',
            'char(10) NULL'
        ),
        'checkbox' => array(
            '多选',
            'varchar(100) NULL'
        ),
        'editor' => array(
            '编辑器',
            'text  NULL'
        ),
        'picture' => array(
            '上传图片',
            'int(10) UNSIGNED NULL'
        ),
        'file' => array(
            '上传附件',
            'int(10) UNSIGNED NULL'
        ),
        'cascade' => array(
            '级联',
            'varchar(255) NULL'
        ),
        'mult_picture' => array(
            '多图上传',
            'varchar(255) NULL'
        ),
        'dynamic_select' => array(
            '动态下拉菜单',
            'varchar(100) NULL'
        ),
        'dynamic_checkbox' => array(
            '动态多选菜单',
            'varchar(100) NULL'
        ),
        'material' => array(
            '素材选择器',
            'varchar(50) NULL'
        ),
        'prize' => array(
            '奖品选择器',
            'varchar(255) NULL'
        ),
        'news' => array(
            '图文素材选择器',
            'int(10) NULL'
        ),
        'image' => array(
            '图片素材选择器',
            'int(10) NULL'
        ),
        'goods' => array(
            '商品选择器',
            'int(11) NULL'
        ),
        
        'user' => array(
            '单用户选择',
            'int(10) NULL'
        ),
        'users' => array(
            '多用户选择',
            'int(10) NULL'
        )
    );
    return $type ? $_type[$type][0] : $_type;
}

/**
 * 获取对应状态的文字信息
 *
 * @param int $status            
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null)
{
    if (! isset($status)) {
        return false;
    }
    switch ($status) {
        case - 1:
            return '已删除';
            break;
        case 0:
            return '禁用';
            break;
        case 1:
            return '正常';
            break;
        case 2:
            return '待审核';
            break;
        default:
            return false;
            break;
    }
}

// 获取数据的状态操作
function show_status_op($status)
{
    switch ($status) {
        case 0:
            return '启用';
            break;
        case 1:
            return '禁用';
            break;
        case 2:
            return '审核';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 获取配置的类型
 *
 * @param string $type
 *            配置类型
 * @return string
 */
function get_config_type($type = 0)
{
    $list = config('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 *
 * @param string $group
 *            配置分组
 * @return string
 */
function get_config_group($group = 0)
{
    $list = config('CONFIG_GROUP_LIST');
    $group = isset($group) ? $group : 0;
    return isset($list[$group]) ? $list[$group] : '';
}

/**
 * 动态扩展左侧菜单,base.html里用到
 *
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu, &$base_menu)
{
    foreach ($extra_menu as $key => $group) {
        if (isset($base_menu['child'][$key])) {
            $base_menu['child'][$key] = array_merge($base_menu['child'][$key], $group);
        } else {
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 *
 * @param int $cid
 *            分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid)
{
    if (empty($cid)) {
        return false;
    }
    $cates = M('Category')->where('status', 1)
        ->field('id,title,pid')
        ->order('sort')
        ->select();
    $child = get_category($cid); // 获取参数分类的信息
    $pid = $child['pid'];
    $temp = [];
    $res[] = $child;
    while (true) {
        foreach ($cates as $key => $cate) {
            if ($cate['id'] == $pid) {
                $pid = $cate['pid'];
                array_unshift($res, $cate); // 将父分类插入到数组第一个元素前
            }
        }
        if ($pid == 0) {
            break;
        }
    }
    return $res;
}

/**
 * 获取当前分类的文档类型
 *
 * @param int $id            
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null)
{
    if (empty($id)) {
        return false;
    }
    $type_list = config('DOCUMENT_MODEL_TYPE');
    $model_type = M('Category')->getFieldById($id, 'type');
    $model_type = explode(',', $model_type);
    foreach ($type_list as $key => $value) {
        if (! in_array($key, $model_type)) {
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 *
 * @param int $id            
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null)
{
    if (empty($cate_id)) {
        return false;
    }
    $cate = M('Category')->where('id=' . $cate_id)->value('title');
    return $cate;
}

// 获取子文档数目
function get_subdocument_count($id = 0)
{
    return M('Document')->where('pid=' . $id)->count();
}

/**
 * 获取行为数据
 *
 * @param string $id
 *            行为id
 * @param string $field
 *            需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null)
{
    if (empty($id) && ! is_numeric($id)) {
        return false;
    }
    $list = S('action_list');
    if (empty($list[$id])) {
        $map = array(
            'status' => array(
                'gt',
                - 1
            ),
            'id' => $id
        );
        $list[$id] = M('Action')->where(wp_where($map))
            ->field(true)
            ->find();
    }
    return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 *
 * @param mixed $value
 *            条件，可用常量或者数组
 * @param string $condition
 *            条件字段
 * @param string $field
 *            需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null)
{
    if (empty($value)) {
        return false;
    }
    
    // 拼接参数
    $map[$condition] = $value;
    $info = M('model')->where(wp_where($map));
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->value($field);
    }
    return $info;
}

/**
 * 获取行为类型
 *
 * @param intger $type
 *            类型
 * @param bool $all
 *            是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false)
{
    $list = array(
        1 => '系统',
        2 => '用户'
    );
    if ($all) {
        return $list;
    }
    return $list[$type];
}
