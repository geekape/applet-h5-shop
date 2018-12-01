<?php
/**
 * Menu数据模型
 */
class MenuTable {
    // 数据表模型配置
    public $config = [
      'name' => 'menu',
      'title' => '系统菜单',
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
      'title' => [
          'title' => '菜单名',
      ],
      'menu_type' => [
          'title' => '菜单类型',
      ],
      'addon_name' => [
          'title' => '插件名',
      ],
      'url' => [
          'title' => '外链',
      ],
      'target' => [
          'title' => '打开方式',
      ],
      'is_hide' => [
          'title' => '隐藏',
      ],
      'sort' => [
          'title' => '排序号',
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
      'menu_type' => [
          'title' => '菜单类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:顶级菜单|pid@hide
1:侧栏菜单|pid@show'
      ],
      'pid' => [
          'title' => '上级菜单',
          'field' => 'varchar(50) NULL',
          'type' => 'cascade',
          'is_show' => 1,
          'extra' => 'type=db&table=menu&menu_type=0&place=[place]&is_hide=0'
      ],
      'title' => [
          'title' => '菜单名',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'url_type' => [
          'title' => '链接类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:插件|addon_name@show,url@hide
1:外链|addon_name@hide,url@show'
      ],
      'addon_name' => [
          'title' => '插件名',
          'field' => 'varchar(30) NULL',
          'type' => 'dynamic_select',
          'is_show' => 1,
          'extra' => 'table=apps&type=0&value_field=name&title_field=title&order=id asc'
      ],
      'url' => [
          'title' => '外链',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'target' => [
          'title' => '打开方式',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'is_show' => 1,
          'extra' => '_self:当前窗口打开
_blank:在新窗口打开'
      ],
      'is_hide' => [
          'title' => '是否隐藏',
          'field' => 'tinyint(2) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:否
1:是'
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '值越小越靠前',
          'is_show' => 1
			],
			'place' => [
					'title' => '位置',
					'field' => 'tinyint(1) NULL',
					'type' => 'string',
					'is_show' => 4,
					'placeholder' => '请输入内容'
			]
  ];
}