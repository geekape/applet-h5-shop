<?php

namespace app\weixin\model;

use app\home\model\Weixin;

/**
 * Wecome模型
 */
class WeixinAddon extends Weixin
{

    function reply($dataArr, $keywordArr = [])
    {
        if ($dataArr['Content'] == 'subscribe') {

            $config = D('common/PublicConfig')->getConfig('weixin', 'weixin_wecome'); // 获取后台插件的配置参数
            $uid = D('common/Follow')->init_follow($dataArr['FromUserName']);
            D('common/Follow')->set_subscribe($dataArr['FromUserName'], 1);
            // 增加积分
            session('mid_' . get_pbid(), $uid);
            // add_credit('subscribe');

            // //关注公众号获取会员卡号
            // D('Card/Card')->init_card_member($dataArr['FromUserName']);
            $has_return = false;
            if (!empty($dataArr['EventKey'])) {
                $has_return = $this->scan($dataArr, $keywordArr);
            }
            if (isset($config['stype']) && !empty($config['stype'])) {
                $this->material_reply($config['stype']);
            }
            if ($has_return) {
                return true;
            }
        } elseif ($dataArr['Content'] == 'scan') {
            $config = getAddonConfig('scan'); // 获取后台插件的配置参数
            $this->scan($dataArr, $keywordArr, $config);
        } elseif ($dataArr['Content'] == 'unsubscribe') {
            D('common/Follow')->set_subscribe($dataArr['FromUserName'], 0);
            // 增加积分
            $map1['openid'] = $dataArr['FromUserName'];
            $map1['pbid'] = get_pbid();
            $map2['uid'] = $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            $credit['uid'] = $uid;
            // add_credit('unsubscribe', $credit, 0);

            // 如果需要用户取消关系时系统自动物理删除该用户，把下面三行开启即可
            // M( 'public_follow' )->where ( wp_where( $map1 ) )->delete ();
            // M( 'user' )->where ( wp_where( $map2 ) )->delete ();
            // M( 'credit_data' )->where ( wp_where( $map2 ) )->delete ();
            session('mid_' . get_pbid(), null);

            $key = 'getUserInfo_' . $map2['uid'];
            S($key, null);
        } elseif ($dataArr['Content'] == '获取内测码') {
            $map['openid'] = $dataArr['FromUserName'];
            $code = M('invite_code')->where(wp_where($map))->value('code');
            if (!$code) {
                $code = $map['code'] = substr(uniqid(), -5);
                M('invite_code')->insert($map);
            }
            $this->replyText('您的内测码是：' . $code . ', 注意：内测码只能使用一次，再次注册时需要重新获取内测码');
        } elseif ($dataArr['Content'] == '自动检测') {

            $this->replyText('auto_check');
        } elseif (isset($dataArr['auto_reply'])) {

            $this->material_reply($dataArr['auto_reply']);
        } elseif (isset($dataArr['noAnswer'])) {

            $this->material_reply($dataArr['noAnswer']);
        }
    }

