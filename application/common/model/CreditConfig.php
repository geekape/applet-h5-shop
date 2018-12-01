<?php
namespace app\common\model;

use app\common\model\Base;


/**
 * 积分操作
 */
class CreditConfig extends Base
{

    protected $table = DB_PREFIX. 'credit_config';

    function addData($data)
    {
        return $this->insertGetId($data);
    }

    function delData($map)
    {
        return $this->where( wp_where($map) )->delete();
    }
}
?>
