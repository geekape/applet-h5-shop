<?php
namespace app\common\widget;

use app\common\controller\base;

/**
 * 素材选择插件
 *
 * @author 凡星
 */
class Express extends base
{

    public $info = array(
        'name' => 'Express',
        'title' => '收货方式选择',
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
     * {:W('common/Express/html', array('send_type'=>'1,2','express'=>'1.00','is_all_store'=>$data['is_all_store'],'stores_ids'=>$data['stores_ids']))}
     */
    public function html($data = [])
    {
        // dump($data);
        $data['title'] = isset($data['title']) ? $data['title'] : '收货方式';
        
        $data['send_type'] = isset($data['send_type']) ? $data['send_type'] : '1';
        $data['express'] = isset($data['express']) ? $data['express'] : 0;
        
        $data['is_all_store'] = isset($data['is_all_store']) ? $data['is_all_store'] : 0;
        $data['stores_ids'] = isset($data['stores_ids']) ? $data['stores_ids'] : '';
        $store_lists = D('shop/Stores')->getListByLinkIds($data['stores_ids']);
        $this->assign('store_lists', $store_lists);
        // dump($data['stores_ids']);
        // dump($store_lists);
        // 获取所有商品门店
        $storeDatas = M('stores')->where('wpid', WPID)->column('name', 'id');
        $this->assign('store_data', $storeDatas);
        
        // dump ( $data );exit;
        $this->assign('data', $data);
        $html = $this->fetch('common@widget/express');
        echo $html;
    }
}