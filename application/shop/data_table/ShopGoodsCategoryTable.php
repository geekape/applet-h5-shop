<?php
/**
 * ShopGoodsCategory数据模型
 */
class ShopGoodsCategoryTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_category',
      'title' => '商品分类',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '分组',
          'width' => '30%',
          'name' => 'title',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'icon' => [
          'title' => '图标',
          'function' => 'get_img_html',
          'raw' => 1,
          'width' => '20%',
          'name' => 'icon',
          'is_sort' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'sort' => [
          'title' => '排序号',
          'width' => '10%',
          'name' => 'sort',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'is_show' => [
          'title' => '显示',
          'width' => '10%',
          'name' => 'is_show',
          'function' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
        'is_recommend' => [
            'title' => '是否推荐',
            'width' => '10%',
            'name' => 'is_recommend',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => [ ]
        ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => '20%',
          'href' => [
              '0' => [
                  'title' => '复制链接',
                  'url' => '[WAP_URL]&id=[id]&pid=[pid]/#/lists'
              ],
              '1' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '2' => [
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
          'title' => '上一级分类',
          'field' => 'int(10) unsigned NULL',
          'type' => 'select',
          'remark' => '如果你要增加一级分类，这里选择“无”即可，最多支持二级分类',
          'is_show' => 1,
      ],
      'title' => [
          'title' => '分类标题',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1,
          'is_must' => 1,
		  'remark' => '一级分类建议长度为4个字',
      ],
      'icon' => [
          'title' => '分类图标',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'remark' => '建议上传100X100的正方形图片',
          'is_show' => 1,
          'is_must' => 1,
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'remark' => '数值越小越靠前',
          'is_show' => 1,
      ],
      'is_recommend' => [
          'title' => '是否推荐',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:否
1:是',
          'remark' => '推荐后的分类将在商城首页显示',
      ],
      'is_show' => [
          'title' => '是否显示',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:不显示
1:显示',
          'value' => 1,
      ],
      'path' => [
          'title' => '分类路径',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'num',
          'is_show' => 4,
      ]
  ];
}