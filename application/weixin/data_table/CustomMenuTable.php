<?php
/**
 * CustomMenu数据模型
 */
class CustomMenuTable {
    // 数据表模型配置
    public $config = [
      'name' => 'custom_menu',
      'title' => '自定义菜单',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'weixin'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '菜单名',
          'width' => '10%',
          'name' => 'title',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'keyword' => [
          'title' => '关联关键词',
          'width' => '10%',
          'name' => 'keyword',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'url' => [
          'title' => '关联URL',
          'width' => '10%',
          'name' => 'url',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'sort' => [
          'title' => '排序号',
          'width' => '5%',
          'name' => 'sort',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => '10%',
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'pid' => [
          'title' => '一级菜单',
          'field' => 'int(10) NULL',
          'type' => 'select',
          'remark' => '如果是一级菜单，选择“无”即可',
          'is_show' => 1,
          'extra' => '0:无',
          'placeholder' => '请输入内容'
      ],
      'title' => [
          'title' => '菜单名',
          'field' => 'varchar(50) NOT NULL',
          'type' => 'string',
          'remark' => '可创建最多 3 个一级菜单，每个一级菜单下可创建最多 5 个二级菜单。编辑中的菜单不会马上被用户看到，请放心调试。',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'from_type' => [
          'title' => '菜单内容',
          'field' => 'char(50) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '1:发送内容|keyword@hide,url@hide,type@hide,sucai_type@show,addon@hide,jump_type@hide
2:跳转网页|keyword@hide,url@show,type@hide,sucai_type@hide,addon@show,jump_type@show
9:自定义|keyword@show,url@hide,type@show,addon@hide,sucai_type@hide,jump_type@hide
',
          'placeholder' => '请输入内容'
      ],
      'type' => [
          'title' => '类型',
          'field' => 'varchar(30) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => 'click:点击推事件|keyword@show,url@hide
scancode_push:扫码推事件|keyword@show,url@hide
scancode_waitmsg:扫码带提示|keyword@show,url@hide
pic_sysphoto:弹出系统拍照发图|keyword@show,url@hide
pic_photo_or_album:弹出拍照或者相册发图|keyword@show,url@hide
pic_weixin:弹出微信相册发图器|keyword@show,url@hide
location_select:弹出地理位置选择器|keyword@show,url@hide
none:无事件的一级菜单|keyword@hide,url@hide',
          'placeholder' => '请输入内容'
      ],
      'jump_type' => [
          'title' => '推送类型',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'extra' => '1:URL|keyword@hide,url@show
0:关键词|keyword@show,url@hide',
          'placeholder' => '请输入内容'
      ],
      'addon' => [
          'title' => '选择插件',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'extra' => '0:请选择',
          'placeholder' => '请输入内容'
      ],
      'sucai_type' => [
          'title' => '素材类型',
          'field' => 'varchar(50) NULL',
          'type' => 'material',
          'is_show' => 1,
          'extra' => '0:请选择
1:图文
2:文本
3:图片
4:语音
5:视频',
          'placeholder' => '请输入内容'
      ],
      'keyword' => [
          'title' => '关联关键词',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'url' => [
          'title' => '关联URL',
          'field' => 'varchar(255) NULL ',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'tinyint(4) NULL ',
          'type' => 'num',
          'remark' => '数值越小越靠前',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'pbid' => [
          'title' => '公众号id',
          'type' => 'string',
          'field' => 'int(10) NULL',
          'is_show' => 0,
          'is_must' => 0,
          'auto_type' => 'function',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1
      ],
      'target_id' => [
          'title' => '选择内容',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 4,
          'extra' => '0:请选择',
          'placeholder' => '请输入内容'
      ]
  ];
}