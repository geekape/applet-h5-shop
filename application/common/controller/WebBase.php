<?php

// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\common\controller\Base;

/**
 * PC运营管理端的控制器基类，实现PC版的初始化，权限控制和一些通用方法
 *
 * @author 凡星
 *
 */
class WebBase extends Base
{

    protected $top_more_button = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize()
    {
        if (strtolower(MODULE_NAME) == 'install') {
            return false;
        }
        parent::initialize();
        
        $not_need_wpid = [
            'public_bind',
            'home',
            'admin'
        ];
        
        if (! in_array(MODULE_NAME, $not_need_wpid) && strtolower(CONTROLLER_NAME) != 'publics' && strtolower(CONTROLLER_NAME) != 'adminmaterial' && strtolower(MODULE_NAME) != 'scene' && strtolower(MODULE_NAME . '/' . CONTROLLER_NAME) != 'weixin/notice' && (! defined('WPID') || WPID <= 0)) {
            $this->error('关键参数缺失');
        }
        
        $index_3 = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
        if ($index_3 == 'weixin/index/index') {
            return false;
        }
        
        // 微信客户端请求的用户初始化在weixin/index/index里实现，这里不作处理
        $this->initUser();
        
        $this->initWeb();
        
        $this->_nav();
    }

    // 初始化用户信息
    private function initUser()
    {
        $uid = intval(session('mid_' . get_pbid()));
        $loginUid = is_login();
        if (empty($uid) && $loginUid > 0) {
            $uid = $loginUid;
            session('mid_' . get_pbid(), $loginUid);
        }
        
        // 当前登录者
        $GLOBALS['mid'] = $this->mid = $uid;
        $myinfo = get_userinfo($this->mid);
        $GLOBALS['myinfo'] = $myinfo;
        
        // 当前访问对象的uid
        $cuid = input('uid');
        $GLOBALS['uid'] = $this->uid = $cuid > 0 ? $cuid : $this->mid;
        
        $this->assign('mid', $this->mid); // 登录者
        $this->assign('uid', $this->uid); // 访问对象
        $this->assign('myinfo', $GLOBALS['myinfo']); // 访问对象
    }

    /**
     * 系统管理员信息初始化
     *
     * @access private
     * @return void
     */
    private function initWeb()
    {
        if (ACTION_NAME == 'logout') {
            return false;
        }
        // 通用表单的控制开关
        $this->assign('add_button', true);
        $this->assign('del_button', true);
        $this->assign('search_button', true);
        $this->assign('check_all', true);
        $this->assign('top_more_button', $this->top_more_button);
        
        $model_name = parse_name(MODULE_NAME);
        $controller_name = parse_name(CONTROLLER_NAME);
        $action_name = parse_name(ACTION_NAME);
        $index_1 = $model_name . '/*/*';
        $index_2 = $model_name . '/' . $controller_name . '/*';
        $index_3 = $model_name . '/' . $controller_name . '/' . $action_name;
        
        // 当前用户信息
        $access = array_map('trim', explode("\n", config('ACCESS')));
        $access = array_map('strtolower', $access);
        $access = array_flip($access);
        
        $guest_login = isset($access[$index_1]) || isset($access[$index_2]) || isset($access[$index_3]) || $index_1 == 'admin/*/*' || $index_3 == 'home/application/execute' || $index_2 == 'home/user/*';
        
        if (IS_GET && ! is_login() && ! $guest_login) {
            $forward = cookie('__forward__');
            empty($forward) && cookie('__forward__', $_SERVER['REQUEST_URI']);
            
            return $this->redirect(U('home/user/login', array(
                'from' => 6
            )));
        }
        
        /* 管理中心的导航 */
        if (IS_GET) {
            $menus = D('common/Menu')->getMenu();
            $this->assign('top_menu', $menus);
            $this->assign('now_top_menu_name', $menus['now_top_menu_name']);
        }
        
        $pbid = defined('PBID') ? PBID : 0;
        if (empty($pbid)) {
            return false;
        }
        
        // 公众号
        $info = D('common/Publics')->getFieldByInfo($pbid);
        if (empty($info) ) {
        	if ($index_3 != 'weixin/publics/lists' && $index_3 !='home/user/login'){
        		return $this->error('公众号不存在或已删除',U('weixin/publics/lists'));
        	}else{
        		return false;
        	}
        }
        $status = getUserInfo($info['uid'], 'status');
        if ($status == 0 && ACTION_NAME != 'logout' && ACTION_NAME != 'login') {
            $this->error('您好，该账号已到期', U('home/user/logout'));
        }
        
        
        if ($info['type'] < 4) {
            // 公众号接口权限
            $config = S('PUBLIC_AUTH_' . $info['type']);
            if (! $config) {
                $config = M('public_auth')->column('name,type_' . intval($info['type']) . ' as val');
                
                S('PUBLIC_AUTH_' . $info['type'], $config, 86400);
            }
            if (is_array($config)) {
                foreach ($config as $c => $v) {
                    config($c, $v); // 公众号接口权限
                }
            }
        }
    }

