<?php
namespace app\common\model;

use app\common\model\Base;
use think\Db;

/**
 * 积分操作
 */
class Credit extends Base
{

    protected $table = DB_PREFIX . 'credit_data';

    // 增加积分
    public function addCredit($data)
    {
        if (empty($data) || empty($data['credit_name'])) {
            return false;
        }
        empty($data['wpid']) && $data['wpid'] = get_wpid();
        $credit = $this->getCreditByName($data['credit_name'],$data['wpid']);
        
        // dump ( $credit );
        empty($data['uid']) && $data['uid'] = session('mid_'.$data['wpid']);
        
        if (empty($data['uid']))
            return false;
        
        empty($data['cTime']) && $data['cTime'] = time();
       
        
        isset($data['score']) || $data['score'] = $credit['score'];
        $data['credit_title'] = isset($data['title']) ? $data['title'] : $credit['title'];
        //是否对接ERP
        if (isset($data['not_erp'])){
        	$addErp = $data['not_erp'];
        	unset($data['not_erp']);
        }else{
        	$addErp = 1;
        }
        $data1 = $data;
        $canAdd=1;
			// 判断是否已经对接领取会员卡
		$hasCard = D ( 'card/CardMember' )->checkHasMemberCard ( $data ['uid'] );
		if (empty ( $hasCard )) {
			$addErp=0;
        }
        if ($canAdd != 1){
        	return false;
        }
        $res = $this->insertGetId($data1);
        if ($res) {
            $score = abs($data['score']);
            if ($data['score'] < 0) {
                $save['score'] = Db::raw('score-' . $score);
            } else {
                $save['score'] = Db::raw('score+' . $score);
            }
            
            D('common/User')->updateInfo($data['uid'], $save);
        }
        return $res;
    }

    // 通过积分标识获取积分配置值
    public function getCreditByName($credit_name = null,$wpid='')
    {
    	empty($wpid) && $wpid = get_wpid();
        $key = cache_key('wpid:0|'.$wpid, DB_PREFIX.'credit_config');
        $config = S($key);
        if ($config === false || ! isset($config[$wpid])) {
            $list = M('credit_config')->where('wpid="0" or wpid="' . $wpid . '"')->select();
            
            $admin_config = $public_config = [];
            foreach ($list as $vo) {
                if ($vo['wpid'] == 0) {
                    $admin_config[$vo['name']] = $vo; // 后台的配置
                } else {
                    $public_config[$vo['name']] = $vo; // 公众号的配置
                }
            }
            
            $config[$wpid] = array_merge($admin_config, $public_config); // 公众号的配置优化于后台的配置
            S($key, $config);
        }
        
        $config[$wpid][$credit_name] = isset($config[$wpid][$credit_name]) ? $config[$wpid][$credit_name] : [];
        return empty($credit_name) ? $config[$wpid] : $config[$wpid][$credit_name];
    }

    // 更新个人总积分
    public function updateFollowTotalCredit($uid)
    {
        $info = $this->where('uid', $uid)
            ->field('sum( score ) as score')
            ->find();
        
        D('common/User')->updateInfo($uid, $info);
    }

    public function getAllCreditInfo($uid)
    {
        $info = $this->where('uid', $uid)
            ->field('sum( score ) as score')
            ->find();
        return $info;
    }

    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
        $key = 'Common_Credit_getCreditByName';
        S($key, null);
    }

    public function updateSubscribeCredit($wpid, $credit, $type = 0)
    {
        if ($type == 0) {
            $config = getAddonConfig('UserCenter', $wpid);
            $config['score'] = $credit['score'];
            D('common/PublicConfig')->setConfig('UserCenter', $config);
        } else {
            $data['wpid'] = $wpid;
            $data['name'] = 'subscribe';
            
            $info = M('credit_config')->where(wp_where($data))->find();
            if ($info) {
                $res = M('credit_config')->where(wp_where($data))->update($credit);
            } else {
                $data['score'] = $credit['score'];
                
                $data['title'] = '关注公众号';
                $data['mTime'] = NOW_TIME;
                
                M('credit_config')->insert($data);
            }
            $this->clearCache(0);
        }
    }
}
