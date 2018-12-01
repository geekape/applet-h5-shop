<?php
namespace app\wei_site\controller;

use app\common\controller\WapBase;

class Wap extends WapBase
{

    protected $config;

    public function initialize()
    {
        parent::initialize();
        $this->assign('nav', null);
        $config = getAddonConfig('wei_site');
        if (! empty($config)) {
            $config['cover_url'] = get_cover_url($config['cover']);
            $config['background_arr'] = explode(',', $config['background']);
            $config['background_id'] = $config['background_arr'][0];
            $config['background'] = get_cover_url($config['background_id']);
        } else {
            $config['cover_url'] = "";
            $config['background_arr'] = "";
            $config['background_id'] = "";
            $config['background'] = "";
        }
        $this->config = $config;
        $this->assign('config', $config);
        // dump($config);
        // dump(get_wpid());
        
        // 定义模板常量
        $act = strtolower(ACTION_NAME);
        $temp = isset($config['template_' . $act]) ? $config['template_' . $act] : '';
        $act = ucfirst($act);
        $title = isset($config['title']) ? $config['title'] : '';
        $this->assign('page_title', $title);
        define('CUSTOM_TEMPLATE_PATH', __ROOT__ . '/wei_site/template');
    }

    // 首页
    public function index()
    {
        // add_credit ( 'weisite', [], 86400 );
        if (file_exists(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_index'] . '.html')) {
            $this->pigcms_index();
            return $this->fetch(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_index'] . '.html');
        } else {
            $map1['wpid'] = $map['wpid'] = get_wpid();
            $map1['is_show'] = $map['is_show'] = 1;
            $map['pid'] = 0; // 获取一级分类
                             
            // 分类
            $category = M('weisite_category')->where(wp_where($map))
                ->order('sort asc, id desc')
                ->select();
            foreach ($category as &$vo) {
                $vo['icon'] = get_cover_url($vo['icon']);
                empty($vo['url']) && $vo['url'] = U('wei_site/Wap/lists', array(
                    'cate_id' => $vo['id']
                ));
            }
            $this->assign('category', $category);
            // dump($category);
            // 幻灯片
            $slideshow = M('weisite_slideshow')->where(wp_where($map1))
                ->order('sort asc, id desc')
                ->select();
            foreach ($slideshow as &$vo) {
                $vo['img'] = get_cover_url($vo['img']);
            }
            
            foreach ($slideshow as &$data) {
                foreach ($category as $cate) {
                    if (isset($data['cate_id']) && $data['cate_id'] == $cate['id'] && empty($data['url'])) {
                        $data['url'] = $cate['url'];
                    }
                }
            }
            $this->assign('slideshow', $slideshow);
            // dump($slideshow);
            
            // dump($category);
            $public_info = get_pbid_appinfo();
            $this->assign('publicid', $public_info['id']);
            
            $this->assign('manager_id', $this->mid);
            
            $this->_footer();
            // $backgroundimg=env('app_path').'wei_site/view/template_index/'.$this->config['template_index'].'/icon.png';
            if ($this->config['show_background'] == 0) {
                $this->config['background'] = '';
                $this->assign('config', $this->config);
            }
            
            $html = empty($this->config['template_index']) ? 'color_v1' : $this->config['template_index'];
            return $this->fetch(env('app_path') . 'wei_site/view/template_index/' . $html . '/index.html');
        }
    }

    // 分类列表
    public function lists()
    {
        $cate_id = I('cate_id/d', 0);
        empty($cate_id) && $cate_id = I('classid/d', 0);
        if (file_exists(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_lists'] . '.html')) {
            $this->pigcms_lists($cate_id);
            return $this->fetch(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_lists'] . '.html');
        } else {
            $map['wpid'] = get_wpid();
            if ($cate_id) {
                $map['cate_id'] = $cate_id;
                $cate = M('weisite_category')->where('id = ' . $map['cate_id'])->find();
                $this->assign('cate', $cate);
                // 二级分类
                $category = M('weisite_category')->where('pid = ' . $map['cate_id'])
                    ->order('sort asc, id desc')
                    ->select();
            }
            if (! empty($category)) {
                foreach ($category as &$vo) {
                    $vo['icon'] = get_cover_url($vo['icon']);
                    empty($vo['url']) && $vo['url'] = U('wei_site/Wap/lists', array(
                        'cate_id' => $vo['id']
                    ));
                }
                $this->assign('category', $category);
                // 幻灯片
                
                $slideshow = M('weisite_slideshow')->where(wp_where($map))
                    ->order('sort asc, id desc')
                    ->select();
                foreach ($slideshow as &$vo) {
                    $vo['img'] = get_cover_url($vo['img']);
                }
                
                foreach ($slideshow as &$data) {
                    foreach ($category as $c) {
                        if ($data['cate_id'] == $c['id']) {
                            $data['url'] = $c['url'];
                        }
                    }
                }
                $this->assign('slideshow', $slideshow);
                
                $this->_footer();
                if ($this->config['template_subcate'] == 'default') {
                    // code...
                    $htmlstr = 'cate.html';
                } else {
                    $htmlstr = 'index.html';
                }
                if (! $cate['template']) {
                    $cate['template'] = $this->config['template_subcate'];
                }
                return $this->fetch(env('app_path') . 'wei_site/view/template_subcate/' . $cate['template'] . '/' . $htmlstr);
            } else {
                $page = I('p', 1, 'intval');
                $row = isset($_REQUEST['list_row']) ? intval($_REQUEST['list_row']) : 20;
                
                $data = M('custom_reply_news')->where(wp_where($map))
                    ->order('sort asc, id DESC')
                    ->paginate($row);
                $list_data = $this->parsePageData($data, [], [], false);
                if (empty($list_data['list_data'])) {
                    $cmap['id'] = $map['cate_id'] = intval($cate_id);
                    $cate = M('weisite_category')->where(wp_where($cmap))->find();
                    if (! empty($cate['url'])) {
                        return redirect($cate['url']);
                    }
                }
                
                $slideData = $lists = [];
                foreach ($list_data['list_data'] as $k => $li) {
                    if ($li['jump_url'] && empty($li['content'])) {
                        $li['url'] = $li['jump_url'];
                    } else {
                        $li['url'] = U('detail', array(
                            'id' => $li['id']
                        ));
                    }
                    $showType = explode(',', $li['show_type']);
                    if (in_array(1, $showType)) {
                        $slideData[] = $li;
                    }
                    if (in_array(0, $showType)) {
                        // unset($list_data['list_data'][$k]);
                        $lists[] = $li;
                    }
                }
                $this->assign('slide_data', $slideData);
                $this->assign('lists', $lists);
                $this->assign($list_data);
                $this->_footer();
                return $this->fetch(env('app_path') . 'wei_site/view/template_lists/' . $this->config['template_lists'] . '/lists.html');
            }
        }
    }

    // 详情
    public function detail()
    {
        if (file_exists(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_detail'] . '.html')) {
            $this->pigcms_detail();
            return $this->fetch(env('app_path') . 'wei_site/view/pigcms/Index_' . $this->config['template_detail'] . '.html');
        } else {
            $map['id'] = I('id/d', 0);
            $info = M('custom_reply_news')->where(wp_where($map))->find();
            // dump($info);exit;
            if ($info['is_show'] == '0') {
                unset($info['cover']);
            }
            // dump($info);exit;
            $this->assign('info', $info);
            
            // dump($info);exit;
            M('custom_reply_news')->where(wp_where($map))->setInc('view_count');
            
            $this->_footer();
            return $this->fetch(env('app_path') . 'wei_site/view/template_detail/' . $this->config['template_detail'] . '/detail.html');
        }
    }

    // 3G页面底部导航
    public function _footer($temp_type = 'weiphp')
    {
        if ($temp_type == 'pigcms') {
            $param['wpid'] = $wpid = get_wpid();
            $param['temp'] = $this->config['template_footer'];
            $url = U('home/Index/getFooterHtml', $param);
            $html = wp_file_get_contents($url);
            // dump ( $url );
            // dump ( $html );
            $file = env('runtime_path') . $wpid . '_' . $this->config['template_footer'] . '.html';
            if (! file_exists($file)) {
                file_put_contents($file, $html);
            }
            
            $this->assign('cateMenuFileName', $file);
        } else {
            $list = D('WeiSite/Footer')->get_list();
            
            $one_arr = [];
            foreach ($list as $k => $vo) {
                if ($vo['pid'] != 0) {
                    continue;
                }
                
                $one_arr[$vo['id']] = $vo;
                unset($list[$k]);
            }
            
            foreach ($one_arr as &$p) {
                $two_arr = [];
                foreach ($list as $key => $l) {
                    if ($l['pid'] != $p['id']) {
                        continue;
                    }
                    
                    $two_arr[] = $l;
                    unset($list[$key]);
                }
                
                $p['child'] = $two_arr;
            }
            $this->assign('footer', $one_arr);
            if (empty($this->config['template_footer'])) {
                $this->config['template_footer'] = 'v1';
            }
            
            $html = $this->fetch(env('app_path') . 'wei_site/view/template_footer/' . $this->config['template_footer'] . '/footer.html');
            
            $this->assign('footer_html', $html);
        }
    }

    public function _deal_footer_data($vo, $k)
    {
        $arr = array(
            'id' => $vo['id'],
            'fid' => $vo['pid'],
            'wpid' => $vo['wpid'],
            'name' => $vo['title'],
            'orderss' => 0,
            'picurl' => get_cover_url($vo['icon']),
            'url' => $vo['url'],
            'status' => "1",
            'RadioGroup1' => "0",
            'vo' => [],
            'k' => $k
        );
        return $arr;
    }

    public function coming_soom()
    {
        return $this->fetch();
    }

    public function tvs1_video()
    {
        return $this->fetch();
    }

    public function pigcms_index()
    {
        $this->pigcms_init();
        
        $cate = $this->_pigcms_cate(0);
        $this->assign('info', $cate);
    }

    public function pigcms_lists($cate_id)
    {
        $this->pigcms_init();
        
        $map['wpid'] = get_wpid();
        $cateArr = M('weisite_category')->where(wp_where($map))->column('id,title');
        
        $thisClassInfo = [];
        if ($cate_id) {
            $map['cate_id'] = $cate_id;
            
            $thisClassInfo = $this->_deal_cate($cateArr[$cate_id]);
        }
        
        $data = M('custom_reply_news')->where(wp_where($map))
            ->order('sort asc, id DESC')
            ->select();
        foreach ($data as $vo) {
            $info[] = array(
                'id' => $vo['id'],
                'uid' => 0,
                'uname' => $vo['author'],
                'keyword' => $vo['keyword'],
                'type' => 2,
                'text' => $vo['intro'],
                'classid' => $vo['cate_id'],
                'classname' => $vo[''],
                'pic' => get_cover_url($vo['cover']),
                'showpic' => 1,
                'info' => strip_tags(htmlspecialchars_decode(mb_substr($vo['content'], 0, 10, 'utf-8'))),
                'url' => $this->_getNewsUrl($vo),
                'createtime' => $vo['cTime'],
                'uptatetime' => $vo['cTime'],
                'click' => $vo['view_count'],
                'wpid' => $vo['wpid'],
                'title' => $vo['title'],
                'usort' => $vo['sort'],
                'name' => $vo['title'],
                'img' => get_cover_url($vo['cover'])
            );
        }
        
        $this->assign('info', $info);
        $this->assign('thisClassInfo', $thisClassInfo);
    }

    public function pigcms_detail()
    {
        $this->pigcms_init();
        
        $cate = $this->_pigcms_cate(0);
        $this->assign('info', $cate);
        
        $map['id'] = I('id/d', 0);
        $res = M('custom_reply_news')->where(wp_where($map))->find();
        if ($res['is_show'] == 0) {
            unset($res['cover']);
        }
        $res = $this->_deal_news($res, 1);
        $this->assign('res', $res);
        M('custom_reply_news')->where(wp_where($map))->setInc('view_count');
        
        $map2['cate_id'] = $res['cate_id'];
        $map2['id'] = array(
            'exp',
            '!=' . $map['id']
        );
        $lists = M('custom_reply_news')->where(wp_where($map2))
            ->order('id desc')
            ->limit(5)
            ->select();
        foreach ($lists as &$new) {
            $new = $this->_deal_news($new);
        }
        
        $this->assign('lists', $lists);
    }

    public function _pigcms_cate($pid = null)
    {
        $map['wpid'] = get_wpid();
        $map['is_show'] = 1;
        $pid === null || $map['pid'] = $pid; // 获取一级分类
        
        $category = M('weisite_category')->where(wp_where($map))
            ->order('sort asc, id desc')
            ->select();
        $count = count($category);
        foreach ($category as $k => $vo) {
            $param['cate_id'] = $vo['id'];
            $url = empty($vo['url']) ? $vo['url'] = U('wei_site/Wap/lists', $param) : $vo['url'];
            $pid = intval($vo['pid']);
            $res[$pid][$vo['id']] = $this->_deal_cate($vo, $count - $k);
        }
        
        foreach ($res[0] as $vv) {
            if (! empty($res[$vv['id']])) {
                $vv['sub'] = $res[$vv['id']];
                unset($res[$vv['id']]);
            }
        }
        
        return $res[0];
    }

    public function _deal_cate($vo, $key = 1)
    {
        return array(
            'id' => $vo['id'],
            'fid' => $vo['pid'],
            'name' => $vo['title'],
            'info' => $vo['title'],
            'sorts' => $vo['sort'],
            'img' => get_cover_url($vo['icon']),
            'url' => $url,
            'status' => 1,
            'path' => empty($vo['pid']) ? 0 : '0-' . $vo['pid'],
            'tpid' => 1,
            'conttpid' => 1,
            'sub' => [],
            'key' => $key,
            'wpid' => $vo['wpid']
        );
    }

    public function _deal_news($vo, $type = 0)
    {
        $map['id'] = $vo['cate_id'];
        return array(
            'id' => $vo['id'],
            'uid' => 0,
            'uname' => $vo['author'],
            'keyword' => $vo['keyword'],
            'type' => 2,
            'text' => $vo['intro'],
            'classid' => $vo['cate_id'],
            'classname' => empty($vo['cate_id']) ? '' : M('weisite_category')->where(wp_where($map))->value('title'),
            'pic' => get_cover_url($vo['cover']),
            'showpic' => 1,
            'info' => $type == 0 ? strip_tags(htmlspecialchars_decode(mb_substr($vo['content'], 0, 10, 'utf-8'))) : $vo['content'],
            'url' => $this->_getNewsUrl($vo),
            'createtime' => $vo['cTime'],
            'uptatetime' => $vo['cTime'],
            'click' => $vo['view_count'],
            'wpid' => $vo['wpid'],
            'title' => $vo['title'],
            'usort' => $vo['sort'],
            'name' => $vo['title'],
            'img' => get_cover_url($vo['cover'])
        );
    }

    public function _getNewsUrl($info)
    {
        $param['wpid'] = get_wpid();
        $param['openid'] = get_openid();
        
        if (! empty($info['jump_url'])) {
            $url = replace_url($info['jump_url']);
        } else {
            $param['id'] = $info['id'];
            $url = U('detail', $param);
        }
        return $url;
    }
}
