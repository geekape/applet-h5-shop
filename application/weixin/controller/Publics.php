<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\weixin\controller;

use app\common\controller\WebBase;

/**
 * 公众号管理
 */
class Publics extends WebBase
{

    protected $addon, $model;

    public function initialize()
    {
        parent::initialize();

        $this->assign('check_all', false);
        $this->assign('search_url', U('lists'));

        $this->model = M('model')->getByName('publics');
        $this->assign('model', $this->model);
    }

    function test()
    {
        $sql = "SELECT TABLE_NAME FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA='yi' and COLUMN_NAME='wpid'";
        $list = M()->query($sql);
        foreach ($list as $v) {
            echo 'update ' . $v['TABLE_NAME'] . ' set wpid=1 where wpid=73;' . "<br/>";
        }
    }

    protected function _display()
    {
        $this->view->display(ACTION_NAME);
    }

    public function help()
    {
        if (empty($_GET['public_id'])) {
            $this->error('公众号参数非法');
        }
        return $this->fetch('Index/help');
    }

    /**
     * 显示指定模型列表数据
     */
    public function lists()
    {
        $uid = $this->mid;
        // 获取模型信息
        $model = $this->model;

        // 搜索条件
        $map['uid'] = $uid;

        // 读取模型数据列表
        $name = parse_name($model['name'], true);
        $data = M($name)->field(true)
            ->where(wp_where($map))
            ->select();

        $listArr[0] = $listArr[1] = [];
        foreach ($data as $d) {
            $listArr[$d['app_type']][] = $d;
        }

        $this->assign('list_data', $listArr);
        return $this->fetch();
    }

    public function del()
    {
        $ids = I('ids');
        $model = $this->model;

        if (empty($ids)) {
            $ids = I('id');
        }
        if (empty($ids)) {
            $ids = array_unique((array)I('ids', 0));
        }
        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }

