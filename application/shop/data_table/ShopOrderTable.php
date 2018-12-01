<?php

/**
 * ShopOrder数据模型
 */
class ShopOrderTable
{

    // 数据表模型配置
    public $config = [
        'name' => 'shop_order',
        'title' => '订单记录',
        'search_key' => 'key:请输入订单编号或客户昵称',
        'add_button' => 0,
        'del_button' => 0,
        'search_button' => 1,
        'check_all' => 1,
        'list_row' => 20,
        'addon' => 'shop'
    ];

    // 列表定义
    public $list_grid = [
        'order_number' => [
            'title' => '订单编号',
            'width' => '15%',
            'name' => 'order_number',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'goods' => [
            'title' => '下单商品',
            'width' => '20%',
            'name' => 'goods',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'uid' => [
            'title' => '客户',
            'width' => '10%',
            'name' => 'uid',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'total_price' => [
            'title' => '总价',
            'width' => '7%',
            'name' => 'total_price',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'cTime' => [
            'title' => '下单时间',
            'function' => 'time_format',
            'width' => '17%',
            'name' => 'cTime',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'common' => [
            'title' => '支付类型',
            'width' => '10%',
            'name' => 'common',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'status_code' => [
            'title' => '订单跟踪',
            'width' => '10%',
            'name' => 'status_code',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'urls' => [
            'title' => '操作',
            'come_from' => 1,
            'width' => '11%',
            'href' => [
                '0' => [
                    'title' => '',
                    'url' => '操作'
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
        'uid' => [
            'title' => '用户id',
            'field' => 'int(10) UNSIGNED NOT NULL',
            'type' => 'num',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'order_number' => [
            'title' => '订单编号',
            'field' => 'varchar(255) NOT NULL',
            'type' => 'string',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'goods_datas' => [
            'title' => '商品序列化数据',
            'field' => 'text NOT NULL',
            'type' => 'textarea',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'remark' => [
            'title' => '备注',
            'field' => 'text NOT NULL',
            'type' => 'textarea',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'cTime' => [
            'title' => '订单时间',
            'field' => 'int(10) NOT NULL',
            'type' => 'datetime',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'total_price' => [
            'title' => '总价',
            'field' => 'decimal(10,2) NULL',
            'type' => 'num',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'address_id' => [
            'title' => '配送信息',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'is_send' => [
            'title' => '是否发货',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'send_code' => [
            'title' => '快递公司编号',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'send_number' => [
            'title' => '快递单号',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'send_type' => [
            'title' => '发货类型',
            'field' => 'char(10) NULL',
            'type' => 'radio',
            'is_show' => 1,
            'extra' => '1|邮寄
2|自提',
            'placeholder' => '请输入内容'
        ],
        'wpid' => [
            'title' => 'wpid',
            'type' => 'num',
            'field' => 'int(10) NULL',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'openid' => [
            'title' => 'OpenID',
            'field' => 'varchar(255) NOT NULL',
            'type' => 'string',
            'placeholder' => '请输入内容'
        ],
        'pay_status' => [
            'title' => '支付状态',
            'field' => 'int(10)  NULL',
            'type' => 'num',
            'extra' => '0:未支付
1:已支付
2:支付订金',
            'placeholder' => '请输入内容'
        ],
        'pay_type' => [
            'title' => '支付类型',
            'field' => 'tinyint(2) NULL',
            'type' => 'num',
            'placeholder' => '0:微信支付
90:积分支付'
        ],
        'is_new' => [
            'title' => '是否为新订单',
            'field' => 'tinyint(2) NULL',
            'type' => 'bool',
            'extra' => '0:否
1:是',
            'placeholder' => '请输入内容'
        ],
        'status_code' => [
            'title' => '订单跟踪状态码',
            'field' => 'char(50) NULL',
            'type' => 'select',
            'extra' => '0:待支付
1:待商家确认
2:待发货
3:配送中
4:确认已收货
5:确认已收款
6:待评价
7:已评价',
            'placeholder' => '请输入内容'
        ],
        'is_lock' => [
            'title' => '数量是否锁定',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1,
            'extra' => '1:锁定
0:释放',
            'placeholder' => '请输入内容'
        ],
        'erp_lock_code' => [
            'title' => 'ERP锁定商品编号',
            'field' => 'text NULL',
            'type' => 'textarea',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'mail_money' => [
            'title' => '邮费金额',
            'field' => 'float(10) NULL',
            'type' => 'num',
            'remark' => '0包邮',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'stores_id' => [
            'title' => '门店编号',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'pay_time' => [
            'title' => '支付时间',
            'field' => 'int(10) NULL',
            'type' => 'datetime',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'send_time' => [
            'title' => '发货时间',
            'field' => 'int(10) NULL',
            'type' => 'datetime',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'extra' => [
            'title' => '扩展参数',
            'field' => 'text NULL',
            'type' => 'textarea',
            'placeholder' => '请输入内容'
        ],
        'order_state' => [
            'title' => '订单状态',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'remark' => '异常信息可查看扩展字段信息extra',
            'extra' => '0:关闭
1:正常
2:异常',
            'placeholder' => '请输入内容'
        ],
        'out_trade_no' => [
            'title' => '支付的订单号',
            'type' => 'string',
            'field' => 'varchar(100) NULL',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'event_type' => [
            'title' => '订单来源',
            'field' => 'tinyint(2) NULL',
            'type' => 'num',
            'extra' => '0:商城
1:拼团
2:秒杀
3:砍价',
            'placeholder' => '请输入内容'
        ],
        'event_id' => [
            'title' => '活动ID',
            'type' => 'num',
            'field' => 'int(10) NULL',
            'is_show' => 1,
            'placeholder' => '请输入内容'
        ],
        'is_original' => [
            'title' => '活动中是否原价购买',
            'type' => 'bool',
            'field' => 'tinyint(2) NULL',
            'extra' => '0:否
1:是',
            'value' => 0,
            'is_show' => 0,
            'is_must' => 0
        ],
        'refund' => [
            'title' => '退款状态',
            'field' => 'tinyint(2) NULL',
            'type' => 'num',
            'extra' => '0:未退款
1:申请退款中
2:审核通过
3:审核不通过'
        ]
    ];
}