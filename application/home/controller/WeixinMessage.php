<?php
namespace app\home\controller;

/**
 * 前台首页控制器
 */
class WeixinMessage extends Home
{

    function initialize()
    {
        parent::initialize();
        $param['mdm'] = I('mdm');
        $act = strtolower(ACTION_NAME);
        
        $res['title'] = '消息列表';
        $res['url'] = U('lists', $param);
        $res['class'] = $act == 'lists' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '收藏列表';
        $res['url'] = U('collect', $param);
        $res['class'] = $act == 'collect' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '待处理列表';
        $res['url'] = U('deal', $param);
        $res['class'] = $act == 'deal' ? 'current' : '';
        $nav[] = $res;
        
        $this->assign('nav', $nav);
    }

    public function collect()
    {
        $map['collect'] = 1;
        // $map ['type'] = 0;
        $map['ToUserName'] = get_wpid();
        $list = M( 'weixin_message' )->where( wp_where($map) )
            ->order('id desc')
            ->paginate();
        $list = dealPage($list); // paginate
        
        $dao = D('common/User');
        foreach ($list['list_data'] as &$v) {
            $user = $dao->getUserInfoByOpenid($v['FromUserName']);
            if ($user) {
                $v['user'] = $user;
            }
            $v['Content'] = $this->_deal_content($v);
        }
        
        $this->assign($list);
        return $this->fetch('collect');
    }

    public function deal()
    {
        $map['deal'] = 1;
        // $map ['type'] = 0;
        $map['ToUserName'] = get_wpid();
        $list = M( 'weixin_message' )->where( wp_where($map) )
            ->order('id desc')
            ->paginate();
        $list = dealPage($list); // paginate
        
        $dao = D('common/User');
        foreach ($list['list_data'] as &$v) {
            $user = $dao->getUserInfoByOpenid($v['FromUserName']);
            if ($user) {
                $v['user'] = $user;
            }
            $v['Content'] = $this->_deal_content($v);
        }
        
        $this->assign($list);
        return $this->fetch('collect');
    }

    public function lists()
    {
        $model = $this->getModel('weixin_message');
        $this->assign('model', $model);
        
        $list_data = $this->_list_grid($model);
        
        $page = I('p', 1, 'intval');
        $row = 20;
        
        $limit = (($page - 1) * $row) . ',' . ($page * $row);
        $px = DB_PREFIX;
        
        $wpid = get_wpid();
        $map['type'] = 0;
        $map['ToUserName'] = $wpid;
        $ids = M( 'weixin_message' )->where( wp_where($map) )
            ->field('MAX(id) as mid')
            ->group('FromUserName')
            ->paginate($row);
		
        $list_data = $this->parsePageData($ids, $model, $list_data, false);
        foreach ($list_data['list_data'] as $vv) {
            $arr[] = $vv['mid'];
        }
        if (! empty($arr)) {
            $map1['id'] = array(
                'in',
                $arr
            );
            $data = M( 'weixin_message' )->where( wp_where($map1) )
                ->order('is_read ASC,id DESC')
                ->paginate($row);
			$list_data = $this->parsePageData($data, $model, $list_data, false);
        }
        
        $dao = D('common/User');
        if (isset($list_data['list_data']) && is_array($list_data['list_data'])) {
            foreach ($list_data['list_data'] as &$v) {
                $user = $dao->getUserInfoByOpenid($v['FromUserName']);

                $v['openid'] = $v['FromUserName'];
                $v['user'] = [];
                if ($user) {
                    $v['user'] = $user;
                }
                $v['Content'] = $this->_deal_content($v);
            }
        }
        
        $this->assign($list_data);
        
        return $this->fetch('lists');
    }

    function person()
    {
        $this->assign('nav', []);
        $map['FromUserName'] = I('openid');
        $map['ToUserName'] = get_wpid();
        $list = M( 'weixin_message' )->where( wp_where($map) )
            ->order('id desc')
            ->paginate();
        $list = dealPage($list); // paginate
        $dao = D('common/User');
        foreach ($list['list_data'] as &$v) {
            if ($v['type'] == 1) {
                $user = $dao->getUserInfo($v['MsgId']);
            } else {
                $user = $dao->getUserInfoByOpenid($v['FromUserName']);
                $toUser = $user;
            }
            if ($user) {
                $v['user'] = $user;
            }
            $v['Content'] = $this->_deal_content($v);
            $creatTime[] = $v['CreateTime'];
        }
        rsort($creatTime);
        $minTime = NOW_TIME - 60 * 60 * 48;
        if ($creatTime[0] < $minTime) {
            $this->assign('normal_tips', $toUser['nickname'] . ' 用户发送消息的时间超过48小时，管理员只可以给48小时以内发送消息的用户回复信息');
            $can_send = 0;
        } else {
            $can_send = 1;
        }
        $text = I('text_content');
        $this->assign('text', $text);
        $this->assign('can_send', $can_send);
        $this->assign('toUser', $toUser);
        $this->assign($list);
        // dump($list);
        // $this->assign ( 'normal_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
        
        return $this->fetch();
    }

