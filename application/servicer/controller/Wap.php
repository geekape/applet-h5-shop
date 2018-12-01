<?php

namespace app\servicer\controller;
use app\common\controller\WapBase;

class Wap extends WapBase{
	var $model;
	function initialize() {
		$this->model = $this->getModel ( 'servicer' );
		parent::initialize();
	}
	public function do_login(){
		$id = I('id',0,intval);
		if(empty($id)){
			//$this -> error('授权失败！');
		}else{
			$info = M( 'Servicer' )->where( wp_where(array('id'=>$id) ))->find();
			$fieldsAttr = get_model_attribute ( $this->model );
			$roleAttr = parse_field_attr($fieldsAttr['role']['extra']);
			$roles = explode(',',$info['role']);
			$roleStr = '';
			foreach($roles as $r){
				if(empty($roleStr)){
					$roleStr = $roleAttr[$r];
				}else{
					$roleStr = $roleStr .'<br/>'. $roleAttr[$r];
				}
			}
			$info['roleStr '] = $roleStr ;
			$this -> assign('info',$info);
		}
		return $this->fetch();
	}
}
