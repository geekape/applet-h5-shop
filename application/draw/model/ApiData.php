<?php
namespace app\draw\model;

use app\common\model\Base;

// 同时为API和Wap提供数据的模型
class ApiData extends Base
{

    public function index()
    {
        $openid = get_openid();
        $gameId = I('games_id/d', 0);
        if (empty($gameId)) {
            return $this->error('还没有配置活动');
        }
        $info = D('draw/Games')->getInfo($gameId);
        if (empty($info)){
        	return $this->error('活动已被删除');
        }
        $errMsg = '';
        
        /*
         * if ($info ['status'] == 0) {
         * $errMsg = '活动已关闭';
         * } else {
         * if (NOW_TIME >= $info ['end_time']) {
         * $errMsg = '活动已结束';
         * } else if (NOW_TIME < $info ['start_time']) {
         * $errMsg = '活动未开始';
         * }
         * }
         */
        $checkRes = $this->check_count($gameId);
        if ($checkRes['status'] == 0) {
            $errMsg = $checkRes['msg'];
        }
        // 分享数据
        $shareData['title'] = isset($info['title'])?$info['title']:'抽奖游戏';
        $desc = empty($info['remark']) ? $shareData['title'] : $info['remark'];
        $shareData['desc'] = filter_line_tab($desc);
        // 奖品列表
        $awardLists = D('Draw/LotteryGamesAwardLink')->getGamesAwardlists($gameId);
        $jplist = [];
        $pTitles = $pIds = $pImgs = [];
        foreach ($awardLists as $v) {
            $jp = [];
            $jp['name'] = $v['grade'];
            // $jp['pic'] = $v['img'];
            $jp['url'] = $v['img_url'];
            $jp['id'] = $v['award_id'];
            $jplist[] = $jp;
        }
        $tmp = '';
        $thanksImg = SITE_URL . '/static/default/img/guaguale/thanks.jpg';
        $info['game_type'] = isset($info['game_type'])?$info['game_type']:1;
        switch ($info['game_type']) {
            case 1:
                // 刮刮乐
                $shareData['imgUrl'] = $info['cover'] ? get_cover_url($info['cover']) : '';
                $tmp = 'guagua';
                break;
            case 2:
                $jp['name'] = '谢谢参与';
                $jp['url'] = $thanksImg;
                $jp['id'] = 0;
                $jplist[] = $jp;
                // 大转盘
                $shareData['imgUrl'] = $info['cover'] ? get_cover_url($info['cover']) : '';
                $tmp = 'dial';
                // 抽奖的奖品列表
                $jplist = json_url($jplist);
                break;
            case 3:
                // 砸金蛋
                $shareData['imgUrl'] = $info['cover'] ? get_cover_url($info['cover']) : '';
                $tmp = 'egg';
                break;
            case 4:
                // 九宫格
                $count = count($jplist);
                $num = 10 - $count;
                if ($num > 0) {
                    for ($i = 0; $i < $num; $i ++) {
                        $jp['name'] = '谢谢参与';
                        $jp['url'] = $thanksImg;
                        $jp['id'] = 0;
                        $jplist[] = $jp;
                    }
                }
                shuffle($jplist);
                $shareData['imgUrl'] = $info['cover'] ? get_cover_url($info['cover']) : '';
                $tmp = 'square_dial';
                break;
        }
        // dump($jplist);
        // 分享数据信息
        $info['id'] = isset($info['id'])?$info['id']:0;
        $shareData['link'] = U('index', array(
            'games_id' => $info['id']
        ));
        $data['shareData'] = $shareData;
        if ($info['id']>0){
        	// 保存当前抽奖次数
        	$isTest = I('test', 1);
        	if ($isTest) {
        		D('draw/Games')->saveCurrentDraw($gameId);
        	}
        	// 浏览记录
        	$pvData['wpid'] = get_wpid();
        	$pvData['uid'] = $this->mid;
        	$pvData['draw_id'] = $gameId;
        	$pvData['cTime'] = time();
        	$pvData['openid'] = get_openid();
        	M('draw_pv_log')->insert($pvData);
        }
        
        // 当前用户是否关注公众号
        $map['pbid'] = get_pbid();
        $map['uid'] = $this->mid;
        // 判断活动是否需要关注才能继续
        $data['qrcode'] = '';
        if (isset($info['need_subscribe']) && $info['need_subscribe']) {
            $has_subscribe = 0;
            if ($openid == '-1' || ! config('USER_OAUTH')) {
                // 公众号没有权限，直接参与
                $has_subscribe = 1;
            } elseif ($openid != '-1' && ! empty($openid)) {
                $has_subscribe = intval(M('public_follow')->where(wp_where($map))->value('has_subscribe'));
            }
            $qrcode = '';
            if ($has_subscribe == 0) {
                // 获取需要关注的公众号二维码
                $qrcode = D('home/QrCode')->add_qr_code('QR_SCENE', 'Draw', $gameId);
            }
            $data['qrcode'] = $qrcode;
        } else {
            $has_subscribe = 1;
        }
        // 测试
        // $has_subscribe=0;
        // $data['qrcode']='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQE08DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydjJCeGR2eVE5T1QxZmV5NmhwMVEAAgTOVd9YAwQAjScA';
        
        $data['has_subscribe'] = $has_subscribe;
        // 判断是否需要领取会员卡
        if (isset($info['need_member']) && $info['need_member'] == 1) {
            $hasCard = D('card/CardMember')->checkHasMemberCard($this->mid);
            if (empty($hasCard)) {
                $errMsg = '需要成为会员才能参与游戏！';
            }
        }
        if (empty($errMsg) && ! isWeixinBrowser()) {
            $errMsg = '请在手机端微信浏览器打开！';
        }
        // 所有获奖列表前5条数据
        $luckLists = D('Draw/LuckyFollow')->getGamesLuckyLists($gameId, 0, - 1, 'lottery_games', 5);
        // dump($luckLists);
        
        $joinUrl = U('draw/Wap/draw_lottery', array(
            'games_id' => $gameId
        ));
        $data['joinurl'] = $joinUrl;
        // $data['has_prize'] = $hasPrize;
        $data['jplist'] = $jplist;
        
        $data['luck_lists'] = $luckLists;
        // dump($awardLists);
        // dump($info);
        $data['award_lists'] = $awardLists;
        $info['remark'] = isset($info['remark'])?$info['remark']:'';
        $info['remark'] = str_replace("\n", "<br/>", $info['remark']);
        $data['err_msg'] = $errMsg;
        $data['info'] = $info;
        $data['templateFile'] = $tmp;
        return $data;
    }

