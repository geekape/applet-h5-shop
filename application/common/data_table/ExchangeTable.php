<?php
/**
 * Exchange数据模型
 */
class ExchangeTable {
    // 数据表模型配置
    public $config = [
      'name' => 'exchange',
      'title' => '积分兑换',
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
      'goods_id' => [
          'title' => '商品id',
      ],
      'uid' => [
          'title' => '兑换用户',
      ],
      'cTime' => [
          'title' => '兑换时间',
      ],
      'mobile' => [
          'title' => '联系方式',
      ],
      'address' => [
          'title' => '联系地址',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '',
                  'url' => '操作'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [ ];
}