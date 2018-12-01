<?php

namespace app\draw\model;

use app\common\model\Base;

/**
 * Award奖品库模型
 */
class Award extends Base
{
    protected $table = DB_PREFIX . 'award';
    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }	
    public function getInfo($id, $update = false, $data = [])
    {
		$key = cache_key('id:'.$id, $this->table);
        $info = S($key);
        if ($info === false || $update ) {
            if (empty($data)) {
                $info = $this->findById($id);
            } else {
                $info = $data;
            }
            if (count($info) != 0) {
            	$typeArr = array(
            			0=>'积分',
            			1=>'实物奖品',
            			2=>'优惠券',
            			3=>'代金券',
            			4=>'现金红包'
            	);
                $info['award_title'] = '';
                $model               = getModelByName('award');
                $info['img_url']     = get_cover_url($info['img']);
                $info['type_name']   = isset($typeArr[$info['award_type']])?$typeArr[$info['award_type']]:''; //TODO
                if ($info['award_type'] == 0) {
                    $info['award_title'] = $info['score'] . '积分';
                } else if ($info['award_type'] == 1) {
//                     $info['price'] = $info['price']==0?'未报价':$info['price'];
                    $info['award_title'] = $info['price'] == 0 ? $info['name'] : '价值 ' . intval($info['price']) . '元';
                } else if ($info['award_type'] == 2) {
                    $coupon              = D('Coupon/Coupon')->getInfo($info['coupon_id']);
                    $info['award_title'] = isset($coupon['title'])? $coupon['title']:'';
                    $info['coupon_num']  = isset($coupon['num'])?$coupon['num']:0;
                } else if ($info['award_type'] == 4) {
                    $info['award_title'] = '返现金额 ' . $info['money'] . '元';
                }
            }
            S($key, $info, 86400);
        }
        return $info;
    }

    public function updateInfo($id, $data = [])
    {
        $map['id'] = $id;
        $res       = $this->where(wp_where($map))->update($data);
        if ($res!==false) {
            $this->clearCache($id);
        }
        return $res;
    }
    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
    	addWeixinLog($id,'delcladksl');
    	if (is_array($id)){
    		foreach ($id as $ii){
    			$this->getInfo($ii,true);
    		}
    		return 1;
    	}else{
    		return $this->getInfo($id, true);
    	}
    }
    
}
