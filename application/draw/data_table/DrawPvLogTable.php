<?php
/**
 * DrawPvLog数据模型
 */
class DrawPvLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'draw_pv_log',
      'title' => '抽奖游戏浏览记录',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'cTime' => [
          'title' => '访问时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'draw_id' => [
          'title' => '游戏ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'uid' => [
          'title' => '用户id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'openid' => [
          'title' => 'openid',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ]
  ];
}