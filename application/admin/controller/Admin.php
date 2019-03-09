<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\WebBase;

/**
 * 后台首页控制器
 */
class Admin extends WebBase
{

    /**
     * 后台控制器初始化
     */
    public function initialize()
    {
        parent::initialize();
        
        // 获取当前用户ID
        if (defined('UID')) {
            return;
        }
        
        define('UID', is_login());
        if (! UID) {
            // 还没登录 跳转到登录页面
            $this->redirect('Publics/login');
        }
        if (config('user_administrator') != UID) {
            $this->redirect('Publics/logout');
        }
        
        // 是否是超级管理员
        define('IS_ROOT', is_administrator());
        if (! IS_ROOT && config('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if (! in_array(get_client_ip(), explode(',', config('ADMIN_ALLOW_IP')))) {
                $this->error('403:禁止访问');
            }
        }
        // 检测系统权限
        if (! IS_ROOT) {
            $access = $this->accessControl();
            if (false === $access) {
                $this->error('403:禁止访问');
            } elseif (null === $access) {
                // 检测访问权限
                $rule = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
                
                // 检测分类及内容有关的各项动态权限
                $dynamic = $this->checkDynamic();
                if (false === $dynamic) {
                    $this->error('未授权访问!');
                }
            }
        }
        
        $this->assign('meta_title', '');
    }

    /**
     * 检测是否是需要动态判断的权限
     *
     * @return boolean|null 返回true则表示当前访问有权限
     *         返回false则表示当前访问无权限
     *         返回null，则表示权限不明
     *        
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    protected function checkDynamic()
    {}

    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null 返回值必须使用 `===` 进行判断
     *        
     *         返回 **false**, 不允许任何人访问(超管除外)
     *         返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *         返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function accessControl()
    {
        $allow = config('ALLOW_VISIT');
        $deny = config('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
        if (! empty($deny) && in_array_case($check, $deny)) {
            return false; // 非超管禁止访问deny中的方法
        }
        if (! empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null; // 需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model
     *            模型名称,供M函数使用的参数
     * @param array $data
     *            修改的数据
     * @param array $where
     *            查询时的where()方法的参数
     * @param array $msg
     *            执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *            url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *            
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    final protected function editRow($model, $data, $where, $msg)
    {
        $id = array_unique((array) I('id', 0));
        $id = is_array($id) ? implode(',', $id) : $id;
        // 如存在id字段，则加入该条件
        $fields = []; // M( $model )->getDbFields();
        if (in_array('id', $fields) && ! empty($id)) {
            $where = array_merge(array(
                array(
                    'id',
                    'in',
                    $id
                )
            ), (array) $where);
        }
        
        $msg = array_merge(array(
            'success' => '操作成功！',
            'error' => '操作失败！',
            'url' => '',
            'ajax' => request()->isAjax()
        ), (array) $msg);
        if (M($model)->where(wp_where($where))->update($data) !== false) {
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        } else {
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }

    /**
     * 禁用条目
     *
     * @param string $model
     *            模型名称,供D函数使用的参数
     * @param array $where
     *            查询时的 where()方法的参数
     * @param array $msg
     *            执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *            url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *            
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    protected function forbid($model, $where = [], $msg = array('success' => '状态禁用成功！', 'error' => '状态禁用失败！'))
    {
        $data = array(
            'status' => 0
        );
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 恢复条目
     *
     * @param string $model
     *            模型名称,供D函数使用的参数
     * @param array $where
     *            查询时的where()方法的参数
     * @param array $msg
     *            执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *            url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *            
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    protected function resume($model, $where = [], $msg = array('success' => '状态恢复成功！', 'error' => '状态恢复失败！'))
    {
        $data = array(
            'status' => 1
        );
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 还原条目
     *
     * @param string $model
     *            模型名称,供D函数使用的参数
     * @param array $where
     *            查询时的where()方法的参数
     * @param array $msg
     *            执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *            url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author huajie <banhuajie@163.com>
     */
    protected function restore($model, $where = [], $msg = array('success' => '状态还原成功！', 'error' => '状态还原失败！'))
    {
        $data = array(
            'status' => 1
        );
        $where = array_merge(array(
            'status' => - 1
        ), $where);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 条目假删除
     *
     * @param string $model
     *            模型名称,供D函数使用的参数
     * @param array $where
     *            查询时的where()方法的参数
     * @param array $msg
     *            执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *            url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *            
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    protected function delete($model, $where = [], $msg = array('success' => '删除成功！', 'error' => '删除失败！'))
    {
        $data['status'] = - 1;
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model = CONTROLLER_NAME)
    {
        $ids = I('ids');
        $status = I('status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        
        $map[] = array(
            'id',
            'in',
            $ids
        );
        switch ($status) {
            case - 1:
                $this->delete($Model, $map, array(
                    'success' => '删除成功',
                    'error' => '删除失败'
                ));
                break;
            case 0:
                $this->forbid($Model, $map, array(
                    'success' => '禁用成功',
                    'error' => '禁用失败'
                ));
                break;
            case 1:
                $this->resume($Model, $map, array(
                    'success' => '启用成功',
                    'error' => '启用失败'
                ));
                break;
            default:
                $this->error('参数错误');
                break;
        }
    }



    /**
     * 返回后台节点数据
     *
     * @param boolean $tree
     *            是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     *            @retrun array
     *            
     *            注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *            
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true)
    {
        return [];
    }

    /**
     * 通用分页列表数据集获取方法
     *
     * 可以通过url参数传递where条件,例如: index.html?name=asdfasdfasdfddds
     * 可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     * 可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model $model
     *            模型名或模型实例
     * @param array $where
     *            where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order
     *            排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *            请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *            否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *            
     * @param boolean $field
     *            单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *        
     * @return array|false 返回数据集
     */
    protected function lists_data($model, $where = [], $order = '', $field = true)
    {
        $options = [];
        $REQUEST = (array) I('request.');
        if (is_string($model)) {
            $model = M($model);
        }
        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);
        $pk = $model->getPk();
        if ($order === null) {
            // order置空
        } elseif (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array(
            'desc',
            'asc'
        ))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && ! empty($pk)) {
            if (is_array($pk)) {
                $pkstr = '';
                foreach ($pk as $p) {
                    $pkstr .= $p . ' desc,';
                }
                $options['order'] = substr($pkstr, 0, strlen($pkstr) - 1);
            } else {
                $options['order'] = $pk . ' desc';
            }
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);
        
        $options['where'] = $where;
        $options = array_merge((array) $OPT->getValue($model), $options);
        
        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = config('LIST_ROWS') > 0 ? config('LIST_ROWS') : 20;
        }
        $list = $model->where(wp_where($options['where']))
            ->order($options['order'])
            ->strict(false)
            ->paginate($listRows);
        
