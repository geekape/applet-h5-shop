<?php
namespace app\servicer\controller;

use app\common\controller\WebBase;

class Servicer extends WebBase
{

    var $model;

    function initialize()
    {
        $this->model = $this->getModel('servicer');
        parent::initialize();
        $controller = strtolower(CONTROLLER_NAME);
        
        $res['title'] = '授权列表';
        $res['url'] = U('Servicer/Servicer/lists');
        $res['class'] = ($controller == 'servicer' && ACTION_NAME == "lists") ? 'current' : '';
        $nav[0] = $res;
        if ($controller == 'servicer' && ACTION_NAME == "add") {
            $res['title'] = '添加授权';
            $res['url'] = '#';
            $res['class'] = ($controller == 'servicer' && ACTION_NAME == "add") ? 'current' : '';
            $nav[1] = $res;
        } else if ($controller == 'servicer' && ACTION_NAME == "edit") {
            $res['title'] = '编辑授权';
            $res['url'] = '#';
            $res['class'] = ($controller == 'servicer' && ACTION_NAME == "edit") ? 'current' : '';
            $nav[1] = $res;
        }
        $this->assign('nav', $nav);
    }

    // 通用插件的列表模型
    public function lists()
    {
        $model = $this->model;
        $map['wpid'] = get_wpid();
        session('common_condition', $map);
        
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        
        $key = input('truename');
        $where = '';
        if (! empty($key)) {
            
            $uids = M('user')->where('nickname', 'like', "%{$key}%")->column('uid');
            if (! empty($uids)) {
                unset($map['truename']);
                $where .= '(uid in (' . implode(',', $uids) . ') or truename like "%' . $key . '%")';
            }
        }
        // dump($where);
        // 读取模型数据列表
        $name = parse_name($model['name'], true);
        
        $data = M($name)->field(true)
            ->where(wp_where($map))
            ->where($where)
            ->order('id desc')
            ->select();
        
        /* 查询记录总数 */
        
        $list_data['list_data'] = $this->parseListData($data, $model);
        
        foreach ($list_data['list_data'] as &$vo) {
            $uInfo = getUserInfo($vo['uid']);
            $vo['nickname'] = '<img width="60" src="' . $uInfo['headimgurl'] . '"/><br/>' . $uInfo['nickname'];
            $qrCode = 'http://qr.liantu.com/api.php?text=' . U('Servicer/Wap/do_login', array(
                'id' => $vo['id'],
                'publicid' => $this->mid
            ));
            $vo['uid'] = '<img class="list_img" width="100" style="width:100px" src="' . $qrCode . '"/>';
        }
        
        $this->assign($list_data);
        $templateFile = $this->model['template_list'] ? $this->model['template_list'] : '';
        return $this->fetch($templateFile);
    }

    function set_enable()
    {
        $save['enable'] = 1 - I('enable');
        $map['id'] = I('id');
        $res = M('Servicer')->where(wp_where($map))->update($save);
        
        $this->success('操作成功');
    }

    function check_user($id = 0)
    {
        if (! IS_POST) {
            return false;
        }
        
        $uid = input('uid/d');
        if (empty($uid)) {
            $this->error('请选择用户');
        }
        $dao = M('Servicer')->where('uid', $uid)->where('wpid', get_wpid());
        if ($id > 0) {
            $dao->where('id', '<>', $id);
        }
        $count = $dao->count();
        if ($count > 0) {
            $this->error('该用户已存在，请勿重复增加');
        }
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $this->check_user(input('id'));
        return parent::edit();
    }

    // 通用插件的增加模型
    public function add()
    {
        $this->check_user();
        return parent::add();
    }
}
