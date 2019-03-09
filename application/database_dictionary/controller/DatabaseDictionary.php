<?php

namespace app\database_dictionary\controller;

use app\common\controller\WebBase;

// PC运营管理端的控制器
class DatabaseDictionary extends WebBase
{

    function lists()
    {
        $html = input('?md') ? 'md' : 'lists';

        $res['title'] = '网页格式';
        $res['url'] = U('lists');
        $res['class'] = $html == 'lists' ? 'current' : '';
        $nav[] = $res;

        $res['title'] = 'MarkDown格式';
        $res['url'] = U('lists?md=1');
        $res['class'] = $html == 'md' ? 'current' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);

        $tree = [];
        $db_name = config('database.database');
        $sql = "SELECT TABLE_NAME,COLUMN_NAME,IS_NULLABLE,COLUMN_TYPE,COLUMN_DEFAULT,COLUMN_COMMENT FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA='{$db_name}'";
        $lists = M()->query($sql);
        foreach ($lists as $vo) {
            $tables[$vo['TABLE_NAME']][] = $vo;
        }

        $addons = M('apps')->column('title', 'name');
        $addons['core'] = 'WeiPHP基础';

        $models = M('model')->field('name,title,addon')->select();
        $px = config('database.prefix');
        foreach ($models as $vo) {
            $key = $px . $vo['name'];
            $model_titles[$key] = $vo['title'];
            $name = parse_name($vo['addon']);
            $addon_titles[$key] = isset($addons[$name]) ? $addons[$name] : '其它';
        }

        foreach ($tables as $table_name => $vo) {
            $addon = isset($addon_titles[$table_name]) ? $addon_titles[$table_name] : '其它';
            $tree[$addon][$table_name] = $vo;
        }

        $this->assign('tree', $tree);

        $this->assign('tables', $model_titles);
        // dump($tables);
        // exit();

        return $this->fetch($html);
    }
}
