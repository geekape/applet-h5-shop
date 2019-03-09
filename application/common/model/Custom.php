<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 微信客服接口操作类
 */
class Custom extends Base
{

    protected $table = DB_PREFIX . 'user';

    /* 回复文本消息 */
    public function replyText($uid, $content)
    {
        $param['text']['content'] = $content;
        return $this->replyData($uid, $param, 'text');
    }

    /* 回复图片消息 */
    public function replyImage($uid, $media_id, $type = 'cover_id')
    {
        $type == 'cover_id' && $media_id = $this->get_image_media_id($media_id);
        // 素材图片id
        if ($type == 'material_image') {
            $imageMaterial = M('material_image')->where('id', $media_id)->find();
            if ($imageMaterial['media_id']) {
                $media_id = $imageMaterial['media_id'];
            } else {
                $media_id = $this->get_image_media_id($imageMaterial['cover_id']);
                if (!empty($media_id) && !empty($imageMaterial['id'])){
                	//永久素材的media_id
                	M('material_image')->where('id', $imageMaterial['id'])->setField('media_id',$media_id);
                }
            }
        }
        $param['image']['media_id'] = $media_id;
        
        return $this->replyData($uid, $param, 'image');
    }

    /* 回复语音消息 */
    /**
     *
     * @param unknown $uid
     * @param unknown $media_id:
     *            id值
     * @param string $type
     *            决定id值的类型： material_file：文件素材的id, file_id:文件id '':media_id
     * @return Ambigous <number, string>
     */
    public function replyVoice($uid, $media_id, $type = 'file_id')
    {
        $type == 'file_id' && $media_id = $this->get_file_media_id($media_id, 'voice');
        if ($type == 'material_file') {
            $fileMaterial = M('material_file')->where('id', $media_id)->find();
            if ($fileMaterial['media_id']) {
                $media_id = $fileMaterial['media_id'];
            } else {
                $media_id = $this->get_file_media_id($fileMaterial['file_id'], 'voice');
            }
        }
        $msg['voice']['media_id'] = $media_id;
        return $this->replyData($uid, $msg, 'voice');
    }

    /* 回复视频消息 */
    public function replyVideo($uid, $media_id, $type = 'file_id', $thumb = '', $title = '', $description = '')
    {
        $type == 'file_id' && $media_id = $this->get_file_media_id($media_id, 'video');
        if ($type == 'material_file') {
            $fileMaterial = M('material_file')->where('id', $media_id)->find();
            empty($title) && $title = $fileMaterial['title'];
            empty($description) && $description = $fileMaterial['introduction'];
            if ($fileMaterial['media_id']) {
                $media_id = $fileMaterial['media_id'];
            } else {
                $media_id = $this->get_image_media_id($fileMaterial['file_id'], 'video');
            }
        }
        $msg['video']['media_id'] = $media_id;
        $msg['video']['thumb_media_id'] = $thumb ? $thumb : $this->get_thumb_media_id(); // 缩略图
        $msg['video']['title'] = $title;
        $msg['video']['description'] = $description;
        return $this->replyData($uid, $msg, 'video');
    }

    /* 回复音乐消息 */
    public function replyMusic($uid, $media_id, $title = '', $description = '', $music_url, $HQ_music_url)
    {
        $msg['Music']['ThumbMediaId'] = $media_id;
        $msg['Music']['Title'] = $title;
        $msg['Music']['Description'] = $description;
        $msg['Music']['MusicURL'] = $music_url;
        $msg['Music']['HQMusicUrl'] = $HQ_music_url;
        return $this->replyData($uid, $msg, 'music');
    }

    /*
     * 回复图文消息 传出图文素材的ID
     */
    public function replyNews($uid, $sucai_id)
    {
        $map['group_id'] = $sucai_id;
        $appMsgData = M('material_news')->where(wp_where($map))->select();
        foreach ($appMsgData as $vo) {
            // 文章内容
            $art['title'] = $vo['title'];
            $art['description'] = $vo['intro'];
            $openid = get_openid();
            if (empty($vo['url'])) {
                if (empty($vo['content'])) {
                    $art['url'] = replace_url($vo['link']);
                }
                $public_info = get_pbid_appinfo();
                if (empty($art['url'])) {
                    $art['url'] = U('material/Wap/news_detail', array(
                        'id' => $vo['id'],
                        'publicid' => $public_info['id']
                    ));
                }
            } else {
                $art['url'] = $vo['url'];
            }
            
            if (! config('USER_OAUTH')) {
                $art['url'] .= '&openid=' . $openid;
            }
            
            // 获取封面图片URL
            $coverId = $vo['cover_id'];
            $art['picurl'] = get_cover_url($coverId);
            $articles[] = $art;
        }
        $param['news']['articles'] = $articles;
        
        return $this->replyData($uid, $param, 'news');
    }

