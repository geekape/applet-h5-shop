<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\common\controller;

use think\Controller;
use think\Validate;
use think\facade\Log;

/**
 * WeiPHP的系统的核心基类，包括手机版和PC版，主要实现系统级别的初始化和一些通用方法
 *
 * @author Administrator
 *
 */
class Base extends Controller
{

    protected $mid = 0;

    protected $uid = 0;

    protected $user = [];

    protected $get_param = [];

    public function __construct()
    {
        // WeiPHP常量定义
        defined('MODULE_NAME') or define('MODULE_NAME', request()->module());
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', request()->controller());
        defined('ACTION_NAME') or define('ACTION_NAME', request()->action(true));

        defined('ADDON_PUBLIC_PATH') or define('ADDON_PUBLIC_PATH', __ROOT__ . '/' . MODULE_NAME . '');

        defined('NOW_TIME') or define('NOW_TIME', $_SERVER['REQUEST_TIME']);
        defined('IS_GET') or define('IS_GET', request()->isGet());
        defined('IS_POST') or define('IS_POST', request()->isPost());
        defined('IS_AJAX') or define('IS_AJAX', request()->isAjax());
        defined('__SELF__') or define('__SELF__', strip_tags($_SERVER['REQUEST_URI']));

        $requestData = input();
        $requestData = empty($requestData) ? [] : $requestData;
        $_REQUEST = array_merge($_REQUEST, $requestData);
//         add_debug_log($_REQUEST, 'testvistisf_'.get_mid());
        if (isset($_REQUEST['PHPSESSID']) && !empty($_REQUEST['PHPSESSID'])) {
            session_id($_REQUEST['PHPSESSID']);
        }
//         add_debug_log($_SESSION, 'testvistisf11_'.get_mid());

        // 不用记录定时任务的日志
        if (ACTION_NAME != 'cron' && CONTROLLER_NAME != 'Canal') {
            Log::key('allow_log');
        } else {
            Log::key('fobi_log');
        }
        // 解决TP框架中的GET不包含PHP_INFO里的参数的问题
        $route = input('route.');
        $_GET = array_merge($route, $_GET);

        $pbid = input('pbid/d', 0);
        if ($pbid > 0) {
            session('pbid', $pbid);
        }
        if ($pbid == 0) {
            $pbid = intval(session('pbid'));
        }
        if ($pbid == 0 && DEFAULT_PBID != '-1') {
            $pbid = DEFAULT_PBID;
        }
        if ($pbid == 0 && strtolower(MODULE_NAME) != 'install') {
            // 在单账号系统中，没有指定公众号的情况下取第一个
            $pbid = D('common/Publics')->value('id');
        }

        $wpid = session('wpid_' . $pbid);
        if (!$wpid && strtolower(MODULE_NAME) != 'install') {
            if ($pbid > 0) {
                $wpid = D('common/Publics')->getInfoById($pbid, 'wpid');
            } else {
                $wpid = session('mid_' . get_pbid()); // 管理后台的wpid直接取mid，后续可优化
            }
            session('wpid_' . $pbid, $wpid);
        }

        if (!$wpid && DEFAULT_WPID != '-1') {
            $wpid = DEFAULT_WPID;
        }
        if (!defined('PBID')) { //&& strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME) != 'home/weixin/index'
            defined('PBID') || define('PBID', $pbid);
            defined('WPID') || define('WPID', $wpid);
        }

        $public_dir = parse_name(MODULE_NAME);
        if (!is_dir(SITE_PATH . '/public/' . $public_dir)) {
            $public_dir = 'home';
        }

        config('template.tpl_replace_string', array(
            '__STATIC__' => __ROOT__ . '/static',
            '_MODULE_NAME__' => __ROOT__ . '/' . $public_dir . '/addons',
            '__IMG__' => __ROOT__ . '/' . $public_dir . '/images',
            '__CSS__' => __ROOT__ . '/' . $public_dir . '/css',
            '__JS__' => __ROOT__ . '/' . $public_dir . '/js',
            '__ROOT__' => __ROOT__, // 当前网站地址
            '__SELF__' => __SELF__, // 当前页面地址
            '__PUBLIC__' => __ROOT__,
            '__PUBLICTHIS__' => __ROOT__ . '/' . $public_dir
        )); // 站点公共目录

        parent::__construct();
    }

