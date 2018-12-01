<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class Swiper extends base
{

    public $info = array(
        'name' => 'Swiper',
        'title' => '轮播图',
        'description' => '主要用于商品显示，在微信里点击图片可以微信相册的方式打开浏览',
        'status' => 1,
        'author' => '凡星',
        'version' => '0.1',
        'has_adminlist' => 0,
        'type' => 0
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * 编辑器挂载的后台文档模型文章内容钩子
     *
     * {:W('common/Swiper/html', array('imgs_url'=>[]))}
     */
    public function html($data = [])
    {
        // dump($data); // exit;
        $this->assign($data);
        $html = $this->fetch('common@widget/swiper');
        echo $html;
    }
}