<?php

namespace app\coupon\controller;

use app\common\controller\WebBase;

class Sn extends WebBase
{
    public $table = 'sn_code';
    public $addon = 'Coupon';
    public function initialize()
    {
        parent::initialize();

        $controller = strtolower(CONTROLLER_NAME);

        $res['title'] = '优惠券';
        $res['url']   = U('Coupon/Coupon/lists', $this->get_param);
        $res['class'] = $controller == 'coupon' ? 'current' : '';
        $nav[]        = $res;

        $this->assign('nav', $nav);
    }
    public function lists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('search_button', false);
        $this->assign('check_all', false);

        $top_more_button[] = array(
            'title' => '导出数据',
            'url'   => U('export', array(
                'target_id' => I('target_id'),
            )),
        );
        $this->assign('top_more_button', $top_more_button);

        $model = $this->getModel($this->table);

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        unset($list_data['list_grids'][2]);
        $grids  = $list_data['list_grids'];
        $fields = $list_data['fields'];

        // 搜索条件
        $map['target_id'] = I('target_id');
        $map['wpid']     = get_wpid();
        session('common_condition', $map);
        $map = $this->_search_map($model, $list_data['db_fields']);

        $row = empty($model['list_row']) ? 20 : $model['list_row'];

        empty($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name($model['name'], true);
        $data = M($name)->field(empty($fields) ? true : $fields)->where(wp_where($map))->order('id DESC')->paginate($row);
        unset( $list_data['list_grids']['urls']);
        $list_data = $this->parsePageData($data, $model, $list_data);

        return $this->fetch();
    }
    public function export()
    {
        $model = $this->getModel('sn_code');

        // 搜索条件
        $map['target_id'] = I('target_id');
        $map['wpid']     = get_wpid();
        session('common_condition', $map);

//        return parent::common_export($model);
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        // 获取模型信息
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $grids = $list_data['list_grids'];
        $fields = $list_data['fields'];
        
        foreach ($grids as $k => $v) {
        	if ($v['come_from'] == 1) {
        		array_pop($grids);
        	} else {
        		$ht[$k] = $v['title'];
        	}
        }
        $dataArr[0] = $ht;
        
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        
        $name = parse_name($model['name'], true);
        $data = M($name)->field(empty($fields) ? true : $fields)
        ->where(wp_where($map))
        ->order('id desc')
        ->select();
        
        if ($data) {
        	$dataTable = D('Common/Models')->getFileInfo($model);
        	$data = $this->parseListData($data, $dataTable);
        	foreach ($data as &$vo) {
        		foreach ($ht as $key => $val) {
        			$newArr[$key] = empty($vo[$key]) ? ' ' : $vo[$key].' ';
        		}
        		$vo = $newArr;
        	}
        	
        	$dataArr = array_merge($dataArr, $data);
        }
        
        outExcel($dataArr);
        
    }
    public function del()
    {
        $model = $this->getModel('sn_code');
        return parent::del($model);
    }
    public function set_use()
    {
        $id = I('id');

        $info = D('common/SnCode')->getInfoById($id);
        $res  = D('common/SnCode')->set_use($id);
        if ($res) {
            $map['is_use']     = 1;
            $map['target_id']  = $info['target_id'];
            $save['use_count'] = intval(D('common/SnCode')->where(wp_where($map))->count());
            D('Coupon')->updateInfo($info['target_id'], $save);
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }
}