    function initialize()
    {
        parent::initialize();

        if (strtolower(MODULE_NAME) != 'install') {
            $this->initSite();
        }
    }

    /**
     * 应用信息初始化
     *
     * @access private
     * @return void
     */
    private function initSite()
    {
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        foreach ($config as $c => $v) {
            config($c, $v); // 添加全站配置
        }

        if (!config('WEB_SITE_CLOSE') && strtolower(MODULE_NAME) != 'admin') {
            $this->error('站点已经关闭，请稍后访问~');
        }

        $diff = array(
            '_addons' => 1,
            '_controller' => 1,
            '_action' => 1,
            'm' => 1,
            'id' => 1
        );

        $GLOBALS['get_param'] = $this->get_param = array_diff_key($_GET, $diff);
        if (isset($this->get_param['page'])) {
            unset($this->get_param['page']);
        }
        $this->assign('get_param', $this->get_param);

        // js,css的版本
        if (config('app_debug')) {
            defined('SITE_VERSION') or define('SITE_VERSION', time());
        } else {
            defined('SITE_VERSION') or define('SITE_VERSION', config('SYSTEM_UPDATRE_VERSION'));
        }
        // 公众号信息
        $info = $public_info = get_pbid_appinfo();
        $this->assign('public_info', $public_info);

        $page_title = isset($info['public_name']) ? $info['public_name'] : $config['WEB_SITE_TITLE'];
        $this->assign('page_title', $page_title);

        // 设置版权信息
        $this->assign('system_copy_right', config('COPYRIGHT'));

        $tongji_code = '';
        $this->assign('tongji_code', $tongji_code);

        if (MODULE_NAME == 'scene') {
            error_reporting(E_ERROR | E_PARSE);
        }
    }

    // ***************************通用的模型数据操作 begin 凡星********************************/
    public function getModel($model = null)
    {
        $model || $model = MODULE_NAME;
        $model || $model = input('model');
        $model || $this->error('模型名标识必须！');
        if (is_numeric($model)) {
            $model = M('model')->where('id', $model)->find();
        } else {
            $model = getModelByName($model);
        }
        $this->assign('model', $model);
        return $model;
    }

    /**
     * 显示指定模型列表数据
     *
     * @param String $model
     *            模型标识
     * @author 凡星
     */
    public function common_lists($model = null, $templateFile = '', $order = 'id desc')
    {
        // 获取模型信息
        is_array($model) || $model = $this->getModel($model);
        $list_data = $this->_get_model_list($model, $order);
        $this->assign($list_data);

        empty($templateFile) && $templateFile = 'lists';

        return $this->fetch($templateFile);
    }

    // 只返回列表数据，方便作后续业务处理
    public function common_lists_data($model = null, $templateFile = '', $order = 'id desc')
    {
        // 获取模型信息
        is_array($model) || $model = $this->getModel($model);
        $list_data = $this->_get_model_list($model, $order);

        return $list_data;
    }

    public function common_export($model = null, $order = 'id desc', $return = false)
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        // 获取模型信息
        is_array($model) || $model = $this->getModel($model);
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
            ->order($order)
            ->select();

        if ($data) {
            $dataTable = D('Common/Models')->getFileInfo($model);
            $data = $this->parseListData($data, $dataTable);
            foreach ($data as &$vo) {
                foreach ($ht as $key => $val) {
                    $newArr[$key] = empty($vo[$key]) ? ' ' : $vo[$key];
                }
                $vo = $newArr;
            }

            $dataArr = array_merge($dataArr, $data);
        }

