<?php
/*
 * 商城接口
 */
namespace app\shop\controller;

use app\common\controller\ApiBase;

// 自动生成API文档的命令：apidoc -f "Api.php" -i e:/htdocs/weiphp5.0/application/ -o e:/htdocs/weiphp5.0/apidoc/
class ApiServer extends ApiBase
{

    protected $accesstoken = 123456;

    function initialize()
    {
        parent::initialize();
        
        if (ACTION_NAME != 'token') {
            $this->check_access_token();
        }
    }

    function token()
    {
        $accessToken = '';
        
        $map['appid'] = I('appid');
        $map['secret'] = I('appsecret');
        if (empty($map['appid']) || empty($map['secret'])) {
            return $this->api_error('appid或secret参数不能为空');
        }
        
        $dao = M('api_access_token');
        
        $cache_key = 'api_access_token_' . $map['appid'];
        $access_token = S($cache_key);
        if ($access_token === false) {
            // 先从数据库中找找2小时内有效的access_token
            $map['cTime'] = [
                'gt',
                NOW_TIME - $this->expires_in
            ];
            
            $info = $dao->where($map)->find();
            if (! empty($info)) {
                $access_token = $info['access_token'];
                $time = $this->expires_in - (NOW_TIME - $info['cTime']);
                S($cache_key, $access_token, $time);
                S('check_' . $access_token, 1, $time);
            }
            unset($map['cTime']);
        }
        // 重新分配access_token
        if (empty($access_token)) {
            // 先判断appid和secret是否正确
            $list = M('public_config')->where('pkey', 'shop_shop')->column('pvalue');
            $check = false;
            foreach ($list as $val) {
                $value = json_decode($val, true);
                
                if (isset($value['erp_appid']) && isset($value['erp_appsecret']) && $value['erp_appid'] == $map['appid'] && $value['erp_appsecret'] == $map['secret']) {
                    $check = true;
                    break;
                }
            }
            if (! $check) {
                return $this->api_error('appid或secret参数不对');
            }
            
            $rand = rand(10, 99);
            $access_token = md5(NOW_TIME . $rand . $map['appid']);
            S($cache_key, $access_token, $this->expires_in);
            S('check_' . $access_token, 1, $this->expires_in);
            
            $map['access_token'] = $access_token;
            $map['cTime'] = NOW_TIME;
            $res = $dao->insert($map);
            if (! $res) {
                return $this->api_error('保存access_token失败');
            }
        }
        
        $this->del_old_data();
        
        return $this->api_success([
            'access_token' => $access_token,
            'expires_in' => $this->expires_in
        ]);
    }

    /**
     * @api {POST} index.php/shop/api_server/CouponSetUse
     * @apiName ERP端核销优惠券时推送状态到微信
     * @apiGroup 商城
     *
     * @apiParam {string} sn_code SN码
     * @apiParam {string} admin_openid 操作员的openid
     * @apiParam {int} use_time 核销时间（时间戳）
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 1,
     * "msg": "操作成功",
     * }
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 0,
     * "msg": "错误原因",
     * }
     */
    function CouponSetUse()
    {
        $dao = D('common/SnCode');
        
        $sn = I('sn_code');
        $use_time = input('use_time');
        $openid = input('openid');
        
        $info = $dao->where('sn', $sn)->find();
        if (! $info) {
            return $this->api_error('该券不存在');
        }
        if ($info['is_use']) {
            return $this->api_error('该券已经使用过，请不要重复使用');
        }
        $coupon = D('coupon/Coupon')->getInfo($info['target_id']);
        if (! $coupon) {
            return $this->api_error('该券不存在！');
        }
        
        $info['is_use'] = $save['is_use'] = 1;
        $save['can_use'] = 0;
        $info['use_time'] = $save['use_time'] = empty($use_time) ? time() : $use_time;
        $save['admin_uid'] = M('public_follow')->where('openid', $openid)->value('uid');
        
        $res = $dao->updateInfo($info['id'], $save);
        
        $map['is_use'] = 1;
        $map['target_id'] = $info['target_id'];
        $save2['use_count'] = intval($dao->where(wp_where($map))->count());
        
        D('coupon/Coupon')->updateInfo($info['target_id'], $save2);
        
        $data['msg'] = '核销成功';
        
        return $this->api_success($data);
    }

