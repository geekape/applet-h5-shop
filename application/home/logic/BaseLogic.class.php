<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

namespace Home\Logic;
use app\common\model\Base;
 

/**
 * 文档模型逻辑层公共模型
 * 所有逻辑层模型都需要继承此模型
 */
class BaseLogic extends Base{

	/**
	 * 构造函数，用于这是Logic层的表前缀
	 * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
	 */
	public function __construct($name = '', $tablePrefix = '', $connection = '') {
		/* 设置默认的表前缀 */
		$this->tablePrefix = DB_PREFIX . 'document_'; 
		/* 执行构造方法 */
		parent::__construct($name, $tablePrefix, $connection);
	}

	/**
	 * 获取模型详细信息
	 * @param  integer $id 文档ID
	 * @return array       当前模型详细信息
	 */
	public function detail($id){
		$data = $this->field(true)->where('id', $id)->find();
		if(!$data){
			$this->error = '获取详细信息出错！';
			return false;
		}
		return $data;
	}

	/**
	 * 获取段落列表
	 * @param  array $ids 要获取的段落ID列表
	 * @return array      段落数据列表
	 */
	public function lists($ids){
		$data = $this->field(true)->whereIn ( 'id', $ids )->select();
		$list = [];
		foreach ($data as $value) {
			$list[$value['id']] = $value;
		}
		return $list;
	}

    /**
     * 新增或添加模型数据
     * @param  number $id 文章ID
     * @return boolean    true-操作成功，false-操作失败
     */
    public function update($id = 0) {
        /* 获取数据 */
        $data = input('post.');
        if ($data === false) {
            return false;
        }

        if (empty($data['id'])) {//新增数据
            $data['id'] = $id;
            $id = $this->insertGetId($data);
            if (!$id) {
                $this->error = '新增数据失败！';
                return false;
            }
        } else { //更新数据
            $status = $this->isUpdate(true)->save($data);
            if (false === $status) {
                $this->error = '更新数据失败！';
                return false;
            }
        }
        return true;
    }
}
