<?php
namespace app\coupon\controller;

use app\common\controller\WebBase;

class Coupon extends WebBase
{

    public function initialize()
    {
        parent::initialize();
        
        $res['title'] = '优惠券';
        $res['url'] = U('Coupon/Coupon/lists');
        $res['class'] = 'current';
        $nav[] = $res;
        
        $this->assign('nav', $nav);
    }

    public function lists()
    {
        $isAjax = I('isAjax');
        $isRadio = I('isRadio');
        
        // 更新延时插入的缓存
        D('common/SnCode')->delayAdd();
        // dump(222222222);
        $dao = D('Coupon');
        // dump(33333);
        $order = 'id desc';
        $model = $this->getModel();
        
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        $row = empty($model['list_row']) ? 20 : $model['list_row'];
        $map['is_del'] = 0;
        // 读取模型数据列表
        $data = $dao->field('id')
            ->where(wp_where($map))
            ->order($order)
            ->paginate($row);
        $list_data = array_merge($list_data, dealPage($data));
        
        $snDao = D('common/SnCode');
        
        $datas = [];
        foreach ($list_data['list_data'] as $d) {
            $coupon = $dao->getInfo($d['id']);
            $useMap['target_id'] = $snMap['target_id'] = $d['id'];
            //$snMap['can_use'] = 1;
            $useMap ['wpid'] = $snMap ['wpid'] = get_wpid ();
            $coupon['collect_count'] = $snDao->where(wp_where($snMap))->count();
            
            $useMap['is_use'] = 1;
            $coupon['use_count'] = $snDao->where(wp_where($useMap))->count();
            
            $datas[] = $coupon;
        }
        
        $list_data['list_data'] = $this->parseListData($datas, $model);
        
        if ($isAjax) {
            $this->assign('isRadio', $isRadio);
            $this->assign($list_data);
            return $this->fetch('ajax_lists_data');
        } else {
            $this->assign($list_data);
            return $this->fetch();
        }
    }

    public function list_data()
    {
        // $page = I ( 'p', 1, 'intval' );
        $map['wpid'] = get_wpid();
        $map['aim_table'] = 'lottery_games';
        $dao = D('Coupon/Coupon');
        $list_data = $dao->where(wp_where($map))
            ->field('id')
            ->order('id DESC')
            ->select();
        
        foreach ($list_data as &$v) {
            $v = $dao->getInfo($v['id']);
            $v['background'] = get_cover_url($v['background']);
        }
        $list_data['list_data'] = $list_data;
        // dump ( $list_data );
        $this->ajaxReturn($list_data, 'JSON');
    }

