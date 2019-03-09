<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 微信客服接口操作类
 */
class TemplateMessage extends Base
{
    protected $table = DB_PREFIX . 'user';

    public function bugNotice($uid, $title, $status_title, $level_title, $deal_user, $tester, $remark = '', $url = '', $templateId = '')
    {
        $title == '' && $title = '新增加了bug，请登录电脑端查看';

        $remark == '' && $remark = '建议在电脑端查看';
        $param['data']['first']['value'] = $title;
        $param['data']['first']['color'] = "#E60B43";

        $param['data']['keyword1']['value'] = $status_title;
        $param['data']['keyword1']['color'] = "#173177";

        $param['data']['keyword2']['value'] = $level_title;
        $param['data']['keyword2']['color'] = "#E60B43";

        $param['data']['keyword3']['value'] = $deal_user;
        $param['data']['keyword3']['color'] = "#173177";

        $param['data']['keyword4']['value'] = $tester;
        $param['data']['keyword4']['color'] = "#E60B43";

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = 'GD344gKKgf1PNyAq5z4ufyDnzWf05AXmnr1Mcc-MCBA';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 模板消息：会员有礼奖品领取通知
     * 标题：礼品领取成功通知
     * OPENTM200772305
     */
    public function replyCustomMessage($uid, $first, $sender, $prize, $getTime, $templateId = '', $url = '')
    {
        $remark = '';
        empty($title) && $title = '通知提醒';
        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#173177";

        $param['data']['keyword1']['value'] = $sender;
        $param['data']['keyword1']['color'] = "#173177";

        $param['data']['keyword2']['value'] = $prize;
        $param['data']['keyword2']['color'] = "#173177"; // #E60B43

        $param['data']['keyword3']['value'] = $getTime;
        $param['data']['keyword3']['color'] = "#173177"; // #E60B43

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = '';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 模板消息：群发消息
     * 标题：待办事项提醒
     */
    public function replyMessage($uid, $first, $title, $sender, $templateId = '', $url = '')
    {
        $remark = '';
        empty($title) && $title = '通知提醒';
        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#173177";

        $param['data']['keyword1']['value'] = $title;
        $param['data']['keyword1']['color'] = "#173177";

        $param['data']['keyword2']['value'] = $sender;
        $param['data']['keyword2']['color'] = "#173177"; // #E60B43

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = '';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 礼包领取通知消息模板 OPENTM200977411
     * 线上templateid c1n1Ry0Le6m7JfDSjeHrRs-m2YFYjR8k7BWoezFjqlk
     */
    public function replyGiftNotice($uid, $name, $first = '', $orderId = '', $remark = '', $url = '', $templateId = '')
    {
        $first == '' && $first = '您推荐的爱心分享已被领取';
        $orderId == '' && $orderId = time_format(time(), 'YmdHis');
        $remark == '' && $remark = '您的好友领取了您推荐的爱心分享，您的人气指数直接爆表！';
        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#E60B43";

        $param['data']['keyword1']['value'] = $orderId;
        $param['data']['keyword1']['color'] = "#173177";

        $param['data']['keyword2']['value'] = $name;
        $param['data']['keyword2']['color'] = "#E60B43";

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = 'VD0sCtsox8YHjFh12XzXRrS-k6-5GN3KMN8McPw0IiY';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 礼包领取失败通知消息模板TM00384
     * 线上templateid 5kb99T5UeEcFu_krkngLn_hnjvXEHZ1jcwYzC9uBk8I
     */
    public function replyGiftFail($uid, $actName, $reason, $giftName = '', $remark = '', $first = '', $url = '', $templateId = '')
    {
        $first == '' && $first = '亲爱的用户：';
        $giftName == '' && $giftName = '礼品';
        $remark == '' && $remark = '感谢您的参与!';

        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#173177";

        $param['data']['name']['value'] = $actName;
        $param['data']['name']['color'] = "#E60B43";

        $param['data']['giftName']['value'] = $giftName;
        $param['data']['giftName']['color'] = "#173177";

        $param['data']['reason']['value'] = $reason;
        $param['data']['reason']['color'] = "#E60B43";

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = 'JUX4gPYu5BgXj4XLakTvAfMpSmFZoQ_gQ0eKy6MF8wk';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 优惠券领取成功通知 OPENTM200474379
     * 0gxG83GSMpf8ymCDILnKtcOF5zSMQRjde0hYs9iO27M
     */
    public function replyCouponSuccess($uid, $couponName, $snCode, $endTime, $remark = '', $first = '', $url = '', $templateId = '')
    {
        $first == '' && $first = '恭喜您领到一张优惠券！';
        $remark == '' && $remark = '凭兑换码到店使用！';

        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#173177";

        $param['data']['keyword1']['value'] = $couponName;
        $param['data']['keyword1']['color'] = "#E60B43";

        $param['data']['keyword2']['value'] = $snCode;
        $param['data']['keyword2']['color'] = "#173177";

        $param['data']['keyword3']['value'] = $endTime;
        $param['data']['keyword3']['color'] = "#E60B43";

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = '4tFFvlKkiUbVEuK6DHTJWVCrFHntiS-qy_P-BwsY3lM';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /*
     * 返现到账通知 OPENTM205223929
     * rb-7hIQFr6P6hYxYPfzvHEaVivE-TSAX9n-T1cinzSM
     */
    public function replyReturnMoney($uid, $money, $content, $remark = '', $first = '', $url = '', $templateId = '')
    {
        $first == '' && $first = '尊敬的用户您好，您的一笔返现已到账。';
        $remark == '' && $remark = '感谢你的使用，谢谢！';

        $param['data']['first']['value'] = $first;
        $param['data']['first']['color'] = "#173177";

        $param['data']['keyword1']['value'] = $money;
        $param['data']['keyword1']['color'] = "#E60B43";

        $param['data']['keyword2']['value'] = $content;
        $param['data']['keyword2']['color'] = "#173177";

        $param['data']['remark']['value'] = $remark;
        $param['data']['remark']['color'] = "#173177";

        $templateId == '' && $templateId = 'na8JwAd--iYlefDZknhhKOFpmfGF6jSI83o2LL1oKzs';
        return $this->replyData($uid, $param, $templateId, $url);
    }

    /* 发送回复模板消息到微信平台 */
    function replyData($uid, $param, $template_id, $jumpUrl = '')
    {
        $result['status'] = 0;
        $result['msg'] = '获取用户openid失败';
        if (is_numeric($uid)) {
            $map['uid'] = $uid;
        } else {
            $map['openid'] = $uid;
        }
        $follow = M('public_follow')->where(wp_where($map))->find();
        if (empty($follow)) {
            return $result;
        }

        $param['touser'] = $follow['openid'];
        $param['template_id'] = $template_id;
        $param['url'] = str_replace('notice.php', 'index.php', $jumpUrl);

        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . get_access_token($follow['pbid']);
        // dump($param);
        // die;

        $res = post_data($url, $param);

        if (isset($res['errcode']) && $res['errcode'] != 0) {
            $result['msg'] = error_msg($res);
        } else {
            $result['status'] = 1;
            $result['msg'] = '发送成功';
        }
        return $result;
    }
}

?>
