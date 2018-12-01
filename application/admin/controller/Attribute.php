<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 属性控制器
 *
 * @author huajie <banhuajie@163.com>
 */
class Attribute extends Admin
{

    /**
     * 属性列表
     *
     * @author huajie <banhuajie@163.com>
     */
    public function index()
    {
        $model_id = I('model_id');

        $obj = D('common/Models')->getFileInfo($model_id);
        $list = $obj->fields;

        $this->assign('_list', $list);
        $this->assign('model_id', $model_id);
        $this->assign('model_title', $obj->config['title']);
        
        return $this->fetch();
    }

    /**
     * 新增页面初始化
     *
     * @author huajie <banhuajie@163.com>
     */
    public function add()
    {
        $model_id = I('model_id');
        $model = M('model')->field('id,title,name')->find($model_id);

        $this->assign('model', $model);
        
        $this->assign('info', array(
            'model_id' => $model_id
        ));
        $this->meta_title = '新增属性';
        return $this->fetch('edit');
    }

    /**
     * 编辑页面初始化
     *
     * @author huajie <banhuajie@163.com>
     */
    public function edit()
    {
        $dao = D('common/Models');
        $name = I('name', '');
        $model_id = I('model_id', '');
        if (empty($name)) {
            $this->error('140065:参数不能为空！');
        }
        
        /* 获取一条记录的详细数据 */
        $obj = $dao->getFileInfo($model_id);
        $list = $obj->fields;
        
        // 虚拟一个ID值，用于编辑
        $i = 1;
        foreach ($list as &$vo) {
            $vo['id'] = $i;
            $vo['model_id'] = $model_id;
            $i ++;
        }
        
        if (! isset($list[$name])) {
            $this->error('140066:参数不能正确！');
        }
        $list[$name]['name'] = $name;
        
        $model = $dao->field('id,title,name')->find($model_id);
        // dump ( $model );
        $this->assign('model', $model);
        $this->assign('info', $list[$name]);
        $this->meta_title = '编辑属性';
        return $this->fetch();
    }

    function save_sort()
    {
        $dao = D('common/Models');
        $data = I('post.');
        
        $model_id = $data['model_id'];
        $obj = $dao->getFileInfo($model_id);
        $list = $obj->fields;
        
        $newList = [];
        foreach ($data['sort'] as $name) {
            $newList[$name] = $list[$name];
        }
        
        // dump ( $data );
        $model = $dao->field(true)->find($model_id);
        // dump ( $newList );
        // exit ();
        $dao->buildFile($model, $newList);
        $this->success('保存成功');
    }

    /**
     * 更新一条数据
     *
     * @author huajie <banhuajie@163.com>
     */
    public function update()
    {
        $dao = D('common/Models');
        /* 获取数据对象 */
        $data = I('post.');
        $model_id = $data['model_id'];
        $obj = $dao->getFileInfo($model_id);
        
        if (! is_writable($obj->datatable_path)) {
            $this->error('140071:' . $obj->datatable_path . '文件没有可写权限！');
        }
        $list = $obj->fields;
        
        if ($data['is_must'] == 1 && strpos($data['field'], 'NOT NULL') === false) {
            $data['field'] = str_replace('NULL', 'NOT NULL', $data['field']);
        } elseif ($data['is_must'] == 0 && strpos($data['field'], 'NOT NULL') !== false) {
            $data['field'] = str_replace('NOT NULL', 'NULL', $data['field']);
        }
        
        if (empty($data['id'])) { // 新增属性
            $res = $dao->addField($data);
            if (! $res) {
                $this->error('140067:新建字段出错！');
            }
            
            $name = $data['name'];
            unset($data['id'], $data['name'], $data['model_id']);
            $list[$name] = $data;
            $newList = $list;
        } else { // 更新数据
            $i = 1;
            $old = $listArr = [];
            $id = $data['id'];
            foreach ($list as $name => $vo) {
                if ($id == $i) {
                    $old = $vo;
                    $old['name'] = $name;
                }
                
                $vo['name'] = $name;
                $listArr[$i] = $vo;
                $i ++;
            }
            // dump ( $listArr );
            $res = $dao->updateField($data, $old);
            if (! $res) {
                $this->error('140068:更新字段出错！');
            }
            
            // 更新文件里的字段，并保持排序位置不变
            unset($list[$old['name']]);
            unset($data['id'], $data['model_id']);
            $listArr[$id] = $data;
            foreach ($listArr as $v) {
                $n = $v['name'];
                unset($v['name']);
                $newList[$n] = $v;
            }
        }
        // 删除字段缓存文件
        // dump ( $data );
        $model = $dao->field(true)->find($model_id);
        $cache_name = config('database.DB_NAME') . '.' . preg_replace('/\W+|\_+/', '', $model['name']);
        S($cache_name, null, DATA_PATH . '_fields/');
        // dump ( $newList );
        // exit ();
        $dao->buildFile($model, $newList);
        
        // 记录行为
        action_log('update_attribute', 'attribute', '', UID);
        
        $this->success('保存成功', U('index', [
            'model_id' => $model_id
        ]));
    }

    /**
     * 删除一条数据
     *
     * @author huajie <banhuajie@163.com>
     */
    public function remove()
    {
        $dao = D('common/Models');
        $model_id = I('model_id');
        $name = I('name');
        $obj = $dao->getFileInfo($model_id);
        $list = $obj->fields;
        
        isset($list[$name]) || $this->error('140069:该字段不存在！');
        
        $info = $list[$name];
        $info['name'] = $name;
        
        // 更新数据模型文件
        unset($list[$name]);
        $dao->buildFile($model_id, $list);
        
        // 删除表字段
        $res = $dao->deleteField($info, $model_id);
        if (! $res) {
            $this->error('140070:删除失败');
        } else {
            // 记录行为
            action_log('delete_attribute', $obj->config['name'] . ':' . $name, '', UID);
            $this->success('删除成功', U('index', 'model_id=' . $model_id));
        }
    }
}
