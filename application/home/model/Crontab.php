<?php
namespace app\home\model;

use app\common\model\Base;


/**
 * 系统定时任务
 *
 * @author 凡星
 */
class Crontab extends Base
{
    // 定时任务入口
    public function run()
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        ignore_user_abort(true);
        
        // 获取当前要执行的任务，一次只执行一个任务
        $task = $this->getTask();
        if (! $task)
            return false;
            
            // 锁定当前任务，防止上一轮没完成下一轮就开始
        $cache_key = 'crontab_lock_' . $task['id'];
        $is_lock = S($cache_key);
        if ($is_lock == 1)
            return false;
        
        S($cache_key, 1, 3600);
        
        // 提前更新任务状态，防止执行任务过程中断导致无法更新
        $save['last_time'] = NOW_TIME;
        if ($task['type'] == 2) { // 每周定时执行
            list ($h, $m) = explode(':', $task['cron_param']);
            $save['next_time'] = strtotime('+1 week', mktime($h, $m, 0, date('m'), date('d'), date('Y')));
        } elseif ($task['type'] == 1) { // 每天定时执行
            list ($h, $m) = explode(':', $task['cron_param']);
            $save['next_time'] = strtotime('+1 day', mktime($h, $m, 0, date('m'), date('d'), date('Y')));
        } else { // 间隔多少秒执行
            $save['next_time'] = NOW_TIME + $task['cron_param'];
        }
        
        $save['sort'] = [
            'exp',
            'sort+1'
        ];
        
        $map['id'] = $task['id'];
        $res = $this->where( wp_where($map) )->update($save);
        if (! $res)
            return false;
            
            // 开始执行任务
        if (substr($task['task'], 0, 4) == 'http') { // 外部URL任务
            $res = wp_file_get_contents($task['task']);
        } else { // 内部任务
            $arr = explode('/', $task['task']);
            $mod = $arr[0] . '/' . $arr[1];
            $act = $arr[2];
            unset($arr[0], $arr[1], $arr[2]);
            
            $param = [];
            $count = count($arr) + 3;
            if ($count > 0) {
                for ($i = 3; $i < $count; $i += 2) {
                    $param[$arr[$i]] = $arr[$i + 1];
                }
            }
            
            $res = D($mod)->$act($param);
        }
        S($cache_key, 0);
        
        return $res;
    }

    private function getTask()
    {
        // 获取任务列表
        $map['status'] = 1;
        $map['next_time'] = [
            '<=',
            NOW_TIME
        ];
        $info = $this->where( wp_where($map) )
            ->order('sort asc,id asc')
            ->find();
        
        // 防止sort值溢出，当值很大时，自动重置为0
        if ($info['sort'] > 10000000) {
            $map2['id'] = [
                'exp',
                '>0'
            ];
            $this->where( wp_where($map2) )->update(array(
                'sort' => 0
            ));
        }
        
        return $info;
    }

    function test()
    {
        echo '每隔1秒执行一次';
    }

    function test1()
    {
        echo 2;
    }

    function test2()
    {
        echo '每隔1秒执行一次22';
    }
}
