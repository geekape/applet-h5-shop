<?php
/*
 * 商城接口
 */
namespace app\coupon\controller;

use app\common\controller\ApiBase;

// 自动生成API文档的命令：apidoc -f "Api.php" -i e:/htdocs/weiphp5.0/application/ -o e:/htdocs/weiphp5.0/apidoc/
class Api extends ApiBase
{

    protected $apiModel;

    function initialize()
    {
        parent::initialize();
        $this->apiModel = D('ApiData');
    }
}
