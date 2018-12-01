<?php
/**
 * CardMember数据模型
 */
class CardMemberTable {
    // 数据表模型配置
    public $config = [
      'name' => 'card_member',
      'title' => '会员卡成员',
      'search_key' => 'username:请输入姓名',
      'add_button' => 0,
      'del_button' => 0,
      'search_button' => 1,
      'check_all' => 0,
      'list_row' => 10,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'number' => [
          'title' => '卡号',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'number',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'username' => [
          'title' => '姓名',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'username',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'phone' => [
          'title' => '手机号',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'phone',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'area' => [
          'title' => '地区',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'area',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'uid' => [
          'title' => '积分',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'uid',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'cTime' => [
          'title' => '加入时间',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'cTime',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'belong_staff' => [
          'title' => '所属员工',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'belong_staff',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'is_staff' => [
          'title' => '是否员工',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0,
          'name' => 'is_staff',
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
                  'title' => '删除',
                  'url' => '[DELETE]'
              ],
              '1' => [
                  'title' => '修改积分',
                  'url' => 'update_score&id=[id]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'phone' => [
          'title' => '手机号',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'username' => [
          'title' => '姓名',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'recharge' => [
          'title' => '余额',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'level' => [
          'title' => '会员卡等级',
          'field' => 'int(10) NULL',
          'type' => 'dynamic_select',
          'is_show' => 1,
          'extra' => 'table=card_level&value_field=id&title_field=level&order=id desc&first_option=请选择',
          'placeholder' => '请输入内容'
      ],
      'sex' => [
          'title' => '性别',
          'field' => 'int(10) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '1:男
2:女',
          'placeholder' => '请输入内容'
      ],
      'area' => [
          'title' => '地区',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'type' => [
          'title' => '会员类型',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'extra' => '0:观众
1:展商',
          'placeholder' => '请输入内容'
      ],
      'pid' => [
          'title' => '上级会员',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'company' => [
          'title' => '公司',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'email' => [
          'title' => '电子邮箱',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'infomation' => [
          'title' => '展商信息',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'status' => [
          'title' => '会员状态',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '1:正常
0:冻结',
          'placeholder' => '请输入内容'
      ],
      'birthday' => [
          'title' => '生日',
          'field' => 'int(10) NULL',
          'type' => 'date',
          'placeholder' => '请输入内容'
      ],
      'address' => [
          'title' => '地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'varchar(100) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'uid' => [
          'title' => '用户UID',
          'field' => 'int(10) NOT NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'cTime' => [
          'title' => '加入时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'number' => [
          'title' => '卡号',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 3,
          'placeholder' => '请输入内容'
      ],
      'marryday' => [
          'title' => '结婚纪念日',
          'type' => 'datetime',
          'field' => 'int(10) NULL',
          'placeholder' => '请输入内容'
      ],
      'job_num' => [
          'title' => '推荐工号',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'is_staff' => [
          'title' => '是否员工',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:用户
1:员工',
          'placeholder' => '请输入内容'
      ],
      'belong_staff' => [
          'title' => '所属员工',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'staff_code' => [
          'title' => '员工二维码',
          'type' => 'string',
          'field' => 'varchar(500) NULL',
          'placeholder' => '请输入内容'
      ],
      'erp_sales_id' => [
          'title' => 'erp员工编码',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'shop_code' => [
          'title' => '门店编码',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'level_name' => [
          'title' => '等级名',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'erp_customer_id' => [
          'title' => 'erp会员id',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'placeholder' => '请输入内容'
      ],
      'member_qrcode' => [
          'title' => '会员二维码',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'show_member_qrcode' => [
          'title' => '显示会员二维码',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:不显示
1:显示',
          'value' => 1,
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}