<?php
/**
 * ShopReward数据模型
 */
class ShopRewardTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_reward',
      'title' => '促销活动',
      'search_key' => 'title:请输入活动名称搜索',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '活动状态',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'start_time' => [
          'title' => '有效期',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => '',
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
          ]
      ]
  ];

    // 字段定义
    public $fields = [ ];
}