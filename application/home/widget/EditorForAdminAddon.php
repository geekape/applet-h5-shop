<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace app\home\widget;

use think\controller;
//use app\common\controller\Plugin;
use think\facade\Config;

/**
 * 编辑器插件
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class EditorForAdminAddon extends Controller
{

    public $info = array(
        'name' => 'EditorForAdmin',
        'title' => '后台编辑器',
        'description' => '用于增强整站长文本的输入和显示',
        'status' => 1,
        'author' => 'thinkphp',
        'version' => '0.2'
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
     * @param
     *            array('name'=>'表单name','value'=>'表单对应的值')
     */
    public function adminArticleEdit($data)
    {
        Config::load(env('app_path') . 'home/config.php');
        $uploadDriver = strtolower(config("picture_upload_driver"));
        if ($uploadDriver == 'qiniu') {
            $driverfile = 'ueditor_qiniu';
        } else {
            $driverfile = 'ueditor';
        }
        $this->assign('driver_file', $driverfile);
        $data['is_mult'] = isset($data['is_mult']) ? intval($data['is_mult']) : 0; // 默认不传时为0
        $this->assign('addons_data', $data);
        $config = $this->getConfig();
        $resizeType = $config['editor_resize_type'] == 1 ? 1 : 0;
        $is_mult = isset($data['is_mult']) ? $data['is_mult'] : '';
        $this->assign('is_mult', $is_mult);

        $hasBtnClass = isset($data['btnClassName']) ? $data['btnClassName'] : '';
        $this->assign('has_btn_class', $hasBtnClass);
        $editor_wysiwyg = isset($config['editor_wysiwyg'])?$config['editor_wysiwyg']:1;
        $this->assign('editor_wysiwyg',$editor_wysiwyg);
        $this->assign('resize_type', $resizeType);
        $this->assign('addons_config', $config);
        $this->assign('styleUrl', '');
        //return $this->fetch('content'.$config['editor_type']);
        return $this->fetch('common@widget/content_edit');
    }

    /**
     * 编辑器挂载的后台文档模型文章内容钩子
     *
     * @param
     *            array('name'=>'表单name','value'=>'表单对应的值')
     */
    public function uploadImg($data)
    {
        $this->assign('addons_data', $data);
        $this->assign('addons_config', $this->getConfig());
        $uploadDriver = strtolower(config("EDITOR_PICTURE_UPLOAD_DRIVER"));
        if ($uploadDriver == 'qiniu') {
            $driverfile = 'ueditor_qiniu';
        } else {
            $driverfile = 'ueditor';
        }
        $this->assign('driver_file', $driverfile);

        return $this->fetch('uploadBtn');
    }
}
