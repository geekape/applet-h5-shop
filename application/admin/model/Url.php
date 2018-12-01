<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace app\admin\model;
use app\common\model\Base;


/**
 * URL模型
 * @author huajie <banhuajie@163.com>
 */

class Url extends Base {

    /* 自动验证规则 */
//    protected $_validate = array(
//        array('url', 'url', 'URL格式不正确', self::MUST_VALIDATE, 'regex', MODEL_BOTH),
//        array('short', 'url', 'URL格式不正确', self::VALUE_VALIDATE, 'regex', MODEL_BOTH),
//    );
//
//    /* 自动完成规则 */
//    protected $_auto = array(
//        array('status', 1, MODEL_INSERT, 'string'),
//        array('create_time', 'time', MODEL_BOTH, 'function'),
//    );

    /**
     * 新增或更新一个URL
     * @return boolean fasle 失败 ， 成功 返回完整的数据
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfo($data){
        /* 获取数据对象 */
        $data = empty($data) ? input('post.') : $data;
        if(empty($data)){
            return false;
        }

        /* 如果链接已存在则直接返回 */
        $info = $this->getByUrl($data['url']);
        if(!empty($info)){
            return $info;
        }

        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据
            $id = $this->save($data);
            $data['id'] = $id;
            if(!$id){
                $this->error = '新增链接出错！';
                return false;
            }
        } else { //更新数据
            $status = $this->isUpdate(true)->save($data); //更新基础内容
            if(false === $status){
                $this->error = '更新链接出错！';
                return false;
            }
        }

        //内容添加或更新完成
        return $data;
    }

}
