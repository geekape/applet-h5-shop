<?php
/**
 * CustomSendall数据模型
 */
class CustomSendallTable {
    // 数据表模型配置
    public $config = [
      'name' => 'custom_sendall',
      'title' => '客服群发消息',
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
      'ToUserName' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'FromUserName' => [
          'title' => 'openid',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'msgType' => [
          'title' => '消息类型',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'manager_id' => [
          'title' => '管理员id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'content' => [
          'title' => '内容',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'media_id' => [
          'title' => '多媒体文件id',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'is_send' => [
          'title' => '是否已经发送',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'extra' => '0:未发
1:已发'
      ],
      'uid' => [
          'title' => '粉丝uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'news_group_id' => [
          'title' => '图文组id',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'video_title' => [
          'title' => '视频标题',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'video_description' => [
          'title' => '视频描述',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'video_thumb' => [
          'title' => '视频缩略图',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'voice_id' => [
          'title' => '语音id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'image_id' => [
          'title' => '图片id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'video_id' => [
          'title' => '视频id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'send_type' => [
          'title' => '发送方式',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'extra' => '0:分组
1:指定用户'
      ],
      'send_openids' => [
          'title' => '指定用户',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'group_id' => [
          'title' => '分组id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'diff' => [
          'title' => '区分消息标识',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ]
  ];
}