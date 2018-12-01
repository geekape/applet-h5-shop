<?php
/**
 * LotteryGames数据模型
 */
class LotteryGamesTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lottery_games',
      'title' => '抽奖游戏',
      'search_key' => 'title:请输入活动名称搜索',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '序号',
          'come_from' => 0,
          'width' => 4,
          'is_sort' => 0
      ],
      'title' => [
          'title' => '活动名称',
          'come_from' => 0,
          'width' => 8,
          'is_sort' => 0
      ],
      'game_type' => [
          'title' => '游戏类型',
          'come_from' => 0,
          'width' => 8,
          'is_sort' => 0
      ],
      'start_time' => [
          'title' => '开始时间',
          'come_from' => 0,
          'width' => 12,
          'is_sort' => 0
      ],
      'end_time' => [
          'title' => '结束时间',
          'come_from' => 0,
          'width' => 12,
          'is_sort' => 0
      ],
      'status' => [
          'title' => '活动状态',
          'come_from' => 0,
          'width' => 7,
          'is_sort' => 0
      ],
      'attend_num' => [
          'title' => '参与人数',
          'come_from' => 0,
          'width' => 7,
          'is_sort' => 0
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => 20,
          'is_sort' => 0,
          'href' => [
              '0' => [
                  'title' => '中奖人列表',
                  'url' => 'draw/LuckyFollow/games_lucky_lists&games_id=[id]'
              ],
              '1' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '2' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ],
              '3' => [
                  'title' => '预览',
                  'url' => 'preview&games_id=[id]'
              ],
              '4' => [
                  'title' => '复制链接',
                  'url' => 'draw/Wap/index&games_id=[id]'
              ],
              '5' => [
                  'title' => '统计分析',
                  'url' => 'draw/Games/statistics&games_id=[id]'
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
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'keyword' => [
          'title' => '微信关键词',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'placeholder' => '请输入内容'
      ],
      'game_type' => [
          'title' => '游戏类型',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '1:刮刮乐
2:大转盘
3:砸金蛋
4:九宫格',
          'placeholder' => '请输入内容'
      ],
      'start_time' => [
          'title' => '开始时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'end_time' => [
          'title' => '结束时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'status' => [
          'title' => '状态',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '1:开启
0:禁用',
          'placeholder' => '请输入内容'
      ],
      'day_attend_limit' => [
          'title' => '每人每天抽奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0，则不限制，超过此限制点击抽奖，系统会提示“您今天的抽奖次数已经用完!”',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'attend_limit' => [
          'title' => '每人总共抽奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0，则不限制；否则必须>=每人每天抽奖次数，超过此限制点击抽奖，系统会提示“您的所有抽奖次数已用完!”',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'day_win_limit' => [
          'title' => '每人每天中奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0，则不限制，超过此限制点击抽奖，抽奖者将无概率中奖',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'win_limit' => [
          'title' => '每人总共中奖次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0，则不限制；否则必须>=每人每天中奖次数，超过此限制点击抽奖，抽奖者将无概率中奖',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'day_winners_count' => [
          'title' => '每天最多中奖人数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '0，则不限制，超过此限制时，系统会提示“今天奖品已抽完，明天再来吧!”',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'remark' => [
          'title' => '活动说明',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'cover' => [
          'title' => '封面图片',
          'type' => 'picture',
          'field' => 'int(10) UNSIGNED NULL',
          'remark' => '建议上传100*100的图片,用于转发后的封面图',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'manager_id' => [
          'title' => '管理员id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'url' => [
          'title' => '关注链接',
          'field' => 'varchar(300) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'attend_num' => [
          'title' => '参与总人数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'template' => [
          'title' => '模板',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'placeholder' => '请输入内容'
      ],
      'need_subscribe' => [
          'title' => '关注公众号才能参与',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:否
1:是',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'need_member' => [
          'title' => '成为会员才能参与',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:否
1:是',
          'remark' => '若奖品有优惠券，请设置为\'是\'。只有成为会员的用户才能领取优惠券',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'show_prize_num' => [
          'title' => '是否显示奖品数量',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:不显示
1:显示',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'show_winner' => [
          'title' => '是否显示中奖记录',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:不显示
1:显示',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'show_prize_details' => [
          'title' => '是否显示奖品详情',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:不显示
1:显示',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'winning_mess_img' => [
          'title' => '中奖推送消息封面图片',
          'type' => 'picture',
          'field' => 'int(10) UNSIGNED NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容',
          'remark' => '建议上传100*100的图片'
      ],
      'winning_mess_text' => [
          'title' => '中奖推送消息封面描述',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'remark' => '不超过200个字',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'winning_money_text' => [
          'title' => '抽中现金红包推送消息',
          'type' => 'string',
          'field' => 'text NULL',
          'remark' => '使用 {val} 代替中奖金额值',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'winning_score_text' => [
          'title' => '抽中积分推送消息',
          'type' => 'string',
          'field' => 'text NULL',
          'remark' => '使用 {val} 代替中奖积分值',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'current_draw_num' => [
          'title' => '当前抽奖次数',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'placeholder' => '请输入内容'
      ],
      'draw_count' => [
          'title' => '活动抽奖总次数',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'remark' => '在该次数内把所有奖品抽完，若等于或小于0则不抽中奖品，等于奖品总数量则每次抽奖都能中奖',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'win_num_list' => [
          'title' => '抽中奖品对应次数列表',
          'type' => 'textarea',
          'field' => 'longtext NULL',
          'placeholder' => '请输入内容'
      ]
  ];
}