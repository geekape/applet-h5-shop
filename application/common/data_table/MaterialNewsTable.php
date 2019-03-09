<?php
/**
 * MaterialNews数据模型
 */
class MaterialNewsTable {
    // 数据表模型配置
    public $config = [
      'name' => 'material_news',
      'title' => '图文素材',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '标题',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'author' => [
          'title' => '作者',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'cover_id' => [
          'title' => '封面',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'is_show' => 1
      ],
      'intro' => [
          'title' => '摘要',
          'field' => 'varchar(255) NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'content' => [
          'title' => '内容',
          'field' => 'longtext  NULL',
          'type' => 'editor',
          'is_show' => 1
      ],
      'link' => [
          'title' => '外链',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'group_id' => [
          'title' => '多图文组的ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0 表示单图文，多于0 表示多图文中的第一个图文的ID值'
      ],
      'thumb_media_id' => [
          'title' => '图文消息的封面图片素材id（必须是永久mediaID）',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'media_id' => [
          'title' => '微信端图文消息素材的media_id',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'manager_id' => [
          'title' => '管理员ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'pbid' => [
          'title' => 'pbid',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'cTime' => [
          'title' => '发布时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ],
      'url' => [
          'title' => '图文页url',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'is_use' => [
          'title' => '可否使用',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'extra' => '0:不可用
1:可用'
      ],
      'aim_id' => [
          'title' => '添加来源标识id',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'aim_table' => [
          'title' => '来源表名',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'update_time' => [
          'title' => 'update_time',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ]
  ];
}