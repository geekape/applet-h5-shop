<?php
/**
 * LotteryGamesAwardLink数据模型
 */
class LotteryGamesAwardLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lottery_games_award_link',
      'title' => '抽奖游戏奖品设置',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'draw'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'award_id' => [
          'title' => '奖品id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'games_id' => [
          'title' => '抽奖游戏id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'grade' => [
          'title' => '中奖等级',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'num' => [
          'title' => '奖品数量',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'max_count' => [
          'title' => '最多抽奖',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => 'n次,把奖品发放完, 不能小于奖品数量',
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
      'sort' => [
          'title' => '排序',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'placeholder' => '请输入内容'
      ],
      'unreal_num' => [
          'title' => '假的数量',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'value' => 0,
          'remark' => '不填写则显示真实数量',
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}