<?php
/**
 * Servicer数据模型
 */
class ServicerTable {
    // 数据表模型配置
    public $config = [
      'name' => 'servicer',
      'title' => '授权用户',
      'search_key' => 'truename:请输入姓名搜索',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'servicer'
  ];

    // 列表定义
    public $list_grid = [
      'truename' => [
          'title' => '姓名',
          'name' => 'truename',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'role' => [
          'title' => '权限列表',
          'name' => 'role',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'nickname' => [
          'title' => '微信名称',
          'raw' => 1,
          'name' => 'nickname',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'enable' => [
          'title' => '是否启用',
          'name' => 'enable',
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
                  'title' => 'enable:0|启用,1|禁用',
                  'url' => 'set_enable?id=[id]&enable=[enable]',
                  'class' => 'ajax-get'
              ],
              '1' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '2' => [
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
      'uid' => [
          'title' => '用户选择',
          'field' => 'int(10) NULL',
          'type' => 'user',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'truename' => [
          'title' => '真实姓名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'mobile' => [
          'title' => '手机号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'role' => [
          'title' => '授权列表',
          'field' => 'varchar(100) NULL',
          'type' => 'checkbox',
          'is_show' => 1,
          'extra' => '1:微信客服
2:扫码验证',
          'placeholder' => '请输入内容'
      ],
      'enable' => [
          'title' => '是否启用',
          'field' => 'int(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'value' => 1,
          'extra' => '0:禁用
1:启用',
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'update_at' => [
          'title' => '更新时间',
          'field' => 'int(10) NOT NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'pbid' => [
          'title' => '公众号id',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'value' => 0,
          'is_show' => 0,
          'is_must' => 0,
          'auto_type' => 'function',
          'auto_rule' => 'get_pbid',
          'auto_time' => 3
      ]
  ];
}
