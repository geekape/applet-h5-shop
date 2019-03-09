<?php
/**
 * ShopUserLevelLink数据模型
 */
class ShopUserLevelLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_user_level_link',
      'title' => '分销用户级别关系',
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
          'title' => '分销用户',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'upper_user' => [
          'title' => '上级分销用户',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'level' => [
          'title' => '分销级别',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'extra' => '1:一级分销商
2:二级分销商
3:三级分销商',
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 0,
          'is_must' => 0
      ]
  ];
}