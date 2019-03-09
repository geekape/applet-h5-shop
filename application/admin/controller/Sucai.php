<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 在线应用商店
 */
class Sucai extends Admin {
	
	function chooseTemplate(){
		$Model = D('SucaiTemplate');
		$templateList = $this->_getSucaiTemplate();
		$uid = I('uid');
		$userTemplateList = $Model->where( 'uid='.$uid)->select();
		if(!empty($userTemplateList)){
			foreach($templateList as &$vo){
				$addons = $vo['apps'];
				$template = $vo['template'];
				foreach($userTemplateList as $vou){
					if($vou['apps']==$addons && $vou['template']==$template){
						$vo['isUse'] = true;
						//dump('aaaaaaaaaaaaaaaaaaa');
					}
				}
			}
		}
		//dump($templateList);
		$this -> assign('templateList',$templateList);
		return $this->fetch();
	}
	function _getSucaiTemplate($addons=''){
		if(empty($addons)){
			$dir = SITE_PATH . '/SucaiTemplate';
			$dirObj = opendir ( $dir );
			while ( false!==($file = readdir($dirObj)) ) {
				if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
					continue;
				
				$subDir = $dir.'/'.$file;
				$subDirObj = opendir ( $subDir );
				while ( false!==($subFile = readdir ( $subDirObj ) )) {
					if ($subFile === '.' || $subFile == '..' || $subFile == '.svn' || is_file ( $subDir . '/' . $subFile ))
						continue;
					// 获取配置文件
					$res['apps'] = $file;
					$res['template'] = $subFile;
					if (file_exists ( $subDir . '/' . $subFile . '/info.php' )) {
						$info = require_once $subDir . '/' . $subFile . '/info.php';
						$res = array_merge ( $res, $info );
					}
					
					// 获取效果图
					if (file_exists ( $subDir . '/' . $subFile . '.png' )) {
						$res ['icon'] = __ROOT__ . '/SucaiTemplate/'.$file.'/'.$subFile.'.png';
					} else {
						$res ['icon'] = __ROOT__ . '/home/images/no_template_icon.png';
					}
					$templateList [] = $res; 
					unset ( $res );
				}
			}
			closedir ( $dir );
			//dump($templateList);
			return $templateList;
		}else{
			$dir = SITE_PATH . '/SucaiTemplate/'.$addons;
			$dirObj = opendir ( $dir );
			
			while ( false!==($subFile = readdir ( $dirObj ) ) ) {
				if ($subFile === '.' || $subFile == '..' || $subFile == '.svn' || is_file ( $dir . '/' . $subFile ))
					continue;
				// 获取配置文件
				$res['apps'] = $addons;
				$res['template'] = $subFile;
				if (file_exists ( $dir . '/' . $subFile . '/info.php' )) {
					$info = require_once $dir . '/' . $subFile . '/info.php';
					$res = array_merge ( $res, $info );
				}
				
				// 获取效果图
				if (file_exists ( $dir . '/' . $subFile . '.png' )) {
					$res ['icon'] = __ROOT__ . '/SucaiTemplate/'.$file.'/'.$subFile.'.png';
				} else {
					$res ['icon'] = __ROOT__ . '/home/images/no_template_icon.png';
				}
				$templateList [] = $res; 
				unset ( $res );
			}
			
			closedir ( $dir );
			//dump(count($templateList));
			return $templateList;
		}
		
	}
	
	//授权操作
	function useTemplate(){
		$data['apps'] = I('apps');
		$data['template'] = I('template');
		$data['uid'] = I('uid');
		$mInfo = M('publics')->where( 'uid='.$data['uid'])->find();
		$data['wpid'] = $mInfo['id'];
		if(D('SucaiTemplate')->insertGetId($data)){
			$this->success ( '授权成功' );
		}else{
			$this->error ( '授权失败！' );
		}
	}
	//取消授权
	function cancelTemplate(){
		$data['apps'] = I('apps');
		$data['template'] = I('template');
		$data['uid'] = I('uid');
		$mInfo = M('publics')->where( 'uid='.$data['uid'])->find();
		$data['wpid'] = $mInfo['id'];
		
		if(D('SucaiTemplate')->where( wp_where($data) )->delete()){
			$this->success ( '取消授权成功' );
		}else{
			$this->error ( '取消授权失败！' );
		}
	}
	
}
