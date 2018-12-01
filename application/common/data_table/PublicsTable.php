<?php
/**
 * Publics数据模型
 */
class PublicsTable {
    // 数据表模型配置
    public $config = [
      'name' => 'publics',
      'title' => '公众号管理',
      'search_key' => 'public_name',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '公众号ID',
          'name' => 'id',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'public_name' => [
          'title' => '公众号名称',
          'name' => 'public_name',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'wpid' => [
          'title' => 'wpid',
          'name' => 'wpid',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
      ],
      'count' => [
          'title' => '管理员数',
          'name' => 'count',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0,
          'come_from' => 0,
          'href' => [ ]
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
                  'title' => '进入管理',
                  'url' => 'main&public_id=[id]'
              ]
          ],
          'name' => 'urls',
          'function' => '',
          'width' => '',
          'is_sort' => 0,
          'raw' => 0
      ]
  ];

    // 字段定义
    public $fields = [
      'public_name' => [
          'title' => '公众号名称',
          'field' => 'varchar(50) NOT NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'public_id' => [
          'title' => '公众号原始id',
          'field' => 'varchar(100) NOT NULL',
          'type' => 'string',
          'remark' => '请正确填写，保存后不能再修改，且无法接收到微信的信息',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wechat' => [
          'title' => '微信号',
          'field' => 'varchar(100) NOT NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'headface_url' => [
          'title' => '公众号头像',
          'field' => 'varchar(255) NULL',
          'type' => 'picture',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'type' => [
          'title' => '公众号类型',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:普通订阅号
1:认证订阅号/普通服务号
2:认证服务号',
          'placeholder' => '请输入内容'
      ],
      'appid' => [
          'title' => 'AppID',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '应用ID',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'secret' => [
          'title' => 'AppSecret',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '应用密钥',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'encodingaeskey' => [
          'title' => 'EncodingAESKey',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '安全模式下必填',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'tips_url' => [
          'title' => '提示关注公众号的文章地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'GammaAppId' => [
          'title' => 'GammaAppId',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'GammaSecret' => [
          'title' => 'GammaSecret',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'public_copy_right' => [
          'title' => '版权信息',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'is_use' => [
          'title' => '是否为当前公众号',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'uid' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL ',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'interface_url' => [
          'title' => '接口地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'area' => [
          'title' => '地区',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'addon_status' => [
          'title' => '插件状态',
          'field' => 'text NULL',
          'type' => 'textarea',
          'extra' => '167:微信小程序CMS
168:用户反馈
169:导航管理
170:文档中心
171:WebSocket
173:小程序导航
196:应用市场
197:奖品库
200:微信管理
201:微信红包
202:有道职场
203:积分等级
207:微考试
208:扫码管理
209:一键绑定公众号
211:实物奖励
212:微信红包
217:微预约
218:微邀约
219:竞猜
220:在线问答
221:通用表单
223:互动游戏
226:微信卡券
227:微调研
228:短信服务
231:租房模块
235:投票
245:抽奖游戏
238:微测试
239:会员卡
240:工作授权
241:数据库字典
246:扫码管理
247:扫码管理
248:商城
249:微信用户中心
250:奖品库
251:微官网
252:微贺卡
253:帮拆礼包
254:微社区
255:抢答
',
          'placeholder' => '请输入内容'
      ],
      'is_audit' => [
          'title' => '是否审核',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'is_init' => [
          'title' => '是否初始化',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'domain' => [
          'title' => '自定义域名',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'is_bind' => [
          'title' => '是否为微信开放平台绑定账号',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'app_type' => [
          'title' => '类型',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:公众号
1:小程序
2:APP',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'check_file' => [
          'title' => '微信验证文件',
          'type' => 'file',
          'field' => 'int(10) UNSIGNED NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容',
          'validate_file_size' => 10485760
      ],
      'order_payok_messageid' => [
          'title' => '交易完成通知的模板ID',
          'type' => 'string',
          'field' => 'varchar(255) NULL',
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}