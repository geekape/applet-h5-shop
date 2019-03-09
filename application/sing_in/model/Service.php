<?php
namespace sing_in\model;
use app\common\model\ServiceBase;

class Service extends ServiceBase
{
	public $info = '';
    function reply($data){
        $act =$data['act'];
        $this->$act($data);

    }

}