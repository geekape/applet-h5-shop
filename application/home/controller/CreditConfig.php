<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class CreditConfig extends Home
{

    public function initialize()
    {
        parent::initialize();

        $act          = strtolower(CONTROLLER_NAME);
        $nav          = [];
        $res['title'] = '积分配置';
        $res['url']   = U('CreditConfig/lists');
        $res['class'] = $act == 'creditconfig' ? 'current' : '';
        $nav[]        = $res;

        $res['title'] = '积分记录';
        $res['url']   = U('CreditData/lists');
        $res['class'] = $act == 'creditdata' ? 'current' : '';
        $nav[]        = $res;

        $this->assign('nav', $nav);
    }

    public function lists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('search_button', false);
        $this->assign('check_all', false);

        $model = $this->getModel('credit_config');

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        // dump ( $list_data );

        $list_data['list_data'] = D('common/Credit')->getCreditByName();
        $list_data['list_data'] = $this->parseListData($list_data['list_data'], $model);

        $this->assign($list_data);

        return $this->fetch('common@base/lists');
    }

    public function edit($id = 0)
    {
        $model     = $this->getModel('credit_config');
        $id || $id = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        if (request()->isPost()) {
            $data = input('post.');
            $act  = 'update';
            if (!isset($data['wpid'])) {
                $data['wpid'] = get_wpid();
                unset($data['id']);
                $act = 'insert';
            }
            $Model = D($model['name']);
            // 获取模型的字段信息

            $data = $this->checkData($data, $model);
            if ($Model->$act($data)!==false) {
                if ($data['name'] == 'subscribe') {
                    $credit['score'] = I('score');
                    D('common/Credit')->updateSubscribeCredit($data['wpid'], $credit, 0);
                }
                D('common/Credit')->clearCache(0);
                // dump($Model->getLastSql());
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name']));
            } else {
                // dump($Model->getLastSql());
                $this->error($Model->getError());
            }
        } else {
            $fields                    = get_model_attribute ( $model );
            $fields['name']['is_show'] = $fields['title']['is_show'] = 4;

            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model['title'];

            return $this->fetch('common@base/edit');
        }
    }
}
