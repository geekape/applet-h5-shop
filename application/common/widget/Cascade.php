<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 级联菜单插件
 *
 * @author 凡星
 */
class Cascade extends base
{

    public $info = array(
        'name' => 'Cascade',
        'title' => '级联菜单',
        'description' => '支持无级级联菜单（当然也包括常见的一级下拉菜单也可以用此插件来实现），用于地区选择、多层分类选择等场景。菜单的数据来源支持查询数据库和直接用户按格式输入两种方式',
        'status' => 1,
        'author' => '凡星',
        'version' => '0.1',
        'has_adminlist' => 0,
        'type' => 0
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * 编辑器挂载的后台文档模型文章内容钩子
     *
     * type=db&table=common_category&module=shop_category&value_field=id&custom_field=id,title,pid,sort&custom_pid=0
     * type=text&data=[广西[南宁,桂林], 广东[广州, 深圳[福田区, 龙岗区, 宝安区]]]
     */
    public function cascade($data)
    {
        // dump($data);
        $key = $data['name'] . '_' . get_wpid();
        $json = S($key);
        if ($json === false || true) {
            $manager_id = $GLOBALS['uid'];
            $wpid = get_wpid();
            $data['extra'] = str_replace(array(
                '[manager_id]',
                '[wpid]'
            ), array(
                $manager_id,
                $wpid
            ), $data['extra']);
            
            parse_str($data['extra'], $arr);
            ! isset($arr['type']) && $arr['type'] = 'db';
            $first_option = isset($arr['first_option']) ? $arr['first_option'] : '请选择';
            
            if ($arr['type'] == 'db') {
                $table = isset($arr['table']) ? $arr['table'] : 'common_category';
                $value_field = isset($arr['value_field']) ? $arr['value_field'] : 'id';
                $custom_field = isset($arr['custom_field']) ? $arr['custom_field'] : 'id,title,pid,sort';
                $custom_pid = isset($arr['custom_pid']) ? $arr['custom_pid'] : 0;
                
                unset($arr['type'], $arr['table'], $arr['value_field'], $arr['custom_field'], $arr['custom_pid'], $arr['first_option']);
                // dump ( $table );
                
                // dump($arr);exit;
                $list = M($table)->where(wp_where($arr))
                    ->field($custom_field)
                    ->order('pid asc, sort asc')
                    ->select();
                // lastsql ();
                // dump ( $list );
                // exit ();
                $tree = $this->makeTree($list, $custom_pid, $value_field);
            } elseif ($arr['type'] == 'extra') {
                $tree = $this->extra2json($arr['data']);
            } else {
                $tree = $this->str2json($arr['data']);
            }
            // dump($tree);
            
            $a = array(
                'a' => '0',
                't' => $first_option
            );
            array_unshift($tree, $a);
            // dump($tree);
            $json = json_encode($tree);
            
            S($key, $json, 86400);
        }
        // dump($json);
        $this->assign('json', $json);
        
        $data['default_value'] = $data['value'];
        ! isset($data['default_value']) || $data['default_value'] = '"' . str_replace(',', '","', $data['default_value']) . '"';
        // dump($data);exit;
        $this->assign($data);
        
        $content = $this->fetch('common@widget/cascade');
        return $content;
    }

    public function makeTree($list, $pid = 0, $value_field = 'id')
    {
        $result = [];
        foreach ($list as $k => $vo) {
            if ($vo['pid'] == $pid) {
                $data['a'] = $vo[$value_field];
                $data['t'] = $vo['title'];
                unset($list[$k]);
                $d = $this->makeTree($list, $vo['id'], $value_field);
                empty($d) || $data['d'] = $d;
                
                $result[] = $data;
                unset($data);
            }
        }
        return $result;
    }

    // $str = '[1:广西[3:南宁,4:桂林],5:123[6:456,7:789,asd], 2:广东[广州, 深圳[福田区, 龙岗区[板田,龙华], 宝安区]]]';
    public function str2json($str)
    {
        $str = str_replace('，', ',', $str);
        $str = str_replace('【', '[', $str);
        $str = str_replace('】', ']', $str);
        $str = str_replace('：', ':', $str);
        
        $arr = StringToArray($str);
        $str = '';
        foreach ($arr as $v) {
            if ($v == '[' || $v == ']' || $v == ',') {
                if ($str) {
                    $block = explode(':', trim($str));
                    $blockArr['a'] = $block[0];
                    $blockArr['t'] = isset($block[1]) ? $block[1] : $block[0];
                    
                    $arr2[] = $blockArr;
                }
                $v == ',' || $arr2[] = $v;
                $str = '';
            } else {
                $str .= $v;
            }
        }
        if ($arr2[0] == '[') {
            unset($arr2[0]);
            array_pop($arr2);
        }
        // dump ( $arr2 );
        // 通过栈的原理把一维数组转成多维数据
        $wareroom = [];
        foreach ($arr2 as $k => $vo) {
            if ($vo == ']') {
                // 逆向出栈
                $count = count($wareroom) - 1;
                for ($i = $count; $i >= 0; $i --) {
                    if ($wareroom[$i] == '[') {
                        $parent = $i - 1;
                        array_pop($wareroom);
                        break;
                    } else {
                        $d[] = array_pop($wareroom);
                    }
                }
                
                krsort($d);
                $wareroom[$parent]['d'] = $d;
                unset($d);
            } else {
                // 入栈
                array_push($wareroom, $vo);
            }
        }
        // dump ( $wareroom );
        return $wareroom;
    }

    // $str = '[1:广西[3:南宁,4:桂林],5:123[6:456,7:789,asd], 2:广东[广州, 深圳[福田区, 龙岗区[板田,龙华], 宝安区]]]';
    public function extra2json($str)
    {
        $str = str_replace('，', ',', $str);
        $str = str_replace('：', ':', $str);
        
        $arr = wp_explode($str);
        $res = [];
        foreach ($arr as $v) {
            if (false !== strpos($v, ':')) {
                $block = explode(':', trim($str));
                $blockArr['a'] = $block[0];
                $blockArr['t'] = isset($block[1]) ? $block[1] : $block[0];
                
                $res[] = $blockArr;
            } else {
                $res[] = [
                    'a' => $v,
                    't' => $v
                ];
            }
        }
        
        return $res;
    }
}
