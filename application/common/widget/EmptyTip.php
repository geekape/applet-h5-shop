<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class EmptyTip extends base
{

    public $info = array(
        'name' => 'EmptyTip',
        'title' => '空页面提示',
        'description' => '',
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

    public function html($data = [])
    {
        $data['remark'] = isset($data['remark']) ? $data['remark'] : '暂无相关数据';

        //dump ( $data );exit;
        $this->assign($data);
        $html = $this->fetch('common@widget/empty_tip');
        echo $html;
    }
}