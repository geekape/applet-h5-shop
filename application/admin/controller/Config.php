<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 后台配置控制器
 */
class Config extends Admin
{

    function test()
    {
        $dirname = 'E:\htdocs\onlineshop\Addons';
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $path = $dirname . DIRECTORY_SEPARATOR . $entry . '/View/default';
            if (is_dir($path)) {
                rmdir($path);
            }


            //dump(88);
        }
        $dir->close();
    }

    /**
     * 配置管理
     */
    public function index()
    {
        /* 查询条件初始化 */
        $map = [];
        $map = array(
            'status' => 1
        );
        $group = I('group', 0);
        if (!empty($group)) {
            $map['group'] = $group;
        }
        $name = (string)I('name');
        if (!empty($name)) {
            $map['name'] = array(
                'like',
                '%' . $name . '%'
            );
        }

        $list = $this->lists_data('config', $map, 'id');
        // 记录当前列表页的cookie
        empty($forward) && cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('group', config('CONFIG_GROUP_LIST'));
        $this->assign('group_id', I('group', 0));
        $this->assign('list', $list);
        $this->meta_title = '配置管理';
        return $this->fetch();
    }

    /**
     * 新增配置
     */
    public function add()
    {
        if (request()->isPost()) {
            $Config = D('Config');
            $data = input('post.');
            if ($data) {
                if ($Config->insertGetId($data)) {
                    S('DB_CONFIG_DATA', null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info', null);
            return $this->fetch('edit');
        }
    }

    /**
     * 编辑配置
     */
    public function edit()
    {
        $id = I('id/d', 0);
        if (request()->isPost()) {
            $Config = D('Config');
            $data = input('post.');
            if ($data) {
                if ($Config->isUpdate(true)->save($data)) {
                    S('DB_CONFIG_DATA', null);
                    // 记录行为
                    action_log('update_config', 'config', $data['id'], UID);
                    $this->success('更新成功', cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = [];
            /* 获取数据 */
            $info = M('config')->field(true)
                ->where('id', $id)
                ->find();

            if (false === $info) {
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            return $this->fetch();
        }
    }

    /**
     * 批量保存配置
     */
    public function save()
    {
        $config = input('post.');
        if (array_key_exists('IS_QRCODE_LOGIN', $config) && $config['IS_QRCODE_LOGIN'] == 1) { // echo $this->check_qrcode($config);exit;
            if ($this->check_qrcode($config) < 0) {
                $this->error('公众号配置信息错误');
            }
        }
        if ($config && is_array($config)) {
            foreach ($config as $name => $value) {
                $map = array(
                    'name' => $name
                );
                M('config')->where(wp_where($map))->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA', null);
        $this->success('保存成功！');
    }

    /*
     * 测试配置扫码公众号是否正常
     */
    private function check_qrcode($config)
    {
        return $qrcode = D('home/QrCode')->re_init($config['DEFAULT_PUBLICS'])->add_qr_code();
    }

    /**
     * 删除配置
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array(
            array(
                'id',
                'in',
                $id
            )
        );
        if (M('config')->where(wp_where($map))->delete()) {
            S('DB_CONFIG_DATA', null);
            // 记录行为
            action_log('update_config', 'config', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group()
    {
        $id = I('id', 1);
        if ($id == 10) { // 初始化扫码登录配置信息
            $this->public_init($id);
        }
        $type = config('CONFIG_GROUP_LIST');
        $list = M('config')->where(wp_where(array(
            'status' => 1,
            'group' => $id
        )))
            ->field('id,name,title,extra,value,remark,type')
            ->order('sort')
            ->select();
        $button_show = 0;
        if ($list) {
            if ($id == 10 && $list[1]['extra'] == '') {
                unset($list[0]);
                $list[1]['type'] = -1;
                $list[1]['title'] = "请先到运营后台添加认证服务号……(<a target='_blank' href='" . U('weixin/Publics/lists') . "'>去添加</a>)";
                $button_show = 1;
            }
            $this->assign('list', $list);
        }

        $this->assign('button_show', $id);
        $this->assign('id', $id);
        $this->meta_title = $type[$id] . '设置';

        return $this->fetch();
    }

    /*
     * 初始化可提供扫码的公众号信息
     * ouyangessen 20161220
     */
    private function public_init($id)
    {
        // type=3 ,认证的服务号
        $publics = M('publics')->where('type', 3)
            ->order('id asc')
            ->select();

        if (!empty($publics)) {
            $extra = '';
            foreach ($publics as $v) {
                $extra .= $v['id'] . ':' . $v['public_name'] . chr(10);
            }

            $default_value = M('config')->where('name', 'DEFAULT_PUBLICS')->value('value');
            $data['extra'] = $extra;
            $data['value'] = $default_value ? $default_value : '';
        } else {
            $data['extra'] = '';
            $data['value'] = '';
            M('config')->where('name', 'IS_QRCODE_LOGIN')->update(array(
                'value' => '0'
            ));
        }

        M('config')->where('name', 'DEFAULT_PUBLICS')->update($data);
    }

    /**
     * 配置排序
     *
     * @author huajie <banhuajie@163.com>
     */
    public function sort()
    {
        if (IS_GET) {
            $ids = I('ids');

            // 获取排序的数据
            $map = array(
                array(
                    'status',
                    'gt',
                    -1
                )
            );
            if (!empty($ids)) {
                $map[] = array(
                    'id',
                    'in',
                    $ids
                );
            } elseif (I('group')) {
                $map[] = [
                    'group',
                    '=',
                    I('group')
                ];
            }
            $list = M('config')->where(wp_where($map))
                ->field('id,title')
                ->order('sort asc,id asc')
                ->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            return $this->fetch();
        } elseif (request()->isPost()) {
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = M('config')->where('id', $value)->setField('sort', $key + 1);
            }
            if ($res !== false) {
                $this->success('排序成功！', cookie('__forward__'));
            } else {
                $this->error('排序失败！');
            }
        } else {
            $this->error('非法请求！');
        }
    }
}