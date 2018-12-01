<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class Goods extends Base
{

    public $model;

    public function initialize()
    {
        $this->model = $this->getModel('shop_goods');
        parent::initialize();
        
        $type = I('type/d', 0);
        $param['mdm'] = isset($_GET['mdm']) ? input('mdm') : '';
        $param['type'] = 0;
        $res['title'] = '出售中的商品';
        $res['url'] = U('Shop/Goods/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 0 ? 'current' : '';
        $nav[] = $res;
        
        $param['type'] = 4;
        $res['title'] = '待上架的商品';
        $res['url'] = U('Shop/Goods/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 4 ? 'current' : '';
        $nav[] = $res;
        
        $param['type'] = 1;
        $res['title'] = '已售罄的商品';
        $res['url'] = U('Shop/Goods/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 1 ? 'current' : '';
        $nav[] = $res;
        
        $param['type'] = 2;
        $res['title'] = '下架的商品';
        $res['url'] = U('Shop/Goods/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 2 ? 'current' : '';
        $nav[] = $res;
        
        $param['type'] = 3;
        $res['title'] = '商品回收站';
        $res['url'] = U('Shop/Goods/lists', $param);
        $res['class'] = ACTION_NAME == 'lists' && $type == 3 ? 'current' : '';
        $nav[] = $res;
        
        if (ACTION_NAME == 'edit') {
            $res['title'] = '编辑商品';
            $res['url'] = '#';
            $res['class'] = 'current';
            $nav[] = $res;
        } else if (ACTION_NAME == 'add') {
            $map['mdm'] = isset($_GET['mdm']) ? input('mdm') : '';
            $res['title'] = '发布商品';
            $res['url'] = U('Shop/Goods/add', $map);
            $res['class'] = ACTION_NAME == 'add' ? 'current' : '';
            $nav[] = $res;
        }
        
        $this->assign('nav', $nav);
    }

    // 通用插件的列表模型
    public function lists()
    {
        $isAjax = I('isAjax');
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $type = I('type/d', 0);
        
        $res['title'] = '新增商品';
        $res['is_buttion'] = 0;
        $res['url'] = U('Shop/Goods/add');
        $res['class'] = 'btn';
        $top_more_button[] = $res;
        
        if ($type == 0 || $type == 1) {
            $res['title'] = '批量下架';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/set_down?val=0', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
        } elseif ($type == 2) {
            $res['title'] = '批量上架';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/set_down?val=1', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
            
            $res['title'] = '批量删除';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/del?val=0', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
        } elseif ($type == 3) {
            $res['title'] = '批量下架';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/set_down?val=2', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
            
            $res['title'] = '彻底删除';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/del?val=1&type=3', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
        } elseif ($type == 4) {
            $res['title'] = '批量上架';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/set_down?val=1', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
            
            $res['title'] = '批量删除';
            $res['is_buttion'] = 1;
            $res['url'] = U('Shop/Goods/del?val=0', $this->get_param);
            $res['class'] = 'btn ajax-post confirm';
            $top_more_button[] = $res;
        }
        $this->assign('top_more_button', $top_more_button);
        $map['is_delete'] = 0;
        if ($type == 1) {
            // 售完
            $map['is_show'] = 1;
            $map['stock_active'] = [
                '<=',
                '0'
            ];
        } else if ($type == 2) {
            // 下架
            $map['is_show'] = 0;
        } else if ($type == 0) {
            // 出售
            $map['is_show'] = 1;
            $map['stock_active'] = [
                '>',
                '0'
            ];
        } else if ($type == 3) {
            // 回收站
            $map['is_delete'] = 1;
        } else if ($type == 4) {
            $map['is_show'] = 2;
        }
        $map2['wpid'] = $map1['wpid'] = $map['wpid'] = get_wpid();
        
        $cid = I('cid/d', 0);
        $this->assign('cid', $cid);
        
        if ($cid) {
            $goodsIdArr = M('goods_category_link')->where('category_second|category_first', $cid)->column('goods_id');
            $goodsDao = D('shop/ShopGoods');
            if ($goodsIdArr) {
                $map['id'] = array(
                    'in',
                    $goodsIdArr
                );
            } else {
                $map['id'] = 0;
            }
        }
        
        session('common_condition', $map);
        $model = $this->model;
        if (empty($model)) {
            return false;
        }
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $fields = $list_data['fields'];
        
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        
        $row = empty($model['list_row']) ? 20 : $model['list_row'];
        
        // 读取模型数据列表
        
        empty($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name($model['name'], true);
        
        $data = M($name)->alias('g')
            ->join('shop_goods_stock s', 's.goods_id = g.id and s.event_type=' . SHOP_EVENT_TYPE)
            ->where(wp_where($map))
            ->order('g.id desc')
            ->paginate($row);
        
        // 分类数据
        $map2['is_show'] = 1;
        $list = M('shop_goods_category')->where(wp_where($map2))
            ->field('id,title')
            ->select();
        $cate[0] = '';
        foreach ($list as $vo) {
            $cate[$vo['id']] = $vo['title'];
        }
        
        $this->assign('goods_category', $list);
        $list_data = $this->parsePageData($data, $model, $list_data, false);
        if ($isAjax) {
            unset($list_data['list_grids']['sale_count']);
            unset($list_data['list_grids']['is_show']);
            unset($list_data['list_grids']['urls']);
            
            $this->assign($list_data);
            $this->assign('isRadio', I('isRadio/d', 0));
            return $this->fetch('lists_data');
        } else {
            $this->assign($list_data);
            return $this->fetch();
        }
    }

    public function isUrl($url)
    {
        $regex = "((http|ftp|https)://)(([a-zA-Z0-9\._-]+\.[a-zA-Z]{2,6})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,4})*(/[a-zA-Z0-9\&%_\./-~-]*)?";
        $res = preg_match($regex, $url);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function _check_post_data($data)
    {
        if (empty($data['cate_first'])) {
            $this->error('请填写添加商品分类', '', true);
        }
        
        // 无规格、删除所配置的规格信息
        unset($data['spec']);
        unset($data['market_price_arr']);
        // }
        $data['is_show'] = isset($data['is_show']) ? $data['is_show'] : 0;
        if ($data['is_show'] == 1 && $data['stock'] <= 0) {
            $this->error('直接上架，库存必须大于0', '', true);
        }
        
        // 没有规格
        $markPrice = floatval($data['market_price']); // 市场价格
        $salePrice = floatval($data['sale_price']); // 促销价格
        if ($markPrice <= 0) {
            $this->error('原价必须大于0元', '', true);
        }
        if ($salePrice > $markPrice) {
            $this->error('促销价应小于原价', '', true);
        }
        
        if (empty($data['title'])) {
            $this->error('请填写商品名称', '', true);
        }
        if ($data['imgs'] && count($data['imgs']) > 0) {
            $data['cover'] = $data['imgs'][0];
            $data['imgs'] = implode(',', $data['imgs']);
        } else {
            $this->error('请上传商品图片', '', true);
        }
        
        return $data;
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $model = $this->model;
        $id = I('id');
        
        if (request()->isPost()) {
            $data = input('post.');
            
            $data = $this->_check_post_data($data);
            
            $Model = D($model['name']);
            
            $data = $this->checkData($data, $model);
            // 获取模型的字段信息
            $res = false;
            $res = $Model->isUpdate(true)->save($data);
            if ($res !== false) {
                // 保存商品分类信息
                $this->set_category($id, input('post.'));
                // 保存商品参数
                $this->set_param($id, input('post.'));
                
                // 保存详情
                $this->set_content($id, input('post.content'));
                
                // 保存库存
                D('Stock')->saveStock($id, input('post.'));
                
                // 保存商品所在门店
                D('shop/GoodsStoreLink')->set_store($id, SHOP_EVENT_TYPE, $data);
                
                // 更新缓存
                $goodsInfo = D('ShopGoods')->getInfo($id, true);
                
                $nextUrl = U('lists');
                $this->success('保存' . $model['title'] . '成功！', $nextUrl, true);
            } else {
                $this->error($Model->getError(), '', true);
            }
        } else {
            // 获取数据
            $data = D($model['name'])->getInfo($id, true);
            $data || $this->error('数据不存在！');
            
            if (isset($data['wpid']) && WPID != $data['wpid']) {
                $this->error('非法访问！');
            }
            
            $data['imgs'] = explode(',', $data['imgs']);
            $data['stores_ids']=D('shop/GoodsStoreLink')->get_store($id,$data['event_type']);
//             dump($data);
            
            $this->assign('data', $data);
            // 商品分类
            $catelists = D('shop/Category')->getCateDatalists();
            // dump($catelists);
            $this->assign('cate_data', $catelists);
            
            $list = M('goods_category_link')->where('goods_id', $id)->select();
            // dump($list);
            $this->assign('cate_list', $list);
            // dump($gmap);
            // 获取商品参数
            $param_lists = M('goods_param_link')->where('goods_id', $id)
                ->order('id asc')
                ->select();
            $this->assign('param_lists', $param_lists);
            
            return $this->fetch();
        }
    }

    // 通用插件的增加模型
    public function add()
    {
        $model = $this->model;
        $Model = D($model['name']);
        // dump($Model);
        
        if (request()->isPost()) {
            $data = input('post.');
            
            $data = $this->_check_post_data($data);
            $data = $this->checkData($data, $model);
            
            $id = $Model->insertGetId($data);
            if ($id) {
                // 保存商品分类信息
                $this->set_category($id, input('post.'));
                // 保存商品参数
                $this->set_param($id, input('post.'));
                
                // 保存详情
                $this->set_content($id, input('post.content'));
                
                // 保存库存
                D('Stock')->saveStock($id, input('post.'));
                
                // 保存商品所在门店
                D('shop/GoodsStoreLink')->set_store($id, SHOP_EVENT_TYPE, $data);
                
                $data['is_show'] = input('?post.is_show') ? input('post.is_show') : '';
                
                $nextUrl = U('lists');
                $this->success('保存' . $model['title'] . '成功！', $nextUrl, true);
            } else {
                $this->error($Model->getError(), '', true);
            }
        } else {
            $catelists = D('shop/Category')->getCateDatalists();
            $this->assign('cate_data', $catelists);
            // dump($fields);
            $this->assign('post_url', U('add'));
            
            $this->assign('data', []);
            return $this->fetch('edit');
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        $id = I('id');
        $ids = I('ids');
        if (empty($id) && empty($ids)){
        	$this->error('请选择要操作的数据!');
        }
        $type = I('type');
        if (! empty($id)) {
            $key = 'Goods_getInfo_' . $id;
            $map['id'] = $id;
            S($key, null);
        } else {
            foreach ((array) $ids as $i) {
                $key = 'Goods_getInfo_' . $i;
                S($key, null);
            }
            $map[] = array(
                'id',
                'in',
                $ids
            );
        }
        if ($type == 3) {
            $save['is_delete'] = 2;
            $save['is_show'] = 0;
        } else {
            $save['is_delete'] = 1;
            $save['is_show'] = 0;
        }
        
        $res = D('ShopGoods')->where(wp_where($map))->update($save);
        if ($res !== false && $type == 3) {
            $this->success('删除成功');
        } else if ($res) {
            $this->success('商品已加入回收站');
        }
        
        // return parent::common_del ( $this->model );
    }

    // /////////////商品参数配置///////////////////////
    // 商品详情
    function set_content($goods_id, $content)
    {
        $id = M('shop_goods_content')->where('goods_id', $goods_id)->value('id');
        if ($id > 0) {
            M('shop_goods_content')->where('goods_id', $goods_id)->setField('content', $content);
        } else {
            M('shop_goods_content')->insert([
                'goods_id' => $goods_id,
                'content' => $content
            ]);
        }
    }

    // 保存商品分类
    public function set_category($goods_id, $data)
    {
        $gmap['goods_id'] = $goods_id;
        $list = M('goods_category_link')->where(wp_where($gmap))->select();
        foreach ($list as $v) {
            $arr[$v['sort']] = $v['id'];
        }
        foreach ($data['cate_first'] as $key => $val) {
            if (empty($val)) {
                continue;
            }
            $save['goods_id'] = $goods_id;
            $save['wpid'] = get_wpid();
            $save['sort'] = $key;
            $save['category_first'] = intval($val);
            $save['category_second'] = intval($data['select_cate_second'][$key]);
            if (! empty($arr[$key])) {
                $ids[] = $map['id'] = $arr[$key];
                M('goods_category_link')->where(wp_where($map))->update($save);
            } else {
                $ids[] = M('goods_category_link')->insertGetId($save);
            }
            unset($save);
        }
        $arr = isset($arr) ? $arr : [];
        $diff = array_diff($arr, $ids);
        if (! empty($diff)) {
            $map2['id'] = array(
                'in',
                $diff
            );
            M('goods_category_link')->where(wp_where($map2))->delete();
        }
    }

    // 保存商品参数
    public function set_param($goods_id, $post)
    {
        foreach ($post['goods_param_title'] as $key => $opt) {
            if (empty($opt)) {
                continue;
            }
            
            $opt_data['goods_id'] = $goods_id;
            $opt_data['wpid'] = get_wpid();
            $opt_data['title'] = $opt;
            $opt_data['param_value'] = $post['goods_param_value'][$key];
            if ($key > 0) {
                // 更新选项
                $optIds[] = $map['id'] = $key;
                M('goods_param_link')->where(wp_where($map))->update($opt_data);
            } else {
                // 增加新选项
                $optIds[] = M('goods_param_link')->insertGetId($opt_data);
            }
            unset($opt_data);
        }
        if (! empty($optIds)) {
            // 删除旧选项
            $map2['id'] = array(
                'not in',
                $optIds
            );
            $map2['goods_id'] = $goods_id;
            M('goods_param_link')->where(wp_where($map2))->delete();
        }
        if (empty($post['goods_param_title'])) {
            $map['goods_id'] = $goods_id;
            M('goods_param_link')->where(wp_where($map))->delete();
        }
    }

    public function set_show()
    {
        $isShow = I('is_show');
        $save['is_show'] = 1 - $isShow;
        $map['id'] = I('id');
        
        $map['wpid'] = WPID;
        $type = I('type');
        if ($type == 3 || $type == 4) {
            $save['is_show'] = 1;
            $save['is_delete'] = 0;
        }
        $res = M('shop_goods')->where(wp_where($map))->update($save);
        
        $this->success('操作成功', U('lists'));
    }

    public function set_down()
    {
        $val = I('val');
        $ids = I('ids');
        if (empty($ids)){
        	$this->error('请选择要操作的数据!');
        }
        // dump($ids);exit;
        if ($val == 0 || $val == 2) {
            $save['is_show'] = 0;
        } else if ($val == 1) {
            $save['is_show'] = 1;
        }
        if (! empty($ids)) {
            $res = D('shop/ShopGoods')->whereIn('id', $ids)->update($save);
        }
        if (isset($res) && $res !== false) {
            $this->success('操作成功');
        }
    }

    public function getConfigGoods()
    {
        $goodsId = I('goods_id/d', 0);
        $info = D('Shop/ShopGoods')->where('id', $goodsId)->find();
        $info['img'] = get_cover_url($info['cover']);
        $this->ajaxReturn($info);
    }

    public function goodsCommentLists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $search = input('title');
        if ($search) {
            $this->assign('search', $search);
            
            $map1['nickname'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $truename_follow_ids = D('common/User')->where(wp_where($map1))->column('uid');
            // $truename_follow_ids = implode ( ',', $truename_follow_ids );
            if (! empty($truename_follow_ids)) {
                $map['uid'] = array(
                    'in',
                    $truename_follow_ids
                );
            } else {
                $map['id'] = 0;
            }
            
            unset($_REQUEST['title']);
        }
        
        $map['goods_id'] = $goodsId = I('goods_id/d', 0);
        $model = $this->getModel('shop_goods_comment');
        session('common_condition', $map);
        $list_data = $this->_get_model_list($model, 'id desc');
        $goodsTitle = M('shop_goods')->where('id',$goodsId)->value('title');
        foreach ($list_data['list_data'] as &$vo){
			$vo ['uid'] = empty ( $vo ['uid'] ) ? '匿名' : $vo ['uid'];
			$vo ['goods_title'] = empty ( $vo ['goods_title'] ) ? $goodsTitle : $vo ['goods_title'];
        }
        
        $this->assign($list_data);
        $this->assign('search_url', U('Shop/Goods/goodsCommentLists', array(
            'mdm' => input('mdm'),
            'goods_id' => $goodsId
        )));
        $this->assign('placeholder', '请输入用户昵称');
        return $this->fetch('goods_comment_lists');
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
