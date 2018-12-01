<?php
/**
 * ShopDistributionUser数据模型
 */
class ShopDistributionUserTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_distribution_user',
      'title' => '分销用户',
      'search_key' => 'truename:请输入姓名',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '序号',
          'name' => 'id',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
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
      'mobile' => [
          'title' => '手机号',
          'name' => 'mobile',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'uid' => [
          'title' => '微信昵称',
          'name' => 'uid',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'wechat' => [
          'title' => '微信号',
          'name' => 'wechat',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'inviter' => [
          'title' => '邀请人',
          'name' => 'inviter',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'ctime' => [
          'title' => '创建时间',
          'function' => 'time_format',
          'name' => 'ctime',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'level' => [
          'title' => '分销级别',
          'name' => 'level',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'is_audit' => [
          'title' => '审核',
          'name' => 'is_audit',
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
          'title' => '用户id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'qr_code' => [
          'title' => '二维码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'wechat' => [
          'title' => '微信号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'inviter' => [
          'title' => '邀请人',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'level' => [
          'title' => '分佣级别',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'is_audit' => [
          'title' => '是否审核',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'extra' => '0:未审核
1:通过
2:未通过',
      ],
      'enable' => [
          'title' => '是否启用',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'extra' => '0:否
1:是',
      ],
      'branch_id' => [
          'title' => '授权门店',
          'field' => 'varchar(255) NULL',
          'type' => 'cascade',
          'extra' => 'type=db&table=stores&branch_id=id',
      ],
      'ctime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
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
      ],
      'fans_gift_money' => [
          'title' => '转发增粉奖励金额',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'fans_gift_score' => [
          'title' => '转发增粉奖励积分',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'fans_gift_coupon' => [
          'title' => '转发增粉奖励优惠券',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'is_delete' => [
          'title' => '是否删除',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'shop_name' => [
          'title' => '商城店名字',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '默认为 用户姓名 的小店',
      ],
      'shop_logo' => [
          'title' => '商城图标',
          'field' => 'varchar(300) NULL',
          'type' => 'string',
          'remark' => '默认为 用户的头像',
      ],
      'profit_money' => [
          'title' => '盈利金额',
          'field' => 'float(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'zfb_name' => [
          'title' => '支付宝名称',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
      ],
      'zfb_account' => [
          'title' => '支付宝账号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
      ]
  ];
}