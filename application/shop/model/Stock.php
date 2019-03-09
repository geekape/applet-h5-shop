<?php
namespace app\shop\model;

use app\common\model\Base;
use think\Db;

/**
 * 库存处理模型
 *
 * 注意：可用库存已经通过数据库的触发器实现自动更新，因为在库存变化时不再需要处理它
 */
class Stock extends Base
{

    protected $table = DB_PREFIX . 'shop_goods_stock';

    function getInfoByGoodsId($goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        $Obj = $this->where('goods_id', $goods_id)->where('event_type', $event_type);
        $infoObj = $Obj->field(true)
            ->order('stock_id desc')
            ->find();
        if (! empty($infoObj)) {
            $info = $infoObj->toArray();
            if (empty($info['sale_price']) || $info['sale_price'] == '0.00') {
                $info['sale_price'] = $info['market_price'];
            }
        } else {
            $info = [];
        }
        
        return $info;
    }

    function getListByGoodsIds($goods_ids, $event_type = SHOP_EVENT_TYPE)
    {
        $Obj = $this->whereIn('goods_id', $goods_ids)->where('event_type', $event_type);
        $list = $Obj->field(true)->select();
        
        $res = [];
        if (! empty($list)) {
            foreach ($list as $info) {
                if (empty($info['sale_price']) || $info['sale_price'] == '0.00') {
                    $info['sale_price'] = $info['market_price'];
                }
                $res[$info['goods_id']] = $info;
            }
        }
        return $res;
    }

    // 保存库存数据
    function saveStock($goods_id, $post, $event_type = SHOP_EVENT_TYPE)
    {
        if (! isset($post['stock']) || empty($post['stock']))
            return false;
        
        $info = $this->where('goods_id', $goods_id)
            ->where('event_type', $event_type)
            ->where('del_at', 0)
            ->find();
        
        $data = [
            'stock' => $post['stock'],
            'market_price' => $post['market_price'],
            'sale_price' => $post['sale_price']
        ];
        if (! empty($info)) {
            if ($event_type == SHOP_EVENT_TYPE) {
            	//直接保存不增加
            	$data['stock'] =  $post['stock'];
//                 $data['stock'] = Db::raw('stock+' . $post['stock']);
            }
            $this->where('stock_id', $info['stock_id'])->update($data);
        } else {
            $data['goods_id'] = $goods_id;
            $data['event_type'] = $event_type;
            return $this->insert($data);
        }
    }

