<?php
/**
 * Award数据模型
 */
class AwardTable {
    // 数据表模型配置
    public $config = [
      'name' => 'award',
      'title' => '奖品库奖品',
      'search_key' => 'name:请输入奖品名称',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '编号',
          'come_from' => 0,
          'width' => 15,
          'is_sort' => 0,
          'name' => 'id',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'name' => [
          'title' => '奖项名称',
          'come_from' => 0,
          'width' => 20,
          'is_sort' => 0,
          'name' => 'name',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'award_type' => [
          'title' => '奖品类型',
          'come_from' => 0,
          'width' => 20,
          'is_sort' => 0,
          'name' => 'award_type',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'explain' => [
          'title' => '奖品说明',
          'come_from' => 0,
          'width' => 20,
          'is_sort' => 0,
          'name' => 'explain',
          'function' => '',
          'raw' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => 20,
          'is_sort' => 0,
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
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'award_type' => [
          'title' => '奖品类型',
          'type' => 'select',
          'field' => 'varchar(30) NULL',
          'extra' => '1:实物奖品|score@hide,money@hide,coupon_id@hide
0:积分|score@show,money@hide,coupon_id@hide
2:优惠券|score@hide,coupon_id@show,money@hide
4:现金红包|score@hide,money@show,coupon_id@hide',
          'value' => 1,
          'remark' => '选择奖品类别',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'name' => [
          'title' => '奖项名称',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'img' => [
          'title' => '奖品图片',
          'type' => 'picture',
          'field' => 'int(10) NULL',
          'remark' => '最佳尺寸200*200',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'score' => [
          'title' => '积分数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '积分奖励',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'money' => [
          'title' => '现金',
          'type' => 'num',
          'field' => 'decimal(10,2) NULL',
          'value' => 0,
          'remark' => '赠送到用户的微信钱包，单位 元，不能低于1元',
          'is_show' => 1,
          'is_must' => 0
      ],
      'explain' => [
          'title' => '奖品说明',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'coupon_id' => [
          'title' => '选择赠送券',
          'type' => 'select',
          'field' => 'char(50) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'aim_table' => [
          'title' => '活动标识',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'count' => [
          'title' => '奖品数量',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'price' => [
          'title' => '商品价格',
          'field' => 'FLOAT(10) NULL',
          'type' => 'num',
          'remark' => '价格默认为0，表示未报价',
          'placeholder' => '请输入内容'
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ]
  ];
}