    function _deal_content($data)
    {
        require_once(env('vendor_path')."qqface.php");
        $data['Content'] = qqface_convert_html($data['Content']);
        $dd['Content'] = parseHtmlemoji($data['Content']);
        switch ($data['MsgType']) {
            case 'image':
                $msgtype = 'image';
                if (empty($data['Content'])) {
                    $coverid = down_media($data['MediaId']);
                    $data['PicUrl'] = get_cover_url($coverid);
                    $dd['url'] = $data['PicUrl'];
                    // 保存到Content里
                    $addContent['msgtype'] = 'image';
                    $addContent['image']['media_id'] = $data['MediaId'];
                    $addContent['picurl'] = $dd['url'];
                    $save['Content'] = json_url($addContent);
                    M( 'weixin_message' )->where('id', $data['id'])
                        ->update($save);
                } else {
                    $con = json_decode($data['Content'], true);
                    $dd['url'] = $con['picurl'];
                }
                // $data ['Content'] = '<a target="_blank" href="' . $data ['PicUrl'] . '"><img width="100" height="100" src="' . $data ['PicUrl'] . '"></a>';
                break;
            case 'voice':
                $msgtype = 'voice';
                // $fileid=down_file_media($data['MediaId'],'voice');
                if (empty($data['Content'])) {
                    $fileid = down_file_media($data['MediaId'], 'voice');
                    if ($fileid) {
                        $file_voice = M( 'file' )->where('id', $fileid)->find ();
                        $dd['id'] = $file_voice['id'];
                        // 保存到Content里
                        $addContent['msgtype'] = 'voice';
                        $addContent['voice']['media_id'] = $data['MediaId'];
                        $addContent['file_id'] = $dd['id'];
                        $save['Content'] = json_url($addContent);
                        M( 'weixin_message' )->where('id', $data['id'])
                            ->update($save);
                    }
                } else {
                    $con = json_decode($data['Content'], true);
                    $dd['id'] = $con['file_id'];
                }
                
                $data['Content'] = 'voice'; // TODO
                break;
            case 'video':
                $msgtype = 'video';
                
                if (empty($data['Content'])) {
                    $fileid = down_file_media($data['MediaId'], 'video');
                    if ($fileid) {
                        $file_video = M( 'file' )->where('id', $fileid)->find ();
                        $dd['id'] = $file_video['id'];
                        // 保存到Content里
                        $addContent['msgtype'] = 'video';
                        $addContent['video']['media_id'] = $data['MediaId'];
                        $addContent['file_id'] = $dd['id'];
                        $save['Content'] = json_url($addContent);
                        M( 'weixin_message' )->where('id', $data['id'])
                            ->update($save);
                    }
                } else {
                    $con = json_decode($data['Content'], true);
                    $dd['id'] = $con['file_id'];
                }
                $data['Content'] = 'video'; // TODO
                break;
            case 'shortvideo':
                $msgtype = 'shortvideo';
                $data['Content'] = 'shortvideo'; // TODO
                break;
            case 'location':
                $msgtype = 'location';
                $data['Content'] = 'location'; // TODO
                break;
            case 'link':
                $msgtype = 'link';
                $dd['url'] = $data['Url'];
                $dd['title'] = $data['Title'];
                $dd['description'] = $data['Description'];
                break;
            default:
                $content = json_decode($data['Content'], true);
                $msgtype = $content['msgtype'];
                // dump($data);
                if (! empty($content)) {
                    if (isset($content['image'])) {
                        $imagemap['media_id'] = $content['image']['media_id'];
                        $imagemap['wpid'] = get_wpid();
                        $image = M( 'material_image' )->where( wp_where($imagemap) )->find();
                        if ($image['cover_url']) {
                            $dd['url'] = $image['cover_url'];
                        } else {
                            $coverid = down_media($content['image']['media_id']);
                            if (! $coverid) {
                                $coverid = do_down_image($content['image']['media_id']);
                            }
                            $data['PicUrl'] = get_cover_url($coverid);
                            $dd['url'] = $data['PicUrl'];
                        }
                    } else 
                        if (isset($content['voice'])) {
                            $voicemap['media_id'] = $content['voice']['media_id'];
                            $voicemap['wpid'] = get_wpid();
                            $voicemap['type'] = 1;
                            $file_voice = M( 'material_file' )->where( wp_where($voicemap) )->find();
                            $dd['id'] = $file_voice['id'];
                            $dd['title'] = $file_voice['title'];
                            $dd['file_id'] = $file_voice['file_id'];
                        } else 
                            if (isset($content['news'])) {
                                $news = $content['news']['articles'];
                                $index = count($news) - 1;
                                $fist = $news[$index];
                                unset($news[$index]);
                                $other = $news;
                                $dd['first'] = $fist;
                                $dd['child'] = $other;
                            } else 
                                if (isset($content['video'])) {
                                    $videomap['media_id'] = $content['video']['media_id'];
                                    $videomap['wpid'] = get_wpid();
                                    $videomap['type'] = 2;
                                    $file_video = M( 'material_file' )->where( wp_where($videomap) )->find();
                                    $dd['id'] = $file_video['id'];
                                    $dd['title'] = $file_video['title'];
                                    $dd['file_id'] = $file_video['file_id'];
                                    $dd['introduction'] = $file_video['introduction'];
                                }
                }
                break;
        }
        $dd['msg_type'] = $msgtype;
        if (empty($dd['msg_type'])) {
            $dd['msg_type'] = 'text';
            require_once(env('vendor_path')."emoji.php");
            $tmpStr = json_encode($data['Content']);
            $tmpStr = preg_replace_callback("#(\\\ue[0-9a-f]{3})#i", function ($r) {
                return addslashes('\\1');
            }, $tmpStr);
            $text = json_decode($tmpStr);
            $src = array(
                array(
                    0x2600
                ),
                array(
                    0x1F494
                ),
                array(
                    0x1F197
                ),
                array(
                    0x32,
                    0x20E3
                )
            );
            
            foreach ($src as $unified) {
                $bytes = '';
                $hex = [];
                foreach ($unified as $cp) {
                    $bytes .= $this->utf8_bytes('\ue40e');
                }
            }
        }
        $data['Content'] = $dd;
        return $data['Content'];
    }
    // 转换编码，将Unicode编码转换成可以浏览的utf-8字符串
    function unicode_decode($uStr)
    {
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $uStr, $matches);
        if (! empty($matches)) {
            $uStr = '';
            for ($j = 0; $j < count($matches[0]); $j ++) {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0) {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code) . chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $uStr .= $c;
                } else {
                    $uStr .= $str;
                }
            }
        }
        return $uStr;
    }

    function utf8_bytes($cp)
    {
        if ($cp > 0x10000) {
            // 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)) . chr(0x80 | (($cp & 0x3F000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
        } else 
            if ($cp > 0x800) {
                // 3 bytes
                return chr(0xE0 | (($cp & 0xF000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
            } else 
                if ($cp > 0x80) {
                    // 2 bytes
                    return chr(0xC0 | (($cp & 0x7C0) >> 6)) . chr(0x80 | ($cp & 0x3F));
                } else {
                    // 1 byte
                    return chr($cp);
                }
    }
    
    // 设置消息状态
    function set_status()
    {
        $map['id'] = I('id');
        $field = I('field');
        $val = I('val');
        $res = M( 'weixin_message' )->where( wp_where($map) )->setField($field, $val);
        echo $res;
    }
    
    // 使用客户接口回复用户信息 TODO 目前只支持发文本
    function reply()
    {
        // 回复类型：text：文本 appmsg：图文消息 image：图片消息
        $msg_type = I('msg_type');
        switch ($msg_type) {
            case 'text':
                $param['touser'] = I('openid');
                $param['msgtype'] = 'text';
                $param['text']['content'] = I('content');
                break;
            case 'appmsg':
                $param['touser'] = I('openid');
                $param['msgtype'] = 'news';
                
                $appmsgId = I('appmsg_id');
                $map['group_id'] = $appmsgId;
                $appMsgData = M( 'material_news' )->where( wp_where($map) )->select();
                foreach ($appMsgData as $vo) {
                    // 文章内容
                    $art['title'] = $vo['title'];
                    $art['description'] = $vo['intro'];
                    if (empty($vo['url'])) {
                        $art['url'] = U('material/Wap/news_detail', array(
                            'id' => $vo['id']
                        ));
                    } else {
                        $art['url'] = $vo['url'];
                    }
                    
                    // 获取封面图片URL
                    $coverId = $vo['cover_id'];
                    $art['picurl'] = get_cover_url($coverId);
                    $articles[] = $art;
                }
                $param['news']['articles'] = $articles;
                break;
            case 'image':
                // 图片
                $image_material = input('post.image_material');
                $image_cover_id = input('post.image');
                if ($image_cover_id) {
                    $mediaId = D('common/Custom')->get_image_media_id($image_cover_id);
                    
                    // $result=D('common/Custom')->replyImage($k,$data['media_id'],'');
                } else 
                    if ($image_material) {
                        $imageMaterial = M( 'material_image' )->find($image_material);
                        if ($imageMaterial['media_id']) {
                            $mediaId = $imageMaterial['media_id'];
                        } else {
                            $mediaId = D('common/Custom')->get_image_media_id($image_material);
                        }
                        // $result=D('common/Custom')->replyImage($k,$data['media_id'],'');
                    } else {
                        $this->error('请选择要发送的图片');
                    }
                
                // 新增图片素材
                $param['touser'] = I('openid');
                $param['msgtype'] = 'image';
                $param['image']['media_id'] = $mediaId;
                break;
            case 'voice':
                $voiceId = input('post.voice_id');
                if (empty($voiceId)) {
                    $this->error('请选择语音消息');
                }
                $voiceMaterial = M( 'material_file' )->where('id', $voiceId)->find ();
                if ($voiceMaterial['media_id']) {
                    $mediaId = $voiceMaterial['media_id'];
                } else {
                    $mediaId = D('common/Custom')->get_file_media_id($voiceMaterial['file_id'], 'voice');
                }
                $param['touser'] = I('openid');
                $param['msgtype'] = 'voice';
                $param['voice']['media_id'] = $mediaId;
                break;
            case 'video':
                // 视频
                $videoId = input('post.video_id');
                if (empty($videoId)) {
                    $this->error('请选择视频消息');
                }
                $videoMaterial = M( 'material_file' )->find($videoId);
                $data['Title'] = $videoMaterial['title'];
                $data['Description'] = $videoMaterial['introduction'];
                $data['ThumbMediaId'] = D('common/Custom')->get_thumb_media_id();
                
                if ($videoMaterial['media_id']) {
                    $mediaId = $videoMaterial['media_id'];
                } else {
                    $mediaId = D('common/Custom')->get_file_media_id($videoMaterial['file_id'], 'video');
                }
                $param['touser'] = I('openid');
                $param['msgtype'] = 'video';
                $param['video']['media_id'] = $mediaId;
                $param['video']['thumb_media_id'] = $data['ThumbMediaId']; // 缩略图
                $param['video']['title'] = $data['Title'];
                $param['video']['description'] = $data['Description'];
                break;
            default:
                $param = [];
                break;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . get_access_token();
        
        // dump($param);
        // die;
        
        $res = $this->post_data($url, $param);
        
        $data['ToUserName'] = get_wpid();
        $data['FromUserName'] = $param['touser'];
        $data['CreateTime'] = NOW_TIME;
        $data['Content'] = isset($param['text']['content']) ? $param['text']['content'] : json_encode($param);
        $data['MsgId'] = $this->mid; // 该字段保存管理员ID
        $data['type'] = 1;
        $data['is_read'] = 1;
        M( 'weixin_message' )->insert($data);
        if ($msg_type == 'text') {
            $this->success('回复成功', U('person', array(
                'openid' => $data['FromUserName'],
                'text_content' => $param['text']['content']
            )));
        } else {
            $this->success('回复成功');
        }
        // dump ( $res );
    }
   
    
    // 设置为文本素材
    function set_meterial()
    {
        $id = I('id');
        $type = I('type');
        $set_sucai = I('set_sucai');
        $message = M( 'weixin_message' )->where('id', $id)->find();
        $res = 0;
        if ($type == 'text' && $message['Content']) {
            $map['wpid'] = get_wpid();
            $map['uid'] = $this->mid;
            $map['aim_id'] = $id;
            $map['aim_table'] = 'weixin_message';
            $material = M( 'material_text' )->where( wp_where($map) )
                ->field('id,is_use')
                ->find();
            if (! empty($material)) {
                $saveUse['is_use'] = $set_sucai;
                $res1 = M( 'material_text' )->where( wp_where($map) )
                    ->update($saveUse);
            } else {
                $data['wpid'] = get_wpid();
                $data['uid'] = $this->mid;
                $data['aim_id'] = $id;
                $data['aim_table'] = 'weixin_message';
                $data['content'] = $message['Content'];
                $data['is_use'] = $set_sucai;
                $res1 = M( 'material_text' )->insertGetId($data);
            }
        } else 
            if ($type == 'image') {
                $content = json_decode($message['Content'], true);
                $imagemap['media_id'] = $content['image']['media_id'];
                if (! $imagemap['media_id']) {
                    $imagemap['media_id'] = $message['MediaId'];
                }
                $imagemap['wpid'] = get_wpid();
                $image = M( 'material_image' )->where( wp_where($imagemap) )->find();
                if ($image) {
                    // 保存
                    $save['is_use'] = $set_sucai;
                    $save['aim_id'] = $id;
                    $save['aim_table'] = 'weixin_message';
                    if (! $image['cover_id']) {
                        $save['cover_id'] = down_media($imagemap['media_id']);
                        if (! $save['cover_id']) {
                            $save['cover_id'] = do_down_image($imagemap['media_id']);
                        }
                        
                        if (! $image['cover_url']) {
                            $save['cover_url'] = get_cover_url($save['cover_id']);
                        }
                    }
                    $res1 = M( 'material_image' )->where( wp_where($imagemap) )
                        ->update($save);
                    // $dd['url']=$image['cover_url'];
                } else {
                    $save['is_use'] = $set_sucai;
                    $save['aim_id'] = $id;
                    $save['aim_table'] = 'weixin_message';
                    $save['media_id'] = $imagemap['media_id'];
                    $save['cTime'] = time();
                    $save['manager_id'] = $this->mid;
                    $save['wpid'] = get_wpid();
                    $save['cover_id'] = down_media($imagemap['media_id']);
                    if (! $save['cover_id']) {
                        $save['cover_id'] = do_down_image($imagemap['media_id']);
                    }
                    if (! $image['cover_url']) {
                        $save['cover_url'] = get_cover_url($save['cover_id']);
                    }
                    $res1 = M( 'material_image' )->insertGetId($save);
                }
            } else 
                if ($type == 'voice') {
                    $content = json_decode($message['Content'], true);
                    $voicemap['media_id'] = $content['voice']['media_id'];
                    if (! $voicemap['media_id']) {
                        $voicemap['media_id'] = $message['MediaId'];
                    }
                    $voicemap['wpid'] = get_wpid();
                    $voicemap['manager_id'] = $this->mid;
                    $voicemap['type'] = 1;
                    $voice = M( 'material_file' )->where( wp_where($voicemap) )->find();
                    if ($voice) {
                        // 保存
                        $save['is_use'] = $set_sucai;
                        $save['aim_id'] = $id;
                        $save['aim_table'] = 'weixin_message';
                        $res1 = M( 'material_file' )->where( wp_where($voicemap) )
                            ->update($save);
                        // $dd['url']=$image['cover_url'];
                    } else {
                        $save['is_use'] = $set_sucai;
                        $save['aim_id'] = $id;
                        $save['aim_table'] = 'weixin_message';
                        $save['media_id'] = $voicemap['media_id'];
                        $save['cTime'] = time();
                        $save['manager_id'] = $this->mid;
                        $save['type'] = 1;
                        $save['wpid'] = get_wpid();
                        $save['file_id'] = down_file_media($voicemap['media_id'], 'voice');
                        $res1 = M( 'material_file' )->insertGetId($save);
                    }
                } else 
                    if ($type == 'video') {
                        $content = json_decode($message['Content'], true);
                        $videomap['media_id'] = $content['video']['media_id'];
                        if (! $videomap['media_id']) {
                            $videomap['media_id'] = $message['MediaId'];
                        }
                        $videomap['wpid'] = get_wpid();
                        $videomap['manager_id'] = $this->mid;
                        $videomap['type'] = 2;
                        $video = M( 'material_file' )->where( wp_where($videomap) )->find();
                        if ($video) {
                            // 保存
                            $save['is_use'] = $set_sucai;
                            $save['aim_id'] = $id;
                            $save['aim_table'] = 'weixin_message';
                            $res1 = M( 'material_file' )->where( wp_where($videomap) )
                                ->update($save);
                            // $dd['url']=$image['cover_url'];
                        } else {
                            $save['is_use'] = $set_sucai;
                            $save['aim_id'] = $id;
                            $save['aim_table'] = 'weixin_message';
                            $save['media_id'] = $videomap['media_id'];
                            $save['cTime'] = time();
                            $save['manager_id'] = $this->mid;
                            $save['type'] = 2;
                            $save['wpid'] = get_wpid();
                            $save['file_id'] = down_file_media($videomap['media_id'], 'video');
                            $res1 = M( 'material_file' )->insertGetId($save);
                        }
                    }
        if ($res1!==false) {
            // $isMaterial=$message['is_material'];
            $save['is_material'] = $set_sucai;
            $res = M( 'weixin_message' )->where('id', $id)
                ->update($save);
        }
        
        echo $res;
    }
}