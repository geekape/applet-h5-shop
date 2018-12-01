<?php
namespace app\common\model;

use app\common\model\Base;

class AuthRule extends Base
{

    var $table = DB_PREFIX. 'auth_rule';

    public function getList()
    {
        $auth_rule = S('AuthRule_getList');
        // 从缓存获取不到时从数据库里获取规则
        if (empty($auth_rule)) {
            $list = $this->field('id,name,mod')->select();
            foreach ($list as $vo) {
                $auth_rule[$vo['mod'] . ':' . $vo['name']] = $vo['id'];
            }
            S('auth_rule', $auth_rule, 86400);
        }
        
        return $auth_rule;
    }

    public function getAll($map = [])
    {
        $list = $this->where( wp_where($map) )->select();
        foreach ($list as $v) {
            $lists[] = $v;
        }
        return $lists;
    }

    public function updateRule($rid, $flag, $id = null, $mod = null)
    {
        $map['id'] = $rid;
        
        // 已设置的权限节点
        $rule_ids = M( 'user_tag' )->where( wp_where($map) )->value('rule');
        if (empty($rule_ids)) {
            $rule_ids = [];
        } else {
            $rule_ids = explode(',', $rule_ids);
        }
        
        if ($id == null) { // 批量操作
                           // 模块下系统权限节点
            $mods = $this->where('mod', $mod)->column('id');
        } else { //
            $mods[] = $id;
        }
        $callback = $flag ? 'array_merge' : 'array_diff';
        $data = implode(',', array_unique($callback($rule_ids, $mods)));
        
        return M( 'user_tag' )->where( wp_where($map) )->update(array(
            'rule' => $data
        ));
    }

    public function getByNameMod($name, $mod)
    {
        $info = S('auth_rule_' . $name . '_' . $mod);
        if ($info === false) {
            $info = $this->where([
                'name' => $name,
                'mod' => $mod
            ])->find();

            S('auth_rule_' . $name . '_' . $mod, $info);
        }
        return $info;
    }

    function addData($data)
    {
        return $this->insertGetId($data);
    }

    function delData($map)
    {
        return $this->where( wp_where($map) )->delete();
    }
}
