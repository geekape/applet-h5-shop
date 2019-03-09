<?php
namespace app\draw\controller;

use app\common\controller\WebBase;

class Award extends WebBase
{

    function initialize()
    {
        parent::initialize();
        
        $controller = strtolower(CONTROLLER_NAME);
        $res['title'] = '抽奖游戏';
        $res['url'] = U('Draw/Games/lists', $this->get_param);
        $res['class'] = $controller == 'games' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '奖品库管理';
        $res['url'] = U('Draw/Award/lists', $this->get_param);
        $res['class'] = $controller == 'award' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '中奖人列表';
        $res['url'] = U('Draw/LuckyFollow/games_lucky_lists', $this->get_param);
        $res['class'] = $controller == 'luckyfollow' ? 'current' : '';
        $nav[] = $res;
        
        $this->assign('nav', $nav);
        if (ACTION_NAME == 'lists') {
            unset($nav);
            
            $type = input('type/d', 0);
            
            $res['title'] = '全部';
            $res['url'] = U('lists', array(
                'type' => 0
            ));
            $res['class'] = $type == 0 ? 'cur' : '';
            $nav[] = $res;
            
            $res['title'] = '实物奖品';
            $res['url'] = U('lists', array(
                'type' => 1
            ));
            $res['class'] = $type == 1 ? 'cur' : '';
            $nav[] = $res;
            
            $res['title'] = '现金红包';
            $res['url'] = U('lists', array(
                'type' => 2
            ));
            $res['class'] = $type == 2 ? 'cur' : '';
            $nav[] = $res;
            
            $res['title'] = '积分';
            $res['url'] = U('lists', array(
                'type' => 3
            ));
            $res['class'] = $type == 3 ? 'cur' : '';
            $nav[] = $res;
            
            $res['title'] = '优惠券';
            $res['url'] = U('lists', array(
                'type' => 5
            ));
            $res['class'] = $type == 5 ? 'cur' : '';
            $nav[] = $res;
            
            $this->assign('sub_nav', $nav);
        }
    }

    // 通用插件的列表模型
    public function lists()
    {
        $page = I('p', 1, 'intval');
        $model = $this->getModel('award');
        $type = input('type');
        if ($type == 1) {
            // 实物奖品
            $map['award_type'] = 1;
        } elseif ($type == 2) {
            // 现金红包
            $map['award_type'] = 4;
        } elseif ($type == 3) {
            // 虚拟奖品
            $map['award_type'] = 0;
        } elseif ($type == 4) {
            // 代金券
            $map['award_type'] = 3;
        } elseif ($type == 5) {
            // 优惠券
            $map['award_type'] = 2;
        }
        // $map['uid']=$this->mid;
        $map['wpid'] = get_wpid();
        $map['aim_table'] = 'lottery_games';
        session('common_condition', $map);
        $list_data = $this->_get_model_list($model, 'id desc', true);
        $dao = D('draw/Award');
        foreach ($list_data['list_data'] as &$vo) {
            $info = $dao->getInfo($vo['id']);
            $vo = array_merge($vo, $info);
            $vo['award_type'] = $vo['type_name'];
            // $vo ['img']=get_img_html($vo['img']);
        }
        // dump($list_data['list_data']);
        $this->assign($list_data);
        
        return $this->fetch();
    }

    function export($model = null)
    {
        is_array($model) || $model = $this->getModel('award');
        return parent::common_export($this->model);
    }

    function add()
    {
        $model = $this->getModel('award');
        if (IS_POST) {
            $data = I('post.');
            $data = $this->checkPostData($data);
            $Model = D($model['name']);
            $data = $this->checkData($data, $model);
            $id = $Model->insertGetId($data);
            if ($id) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            
            return $this->fetch();
        }
    }

    function edit()
    {
        $model = $this->getModel('award');
        $id = I('id');
        
        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        if (IS_POST) {
            $data = I('post.');
            $data = $this->checkPostData($data);
            $Model = D($model['name']);
            $data = $this->checkData($data, $model);
            $res = $Model->allowField(true)->save($data, [
                'id' => $id
            ]);
            if ($res !== false) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            
            return $this->fetch();
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        $ids = I('ids');
        $model = $this->getModel('award');
        return parent::common_del($model, $ids);
    }

    function checkPostData($data)
    {
        $data['aim_table'] = 'lottery_games';
        // if (input('post.count')<0){
        // $this->error ( ' 奖品数量不能低于0' );
        // exit ();
        // }
        if (empty($data['name'])) {
            $this->error('请填写奖项名称');
        }
        if ($data['award_type'] == 0) {
            // 虚拟奖品
            if (! $data['score']) {
                $this->error('请设置奖品积分');
                exit();
            }
            if ($data['score'] < 0) {
                $this->error('设置奖品积分不能小于0');
                exit();
            }
        } else if ($data['award_type'] == 4) {
            // 返现
            if (! $data['money']) {
                $this->error('请填写现金金额');
                exit();
            }
            if ($data['money'] < 1) {
                $this->error('现金不能小于1元');
                exit();
            }
        } else if ($data['award_type'] == 5) {
            if (! $data['card_id']) {
                $this->error('没有选择微信卡券');
                exit();
            }
            $cardV = D('CardVouchers/CardVouchers')->getInfo($data['card_id']);
            $data['name'] = $cardV['title'];
            $data['img'] = $cardV['background'];
        }else if ($data['award_type'] == 2) {
        	if (empty($data['coupon_id'])) {
        		$this->error('没有选择赠送券');
        		exit();
        	}
        }else {
            /*
             * if (! $data ['coupon_id']) {
             * $this->error ( '没有可赠送券' );
             * exit ();
             * }
             */
        }
        return $data;
    }

    function get_coupon()
    {
        $awardType = I('award_type');
        $list = $this->_coupon($awardType);
        $this->ajaxReturn($list);
    }

    function _coupon($awardType = 2)
    {
        $map['end_time'] = array(
            'gt',
            NOW_TIME
        );
        $map['wpid'] = get_wpid();
        $map['is_del'] = 0;
        $list = [];

            // 优惠券
            $list = M('coupon')->where(wp_where($map))
                ->field('id,title')
                ->order('id desc')
                ->select();

        return $list;
    }

    function list_data()
    {
        $page = I('p', 1, 'intval');
        $map['wpid'] = get_wpid();
        $map['aim_table'] = 'lottery_games';
        $dao = D('Draw/Award');
        $page_data = $dao->where(wp_where($map))
            ->field('id')
            ->order('id DESC')
            ->paginate(20);
        $list = dealPage($page_data);
        
        foreach ($list['list_data'] as &$v) {
            $info = $dao->getInfo($v['id']);
            $v = array_merge($v, $info);
        }
        $this->ajaxReturn($list, 'JSON');
    }
}