        $this->assign('_page', $list->render());
        
        $list = $list->toArray();
        $this->assign('_total', $list['total']);
        
        return $list['data'];
    }

    /**
     * 处理文档列表显示
     *
     * @param array $list
     *            列表数据
     * @param integer $model_id
     *            模型id
     */
    protected function parseDocumentList($list, $model_id = null)
    {
        $model_id = $model_id ? $model_id : 1;
        $model = $this->getModel($model_id);
        $attrList = get_model_attribute($model);
        // 对列表数据进行显示处理
        if (is_array($list)) {
            foreach ($list as $k => $data) {
                foreach ($data as $key => $val) {
                    if (isset($attrList[$key])) {
                        $extra = $attrList[$key]['extra'];
                        $type = $attrList[$key]['type'];
                        if ('select' == $type || 'checkbox' == $type || 'dynamic_checkbox' == $type || 'radio' == $type || 'bool' == $type) {
                            // 枚举/多选/单选/布尔型
                            $options = parse_field_attr($extra);
                            if ($options && array_key_exists($val, $options)) {
                                $data[$key] = $options[$val];
                            }
                        } elseif ('date' == $type) {
                            // 日期型
                            $data[$key] = date('Y-m-d', $val);
                        } elseif ('datetime' == $type) {
                            // 时间型
                            $data[$key] = date('Y-m-d H:i', $val);
                        }
                    }
                }
                $data['model_id'] = $model_id;
                $list[$k] = $data;
            }
        }
        return $list;
    }
}
