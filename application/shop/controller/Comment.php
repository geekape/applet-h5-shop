<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class Comment extends Base
{

    public $model;

    public function initialize()
    {
        $this->model = $this->getModel('shop_goods');
        parent::initialize();
        
        $from = input('from');
        if ($from == 'goods') {
			$param['type'] = 0;
			$res['title'] = '出售中的商品';
			$res['url'] = U('Shop/Goods/lists', $param);
			$res['class'] = '';
			$nav[] = $res;
			
			$param['type'] = 4;
			$res['title'] = '待上架的商品';
			$res['url'] = U('Shop/Goods/lists', $param);
			$res['class'] = '';
			$nav[] = $res;
			
			$param['type'] = 1;
			$res['title'] = '已售罄的商品';
			$res['url'] = U('Shop/Goods/lists', $param);
			$res['class'] = '';
			$nav[] = $res;
			
			$param['type'] = 2;
			$res['title'] = '下架的商品';
			$res['url'] = U('Shop/Goods/lists', $param);
			$res['class'] = '';
			$nav[] = $res;
			
			$param['type'] = 3;
			$res['title'] = '商品回收站';
			$res['url'] = U('Shop/Goods/lists', $param);
			$res['class'] = '';
			$nav[] = $res;

            $res['title'] = '商品评价';
            $res['url'] = U('Shop/Comment/lists?from=goods');
            $res['class'] = 'current';
            $nav[] = $res;
        } else {
            $param['status'] = 1;
            $res['title'] = '待支付';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $param['status'] = 2;
            $res['title'] = '已支付';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $param['status'] = 3;
            $res['title'] = '待确认';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $param['status'] = 4;
            $res['title'] = '已完成';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $param['status'] = 5;
            $res['title'] = '退款';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $param['status'] = 0;
            $res['title'] = '全部';
            $res['url'] = U('shop/Order/lists', $param);
            $res['class'] = '';
            $nav[] = $res;

            $res['title'] = '商品评价';
            $res['url'] = U('lists');
            $res['class'] = 'current';
            $nav[] = $res;
        }

        $this->assign('nav', $nav);
    }

    public function lists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $search = input('title');
        $map = [];
        if ($search) {
            $this->assign('search', $search);

            $gids = D('shop/ShopGoods')->where("title like '%{$search}%'")
                ->where('wpid', WPID)
                ->column('id');
            if (!empty($gids)) {
                $map['goods_id'] = [
                    'in',
                    $gids
                ];
            } else {
                $map['id'] = 0;
            }

            unset($_REQUEST['title']);
        }

        $model = $this->getModel('shop_goods_comment');
        session('common_condition', $map);
        $list_data = $this->_get_model_list($model, 'id desc', true);

        if (!empty($list_data['list_data'])) {
            $goods_ids = getSubByKey($list_data['list_data'], 'goods_id');
            $titleArr = M('shop_goods')->whereIn('id', $goods_ids)->column('title', 'id');
            foreach ($list_data['list_data'] as &$vo) {
                $vo['goods_title'] = isset($titleArr[$vo['goods_id']]) ? $titleArr[$vo['goods_id']] : '';
            }
        }

        $this->assign($list_data);

        $this->assign('search_url', U('lists'));
        $this->assign('placeholder', '请输入商品名搜索');
        return $this->fetch();
    }

    public function changeShow()
    {
        $id = I('id');
        $info = M('shop_goods_comment')->where('id', $id)->find();
        $save['is_show'] = 1 - $info['is_show'];
        $res = M('shop_goods_comment')->where('id', $id)->update($save);
        if ($res !== false) {
            D('Shop/GoodsComment')->getShopComment($info['goods_id'], true);
            $this->success('设置成功！');
        } else {
            $this->error('设置失败！');
        }
    }
}
