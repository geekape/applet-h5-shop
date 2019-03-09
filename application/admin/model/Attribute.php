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
 * 属性模型
 * @author huajie <banhuajie@163.com>
 */

class Attribute extends Base {
	protected $table = DB_PREFIX. 'attribute';

    /* 自动验证规则 */
//    protected $_validate = array(
//        array('name', 'require', '字段名必须', self::MUST_VALIDATE, 'regex', MODEL_BOTH),
//        array('name', '/^[a-zA-Z][\w_]{1,29}$/', '字段名不合法', self::MUST_VALIDATE, 'regex', MODEL_BOTH),
//    	array('name', 'checkName', '字段名已存在', self::MUST_VALIDATE, 'callback', MODEL_BOTH),
//    	array('field', 'require', '字段定义必须', self::MUST_VALIDATE, 'regex', MODEL_BOTH),
//    	array('field', '1,100', '注释长度不能超过100个字符', self::VALUE_VALIDATE, 'length', MODEL_BOTH),
//        array('title', '1,100', '注释长度不能超过100个字符', self::VALUE_VALIDATE, 'length', MODEL_BOTH),
//        array('remark', '1,100', '备注不能超过100个字符', self::VALUE_VALIDATE, 'length', MODEL_BOTH),
//    	array('model_id', 'require', '未选择操作的模型', self::MUST_VALIDATE, 'regex', MODEL_BOTH),
//    );
//
//    /* 自动完成规则 */
//    protected $_auto = array(
//        array('status', 1, MODEL_INSERT, 'string'),
//    	array('create_time', 'time', MODEL_INSERT, 'function'),
//        array('update_time', 'time', MODEL_BOTH, 'function'),
//    );

    /* 操作的表名 */
    protected $tableName_name = null;

    /**
     * 新增或更新一个属性
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfo($data = null, $create = true){
        /* 获取数据对象 */
    	$data = empty($data) ? input('post.') : $data;
        $data = input('post.');
        if(empty($data)){
            return false;
        }
        /* 添加或新增属性 */
        if(empty($data['id'])){ //新增属性

            $id = $this->allowField(true)->insertGetId($data);
            if(!$id){
                $this->error = '新增属性出错！';
                return false;
            }

            if($create){
            	//新增表字段
            	$res = $this->addField($data);
            	if(!$res){
            		$this->error = '新建字段出错！';
            		//删除新增数据
            		$this->where( 'id='.$id)->delete ();
            		return false;
            	}
            }

        } else { //更新数据
        	if($create){
        	//更新表字段
	        	$res = $this->updateField($data);
	        	if(!$res){
	        		$this->error = '更新字段出错！';
	        		return false;
	        	}
        	}
            $status = $this->allowField(true)->isUpdate(true)->save($data);
            if(false === $status){
                $this->error = '更新属性出错！';
                return false;
            }
        }
        //删除字段缓存文件
        $model_name = M( 'model' )->field('name')->where('id', $data['model_id'])->find ();
        $cache_name = config('DB_NAME').'.'.preg_replace('/\W+|\_+/','',$model_name['name']);
        S($cache_name, null);

        //记录行为
        action_log('update_attribute', 'attribute', $data['id'] ? $data['id'] : $id, UID);

        //内容添加或更新完成
        return $data;

    }

    /**
     * 检查同一张表是否有相同的字段
     * @author huajie <banhuajie@163.com>
     */
    protected function checkName(){
    	$name = I('post.name');
    	$model_id = I('post.model_id');
    	$id = I('post.id');
    	$map = array('name'=>$name, 'model_id'=>$model_id);
    	if(!empty($id)){
    		$map['id'] = array('neq', $id);
    	}
    	$res = $this->where( wp_where($map) )->find();
    	return empty($res);
    }

    /**
     * 检查当前表是否存在
     * @param intger $model_id 模型id
     * @return intger 是否存在
     * @author huajie <banhuajie@163.com>
     */
    protected function checkTableExist($model_id){
    	//当前操作的表
		$name = M( 'model' )->where( 'id', $model_id )->value('name');
		$table_name = $this->table_name = DB_PREFIX.strtolower($name);
		
		$sql = <<<sql
				SHOW TABLES LIKE '{$table_name}';
sql;
		$res = M()->query($sql);
		return count($res);
    }

    /**
     * 新建表字段
     * @param array $field 需要新建的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    protected function addField($field){
    	//检查表是否存在
    	$table_exist = $this->checkTableExist($field['model_id']);

    	//获取默认值
    	if($field['value'] === ''){
    		$default = '';
    	}elseif (is_numeric($field['value'])){
    		$default = ' DEFAULT '.$field['value'];
    	}elseif (is_string($field['value'])){
    		$default = ' DEFAULT \''.$field['value'].'\'';
    	}else {
    		$default = '';
    	}

    	if($table_exist){
    		$sql = <<<sql
				ALTER TABLE `{$this->table_name}`
ADD COLUMN `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}';
sql;
    	}else{
    		//新建表时是否默认新增“id主键”字段
			$map['id'] = $field['model_id'];
    		$model_info = M( 'model' )->field('engine_type,need_pk')->where( wp_where($map) )->find();
    		if($model_info['need_pk']){
    			$sql = <<<sql
				CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
				`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键' ,
				`{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ,
				PRIMARY KEY (`id`)
				)
				ENGINE={$model_info['engine_type']}
				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
				CHECKSUM=0
				ROW_FORMAT=DYNAMIC
				DELAY_KEY_WRITE=0
				;
sql;
    		}else{
    			$sql = <<<sql
				CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
				`{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}'
				)
				ENGINE={$model_info['engine_type']}
				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
				CHECKSUM=0
				ROW_FORMAT=DYNAMIC
				DELAY_KEY_WRITE=0
				;
sql;
    		}

    	}
    	$res = M()->execute($sql);
    	return $res !== false;
    }

    /**
     * 更新表字段
     * @param array $field 需要更新的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateField($field){
    	//检查表是否存在
    	$table_exist = $this->checkTableExist($field['model_id']);

    	//获取原字段名
    	$last_field = $this->getFieldById($field['id'], 'name');

    	//获取默认值
    	if($field['value'] === ''){
    		$default = '';
    	}elseif (is_numeric($field['value'])){
    		$default = ' DEFAULT '.$field['value'];
    	}elseif (is_string($field['value'])){
    		$default = ' DEFAULT \''.$field['value'].'\'';
    	}else {
    		$default = '';
    	}    	

    	$sql = <<<sql
			ALTER TABLE `{$this->table_name}`
CHANGE COLUMN `{$last_field}` `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ;
sql;

    	$res = M()->execute($sql);
    	return $res !== false;
    }

    /**
     * 删除一个字段
     * @param array $field 需要删除的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    public function deleteField($field){
    	//检查表是否存在
    	$table_exist = $this->checkTableExist($field['model_id']);

    	$sql = <<<sql
			ALTER TABLE `{$this->table_name}`
DROP COLUMN `{$field['name']}`;
sql;
    	$res = M()->execute($sql);
    	return $res !== false;
    }

}