    /*
     * 用户下单锁定库存
     *
     * 操作：物理库存不变, 锁定库存增加, 可用库存扣除, 销量时不变
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function beforeOrder($need_stock, $goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        // dump($goods_id);dump($event_type);
        $goods = $this->getInfoByGoodsId($goods_id, $event_type);
        // dump($goods);
        $goods['del_at'] = isset($goods['del_at'] )?$goods['del_at'] :0;
        if (empty($goods) || $goods['del_at'] > 0) {
            exception('商品已经下架');
        }
        
        $save['lock_count'] = Db::raw('lock_count+' . $need_stock); // 锁定库存增加
        $res = $this->where('goods_id', $goods_id)
            ->where('event_type', $event_type)
            ->where('stock_active', '>=', $need_stock)
            ->update($save);
        if (! $res) {
            exception('抱歉，商品已被抢光');
        }
        
        return $res;
    }

    /*
     * 用户支付后
     *
     * 操作：物理库存扣除，锁定库存释放，可用库存不变，销售量增加
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function afterPayment($need_stock, $goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        $save['stock'] = Db::raw('stock-' . $need_stock); // 物理库存扣除
        $save['lock_count'] = Db::raw('lock_count-' . $need_stock); // 锁定库存释放
        $save['sale_count'] = Db::raw('sale_count+' . $need_stock); // 销售量增加
        $res = $this->where('goods_id', $goods_id)
            ->where('event_type', $event_type)
            ->where('stock', '>=', $need_stock)
            ->update($save);
        if (! $res) {
            $sql = $this->getLastSql();
            file_log($sql, 'afterPayment'); // 记录失败的日志
        }
        
        return $res;
    }

    function afterPaymentByOrder($order)
    {
        if (! isset($order['goods'])) {
            $order['goods'] = json_decode($order['goods_datas'], true);
        }
        foreach ($order['goods'] as $goods) {
            $this->afterPayment($goods['num'], $goods['id'], $order['event_type']);
        }
    }

    /*
     * 用户取消订单，退款退货
     *
     * 操作：物理库存不变, 锁定库存减少, 可用库存增加, 销售量不变
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function canelUnPayOrder($need_stock, $goods_id, $event_type = SHOP_EVENT_TYPE)
    {
    	
        $res = $this->reBackEventStockByDel($need_stock, $goods_id, $event_type,1);
        if (! $res) {
            $save['lock_count'] = Db::raw('lock_count-' . $need_stock); // 物理库存增加
            $res = $this->where('goods_id', $goods_id)
                ->where('event_type', $event_type)
                ->where('lock_count', '>=', $need_stock)
                ->update($save);
        }
        return $res;
    }

    private function reBackEventStockByDel($need_stock, $goods_id, $event_type,$isUnPay=0)
    {
        $del_at = 0;
        if ($event_type != SHOP_EVENT_TYPE) {
            $event_goods = $this->getInfoByGoodsId($goods_id, $event_type);
            $del_at = isset($event_goods['del_at'])?$event_goods['del_at']:0;
        }
        //加$isUnPay 这个是为了定时任务释放超过30分钟内未支付的订单（活动商品还未被删除的情况）
        if (($del_at > 0|| $isUnPay) && $event_type != SHOP_EVENT_TYPE) {
            // 活动商品扣除
            $save['lock_count'] = Db::raw('lock_count-' . $need_stock); // 锁定库存扣除
            $res = $this->where('goods_id', $goods_id)
                ->where('event_type', $event_type)
                ->where('lock_count', '>=', $need_stock)
                ->update($save);
            if (! $res) {
                exception('活动商品库存扣除失败'.M()->getLastSql());
            }
            //判断营销活动的商品是否被删除了（活动结束会自动删除），若删除的话将释放的库存退回商城            
            if ($del_at>0){
            	//将活动商品库存删除（不删除的话，在活动的商品管理那删除商品，库存又退回商城，这样商城的库存就多了）
            	$this->where('goods_id', $goods_id)
            	->where('event_type', $event_type)
            	->where('stock','egt',$need_stock)
            	->update([
            			'stock' => Db::raw('stock-' . $need_stock)
            	]);
            	
            	// 退回库存给商城
            	$res = $this->where('goods_id', $event_goods['shop_goods_id'])
            	->where('event_type', SHOP_EVENT_TYPE)
            	->update([
            			'stock' => Db::raw('stock+' . $need_stock)
            	]);
            	if (! $res) {
            		exception('退回库存给商城失败');
            	}
            }
           
            return true;
        }
        return false;
    }

    /*
     * 用户取消订单，退款退货
     *
     * 操作：物理库存增加, 锁定库存不变, 可用库存增加, 销售量扣除
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function canelOrder($need_stock, $goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        $res = $this->reBackEventStockByDel($need_stock, $goods_id, $event_type);
        if (! $res) {
            $save['stock'] = Db::raw('stock+' . $need_stock); // 物理库存增加
            $save['sale_count'] = Db::raw('sale_count-' . $need_stock); // 销售量扣除
            $res = $this->where('goods_id', $goods_id)
                ->where('event_type', $event_type)
                ->where('sale_count', '>=', $need_stock)
                ->update($save);
            if (! $res) {
                $sql = $this->getLastSql();
                file_log($sql, 'canelOrder'); // 记录失败的日志
            }
        }
        return $res;
    }

    /*
     * 创建活动商品时直接从商品库存里划走
     *
     * 操作：物理库存扣除, 锁定库存增加, 可用库存扣除, 销量时不变
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function eventLock($goods_id, $event_type, $data, $old_data = [])
    {
        $result['code'] = $has_change = 0;
        $need_stock = $data['stock'];
        $stock = $this->getInfoByGoodsId($goods_id, $event_type);
        if (! empty($old_data)) { // 编辑的情况下重新计算库存锁定的值
            if ($old_data['shop_goods_id'] != $data['shop_goods_id']) {
                // 切换了商品的情况下, 划回到商城中库存
                $old_save['stock'] = Db::raw('stock+' . $old_data['stock_active']); // 物理库存增加
                $res = $this->where('goods_id', $old_data['shop_goods_id'])
                    ->where('event_type', SHOP_EVENT_TYPE)
                    ->update($old_save);
                // 旧商品库存释放，设置为删除状态，方便后面的锁定库存解锁时直接返回商城
                $this->where('goods_id', $old_data['goods_id'])
                    ->where('event_type', $event_type)
                    ->update([
                    'stock' => Db::raw('stock-' . $old_data['stock_active']),
                    'del_at' => NOW_TIME
                ]);
                
                $has_change = 1;
                $stock = [];
            } else { // 不换商品情况,计算差额
                $need_stock = $data['stock'] - $old_data['stock_active']; // 要释放的是可用库存，防止有锁定库存的情况下stock!=stock_active,导致减为负数而报错
            }
        }
        
        $add = [
            'market_price' => $data['market_price'],
            'sale_price' => $data['sale_price'],
            'shop_goods_id' => $data['shop_goods_id']
        ];
        if ($need_stock > 0) {
            $save['stock'] = Db::raw('stock-' . $need_stock); // 商城物理库存扣除
            if (! empty($stock)) {
                $add['stock'] = $has_change ? $need_stock : Db::raw('stock+' . $need_stock); // 活动物理库存增加
            }
        } else {
            $need_stock = abs($need_stock);
            $save['stock'] = Db::raw('stock+' . $need_stock); // 商城物理库存划回
            if (! empty($stock)) {
                $add['stock'] = Db::raw('stock-' . $need_stock); // 活动物理库存扣除
            }
        }
        
        $res = $this->where('goods_id', $data['shop_goods_id'])
            ->where('event_type', SHOP_EVENT_TYPE)
            ->update($save);
        
        if (empty($stock)) { // 增加的情况下
            $add['stock'] = $need_stock;
            
            $add['goods_id'] = $goods_id;
            $add['event_type'] = $event_type;
            $res = $this->insert($add);
        } else { // 编辑的情况
            $res = $this->where('stock_id', $stock['stock_id'])->update($add);
        }
        
        $result['code'] = 1;
        return $result;
    }

    /*
     * 活动结束，释放库存回商城
     *
     * 物理库存增加：原活动物理库存 + 活动锁定库存 - 活动销量
     * 物理锁定库存释放：释放量=原活动物理库存 + 活动锁定库存 + 活动销量（即活动创建时被锁定的库存）
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function eventEnd($need_stock, $event_goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        $save['stock'] = Db::raw('stock-' . $need_stock); // 物理库存扣除
        $save['lock_count'] = Db::raw('lock_count+' . $need_stock); // 锁定库存增加
        $res = $this->where('goods_id', $event_goods_id)
            ->where('event_type', $event_type)
            ->where('sale_count', '>=', $need_stock)
            ->update($save);
        
        return $res;
    }

    /*
     * 活动商品被删除时，释放库存回商城
     *
     * 物理库存增加：原活动物理库存 + 活动锁定库存 - 活动销量
     * 物理锁定库存释放：释放量=原活动物理库存 + 活动锁定库存 + 活动销量（即活动创建时被锁定的库存）
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function eventGoodsDel($event_goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        // 先查出原活动商品
        $event_goods = $this->getInfoByGoodsId($event_goods_id, $event_type);
        if (empty($event_goods))
            return false;
        
        // 可释放的可用库存 ，注：锁定库存是由订单释放，不在此释放，因此此表的数据不能删除
        $stock = $event_goods['stock_active'];
        if ($stock > $event_goods['stock']) {
            $stock = $event_goods['stock']; // 减少异常数据导致的SQL报错
        }
        
        // 设置原活动商品删除时间
        $this->where('goods_id', $event_goods_id)
            ->where('event_type', $event_type)
            ->where('stock','egt',$stock)
            ->update([
            'del_at' => NOW_TIME,
            'stock' => Db::raw('stock-' . $stock)
        ]);
        
        $res = true;
        if ($stock > 0) {
            $old_save['stock'] = Db::raw('stock+' . $stock); // 物理库存增加
            $res = $this->where('goods_id', $event_goods['shop_goods_id'])
                ->where('event_type', SHOP_EVENT_TYPE)
                ->update($old_save);
        }
        
        return $res;
    }

    /*
     * 把可用库存释放回ERP
     *
     * 物理库存减少，可用库存自动变为0，其它量不变
     *
     *
     * @param $need_stock int 需要锁定的商品数
     * @param $goods_id int 需要锁定的商品ID
     * @param $event_type int 商品属于哪个活动
     */
    function backToErp($need_stock, $event_goods_id, $event_type = SHOP_EVENT_TYPE)
    {
        // 物理库存减少
        $res = $this->where('goods_id', $event_goods_id)
            ->where('event_type', $event_type)
            ->where('stock', '>=', $need_stock)
            ->update([
            'stock' => Db::raw('stock-' . $need_stock)
        ]);
        
        return $res;
    }