        $Model = M($model['name']);
        if ($Model->whereIn('id', $ids)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function step_0()
    {
        $uid = $this->mid;

        if (config('PUBLIC_BIND') && is_install('PublicBind')) {
            $res = D('public_bind/PublicBind')->bind();
            if (!$res['status']) {
                $this->error($res['msg']);
                exit();
            }
            return redirect($res['jumpURL']);
        }

        $map['id'] = $id = I('id');
        $data = D('common/Publics')->where(wp_where($map))->find();

        $this->assign('id', $id);

        $model = $this->model;
        if (IS_POST) {
            $data = I('post.');
            foreach ($data as &$v) {
                $v = trim($v);
            }

            $data['public_id'] = input('post.public_id');
            $data['uid'] = $uid;

            $Model = D($model['name']);

            if (empty($id)) {
                $data['uid'] = $uid;
                $id = $Model->insertGetId($data);
                if ($id) {
                    D('common/User')->where('uid', $uid)->setField('wpid', $id);
                    // 更新缓存
                    D('common/Publics')->clearCache($id);
                    D('common/User')->clearCache($uid);

                    $url = U('step_1?id=' . $id);

                    $this->success('添加基本信息成功！', $url);
                } else {
                    $this->error($Model->getError());
                }
            } else {
                $data['id'] = $id;
                $url = U('step_1?id=' . $id);

                $data = $this->checkData($data, $model);
                $res = $Model->where('id', $id)->update($data);
                // 更新缓存
                D('common/Publics')->clearCache($id);
                D('common/User')->clearCache($uid);

                if ($res !== false) {
                    $this->success('保存基本信息成功！', $url);
                } elseif ($res === 0) {
                    $this->success(' ', $url);
                } else {
                    $this->error($Model->getError());
                }
            }
        } else {
            if ($data) {
                $data['type'] = intval($data['type']);
            } else {
                $data['type'] = intval($data['type']);
                $data['public_name'] = '';
                $data['public_id'] = '';
                $data['wechat'] = '';
            }
            $this->assign('info', $data);

            return $this->fetch('publics/step_0');
        }
    }

    public function step_1()
    {
        $id = I('id');
        $this->assign('id', $id);

        return $this->fetch('publics/step_1');
    }

    public function step_2()
    {
        $model = $this->model;
        $id = I('id');
        $this->assign('id', $id);

        $data = M($model['name'])->where('id', $id)->find();
        // if (empty($data) || $data['uid'] != $this->mid) {
        // $this->error('非法操作');
        // }

        $uid = $this->mid;
        if (empty($uid)) {
            $this->error('访问地址有误');
        }

        $user = D('common/User')->where('uid', $uid)->find();
        $is_audit = $user['is_audit'];
        $this->assign('is_audit', $is_audit);
        if (IS_POST) {
            $data = I('post.'); // dump($data);exit;
            // 更新缓存
            D('common/Publics')->clearCache($id);

            $data['id'] = $id;
            foreach ($data as &$v) {
                $v = trim($v);
            }
            $Model = D($model['name']);

            $data = $this->checkData($data, $model);
            $res = $Model->save($data, [
                'id' => $id
            ]); // dump($res);
            if ($res !== false) {
                D('common/Publics')->clearCache($data['id']);

                $this->success('保存成功！', U('weixin/publics/lists'));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $data || $this->error('数据不存在！');

            $this->assign('info', $data);

            return $this->fetch('publics/step_2');
        }
    }

    // 等待审核页面
    public function waitAudit()
    {
        $data = D('common/User')->where('uid', $this->mid)->find();
        $is_audit = $data['is_audit'];
        if ($is_audit == 0 && !config('REG_AUDIT')) {
            return $this->fetch('publics/waitAudit');
        } else {
            return redirect(U('home/index/index'));
        }
    }

    // 自动检测
    public function check_res()
    {
        $id = input('id/d');
        $info = M('publics')->where('id', $id)->find();
        $type = $info['type'];
        $arr = array(
            '0' => '普通订阅号',
            '1' => '微信认证订阅号',
            '2' => '普通服务号',
            '3' => '微信认证服务号'
        );
        $this->assign('public_type', $arr[$type]);
        $this->assign('info', $info);

        M('public_check')->where('wpid', $id)->delete();

        // 获取微信权限节点
        $list = M('public_auth')->select();
        foreach ($list as &$vo) {
            $vo['type'] = $vo['type_' . $type];
        }
        $this->assign('list_data', $list);
        // dump ( $list );

        return $this->fetch();
    }

    // 微信支付配置
    function payment_set()
    {
        $id = I('id');
        if (!$id) {
            $id = PBID;
        }
        $data = D('Common/Publics')->getInfo($id);

        $this->assign('id', $id);

        if (IS_POST) {
            $save = input('post.');
            foreach ($save as &$v) {
                $v = trim($v);
            }
            if (empty($save['appid'])) {
                $this->error('110101:APPID不能为空');
            }
            if (empty($save['mch_id'])) {
                $this->error('110102:微信支付商户号不能为空');
            }
            if (empty($save['partner_key'])) {
                $this->error('110103:API密钥不能为空');
            }

            if (!empty($data['appid']) && $save['appid'] != $data['appid']) {
                $this->error('110104:appid与当前账号的appid不一致');
            }

            D('Common/Publics')->updateInfo($id, $save);

            // 更新缓存
            D('Common/User')->clearCache($this->mid);

            $this->success('保存成功！', U('payment_set'));
        } else {
            $data['type'] = intval($data['type']);
            $this->assign('info', $data);

            $normal_tips = '除了配置下面的参数，还需要在微信商户平台配置授权域名，<a href="' . U('payment_help') . '">查看配置教程</a>';
            $this->assign('normal_tips', $normal_tips);

            return $this->fetch();
        }
    }

    // 微信支付配置
    function payment_help()
    {
        return $this->fetch();
    }

    // 上传微信验证文件
    function upload_wxfile()
    {
        if (IS_POST) {
            $data = input('post.');
            if (empty($data['file_id'])) {
                $this->error('请上传文件');
            }
            if (empty($data['wpid'])) {
                $this->error('找不到公众号id');
            }
            $save['check_file'] = $data['file_id'];
            $res = D('common/Publics')->updateInfo($data['wpid'], $save);
            if ($res !== false) {
                $this->success('上传成功');
            } else {
                $this->error('上传失败');
            }
        } else {
            $wpid = I('wpid', 0, 'intval');
            $this->assign('wpid', $wpid);
            $info = D('common/Publics')->getInfoById($wpid);
            $data['file_id'] = isset($info['check_file']) ? $info['check_file'] : '';
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    function check_url()
    {
        $info = parse_url(SITE_URL);
        if (!config('app_debug')) {
            if ($info['scheme'] == 'http') {
                $this->error('110500:小程序需要在https环境下配置');
            }
            if ($info['host'] == 'localhost' || $info['host'] == '127.0.0.1') {
                $this->error('110501:小程序需要有域名的环境下配置');
            }
        }
        $this->assign('host', $info['host']);
    }

    function step_miniapp_0()
    {
        $this->check_url();
        $uid = $this->mid;

//         $map['id'] = $id = get_pbid();
        $map['id'] = $id =input('id');
        $map['app_type'] = 1;
        $data = D('Common/Publics')->where($map)->find();
        if (!empty($data) && $data['uid'] != $uid) {
            $this->error('110022:非法操作');
        }

        $this->assign('id', $id);

        $model = $this->model;
        if (IS_POST) {
            $post = I('post.');
            foreach ($post as &$v) {
                $v = trim($v);
            }
            if (empty($post['public_name'])) {
                $this->error('请先填写小程序名称再提交');
            }
            if (empty($post['public_id'])) {
                $this->error('请先填写原始ID再提交');
            }

            $post['token'] = $post['public_id'];
            $post['uid'] = $uid;
            $post['app_type'] = 1;

            if (empty($id)) {
                $id = D('Common/Publics')->insertGetId($post);
                if ($id) {
                    // 更新缓存
                    D('Common/Publics')->clearCache($id);
                    D('Common/User')->clearCache($uid);

                    $url = U('step_miniapp_1?id=' . $id);

                    $this->success('添加基本信息成功！', $url);
                } else {
                    $this->error('增加小程序失败');
                }
            } else {
                $url = U('step_miniapp_1?id=' . $id);
                $res = D('Common/Publics')->where('id', $id)->update($post);
                // 更新缓存
                D('Common/Publics')->clearCache($id);
                D('Common/User')->clearCache($uid);

                if ($res) {
                    $this->success('保存基本信息成功！', $url);
                } elseif ($res === 0) {
                    $this->success(' ', $url);
                } else {
                    $this->error('保存小程序失败');
                }
            }
        } else {
            $data['type'] = intval($data['type']);
            $this->assign('info', $data);

            return $this->fetch();
        }
    }

    function step_miniapp_1()
    {
        $this->check_url();

        $id = get_pbid();
        $this->assign('id', $id);

        $baseUrl = SITE_URL . '/index.php?pbid=' . $id . '&s=/';
        $this->assign('baseUrl', $baseUrl);

        return $this->fetch();
    }
}
