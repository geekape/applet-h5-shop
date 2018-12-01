<?php
/**
 * ShopStatisticsFollow数据模型
 */
class ShopStatisticsFollowTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_statistics_follow',
      'title' => '分销粉丝统计表',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'uid' => [
          'title' => '粉丝id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'duid' => [
          'title' => '分销用户id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'ctime' => [
          'title' => '关注时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0,
          'auto_type' => 'function',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3
      ],
      'openid' => [
          'title' => '粉丝openid',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ]
  ];
}