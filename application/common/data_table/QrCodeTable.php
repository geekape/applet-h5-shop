<?php
/**
 * QrCode数据模型
 */
class QrCodeTable {
    // 数据表模型配置
    public $config = [
      'name' => 'qr_code',
      'title' => '二维码表',
      'search_key' => 'qr_code',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'scene_id' => [
          'title' => '事件KEY值',
      ],
      'qr_code' => [
          'title' => '二维码',
          'function' => 'get_code_img',
            'raw' => 1
      ],
      'action_name' => [
          'title' => ' 	二维码类型',
      ],
      'addon' => [
          'title' => '所属插件',
      ],
      'aim_id' => [
          'title' => '插件数据ID',
      ],
      'cTime' => [
          'title' => '增加时间',
          'function' => 'time_format',
      ],
      'request_count' => [
          'title' => '请求数',
          'function' => 'intval',
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
      'qr_code' => [
          'title' => '二维码',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'addon' => [
          'title' => '二维码所属插件',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'aim_id' => [
          'title' => '插件表里的ID值',
          'field' => 'int(10) unsigned NOT NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'extra_text' => [
          'title' => '文本扩展',
          'field' => 'text NULL ',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'extra_int' => [
          'title' => '数字扩展',
          'field' => 'int(10) NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'scene_id' => [
          'title' => '场景ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'action_name' => [
          'title' => '二维码类型',
          'field' => 'char(30) NULL',
          'type' => 'select',
          'remark' => 'QR_SCENE为临时,QR_LIMIT_SCENE为永久 ',
          'is_show' => 1,
          'extra' => 'QR_SCENE:临时二维码
QR_LIMIT_SCENE:永久二维码'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'request_count' => [
          'title' => '请求数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '用户回复的次数'
      ]
  ];
}