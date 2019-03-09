<?php
/**
 * LzwgVote数据模型
 */
class LzwgVoteTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_vote',
      'title' => '投票',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '题目编号',
      ],
      'title' => [
          'title' => '题目名称',
      ],
      'vote_option' => [
          'title' => '题目选项',
      ],
      'type' => [
          'title' => '类型',
      ],
      'vote_count' => [
          'title' => '投票数',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]&id=[id]'
              ],
              '1' => [
                  'title' => '投票记录',
                  'url' => 'showLog&id=[id]'
              ],
              '2' => [
                  'title' => '选项票数',
                  'url' => 'showCount&id=[id]'
              ],
              '3' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '投票标题',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'description' => [
          'title' => '投票描述',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'picurl' => [
          'title' => '封面图片',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'remark' => '支持JPG、PNG格式，较好的效果为大图360*200，小图200*200',
          'is_show' => 1
      ],
      'start_date' => [
          'title' => '开始日期',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'end_date' => [
          'title' => '结束日期',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'template' => [
          'title' => '素材模板',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'vote_type' => [
          'title' => '题目类型',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:投票
1:调查'
      ],
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'remark' => '用户在微信里回复此关键词将会触发此投票。',
          'validate_type' => 'function',
          'validate_rule' => 'keyword_unique',
          'validate_time' => 1,
          'error_info' => '此关键词已经存在，请换成别的关键词再试试'
      ],
      'type' => [
          'title' => '选择类型',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'extra' => '0:单选
1:多选'
      ],
      'is_img' => [
          'title' => '文字/图片投票',
          'field' => 'tinyint(2) NULL',
          'type' => 'radio',
          'extra' => '0:文字投票
1:图片投票'
      ],
      'vote_count' => [
          'title' => '投票数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num'
      ],
      'cTime' => [
          'title' => '投票创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'mTime' => [
          'title' => '更新时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ]
  ];
}