    // ***************************通用的模型数据操作 begin 凡星********************************/
    // 通用的保存关键词方法
    public function _saveKeyword($model, $id)
    {
        if (input('?post.keyword') && $model['name'] != 'keyword' && defined('MODULE_NAME') && ! isset($_REQUEST['keyword_no_deal'])) {
            $keyword_type = input('?post.keyword_type') ? input('post.keyword_type') : 0;
            D('common/Keyword')->set(input('post.keyword'), MODULE_NAME, $id, $keyword_type);
        }
    }

    /*
     * 导出数据
     */
    public function outExcel($dataArr, $fileName = '', $sheet = false)
    {
        require_once env('root_path') . 'vendor/' . 'download-xlsx.php';
        export_csv($dataArr, $fileName, $sheet);
        unset($sheet);
        unset($dataArr);
    }

    public function inExcel()
    {
        require_once env('root_path') . 'vendor/' . '/PHPExcel.php';
        require_once env('root_path') . 'vendor/' . 'PHPExcel/IOFactory.php';
        require_once env('root_path') . 'vendor/' . 'PHPExcel/Reader/Excel5.php';
    }

    public function _nav()
    {
        $addon = D('home/Addons')->getInfoByName(MODULE_NAME);
        $nav = [];
        if (! empty($addon) && $addon['name'] != 'weixin') {
            if ($addon['has_adminlist']) {
                $res['title'] = $addon['title'];
                $res['url'] = U('lists');
                $res['class'] = ACTION_NAME == 'lists' ? 'current' : '';
                $nav[] = $res;
            }
            if (file_exists(env('app_path') . MODULE_NAME . '/config.php')) {
                $res['title'] = '功能配置';
                $res['url'] = U('config');
                $res['class'] = ACTION_NAME == 'config' ? 'current' : '';
                $nav[] = $res;
            }
            if (empty($nav) && ACTION_NAME != 'nulldeal') {
                U('nulldeal', '', true);
            }
        }
        $this->assign('nav', $nav);
        
        return $nav;
    }

    // 通用插件的列表模型
    public function lists()
    {
        $model = I('model');
        $model = $this->getModel($model);
        $templateFile = $this->getAddonTemplate($model['template_list']);
        
        return $this->common_lists($model, $templateFile);
    }

    public function export()
    {
        $model = I('model');
        $model = $this->getModel($model);
        $this->common_export($model);
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $model = I('model');
        $id = I('id');
        $model = $this->getModel($model);
        $templateFile = $this->getAddonTemplate($model['template_edit']);
        return $this->common_edit($model, $id, $templateFile);
    }

    // 通用插件的增加模型
    public function add()
    {
        $model = I('model');
        $model = $this->getModel($model);
        $templateFile = $this->getAddonTemplate($model['template_add']);
        
        return $this->common_add($model, $templateFile);
    }

    // 通用插件的删除模型
    public function del()
    {
        $model = I('model');
        $ids = I('ids');
        return $this->common_del($model, $ids);
    }

    // 通用设置插件模型
    public function config()
    {
        $this->getModel();
        $map['name'] = parse_name(MODULE_NAME, 0);
        $controller = parse_name(CONTROLLER_NAME, 0);
        
        $pkey = $map['name'] . '_' . $controller;
        
        $addon = [];
        
        $file = env('app_path') . $map['name'] . '/config.php';
        
        $re_arr = include $file;
        
        $addon = isset($re_arr[$controller]) ? $re_arr[$controller] : $re_arr;
        
        if (request()->isPost()) {
            $data = input('post.');
            foreach ($addon as $k => $vv) {
                if ($vv['type'] == 'material') {
                    $type_name = input('post.material_' . $k . '_type');
                    $final_value = $type_name . ':' . input('post.material_' . $k . '_' . $type_name . '_id');
                    
                    $data[$k] = $final_value;
                }
            }
            // dump(input('post.'));exit;
            $flag = D('common/PublicConfig')->setConfig($pkey, $data);
            
            if ($flag !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        
        $db_config = D('common/PublicConfig')->getConfig($map['name'], $pkey);
        // dump($db_config);
        if (! empty($db_config)) {
            foreach ($addon as $key => $value) {
                ! isset($db_config[$key]) || $addon[$key]['value'] = $db_config[$key];
            }
        }
        // dump($addon);
        $this->assign('fields', $addon);
        
        $data = [];
        foreach ($addon as $key => $value) {
            $data[$key] = $value['value'];
        }
        $this->assign('data', $data);
        
        return $this->fetch();
    }

    // 没有管理页面和配置页面的插件的通用提示页面
    public function nulldeal()
    {
        return $this->fetch('common@base/nulldeal');
    }

    function scene_res($obj, $success = true, $code = 200, $msg = '', $map = null, $list = null)
    {
        $data['obj'] = $obj;
        $data['success'] = $success;
        $data['code'] = $code;
        $data['msg'] = $msg;
        $data['map'] = $map;
        $data['list'] = $list;
        return json($data);
    }

    function scene_json_res($data)
    {
        if (! is_array($data)) {
            $data = json_decode($data, true);
        }
        return json($data);
    }

    function scene_check_loin()
    {
        if (intval(session('mid_' . get_pbid())) == 0) {
            header('Content-type: text/json');
            header('HTTP/1.1 401 error');
            echo json_encode(array(
                "success" => false,
                "code" => 1001,
                "msg" => "请先登录!",
                "obj" => null,
                "map" => null,
                "list" => null
            ));
            exit();
        }
    }
}