    function checkStock($data, $shop_goods = [], $old_data = [])
    {
        $result['code'] = 0;
        if (empty($shop_goods)) {
            $shop_goods = $this->getInfoByGoodsId($data['shop_goods_id'], SHOP_EVENT_TYPE);
            $stock = $data['stock'];
            if ($stock > 0 && $stock > $shop_goods['stock_active']) {
                $result['msg'] = '商品数量不能大于商城可用库存!';
                return $result;
            }
        } else { // 编辑的情况下重新计算库存锁定的值
            if ($old_data['shop_goods_id'] == $data['shop_goods_id']) { // 同一个商品下情况下
                $stock = $data['stock'] - $old_data['stock'];
                
                if ($stock > 0 && $stock > $shop_goods['stock_active']) {
                    $result['msg'] = '商品数量不能大于商城可用库存.';
                    return $result;
                }
            } else { // 切换了商品的情况下
                $stock = $data['stock'];
                $shop_goods = $this->getInfoByGoodsId($data['shop_goods_id'], SHOP_EVENT_TYPE);
                
                if ($stock > 0 && $stock > $shop_goods['stock_active']) {
                    $result['msg'] = '商品数量不能大于商城可用库存';
                    return $result;
                }
            }
        }
        
        $result['code'] = 1;
        return $result;
    }

