<?php

namespace app\shop\controller;

use app\shop\controller\Base;

class Spec extends Base
{
    public $model;
    public function initialize()
    {
        $this->model = $this->getModel('shop_spec');
        parent::initialize();

        $res['title'] = '商品规格管理列表';
        $res['url']   = U ( 'Shop/Spec/lists', $this->get_param);
        $res['class'] = ACTION_NAME == 'lists' ? 'current' : '';
        $nav[]        = $res;

        $this->assign('nav', $nav);
    }
    // 通用插件的列表模型
    public function lists()
    {
        $map['uid'] = $this->mid;
        session('common_condition', $map);
        $list_data = $this->_get_model_list($this->model);
        // 属性值
        $ids = getSubByKey($list_data['list_data'], 'id');
        if (!empty($ids)) {
            $map1['spec_id'] = array(
                'in',
                $ids,
            );

            $list = M('shop_spec_option')->where(wp_where($map1))->order('sort desc, id asc')->select();
            foreach ($list as $vo) {
                $option[$vo['spec_id']][] = $vo['name'];
            }
            if (!empty($option)) {
                foreach ($list_data['list_data'] as &$vv) {
                    $vv['remark'] = implode(', ', $option[$vv['id']]);
                }
            }
        }
        $this->assign($list_data);

        return $this->fetch('base/lists');
    }
    // 通用插件的编辑模型
    public function edit()
    {
        $res['title'] = '编辑商品';
        $res['url']   = '#';
        $res['class'] = 'current';
        $nav[]        = $res;
        $this->assign('nav', $nav);
        $model   = $this->model;
        $id      = I('id');
        $wpid = WPID;
        if (IS_POST) {
            $data = input('post.');
            if (empty($data['title'])) {
                $this->error('规格名称不能为空');
            }
            if (empty($data['name'])) {
                $this->error('规格属性不能为空');
            }
            $this->set_option($id, $data);

            $Model = D($model['name']);
            // 获取模型的字段信息
            unset($data['name'], $data['sort']);
            $data = $this->checkData($data, $model);
            $res  = $Model->isUpdate(true)->allowField(true)->save($data);
            $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'] , $this->get_param));
        } else {
            $fields = get_model_attribute ( $model );

            // 获取数据
            $data = M($model['name'])->where('id', $id)->find();
            $data || $this->error('数据不存在！');

            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }

            $option_list = M('shop_spec_option')->where('spec_id', $id)->order('sort asc')->select();
            $this->assign('option_list', $option_list);

            $this->assign('fields', $fields);
            $this->assign('data', $data);

            return $this->fetch();
        }
    }

    // 通用插件的增加模型
    public function add()
    {

        $res['title'] = '增加规格';
        $res['url']   = U ( 'Shop/Spec/add', $this->get_param);
        $res['class'] = ACTION_NAME == 'add' ? 'current' : '';
        $nav[]        = $res;
        $this->assign('nav', $nav);

        $model = $this->model;
        $Model = M(parse_name($model['name'], 1));
        if (IS_POST) {
            // 获取模型的字段信息
            $saveData = $data = input('post.');
            if (empty($data['title'])) {
                $this->error('规格名称不能为空');
            }
            if (empty($data['name'])) {
                $this->error('规格属性不能为空');
            }
            unset($data['name'], $data['sort']);
            $data = $this->checkData($data, $this->model);
            
            $id = $Model->removeOption('data')->insertGetId($data);
            if ($id) {
                $this->set_option($id, $saveData);

                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute ( $model );
            $this->assign('fields', $fields);
            $this->assign('post_url', U('add'));

            return $this->fetch('edit');
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        $id  = I('id');
        $ids = I('ids');
        if (!empty($id)) {
            $key = 'Goods_getInfo_' . $id;
            S($key, null);
        } else {
            foreach ($ids as $i) {
                $key = 'Goods_getInfo_' . $i;
                S($key, null);
            }
        }
        return parent::common_del($this->model);
    }
    public function set_option($spec_id, $post)
    {
        $opt_data['spec_id'] = $spec_id;
        foreach ($post['name'] as $key => $opt) {
            if (empty($opt)) {
                continue;
            }

            $opt_data['name'] = $opt;
            $opt_data['sort'] = intval($post['sort'][$key]);
            if ($key > 0) {
                // 更新选项
                $optIds[] = $map['id'] = $key;
                M('shop_spec_option')->where(wp_where($map))->update($opt_data);
            } else {
                // 增加新选项
                $optIds[] = M('shop_spec_option')->insertGetId($opt_data);
            }
        }
        if (!empty($optIds)) {
            // 删除旧选项
            $map2['id'] = array(
                'not in',
                $optIds,
            );
            $map2['spec_id'] = $opt_data['spec_id'];
            M('shop_spec_option')->where(wp_where($map2))->delete();
        }

    }
    public function set_show()
    {
        $save['is_show'] = 1 - I('is_show');
        $map['id']       = I('id');

            $map['wpid'] = WPID;
            $res            = M('shop_goods')->where(wp_where($map))->update($save);

        $this->success('操作成功');
    }

    // 添加虚拟信息
    public function _addVirtualInfo($goods_id, $textareaStr)
    {
        if (!empty($textareaStr)) {

            $arr   = wp_explode($textareaStr);
            foreach ($arr as $v) {
                $accountArr       = explode('|', $v);
                $map['goods_id']  = $goods_id;
                $data['account']  = $map['account']  = $accountArr[0];
                $data['password'] = $accountArr[1];
                $data['is_use']   = 0;
                $data['goods_id'] = $goods_id;
                $res              = M('shop_virtual')->where(wp_where($map))->select();
                if ($res) {
                    M('shop_virtual')->where(wp_where($map))->update($data);
                } else {
                    M('shop_virtual')->insert($data);
                }
            }
        }
    }
}
