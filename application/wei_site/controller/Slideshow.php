<?php

namespace app\wei_site\controller;

use app\wei_site\controller\Base;

class Slideshow extends Base
{
    public $model;
    public function initialize()
    {
        $this->model = $this->getModel('weisite_slideshow');
        parent::initialize();
    }
    // 通用插件的列表模型
    public function lists()
    {
        $has_slide = 1;
        $file      = env('app_path') . MODULE_NAME . '/view/template_index/' . $this->config['template_index'] . '/info.php';
        if (file_exists($file)) {
            $info = require_once $file;
            if (isset($info['has_slide']) && $info['has_slide'] == 0) {
                $has_slide = 0;
            }
        } else if (file_exists(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_index'] . '.html')) {
            $pigcms_temps = require_once env('app_path') . MODULE_NAME . '/view/pigcms/index.Tpl.php';
            foreach ($pigcms_temps as $pig) {
                if ($pig['tpltypename'] == $this->config['template_index']) {
                    $has_slide = $pig['has_slide'];
                }
            }
        }

        $this->assign('has_slide', $has_slide);

        $map['wpid'] = get_wpid();
        session('common_condition', $map);

        $list_data = $this->_get_model_list($this->model);
        foreach ($list_data['list_data'] as &$vo) {
//             $vo['img'] = '<img src="' . get_cover_url($vo['img']) . '" width="50px" >';
        }
        $this->assign($list_data);

        return $this->fetch();
    }
    // 通用插件的编辑模型
    public function edit()
    {
        $model                     = $this->model;
        is_array($model) || $model = $this->getModel($model);
        $id                 = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        $wpid = get_wpid();
        if (isset($data['wpid']) && $wpid != $data['wpid']) {
            $this->error('非法访问！');
        }

        if (IS_POST) {
            $Model = D($model['name']);
            $data  = I('post.');
            $data  = $this->checkData($data, $model);
            $res   = $Model->save($data, ['id' => $id]);
            if ($res!==false) {
                $this->_saveKeyword($model, $id);

                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');

                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {

            $map['wpid'] = get_wpid();

            $list = M('weisite_category')->where(wp_where($map))->select();
            //dump($list);
			$extra = '';
            foreach ($list as $v) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }

            $fields = get_model_attribute ( $model );
            if (!empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        $vo['extra'] .= "\r\n" . $extra;
                    }
                }
            }
            //dump($extra);
            //dump($fields);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            return $this->fetch();
        }
    }

    // 通用插件的增加模型
    public function add()
    {
        D ($this->model ['name']);
        return parent::common_add($this->model);
    }

    // 通用插件的删除模型
    public function del()
    {
        return parent::common_del($this->model);
    }
    // 首页
    public function index()
    {
        return $this->fetch();
    }
    // 分类列表
    public function category()
    {
        return $this->fetch();
    }
    // 相册模式
    public function picList()
    {
        return $this->fetch();
    }
    // 详情
    public function detail()
    {
        return $this->fetch ();
    }
}
