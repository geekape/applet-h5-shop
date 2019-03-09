<?php

namespace app\real_prize\controller;

use app\common\controller\WebBase;

class Realprize extends WebBase
{
    var $r_prize = 'real_prize';
    var $p_address = 'prize_address';
    function initialize()
    {
        parent::initialize();
    
        $res ['title'] = '实物奖励';
        $res ['url'] = U('real_prize/RealPrize/lists');
        $res ['class'] = 'current';
        $nav [] = $res;
    
        $this->assign('nav', $nav);
    }
    function lists()
    {
        $model = $this->getModel('real_prize');
        $list_data = $this->_get_model_list($model, 'id desc', true);
        $this->assign($list_data);
        return $this->fetch();
    }
    function edit()
    {
        $id = I('id');
        $model = $this->getModel();
        
        if (request()->isPost()) {
            $this->checkPostData();
            $Model = D($model ['name']);
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $res = $Model->where('id', $id)->update($data);
            if ($res!==false) {
                $this->_saveKeyword($model, $id);
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                D('RealPrize')->getInfo($id, true);
                $this->success('保存' . $model ['title'] . '成功！', U('lists?model=' . $model ['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            // 获取数据
            $data = M($model ['name'])->where('id', $id)->find();
            $data || $this->error('数据不存在！');
            
            $wpid = get_wpid();
            if (isset($data ['wpid']) && $wpid != $data ['wpid']) {
                $this->error('非法访问！');
            }
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model ['title'];
            
            return $this->fetch();
        }
    }
    function checkPostData()
    {
        if (! I('post.prize_title')) {
            $this->error('活动名称不能为空');
        }
        if (! I('post.prize_name')) {
            $this->error('奖品名称不能为空');
        }
        if (! I('post.prize_conditions')) {
            $this->error('活动说明不能为空');
        }
        if (intval(I('post.prize_count')) <= 0) {
            $this->error('奖品个数应大于0');
        }
        if (! I('post.prize_image')) {
            $this->error('请选择奖品图片');
        }
        if (! I('post.use_content')) {
            $this->error('使用说明不能为空');
        }
        if (! I('post.fail_content')) {
            $this->error('领取提示不能为空');
        }
    }
    function add()
    {
        $model = $this->getModel();
        if (request()->isPost()) {
            $this->checkPostData();
            $Model = D($model ['name']);
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $id = $Model->insertGetId($data);
            if ($id) {
                $this->_saveKeyword($model, $id);
                
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                D('RealPrize')->getInfo($id, true);
                $this->success('添加' . $model ['title'] . '成功！', U('lists?model=' . $model ['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $model ['title'];
            
            return $this->fetch();
        }
    }
    function index()
    {
        $id = I('id');
        $param ['prizeid'] = $id;
        $info = get_pbid_appinfo();
        $param ['publicid'] = $info ['id'];
        $data = D('RealPrize')->getInfo($id);
        $this->assign('data', $data);
        // 设置奖品页面领取对应的跳转链接
        $prizetype = isset($data ['prize_type']) ? $data ['prize_type'] :'';
        if ($prizetype == '0') {
            $url = U("RealPrize/RealPrize/save_address", $param);
        } else {
            $url = U("RealPrize/RealPrize/address", $param);
        }
        $this->assign('jumpurl', $url);
        
        // 获取奖品类型名称，方便显示
        $tname = $prizetype == '0' ? '虚拟物品' : '实体物品';
        $this->assign('tname', $tname);
        // 服务号信息
        $service_info = get_pbid_appinfo();
        $this->assign('service_info', $service_info);
        return $this -> fetch();
    }
    function preview()
    {
        $id = I('id/d', 0);
        $url = U('index', array('id'=>$id));
        $this -> assign('url', $url);
        return $this->fetch('common@base/preview');
    }
    function address($prizeid)
    {
        $data = D('RealPrize/RealPrize')->getInfo($prizeid);
        if ($data ['prize_count'] > 0) {
            if (request()->isPost()) {
                $this->save_address($prizeid);
            } else {
                $this->assign('prizeid', $prizeid);
                $url = U("RealPrize/RealPrize/address?prizeid=$prizeid");
                $this->assign('url', $url);
                return $this->fetch('address');
            }
        } else {
            $res ['result'] = "fail";
            $res ['msg'] = "抱歉手太慢，奖品被领取完了";
            $this->assign("res", $res);
            return $this->fetch('result');
        }
    }
    // 增加收货地址
    function save_address($prizeid)
    {
        $uid = get_mid();
        $data = D('real_prize/RealPrize')->getInfo($prizeid);
        // $num = M( 'prize_address' )->where("uid = $uid and prizeid = $prizeid" )->find ();
        $num = D('PrizeAddress')->getAddressInfo($uid, $prizeid);
        $this->assign("data", $data);
        // 判断是否领取
        if (! empty($num)) {
            $res ['result'] = "fail";
            $res ['msg'] = "您已经领取该奖品了,请不要重复领取";
            $this->assign("res", $res);
            return $this->fetch('result');
            exit();
        } else {
            $data = D('real_prize/RealPrize')->getInfo($prizeid);
            if ($data ['prize_count'] > 0) {
                $model = $this->getModel('prize_address');
                // 实体奖品保存收货地址
                if (request()->isPost()) {
                    $Model = D($model ['name']); // dump($model);die();
                                                                                       // 获取模型的字段信息
                      $data = I('post.');
                      $data = $this->checkData($data, $this->model);
                      $id = $Model->insertGetId($data);
                    if ($id) {
                        // 清空缓存
                        method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                        D('PrizeAddress')->getAddressInfo($uid, $prizeid, true);
                        // 减1
                        // M( 'prize_address' )->where("prizeid = $prizeid" )->setDec ( 'prize_count' );
                        D('RealPrize')->updatePrizeCount($prizeid);
                        // 结果
                        $res ['result'] = "success";
                        $res ['msg'] = "恭喜你，领取成功！";
                        $this->assign("res", $res);
                        $this->assign('address', input('post.'));
                        return $this->fetch('result');
                        exit();
                    }
                } else {
                    // 积分保存uid
                    // $data ['address'] = '';
                    // $data ['city'] = '';
                    // $data ['mobile'] = '';
                    // $data ['uid'] = $uid;
                    // $data ['remark'] = '';
                    // $data ['prizeid'] = $prizeid;
                    // $result = M( 'prize_address' )->isUpdate(false)->update( $data );
                    // D('PrizeAddress')->getAddressInfo($uid,$prizeid,true);
                    // 减1
                    // M( 'prize_address' )->where("prizeid = $prizeid" )->setDec ( 'prize_count' );
                    D('RealPrize')->updatePrizeCount($prizeid);
                    // 结果
                    $res ['result'] = "success";
                    $res ['msg'] = "恭喜你，领取成功！";
                    $this->assign("res", $res);
                    return $this->fetch('result');
                    exit();
                }
            } else {
                $res ['result'] = "fail";
                $res ['msg'] = "抱歉手太慢，奖品被领取完了";
                $this->assign("res", $res);
                return $this->fetch('result');
                exit();
            }
        }
        // return $this->fetch();
    }
    // 显示实物奖品对应的收货地址
    function address_lists()
    {
        $nav [0] ['title'] = "实物奖励";
        $nav [0] ['class'] = "";
        $nav [0] ['url'] = U("lists");
        $nav [1] ['title'] = "收货地址";
        $nav [1] ['class'] = "current";
        $this->assign('nav', $nav);
        $model = $this->getModel('prize_address');
        $this->assign('add_button', false);
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        
        // unset ( $list_data ['list_grids'] [2] );
        
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];
        
        // 搜索条件
        $param['target_id']=$map ['prizeid'] = I('target_id');
        //$map ['wpid'] = get_wpid ();
        session('common_condition', $map);

        $search_url=U('address_lists', $param);
        $this->assign('search_url', $search_url);
        
        $map = $this->_search_map($model, $list_data['db_fields']);
        
        $row = empty($model ['list_row']) ? 20 : $model ['list_row'];
        
        empty($fields) || in_array('id', $fields) || array_push($fields, 'id');
        
        $name = parse_name($model ['name'], true);
        $data = M($name)->field(empty($fields) ? true : $fields)->where(wp_where($map))->order('id DESC')->paginate($row);
        $list_data = $this->parsePageData($data, $model, $list_data, false);
        
        // 获取prizeid对应的奖品名称
        $map2 ['id'] = I('target_id');
        $pname = M('real_prize')->where(wp_where($map2))->value('prize_name');
        foreach ($list_data ['list_data'] as &$v) {
            $v ['prizeid'] = $pname;
        }
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch('lists');
    }
    
    function address_edit()
    {
        $id = I('id');
        $model = $this->getModel('prize_address');
        if (request()->isPost()) {
            $Model = D($model ['name']);
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $res = $Model->save($data, ['id' => $id]);
            if ($res!==false) {
                $this->_saveKeyword($model, $id);
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                $this->success('保存' . $model ['title'] . '成功！', U('address_lists?model=' . $model ['name'].'&target_id='.input('post.prizeid')));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            // 获取数据
            $data = M($model ['name'])->where('id', $id)->find();
            $data || $this->error('数据不存在！');
                
            $wpid = get_wpid();
            if (isset($data ['wpid']) && $wpid != $data ['wpid']) {
                $this->error('非法访问！');
            }
            $param['mdm']=input('mdm');
            $postUrl=U('address_edit', $param);
            $this->assign('post_url', $postUrl);
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model ['title'];
            return $this->fetch(SITE_PATH . '/application/common/view/base/edit.html');
        }
    }
    function list_data()
    {
        //$page = I ( 'p', 1, 'intval' );
        $map['wpid']=get_wpid();
        $map['aim_table']='lottery_games';
        $dao=D('RealPrize/RealPrize');
        $list_data =$dao->where(wp_where($map))->field('id')->order('id DESC')->select();
       
        foreach ($list_data as &$v) {
            $v=$dao->getInfo($v['id']);
            $v['background']=get_cover_url($v['prize_image']);
            $v['title']=$v['prize_name'];
            $v['num']=$v['prize_count'];
        }
        $list_data['list_data']=$list_data;
         //dump ( $list_data );
        $this->ajaxReturn($list_data, 'JSON');
    }
}
