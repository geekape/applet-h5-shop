<?php
/**
 * WeixinMessage数据模型
 */
class WeixinMessageTable {
    // 数据表模型配置
    public $config = [
      'name' => 'weixin_message',
      'title' => '微信消息管理',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'FromUserName' => [
          'title' => '用户',
      ],
      'content' => [
          'title' => '内容',
      ],
      'CreateTime' => [
          'title' => '时间',
      ]
  ];

    // 字段定义
    public $fields = [
      'ToUserName' => [
          'title' => 'wpid',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'FromUserName' => [
          'title' => 'OpenID',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'CreateTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ],
      'MsgType' => [
          'title' => '消息类型',
          'field' => 'varchar(30) NULL',
          'type' => 'string'
      ],
      'MsgId' => [
          'title' => '消息ID',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'Content' => [
          'title' => '文本消息内容',
          'field' => 'text NULL',
          'type' => 'textarea'
      ],
      'PicUrl' => [
          'title' => '图片链接',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'MediaId' => [
          'title' => '多媒体文件ID',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'Format' => [
          'title' => '语音格式',
          'field' => 'varchar(30) NULL',
          'type' => 'string'
      ],
      'ThumbMediaId' => [
          'title' => '缩略图的媒体id',
          'field' => 'varchar(30) NULL',
          'type' => 'string'
      ],
      'Title' => [
          'title' => '消息标题',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'Description' => [
          'title' => '消息描述',
          'field' => 'text NULL',
          'type' => 'textarea'
      ],
      'Url' => [
          'title' => 'Url',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'collect' => [
          'title' => '收藏状态',
          'field' => 'tinyint(1) NULL',
          'type' => 'bool',
          'extra' => '0:未收藏
1:已收藏'
      ],
      'deal' => [
          'title' => '处理状态',
          'field' => 'tinyint(1) NULL',
          'type' => 'bool',
          'extra' => '0:未处理
1:已处理'
      ],
      'is_read' => [
          'title' => '是否已读',
          'field' => 'tinyint(1) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:未读
1:已读'
      ],
      'type' => [
          'title' => '消息分类',
          'field' => 'tinyint(1) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:用户消息
1:管理员回复消息'
      ],
      'is_material' => [
          'title' => '设置为文本素材',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'extra' => '0:不设置
1:设置'
      ]
  ];
}