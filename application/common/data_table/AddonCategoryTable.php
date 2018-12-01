<?php
/**
 * AddonCategory数据模型
 */
class AddonCategoryTable {
    // 数据表模型配置
    public $config = [
      'name' => 'addon_category',
      'title' => '插件分类',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'icon' => [
          'title' => '分类图标',
          'function' => 'get_img_html',
          'raw' => 1,
      ],
      'title' => [
          'title' => '分类名',
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
      'icon' => [
          'title' => '分类图标',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'is_show' => 1
      ],
      'title' => [
          'title' => '分类名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '值越小越靠前',
          'is_show' => 1
      ]
  ];
}