    public function condition()
    {
        $id = I('id');
        $model = $this->getModel();
        
        if (request()->isPost()) {
            $data = I('post.');
            $goods_category = [];
            if (isset($data['cate_first'])) {
                foreach ($data['cate_first'] as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    
                    $save['category_first'] = intval($val);
                    $save['category_second'] = intval($data['select_cate_second'][$key]);
                    
                    $goods_category[] = $save;
                }
            }
            
            $data['goods_category'] = json_encode($goods_category);
            
            $Model = D($model['name']);
            
            $res = $Model->isUpdate(true)->save($data);
            
            // 清空缓存
            method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
            
            $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
        } else {
            $fields = get_model_attribute($model);
            
            // 获取数据
            $data = D('Coupon')->getInfo($id);
            $data || $this->error('数据不存在！');
            
            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }
            
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model['title'];
            
            $catelists = D('shop/Category')->getCateDatalists();
            $this->assign('cate_data', $catelists);
            
            $list = json_decode($data['goods_category'], true);
            if (! empty($list)) {
                foreach ($list as $k => &$v) {
                    $v['sort'] = $k;
                }
            } else {
                $list = [];
            }
            // dump($list);
            $this->assign('cate_list', $list);
            
            return $this->fetch();
        }
    }

    public function edit()
    {
        $id = I('id');
        $model = $this->getModel();
        if (request()->isPost()) {
            $data = I('post.');
            $this->checkPostData();
            
            $data['wpid'] = get_wpid();
            $this->save_shop($id, input('post.wpid'));
            D('Coupon')->getInfo($id, true);
            // $data['update_time'] = NOW_TIME;
            $Model = D($model['name']);
            
            $data = $this->checkData($data, $model);
            
            $res = $Model->isUpdate(true)->save($data);
            
            // 清空缓存
            method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
            
            $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
        } else {
            $fields = get_model_attribute($model);
            
            // 获取数据
            $data = D('Coupon')->getInfo($id);
            $data || $this->error('数据不存在！');
            
            $wpid = get_wpid();
            if (isset($data['wpid']) && $wpid != $data['wpid']) {
                $this->error('非法访问！');
            }
            
            $maps['coupon_id'] = $id;
            $list = M('stores_link')->where(wp_where($maps))->select();
            $wpids = getSubByKey($list, 'wpid');
            if (! empty($wpids)) {
                $shop_list = M('stores')->whereIn('id', $wpids)->select();
                $shop_list = isset($shop_list) ? $shop_list : [];
                $this->assign('shop_list', $shop_list);
            }
            $data['member'] = explode(',', $data['member']);
            $levelData = $this->get_card_level();
            $this->assign('level', $levelData);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model['title'];
            
            $this->_deal_data();
            
            return $this->fetch();
        }
    }

    public function add()
    {
        $model = $this->getModel();
        $Model = D($model['name']);
        if (request()->isPost()) {
            $this->checkPostData();
            // dump(parse_name ( $model ['name'], 1 ));
            $Model = D($model['name']);
            
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $id = $Model->insertGetId($data);
            if ($id) {
                // $this->save_shop ( $id, input('post.wpid') );
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $levelData = $this->get_card_level();
            $this->assign('level', $levelData);
            // dump($fields);
            $this->assign('fields', $fields);
            $this->_deal_data();
            
            return $this->fetch();
        }
        
        // return $this->fetch();
    }

    public function save_shop($coupon_id, $wpids = [])
    {
        $map['coupon_id'] = $coupon_id;
        M('stores_link')->where(wp_where($map))->delete();
        if (empty($wpids)) {
            return false;
        }
        
        $wpids = array_filter($wpids);
        foreach ($wpids as $id) {
            $map['wpid'] = $id;
            
            M('stores_link')->insert($map);
        }
    }

    // 增加或者编辑时公共部分
    public function _deal_data()
    {
        return false;
        $normal_tips = '插件场景限制参数说明：格式：[插件名:id],如<br/>
                [投票:10]，表示对ID为10的投票投完对能领取<br/>
                [投票:*]，表示只要投过票就可以领取<br/>
                [微调研:15]，表示完成ID为15的调研就能领取<br/>
                [微考试:10]，表示完成ID为10的考试就能领取<br/>';
        $this->assign('normal_tips', $normal_tips);
    }

    public function checkPostData()
    {
        if (! I('post.title')) {
            $this->error('优惠劵标题不能为空');
        }
        /*
         * if (! I ( 'post.shop_name' )) {
         * $this->error ( '商家名称不能为空' );
         * }
         */
        if (I('post.num') <= 0) {
            $this->error('优惠券数量必须大于0');
        }
        if (I('post.max_num') < 0) {
            $this->error('每人最多领取数量不能小于0');
        }
        
        if (strtotime(I('post.start_time')) >= strtotime(I('post.end_time'))) {
            $this->error('领取优惠券开始时间不能大于等于结束时间');
        }
        
        if (! I('post.use_start_time')) {
            $this->error('请选择优惠券使用开始时间');
        } elseif (! I('post.over_time')) {
            $this->error('请选择优惠券使用结束时间');
        } elseif (strtotime(I('post.use_start_time')) > strtotime(I('post.over_time'))) {
            $this->error('优惠券使用开始时间不能大于结束时间');
        } elseif (strtotime(I('post.use_start_time')) < strtotime(I('post.start_time'))) {
            $this->error('使用时间不能早于领取时间');
        } elseif (strtotime(I('post.end_time')) > strtotime(I('post.over_time'))) {
            $this->error('发放结束时间不能大于使用结束领取时间');
        }
        addWeixinLog('88', '888888');
    }

    public function preview()
    {
        // 公众号信息
        $info = $public_info = get_pbid_appinfo();
        
        $id = I('id/d', 0);
//         $url = U('', array(
//             'id' => $id,
//             'publicid' => $info['id']
//         ));
        $url=WAP_URL.'?pbid='.$info['id'].'#/coupon/get/'.$id;
        
        $this->assign('url', $url);
        return $this->fetch('common@base/preview');
    }

    // 获取会员等级
    public function get_card_level()
    {
        if (M('apps')->where('name="card"')->find()) {
            $map['wpid'] = get_wpid();
            $data = M('card_level')->where(wp_where($map))->column('level', 'id');
            return $data;
        }
    }

    public function sncode_lists()
    {
        $id = $hpmap['id'] = I('id/d', 0);
        
        $info = D('Coupon')->getInfo($id);
        
        $list_data["list_grids"] = array(
            "nickname" => array(
                "field" => "nickname",
                "title" => "用户"
            ),
            "content" => array(
                "field" => "content",
                "title" => " 详细信息"
            ),
            "sn" => array(
                "field" => "sn",
                "title" => " SN码"
            ),
            "admin_uid" => array(
                "field" => "admin_uid",
                "title" => "工作人员"
            ),
            "use_time" => array(
                "field" => "use_time",
                "title" => "核销时间"
            )
        );
        foreach ($list_data["list_grids"] as &$g) {
            $g['raw'] = 0;
            $g['come_from'] = 0;
        }
        
        $px = DB_PREFIX;
        
        // 搜索条件
        $where = "is_use=1 AND target_id=" . $id;
        
        $start_time = I('start_time');
        if ($start_time) {
            $where .= " AND s.use_time>" . strtotime($start_time);
            $this->assign('start_time', $start_time);
        }
        
        $end_time = I('end_time');
        if ($end_time) {
            $where .= " AND s.use_time<" . strtotime($end_time);
            $this->assign('end_time', $start_time);
        }
        
        $search_nickname = I('search_nickname');
        if (! empty($search_nickname)) {
            $where .= " AND s.uid IN(" . D('common/User')->searchUser($search_nickname) . ")";
            
            $this->assign('search_nickname', $search_nickname);
        }
        
        // 读取模型数据列表
        $data = D('common/SnCode')->field(true)
            ->where(wp_where($where))
            ->order('use_time DESC')
            ->paginate(20);
        $list_data = $this->parsePageData($data, [], $list_data, false);
        // dump ( $data );
        foreach ($list_data['list_data'] as &$vo) {
            $vo['nickname'] = get_nickname($vo['uid']);
            $vo['use_time'] = time_format($vo['use_time']);
            $vo['admin_uid'] = get_nickname($vo['admin_uid']);
            
            $vo['content'] = '核销优惠券： ' . $info['title'];
        }
        
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch();
    }

    public function export()
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        
        $id = $hpmap['id'] = I('id/d', 0);
        $info = D('Coupon')->getInfo($id);
        
        $dataArr[0] = array(
            0 => "用户",
            1 => " 详细信息",
            2 => " SN码",
            3 => "工作人员",
            4 => "核销时间"
        );
        
        $px = DB_PREFIX;
        
        // 搜索条件
        $where = "is_use=1 AND target_id=" . $id;
        
        $start_time = I('start_time');
        if ($start_time) {
            $where .= " AND s.use_time>" . strtotime($start_time);
        }
        
        $end_time = I('end_time');
        if ($end_time) {
            $where .= " AND s.use_time<" . strtotime($end_time);
        }
        
        $search_nickname = I('search_nickname');
        if (! empty($search_nickname)) {
            $where .= " AND s.uid IN(" . D('common/User')->searchUser($search_nickname) . ")";
        }
        
        // 读取模型数据列表
        $data = D('common/SnCode')->field(true)
            ->where(wp_where($where))
            ->order('use_time DESC')
            ->limit(5000)
            ->select();
        // dump ( $data );
        foreach ($data as $k => $vo) {
            $vo['content'] = '核销优惠券： ' . $info['title'];
            
            $dataArr[$k + 1] = array(
                0 => get_nickname($vo['uid']),
                1 => $vo['content'],
                2 => $vo['sn'],
                3 => get_nickname($vo['admin_uid']),
                4 => time_format($vo['use_time'])
            );
        }
        require_once env('vendor_path') . 'out-csv.php';
        export_csv($dataArr, 'Coupon_' . $id);
        // outExcel ( $dataArr, 'Coupon_' . $id );
    }

    public function del()
    {
        $ids = I('ids');
        $id = I('id');
        if ($id) {
            $map['id'] = $id;
        }
        if ($ids) {
            $map['id'] = array(
                'in',
                $ids
            );
        }
        $save['is_del'] = 1;
        $res = M('coupon')->where(wp_where($map))->update($save);
        if ($res !== false) {
            $this->success('删除成功');
        } else {
            $this->error('请选择要操作的数据');
        }
    }
}
