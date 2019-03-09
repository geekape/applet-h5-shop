<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * DiyPage模型
 */
class DiyPage extends Base
{

    protected $table = DB_PREFIX . 'shop_page';

    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }

    public function getInfo($id, $update = false, $data = [])
    {
        $key = cache_key('id:' . $id, $this->table);
        $info = S($key);
        if ($info === false || $update || true) {
            // TODO 关闭缓存
            if ($data) {
                $info = $data;
            } else {
                $info = $this->findById($id);
            }
            if (empty($info)) {
                return $info;
            }
            $configData = $info['config'];
            if (! empty($configData)) {
                $configDataJson = json_decode(urldecode($configData), true);
                $goodsDao = D('shop/ShopGoods');
                $newGoodsData = [];
                if (! empty($configDataJson)) {
                    foreach ($configDataJson as &$json) {
                        if ($json['id'] == "goods" || $json['id'] == "mutipic_goods") {
                            $goodsData = $json['params']['goods_list'];
                            
                            foreach ($goodsData as $g) {
                                if ($g['id'] > 0) {
                                    $goodsInfo = $goodsDao->getInfo($g['id']);
                                    $gInfo['id'] = $g['id'];
                                    $gInfo['title'] = $goodsInfo['title'];
                                    $gInfo['img'] = $goodsInfo['cover'];
                                    $gInfo['url'] = U('shop/wap/goods_detail', array(
                                        'id' => $g['id']
                                    ));
                                    $gInfo['stock'] = $goodsInfo['stock'];
                                    $gInfo['$$hashKey'] = $g['$$hashKey'];
                                    // if (isset($goodsInfo['sku_data'])){
                                    // foreach ($goodsInfo['sku_data'] as $ss){
                                    // if (!empty($ss['sale_price'])){
                                    // $gInfo['market_price'] = $ss['sale_price'];
                                    // }else{
                                    // $gInfo['market_price'] = $ss['market_price'];
                                    // }
                                    // }
                                    // }else{
                                    if (floatval($goodsInfo['sale_price']) > 0) {
                                        $gInfo['market_price'] = $goodsInfo['sale_price'];
                                    } else {
                                        $gInfo['market_price'] = $goodsInfo['market_price'];
                                    }
                                    // }
                                    
                                    $newGoodsData[] = $gInfo;
                                    unset($gInfo);
                                }
                            }
                        }
                        $json['params']['goods_list'] = $newGoodsData;
                        // dump($goodsData['params']['goods_list']);
                    }
                }
                $info['config'] = rawurlencode(json_encode($configDataJson));
                // $info['config'] = json_encode($configDataJson);
                S($key, $info);
            }
        }
        return $info;
    }

    function getInfoByPage($page)
    {
        $map['wpid'] = get_wpid();
        $map['use'] = $page;
        $diyRes = $this->where(wp_where($map))->find();
        if ($diyRes) {
            $diyData = $this->getInfo($diyRes['id'], false, $diyRes);
        } else {
            $diyData['config'] = '';
        }
        return $diyData;
    }
}