    // 直接返回抽奖失败信息
    function _error_return($status, $msg, $jumpUrl = '')
    {
        $returnData['status'] = $status;
        $returnData['msg'] = $msg;
        $returnData['award_id'] = 0;
        $returnData['jump_url'] = $jumpUrl;
        $returnData['title'] = ''; // 用于刮刮乐
        return $returnData;
    }

    // 抽奖方法
    public function draw_lottery()
    {
        if (! isWeixinBrowser()) {
            $status = 0;
            $msg = '请在手机端微信浏览器打开！';
            $allow_draw = false;
            return $this->_error_return($status, $msg);
        }
        $openid = get_openid();
        $gameId = I('games_id/d', 0);
        $msg = '';
        $status = 0;
        $uid = $this->mid;
        // $wpid = get_wpid();
        
        $awardId = 0;
        $angle = - 60;
        $allow_draw = true; // 有机会抽奖
        if (empty($gameId)) {
            $status = 0;
            $msg = '活动已结束!！';
            $allow_draw = false;
            return $this->_error_return($status, $msg);
        }
        $info = D('Draw/Games')->getInfo($gameId);
        if ($info['status'] == 0) {
            $status = 0;
            $msg = '活动已关闭';
            $allow_draw = false;
        } else {
            if (NOW_TIME >= $info['end_time']) {
                $status = 0;
                $msg = '活动已结束！';
                $allow_draw = false;
            } else if (NOW_TIME < $info['start_time']) {
                $status = 0;
                $msg = '活动未开始！';
                $allow_draw = false;
            }
        }
        if (! $allow_draw) {
            // 返回提示不继续走
            return $this->_error_return($status, $msg);
        }
        // 当前用户是否关注公众号
        $map['pbid'] = get_pbid();
        $map['uid'] = $this->mid;
        // 判断活动是否需要关注才能继续
        if ($info['need_subscribe']) {
            $has_subscribe = 0;
            if ($openid == '-1' || ! config('USER_OAUTH')) {
                // 公众号没有权限，直接参与
                $has_subscribe = 1;
            } elseif ($openid != '-1' && ! empty($openid)) {
                $has_subscribe = intval(M('public_follow')->where(wp_where($map))->value('has_subscribe'));
            }
        } else {
            $has_subscribe = 1;
        }
        if ($has_subscribe == 0) {
            $status = 0;
            $msg = '需要关注公众号才能参与活动！';
            $allow_draw = false;
        }
        // 判断是否需要领取会员卡
        if (isset($info['need_member']) && $info['need_member'] == 1) {
            $hasCard = D('card/CardMember')->checkHasMemberCard($this->mid);
            if (empty($hasCard)) {
                $status = 0;
                $msg = '需要成为会员才能参与游戏！';
                $allow_draw = false;
            }
        }
        if (! $allow_draw) {
            // 返回提示不继续走
            return $this->_error_return($status, $msg);
        }
        $wpid = get_wpid();
        $credit_title = "抽奖游戏";
        if ($uid <= 0) {
            $status = 0;
            $msg = '抱歉，获取不到用户信息！';
            $allow_draw = false;
        }
        if (! $allow_draw) {
            // 返回提示不继续走
            return $this->_error_return($status, $msg);
        }
        // 每人总共抽奖次数
        if ($info['attend_limit'] > 0) {
            $attend_limit = D('Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid);
            if ($attend_limit >= $info['attend_limit']) {
                $status = 0;
                $msg = '您的所有抽奖次数已用完!';
                $allow_draw = false;
                return $this->_error_return($status, $msg);
            }
        }
        
        // 每人每天抽奖次数
        if ($info['day_attend_limit'] > 0) {
            $day_attend_limit = D('Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid, NOW_TIME);
            if ($day_attend_limit >= $info['day_attend_limit']) {
                $status = 0;
                $msg = '您今天的抽奖次数已经用完!';
                $allow_draw = false;
                return $this->_error_return($status, $msg);
            }
        }
        
        // 每天最多中奖人数
        if ($info['day_winners_count'] > 0) {
            $day_winners = D('Draw/LuckyFollow')->get_day_winners_count($gameId, NOW_TIME);
            $day_winners_count = $day_winners['num'];
            if ($day_winners_count >= $info['day_winners_count'] && ! isset($day_winners['uids'][$this->mid])) {
                $status = 0;
                $msg = '今天奖品已抽完，明天再来吧!';
                $allow_draw = false;
                return $this->_error_return($status, $msg);
            }
        }
        // 每人总共中奖次数
        if ($info['win_limit'] > 0) {
            $win_limit = D('Draw/LuckyFollow')->get_user_win_count($gameId, $uid);
            if ($win_limit >= $info['win_limit']) {
                // 超过此限制点击抽奖，抽奖者将无概率中奖
                $status = 0;
                $msg = '没有抽中,继续努力';
                $allow_draw = false;
            }
        }
        if ($allow_draw) {
            // 每人每天中奖次数
            if ($info['day_win_limit'] > 0) {
                $day_win_limit = D('Draw/LuckyFollow')->get_user_win_count($gameId, $uid, NOW_TIME);
                if ($day_win_limit >= $info['day_win_limit']) {
                    // 抽奖者将无概率中奖
                    $status = 0;
                    $msg = '今天的运气用完了';
                    $awardId = 0;
                    $allow_draw = false;
                }
            }
        }
        // 保存抽奖记录
        $drawLog['follow_id'] = $uid;
        $drawLog['sports_id'] = $gameId;
        $drawLog['count'] = 1;
        $drawLog['cTime'] = NOW_TIME;
        $drawLog['wpid'] = $wpid;
        M('draw_follow_log')->insert($drawLog);
        
        if (! $allow_draw) {
            return $this->_error_return($status, $msg);
        }
        $title = $img = $jumpUrl = '';
        $need_writeinfo = $awardId = 0;
        if ($allow_draw) {
            // 抽奖，获取奖品id
            $awardId = $this->_do_lottery($gameId);
            $need_writeinfo = 0;
            if ($awardId == 0) {
                $status = 0;
                $msg = '没有抽中,继续努力啊';
            } else {
                $awardInfo = D('draw/Award')->getInfo($awardId);
                if (empty($awardInfo)) {
                    $status = 0;
                    $msg = '没有抽中,继续努力啊!';
                } else {
                    $title = $awardInfo['name'];
                    // $config = getAddonConfig ( 'draw' );
                    // $need_writeinfo = $config ['need_writeinfo'];
                    $need_writeinfo = 0;
                    // $img = get_cover_url ( $awardInfo ['img'] );
                    $img = isset($awardInfo['img_url']) ? $awardInfo['img_url'] : get_cover_url($awardInfo['img']);
                    if ($need_writeinfo == 1) {
                        $status = 1;
                        $msg = '恭喜，您中了奖品  ' . $awardInfo['name'] . '!';
                    } else {
                        $result = $this->_do_save_result($gameId, $awardId, $uid, $awardInfo);
                        $status = $result['status'];
                        $msg = $result['msg'];
                        $awardId = $result['awardId'];
                        $jumpUrl = $result['url'];
                    }
                }
            }
        }
        
        if (empty($jumpUrl)) {
            $jumpUrl = U('draw/Wap/index', array(
                'games_id' => $gameId
            ));
        }
        
        $returnData['status'] = $status;
        $returnData['msg'] = $msg;
        $returnData['img'] = $img;
        $returnData['title'] = $title;
        $returnData['jump_url'] = $jumpUrl;
        $returnData['award_id'] = $awardId;
        $returnData['need_writeinfo'] = $need_writeinfo;
        addWeixinLog($returnData, 'draw_result_' . $gameId);
        return $returnData;
    }

    // 获取奖品id
    public function _do_lottery($eventId)
    {
        $redis = new \think\cache\driver\Redis();
        // 获取当前第几位抽奖
        $rediesKey = 'rdraw_lottery_' . $eventId;
        $cNum = $redis->inc($rediesKey);
        // 已经抽中的列表
        $rkey = 'rdraw_winlists_' . $eventId;
        $winList = $redis->get($rkey);
        // 存在可能用户抽中了优惠券，但他的领取次数超过了限制，不能再领，所以要把抽奖设置为未中奖，抽中的奖品设置回去，让别人领取
        // 如果同时参与的人数多时，前面有奖品设置回去了，后面的奖品又被抽走了，所以后面有被抽走的直接跳过，不算次数
        while (isset($winList[$cNum])) {
            // 如果该数已经被抽走了就重新加数
            $cNum = $redis->inc($rediesKey);
        }
        $data = D('draw/EventPrizes')->getPrize($eventId, $cNum);
        addWeixinLog($data, 'draw_num_' . $eventId . '_' . $cNum);
        $prizeid = $data['prize_id'];
        $winList = $data['win_list'];
        $flat = true;
        // 根据优惠券的限制数发放
        if ($prizeid != 0) {
            $awardInfo = D('draw/Award')->getInfo($prizeid);
            $prizeid = $awardInfo['id'];
                // 优惠券
                $info = D('coupon/Coupon')->getInfo($awardInfo['coupon_id']);

                if (! isset($info['id'])) {
                    $flat = false;
                } else if (! empty($info['start_time']) && $info['start_time'] > NOW_TIME) {
                    $flat = false;
                } else if (! empty($info['end_time']) && $info['end_time'] < NOW_TIME) {
                    $flat = false;
                } else if ($info['is_del'] == 1) {
                    $flat = false;
                }
                
               
            if (! $flat) {
                $prizeid = 0;
            }
        }
        return $prizeid;
    }

    // 保存中奖信息
    public function save_zjInfo($gameId, $awardId, $uid, $awardInfo = array())
    {
        $res['other'] = 0;
        
        $data['draw_id'] = $gameId;
        $data['wpid'] = get_wpid();
        $data['aim_table'] = 'lottery_games';
        $data['zjtime'] = NOW_TIME;
        $data['num'] = 1;
        $data['follow_id'] = $uid;
        $data['award_id'] = $awardId;
        empty($awardInfo) && $awardInfo = D('draw/Award')->getInfo($awardId);
        $data['award_type'] = $awardInfo['award_type'];
        switch ($awardInfo['award_type']) {
            case 0:
                // 积分
                // 虚拟物品，积分奖励
                $credit['score'] = $awardInfo['score'];
                $credit['title'] = '抽奖游戏活动';
                $credit['uid'] = $uid;
                $cRes = add_credit('lottery_games', $credit,0);
                if ($cRes) {
                    $data['send_aim_id'] = $cRes;
                    $data['state'] = 1;
                    $data['djtime'] = time();
                } else {
                    $data['state'] = 0;
                    $data['error_remark'] = '积分发放失败';
                }
                $res['other'] = 0;
                break;
            case 1:
                // 实物
                $data['state'] = 0;
                $res['other'] = 1;
                $str = time();
                $rand = rand(1000, 9999);
                $str .= $rand;
                $data['scan_code'] = $str;
                break;
            case 2:
                // 优惠券
                $awardInfo['coupon_id'] = isset($awardInfo['coupon_id']) ? $awardInfo['coupon_id'] : 0;
                $cRes = D('coupon/Coupon')->sendCoupon($awardInfo['coupon_id'], $this->mid);
                if ($cRes) {
                    $data['send_aim_id'] = $cRes;
                    $data['state'] = 1;
                    $data['djtime'] = time();
                } else {
                    $data['state'] = 0;
                    $data['error_remark'] = '代金券发送失败';
                }
                $res['sn_id'] = $cRes;
                $res['other'] = 2;
                // 优惠券
                break;
            case 4:
                $dOpenid = get_openid();
                
                $map1['uid'] = $uid;
                $map1['wpid'] = get_wpid();
                // TODO企业付款，下发金额
                $res['other'] = 4;
                $money = $awardInfo['money'] * 100; // 单位：分
                $more_param = array(
                    'act_name' => '抽奖游戏',
                    'act_id' => $gameId,
                    'act_mod' => 'draw'
                ); // 红包其它参数，默认为空
                $redRes = D('Common/Transfer')->add_pay($dOpenid, $money, $more_param, true);
                $redRes['defaultopenid'] = $dOpenid; // 记入缓存
                addWeixinLog($redRes, 'addredbag2_' . $gameId . '_' . $uid);
                if ($redRes['status']) {
                    $data['send_aim_id'] = $redRes['id'];
                    $data['djtime'] = time();
                    $data['state'] = 1;
                } else {
                    $data['state'] = 0;
                    $data['error_remark'] = $redRes['msg'];
                    // $canAdd = 0;
                }
                $res['rb_res'] = $redRes['status'];
                $res['rb_msg'] = $redRes['msg'];
                break;
            case 5:
                $data['state'] = 0;
                // 微信卡券
                $res['other'] = 5;
                $res['card_id'] = isset($awardInfo['card_id']) ? $awardInfo['card_id'] : 0;
                // 返现
                break;
        }
        $res['id'] = M('lucky_follow')->insertGetId($data);
        return $res;
    }

    public function _do_save_result($gameId, $awardId, $uid, $awardInfo, $isWrite = 0)
    {
        // 直接保存中奖信息，无须填写用户信息
        $res = $this->save_zjInfo($gameId, $awardId, $uid, $awardInfo);
        if (isset($res['id']) && $res['id']) {
            $arr['status'] = 1;
            if (! empty($isWrite)) {
                $msg = '保存信息成功，我们已经为您下发奖品 ' . $awardInfo['name'];
            } else {
                $msg = '恭喜，您中了 ' . $awardInfo['name'];
            }
            
            if (isset($res['other']) && $res['other'] == 1) {
                // 领取奖品
                $jumpUrl = U('Draw/Wap/get_prize', array(
                    'id' => $res['id']
                ));
                // 推送消息
                $this->send_msg($gameId, 'news', $jumpUrl);
            } else if (isset($res['other']) && $res['other'] == 2) {
                // 优惠详情
                $jumpUrl = U('Coupon/Wap/show', array(
                    'id' => $awardInfo['coupon_id'],
                    'sn_id' => $res['sn_id']
                ));
                // 推送消息
                $this->send_msg($gameId, 'news', $jumpUrl);
            } else if (isset($res['other']) && $res['other'] == 0) {
                if ($isWrite) {
                    $msg = '保存信息成功，您获得了' . $awardInfo['score'] . '积分，已自动充到个人积分中。';
                } else {
                    $msg = '恭喜，您中了 ' . $awardInfo['name'] . ',获得了' . $awardInfo['score'] . '积分，已自动充到个人积分中。';
                }
                
                $jumpUrl = U('Draw/Wap/index', array(
                    'games_id' => $gameId
                ));
                // 推送消息
                $this->send_msg($gameId, 'score', '', $awardInfo['score']);
            } else if (isset($res['other']) && $res['other'] == 4) {
                // addWeixinLog($res,'draw_res');
                // if (!$res['rb_res']){
                // $awardId = 0;
                // $arr['status'] = 0;
                // $msg = '可惜，差一点点，继续努力！';
                // }else{
                if ($isWrite) {
                    $msg = '保存信息成功，您获得了' . $awardInfo['money'] . '现金红包，已经通知下发。';
                } else {
                    $msg = '恭喜，您中了 ' . $awardInfo['name'] . ',获得了' . $awardInfo['money'] . '现金红包，已经通知下发。';
                }
                // }
                $jumpUrl = U('draw/Wap/index', array(
                    'games_id' => $gameId
                ));
                // 推送消息
                $this->send_msg($gameId, 'money', '', $awardInfo['money']);
            } else if (isset($res['other']) && $res['other'] == 5) {
                // 微信卡券
                $jumpUrl = U('card_vouchers/Wap/index', array(
                    'id' => $res['card_id'],
                    'from_type' => 'drawgame',
                    'aim_id' => $res['id']
                ));
            }
        } else {
            $awardId = 0;
            $arr['status'] = 0;
            $msg = '可惜，差一点了';
        }
        
        $arr['msg'] = $msg;
        $arr['awardId'] = $awardId;
        $arr['url'] = $jumpUrl;
        return $arr;
    }

    // 中奖推送消息给用户
    function send_msg($gameId, $type = 'news', $jurl = '', $val = 0)
    {
        $info = D('draw/Games')->getInfo($gameId);
        $uid = $this->mid;
        if ($type == 'money') {
            // 现金红包推送消息
            if (! empty($info['winning_money_text'])) {
                $replaceArr['{val}'] = $val;
                $info['winning_money_text'] = strtr($info['winning_money_text'], $replaceArr);
                $rr = D('common/Custom')->replyText($uid, $info['winning_money_text']);
            }
        } elseif ($type == 'score') {
            // 积分
            if (! empty($info['winning_score_text'])) {
                $replaceArr['{val}'] = $val;
                $info['winning_score_text'] = strtr($info['winning_score_text'], $replaceArr);
                $rr = D('common/Custom')->replyText($uid, $info['winning_score_text']);
            }
        } else {
            // 推送图文消息
            $art = [];
            $art['title'] = $info['title'];
            $art['description'] = $info['winning_mess_text'];
            $art['url'] = $jurl;
            // 获取封面图片URL
            $art['picurl'] = get_cover_url($info['winning_mess_img']);
            $articles[] = $art;
            $param['news']['articles'] = $articles;
            $rr = D('common/Custom')->replyData($uid, $param, 'news');
        }
    }

    public function check_count($gameId)
    {
        // $gameId = I ( 'games_id/d', 0 );
        $allow_draw = true; // 有机会抽奖
        $msg = '';
        if (empty($gameId)) {
            $status = 0;
            $msg = '活动已结束!！';
            $allow_draw = false;
        }
        $info = D('Draw/Games')->getInfo($gameId);
        if (! isset($info['status'])) {
            $msg = '活动已删除';
            $allow_draw = false;
        } else {
            if (empty($msg)) {
                if ($info['status'] == 0) {
                    $status = 0;
                    $msg = '活动已关闭';
                    $allow_draw = false;
                } else {
                    if (NOW_TIME >= $info['end_time']) {
                        $status = 0;
                        $msg = '活动已结束！';
                        $allow_draw = false;
                    } elseif (NOW_TIME < $info['start_time']) {
                        $status = 0;
                        $msg = '活动未开始！';
                        $allow_draw = false;
                    }
                }
            }
        }
        $uid = $this->mid;
        if (empty($msg) && $uid <= 0) {
            $status = 0;
            $msg = '抱歉，获取不到用户信息！';
            $allow_draw = false;
        }
        // 每人每天抽奖次数
        if (empty($msg) && $info['day_attend_limit'] > 0) {
            $day_attend_limit = D('Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid, NOW_TIME);
            if ($day_attend_limit >= $info['day_attend_limit']) {
                $status = 0;
                $msg = '您今天的抽奖次数已经用完!';
                $allow_draw = false;
            }
        }
        // 每人总共抽奖次数
        if (empty($msg) && $info['attend_limit'] > 0) {
            $attend_limit = D('Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid);
            if ($attend_limit >= $info['attend_limit']) {
                $status = 0;
                $msg = '您的所有抽奖次数已用完!';
                $allow_draw = false;
            }
        }
        // 每天最多中奖人数
        if (empty($msg) && $info['day_winners_count'] > 0) {
            // $day_winners_count = D ( 'Draw/LuckyFollow' )->get_day_winners_count ( $gameId, NOW_TIME );
            $day_winners = D('Draw/LuckyFollow')->get_day_winners_count($gameId, NOW_TIME);
            $day_winners_count = $day_winners['num'];
            if ($day_winners_count >= $info['day_winners_count'] && ! isset($day_winners['uids'][$this->mid])) {
                $status = 0;
                $msg = '今天奖品已抽完，明天再来吧!';
                $allow_draw = false;
            }
        }
        
        /*
         * // 每人每天中奖次数
         * if (empty ( $msg ) && $info ['day_win_limit']>0) {
         * $day_win_limit = D ( 'Draw/LuckyFollow' )->get_user_win_count ( $gameId, $uid, NOW_TIME );
         * if ($day_win_limit >= $info ['day_win_limit']) {
         * // 抽奖者将无概率中奖
         * $status = 0;
         * $msg = '今天的运气用完了';
         * $awardId = 0;
         * $allow_draw = false;
         * }
         * }
         */
        $returnData['status'] = $allow_draw ? 1 : 0;
        $returnData['msg'] = $msg;
        return $returnData;
    }

    public function ajax_writeInfo()
    {
        $status = 0;
        $msg = '操作失败';
        if (request()->isPost()) {
            $name = input('username');
            $mobile = input('mobile');
            if (empty($name)) {
                $this->error('姓名不为空', '', true);
            }
            if (empty($mobile)) {
                $this->error('手机号不为空', '', true);
            }
            $gameId = input('games_id/d', 0);
            $awardId = input('award_id/d', 0);
            if (empty($gameId)) {
                $this->error('保存失败', '', true);
            }
            if (empty($awardId)) {
                $this->error('保存失败!', '', true);
            }
            $uid = $this->mid;
            $awardInfo = D('Draw/Award')->getInfo($awardId);
            $result = $this->_do_save_result($gameId, $awardId, $uid, $awardInfo, 1);
            $status = $result['status'];
            $msg = $result['msg'];
            $jumpUrl = $result['url'];
        }
        if (empty($jumpUrl)) {
            $jumpUrl = U('index', array(
                'games_id' => $gameId
            ));
        }
        $returnData['status'] = $status;
        $returnData['msg'] = $msg;
        $returnData['url'] = $jumpUrl;
        $this->ajaxReturn($returnData);
    }

    // 领取
    public function get_prize()
    {
        $id = I('id/d', 0);
        $userAward = D('draw/LuckyFollow')->getUserAward($id, true);
        $data['user_award'] = $userAward;
        $data['is_check'] = $userAward['state'];
        return $data;
    }

    public function save_prize_address()
    {
        $map['id'] = I('id');
        $save['address'] = I('address_id');
        $res = M('lucky_follow')->where(wp_where($map))->update($save);
        /*
         * if ($res) {
         * echo 1;
         * } else {
         * echo 0;
         * }
         */
        return $res;
    }

    // 实物奖品扫码核销
    public function scan_success()
    {
        $cTime = I('cTime/d', 0);
        $tt = NOW_TIME * 1000 - $cTime;
        if ($cTime > 0) {
            if ($tt > 30000) {
                $this->error('二维码已经过期');
            }
        }
        // 扫码员id
        $mid = $this->mid;
        // 授权表查询
        $map['uid'] = $mid;
        $map['wpid'] = get_wpid();
        $map['enable'] = 1;
        $role = M('servicer')->where(wp_where($map))->value('role');
        $roleArr = explode(',', $role);
        if (! in_array(2, $roleArr)) {
            return $this->error('你还没有扫码验证的权限');
            exit();
        }
        
        $scanCode = I('scan_code');
        $map1['id'] = I('id');
        $lucky = M('lucky_follow')->where('id', $map1['id'])->find();
        $is_check = 0;
        if ($lucky['scan_code'] == $scanCode) {
            // 验证成功
            $save['state'] = 1;
            $save['djtime'] = time();
            $res = M('lucky_follow')->where(wp_where($map1))->update($save);
            if ($res !== false) {
                $is_check = 1;
            }
        }
        $userAward = D('Draw/LuckyFollow')->getUserAward($map1['id'], true);
        $data['user_award'] = $userAward;
        $data['is_check'] = $is_check;
        $data['templateFile'] = 'get_prize';
        return $data;
    }

    public function get_state()
    {
        $id = I('id');
        $state = M('lucky_follow')->where('id', $id)->value('state');
        return $state;
    }

    /**
     * 奖品详情
     *
     * @return array
     */
    public function prize_detail()
    {
        $gameId = I('games_id/d', 0);
        // 获取各奖品已抽中的数量
        $winNum = M('lucky_follow')->where('draw_id', $gameId)
            ->field('award_id,sum( num ) lucky_num')
            ->group('award_id')
            ->select();
        $winArr = [];
        foreach ($winNum as $vo) {
            $winArr[$vo['award_id']] = $vo['lucky_num'];
        }
        $awardList = D('draw/LotteryGamesAwardLink')->getGamesAwardlists($gameId);
        
        foreach ($awardList as &$vv) {
            $vv['show_num'] = $vv['unreal_num'] > 0 ? intval($vv['unreal_num']) : intval($vv['num']);
            $vv['win_num'] = isset($winArr[$vv['award_id']]) ? intval($winArr[$vv['award_id']]) : 0;
        }
        $data['list'] = $awardList;
        return $data;
    }

    /**
     * 中奖记录
     */
    public function lucky_list()
    {
        $gameId = I('games_id/d', 0);
        $luckLists = D('Draw/LuckyFollow')->getGamesLuckyLists($gameId);
        $data['list'] = $luckLists;
        return $data;
    }

    // 我的奖品
    public function my_prize_new()
    {
        $gameId = I('games_id/d', 0);
        $luckLists = D('draw/LuckyFollow')->getGamesLuckyLists($gameId, $this->mid);
        $data['list'] = $luckLists;
        return $data;
    }

    function egg2()
    {
        return [];
    }
}
