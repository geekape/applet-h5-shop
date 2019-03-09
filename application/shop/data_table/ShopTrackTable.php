<?php
/**
 * ShopTrack数据模型
 */
class ShopTrackTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_track',
      'title' => '足迹',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'uid' => [
          'title' => 'uid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'goods_id' => [
          'title' => 'goods_id',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'create_at' => [
          'title' => 'create_at',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}