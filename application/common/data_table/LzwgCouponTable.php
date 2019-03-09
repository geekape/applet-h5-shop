<?php
/**
 * LzwgCoupon数据模型
 */
class LzwgCouponTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_coupon',
      'title' => '优惠券',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '名称',
      ],
      'money' => [
          'title' => '减免金额',
      ],
      'name' => [
          'title' => '代金券标题',
      ],
      'condition' => [
          'title' => '抵押条件',
      ],
      'intro' => [
          'title' => '优惠券简述',
      ],
      'sn_str' => [
          'title' => '数量',
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
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '名称',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'money' => [
          'title' => '减免金额',
          'field' => 'decimal(10,2) NULL',
          'type' => 'string',
          'remark' => '元，减免金额只能是大于0.01的数字',
          'is_show' => 1
      ],
      'name' => [
          'title' => '代金券 标题',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '建议填写代金券“减免金额”及自定义内容',
          'is_show' => 1
      ],
      'condition' => [
          'title' => '抵押条件',
          'field' => 'decimal(10,2) NULL',
          'type' => 'string',
          'remark' => '元，选填，消费满多少元可以用。如不填写则默认：消费满任何金额可用',
          'is_show' => 1
      ],
      'intro' => [
          'title' => '优惠券简述',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'sn_str' => [
          'title' => '序列号',
          'field' => 'text NULL',
          'type' => 'textarea',
          'remark' => '一个序列号占一行，请换行添加',
          'is_show' => 1
      ],
      'img' => [
          'title' => '优惠卷图标',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'is_show' => 1
      ]
  ];
}