<?php
/**
 * EventPrizes数据模型
 */
class EventPrizesTable {
    // 数据表模型配置
    public $config = [
      'name' => 'event_prizes',
      'title' => '活动中奖奖品',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'event_id' => [
          'title' => '活动id',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'prize_list' => [
          'title' => '奖品列表',
          'type' => 'textarea',
          'field' => 'text NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'prize_count' => [
          'title' => '奖品数量',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'start_num' => [
          'title' => '开始数字',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'end_num' => [
          'title' => '最后数字',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'sort' => [
          'title' => '顺序',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'value' => 1,
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ]
  ];
}