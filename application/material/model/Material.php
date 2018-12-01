<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace app\material\model;

use app\common\model\Base;

/**
 * 分类模型
 */
class Material extends Base
{

    protected $table = DB_PREFIX . 'material_news';

    /**
     * 获取导航列表，支持多级导航
     *
     * @param boolean $field
     *            要列出的字段
     * @return array 导航树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function getMediaIdByGroupId($group_id)
    {
        $map['group_id'] = $group_id;
        $list = $this->where(wp_where($map))
            ->order('id asc')
            ->select();
        if (! empty($list[0]['media_id']))
            return $list[0]['media_id'];
        
        // 自动同步到微信端
        foreach ($list as $vo) {
            $data['title'] = $vo['title'];
            $data['thumb_media_id'] = empty($vo['thumb_media_id']) ? $this->_thumb_media_id($vo['cover_id']) : $vo['thumb_media_id'];
            $data['author'] = $vo['author'];
            $data['digest'] = $vo['intro'];
            $data['show_cover_pic'] = 0;
            $vo['content'] = $this->getNewContent($vo['content']);
            $data['content'] = str_replace('"', '\'', $vo['content']);

            //$data['content'] = $vo['content'];
            $data['content_source_url'] = ! empty($vo['link']) ? $vo['link'] : U('material/Wap/news_detail', array(
                'id' => $vo['id']
            ));
            
            $articles[] = $data;
        }
        
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . get_access_token();
        $param['articles'] = $articles;
        
        $res = post_data($url, $param);
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            return false;
        } else {
            $this->where(wp_where($map))->setField('media_id', $res['media_id']);
            return $res['media_id'];
        }
    }

    function _thumb_media_id($cover_id)
    {
        $media_id = D('common/Custom')->get_image_media_id($cover_id, 'thumb');
        if (! empty($media_id)) {
            $res = M('material_news')->where('cover_id', $cover_id)->setField('thumb_media_id', $media_id);
        }
        return $media_id;
    }

     // 图文消息的内容图片，上传到微信并获取新的链接覆盖
    public function getNewContent($content)
    {
        if (! $content) {
            return;
        }
        
        $newUrl = [];
        // 获取文章中图片img标签
        // $match=$this->getImgSrc($content);
        preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $content, $match);
        foreach ($match[1] as $mm) {
            $oldUrl=$mm;
            
            if(!preg_match("/^(http:\/\/|https:\/\/).*$/",$mm)){
                //没有
                $mm = SITE_URL . $mm;
                $mm = str_replace('public./', '', $mm);
            }

            $newUrl[$oldUrl] = uploadimg($mm);
        }

        if (count($newUrl)) {
            $content_new = strtr($content, $newUrl);
        }
        return empty($content_new) ? $content : $content_new;
    }
}
