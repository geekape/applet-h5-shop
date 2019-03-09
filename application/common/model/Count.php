<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 计数池
 */
class Count extends Base
{

    protected $table = DB_PREFIX . 'user';

    // 写入缓存
    function set($table, $id, $field, $step = 1)
    {
        $key = 'Comment_Count_set_datas';
        $datas = (array) S($key);
        $index = $table . '|' . $id;
        if (isset($datas[$index][$field])) {
            $datas[$index][$field] += $step;
        } else {
            $datas[$index][$field] = $step;
        }
        S($key, $datas);
        
        // 数据过大时自动写入数据库
        $count = count($datas);
        if ($count > 100) {
            $this->write();
        }
    }

    // 异步写入数据库
    function write()
    {
        $key = 'Comment_Count_set_datas';
        $datas = (array) S($key);
        S($key, null);
        
        $px = DB_PREFIX;
        
        foreach ($datas as $k => $d) {
            list ($table, $id) = explode('|', $k);
            $set = '';
            foreach ($d as $f => $c) {
                $set .= "`$f`=`$f`+$c, ";
            }
            $set = rtrim($set, ', ');
            if (empty($set))
                continue;
            
            $sql = "UPDATE {$px}{$table} SET $set WHERE id=" . $id . ' limit 1';
            M()->execute($sql);
        }
    }
}
?>
