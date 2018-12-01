<?php
/**
 * User数据模型
 */
class UserTable {
    // 数据表模型配置
    public $config = [
      'name' => 'user',
      'title' => '用户信息表',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'headimgurl' => [
          'title' => '头像',
          'function' => 'url_img_html',
          'raw' => 1,
          'name' => 'headimgurl',
          'width' => '',
          'is_sort' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'login_name' => [
          'title' => '登录账号',
          'name' => 'login_name',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'login_password' => [
          'title' => '登录密码',
          'name' => 'login_password',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'nickname' => [
          'title' => '用户昵称',
          'name' => 'nickname',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'sex' => [
          'title' => '性别',
          'name' => 'sex',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'group' => [
          'title' => '分组',
          'name' => 'group',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'score' => [
          'title' => '金币值',
          'name' => 'score',
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
                  'title' => '设置登录账号',
                  'url' => 'set_login?uid=[uid]'
              ],
              '1' => [
                  'title' => '详细资料',
                  'url' => 'detail?uid=[uid]'
              ],
              '2' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
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
      'come_from' => [
          'title' => '来源',
          'field' => 'tinyint(1) NULL',
          'type' => 'select',
          'extra' => '0:PC注册用户
1:微信同步用户
2:手机注册用户',
          'placeholder' => '请输入内容'
      ],
      'nickname' => [
          'title' => '用户名',
          'field' => 'text NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'password' => [
          'title' => '登录密码',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'truename' => [
          'title' => '真实姓名',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'mobile' => [
          'title' => '联系电话',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'email' => [
          'title' => '邮箱地址',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'sex' => [
          'title' => '性别',
          'field' => 'tinyint(2) NULL',
          'type' => 'radio',
          'extra' => '0:保密
1:男
2:女',
          'placeholder' => '请输入内容'
      ],
      'headimgurl' => [
          'title' => '头像地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'city' => [
          'title' => '城市',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'province' => [
          'title' => '省份',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'country' => [
          'title' => '国家',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'language' => [
          'title' => '语言',
          'field' => 'varchar(20) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'score' => [
          'title' => '积分值',
          'type' => 'num',
          'field' => 'float(10) NULL',
          'is_show' => 0,
          'is_must' => 0
      ],
      'unionid' => [
          'title' => '微信第三方ID',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'login_count' => [
          'title' => '登录次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'reg_ip' => [
          'title' => '注册IP',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'reg_time' => [
          'title' => '注册时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'placeholder' => '请输入内容'
      ],
      'last_login_ip' => [
          'title' => '最近登录IP',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'last_login_time' => [
          'title' => '最近登录时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'placeholder' => '请输入内容'
      ],
      'status' => [
          'title' => '状态',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:禁用
1:启用',
          'placeholder' => '请输入内容'
      ],
      'is_init' => [
          'title' => '初始化状态',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:未初始化
1:已初始化',
          'placeholder' => '请输入内容'
      ],
      'is_audit' => [
          'title' => '审核状态',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:未审核
1:已审核',
          'placeholder' => '请输入内容'
      ],
      'subscribe_time' => [
          'title' => '用户关注公众号时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'placeholder' => '请输入内容'
      ],
      'remark' => [
          'title' => '微信用户备注',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'groupid' => [
          'title' => '微信端的分组ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'login_name' => [
          'title' => 'login_name',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'level' => [
          'title' => '管理等级',
          'field' => 'tinyint(2) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'login_password' => [
          'title' => '登录密码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'manager_id' => [
          'title' => '公众号管理员ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'membership' => [
          'title' => '会员等级',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'remark' => '请在会员等级 添加会员级别名称',
          'placeholder' => '请输入内容'
      ],
      'bind_openid' => [
          'title' => '绑定的openid',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'audit_time' => [
          'title' => '审核通过时间',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '审核通过时间',
          'placeholder' => '请输入内容'
      ],
      'grade' => [
          'title' => '当前用户的等级',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'auth_code' => [
          'title' => '用户授权码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ]
  ];
}