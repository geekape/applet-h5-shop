<?php

namespace app\weixin\model;
use app\common\model\Base;


/**
 * CustomMenu模型
 */
class CustomMenu extends Base{

    // 获取进行中的活动
    function getListData($addon,$model,$stime_col='',$etime_col='',$wpid_col='',$state_col='',$state_val=1) {
        if ($wpid_col){
            $map [$wpid_col] = get_wpid ();
        }
        if ($stime_col){
            $map[$stime_col]=array('elt',NOW_TIME);
        }
        if ($etime_col){
            $map[$etime_col]=array('gt',NOW_TIME);
        }
        if ($state_col){
            $map[$state_col]=$state_val;
        }
        $data_list = D("$addon/$model")->where ( wp_where( $map ) )->field ( 'id,title' )->order ( 'id desc' )->select ();
        return $data_list;
    }
    
}
