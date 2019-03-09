<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class ParamTemp extends Base
{

    public function initialize()
    {
        $this->model = $this->getModel('goods_param_temp');
        parent::initialize();
    }

    function lists()
    {
        return parent::common_lists($this->model);
    }

    public function edit()
    {
        $id = I('id');
        
        if (request()->isPost()) {
            $data = input('post.');
            
            $data = $this->checkPost($data);
            if (empty($id)) {
                $res = M('goods_param_temp')->insert($data);
            } else {
                $res = M('goods_param_temp')->where('id', $id)->update($data);
            }
            if ($res !== false) {
                $this->success('保存成功！', U('lists'));
            } else {
                $this->error('保存失败！');
            }
        } else {
            // 获取数据
            $data = M('goods_param_temp')->where('id', $id)->find();
            $data || $this->error('数据不存在！');
            
            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }
            
            $type = input('type');
            if ($type == 'copy') {
                $data['id'] = 0;
            }
            
            $this->assign('data', $data);
            
            $this->assign('param_lists', json_decode($data['param'], true));
            
            return $this->fetch();
        }
    }

    private function checkPost($data)
    {
        if (empty($data['title'])) {
            $this->error('模板名称不能为空');
        }
        $data['param'] = json_encode($data['param']);
        $data['wpid'] = WPID;
        return $data;
    }

    public function add()
    {
        if (request()->isPost()) {
            // 获取模型的字段信息
            $data = input('post.');
            $data = $this->checkPost($data);
            // dump($data);exit;
            $id = M('goods_param_temp')->insertGetId($data);
            if ($id) {
                $this->success('添加成功！', U('lists'));
            } else {
                $this->error('添加失败！');
            }
        } else {
            $param_lists = [
                [
                    'name' => 'barcode',
                    'title' => '条码号'
                ],
                [
                    'name' => 'sort',
                    'title' => '货品种类'
                ],
                [
                    'name' => 'style',
                    'title' => '款式'
                ],
                [
                    'name' => 'modelno',
                    'title' => '模号'
                ],
                [
                    'name' => 'govbarcode',
                    'title' => '证书编号'
                ],
                [
                    'name' => 'govbarcode2',
                    'title' => '证书编号2'
                ],
                [
                    'name' => 'giacertno',
                    'title' => 'GIA证书号'
                ],
                [
                    'name' => 'diamondno',
                    'title' => '主石粒数'
                ],
                [
                    'name' => 'diamondweight',
                    'title' => '主石石重'
                ],
                [
                    'name' => 'pno',
                    'title' => '副石粒数'
                ],
                [
                    'name' => 'pweight',
                    'title' => '副石石重'
                ],
                [
                    'name' => 'dmaterial',
                    'title' => '主石石料'
                ],
                [
                    'name' => 'pmaterial',
                    'title' => '副石石料'
                ],
                [
                    'name' => 'goldweight',
                    'title' => '金重'
                ],
                [
                    'name' => 'itemweight',
                    'title' => '件重'
                ],
                [
                    'name' => 'cirlenght',
                    'title' => '圈口'
                ],
                [
                    'name' => 'diameter',
                    'title' => '直径'
                ],
                [
                    'name' => 'specification',
                    'title' => '规格'
                ],
                [
                    'name' => 'cutting',
                    'title' => '切工'
                ],
                [
                    'name' => 'polishing',
                    'title' => '抛光'
                ],
                [
                    'name' => 'symmetry',
                    'title' => '对称'
                ],
                [
                    'name' => 'fluorescent',
                    'title' => '荧光'
                ],
                [
                    'name' => 'fluorescentcolor',
                    'title' => '荧光颜色'
                ],
                [
                    'name' => 'collectionname',
                    'title' => '系统名称'
                ],
                [
                    'name' => 'collectiondesc',
                    'title' => '系统款式'
                ],
                [
                    'name' => 'itemcalmethod',
                    'title' => '计价方式'
                ],
                [
                    'name' => 'itemtype',
                    'title' => '销售类型'
                ],
                [
                    'name' => 'remark',
                    'title' => '备注'
                ]
            ];
            
            foreach ($param_lists as &$vo) {
                $vo['value'] = '';
                $vo['is_show'] = 0;
            }
            $this->assign('param_lists', $param_lists);
            return $this->fetch('edit');
        }
    }

    function load_data()
    {
        $productid = input('productid/d');
        $goods = D('shop/ShopGoods')->where('productid', $productid)->find();
        if (! isset($goods['productid'])) {
            $param = M('erp_goods')->where('productid', $productid)->value('param');
            $goods = json_decode($param, true);
        }
        
        if (! isset($goods['productid'])) {
            return $this->error('获取商品信息失败');
        }
        
        $id = input('id/d');
        $lists = D('shop/GoodsParamTemp')->getParam($id, $goods);
        
        return json($lists);
    }

    function init_param()
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        $list = M('erp_goods')->where('productid', '>', '0')
            ->field('param')
            ->select();
        
        $dao = D('shop/ShopGoods');
        foreach ($list as $vo) {
            $goods = json_decode($vo['param'], true);
            $dao->where('productid', $goods['productid'])->update($goods);
        }
    }
}
