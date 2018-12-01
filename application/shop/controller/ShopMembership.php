<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class ShopMembership extends Base{

    var $model;

    function initialize()
    {
        $this->model = $this->getModel('shop_membership');
        parent::initialize();
    }
    // 通用插件的列表模型
    public function lists()
    {
        $map['wpid'] = get_wpid();
        $map['uid'] = $this->mid;
        session('common_condition', $map);
        $list_data = $this->_get_model_list($this->model);
        $this->assign($list_data);
        $templateFile = $this->model['template_list'] ? $this->model['template_list'] : '';
        return $this->fetch($templateFile);
    }
    // 通用插件的编辑模型
    public function edit()
    {
        $id = I('id');
        // 获取数据
        $data = M( $this->model ['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        if (IS_POST) {
            $this->_checkdata(input('post.'));
            $Model = D ($this->model ['name']);
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkData($data, $this->model);
            $res  = $Model->isUpdate(true)->save($data);
            if ($res!==false) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');

                $this->success('保存' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute ( $this->model );
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            return $this->fetch();
        }
    }
    
    // 通用插件的增加模型
    public function add()
    {
        if (IS_POST) {
            $this->_checkdata(input('post.'));
            $Model = D ($this->model ['name']);
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkData($data, $this->model);

            $id = $Model->insertGetId($data);
            if ($id) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                
                $this->success('添加' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute ( $this->model );
            $this->assign('fields', $fields);
            return $this->fetch();
        }
    }
    
    // 通用插件的删除模型
    public function del()
    {
        parent::common_del($this->model);
    }

    function _checkdata($data)
    {
        if ($data['condition'] < 0) {
            $this->error('会员升级条件不能小于0');
        }
    }
}
