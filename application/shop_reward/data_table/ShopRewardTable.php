<?php
/**
 * ShopReward数据模型
 */
class ShopRewardTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_reward',
      'title' => '促销活动',
      'search_key' => 'title:请输入活动名称搜索',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop_reward'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '活动名称',
          'name' => 'title',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'start_time' => [
          'title' => '有效期',
          'name' => 'start_time',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'status' => [
          'title' => '活动状态',
          'name' => 'status',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '活动名称',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'start_time' => [
          'title' => '开始时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
      ],
      'end_time' => [
          'title' => '过期时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
      ],
      'is_mult' => [
          'title' => '多级优惠',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'remark' => '多级情况下每级优惠不累积叠加',
          'is_show' => 1,
          'extra' => '0:否
1:是',
      ],
      'is_all_goods' => [
          'title' => '适用的活动商品',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:全部商品参与
1:指定商品参与',
      ],
      'manager_id' => [
          'title' => '管理员ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function',
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
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function',
      ],
      'goods_ids' => [
          'title' => '指定商品ID串',
          'field' => 'text NULL',
          'type' => 'textarea',
      ]
  ];
}