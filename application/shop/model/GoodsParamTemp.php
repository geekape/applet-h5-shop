<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class GoodsParamTemp extends Base
{

    protected $table = DB_PREFIX . 'goods_param_temp';

    function getParam($id, $goods)
    {
        $info = $this->where('id', $id)->find();
        $param = json_decode($info['param'], true);
        $lists = [];
        if (! empty($param)) {
            foreach ($param as $vo) {
                if ($vo['is_show'] == 0)
                    continue;
                
                $vo['value'] = empty($goods[$vo['name']]) ? '' : $goods[$vo['name']];
                $lists[] = $vo;
            }
        }
        return $lists;
    }
}
