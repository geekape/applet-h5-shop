<?php
/**
 * UpdateScoreLog数据模型
 */
class UpdateScoreLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'update_score_log',
      'title' => '修改积分记录',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'score' => [
          'title' => '修改积分',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'branch_id' => [
          'title' => '修改门店',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'operator' => [
          'title' => '操作员',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'cTime' => [
          'title' => '修改时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'is_show' => 1,
          'auto_rule' => 'get_wpid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'member_id' => [
          'title' => '会员卡id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 4
      ],
      'manager_id' => [
          'title' => '管理员id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ]
  ];
}