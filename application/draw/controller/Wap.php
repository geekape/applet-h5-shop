<?php

namespace app\draw\controller;

use app\common\controller\WapBase;

class Wap extends WapBase
{
	public function initialize()
	{
		parent::initialize();
		$this->apiModel = D('ApiData');
	}
	
}
