<?php
namespace app\wei_site\controller;

use app\wei_site\controller\Base;

class Category extends Base
{

    var $model;

    function initialize()
    {
        $this->model = $this->getModel('weisite_category');
        parent::initialize();
    }

    // 通用插件的列表模型
    public function lists()
    {
        // 使用提示
        $normal_tips = '外链为空时默认跳转到该分类的文章列表页面';
        $this->assign('normal_tips', $normal_tips);
        
        $this->assign('no_slideshow', 0); 
        
        $map['wpid'] = get_wpid();
        // session ( 'common_condition', $map );
        // $list_data = $this->_get_model_list ( $this->model );
        $list_data = $this->_list_grid($this->model);
        $fields = $list_data['fields'];
        // $map = $this->_search_map ( $this->model, $list_data['db_fields'] );
        $key = $this->model['search_key'] ? $this->model['search_key'] : 'title';
        $keyArr = explode(':', $key);
        $key = $keyArr[0];
        $placeholder = isset($keyArr[1]) ? $keyArr[1] : '请输入关键字';
        $this->assign('placeholder', $placeholder);
        $this->assign('search_key', $key);
        $this->assign('search_url', U('lists', array(
            'mdm' => input('mdm')
        )));
		$_REQUEST=input('param.');
        if (isset($_REQUEST[$key]) && ! isset($map[$key])) {
            $map[$key] = array(
                'like',
                '%' . htmlspecialchars($_REQUEST[$key]) . '%'
            );
            unset($_REQUEST[$key]);
        }
        $list_data['list_data'] = M('weisite_category')->where(wp_where($map))
            ->order('id desc')
            ->select();
        $list_data['list_data'] = $this->parseListData($list_data['list_data'], $this->model);
        $list_data['list_data'] = $this->get_data($list_data['list_data']);
        // dump($list_data ['list_data']);
        // dump($list_data['list_data']);
        // $model=$this->model;
        // dump($model);
        foreach ($list_data['list_data'] as $v) {
            $fcate[$v['id']] = $v['title'];
        }
        foreach ($list_data['list_data'] as $key => &$data) {
            // $param['model']=$model['id'];
            // $param['mdm']=input('mdm');
            // $param['id']=$data['id'];
            // $eurl=U('edit',$param);
            // $durl=U('del',$param);
            // $data['ids']="<a target='_self' href=$eurl>编辑</a>&nbsp;";
            // $data['ids'].="<a target='_self' href=$durl>删除</a>&nbsp;";
            if ($data['pid']) {
                $data['pid'] = $fcate[$data['pid']];
            } else {
                $data['pid'] = '';
            }
        }
        unset($list_data['_page']);
        // dump($list_data);
        $this->assign($list_data);
        
        return $this->fetch();
    }

    function get_data($list)
    {
        
        // 取一级菜单
		$one_arr = $data = [];
        foreach ($list as $k => $vo) {
            // dump($vo);
            if ($vo['pid'] != 0)
                continue;
            
            $one_arr[$vo['id']] = $vo;
            unset($list[$k]);
        }
        // dump($one_arr);
        foreach ($one_arr as $p) {
            $data[] = $p;
            
            $two_arr = [];
            foreach ($list as $key => $l) {
                if ($l['pid'] != $p['id'])
                    continue;
                
                $l['title'] = '├──' . $l['title'];
                $two_arr[] = $l;
                unset($list[$key]);
            }
            // dump($two_arr);
            $data = array_merge($data, $two_arr);
        }
        // dump($data);exit;
        return $data;
    }

    public function edit()
    {
        $model = $this->model;
        $Model = D($model['name']);
        $id = I('id');
        
        if (IS_POST) {
			$data = I('post.');
            if ($data['pid'] == $id) {
                $data['pid'] = 0;
            }
            
            $data = $this->checkData($data, $model);
            $res = $Model->save($data, [
                'id' => $id
            ]);
            if ($res!==false) {
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 获取一级菜单
            $map['wpid'] = get_wpid();
            $map['pid'] = 0;
            $map['id'] = array(
                'not in',
                $id
            );
            $list = $Model->where(wp_where($map))->select();
            $extra = '';
            foreach ($list as $v) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }
            
            $fields = get_model_attribute ( $model );
            if (! empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        $vo['extra'] .= $extra;
                    }
                }
            }
            // 获取数据
            $data = M($model['name'])->where('id', $id)->find();
            $data || $this->error('数据不存在！');
            
            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }
            
            isset($data['template']) || $data['template'] = 'color_v1';
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            $tmpImg = __ROOT__ . '/wei_site/template_subcate/' . $data['template'] . '/icon.png';
            $this->assign('tmp_img', $tmpImg);
            // dump($fields);
            $this->meta_title = '编辑' . $model['title'];
            
            return $this->fetch();
        }
    }

    public function add()
    {
        $model = $this->model;
        $Model = D($model['name']);
        
        if (IS_POST) {
            $data = I('post.');
            $data = $this->checkData($data, $this->model);
            $id = $Model->insertGetId($data);
            if ($id) {
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 要先填写appid
            $map['wpid'] = get_wpid();
            
            // 获取一级菜单
            $map['pid'] = 0;
            $list = $Model->where(wp_where($map))->select();
            $extra = '';
            foreach ($list as $v) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }
            
            $fields = get_model_attribute ( $model );
            if (! empty($extra)) {
                foreach ($fields as &$vo) {
                    if ($vo['name'] == 'pid') {
                        $vo['extra'] .= $extra;
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
        return parent::common_del($this->model);
    }

    // 首页
    function index()
    {
        return $this->fetch();
    }

    // 分类列表
    function category()
    {
        return $this->fetch();
    }

    // 相册模式
    function picList()
    {
        return $this->fetch();
    }

    // 详情
    function detail()
    {
        return $this->fetch();
    }
}
