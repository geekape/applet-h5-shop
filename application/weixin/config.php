<?php
return array (
	'wecome' =>array (
         'stype'=>array(
             'title'=>'选择类型',
             'type'=>'material',
             'value'=>''
         )
    ),
    'no_answer' =>array (
        'data_type' => array (
                'title' => '客服方式',
                'type' => 'radio',
                'options' => array (
                        '0' => '自动回复',
                        '1' => '转微信人工回复' 
                ),
                'value' => 0 
        ),
        'stype' => array (
                'title' => '回复内容',
                'type' => 'material',
                'value' => '' 
        ) 
    ),
);
