<?php
/**
 * ShopValue数据模型
 */
class ShopValueTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_value',
      'title' => '分类扩展属性数据表',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'cate_id' => [
          'title' => '所属分类ID',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'num',
          'is_show' => 4,
      ],
      'value' => [
          'title' => '表单值',
          'field' => 'text NULL',
          'type' => 'textarea',
      ],
      'cTime' => [
          'title' => '增加时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function',
      ],
      'openid' => [
          'title' => 'OpenId',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'auto_rule' => 'get_openid',
          'auto_time' => 1,
          'auto_type' => 'function',
      ],
      'uid' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 0,
          'is_must' => 0,
          'auto_type' => 'function',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1
      ]
  ];
}