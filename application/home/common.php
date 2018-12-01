<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 获取列表总行数
 *
 * @param string $category
 *            分类ID
 * @param integer $status
 *            数据状态
 */
function get_list_count($category, $status = 1)
{
    static $count;
    if (! isset($count[$category])) {
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 *
 * @param string $id
 *            文档ID
 * @return integer 段落总数
 */
function get_part_count($id)
{
    static $count;
    if (! isset($count[$id])) {
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 *
 * @param string $url
 *            导航URL
 * @return string 解析或的url
 */
function get_nav_url($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case 'https://' === substr($url, 0, 8):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}
// 运营统计
function tongji($addon)
{
    return false;
    if (empty($addon) || $addon == 'Tongji')
        return false;
    
    $data['wpid'] = get_wpid();
    $data['day'] = date('Ymd');
    $info = M( 'tongji' )->where( wp_where($data) )->find();
    
    if ($info) {
        $content = unserialize($info['content']);
        $content[$addon] += 1;
        
        $save['content'] = serialize($content);
        M( 'tongji' )->where( wp_where($data) )->update($save);
    } else {
        $content[$addon] = 1;
        $data['content'] = serialize($content);
        $data['month'] = date('Ym');
        M( 'tongji' )->insert($data);
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
