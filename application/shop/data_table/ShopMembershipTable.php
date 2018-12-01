<?php
/**
 * ShopMembership数据模型
 */
class ShopMembershipTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_membership',
      'title' => '商城会员设置',
      'search_key' => 'membership:请输入会员名',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [
      'img' => [
          'title' => '20%会员图标',
          'function' => 'get_img_html',
          'raw' => 1,
          'name' => 'img',
          'width' => '',
          'is_sort' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'membership' => [
          'title' => '会员名',
          'width' => '25%',
          'name' => 'membership',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'condition' => [
          'title' => '条件（经历值）',
          'width' => '20%',
          'name' => 'condition',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'width' => '30%',
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
          'is_sort' => 0,
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'membership' => [
          'title' => '会员名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'img' => [
          'title' => '图标',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'is_show' => 1,
      ],
      'condition' => [
          'title' => '升级会员条件',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '以用户经历值做为升级条件',
          'is_show' => 1,
      ],
      'uid' => [
          'title' => '企业用户id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
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
          'auto_time' => 3
      ]
  ];
}