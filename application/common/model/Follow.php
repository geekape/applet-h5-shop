<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 粉丝操作
 */
class Follow extends Base
{

    protected $table = DB_PREFIX . 'public_follow';

    function init_follow($openid, $pbid = '', $has_subscribe = false)
    {
        empty($pbid) && $pbid = get_pbid();
        if (empty($openid) || $openid == - 1 || empty($pbid) || $pbid == - 1)
            return false;
        
        if (! is_numeric($pbid)) {
            $pbid = M('publics')->where('public_id', $pbid)->value('id');
        }
        
        $umap['pbid'] = $data['pbid'] = $pbid;
        $umap['openid'] = $data['openid'] = $openid;
        $uid = $this->where('openid', $openid)->where('pbid',$pbid)->value('uid');
        if ($uid > 0) {
            $userDao = D('common/User');
            // 处理未获取到用户信息，重新获取
            $hasInfo = $userDao->where('uid', $uid)->find();
            if (empty($hasInfo['nickname'])) {
                $user = getWeixinUserInfo($openid);
                if (! empty($user['nickname'])) {
                    $userDao->updateInfo($uid, $user);
                }
            }
            return $uid;
        }
        $lock_key = 'init_follow_lock_' . $openid;
        $lock = S($lock_key);
        if ($lock !== false)
            return false;
        
        S($lock_key, 1, 30); // 锁定30秒，解决微信服务器同时重复请求时的问题
                             
        // 自动注册
                             // $config = getAddonConfig('UserCenter', $pbid);
        $user = array(
            'score' => 0, // isset($config['score']) ? intval($config['score']) : '',
            'reg_ip' => get_client_ip(1),
            'reg_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
            'last_login_time' => NOW_TIME,
            
            'status' => 1,
            'is_init' => 1,
            'is_audit' => 1,
            'come_from' => 1
        );
        
        $user2 = getWeixinUserInfo($openid);
        $user = array_merge($user, $user2);
        isset($user['headimgurl']) && $user['headimgurl'] = str_replace('http:', '', $user['headimgurl']);
        unset($user['subscribe']);
        unset($user['openid']);
        unset($user['tagid_list']);
        unset($user['errcode']);
        unset($user['errmsg']);
        $data['uid'] = D('common/User')->insertGetId($user);
        
        if ($has_subscribe !== false) {
            $data['has_subscribe'] = $has_subscribe;
        }
        if (isset($user['unionid']) && $user['unionid'] != '') {
            $data['unionid'] = $user['unionid'];
        }
        if (! is_null($uid)) {
            $this->where(wp_where($umap))->update($data);
        } else {
            $this->insert($data);
        }
        S($lock_key, null); // 解锁
        return $uid;
    }

    /**
     * 兼容旧的写法
     */
    public function getFollowInfo($id, $update = false)
    {
        return D('common/User')->getUserInfo($id, $update);
    }

    function updateInfo($id, $data)
    {
        return D('common/User')->updateInfo($id, $data);
    }

    function updateByMap($map, $data)
    {
        return false; // 已停用该方法
    }

    function updateField($id, $field, $val)
    {
        return D('common/User')->updateInfo($id, array(
            $field => $val
        ));
    }

    function set_subscribe($user_id, $has_subscribe = 1)
    {
        if (is_numeric($user_id)) {
            $map['uid'] = $user_id;
        } else {
            $map['openid'] = $user_id;
        }
        
        $this->where(wp_where($map))->setField('has_subscribe', $has_subscribe);
    }

    function getOpenidByUid($uid, $pbid = '')
    {
        empty($pbid) && $pbid = get_pbid();
        
        $map['uid'] = $uid;
        $map['pbid'] = $pbid;
        
        $key = cache_key($map, $this->table, 'openid');
        $openid = S($key);
        if ($openid === false) {
            $openid = $this->where(wp_where($map))->value('openid');
            if (! empty($openid)) {
                S($key, $openid);
            }
        }
        return $openid;
    }
}
?>