    // 判断订单里的商品是否已被删除
    function checkOrderGoodsDelCount($order_list)
    {
        $goods_ids = [];
        $event_type = SHOP_EVENT_TYPE;
        foreach ($order_list as $order) {
            $event_type = $order['event_type'];
            $goods = json_decode($order['goods_datas'], true);
            foreach ($goods as $g) {
                $goods_ids[] = $g['id'];
            }
        }
        
        $count = $this->whereIn('goods_id', $goods_ids)
            ->where('event_type', $event_type)
            ->where('del_at', '>', 0)
            ->count();
        return $count;
    }

    // 商城商品下架
    function setDown($goods_id)
    {
        $info = $this->getInfoByGoodsId($goods_id);
        if ($info['stock_active'] > 0) {
            $this->where('stock_id', $info['stock_id'])
                ->where('stock', '>=', $info['stock_active'])
                ->update([
                'stock' => Db::raw('stock-' . $info['stock_active'])
            ]);
        }
        return $info['stock_active'];
    }

    /*
     * 使用定时任务处理超时未支付的订单
     */
    function cronDealOrderStock($map = [])
    {
        if (empty($map)) {
            // 未支付的
            $map['pay_status'] = 0;
            // 未释放库存的
            $map['is_lock'] = 1;
            // 订单超时30分钟以上的
            $map['cTime'] = [
                '<',
                NOW_TIME - SHOP_STOCK_TIME
            ];
        }
        
        // 一次最多只处理5个订单
        $list = D('shop/Order')->where(wp_where($map))
            ->limit(5)
            ->field('id,event_type,goods_datas,cTime')
            ->order('id asc')
            ->select();
        
        if (empty($list)) {
            return true; // 没有相关订单，不用处理
        }
        // 启动事务
        Db::startTrans();
        try {
            $ids = [];
            foreach ($list as $order) {
                $ids[] = $order['id'];
                if (empty($order['goods_datas']))
                    continue;
                
                $goods = json_decode($order['goods_datas'], true);
                foreach ($goods as $g) {
                    $this->canelUnPayOrder($g['num'], $g['id'], $order['event_type']);
                }
                
                $mod = '';
                if ($order['event_type'] == COLLAGE_EVENT_TYPE) {
                    D('collage/Order')->where('order_id', $order['id'])->setField('is_pay', 3);
                } elseif ($order['event_type'] == HAGGLE_EVENT_TYPE) {
                    // D('haggle/Order')->where('id', $order['event_id'])->setField('is_pay', 3); //砍价不需要，活动期间都可以支付，作废状态由活动自行维护
                } elseif ($order['event_type'] == SECKILL_EVENT_TYPE) {
                    D('seckill/Order')->where('order_id', $order['id'])->setField('is_pay', 3);
                }
            }
            if (! empty($ids)) {
                $res = D('shop/Order')->whereIn('id', $ids)->update([
                    'is_lock' => 0,
                    'pay_status' => 3
                ]);
                if (! $res) {
                    exception('设置订单过期失败');
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            file_log($e->getMessage(), 'cronDealOrderStock');
        }
        return true;
    }
}
