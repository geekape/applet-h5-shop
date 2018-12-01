<?php
/**
 * CustomReplyText数据模型
 */
class CustomReplyTextTable {
    // 数据表模型配置
    public $config = [
      'name' => 'custom_reply_text',
      'title' => '文本回复',
      'search_key' => 'keyword',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'weixin'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => 'ID',
      ],
      'keyword' => [
          'title' => '关键词',
      ],
      'keyword_type' => [
          'title' => '关键词类型',
      ],
      'sort' => [
          'title' => '排序号',
      ],
      'view_count' => [
          'title' => '浏览数',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
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
    public $fields = [
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '多个关键词请用空格分开：例如: 高 富 帅',
          'is_show' => 1
      ],
      'keyword_type' => [
          'title' => '关键词类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'select',
          'is_show' => 1,
          'extra' => '0:完全匹配
1:左边匹配
2:右边匹配
3:模糊匹配
4:正则匹配
5:随机匹配'
      ],
      'content' => [
          'title' => '回复内容',
          'field' => 'text NULL',
          'type' => 'textarea',
          'remark' => '请不要多于1000字否则无法发送。支持加超链接，但URL必须带http://',
          'is_show' => 1
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'view_count' => [
          'title' => '浏览数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function'
      ]
  ];
}