    /**
     * @api {POST} index.php/shop/api_server/orderLists
     * @apiName ERP端获取订单消息
     * @apiGroup 商城
     *
     * @apiParam {string} update_at 更新时间（时间戳），只获取这个时间后被更新的订单列表，为空时返回所有
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     * @apiSuccess {array} data 订单信息
     */
    function orderLists()
    {
        $data = D('shop/Order')->orderListByErp();
        
        return $this->api_success($data);
    }

    /**
     * @api {POST} index.php/shop/api_server/publicAccessToken
     * @apiName 获取公众号的access_token
     * @apiGroup 商城
     *
     * @apiParam {string} access_token 系统接口的token
     * @apiParam {string} update 是否强制刷新token，0:不强制（默认），1：强制
     *
     * @apiSuccess {int} code 成功时返回1
     * @apiSuccess {string} msg 提示信息：操作成功
     * @apiSuccess {array} data 订单信息
     */
    function publicAccessToken()
    {
        $access_token = I('access_token');
        
        $map['access_token'] = $access_token;
        $info = M('api_access_token')->where($map)->find();
        if (! isset($info['appid'])) {
            return $this->api_error('ERP的access_token不存在');
        }
        
        $wpid = M('public_config')->where('pkey', 'shop_shop')
            ->whereLike('pvalue', "%{$info['appid']}%")
            ->value('wpid');
        if (! $wpid) {
            return $this->api_error('获取公众号信息失败');
        }
        
        $is_update = input('update/d', 0);
        $update = $is_update == 0 ? false : true;
        $data = get_access_token($wpid, $update);
        return $this->api_success($data);
    }

    function stock()
    {
        $productid = input('productid');
        if (! empty($productid)) {
            $map['productid'] = $productid;
        } else {
            $map['productid'] = [
                'gt',
                input('lastid/d', 0)
            ];
        }
        
        $goodsArr = $productArr = [];
        $list_data = D('shop/ShopGoods')->where(wp_where($map))
            ->order('id desc')
            ->limit(20)
            ->field('id,productid')
            ->select();
        
        foreach ($list_data as $d) {
            $productArr[$d['id']] = $d['productid'];
            $goodsArr[] = $d['id'];
        }
        
        $goods_ids = implode(',', $goodsArr);
        $where = "(event_type=0 and goods_id in($goods_ids) ) OR (event_type>0 and shop_goods_id in($goods_ids) )";
        
        $stock_lists = D('shop/Stock')->where($where)
            ->field('stock_id,stock,stock_active,lock_count,sale_count,event_type,goods_id,shop_goods_id')
            ->select();
        
        $data = [];
        foreach ($stock_lists as $k => $s) {
            $s = $s->toArray();
            $type = $s['event_type'];
            $gid = $type == 0 ? $s['goods_id'] : $s['shop_goods_id'];
            $pid = $productArr[$gid];
            unset($s['goods_id'], $s['shop_goods_id'], $s['event_type']);
            $s['stocktype'] = $type;
            
            $data[$pid][] = $s;
        }
        
        // 转换输出的格式
        $result = [];
        foreach ($data as $pid => $vo) {
            $result[] = [
                'productid' => $pid,
                'stockdata' => $vo
            ];
        }
        
        return $this->api_success($result);
    }

    function noticeFailOrderList()
    {
        $ids = D('shop/Order')->where('notice_erp', '>', 0)->column('id');
        if (! $ids) {
            return $this->api_success([]);
        }
        
        $data = D('shop/Order')->orderListByErp($ids);
        return $this->api_success($data);
    }

    function noticeSuccess()
    {
        $ids = wp_explode(input('id'));
        if (! $ids) {
            return $this->api_error('订单ID不为空');
        }
        
        D('shop/Order')->whereIn('id', $ids)->setField('notice_erp', 0);
        return $this->api_success([]);
    }
}
