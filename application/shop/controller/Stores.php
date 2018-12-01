<?php
namespace app\shop\controller;

use app\common\controller\WebBase;

class Stores extends WebBase
{

    var $model;

    function initialize()
    {
        parent::initialize();
        
        $this->assign('nav', []);
        
        $this->model = $this->getModel('stores');
    }

    function lists()
    {
        $normal_tips = '门店导航显示链接：<a id="copyLink" data-clipboard-text="' . WAP_URL.'?pbid='.get_pbid().'#'.'/shop_lists/'.get_pbid() . '">复制链接</a>
<script type="application/javascript">$.WeiPHP.initCopyBtn("copyLink");</script>';
        $this->assign('normal_tips', $normal_tips);
        $isAjax = I('isAjax/d', 0);
        
        $search = input('name');

            $top_more_button[] = array(
                'title' => '导入数据',
                'url' => U('import')
            );
        
        $top_more_button[] = array(
            'title' => '导出数据',
            'url' => U('output')
        );
        
        $this->assign('top_more_button', $top_more_button);
        $model = $this->model;
        
        $list_data = $this->_get_model_list($this->model,'id desc',true);

        unset($list_data['list_grids']['shop_code']);
        
        if ($isAjax) {
            unset($list_data['list_grids']['phone']);
            unset($list_data['list_grids']['urls']);
            $this->assign($list_data);
            return $this->fetch('lists_data');
        } else {
        	unset($list_data['list_grids']['urls']);
            $this->assign($list_data);
            return $this->fetch();
        }
    }

    function list_data()
    {
        $map['wpid'] = WPID;
        $page_data = M('stores')->where(wp_where($map))
            ->order('id DESC')
            ->paginate(20);
        $list = dealPage($page_data);
        
        echo json_url($list);
    }

    function output()
    {
        return parent::common_export($this->model);
    }

    function import()
    {
        $model = $this->getModel('import');
        if (IS_POST) {
            $column = array(
                'A' => 'name',
                'B' => 'phone',
                'C' => 'address'
            );
            
            $attach_id = I('attach', 0);
            
            $res = importFormExcel($attach_id, $column);
            if ($res['status'] == 0) {
                $this->error($res['data']);
            }
            $total = count($res['data']);
            foreach ($res['data'] as $vo) {
                if (empty($vo['name'])) {
                    $this->error('店名不能为空');
                }
                if (empty($vo['address'])) {
                    $this->error('详细地址不能为空');
                }
                $vo['wpid'] = WPID;
                $r = M('stores')->insertGetId($vo);
            }
            $msg = "共导入" . $total . "条记录";
            // dump($arr);
            // $msg = trim( $msg, ', ' );
            // dump($msg);exit;
            
            $this->success($msg, U('lists'));
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            
            $this->assign('post_url', U('import'));
            $this->assign('import_template', 'stores.xls');
            
            return $this->fetch('common@base/import');
        }
    }

    function del()
    {
        return parent::del($this->model);
    }

    function add()
    {
        $model = $this->model;
        if (request()->isPost()) {
            $Model = D($model['name']);
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkData($data, $model);
            $data['wpid'] = WPID;
            $id = $Model->insertGetId($data);
            if ($id) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                
                $this->success('添加' . $model['title'] . '成功！', U('lists', $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            
            return $this->fetch();
        }
    }

    public function edit()
    {
        $model = $this->model;
        $where['id'] = $id = I('id/d', 0);
        // 获取数据
        $data = D($model['name'])->where(wp_where($where))->find();
        
        $data || $this->error('数据不存在！');
        
        if (WPID != $data['wpid']) {
            $this->error('非法访问！');
        }
        if (request()->isPost()) {
            $Model = D($model['name']);
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkData($data, $model);
            $data['wpid'] = WPID;
            $res = $Model->save($data, [
                'id' => $id
            ]);
            if ($res !== false) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                
                $this->success('保存' . $model['title'] . '成功！', U('lists', $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->assign('data', $data);
            $this->assign('post_url', U('edit', array(
                'id' => $data['id']
            )));
            return $this->fetch();
        }
    }

    function sence_qr()
    {
        $id = I('post.id/d', 0);
        if ($id) {
            $res['qrcode'] = D('home/QrCode')->add_qr_code('QR_SCENE', 'Stores', $id);
            $this->ajaxReturn($res, 'JSON');
        }
    }

}