    /* 发送回复消息到微信平台 */
    function replyData($uid, $param, $msg_type)
    {
        if (empty($uid)) {
            return false;
        }
        
        $map['pbid'] = get_pbid();
        $map['uid'] = $uid;
        
        $param['touser'] = M('public_follow')->where(wp_where($map))->value('openid');
        $param['msgtype'] = $msg_type;
        
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . get_access_token();
        
        // dump($param);
        // die;
        $result['status'] = 0;
        $result['msg'] = '回复失败';
        $res = post_data($url, $param);
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            $result['msg'] = error_msg($res);
        } else {
            $data['ToUserName'] = get_wpid();
            $data['FromUserName'] = $param['touser'];
            $data['CreateTime'] = NOW_TIME;
            $data['Content'] = isset($param['text']['content']) ? $param['text']['content'] : json_encode($param);
            $data['MsgId'] = get_mid(); // 该字段保存管理员ID
            $data['type'] = 1;
            $data['is_read'] = 1;
            M('weixin_message')->insert($data);
            
            $result['status'] = 1;
            $result['msg'] = '回复成功';
        }
        return $result;
    }

    // 新增永久图片素材
    function get_image_media_id($cover_id, $type = 'image')
    {
        $cover = get_cover($cover_id);
        if (empty($cover)) {
            return 0;
        }
        
        $path = SITE_PATH . '/public' . $cover['path'];
        
        if (! file_exists($path)) {
            // 先把图片下载到本地
            $pathinfo = pathinfo($path);
            mkdirs($pathinfo['dirname']);
            
            $content = wp_file_get_contents($cover['url']);
            $res = file_put_contents($path, $content);
            if (! $res) {
                addWeixinLog('远程图片下载失败', $type);
                return 0;
            }
        }
        
        if (! $path) {
            addWeixinLog('获取文章封面失败，请确认是否增加封面', $type);
            return 0;
        }
        
        $param = upload_param_by_curl($path);
        $param['type'] = $type;
        
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token();
        $res = post_data($url, $param, 'file');
        
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            addWeixinLog(error_msg($res, '封面图上传'), '_thumb_media_id');
            return 0;
        }
        if (isset($res['curl_erron']) && $res['curl_error'] != 0) {
            addWeixinLog($res['curl_error'], '_thumb_media_id');
            return 0;
        }
        return $res['media_id'];
    }

    // 新增临时 voice 语音/ video 视频素材
    function get_file_media_id($file_id, $type = 'voice')
    {
        $fileInfo = M('file')->where('id', $file_id)->find();
        if ($fileInfo) {
            // dump($fileInfo);
            $path = SITE_PATH . '/public/uploads/download/' . $fileInfo['savepath'] . $fileInfo['savename'];
            // dump($path);
            if (! $path) {
                return 0;
            }
            //媒体文件在微信后台保存时间为3天，即3天后media_id失效。
            $fileKey = 'get_file_media_id_'.$type.'_'.$fileInfo ['savepath'] . $fileInfo ['savename'];
            $res = S($fileKey);
            if (empty($res)) {
                $param = upload_param_by_curl($path);
                $param['type'] = $type;
                // dump($param);
                $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token();
                $res = post_data($url, $param, 'file');
                // dump($url);
                // dump($res);
                if (isset($res['errcode']) && $res['errcode'] != 0) {
                    return 0;
                }
                //缓存2天
                S($fileKey, $res, 172800);
            }
        } else {
            return 0;
        }
        
        return isset($res['media_id']) ? $res['media_id'] : 0;
    }

    // 临时缩略图素材
    function get_thumb_media_id($path = '')
    {
        if (! $path) {
            $path = SITE_PATH . '/public/home/images/spec_img_add.jpg';
        }
        $param = upload_param_by_curl($path);
        $param['type'] = 'thumb';
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token();
        $res = post_data($url, $param, 'file');
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            return 0;
        }
        return $res['thumb_media_id'];
    }
}
