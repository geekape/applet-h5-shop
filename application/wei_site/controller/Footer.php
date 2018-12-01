<?php

namespace app\wei_site\controller;

use app\wei_site\controller\Base;

class Footer extends Base
{
    public $model;
    public function initialize()
    {
        $this->model = $this->getModel('weisite_footer');
        parent::initialize();
    }
    public function lists()
    {
        $has_data = 1;
        $file     = env('app_path') . MODULE_NAME . '/view/template_footer/' . $this->config['template_footer'] . '/info.php';
        if (file_exists($file)) {
            $info = require_once $file;
            if (isset($info['has_data']) && $info['has_data'] == 0) {
                $has_data = 0;
            }
        }
        $this->assign('has_data', $has_data);

        // 解析列表规则
        $list_data = $this->_list_grid($this->model);
        $fields    = $list_data['fields'];

        // 搜索条件
        $map                    = $this->_search_map($this->model, $list_data['db_fields']);
        $list_data['list_data'] = $this->get_data($map);

        $this->assign($list_data);

        // 使用提示
        $normal_tips = '一级主菜单最多4个，菜单风格1-8子菜单最多6个，菜单风格9-16子菜单最多10个。<br/>
				一键拨号填写范例：tel:136xxxx1570请拷贝代码粘帖到输入框，修改电话';
        $this->assign('normal_tips', $normal_tips);

        return $this->fetch();
    }
    public function get_data($map)
    {
        $dataTable = D('common/Models')->getFileInfo($this->model);
        $list      = D('WeiSite/Footer')->get_list($map);
        $list      = $this->parseListData($list, $dataTable);

        // 取一级菜单
		$one_arr = $data = [];
        foreach ($list as $k => $vo) {
            if ($vo['pid'] != 0) {
                continue;
            }

            $one_arr[$vo['id']] = $vo;
            unset($list[$k]);
        }

        foreach ($one_arr as $p) {
            $data[] = $p;

            $two_arr = [];
            foreach ($list as $key => $l) {
                if ($l['pid'] != $p['id']) {
                    continue;
                }

                $l['title'] = '├──' . $l['title'];
                $two_arr[]  = $l;
                unset($list[$key]);
            }

            $data = array_merge($data, $two_arr);
        }

        return $data;
    }
    public function edit()
    {
        $Model = D ($this->model ['name']);
        $id    = I('id');

        if (IS_POST) {
            $data = I('post.');
            $data = $this->checkData($data, $this->model);
            $res  = $Model->save($data, ['id' => $id]);
            if ($res!==false) {
                $this->success('保存' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 获取一级菜单
            $map['wpid'] = get_wpid();
            $map['pid']   = 0;
            $map['id']    = array(
                'not in',
                $id,
            );
            $list = $Model->where(wp_where($map))->select();
			$extra = '';
            foreach ($list as $v) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }

            $fields = get_model_attribute ( $this->model );
            if (!empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        $vo['extra'] .= "\r\n" . $extra;
                    }
                }
            }

            // 获取数据
            $data = M($this->model['name'])->where('id', $id)->find();
            $data || $this->error('数据不存在！');

            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }

            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $this->model['title'];

            return $this->fetch();
        }
    }
    public function add()
    {
        $Model = D ($this->model ['name']);

        if (IS_POST) {
            $data = I('post.');
            $data = $this->checkData($data, $this->model);
            $id = $Model->insertGetId($data);
            if ($id) {
                $this->success('添加' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 获取一级菜单
            $map['pid']   = 0;
            $map['wpid'] = get_wpid();
            $list         = $Model->where(wp_where($map))->select();
			$extra = '';
            foreach ($list as $v) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }

            $fields = get_model_attribute ( $this->model );
            if (!empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        $vo['extra'] .= "\r\n" . $extra;
                    }
                }
            }

            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $this->model['title'];

            return $this->fetch();
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        return parent::common_del($this->model);
    }
    // 底部导航
    public function template()
    {
        return $this->fetch ();
    }
}
