<?php
namespace app\wei_site\controller;

use app\wei_site\controller\Base;

class WeiSite extends Base
{

    public function config()
    {
        $public_info = get_pbid_appinfo();
        $normal_tips = '想查看效果,可以点击：<a href="' . U('wei_site/Wap/index', array(
            'pbid' => $public_info['id']
        )) . '">预览</a>， <a id="copyLink" data-clipboard-text="' . U('wei_site/Wap/index', array(
            'pbid' => $public_info['id']
        )) . '">复制链接</a><script type="application/javascript">$.WeiPHP.initCopyBtn("copyLink");</script>';
        $this->assign('normal_tips', $normal_tips);
        
        $config = D('common/PublicConfig')->getConfig(MODULE_NAME);
        // dump(MODULE_NAME);
        if (IS_POST) {
            $post = I('post.');
            if (input('?post.background')) {
                $post['background'] = implode(',', input('post.background'));
            }
            $flag = D('common/PublicConfig')->setConfig('wei_site_wei_site', $post);
            if ($flag !== false) {
                $from = I('get.from','');
                if ($from == 'preview') {
                    $url = U('preview');
                } else {
                	$url='';
//                     $url = cookie('__forward__');
                }
                $this->success('保存成功', $url);
            } else {
                $this->error('保存失败');
            }
            exit();
        }
        $config['background_arr'] = explode(',', $config['background']);
        $config['background'] = $config['background_arr'][0];
        $this->assign('data', $config);
        
        return $this->fetch();
    }

    public function coming_soom()
    {
        return $this->fetch();
    }

    public function tvs1_video()
    {
        return $this->fetch();
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
        $param['pbid'] = get_pbid();
        $param['openid'] = get_openid();
        
        if (! empty($info['jump_url'])) {
            $url = replace_url($info['jump_url']);
        } else {
            $param['id'] = $info['id'];
            $url = U('detail', $param);
        }
        return $url;
    }

    /* 预览 */
    public function preview()
    {
        $pbid = get_pbid();
        $url = U('wei_site/Wap/index', array(
            'pbid' => $pbid
        ));
        $this->assign('url', $url);
        
        $config = get_info_config('WeiSite');
        
        if (empty($config['background'])) {
            $config['background_arr'] = [];
            $config['background'] = '';
        } else {
            $config['background_arr'] = explode(',', $config['background']);
            $config['background'] = $config['background_arr'][0];
        }
        
        $this->assign('data', $config);
        
        return $this->fetch();
    }

    public function preview_cms()
    {
        $pbid = get_pbid();
        $url = U('wei_site/Wap/lists', array(
            'pbid' => $pbid,
            'from' => 'preview'
        ));
        $this->assign('url', $url);
        
        return $this->fetch();
    }

    public function preview_old()
    {
        $pbid = get_pbid();
        $url = U('wei_site/Wap/index', array(
            'pbid' => $pbid
        ));
        $this->assign('url', $url);
        return $this->fetch('common@base/preview');
    }
}
