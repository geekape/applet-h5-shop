<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class Category extends Base
{

    var $model;

    function initialize()
    {
        $this->model = $this->getModel('shop_goods_category');
        parent::initialize();
    }

    // 通用插件的列表模型
    public function lists()
    {
        $model = $this->model;
        $map['wpid'] = get_wpid();
        session('common_condition', $map);
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $fields = ! empty($list_data) ? $list_data['fields'] : [];
        // dump($map);
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        
        // 读取模型数据列表
        $name = parse_name($this->model['name'], true);
        $data = M($name)->field(true)
            ->where(wp_where($map))
            ->order('sort asc,id asc')
            ->select();
        
        /* 查询记录总数 */
        $data = $this->parseListData($data, $model);
        
        unset($list_data['list_grids']['pid']);
        $new_data = [];
        list_tree($data, $new_data);
        $list_data['list_data'] = $new_data;
        $this->assign($list_data);
        // $templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
        return $this->fetch();
    }

    function tree_to_list($tree, $child = '_child', &$list = [])
    {
        if (is_array($tree)) {
            $refer = [];
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if (isset($reffer[$child])) {
                    unset($reffer[$child]);
                    $this->tree_to_list($value[$child], $child, $list);
                }
                $list[] = $reffer;
            }
        }
        return $list;
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $model = $this->model;
        $id = I('id');
        $wpid = WPID;
        if (request()->isPost()) {
            $Model = D($model['name']);
            // 获取模型的字段信息
            $data = I('post.');
            $data['wpid'] = get_wpid();
            $data['wpid'] = WPID;
            $data = $this->checkData($data, $model);
            $id = $Model->isUpdate(true)->save($data);
            if ($id!==false) {
                $map['pid'] = input('post.id');
                $map['wpid'] = get_wpid();
                $secIds = M($this->model['name'])->where(wp_where($map))->column('id');
                $map1['id'] = array(
                    'in',
                    $secIds
                );
                
                if (input('post.pid') != 0) {
                    if (! empty($secIds)) {
                        $setsave['pid'] = input('post.pid');
                        M($this->model['name'])->where(wp_where($map1))->update($setsave);
                    }
                } else {
                    if ($data['is_show'] == 0 && ! empty($secIds)) {
                        // 把子类也设置为隐藏
                        M($this->model['name'])->where(wp_where($map1))->update([
                            'is_show' => 0
                        ]);
                    }
                }
                // D ( 'common/Keyword' )->set ( input('post.keyword'), _ADDONS, $id, input('post.keyword_type'), 'custom_reply_news' );
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $extra = D('shop/Category')->getCateData($id, false);
            if (! empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        if (isset($vo['extra'])) {
                            $vo['extra'] .= "\r\n" . $extra;
                        } else {
                            $vo['extra'] = $extra;
                        }
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
            $data['imgs'] = isset($data['imgs']) ? $data['imgs'] : '';
            $data['imgs'] = explode(',', $data['imgs']);
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            $this->meta_title = '编辑' . $model['title'];
            
            return $this->fetch();
        }
    }

    // 通用插件的增加模型
    public function add()
    {
        $wpid = WPID;
        $model = $this->model;
        if (request()->isPost()) {
            $data = I('post.');
            // 获取模型的字段信息
            $Model = D($model['name']);
            
            $res = $this->checkData($data, $model);
            $data['wpid'] = get_wpid();
            $data['wpid'] = WPID;
            $id = $Model->insertGetId($data);
            if ($id) {
                // D ( 'common/Keyword' )->set ( input('post.keyword'), _ADDONS, $id, input('post.keyword_type'), 'custom_reply_news' );
                
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $extra = D('shop/Category')->getCateData(0, false);
            
            if (! empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        if (isset($vo['extra'])) {
                            $vo['extra'] .= "\r\n" . $extra;
                        } else {
                            $vo['extra'] = $extra;
                        }
                    }
                }
            }
            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $model['title'];
            
            return $this->fetch();
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        $id = input('id/d');
        
        $info = D('Category')->where('id', $id)->find();
        if ($info['wpid'] != WPID) {
            $this->error('你无权限删除');
        }
        $ids = [];
        if ($info['pid'] == 0) {
            // 同时要删除下级分类
            $ids = D('Category')->where('pid', $id)->column('id');
        }
        
        $ids[] = $id;
        
        // 删除商品与分类的关系
        $goods_ids = M('goods_category_link')->whereIn('category_first|category_second', $ids)
            ->group('goods_id')
            ->column('goods_id');
        if (! empty($goods_ids)) {
            M('goods_category_link')->whereIn('category_first|category_second', $ids)->delete();
            
            $goodsDao = D('ShopGoods');
            foreach ($goods_ids as $goods_id) {
                $goodsDao->clearCache($goods_id);
            }
        }
        
        // 删除分类
        D('Category')->whereIn('id', $ids)->delete();
        return json([
            'code' => 2,
            'msg' => '删除成功'
        ]);
    }
}
