<?php
namespace app\draw\model;

use app\home\model\Weixin;

/**
 * Draw的微信模型
 */
class WeixinAddon extends Weixin
{

    function reply($dataArr, $keywordArr = [])
    {
//         $config = getAddonConfig('Draw'); // 获取后台插件的配置参数
                                             // dump($config);
        
        $map['wpid'] = get_wpid();
        $keywordArr['aim_id'] && $map['id'] = $keywordArr['aim_id'];
        $data = M('lottery_games')->where(wp_where($map))->find();
        
        // 其中wpid和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
        $param['wpid'] = get_wpid();
        $param['openid'] = get_openid();
        $param['games_id'] = $data['id'];
        $url = U('draw/Wap/index', $param);
        $articles[0] = array(
            'Title' => $data['title'],
            'Url' => $url,
            'Description' => $data['remark']
        );
        switch ($data['game_type']) {
            case 1:
                $articles[0]['PicUrl'] = !empty($data['cover'] )? get_cover_url($data['cover']) : SITE_URL . '/draw/guaguale_cover.jpg';
                break;
            case 2:
            	$articles[0]['PicUrl'] =  !empty($data['cover'] ) ? get_cover_url($data['cover']) : SITE_URL . '/draw/dzp_cover.jpg';
                break;
            case 3:
            	$articles[0]['PicUrl'] = !empty($data['cover'] ) ? get_cover_url($data['cover']) : SITE_URL . '/draw/zjd_cover.jpg';
                break;
            case 4:
            	$articles[0]['PicUrl'] = !empty($data['cover'] ) ? get_cover_url($data['cover']) : SITE_URL . '/draw/nine_cover.jpg';
                break;
        }
        
        $this->replyNews($articles);
    }
}
        	