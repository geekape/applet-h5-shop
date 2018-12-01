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
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'keyword' => [
          'title' => '关键词',
          'name' => 'keyword',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'content' => [
          'title' => '回复内容',
          'raw' => 1,
          'name' => 'content',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]&type=[msg_type]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'img_id' => [
          'title' => '上传图片',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'news_id' => [
          'title' => '图文',
          'field' => 'int(10) NULL',
          'type' => 'news',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '多个关键词可以用空格分开，如“高富帅 白富美”',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'video_id' => [
          'title' => '视频素材id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 4,
          'placeholder' => '请输入内容'
      ],
      'voice_id' => [
          'title' => '语音素材id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 4,
          'placeholder' => '请输入内容'
      ],
      'image_material' => [
          'title' => '素材图片id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'pbid' => [
          'title' => '公众号id',
          'type' => 'string',
          'field' => 'varchar(50) NULL',
          'is_show' => 0,
          'is_must' => 0
      ],
      'manager_id' => [
          'title' => '管理员ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'msg_type' => [
          'title' => '消息类型',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'extra' => 'text:文本|content@show,group_id@hide,image_id@hide,voice_id@hide,video_id@hide
news:图文|content@hide,group_id@show,image_id@hide,voice_id@hide,video_id@hide
image:图片|content@hide,group_id@hide,image_id@show,voice_id@hide,video_id@hide
voice:语音|content@hide,group_id@hide,image_id@hide,voice_id@show,video_id@hide
video:视频|content@hide,group_id@hide,image_id@hide,voice_id@hide,video_id@show
',
          'placeholder' => '请输入内容'
      ],
      'text_id' => [
          'title' => '文本素材id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ]
  ];
}