<?php
/**
 * SportAward数据模型
 */
class SportAwardTable {
    // 数据表模型配置
    public $config = [
      'name' => 'sport_award',
      'title' => '抽奖奖品',
      'search_key' => 'name:请输入抽奖名称',
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
		  'width' => '6%'
      ],
      'name' => [
          'title' => '奖项名称',
		  'width' => '23%'
      ],
      'img' => [
          'title' => '商品图片',
          'function' => 'get_img_html',
          'raw' => 1,
		  'width' => '8%'
      ],
      'price' => [
          'title' => '商品价格',
		  'width' => '8%'
      ],
      'explain' => [
          'title' => '奖品说明',
		  'width' => '24%'
      ],
      'count' => [
          'title' => '奖品数量',
		  'width' => '8%'
      ],
      'urls' => [
          'title' => '操作',
		  'width' => '20%',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ],
              '2' => [
                  'title' => '中奖者列表',
                  'url' => 'LuckyFollow/getlistByAwardId?awardId=[id]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'award_type' => [
          'title' => '奖品类型',
          'field' => 'varchar(30) NULL',
          'type' => 'select',
          'remark' => '选择奖品类别',
          'is_show' => 1,
          'extra' => '1:实物奖品|price@show,score@hide,coupon_id@hide,money@hide
0:积分|price@hide,score@show,coupon_id@hide,money@hide
2:优惠券|price@hide,score@hide,coupon_id@show,money@hide'
      ],
      'name' => [
          'title' => '奖项名称',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'count' => [
          'title' => '奖品数量',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'img' => [
          'title' => '奖品图片',
          'field' => 'int(10) NOT NULL',
          'type' => 'picture',
          'remark' => '图片尺寸建议为200*200',
          'is_show' => 1
      ],
      'price' => [
          'title' => '商品价格',
          'field' => 'FLOAT(10) NULL',
          'type' => 'num',
          'remark' => '价格默认为0，表示未报价',
          'is_show' => 1
      ],
      'score' => [
          'title' => '积分数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '积分奖励',
          'is_show' => 1
      ],
      'explain' => [
          'title' => '奖品说明',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'coupon_id' => [
          'title' => '选择赠送券',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'is_show' => 1
      ],
      'money' => [
          'title' => '返现金额',
          'field' => 'float(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num'
      ],
      'uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'aim_table' => [
          'title' => '活动标识',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ]
  ];
}