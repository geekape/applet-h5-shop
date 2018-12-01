<?php
namespace app\qr_admin\controller;

use app\common\controller\WebBase;

class QrAdmin extends WebBase
{

    public function lists()
    {
        // 获取用户组信息
        $map['pbid'] = get_pbid();
        $groupArr = M( 'auth_group' )->where( wp_where($map) )->column('title', 'id');
        
        // 获取用户标签信息
        $tagArr = M( 'user_tag' )->where( wp_where($map) )->column('title', 'id');
        
        $model = $this->getModel('qr_admin');
        $list_data = $this->_get_model_list($model);
        foreach ($list_data['list_data'] as &$vo) {
            empty($vo['qr_code']) || $vo['qr_code'] = '<img class="list_img" src="' . $vo['qr_code'] . '">';
            $vo['action_name'] = $vo['action_name'] == 'QR_SCENE' ? '临时二维码' : '永久二维码';
            $vo['group_id'] = isset($groupArr[$vo['group_id']]) ? $groupArr[$vo['group_id']] : '';
            
            $tagTitle = [];
            $tag_ids = explode(',', $vo['tag_ids']);
            foreach ($tag_ids as $id) {
                $tagTitle[] = isset($tagArr[$id]) ? $tagArr[$id] : '';
            }
            
            $vo['tag_ids'] = implode(',', $tagTitle);
        }
        $this->assign($list_data);
        // dump ( $list_data );
        $this->assign('search_button', false);
        
        return $this->fetch();
    }

    function add()
    {
        $model = $this->getModel('qr_admin');
        if (IS_POST) {
            
            $Model = D($model['name']);
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $id = $Model->strict(false)->insertGetId($data);
            if ($id) {
                $save['qr_code'] = D('home/QrCode')->add_qr_code(I('action_name'), 'QrAdmin', $id);
                $map['id'] = $id;
                if ($save['qr_code']) {
                    M( 'qr_admin' )->where( wp_where($map) )->update($save);
                } else {
                    M( 'qr_admin' )->where( wp_where($map) )->delete();
                    
                    $msg = '获取二维码失败';
                    if ($save['qr_code'] == - 1) {
                        $msg = '二维码数量已经达到上限，增加失败';
                    } elseif ($save['qr_code'] == - 3) {
                        $msg = '保存二维码失败';
                    }
                    $this->error($msg);
                    exit();
                }
                
                $this->success('添加二维码成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute ( $model );
            $this->assign('fields', $fields);
            
            return $this->fetch();
        }
    }

    function edit()
    {
        $model = $this->getModel('qr_admin');
        $id = I('id');
        // 获取数据
        $data = M( $model['name'] )->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        if (IS_POST) {
            
            $Model = D($model['name']);
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $res = $Model->where('id', $id)
                ->strict(false)
                ->update($data);
            if ($res!==false) {
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute ( $model );
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }
}
