<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

namespace app\admin\model;
use app\common\model\Base;

/**
 * 配置模型
 */

class Config extends Base {
	protected $table = DB_PREFIX. 'config';

//    protected $_auto = array(
//        array('name', 'strtoupper', MODEL_BOTH, 'function'),
//        array('create_time', NOW_TIME, MODEL_INSERT),
//        array('update_time', NOW_TIME, MODEL_BOTH),
//        array('status', '1', MODEL_BOTH),
//    );

    /**
     * 获取配置列表
     * @return array 配置数组
     */
    public function lists(){
        $map    = array('status' => 1);
        $data   = $this->where( wp_where($map) )->field('type,name,value')->select();
        
        $config = [];
        if($data){
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     */
    private function parse($type, $value){
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim( $value, ",;
" ));
                if(strpos($value,':')){
                    $value  = [];
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                }else{
                    $value =    $array;
                }
                break;
        }
        return $value;
    }

}
