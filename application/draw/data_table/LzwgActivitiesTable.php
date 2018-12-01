<?php
/**
 * LzwgActivities数据模型
 */
class LzwgActivitiesTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_activities',
      'title' => '靓妆活动',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '活动名称',
      ],
      'remark' => [
          'title' => '活动说明',
      ],
      'logo_img' => [
          'title' => '活动LOGO',
          'function' => 'get_img_html',
          'raw' => 1,
      ],
      'activitie_time' => [
          'title' => '活动时间',
      ],
      'get_prize_tip' => [
          'title' => '中将提示信息',
      ],
      'no_prize_tip' => [
          'title' => '未中将提示信息',
      ],
      'comment_list' => [
          'title' => '评论列表',
      ],
      'set_vote' => [
          'title' => '设置投票',
      ],
      'set_award' => [
          'title' => '设置奖品',
      ],
      'get_prize_list' => [
          'title' => '中奖列表',
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
      'title' => [
          'title' => '活动名称',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'remark' => [
          'title' => '活动说明',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'logo_img' => [
          'title' => '活动LOGO',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
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
      'get_prize_tip' => [
          'title' => '中奖提示信息',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'no_prize_tip' => [
          'title' => '未中奖提示信息',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'lottery_number' => [
          'title' => '抽奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '每日允许用户抽奖的机会数，小于0 为无限次',
          'is_show' => 1
      ],
      'get_prize_count' => [
          'title' => '中奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '每用户是否允许多次中奖',
          'is_show' => 1
      ],
      'comment_status' => [
          'title' => '评论是否需要审核',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:不审核
1:审核'
      ],
      'ctime' => [
          'title' => '活动创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function'
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