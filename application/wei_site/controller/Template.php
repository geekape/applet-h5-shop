<?php
namespace app\wei_site\controller;

use app\wei_site\controller\Base;

class Template extends Base
{

    public function initialize()
    {
        parent::initialize();
    }

    // 首页模板
    public function index()
    {
        $this->_getTemplateByDir();
        
        $this->assign('next_url', U('wei_site/Slideshow/lists'));
        return $this->fetch();
    }

    // 二级分类
    public function subcate()
    {
        // 使用提示
        $this->_getTemplateByDir('template_subcate');
        return $this->fetch('index');
    }

    public function list_subcate()
    {
        $isAjax = I('isAjax');
        $isRadio = I('isRadio');
        // 使用提示
        $this->_getTemplateByDir('template_subcate');
        $this->assign('isRadio', $isRadio);
        return $this->fetch('ajax_index');
    }

    // 分类列表模板
    public function lists()
    {
        $this->_getTemplateByDir('template_lists');
        
        $this->assign('next_url', U('wei_site/template/detail'));
        
        return $this->fetch();
    }

    // 详情模板
    public function detail()
    {
        $this->_getTemplateByDir('template_detail');
        
        $this->assign('next_url', U('wei_site/Cms/lists'));
        
        return $this->fetch();
    }

    // 底部菜单模板
    public function footer()
    {
        $this->_getTemplateByDir('template_footer');
        
        $this->assign('next_url', U('wei_site/Footer/lists'));
        
        return $this->fetch();
    }

    // 保存切换的模板
    public function save()
    {
        $act = I('post.type');
        $config['template_' . $act] = I('post.template');
        D('common/PublicConfig')->setConfig(MODULE_NAME, $config);
        echo 1;
    }

    // 获取目录下的所有模板
    public function _getTemplateByDir($type = 'template_index')
    {
        $action = strtolower(ACTION_NAME);
        $default = $this->config['template_' . $action];
        // dump($default);
        $dir = env('app_path') . MODULE_NAME . '/view/' . $type;
        $url = SITE_URL . '/application/' . MODULE_NAME . '/view/' . $type;
        
        $dirObj = opendir($dir);
        while (false !== ($file = readdir($dirObj))) {
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
            if (file_exists($dir . '/' . $file . '/info.php')) {
                $res['icon'] = __ROOT__ . '/wei_site/' . $type . '/' . $file . '/icon.png';
            } else {
                $res['icon'] = ADDON_PUBLIC_PATH . '/default.png';
            }
            
            // 默认选中
            if ($default == $file) {
                $res['class'] = 'selected';
                $res['checked'] = 'checked="checked"';
            } else {
                $res['class'] = '';
                $res['checked'] = '';
            }
            isset($res['desc']) || $res['desc'] = '';
            
            $tempList[] = $res;
            unset($res);
        }
        
        closedir($dirObj);
        // 兼容pigcms
        if ($type != 'template_footer' && $type != 'template_lists' && $type != 'template_subcate' && file_exists(env('app_path') . MODULE_NAME . '/view/pigcms/index.Tpl.php')) {
            if ($type == 'template_detail') {
                // $pigcms_temps = require_once env('app_path') . MODULE_NAME . '/view/pigcms/cont.Tpl.php';
            } else {
                $pigcms_temps = require_once env('app_path') . MODULE_NAME . '/view/pigcms/index.Tpl.php';
            }
            foreach ($pigcms_temps as $p) {
                $res['dirName'] = $p['tpltypename'];
                $res['title'] = '模板' . $p['tpltypeid'];
                
                $res['desc'] = $p['tpldesinfo'];
                
                // 获取效果图
                $res['icon'] = __ROOT__ . '/wei_site/pigcms/images/' . $p['tplview'];
                
                // 默认选中
                if ($default == $p['tpltypename']) {
                    $res['class'] = 'selected';
                    $res['checked'] = 'checked="checked"';
                }
                
                $tempList[] = $res;
                unset($res);
            }
        }
        // dump ( $pigcms_temps );
        // exit ();
        
        // dump ( $tempList );
        
        $this->assign('tempList', $tempList);
    }
}
