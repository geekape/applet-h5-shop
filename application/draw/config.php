<?php
return array (
	'draw' =>array (
         'need_attention'=>array(
             'title'=>'是否需要关注才能参加活动',
             'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
			 'options' => array ( // select 和radion、checkbox的子选项
						'1' => '是', // 值=>文字
						'0' => '否'
			  ),
			'value' => '1', // 表单的默认值
         ),
	    'need_writeinfo'=>array(
	        'title'=>'中奖后是否需要填写个人信息',
	        'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
	        'options' => array ( // select 和radion、checkbox的子选项
	            '1' => '是', // 值=>文字
	            '0' => '否'
	        ),
	        'value' => '1', // 表单的默认值
	    )
    )
);
