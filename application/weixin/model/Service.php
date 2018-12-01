<?php
namespace app\weixin\model;

use app\common\model\ServiceBase;

// 应用对外提供服务的接口
class Service extends ServiceBase
{

    public function payok($res_data)
    {
        // 记录下日志
        add_debug_log($res_data, 'payok');
        
        // 进行具体的业务操作
    }
}