        if ($return) {
            return $dataArr;
        } else {
            outExcel($dataArr);
        }
    }

    public function common_del($model = null, $ids = null)
    {
        is_array($model) || $model = $this->getModel($model);

        !empty($ids) || $ids = I('id');
        !empty($ids) || $ids = array_filter(array_unique((array)I('ids', 0)));
        !empty($ids) || $this->error('请选择要操作的数据!');

        try {
        	$Model = D($model['name']);
        }catch (\Exception $e) {
        	if (strpos($e->getMessage(), 'not exists')){
        		$Model = M($model['name']);
        	}else {
        		$this->error('找不到操作模型');
        	}
        }
        $map[] = array(
            'id',
            'in',
            $ids
        );

        // 插件里的操作自动加上Token限制
        $dataTable = D('Common/Models')->getFileInfo($model);
        $wpid = get_wpid();
        if (!empty($wpid) && isset($dataTable->fields['wpid'])) {
            $map[] = [
                'wpid',
                '=',
                $wpid
            ];
        }

        if ($Model->where(wp_where($map))->delete()) {
            // 清空缓存
            method_exists($Model, 'clearCache') && $Model->clearCache($ids, 'del');

            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function common_edit($model = null, $id = 0, $templateFile = '', $post_data = [])
    {
        is_array($model) || $model = $this->getModel($model);
        $id || $id = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        $wpid = get_wpid();
        if (isset($data['wpid']) && $wpid != $data['wpid']) {
            $this->error('非法访问！');
        }

        if (request()->isPost()) {
            try {
            	$Model = D($model['name']);
            }catch (\Exception $e) {
            	if (strpos($e->getMessage(), 'not exists')){
            		$Model = M($model['name']);
            	}else {
            		$this->error('找不到操作模型');
            	}
            }
            // 获取模型的字段信息
            $data = empty($post_data) ? input('post.') : $post_data;
            $data = $this->checkData($data, $model);
//             $res = $Model->isUpdate(true)->save($data);
            $res = $Model->where('id',$id)->update($data);
            if ($res !== false) {
                $this->_saveKeyword($model, $id);

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

            empty($templateFile) && $templateFile = 'edit';

            return $this->fetch($templateFile);
        }
    }

    public function common_add($model = null, $templateFile = '', $post_data = [])
    {
        is_array($model) || $model = $this->getModel($model);
        if (request()->isPost()) {
            try {
            	$Model = D($model['name']);
            }catch (\Exception $e) {
            	if (strpos($e->getMessage(), 'not exists')){
            		$Model = M($model['name']);
            	}else {
            		$this->error('找不到操作模型');
            	}
            }
            // 获取模型的字段信息
            $data = empty($post_data) ? input('post.') : $post_data;
            $data = $this->checkData($data, $model);
            // dump($data);exit;
            $id = $Model->insertGetId($data);
            if ($id) {
                $this->_saveKeyword($model, $id);

                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');

                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            // dump($fields);
            empty($templateFile) && $templateFile = 'add';

            return $this->fetch($templateFile);
        }
    }

    // 判断奖品库选择器 数量是否大于库存
    public function checkPriceNum($prizeValue)
    {
        $data = [];
        $prizeData = explode(',', $prizeValue);
        foreach ($prizeData as $key => $value) {
            $keyArr = explode(':', $value);
            if (empty($keyArr[0])) {
                continue;
            }

            $total_count = 0;
            $num = $keyArr[2];
            $title = '';
            if ($keyArr[0] == 'coupon') {
                $pdata = D('Coupon/Coupon')->getInfo($keyArr[1]);
                $title = $pdata['title'];
                $total_count = $pdata['num'];
            } elseif ($keyArr[0] == 'realPrize') {
                $pdata = D('RealPrize/RealPrize')->getInfo($keyArr[1]);
                $total_count = $pdata['prize_count'];
                $title = $pdata['prize_name'];
            } elseif ($keyArr[0] == 'cardVouchers') {
                // 无库存，不判断
                $title = $pdata['title'];
                if (intval($num) <= 0) {
                    $this->error('奖品 “' . $title . '” 的数量不能小于0');
                }
                continue;
            } elseif ($keyArr[0] == 'redBag') {
                $pdata = D('Redbag/Redbag')->getInfo($keyArr[1]);
                $title = $pdata['act_name'];
                $total_count = $pdata['total_num'];
            } elseif ($keyArr[0] == 'points') {
                // 判断数量是否小于0
                $title = '积分';
                $num = $keyArr[3];
                if (intval($num) <= 0) {
                    $this->error('奖品 “' . $title . '” 的数量不能小于0');
                }
                continue;
            }
            if (intval($num) <= 0) {
                $this->error('奖品 “' . $title . '” 的数量不能小于0');
            }
            if ($num > $total_count) {
                $this->error('奖品 “' . $title . '” 的数量不能大于库存数量');
            }
        }
    }

    protected function checkData($data, $model = [])
    {
        $fields = get_model_attribute($model);
        $fields = isset($fields) ? $fields : [];
        $rules = $message = [];
        foreach ($fields as $key => $attr) {
            if ($attr['type'] == 'prize' && input('post.' . $key)) {
                // 判断奖品库选择器 数量是否大于库存
                $this->checkPriceNum(input('post.' . $key));
            }
            if ($attr['is_must']) {
                // 必填字段
                $rules[$attr['name']] = 'require';
                $message[$attr['name'] . '.require'] = $attr['title'] . '必须!';
            }
            // 自动验证规则
            if (!empty($attr['validate_rule']) || $attr['validate_type'] == 'unique') {
                switch ($attr['validate_type']) {
                    case 'function': // 函数验证
                        $rules[$attr['name']] = 'filter' . $attr['validate_rule'];
                        $message[$attr['name'] . '.filter'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'unique': // 唯一验证
                        $rules[$attr['name']] = 'unique:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.unique'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'length': // 长度验证
                        $rules[$attr['name']] = 'length:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.length'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'in': // 验证在范围内
                        $rules[$attr['name']] = 'in:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.in'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'notin': // 验证不在范围内
                        $rules[$attr['name']] = 'notIn:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.notIn'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'between': // 区间验证
                        $rules[$attr['name']] = 'between:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.between'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                        break;
                    case 'notbetween': // 不在区间验证
                        $rules[$attr['name']] = 'notBetween:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.notBetween'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                    default: // 正则验证
                        $rules[$attr['name']] = 'regex:' . $attr['validate_rule'];
                        $message[$attr['name'] . '.regex'] = $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误';
                }
            }

            // 自动完成规则
            if (!empty($attr['auto_rule'])) {
                $check_model = check_model($model['name']);
                $pk = $check_model ? D($model['name'])->getPk() : 'id';
                empty($pk) && $pk = 'id';
                $type = isset($data[$pk]) && !empty($data[$pk]) ? MODEL_UPDATE : MODEL_INSERT;

                if ($attr['auto_time'] == $type || $attr['auto_time'] == MODEL_BOTH) {
                    switch ($attr['auto_type']) {
                        case 'function':
                            $fun = $attr['auto_rule'];
                            $param = [];
                            if (strpos($attr['auto_rule'], '|') !== false) {
                                list ($fun, $param) = explode('|', $attr['auto_rule']);

                                if (strpos($param, ',') !== false) {
                                    $param = explode(',', $param);
                                }
                            }
                            $data[$attr['name']] = call_user_func_array($fun, $param);
                            break;
                        case 'field':
                            $data[$attr['name']] = "`{$attr['auto_rule']}`";
                            break;
                        case 'string':
                            $data[$attr['name']] = $attr['auto_rule'];
                            break;
                    }
                }
            } elseif ('checkbox' == $attr['type'] || 'dynamic_checkbox' == $attr['type']) {
                // 多选型
                if ( isset($data[$attr['name']]) ){
                	$data[$attr['name']] = arr2str($data[$attr['name']]);
                }
            } elseif ('datetime' == $attr['type'] || 'date' == $attr['type']) {
                // 时间或者日期型
                if ( isset($data[$attr['name']]) && !empty($data[$attr['name']]) ){
                	//没有的不设置为空，以免覆盖新增保存的数据
                	$data[$attr['name']] =strtotime($data[$attr['name']]) ;
                }
                
            } elseif ('mult_picture' == $attr['type']) {
                // 多图
                if (isset($data[$attr['name']]) ){
                	$data[$attr['name']] =$data[$attr['name']];
                	if (is_array($data[$attr['name']])) {
                		$data[$attr['name']] = arr2str($data[$attr['name']]);
                	} else {
                		$data[$attr['name']] = $data[$attr['name']];
                	}
                }
            }
        }
        if (!empty($rules)) {
            $validate = Validate::make($rules, $message);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
        }
        if (isset($data['start_time']) && isset($data['end_time'])) {
       		if ($data['end_time'] <= $data['start_time']) {
       			$this->error('结束时间不能早于开始时间');
       		}
        }
        return $data;
    }

    protected function parseDataByField($val, $field)
    {
        if (empty($field)) {
            return $val;
        }

        switch ($field['type']) {
            case 'date':
                $val = day_format($val);
                break;
            case 'datetime':
                $val = time_format($val);
                break;
            case 'bool':
            case 'select':
            case 'radio':
                if (!empty($field['extra'])) {
                    $extra = parse_config_attr($field['extra']);
                    $val = isset($extra[$val]) ? $extra[$val] : $val;
                }
                break;
            case 'checkbox':
                if (!empty($field['extra'])) {
                    $extra = parse_config_attr($field['extra']);

                    $valArr = explode(',', $val);
                    foreach ($valArr as $v) {
                        $res[] = isset($extra[$v]) ? $extra[$v] : $v;
                    }

                    $val = implode(', ', $res);
                }

                break;
            case 'picture':
                $val = get_img_html($val);
                break;
            case 'file':
                $val = get_file_html($val);
                break;
            case 'cascade':
                break;
            case 'mult_picture':
                break;
            case 'dynamic_select':
                parse_str($field['extra'], $arr);
                $table = !empty($arr['table']) ? $arr['table'] : 'common_category'; // 表名
                $value_field = !empty($arr['value_field']) ? $arr['value_field'] : 'id'; // 值对应的字段名
                $title_field = !empty($arr['title_field']) ? $arr['title_field'] : 'title'; // 显示的内容
                $map[$value_field] = $val;
                // 查询对应选中值对应的显示内容
                $val = M($table)->where($map)->value($title_field);
                break;
            case 'dynamic_checkbox':
                break;
            case 'material':
                break;
            case 'prize':
                break;
            case 'news':
                break;
            case 'image':
                break;
            case 'user':
                $val = get_nickname($val);
                break;
            case 'users':
                $valArr = explode(',', $val);
                foreach ($valArr as $v) {
                    $res[] = get_nickname($v);
                }

                $val = implode(', ', $res);
                break;
            case 'admin':
                $val = get_nickname($val);
                break;
        }

        return $val;
    }

    protected function parsePageData($data, $model, $list_data = [], $assign = true)
    {
        $data = dealPage($data);
        if (!empty($list_data)) {
            $list_data = array_merge($list_data, $data);
        } else {
            $list_data = $data;
        }

        if (!empty($model)) {
            $list_data['list_data'] = $this->parseListData($list_data['list_data'], $model);
        }
        if ($assign) {
            $this->assign($list_data);
        }

        return $list_data;
    }

    protected function parseListData($datas, $mutl)
    {
        if (empty($datas) || empty($mutl)) {
            return [];
        }
        if (is_array($mutl)) {
            $dataTable = D('common/Models')->getFileInfo($mutl);
        } else {
            $dataTable = $mutl;
        }

        if (empty($dataTable)) {
            return $datas;
        }

        $fields = $dataTable->fields;
        $grid = $dataTable->list_grid;
        $model = $dataTable->config;
        foreach ($datas as $key => &$data) {
            if (empty($data)) {
                unset($datas[$key]);
                continue;
            }

            if (gettype($data) == 'object') {
                $data_arr = $data->toArray();
            } else {
                $data_arr = $data;
            }

            $original_data = array_merge($_REQUEST, $data_arr);

            foreach ($grid as $name => $g) {
                $val = $db_val = isset($data[$name]) ? $data[$name] : '';
                $field = isset($fields[$name]) ? $fields[$name] : '';

                if (isset($g['href']) && !empty($g['href'])) {
                    // 链接支持
                    $valArr = [];
                    foreach ($g['href'] as $link) {
                        $href = $link['url'];

                        $show = $link['title'];
                        if (strpos($show, ':') !== false) {
                            // 支持标题随状态变化，设置格式：is_show:0|上架,1|下架
                            list ($show_filed, $show) = explode(':', $show, 2);
                            $show_val = $original_data[$show_filed];
                            $showArr = explode(',', $show);
                            foreach ($showArr as $arr) {
                                list ($v, $t) = explode('|', $arr);
                                if ($v == $show_val) {
                                    $show = $t;
                                    break;
                                }
                            }
                        }
                        // 增加跳转方式处理 weiphp
                        $target = '_self';
                        if (preg_match('/target=(\w+)/', $href, $matches)) {
                            $target = $matches[1];
                            $href = str_replace('&' . $matches[0], '', $href);
                        }

                        // 替换系统特殊字符串
                        $href = str_replace(array(
                            '[DELETE]',
                            '[EDIT]',
                            '[MODEL]',
                            '[WAP_URL]'
                        ), array(
                            'del?id=[id]&model=[MODEL]',
                            'edit?id=[id]&model=[MODEL]',
                            $model['name'],
                            SITE_URL . '/wap/index.html?pbid=' . get_pbid()
                        ), $href);

                        // 替换数据变量
                        $href = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($original_data) {
                            return isset($original_data[$match[1]]) ? $original_data[$match[1]] : '';
                        }, $href);

                        // 兼容多种写法
                        if (strpos($href, '?') === false && strpos($href, '&') !== false) {
                            $href = preg_replace("/&/i", "?", $href, 1);
                        }

                        if ($show == '删除') {
                            $valArr[] = '<a class="tr-del" data-url="' . urldecode(U($href, $GLOBALS['get_param'])) . '">' . $show . '</a>';
                        } elseif ($show == '复制链接') {
                            $paramArrs = $GLOBALS['get_param'];
                            unset($paramArrs['mdm']);
                            if (!strpos($href, '#')){
                            	$href = U($href);
                            }
                            $valArr[] = '<a class="list_copy_link" id="copyLink_' . $original_data['id'] . '"   data-clipboard-text="' . urldecode($href) . '">' . $show . '</a>';
                        } elseif (!empty($show)) {
                            // 排除GET里的参数影响到已赋值的参数
                            $url_param = array();
                            foreach ($GLOBALS['get_param'] as $key => $gp) {
                                if (strpos($href, $key . '=') === false && $key != 'p') {
                                    $url_param[$key] = $gp;
                                }
                            }
                            if (isset($link['class'])) {
                                $valArr[] = '<a  class="' . $link['class'] . '" target="' . $target . '" href="' . urldecode(U($href, $url_param)) . '">' . $show . '</a>';
                            } else {
                                $valArr[] = '<a  target="' . $target . '" href="' . urldecode(U($href, $url_param)) . '">' . $show . '</a>';
                            }
                        }
                    }
                    $val = implode(' ', $valArr);
                } elseif (!empty($g['function']) && $g['function'] != '') {
                    $val = call_user_func($g['function'], $val);
                    $db_val != $val && $data[$name . '_db'] = $db_val;
                } else {
                    // get_name_by_status方法不再用，下面按字段类型自动做数据转换，不再需要人工转换
                    $val = $this->parseDataByField($val, $field);
                    $db_val != $val && $data[$name . '_db'] = $db_val;
                }

                $data[$name] = $val;
            }
        }

        return $datas;
    }

    // 获取模型列表数据
    public function _get_model_list($model = null, $order = 'id desc', $all_field = false)
    {
        if (empty($model)) {
            $this->error('请先增加数据模型再试');
        }

        $dataTable = D('common/Models')->getFileInfo($model);
        if ($dataTable === false) {
            $this->error($model['name'] . ' 的模型文件不存在');
        }

        $this->assign('add_button', $dataTable->config['add_button']);
        $this->assign('del_button', $dataTable->config['del_button']);
        $this->assign('search_button', $dataTable->config['search_button']);
        $this->assign('check_all', $dataTable->config['check_all']);

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $fields = $list_data['fields'];

        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        $row = empty($model['list_row']) ? 20 : $model['list_row'];

        // 读取模型数据列表
        if (empty($order) && isset($_REQUEST['order'])) {
            $order = I('order') . ' ' . I('by');
        }
        if ($model['name'] != 'user') {
            empty($fields) || in_array('id', $fields) || array_push($fields, 'id');
            empty($order) && $order = 'id desc';
        } else {
            empty($fields) || in_array('uid', $fields) || array_push($fields, 'uid');
            empty($order) && $order = 'uid desc';
        }
        // dump ( $order );
        $name = $dataTable->config['name'];
        $db_field = true;
        if (!$all_field && !empty($fields)) {
            $db_field = $fields;
        }

        $wp_where = wp_where($map);
        $data = M($name)->field($db_field)
            ->where($wp_where)
            ->order($order)
            ->paginate($row);

        $list = $this->parsePageData($data, $dataTable, $list_data);
        return $list;
    }

    // 解析列表规则
    public function _list_grid($model)
    {

        // 过滤重复字段信息
        $obj = D('common/Models')->getFileInfo($model);

        $fields = array_keys($obj->list_grid);
        $model_fields = array_keys($obj->fields);

        in_array('id', $model_fields) || array_push($model_fields, 'id');
        $fields = array_intersect($fields, $model_fields);

        $res['fields'] = array_unique($fields);
        $res['list_grids'] = $obj->list_grid;
        $res['db_fields'] = $model_fields;
        return $res;
    }

    public function _search_map($model, $fields = [])
    {
        $map = [];
        empty($fields) && $fields = [];

        // 插件里的操作自动加上Token限制
        $wpid = get_wpid();
        if (!isset($map['wpid']) && !empty($wpid) && in_array('wpid', $fields)) {
            $map['wpid'] = $wpid;
        }

        // 自定义的条件搜索
        $conditon = session('common_condition');
        if (!empty($conditon)) {
            $map = array_merge($map, $conditon);
        }
        session('common_condition', null);

        // 关键字搜索
        $key = $model['search_key'] ? $model['search_key'] : 'title';
        $keyArr = explode(':', $key);
        $key = $keyArr[0];
        $placeholder = isset($keyArr[1]) ? $keyArr[1] : '请输入关键字';
        $this->assign('placeholder', $placeholder);
        $this->assign('search_key', $key);

        // 条件搜索
        $data = input('param.');
        if (isset($data[$key]) && !isset($map[$key]) && in_array($key, $fields)) {
            $map[$key] = array(
                'like',
                '%' . htmlspecialchars($data[$key]) . '%'
            );

            // unset($_REQUEST[$key]);
        }

        foreach ($data as $name => $val) {
            if (!is_numeric($name) && !isset($map[$name]) && in_array($name, $fields)) {
                $map[$name] = $val;
            }
        }
        unset($data[$key]);

        return $map;
    }

    /**
     * 重写模板显示 调用内置的模板引擎显示方法，
     *
     * @access protected
     * @param string $template
     *            模板文件名
     * @param array $vars
     *            模板输出变量
     * @param array $replace
     *            模板替换
     * @param array $config
     *            模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        $template = $this->getAddonTemplate($template);

        $config['tpl_replace_string'] = config('template.tpl_replace_string');
        return parent::fetch($template, $vars, $config);
    }

    public function getAddonTemplate($templateFile = '')
    {
        if (file_exists($templateFile)) {
            return $templateFile;
        }

        if (empty($templateFile)) {
            $path = env('app_path') . parse_name(MODULE_NAME) . '/view/' . parse_name(CONTROLLER_NAME) . '/' . ACTION_NAME . '.html';
            $new_path = env('app_path') . '/common/view/base/' . ACTION_NAME . '.html';
            if (file_exists($path)) {
                $templateFile = $path;
            } elseif (file_exists($new_path)) {
                $templateFile = 'common@base/' . ACTION_NAME;
            }
        } elseif (stripos($templateFile, '@') === false && stripos($templateFile, '/') === false) {
            // 如index
            $path = env('app_path') . parse_name(MODULE_NAME) . '/view/' . parse_name(CONTROLLER_NAME) . '/' . $templateFile . '.html';
            $new_path = env('app_path') . '/common/view/base/' . $templateFile . '.html';
            if (!file_exists($path) && file_exists($new_path)) {
                $templateFile = 'common@base/' . $templateFile;
            }
        }
        return $templateFile;
    }

    // 重置error方法，主要是设置第二个参数,防止在填提交表单有报错时跳转回上一页
    public function error($msg = '', $url = '', $data = '', $wait = 3, array $header = [])
    {
        return parent::error($msg, $url, $data, $wait, $header);
    }

    // 与post_data函数相比，多了错误判断，省得在业务里重复判断
    public function post_data($url, $param, $type = 'json', $return_array = true, $useCert = [])
    {
        $res = post_data($url, $param, $type, $return_array, $useCert);

        // 各种常见错误判断
        if (isset($res['curl_erron'])) {
            $this->error($res['curl_erron'] . ': ' . $res['curl_error']);
        }
        if ($return_array) {
            if (isset($res['errcode']) && $res['errcode'] != 0) {
                $this->error(error_msg($res));
            } elseif (isset($res['return_code']) && $res['return_code'] == 'FAIL' && isset($res['return_msg'])) {
                $this->error($res['return_msg']);
            } elseif (isset($res['result_code']) && $res['result_code'] == 'FAIL' && isset($res['err_code']) && isset($res['err_code_des'])) {
                $this->error($res['err_code'] . ': ' . $res['err_code_des']);
            }
        }
        return $res;
    }

    /**
     * Ajax方式返回数据到客户端，兼容3.0的方法，仅支持JSON
     *
     * @access protected
     * @param mixed $data
     *            要返回的数据
     * @param String $type
     *            AJAX返回数据格式
     * @param int $json_option
     *            传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data, $type = 'JSON', $json_option = 0)
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data, $json_option));
    }
}
