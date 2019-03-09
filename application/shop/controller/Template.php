<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class Template extends Base
{

    function initialize()
    {
        parent::initialize();
        
        // 使用提示
        $param['mdm'] = input('mdm');
        $param['pbid'] = get_pbid();
        $normal_tips = '点击选中下面模板即可实时切换模板，请慎重点击。选择后可点击<a target="_blank" href="' . U('Shop/Wap/index', $param) . '">这里</a>进行预览';
        $this->assign('normal_tips', $normal_tips);
    }

    // 模板列表
    function lists()
    {
        $shop = D('Shop')->getInfo(WPID);
        $this->_getTemplateByDir($shop['template']);
        
        return $this->fetch('index');
    }

    // 保存切换的模板
    function save()
    {
        $save['template'] = I('template');
        D('Shop')->updateById(WPID, $save);
        echo 1;
    }

    // 获取目录下的所有模板
    function _getTemplateByDir($default = '')
    {
        empty($default) && $default = 'jd';
        $dir = env('app_path') . '/shop/view/wap/template/';
        
        $dirObj = opendir($dir);
        while (( false!==($file = readdir($dirObj)) )) {
            if ($file === '.' || $file == '..' || $file == '.svn' || is_file($dir . '/' . $file)) {
                continue;
            }
            
            $res['dirName'] = $res['title'] = $file;
            
            // 获取配置文件
            if (file_exists($dir . '/' . $file . '/info.php')) {
                $info = require_once $dir . '/' . $file . '/info.php';
                $res = array_merge($res, $info);
            }
            
            // 获取效果图
            if (file_exists($dir . '/' . $file . '/icon.png')) {
                $res['icon'] = __ROOT__ . '/shop/template/' . $file . '/icon.png';
            } else {
                $res['icon'] = ADDON_PUBLIC_PATH . '/default.png';
            }
            
            // 默认选中
            if ($default == $file) {
                $res['class'] = 'selected';
                $res['checked'] = 'checked="checked"';
            }
            
            $tempList[] = $res;
            unset($res);
        }
        closedir($dir);
        
        // dump ( $tempList );
        
        $this->assign('tempList', $tempList);
    }
}
