<?php
/**
 * LotteryPrizeList数据模型
 */
class LotteryPrizeListTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lottery_prize_list',
      'title' => '抽奖奖品列表',
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
      'sports_id' => [
          'title' => '比赛场次',
      ],
      'award_id' => [
          'title' => '奖品名称',
      ],
      'award_num' => [
          'title' => '奖品数量',
      ],
      'id' => [
          'title' => '编辑:[EDIT]|编辑,[DELETE]|删除,add?sports_id=[sports_id]|添加',
      ]
  ];

    // 字段定义
    public $fields = [
      'sports_id' => [
          'title' => '活动编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'award_id' => [
          'title' => '奖品编号',
          'field' => 'varchar(255) NULL',
          'type' => 'cascade',
          'is_show' => 1
      ],
      'award_num' => [
          'title' => '奖品数量',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
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