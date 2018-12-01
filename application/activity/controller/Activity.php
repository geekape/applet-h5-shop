<?php

namespace app\activity\controller;

use app\common\controller\WebBase;

class Activity extends WebBase{
	function seckill(){
		return $this->fetch();
	}
	

	function bargain(){
		return $this->fetch();
	}

	function pingtuan(){
		return $this->fetch();
	}
}
