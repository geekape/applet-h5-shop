<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class Stores extends base
{

    public $info = array(
        'name' => 'Stores',
        'title' => '门店选择',
        'description' => '支持动态从商城门店里选择',
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
     * {:W('common/Stores/html', array('is_all_store'=>$data['is_all_store'],'stores_ids'=>$data['stores_ids']))}
     */
    public function html($data = [])
    {
        // dump($data);
        $data['title'] = isset($data['title']) ? $data['title'] : '自提门店';
        
        $data['is_all_store'] = isset($data['is_all_store']) ? $data['is_all_store'] : 0;
        $data['stores_ids'] = isset($data['stores_ids']) ? $data['stores_ids'] : '';
        $data['stores_list'] = D('shop/Stores')->getListByIds($data['stores_ids']);
        // dump ( $data );exit;
        $this->assign('data', $data);
        $html = $this->fetch('common@widget/stores');
        echo $html;
    }
}