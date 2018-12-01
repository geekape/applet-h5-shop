<?php
/**
 * Scratch数据模型
 */
class ScratchTable {
    // 数据表模型配置
    public $config = [
      'name' => 'scratch',
      'title' => '刮刮卡',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'scratch'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '刮刮卡ID',
      ],
      'keyword' => [
          'title' => '关键词',
      ],
      'title' => [
          'title' => '标题',
      ],
      'collect_count' => [
          'title' => '获取人数',
      ],
      'cTime' => [
          'title' => '发布时间',
          'function' => 'time_format',
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
              ],
              '2' => [
                  'title' => '中奖管理',
                  'url' => 'Sn/lists?target_id=[id]&target=_blank'
              ],
              '3' => [
                  'title' => '奖品管理',
                  'url' => 'Prize/lists?target_id=[id]&target=_blank'
              ],
              '4' => [
                  'title' => '预览',
                  'url' => 'preview?id=[id]&target=_blank'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(100) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'title' => [
          'title' => '标题',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'intro' => [
          'title' => '封面简介',
          'field' => 'text NULL',
          'type' => 'editor',
          'is_show' => 1
      ],
      'cover' => [
          'title' => '封面图片',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'remark' => '可为空',
          'is_show' => 1
      ],
      'use_tips' => [
          'title' => '使用说明',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'textarea',
          'remark' => '用户获取刮刮卡后显示的提示信息',
          'is_show' => 1
      ],
      'start_time' => [
          'title' => '开始时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'end_time' => [
          'title' => '结束时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'end_tips' => [
          'title' => '过期说明',
          'field' => 'text NULL',
          'type' => 'textarea',
          'remark' => '活动过期或者结束说明',
          'is_show' => 1
      ],
      'end_img' => [
          'title' => '过期提示图片',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'remark' => '可为空',
          'is_show' => 1
      ],
      'predict_num' => [
          'title' => '预计参与人数',
          'field' => 'int(10) unsigned NOT NULL ',
          'type' => 'num',
          'remark' => '预计人数直接影响抽奖概率：中奖概率 = 奖品总数/(预估活动人数*每人抽奖次数) 要确保100%中奖可设置为1',
          'is_show' => 1
      ],
      'max_num' => [
          'title' => '每人最多允许抽奖次数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'remark' => '0表示不限制数量',
          'is_show' => 1
      ],
      'follower_condtion' => [
          'title' => '粉丝状态',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'remark' => '粉丝达到设置的状态才能获取',
          'is_show' => 1,
          'extra' => '0:不限制
1:已关注
2:已绑定信息
3:会员卡成员'
      ],
      'credit_conditon' => [
          'title' => '积分限制',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'remark' => '粉丝达到多少积分后才能领取，领取后不扣积分',
          'is_show' => 1
      ],
      'credit_bug' => [
          'title' => '积分消费',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'remark' => '用积分中的财富兑换、兑换后扣除相应的积分财富',
          'is_show' => 1
      ],
      'addon_condition' => [
          'title' => '插件场景限制',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '格式：[插件名:id值]，如[投票:10]表示对ID为10的投票投完才能领取，更多的说明见表单上的提示',
          'is_show' => 1
      ],
      'collect_count' => [
          'title' => '已领取人数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'view_count' => [
          'title' => '浏览人数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'template' => [
          'title' => '素材模板',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'cTime' => [
          'title' => '发布时间',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'update_time' => [
          'title' => '更新时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function'
      ]
  ];
}