<?php

// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 模型数据管理控制器
 *
 * @author 凡星
 */
class Menu extends Admin
{

    /**
     * 显示指定模型列表数据
     *
     * @param String $model
     *          模型标识
     * @author 凡星
     */
    public function lists()
    {
        $model = $this->getModel('menu');
        $place = input('place/d', 0);
        session('admin_place', $place);

        $map ['place'] = $place;
        session('common_condition', $map);

        $model ['list_row'] = 10000;
        $list_data = $this->_get_model_list($model);

        $list_data ['list_data'] = $this->_get_data($map, $list_data ['list_data']);
        $list_data ['_page'] = '';

        $this->assign($list_data);

        $nav_title = $place == 1 ? '开发者' : '运营者';
        $this->assign('nav_title', $nav_title);

        return $this->fetch('lists');
    }

    public function admin_lists()
    {
        return $this->lists();
    }

    public function _get_data($map, $list_data)
    {
        $hask = [];
        foreach ($list_data as $vo) {
            $hask [$vo ['id']] = $vo ['urls'];
        }

        $list = M('menu')->field(true)->where(wp_where($map))->order('sort asc,id asc')->select();
        $model = $this->getModel('Menu');
        $list = $this->parseListData($list, $model);
        // lastsql ();
        // 取一级菜单
        $one_arr = $data = [];
        $place = I('place', '');
        foreach ($list as $k => $vo) {
            if ($vo ['pid'] != 0) {
                continue;
            }

            $url = U('add', [
                'place' => $place,
                'pid' => $vo ['id']
            ]);
            $hh = isset($hask [$vo ['id']]) ? $hask [$vo ['id']] : '';
            $vo ['urls'] = '<a target="_self" href="' . $url . '">增加子菜单</a>&nbsp;&nbsp;&nbsp;' . $hh;
            $one_arr [$vo ['id']] = $vo;
            unset($list [$k]);
        }

        foreach ($one_arr as $p) {
            $data [] = $p;

            $two_arr = array();
            foreach ($list as $key => $l) {
                if ($l ['pid'] != $p ['id']) {
                    continue;
                }

                $l ['title'] = '├──' . $l ['title'];
                $l ['urls'] = isset($hask [$l ['id']]) ? $hask [$l ['id']] : '';
                $two_arr [] = $l;
                unset($list [$key]);
            }

            $data = array_merge($data, $two_arr);
        }

        return $data;
    }

    public function edit()
    {
        $id = I('id');
        $model = $this->getModel('menu');

        // 获取数据
        $data = M('menu')->find($id);
        $data || $this->error('140151:数据不存在！');

        $place = session('admin_place');

        if (request()->isPost()) {
            $Model = D($model ['name']);
            $data = input('post.');
            $data = $this->_check_data($data);
            // 获取模型的字段信息
            $data = $this->checkData($data, $model);
            if (isset($data ['pid0'])) {
                $data ['pid'] = $data ['pid0'];
                unset($data ['pid0']);
            }
            $res = $Model->isUpdate(true)->save($data);
            if ($res !== false) {
                // 清空缓存
                D('common/Menu')->clearCache(0);

                $url = $place == 1 ? U('admin_lists?place=' . $place) : U('lists?place=' . $place);
                $this->success('保存' . $model ['title'] . '成功！', $url);
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->getFiedls($model ['id']);
            $this->assign('data', $data);

            $nav_title = $place == 1 ? '开发者' : '运营者';
            $this->assign('nav_title', $nav_title);

            return $this->fetch();
        }
    }

    public function add()
    {
        $model = $this->getModel('menu');
        $place = session('admin_place');

        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->_check_data($data);
            $data['place'] = $place;
            $Model = D($model ['name']);
            // 获取模型的字段信息
            $data = $this->checkData($data, $model);

            $id = $Model->isUpdate(false)->removeOption('data')->strict(false)->insertGetId($data);
            if ($id) {
                D('common/Menu')->clearCache(0);
                $url = $data['place'] == 1 ? U('admin_lists?place=' . $place) : U('lists?place=' . $place);
                $this->success('添加成功！', $url);
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->getFiedls($model ['id']);

            $post_url = U('add');
            $this->assign('post_url', $post_url);

            $nav_title = $place == 1 ? '开发者' : '运营者';
            $this->assign('nav_title', $nav_title);

            return $this->fetch();
        }
    }

    private function getFiedls($model_id)
    {
        $fields = get_model_attribute($model_id);
        $place = session('admin_place');
        $fields ['pid'] ['extra'] = str_replace('[place]', $place, $fields ['pid'] ['extra']);

        $this->assign('fields', $fields);
    }

    public function del()
    {
        D('common/Menu')->clearCache(0);

        $ids = I('ids');
        $model = $this->getModel('menu');
        return parent::common_del($model, $ids);
    }

    private function _check_data($data)
    {
        if ($data ['url_type'] == 0) {
            $data ['url'] = '';
        } else {
            $data ['addon_name'] = '';
        }
        if($data['menu_type']==0){
            $data['pid'] = 0;
        }
        return $data;
    }
}
