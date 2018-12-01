<?php

namespace app\wei_site\controller;
use app\wei_site\controller\Base;

class Leaflets extends Base{
	//首页
	function index(){
		return $this->fetch();
	}
	//分类列表
	function category(){
		return $this->fetch();
	}
	//相册模式
	function picList(){
		return $this->fetch();
	}
	//详情
	function detail(){
		return $this->fetch();
	}
}
