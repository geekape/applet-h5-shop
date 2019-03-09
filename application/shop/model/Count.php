<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * count模型
 */
class Count extends Base
{

    protected $table = DB_PREFIX . 'goods_count';

    /*
     * 分条件获取统计信息
     */
    public function getCharts($param)
    {
        $listAll = M('shop_track')->where('wpid', WPID)
        ->whereBetween('create_at', $param['stime'] . ',' . $param['etime'])
            ->field("FROM_UNIXTIME(create_at,'%Y%m%d') as date,count(1) as num")
            ->group('date')
            ->select();
        
        $re = [];
        foreach ($listAll as $v) {
            $re[$v['date']] = $v['num'];
        }
        // dump($re);
        $xtime = $param['stime'];
        while ($xtime <= $param['etime']) {
            $key_time = time_format($xtime, 'Ymd'); // echo $key_time.'/';
            $res[] = isset($re[$key_time]) && $re[$key_time] > 0 ? $re[$key_time] : 0;
            $xtime = $xtime + 86400;
        } // dump($res);exit;
        return $res;
    }

    /*
     * 添加统计信息
     */
    public function count($id)
    {
        $check_map = array(
            'uid' => intval(session('mid_'.get_pbid())),
            'day_time' => time_format(NOW_TIME, 'Ymd'),
            'good_id' => $id
        );
        if (! $this->where(wp_where($check_map))->value('id')) {
            $check_map['time'] = NOW_TIME;
            $this->insert($check_map);
        }
    }
}
