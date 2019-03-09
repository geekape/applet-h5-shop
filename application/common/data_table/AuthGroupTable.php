<?php
/**
 * AuthGroup数据模型
 */
class AuthGroupTable {
    // 数据表模型配置
    public $config = [
      'name' => 'auth_group',
      'title' => '用户组',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '分组名称',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'title',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'description' => [
          'title' => '描述',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'description',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'qr_code' => [
          'title' => '二维码',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'qr_code',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => '',
          'is_sort' => 0,
          'href' => [
              '0' => [
                  'title' => '导出用户',
                  'url' => 'export?id=[id]'
              ],
              '1' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '分组名称',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'is_show' => 1,
          'is_must' => 1,
          'placeholder' => '请输入内容'
      ],
      'description' => [
          'title' => '描述信息',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wechat_group_name' => [
          'title' => '微信端的分组名',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'wechat_group_id' => [
          'title' => '微信端的分组ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'qr_code' => [
          'title' => '微信二维码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'is_default' => [
          'title' => '是否默认自动加入',
          'field' => 'tinyint(1) NULL',
          'type' => 'radio',
          'remark' => '只有设置一个默认组，设置当前为默认组后之前的默认组将取消',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'pbid' => [
          'title' => '公众号id',
          'type' => 'string',
          'field' => 'varchar(100) NULL',
          'is_show' => 0,
          'is_must' => 0
      ],
      'manager_id' => [
          'title' => '管理员ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '为0时表示系统用户组',
          'placeholder' => '请输入内容'
      ],
      'rules' => [
          'title' => '权限',
          'field' => 'text NULL',
          'type' => 'textarea',
          'placeholder' => '请输入内容'
      ],
      'type' => [
          'title' => '类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:普通用户组
1:微信用户组
2:等级用户组
3:认证用户组',
          'placeholder' => '请输入内容'
      ],
      'status' => [
          'title' => '状态',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '1:正常
0:禁用
-1:删除',
          'placeholder' => '请输入内容'
      ],
      'icon' => [
          'title' => '图标',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'placeholder' => '请输入内容'
      ],
      'wechat_group_count' => [
          'title' => '微信端用户数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'is_del' => [
          'title' => '是否已删除',
          'field' => 'tinyint(1) NULL',
          'type' => 'bool',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ]
  ];
}