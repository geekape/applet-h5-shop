<?php

namespace app\shop\model;

use app\common\model\Base;

/**
 * DiyPage模型
 */
class ShopPage extends Base
{
    protected $table = DB_PREFIX . 'shop_page';
    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }	
    public function getInfo($id, $update = false, $data = [])
    {
		$key = cache_key('id:'.$id, $this->table);
        $info = S($key);
        if ($info === false || $update || true) {
            // TODO 关闭缓存
            if ($data) {
                $info = $data;
            } else {
                $info = $this->findById($id);
            }
            $configData     = isset($info['config']) ? $info['config'] : '';
            $configDataJson = json_decode(urldecode($configData), true);

            $configDataJson = isset($configDataJson) ? $configDataJson : [];

            $data = [];

            foreach ($configDataJson as $k => &$json) {

                if ($json['id'] == "goods" || $json['id'] == "mutipic_goods") {
                    $goodsData    = $json['params']['goods_list'];
                    $newGoodsData = [];
                    foreach ($goodsData as $g) {
                        if ($g['id'] > 0) {
                            $goodsDao           = D('shop/ShopGoods');
                            $goodsInfo          = $goodsDao->getInfo($g['id']);
                            $gInfo['id']        = $g['id'];
                            $gInfo['title']     = $goodsInfo['title'];
                            $gInfo['img']       = get_cover_url($goodsInfo['cover'], 300, 300);
                            $gInfo['url']       = U ( 'shop/wap/goods_detail', array('id' => $g['id']));
                            $gInfo['stock'] = $goodsInfo['stock'];
                            $gInfo['$$hashKey'] = $g['$$hashKey'];

                            if (floatval($goodsInfo['sale_price']) > 0) {
                                $gInfo['market_price'] = $goodsInfo['sale_price'];
                            } else {
                                $gInfo['market_price'] = $goodsInfo['market_price'];
                            }
//                             }

                            $newGoodsData[] = $gInfo;
                            unset($gInfo);
                        }
                    }
                    $json['params']['goods_list'] = $newGoodsData;
                    //dump($goodsData['params']['goods_list']);
                }
                if ($json['id'] == 'header') {
                    unset($configDataJson[$k]);
                }
                if ($json['id'] == 'piclist') {
                    foreach ($json['params']['pic_list'] as &$vo) {
                        $vo['pic'] = SITE_URL . $vo['pic'];
                    }
                }

//                 $i = 0;
                //                 if($json['id'] =='piclist' || $json['id'] =='title' ){

//                     if($k>=2){
                //                         if( $json['id'] == $configDataJson[$k-1]['id'] || $json['id'] ==$configDataJson[$k-2]['id'] ){
                //                             $i++;
                //                         }

//                     }

//                     $data[$i][] =$json;
                //                 }

            }
            //dump($configDataJson);
            //dump($data);
            //$info['config'] = rawurlencode(json_encode($configDataJson));
            $info['config'] = $configDataJson;

            //$info['config'] = $data;
            S($key, $info);
        }
        return $info;
    }
}
