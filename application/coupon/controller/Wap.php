<?php
namespace app\coupon\controller;

use app\common\controller\WapBase;

class Wap extends WapBase
{

    protected $apiModel;

    function initialize()
    {
        parent::initialize();
        
        $this->apiModel = D('ApiData');
        
        if (ACTION_NAME == 'lists' || ACTION_NAME == 'personal') {
            $uid = $this->mid;
            $wpid = get_wpid();
            // 获取通知数
            $key = 'cardnotic_' . $wpid . '_' . $uid;
            $rrs = S($key);
            if ($rrs === false && is_install('card')) {
                $beforetime = 7 * 24 * 60 * 60;
                $thetime = strtotime(time_format(time(), 'Y-m-d')) - $beforetime;
                $cmap['wpid'] = $wpid;
                $cmap['uid'] = $uid;
                $cardMember = M('card_member')->where(wp_where($cmap))->find();
                if (! empty($cardMember['level'])) {
                    $map['cTime'] = array(
                        'egt',
                        $thetime
                    );
                    $map['wpid'] = $wpid;
                    
                    $notices = M('card_notice')->where(wp_where($map))->select();
                    $data = [];
                    foreach ($notices as $v) {
                        $gradeArr = explode(',', $v['grade']);
                        if ($v['to_uid'] == 0) {
                            if (in_array(0, $gradeArr) || in_array($cardMember['level'], $gradeArr)) {
                                $data[] = $v;
                            }
                        } else if ($v['to_uid'] == $uid) {
                            $data[] = $v;
                        }
                    }
                    $rrs = count($data);
                    S($key, $rrs);
                }
            } else if ($rrs <= 0) {
                $rrs = '';
            }
            $this->assign('notice_num', $rrs);
        }
    }
}