    function scan($dataArr, $keywordArr = [], $config = [])
    {

        $map['scene_id'] = ltrim($dataArr['EventKey'], 'qrscene_');
        $map['pbid'] = get_pbid();

        $qr = M('qr_code')->where(wp_where($map))->find();
        add_debug_log($qr, 'scan');

        if ($qr['addon'] == 'UserCenter') { // 设置用户分组
            $uid = $GLOBALS['mid'];
            if (empty($uid)) {
                $map1['openid'] = $dataArr['FromUserName'];
                $map1['pbid'] = get_pbid();
                $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            }
            $group = D('home/AuthGroup')->move_group($uid, $qr['aim_id']);

            $this->replyText('您已加入' . $group['title']);
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'QrAdmin') { // 扫码管理
            $qr_admin = M('qr_admin')->where('id', $qr['aim_id'])->find();

            $uid = $GLOBALS['mid'];
            if (empty($uid)) {
                $map1['openid'] = $dataArr['FromUserName'];
                $map1['pbid'] = get_pbid();
                $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            }

            // 加入用户组
            if (!empty($qr_admin['group_id'])) {
                D('home/AuthGroup')->move_group($uid, $qr_admin['group_id']);
            }

            // 增加用户标签
            if (!empty($qr_admin['tag_ids'])) {
                D('common/Tag')->addTags($uid, $qr_admin['tag_ids']);
            }
            D('common/User')->getUserInfo($uid, true);

            // 回复内容
            if (!empty($qr_admin['material'])) {
                $this->material_reply($qr_admin['material']);
                return true; // 告诉上面的关注方法，不需要再回复欢迎语了
            }
        } elseif ($qr['addon'] == 'Shop') {
            $savedata['openid'] = $map1['openid'] = $dataArr['FromUserName'];
            $map1['pbid'] = get_pbid();
            $followId = M('public_follow')->where(wp_where($map1))->value('uid');

            $savedata['duid'] = $qr['aim_id'];
            $savedata['uid'] = $followId;
            $res1 = M('shop_statistics_follow')->where(wp_where($map1))->value('id');
            if (!$res1) {
                $savedata['ctime'] = time();
                $savedata['wpid'] = get_wpid();
                M('shop_statistics_follow')->insert($savedata);
            }
        } elseif ($qr['addon'] == 'HelpOpen') {
            $user = getUserInfo($qr['extra_int']);
            $url = U('HelpOpen/Wap/index', array(
                'invite_uid' => $qr['extra_int'],
                'id' => $qr['aim_id']
            ));
            $this->replyText("关注成功，<a href='{$url}'>请点击这里继续帮{$user['nickname']}领取奖品</a>");
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'Draw') {
            $url = U('Draw/Wap/index', array(
                'games_id' => $qr['aim_id']
            ));
            $this->replyText("关注成功，<a href='{$url}'>请点击这里继续抽奖游戏</a>");
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'Stores') {
            // 门店二维码
            // 触发会员卡图文
            $config = getAddonConfig('Card'); // 获取后台插件的配置参数
            $articles[0] = array(
                'Title' => '点击进入免费领取微会员哦~',
                'Description' => $config['title'],
                'PicUrl' => SITE_URL . "/card/cover_pic.png",
                'Url' => U('card/Wap/member_center', array(
                    'pbid' => get_pbid()
                ))
            );
            $res = $this->replyNews($articles);
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'CardMember') {
            // 关注员工二维码
            $uid = $GLOBALS['mid'];
            $wpid = get_wpid();
            if (empty($uid)) {
                $map1['openid'] = $dataArr['FromUserName'];
                $map1['pbid'] = get_pbid();
                $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            }
            // 判断用户是否为会员，不是会员则锁定关系
            $memberId = M('card_member')->where('uid', $uid)
                ->where('wpid', $wpid)
                ->value('id');

            if (empty($memberId)) {
                // 未成为会员
                $hasLink = M('staff_follow_link')->where('uid', $uid)
                    ->where('wpid', $wpid)
                    ->value('id');
                $link['uid'] = $uid;
                $link['staff_id'] = $qr['aim_id'];
                $link['wpid'] = $wpid;
                $link['cTime'] = time();
                if ($hasLink) {
                    M('staff_follow_link')->where('id', $hasLink)->update($link);
                } else {
                    M('staff_follow_link')->insert($link);
                }
            }
            $config = getAddonConfig('Card'); // 获取后台插件的配置参数
            $articles[0] = array(
                'Title' => '点击进入免费领取微会员哦~',
                'Description' => $config['title'],
                'PicUrl' => SITE_URL . "/card/cover_pic.png",
                'Url' => U('card/Wap/member_center', array(
                    'pbid' => $pbid
                ))
            );
            $res = $this->replyNews($articles);
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'CardMemberUser') {
            // 扫描会员二维码
            $uid = $GLOBALS['mid'];
            $wpid = get_wpid();
            if (empty($uid)) {
                $map1['openid'] = $dataArr['FromUserName'];
                $map1['pbid'] = get_pbid();
                $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            }
            // 判断用户是否已为别人的粉丝
            $hasLink = M('member_follow_link')->where('uid', $uid)
                ->where('wpid', $wpid)
                ->value('id');
            if (empty($hasLink)) {
                // 未成为粉丝
                $link['uid'] = $uid;
                $link['member_id'] = $qr['aim_id'];
                $link['wpid'] = $wpid;
                $link['cTime'] = time();
                M('member_follow_link')->insert($link);
            }
			$pbid = get_pbid();
            $config = getAddonConfig('Card', $pbid); // 获取后台插件的配置参数
            $articles[0] = array(
                'Title' => '点击进入免费领取微会员哦~',
                'Description' => $config['title'],
                'PicUrl' => SITE_URL . "/card/cover_pic.png",
                'Url' => U('card/Wap/member_center', array(
                    'pbid' => $pbid
                ))
            );
            $res = $this->replyNews($articles);
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'ScanLogin') {

            $uid = M('public_follow')->where('openid', $dataArr['FromUserName'])->value('uid');

            S($qr['extra_text'], $uid, 120);
            $res = $this->replyText('登录成功');
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'ScanBindLogin') {
            $map1['openid'] = $dataArr['FromUserName'];
            $map1['pbid'] = get_pbid();
            /*
             * $map2 ['uid'] = M( 'public_follow' )->where ( wp_where( $map1 ) )->value( 'uid' );
             * M( 'public_follow' )->where ( wp_where( $map1 ) )->setField ( 'uid', $qr ['extra_int'] );
             * M( 'user' )->where ( wp_where( $map2 ) )->delete ();
             * M( 'credit_data' )->where ( wp_where( $map2 ) )->delete ();
             */
            M('user')->where(wp_where(array(
                'uid' => $qr['aim_id']
            )))->setField('bind_openid', $dataArr['FromUserName']);
            D('common/user')->clearCache($qr['aim_id']);
            S('is_bind_wx_' . $qr['aim_id'], 1);
            // S ( $qr ['extra_text'], 1, 120 );

            $res = $this->replyText('绑定成功');
            return true; // 告诉上面的关注方法，不需要再回复欢迎语了
        } elseif ($qr['addon'] == 'MiniLive') {
            $map1['openid'] = $dataArr['FromUserName'];
            $map1['pbid'] = get_pbid();
            $uid = M('public_follow')->where(wp_where($map1))->value('uid');
            // 微现场二维码
            $info = D('MiniLive/MiniLive')->getLive();
            if ($info) {
                $monitor = D('MiniLive/MiniMonitor')->getInfo($info['id']);
                $shakeCount = $monitor['shake_count'] + 1;
                // $userAttend= D('MiniLive/MiniShake')->get_user_attend($info['id'],$info['shake_id'],$shakeCount,$uid);
                $userAttend = D('MiniLive/MiniShake')->getUserShake($info['id'], $info['shake_id'], $shakeCount, $uid);

                if ($userAttend['user_num'] == 0) {
                    $addUA['wpid'] = get_wpid();
                    $addUA['live_id'] = $info['id'];
                    $addUA['shake_count'] = $shakeCount;
                    $addUA['uid'] = $uid;
                    $addUA['join_count'] = 0;
                    $addUA['shake_id'] = $info['shake_id'];
                    M('shake_user_attend')->insert($addUA);
                }
                $rr = D('MiniLive/MiniLive')->isUpUser($uid, $info['id'], 1, $dataArr['FromUserName']);
                if ($monitor['msgwall_state'] == 1) {
                    // 上墙，处理为可上场
                    if ($rr == 1) {
                        $content = D('MiniLive/MiniLive')->_str_rand();
                        $this->replyText($content . $info['up_push']);
                    }
                } else {
                    if ($monitor['game_state'] == 1) {
                        $url1 = U('MiniLive/Wap/shake', array(
                            'pbid' => get_pbid(),
                            'live_id' => $info['id']
                        ));
                        if ($info['game_msg_title']) {
                            $articles[0] = array(
                                'Title' => $info['game_msg_title'],
                                'Description' => $info['game_msg_intro'],
                                'PicUrl' => get_cover_url($info['game_msg_img']),
                                'Url' => $url1
                            );
                            $res = $this->replyNews($articles);
                        } else {
                            $text = "游戏即将开始，<a href='{$url1}'>马上点击参与 >></a>";
                        }
                    } else {
                        if ($monitor['game_state'] == 2) {
                            $url1 = U('MiniLive/Wap/shake', array(
                                'pbid' => get_pbid(),
                                'live_id' => $info['id']
                            ));
                            if ($info['game_msg_title']) {
                                $articles[0] = array(
                                    'Title' => $info['game_msg_title'],
                                    'Description' => $info['game_msg_intro'],
                                    'PicUrl' => get_cover_url($info['game_msg_img']),
                                    'Url' => $url1
                                );
                                $res = $this->replyNews($articles);
                            } else {
                                $this->replyText("游戏进行中，<a href='{$url1}'>马上点击参与...</a>");
                            }
                        } else {
                            if ($monitor['playback_state'] == 1) {
                                if ($info['review_msg_title']) {
                                    $articles[0] = array(
                                        'Title' => $info['review_msg_title'],
                                        'Description' => $info['review_msg_intro']
                                    );
                                    $res = $this->replyNews($articles);
                                }
                                return true;
                            } else {
                                $this->replyText('上墙还没开始或已结束！');
                            }
                        }
                    }
                }
            } else {
                // 活动尚未启用,推送欢迎语内容
                $config = getAddonConfig('Wecome');
                $param['wpid'] = get_wpid();
                $param['openid'] = get_openid();
                $sreach = array(
                    '[follow]',
                    '[website]',
                    '[wpid]',
                    '[openid]'
                );
                $replace = array(
                    U('weixin/Wap/bind', $param),
                    U('wei_site/Wap/index', $param),
                    $param['wpid'],
                    $param['openid']
                );
                $config['description'] = str_replace($sreach, $replace, $config['description']);
                switch ($config['type']) {
                    case '3':
                        if ($config['appmsg_id']) {
                            $res = D('common/Custom')->replyNews($uid, $config['appmsg_id']);
                        }
                        break;
                    case '2':
                        return false;
                        break;
                    default:
                        if ($config['description']) {
                            $res = $this->replyText($config['description']);
                        }
                }
            }
            return true;
        }
    }
}
