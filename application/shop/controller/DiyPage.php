<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class DiyPage extends Base
{

    public $model;

    public function initialize()
    {
        parent::initialize();
        $this->model = $this->getModel('shop_page');
        $controller = strtolower(CONTROLLER_NAME);
        $use = I('use', 'page');
        
        $res['title'] = '自定义专题';
        $res['url'] = U('shop/DiyPage/lists');
        $res['class'] = ACTION_NAME == 'lists' || $use == "page" && ACTION_NAME == 'edit' ? 'current' : '';
        $nav[] = $res;
        
        $use = I('get.use', 'page');
        if ($use == "goodsDetail") {
            $nav = array();
            $res['title'] = '商品详情页';
            $res['url'] = '#';
            $res['class'] = 'current';
            $nav[] = $res;
        }
        $this->assign('nav', $nav);
    }

    public function lists()
    {
        $map['use'] = 'page';
        session('common_condition', $map);
        $list_data = $this->_get_model_list($this->model);

        foreach ($list_data['list_data'] as &$vo) {
            $copyUrl = U('Shop/Wap/diy_page', array(
                'id' => $vo['id'],
                'uid' => $this->mid
            ));
            $vo['copy'] = '<a data-clipboard-text="' . $copyUrl . '" id="copybtn_' . $vo['id'] . '" href="javascript:;">复制链接</a><script type="text/javascript">$.WeiPHP.initCopyBtn("copybtn_' . $vo['id'] . '")</script>';
        }
        $this->assign($list_data);
        
        return $this->fetch();
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $model = $this->model;
        $id = I('id');
        $use = I('use', 'page');
        $this->assign('use', $use);
        $wpid = WPID;
        // dump(input('post.'));die;
        $goods_id = I('goods_id/d', 0);
        if (IS_POST) {
            $data = input('post.');
            // $data['wpid'] = WPID;
            $Model = D($model['name']);
            // 获取模型的字段信息
            $data = $this->checkData($data, $model);
            $this->dealIsIndex($data);
            // dump($Model);exit;
            $res = false;
            $res = $Model->isUpdate(true)->save($data);
            if ($res !== false) {
                D('DiyPage')->getInfo($id, true);
                // D('common/Keyword' )->set ( input('post.keyword'), _ADDONS, $id, input('post.keyword_type'), 'custom_reply_news' );
                if ($use == "goodsDetail") {
                    $this->success('添加' . $model['title'] . '成功！', U('Shop/Goods/lists'), true);
                } elseif ($use != "page") {
                    $this->success('保存' . $model['title'] . '成功！', U('Shop/DiyPage/edit', array(
                        'id' => $id,
                        'use' => $use
                    )), true);
                } else {
                    $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param), true);
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 选择本地编辑器 或七牛编辑器
            $uploadDriver = strtolower(config("EDITOR_PICTURE_UPLOAD_DRIVER"));
            if ($uploadDriver == 'qiniu') {
                $driverfile = 'ueditor_qiniu';
            } else {
                $driverfile = 'ueditor';
            }
            $this->assign('driver_file', $driverfile);
            
            $fields = get_model_attribute($model);
            // 获取数据
            $data = D('DiyPage')->getInfo($id);
            $data || $this->error('数据不存在！');
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            $title = '';
            if ($goods_id > 0) {
                $goods = D("shop/ShopGoods")->getInfo($goods_id);
                $title = $goods['title'];
            }
            $this->assign('goods_title', $title);
            
            $post_url = U('edit', [
                'id' => $id,
                'use' => $use,
                'goods_id' => $goods_id
            ]);
            $this->assign('post_url', $post_url);
            
            return $this->fetch();
        }
    }

    private function dealIsIndex($data)
    {
        if (isset($data['is_index']) && $data['is_index'] == 1) {
            $wpid = isset($data['wpid']) ? $data['wpid'] : get_wpid();
            M('shop_page')->where('wpid', $wpid)->setField('is_index', 0);
        }
    }

    // 通用插件的增加模型
    public function add()
    {
        $model = $this->model;
        $Model = D($model['name']);
        $use = I('use', 'page');
        $goods_id = I('id/d', 0);
        $this->assign('id', $goods_id);
        $diyId = M('shop_goods')->where('id', $goods_id)->value('diy_id');
        
        if (request()->isPost()) {
        	if ($use == "goodsDetail") {
        		$jurl=U('Shop/Goods/lists');
        	} elseif ($use != "page") {
        		$jurl=U('Shop/DiyPage/edit', array(
        				'id' => $id,
        				'use' => $use
        		));
        	} else {
        		$jurl= U('lists?model=' . $model['name'], $this->get_param);
        	}
            $res1['url'] =$jurl;
            $data = $this->checkdata(input('post.'), $model);
            $this->dealIsIndex($data);
            
            if ($diyId > 0) {
                $Model = D($model['name']);
                $res = false;
                $res = $Model->where('id', $diyId)->update($data);
                if ($res !== false) {
//                     return $res1;
                    $this->success ( '保存' . $model ['title'] . '成功！',  $res1['url']);
                } else {
                    $this->error($Model->getError());
                }
            } else {
                $id = $Model->insertGetId($data);
                if ($id) {
                    M('shop_goods')->where('id', $goods_id)->setField('diy_id', $id);
                    return $res1;
                } else {
                    $this->error($Model->getError());
                }
            }
        } else {
            // 选择本地编辑器 或七牛编辑器
            $uploadDriver = strtolower(config("EDITOR_PICTURE_UPLOAD_DRIVER"));
            if ($uploadDriver == 'qiniu') {
                $driverfile = 'ueditor_qiniu';
            } else {
                $driverfile = 'ueditor';
            }
            $this->assign('driver_file', $driverfile);
            
            if ($diyId > 0) {
                // 获取数据
                $data = D('DiyPage')->getInfo($diyId, '', '');
                
                $data || $this->error('数据不存在！');
                
                $this->assign('data', $data);
                
                $post_url = U('add?model=' . $model['id'] . '&id=' . $goods_id.'&use='.$use);
                $this->assign('post_url', $post_url);
            } else {
                $post_url = U('add?model=' . $model['id'] . '&id=' . $goods_id.'&use='.$use, $this->get_param);
                $this->assign('post_url', $post_url);
                
                $data = [];
                $this->assign('data', $data);
            }
            
            $title = '';
            if ($goods_id > 0) {
                $goods = D("shop/ShopGoods")->getInfo($goods_id);
                $title = $goods['title'];
            }
            $this->assign('goods_title', $title);
            
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            
            $this->assign('use', $use);
            
            return $this->fetch('edit');
        }
    }

    public function diyPage()
    {
        $map['use'] = I('use', 'page');
        $map['manager_id'] = $this->mid;
        $map['wpid'] = get_wpid();
        $res = M('shop_page')->where(wp_where($map))->find();
        if ($res) {
            $id = $res['id'];
        } else {
            $map['ctime'] = time();
            $id = M('shop_page')->insertGetId($map);
        }
        return redirect(U('edit', array(
            'id' => $id,
            'use' => $map['use']
        )));
    }

    public function preview()
    {
        $id = I('id');
        $url = U('Shop/Wap/diy_page', array(
            'id' => $id
        ));
        $this->assign('url', $url);
        return $this->fetch('common@base/preview');
    }
}
