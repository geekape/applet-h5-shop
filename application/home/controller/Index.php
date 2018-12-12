<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Index extends Home
{
    public $sReqTimeStamp, $sReqNonce, $sEncryptMsg;

    // 系统首页
    public function index()
    {
        if (!HAS_INDEX) {
            $this->redirect('user/login');
        }
        $newsFields = 'id,title,content,cTime';
        $newsLists = D('solution/News')->getNewsLists([], $newsFields, 4);
        $this->assign('newsLists', $newsLists);
        // dump($newsLists);die;
        return $this->fetch();
    }

    // 产品下载
    public function product()
    {
        return $this->fetch();
    }

    // 培训帮助
    public function course()
    {
        return $this->fetch();
    }

    // 定制开发
    public function service()
    {
        return $this->fetch();
    }

    // 新闻详情
    public function news_detail()
    {
        $id = I('id', 0, 'intval');
        $news = D('solution/News')->findNews($id);

        $this->assign('news', $news);
        return $this->fetch();
    }

    // 插件库
    public function apps()
    {
        $lists = M('solution')->field('content', true)->order('sort asc,id desc')->select();
        $this->assign('lists', $lists);

        return $this->fetch();
    }

    // 插件详情
    public function apps_detail()
    {
        $id = input('id');
        if (empty($id)) {
            return $this->error('非法参数');
        }
        $info = M('solution')->where('id', $id)->find();
        if (!$info) {
            return $this->error('非法参数!');
        }
        $this->assign('info', $info);

        return $this->fetch();
    }

    function chat()
    {
        $data = input('post.');
        // add_debug_log($data, 'chat');
        $res = M('chat')->insert($data);
        if ($res) {
            // if (D('servicer/Servicer')->checkRule($data['to_uid'], 1)) {
            // 把消息通过微信客服推送到客服的公众号里
            $sUrl = U('shop/wap/service', array(
                'wpid' => $data ['wpid'],
                'from_uid' => $data ['uid'],
                'come_from' => 'public'
            ));
            $nickname = get_nickname($data ['uid']);

            $reply_text = '来自 ' . $nickname . ' 的客服消息：' . $data ['content'] . '，<a href="' . $sUrl . '">点击回复</a>';

            $res = D('common/Custom')->replyText($data ['to_uid'], $reply_text);
            // }
        }
        return 1;
    }

    // 扫码二维码绑定
    public function bind()
    {
        if (request()->isPost()) {
            // 检测是否扫码
            if (S('is_bind_wx_' . $this->mid) == 1) {
                S('is_bind_wx_' . $this->mid, null);
                echo json_encode(array(
                    'status' => 1,
                    'url' => U('home/index/main')
                ));
            } else {
                echo 0;
            }
        } else {
            // 显示二维码
            // 生成带UID的事件二维码
            // dump($_SESSION);
            get_wpid(config('DEFAULT_PUBLICS'));
            $qrCode = D('home/QrCode')->add_qr_code('QR_SCENE', 'ScanBindLogin', $this->mid);
            $this->assign('qrCode', $qrCode);
            $url = cookie('__forward__');
            if ($url) {
                cookie('__forward__', null);
            } else {
                $url = U('home/Index/main');
            }
            $this->assign('skip_url', $url);
            // echo $url;echo 3333;exit;
            return $this->fetch();
        }
    }

    // 通用清缓存
    public function generalClean()
    {
        // getModelByName_comment
        $key = I('key');
        dump($key);
        $d = S($key);
        dump($d);
        if (isset ($_GET ['do_clean'])) {
            dump(S($key, null));
        }
    }

    // 管理员预览时初始化粉丝信息
    public function bind_follow()
    {
        $publicid = $map ['publicid'] = I('publicid');
        $uid = $map ['uid'] = I('uid');
        $this->assign($map);

        $info = M('user_follow')->where(wp_where($map))->find();

        $is_ajax = I('is_ajax', 0);
        if ($is_ajax) {
            if ($info ['follow_id'] > 0) {
                session('follow_id', $info ['follow_id']);
            }
            echo $info ['follow_id'];
            exit ();
        } elseif ($info ['follow_id']) {
            // $url = cookie( '__forward__' );
            // cookie( '__forward__', null );
            // if (strpos ( $url, SITE_DOMAIN ) === false) {
            // $url = HTTP_PREFIX . SITE_DOMAIN . $url;
            // }
            // return redirect( $url );
            // exit ();
        }

        $data ['url'] = cookie('__preview_url__');
        if ($info) {
            M('user_follow')->where(wp_where($map))->update($data);
        } else {
            $data ['uid'] = $this->mid;
            $data ['publicid'] = $publicid;
            $info ['id'] = M('user_follow')->insertGetId($data);
        }

        $url = U('Wecome/Wap/bind_follow', array(
            'publicid' => $publicid,
            'uid' => $uid
        ));

        $this->assign('url', $url);
        return $this->fetch();
    }

    public function bind_follow_after()
    {
        $url = cookie('__forward__');
        cookie('__forward__', null);
        if (strpos($url, SITE_DOMAIN) === false) {
            $url = HTTP_PREFIX . SITE_DOMAIN . $url;
        }
        return redirect($url);
    }

    // 接入指引
    public function lead()
    {
        $pbid = get_pbid();
        if ($pbid) {
            $this->assign('id', $pbid);
        }
        return $this->fetch();
    }

    // 系统帮助
    public function help()
    {
        return $this->fetch();
    }

    // 系统关于
    public function about()
    {
        return $this->fetch();
    }

    // 问答及说明
    public function question()
    {
        return $this->fetch();
    }

    // 授权协议
    public function license()
    {
        return $this->fetch();
    }

    // 下载weiphp
    public function downloadFile()
    {
        M('config')->where('name="DOWNLOAD_COUNT"')->setInc('value');
        return redirect('http://down.app.weiphp.cn/weiphp.zip');
    }

    // 远程获取最新版本号
    public function update_version()
    {
        die (M('update_version')->value("max(`version`)"));
    }

    // 远程获取升级包信息
    public function update_json()
    {
        $old_version = intval($_REQUEST ['version']);
        $new_version = M('update_version')->value("max(`version`)");

        $res = [];
        if ($old_version < $new_version) {
            $res = M('update_version')->field('version,title,description,create_date')->where('version>' . $old_version)->select();
        }

        die (json_encode($res));
    }

    // 下载升级包
    public function download_update_package()
    {
        $map ['version'] = intval($_REQUEST ['version']);
        $package = M('update_version')->where(wp_where($map))->value('package');
        if (empty ($package)) {
            $this->error('下载的文件不存在或已被移除');
        }
        M('update_version')->where(wp_where($map))->setInc('download_count');

        return redirect($package);
    }

    public function main()
    {
        if (!is_login() && IS_GET) {
            $forward = cookie('__forward__');
            empty ($forward) && cookie('__forward__', $_SERVER ['REQUEST_URI']);
            $url = U('home/user/login', array(
                'from' => 3
            ));
            return redirect($url);
        }
        // 切换公众号时防止老的__forward__影响
        if (cookie('__forward__')) {
            cookie('__forward__', null);
        }
        if ($GLOBALS ['myinfo'] ['level'] == 4) {
            $url = U('Shop/Shop/summary', [
                'wpid' => $GLOBALS ['myinfo'] ['wpid']
            ]);
        } else {
            $url = U('weixin/publics/lists');
        }
        return redirect($url);

        $map ['status'] = 1;
        $data = M('apps')->where(wp_where($map))->order('id DESC')->select();
        $wpid_status = D('common/AddonStatus')->getList(true);
        foreach ($data as $k => &$vo) {
            if (isset ($wpid_status [$vo ['name']]) && $wpid_status [$vo ['name']] === '-1') {
                unset ($data [$k]);
            }

            $name = parse_name($vo ['name']);
            $app_icon = env('app_path') . $name . '/icon.png';
            if (file_exists($app_icon)) {
                $vo ['app_icon'] = SITE_URL . '/application/' . $name . '/icon.png';
            } else {
                $vo ['app_icon'] = SITE_URL . '/static/base/images/app_no_pic.png';
            }
            // 连接
            if ($vo ['has_adminlist']) {
                $vo ['addons_url'] = U($vo ['name'] . '/' . $vo ['name'] . '/lists');
            } elseif (file_exists(env('app_path') . $vo ['name'] . '/config.php')) {
                $vo ['addons_url'] = U($vo ['name'] . '/' . $vo ['name'] . '/config');
            } else {
                $vo ['addons_url'] = U($vo ['name'] . '/' . $vo ['name'] . '/nulldeal');
            }
        }
        $this->assign('list_data', $data);

        // 自动同步微信用户
        config('USER_LIST') && $this->_autoUpdateUser();
        config('USER_GROUP') && $this->_updateWechatGroup();

        // 用户统计
        unset ($map ['status']);
        $px = DB_PREFIX;
        $map ['f.pbid'] = get_pbid();
        $map ['f.has_subscribe'] = 1;
        $count ['total'] = M('public_follow')->alias('f')->where(wp_where($map))->count();

        $time = strtotime(date('Y-m-d'));
        $map ['u.reg_time'] = array(
            'gt',
            $time
        );
        $count ['today'] = M()->table($px . 'public_follow')->alias('f')->join($px . 'user u', 'f.uid=u.uid')->where(wp_where($map))->count();
        $map ['u.reg_time'] = array(
            'gt',
            $time - 86400
        );
        $count ['yestoday'] = M()->table($px . 'public_follow')->alias('f')->join($px . 'user u', 'f.uid=u.uid')->where(wp_where($map))->count();
        $count ['yestoday'] = $count ['yestoday'] - $count ['today'];
        $this->assign('count', $count);

        return $this->fetch();
    }

    public function union()
    {
        return $this->fetch();
    }

    public function _autoUpdateUser()
    {
        // 获取openid列表
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . get_access_token(); // 只取第一页数据
        $data = wp_file_get_contents($url);
        $data = json_decode($data, true);
        if (!isset ($data ['count']) || $data ['count'] == 0) {
            return false;
        }

        $map ['openid'] = array(
            'in',
            $data ['data'] ['openid']
        );
        $map ['pbid'] = $save ['pbid'] = get_pbid();

        $openids = M('public_follow')->where(wp_where($map))->column('openid');

        $diff = array_diff(( array )$data ['data'] ['openid'], ( array )$openids);
        if (empty ($diff)) {
            // 没有需要同步的用户
            return false;
        }

        foreach ($diff as $oid) {
            $param ['user_list'] [] = array(
                'openid' => $oid,
                'lang' => 'zh-CN'
            );
            $openids [] = $oid;
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . get_access_token();
        $data = post_data($url, $param);
        if (empty ($data ['user_info_list'])) {
            return false;
        }

        $userDao = D('common/User');
        $config = getAddonConfig('UserCenter');
        if (isset ($_GET ['test'])) {
            dump($config);
            exit ();
        }
        foreach ($data ['user_info_list'] as $u) {
            if ($u ['subscribe'] == 0) {
                continue;
            }

            $u ['score'] = intval($config ['score']);
            $u ['reg_time'] = $u ['subscribe_time'];
            $u ['status'] = 1;
            $u ['is_init'] = 1;
            $u ['is_audit'] = 1;

            $uid = D('common/User')->addUser($u);

            $save ['openid'] = $u ['openid'];
            $save ['uid'] = $uid;
            $save ['syc_status'] = 2;
            $save ['has_subscribe'] = 1;
            $res = M('public_follow')->insertGetId($save);
        }
    }

    // 与微信的用户组保持同步
    public function _updateWechatGroup()
    {
        // 先取当前用户组数据
        $map ['wpid'] = get_wpid();
        $map ['manager_id'] = $this->mid;
        $map ['type'] = 1;
        $group_list = M('auth_group')->where(wp_where($map))->column('id', 'wechat_group_id');

        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . get_access_token();
        $data = wp_file_get_contents($url);
        $data = json_decode($data, true);
        foreach ($data ['groups'] as $d) {
            if (isset ($group_list [$d ['id']])) {
                continue;
            }

            $map ['wechat_group_id'] = $d ['id'];
            $map ['wechat_group_name'] = $d ['name'];
            $map ['wechat_group_count'] = $d ['count'];

            // 增加本地数据
            $map ['title'] = $d ['name'];
            $map ['qr_code'] = '';

            M('auth_group')->insert($map);
        }
    }

    public function setStatus()
    {
        $addon = I('addon');
        $wpid_status = D('common/AddonStatus')->getList();

        if ($wpid_status [$addon] === '-1') {
            $this->success('无权限设置');
        }

        $status = 1 - I('status');
        $res = D('common/AddonStatus')->set($addon, $status);
        $this->success('设置成功');
    }

    // 宣传页面
    public function leaflets()
    {
        $config = getAddonConfig('Leaflets');

        if (!empty ($config)) {
            $config ['img'] = is_numeric($config ['img']) ? get_cover_url($config ['img']) : SITE_URL . '/leaflets/qrcode_default.jpg';
            $this->assign('config', $config);
        } else {
            $this->error('请先保存宣传页的配置');
        }
        define('ADDON_PUBLIC_PATH', env('app_path') . 'Leaflets');
        return $this->fetch(SITE_PATH . '/application/leaflets/view/Leaflets/show.html');
    }

    // 定时任务调用入口
    public function cron()
    {
        $time = date('Y-m-d H:i:s');
        trace($time, 'debug');

        // 拼团失败发送模板消息,每分钟触发一次
        if ($this->cron_lock('CollageGroup', 60)) {
            D('collage/CollageGroup')->cronFreeGroup();
        }

        // 拼团中凑团机器人5秒触发一次
        if ($this->cron_lock('CollageRobot', 5)) {
            D('collage/CollageRobot')->auto_join();
        }

        // 超时订单库存处理，建议一分钟执行一次
        if ($this->cron_lock('Stock', 60)) {
            D('shop/Stock')->cronDealOrderStock();
        }

        // 会员有礼（会员生日或节日）
        if ($this->cron_lock('cronCardCustom', 120)) {
            D('card/CardCustom')->do_send_crons();
            // 会员有礼 模板消息通知
            D('card/CardCustom')->cronsSendTplMessage();
        }

        // 发现金红包
        if ($this->cron_lock('cronTransfer', 60)) {
            $this->cronTransfer();
        }

        // 每次更新一个公众号的会员信息 和 用户积分
        if ($this->cron_lock('cronUpdateMember', 20)) {
            $this->cronUpdateMember();
        }

        // 系统自动完成15天后订单设置为已收货或已评价（不用整分是尽量避免太多任务同时执行）
        if ($this->cron_lock('autoSetFinish', 93)) {
            D('shop/Order')->autoSetFinish();
        }
        // 通过微信支付订单查询功能自动更新那些已支付的但系统里没有成功设置为已支付状态的订单，5分5秒执行一次
        if ($this->cron_lock('refreshPayStatus', 305)) {
            D('shop/Order')->refreshPayStatus();
        }
    }

    function cron_lock($tag, $time)
    {
        $key = 'cron_lock_' . $tag;
        $last_time = S($key);
        if ($last_time === false || $last_time <= (NOW_TIME - $time)) {
            S($key, NOW_TIME);
            return true;
        } else {
            return false;
        }
    }

    public function getFooterHtml()
    {
        $wpid = $map ['wpid'] = I('wpid');
        $temp = I('temp');

        $config = getAddonConfig('WeiSite');
        $config ['cover_url'] = get_cover_url($config ['cover']);
        $config ['background_id'] = $config ['background'];
        $config ['background'] = get_cover_url($config ['background']);
        $this->config = $config;
        $this->assign('config', $config);
        // dump ( $config );
        // dump(get_wpid());

        // 定义模板常量
        define('CUSTOM_TEMPLATE_PATH', env('app_path') . 'wei_site/view/Template');

        $list = D('WeiSite/Footer')->get_list($map);

        $one_arr = [];
        foreach ($list as $k => $vo) {
            if ($vo ['pid'] != 0) {
                continue;
            }

            $one_arr [$vo ['id']] = $vo;
            unset ($list [$k]);
        }

        foreach ($one_arr as &$p) {
            $two_arr = [];
            foreach ($list as $key => $l) {
                if ($l ['pid'] != $p ['id']) {
                    continue;
                }

                $two_arr [] = $l;
                unset ($list [$key]);
            }

            $p ['child'] = $two_arr;
        }
        $this->assign('footer', $one_arr);
        $html = $this->fetch(env('app_path') . 'wei_site/view/template_footer/' . $temp . '/footer.html');
        echo $html;
    }

    // 跳转页面
    public function jump()
    {
        return $this->fetch();
    }

    // 发现金红包
    function cronTransfer()
    {
        // addWeixinLog(111,'222');
        // 给还没发送成功的用户现次发红包
        // $fmap ['status'] = 1;
        $list = M('redbag_recode')->where('status', 1)->limit(50)->select();
        if (!empty ($list)) {
            $redbagDao = D('Common/Transfer');
            // $paymentDao = D ( 'Addons://Payment/PaymentOrder' );
            foreach ($list as $recode) {
                $key = 'send_redbag_lock_' . $recode ['uid'];
                $is_lock = S($key);

                if ($is_lock == 1) {
                    continue;
                }

                $map ['id'] = $recode ['id'];
                $msgData = $redbagDao->do_send($recode, false);
                addWeixinLog($msgData, 'cron_send_redbag');

                // $msgData = $redbagDao ->sendRedBag ( $info, $recode ['follow_id'], $recode );
                if ($msgData ['status'] == 0) {
                    // 把错误原因推送给管理员
                    // send_error_to_admin ( $msgData ['msg'] . ', recode_id 为' . $recode ['id'], 'cron_send_redbag' );
                } else {
                    S($key, 1, 60); // 锁定1分钟，实现微信的规则：◆ 同一个商户号，每分钟最多给同一个用户发送一个红包；
                }
            }
        }
        unset ($map);
        // D ( 'Home/Cron' )->run ();
        echo date('Y-m-d H:i:s') . "\r\n";
    }

    // 批量更新顾客基本资料(积分、等级)
    function cronUpdateMember()
    {
        // 获取所有公众号
        $key = 'cronUpdateMember_wpid';
        $wpidArr = S($key);
        if (empty ($wpidArr)) {
            $wpidArr = M('publics')->column('id');
            foreach ($wpidArr as $k => $wpid) {
                $config = getAddonConfig('shop', $wpid);
                if (empty ($config ['erp_appid']) || empty ($config ['erp_appsecret'])) {
                    unset ($wpidArr [$k]);
                    continue;
                }
            }
            S($key, $wpidArr);
        }
        // 有对接ERP的公众号
        // 每次更新一个公众号的会员
        $wpid = current($wpidArr);
        $firstKey = key($wpidArr);
        unset ($wpidArr [$firstKey]);
        S($key, $wpidArr);
        if (empty ($wpid)) {
            return '';
        }
        // 获取当前公众号的等级列表
        $levelArr = M('card_level')->where('wpid', $wpid)->column('level,id');
        if (empty ($levelArr)) {
            // 拉取ERP的等级，并保存
            $erpLevel = D('shop/ErpApi')->getMemberStructure($wpid);
            $level = [];
            foreach ($erpLevel as $vo) {
                $level [] = array(
                    'level' => $vo ['membername'],
                    'start_score' => $vo ['startmarks'],
                    'score' => $vo ['endmarks'],
                    'wpid' => $wpid,
                    'update_time' => time()
                );
            }
            if (!empty ($level)) {
                M('card_level')->insertAll($level);
                $levelArr = M('card_level')->where('wpid', $wpid)->column('level,id');
            }
        }
        // 获取当前公众号的会员，openid
        $memberArr = M('card_member')->where('wpid', $wpid)->where('erp_customer_id', 'gt', 0)->column('erp_customer_id,id,phone,uid');
        $uidArr = getSubByKey($memberArr, 'uid');
        $openidArr = M('public_follow')->where('pbid', $wpid)->where('uid', 'in', $uidArr)->column('uid,openid');
        $num = 0;
        $openidStr = '';
        foreach ($openidArr as $uid => $openid) {
            $openidStr .= "'" . $openid . "',";
            $num++;
            if ($num >= 2000) {
                // 每次查两千个
                $this->_do_save_member($openidStr, $wpid, $memberArr, $levelArr);
                $num = 0;
                $openidStr = '';
            }
        }
        if (!empty ($openidStr)) {
            $this->_do_save_member($openidStr, $wpid, $memberArr, $levelArr);
        }
        echo date('Y-m-d H:i:s') . "\r\n";
    }

    function _do_save_member($openidStr, $wpid, $memberArr, $levelArr = [])
    {
        $apiDao = D('shop/ErpApi');
        $userDao = D('common/User');
        // 每次查两千个
        $erpData = $apiDao->get_batchuserinfo($openidStr, $wpid);
        foreach ($erpData as $vo) {
            if (isset ($memberArr [$vo ['customerid']])) {
                $save = [];
                if (isset ($vo ['memberid'])) {
                    $save ['number'] = $vo ['memberid'];
                }
                if (isset ($vo ['mobile'])) {
                    $save ['phone'] = $vo ['mobile'];
                }
                if (isset ($vo ['custtype'])) {
                    $save ['level_name'] = $vo ['custtype'];
                }
                if (isset ($levelArr [$vo ['custtype']])) {
                    $save ['level'] = $levelArr [$vo ['custtype']];
                }
                if (isset ($vo ['customername'])) {
                    $save ['username'] = $vo ['customername'];
                }
                if (isset ($vo ['gender'])) {
                    $save ['sex'] = $vo ['gender'] == '男' ? 1 : 2;
                }
                if (isset ($vo ['birth'])) {
                    $save ['birthday'] = strtotime($vo ['birth']);
                }
                if (isset ($vo ['marryday'])) {
                    $save ['marryday'] = strtotime($vo ['marryday']);
                }
                if (isset ($vo ['availablemarks'])) {
                    // 用户积分
                    $score ['score'] = $vo ['availablemarks'];
                    $userDao->updateInfo($memberArr [$vo ['customerid']] ['uid'], $score);
                }
                if (!empty ($save)) {
                    M('card_member')->where('id', $memberArr [$vo ['customerid']] ['id'])->update($save);
                }
            }
        }
    }

    function copy()
    {
        $url = urldecode(input('text'));

        $list = D('common/Publics')->where('app_type', 0)->field('id,public_name')->select();
        foreach ($list as &$vo) {
            $vo ['url'] = preg_replace('/pbid=([\d]+)/', 'pbid=' . $vo ['id'], $url);
            $vo ['url'] = preg_replace('/pbid\/([\d]+)/', 'pbid/' . $vo ['id'], $vo ['url']);
        }

        $this->assign('lists', $list);

        return $this->fetch();
    }

    //获取支付的二维码
    public function remote_scan_pay()
    {
        $default_no = date('YmdHis') . uniqid();
        $product_id = I('product_id', $default_no);
        $out_trade_no = I('out_trade_no', $default_no);
        $total_fee = I('total_fee', 100, 'intval');
        $body = I('body', '圆梦云-购买服务');

        $info = get_pbid_appinfo();
        $appid = $info['appid'];
        $product = [
            'product_id' => $product_id,
            'body' => $body,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
        ];
        $callback = 'Home/Service/remoteScanPayOk';
        $type = 2; //模式一
        $scan = D('weixin/Payment')->scan_pay($appid, $product, $callback, $type);
        $scan['out_trade_no'] = $out_trade_no;
        echo json_url($scan);
    }

    //获取支付的二维码
    public function remote_scan_pay_ckeck()
    {

        $out_trade_no = I('out_trade_no');
        $map['out_trade_no'] = I('out_trade_no');
        $payment = M('payment')->where($map)->find();

        echo json_url($payment);
    }

    public function remote_send_msg()
    {
        $all = $content = file_get_contents('php://input');

        $notice = json_decode($all, true);

        $uids = $notice['uids'];

        $uidArr = explode(',', $uids);

        $title = $notice['title'];
        $status_title = $notice['status_title'];
        $level_title = $notice['level_title'];
        $deal_user = $notice['deal_user'];
        $tester = $notice['tester'];
        $remark = $notice['remark'];
        $url = $notice['url'];
        foreach ($uidArr as $uid) {
            $res = D('common/TemplateMessage')->bugNotice($uid, $title, $status_title, $level_title, $deal_user, $tester, $remark, $url);
        }

        echo json_url($res);
    }

    function trigger()
    {
        //增加触发器
        $dbconfig = config('database.');
        $dbms = 'mysql';     //数据库类型
        $host = $dbconfig['hostname']; //数据库主机名
        $dbName = $dbconfig['database'];    //使用的数据库
        $dbport = $dbconfig['hostport'];
        $dsn = "$dbms:host=$host;port=$dbport;dbname=$dbName";
        $new_db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
        $new_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        dump($host);
        dump($dbName);

        dump($new_db->exec('DROP TRIGGER IF EXISTS `add`'));
        dump($new_db->exec("CREATE TRIGGER `add` BEFORE INSERT ON `wp_shop_goods_stock` FOR EACH ROW set new.stock_active = new.stock - new.lock_count"));
        dump($new_db->exec('DROP TRIGGER IF EXISTS `save`'));
        dump($new_db->exec("CREATE TRIGGER `save` BEFORE UPDATE ON `wp_shop_goods_stock` FOR EACH ROW set new.stock_active = new.stock - new.lock_count"));
    }
}
