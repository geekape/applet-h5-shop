<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class Material extends base
{

    public $info = array(
        'name' => 'Material',
        'title' => '素材选择',
        'description' => '支持动态从素材库里选择素材',
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
     * table=addons&type=1&value_field=name&title_field=title&order=id desc
     */
    public function material($data = [])
    {
        // dump($data);
        $data['default_type'] = "";
        $data['default_id'] = "";
        $data['news_id'] = "";
        $data['img_id'] = "";
        $data['voice_id'] = "";
        $data['video_id'] = "";
        
        $data['default_value'] = isset($data['value']) ? $data['value'] : '';
        $valArr = wp_explode($data['value'], ':');
        isset($valArr[0]) || $valArr[0] = '';
        isset($valArr[1]) || $valArr[1] = '';
        $data[$valArr[0] . '_id'] = $valArr[1];
        // dump($data); // exit;
        $this->assign($data);
        $html = $this->fetch('common@widget/material');
        echo $html;
    }
}