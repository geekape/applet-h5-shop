<?php
/**
 * AutoReply数据模型
 */
class AutoReplyTable {
    // 数据表模型配置
    public $config = [
      'name' => 'auto_reply',
      'title' => '自动回复',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'weixin'
  ];

    // 列表定义
    public $list_grid = [
      'keyword' => [
          'title' => '关键词',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'img_id' => [
          'title' => '回复内容',
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
                  'url' => '[EDIT]&type=[msg_type]'
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