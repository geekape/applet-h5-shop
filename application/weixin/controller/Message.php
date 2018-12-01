<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\weixin\controller;

use app\common\controller\WebBase;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Message extends WebBase
{

    public function initialize()
    {
        parent::initialize();
        $param['mdm'] = I('mdm');
        $act = strtolower(ACTION_NAME);
        
        $res['title'] = '高级群发';
        $res['url'] = U('add', $param);
        $res['class'] = $act == 'add' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '客服群发';
        $res['url'] = U('custom_sendall', $param);
        $res['class'] = $act == 'custom_sendall' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '消息管理';
        $res['url'] = U('sendall_lists', $param);
        $res['class'] = $act == 'sendall_lists' ? 'current' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);
    }

    public function lists()
    {
        return $this->fetch('common@base/lists');
    }

    public function add()
    {
        $model = $this->getModel('Message');
        if (request()->isPost()) {
            $data = I('post.');
            /*
             * if (! config( 'SEND_GROUP_MSG' )) {
             * $this->error ( '抱歉，您的公众号没有群发消息的权限' );
             * }
             */
            if (! public_interface('SEND_GROUP_MSG')) {
                $this->error('抱歉，您的公众号没有群发消息的权限');
            }
            $send_type = I('send_type/d', 0);
            $group_id = I('group_id/d', 0);
            $send_openids = I('send_openids');
            
            if ($send_type == 0) {
                $data['msg_id'] = $this->_send_by_group($group_id);
            } else {
                $data['msg_id'] = $this->_send_by_openid($send_openids);
            }
            
            $Model = M(parse_name($model['name'], 1));
            
            // $data = $this->checkData($data, $this->model);
            $data['msgtype'] = $data['msg_type'];
            $data['image_id'] = '';
            if ($data['image'] > 0) {
                $data['image_id'] = $data['image'];
            } elseif ($data['image_material'] > 0) {
                $data['image_id'] = M('material_image')->where('id', $data['image_material'])->value('cover_id');
            }
            unset($data['msg_type'], $data['image'], $data['image_material']);
            $data['cTime'] = NOW_TIME;
            $data['pbid'] = get_pbid();
            $id = $Model->insertGetId($data);
            if ($id) {
                // $this->_saveKeyword ( $model, $id );
                
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                
                $this->success('添加' . $model['title'] . '成功！', U('add', $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            
            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $model['title'];
            ! config('SEND_GROUP_MSG') && $this->assign('normal_tips', '温馨提示：目前微信仅开放认证公众号的群发消息权限，未认证公众号无法使用此功能');
            $this->assign('normal_tips', '注意：1、对于认证订阅号，群发接口每天可成功调用1次，此次群发可选择发送给全部用户或某个分组；<br/>
2、对于认证服务号虽然使用高级群发接口的每日调用限制为100次，但是用户每月只能接收4条，无论在公众平台网站上，还是使用接口群发，用户每月只能接收4条群发消息，多于4条的群发将对该用户发送失败；');
            
            $map['pbid'] = get_pbid();
//             $map['manager_id'] = $this->mid;
            $map['is_del'] = 0;
            $group_list = M('auth_group')->where(wp_where($map))->select();
            $this->assign('group_list', $group_list);
            
            return $this->fetch();
        }
    }

    public function del()
    {
        $model = $this->getModel('Message');
        return parent::common_del($model);
    }

    // 预览群发的信息
    public function preview()
    {
        /*
         * if (! config( 'SEND_GROUP_MSG' )) {
         * $this->error ( '抱歉，您的公众号没有群发消息的权限' );
         * }
         */
        if (! public_interface('SEND_GROUP_MSG')) {
            $this->error('抱歉，您的公众号没有群发消息的权限');
        }
        $openids = wp_explode(I('preview_openids'));
        if (empty($openids)) {
            $this->error('预览OpenID不能为空');
        }
        $info = $this->_sucai_media_info();
        if ($info['msgtype'] == 'text') {
            $param['text']['content'] = $info['media_id'];
        } else if ($info['msgtype'] == 'mpnews') {
            $param['mpnews']['media_id'] = $info['media_id'];
        } else if ($info['msgtype'] == 'voice') {
            $param['voice']['media_id'] = $info['media_id'];
        } else if ($info['msgtype'] == 'mpvideo') {
            $param['mpvideo']['media_id'] = $info['media_id'];
        } else if ($info['msgtype'] == 'image') {
            $param['image']['media_id'] = $info['media_id'];
        }
        $param['msgtype'] = $info['msgtype'];
        
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=' . get_access_token();
        $count = 0;
        foreach ($openids as $openid) {
            $param['touser'] = $openid;
            $res = post_data($url, $param);
            if ($res['errcode'] > 0) {
                $count ++;
            }
        }
        if ($count == 0) {
            $this->success('发送成功~');
        } else {
            $this->error('有 ' . $count . '条信息发送失败');
        }
    }

    // 按用户组发送
    public function _send_by_group($group_id)
    {
        if ($group_id) {
            // 本地分组ID换取微信端的分组ID
            $map['id'] = $group_id;
            $groupid = M('auth_group')->where(wp_where($map))->value('wechat_group_id');
        }
        
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . get_access_token();
        
        $paramStr = '';
        if ($group_id) {
            // $param ['filter'] ['is_to_all'] = "false";
            // $param ['filter'] ['group_id'] = $groupid;
            $paramStr .= '{"filter":{"is_to_all":false,"tag_id":"' . $groupid . '"},';
        } else {
            // $param ['filter'] ['is_to_all'] = "true";
            $paramStr .= '{"filter":{"is_to_all":true},';
        }
        $info = $this->_sucai_media_info();
        
        if ($info['msgtype'] == 'text') {
            // $param ['text'] ['content'] = $info ['media_id'];
            $paramStr .= '"text":{"content":"' . $info['media_id'] . '"},"msgtype":"text"}';
        } else if ($info['msgtype'] == 'mpnews') {
            // $param ['mpnews'] ['media_id'] = $info ['media_id'];
            $paramStr .= '"mpnews":{"media_id":"' . $info['media_id'] . '"},"msgtype":"mpnews","send_ignore_reprint":1}';
        } else if ($info['msgtype'] == 'voice') {
            // $param ['mpnews'] ['media_id'] = $info ['media_id'];
            $paramStr .= '"voice":{"media_id":"' . $info['media_id'] . '"},"msgtype":"voice"}';
        } else if ($info['msgtype'] == 'mpvideo') {
            // $param ['mpnews'] ['media_id'] = $info ['media_id'];
            $paramStr .= '"mpvideo":{"media_id":"' . $info['media_id'] . '"},"msgtype":"mpvideo"}';
        } else if ($info['msgtype'] == 'image') {
            // $param ['mpnews'] ['media_id'] = $info ['media_id'];
            $paramStr .= '"image":{"media_id":"' . $info['media_id'] . '"},"msgtype":"image"}';
        }
        
        $res = $this->post_data($url, $paramStr);
        
        return $res['msg_id'];
    }

    // 按用户组发送 订阅号不可用，服务号认证后可用
    public function _send_by_openid($openids)
    {
        $openids = wp_explode($openids);
        if (empty($openids)) {
            $this->error('要发送的OpenID值不能为空');
        }
        if (count($openids) < 2) {
            $this->error('OpenID至少需要2个或者2个以上');
        }
        
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . get_access_token();
        
        $info = $this->_sucai_media_info();
        
        $param['touser'] = $openids;
        if ($info['msgtype'] == 'text') {
            $param['text']['content'] = $info['media_id'];
            $param['msgtype'] = $info['msgtype'];
        } else if ($info['msgtype'] == 'mpnews') {
            $param['mpnews']['media_id'] = $info['media_id'];
            $param['msgtype'] = $info['msgtype'];
            $param['send_ignore_reprint'] = 1;
        } else if ($info['msgtype'] == 'voice') {
            $param['voice']['media_id'] = $info['media_id'];
            $param['msgtype'] = $info['msgtype'];
        } else if ($info['msgtype'] == 'mpvideo') {
            $param['mpvideo']['media_id'] = $info['media_id'];
            $param['msgtype'] = $info['msgtype'];
        } else if ($info['msgtype'] == 'image') {
            $param['image']['media_id'] = $info['media_id'];
            $param['msgtype'] = $info['msgtype'];
        }
        
        $param = json_url($param);
        $res = $this->post_data($url, $param);
        
        return $res['msg_id'];
    }

    // 获取素材的media_id
    public function _sucai_media_info()
    {
        $type = I('msg_type');
        $content = I('content');
        $appmsg_id = I('appmsg_id');
        // $image = I ( 'image' );
        $post = input('post.');
        if ($type == 'text') {
            if (empty($content)) {
                $this->error('文本内容不能为空');
            }
            
            $res['media_id'] = $content;
            $res['msgtype'] = 'text';
        } else if ($type == 'image') {
            // 图片
            $image_material = input('post.image_material');
            $image_cover_id = input('post.image');
            if (empty($image_material) && empty($image_cover_id)) {
                // code...
                $this->error('发送图片不能为空');
            }
            if ($image_cover_id) {
                $res['media_id'] = D('common/Custom')->get_image_media_id($image_cover_id);
            } else if ($image_material) {
                $imageMaterial = M('material_image')->where('id', $image_material)->find();
                // $data ['image_id'] = $imageMaterial ['cover_id'];
                if ($imageMaterial['media_id']) {
                    $res['media_id'] = $imageMaterial['media_id'];
                } else {
                    $res['media_id'] = D('common/Custom')->get_image_media_id($imageMaterial['cover_id']);
                }
            }
            $res['msgtype'] = 'image';
        } else if ($type == 'appmsg') {
            if (empty($appmsg_id)) {
                $this->error('图文素材不能为空');
            }
            
            $res['media_id'] = D('material/Material')->getMediaIdByGroupId($appmsg_id);
            $res['msgtype'] = 'mpnews';
        } else if ($type == 'voice') {
            $voice = I('voice_id');
            if (empty($voice)) {
                $this->error('语音素材不能为空');
            }
            
            $file = M('material_file')->where('id', $voice)->find();
            if ($file['media_id']) {
            	$res['media_id'] = $file['media_id'];
            } else {
            	$res['media_id'] = D('common/Custom')->get_file_media_id($file['file_id'], 'voice');
            }
            $res['msgtype'] = 'voice';
        } else if ($type == 'video') {
            $video = I('video_id');
            if (empty($video)) {
                $this->error('视频素材不能为空');
            }
            
            $file = M('material_file')->where('id', $video)->find();
            if ($file['media_id']) {
            	$mediaId = $file['media_id'];
            } else {
            	$mediaId = D('common/Custom')->get_file_media_id($file['file_id'], 'video');
            }
            $data['media_id'] = $mediaId;
            $data['title'] = $file['title'];
            $data['description'] = $file['introduction'];
            // dump($data);
            // exit();
            $url1 = "https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token=" . get_access_token();
            $result = post_data($url1, $data);
            
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                $this->error(error_msg($result));
            }
            $res['media_id'] = $result['media_id'];
            $res['msgtype'] = 'mpvideo';
        }
        return $res;
    }

    public function custom_sendall()
    {
        $this->assign('normal_tips', '温馨提示<br/>客服群发接口是指：管理者可以给 在48小时内主动发消息给公众号的用户群发消息 ，发送次数没有限制；如果没有成功接收到消息的用户，则在他主动发消息给公众号时，再重新发给该用户。');
        
        // $this->assign ( 'normal_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
        if (IS_POST) {
            $data['ToUserName'] = get_pbid();
            $data['cTime'] = time();
            $data['msgType'] = input('post.msg_type');
            $data['manager_id'] = $this->mid;
            $data['content'] = input('post.content');
            $data['send_type'] = $sendType = input('post.send_type');
            $data['group_id'] = $groupId = input('post.group_id');
            $data['send_openids'] = $sendOpenid = input('post.send_openids');
            if ($sendType == 1 && $sendOpenid == '') {
                $this->error('指定的Openid值不能为空');
            }
            if ($data['msgType'] == 'appmsg') {
                $data['msgType'] = 'news';
            }
            $map1['ToUserName'] = get_pbid();
            $diff = M('custom_sendall')->where(wp_where($map1))->value("max(diff) as diff");
            $diff += 1;
            $openidArr = $this->_get_user_openid($sendType, $groupId, $sendOpenid);
            $count = 0;
            foreach ($openidArr as $k => $v) {
                if ($data['msgType'] == 'text') {
                    if (! $data['content']) {
                        $this->error('文本内容不能为空');
                    }
                    // 文本
                    $result = D('common/Custom')->replyText($k, $data['content']);
                } else if ($data['msgType'] == 'news') {
                    // 图文
                    $data['msgType'] = 'news';
                    $data['news_group_id'] = input('post.appmsg_id');
                    if (empty($data['news_group_id'])) {
                        $this->error('请选择图文消息！');
                    }
                    $result = D('common/Custom')->replyNews($k, $data['news_group_id']);
                } else if ($data['msgType'] == 'image') {
                    // 图片
                    $image_material = input('post.image_material');
                    $image_cover_id = input('post.image');
                    if (empty($image_material) && empty($image_cover_id)) {
                        $this->error('发送图片不能为空');
                    }
                    if ($image_cover_id) {
                        $data['image_id'] = $image_cover_id;
                        $data['media_id'] = D('common/Custom')->get_image_media_id($image_cover_id);
                        $result = D('common/Custom')->replyImage($k, $data['media_id'], '');
                    } else if ($image_material) {
                        $imageMaterial = M('material_image')->where('id', $image_material)->find();
                        $data['image_id'] = $imageMaterial['cover_id'];
                        if ($imageMaterial['media_id']) {
                            $data['media_id'] = $imageMaterial['media_id'];
                        } else {
                            $data['media_id'] = D('common/Custom')->get_image_media_id($imageMaterial['cover_id']);
                        }
                        $result = D('common/Custom')->replyImage($k, $data['media_id'], '');
                    } else {
                        $this->error('请选择要发送的图片');
                    }
                } else if ($data['msgType'] == 'voice') {
                    
                    // 语音
                    $data['voice_id'] = $voiceId = input('post.voice_id');
                    if (empty($voiceId)) {
                        $this->error('请选择语音消息');
                    }
                    
                    $voiceMaterial = M('material_file')->where('id', $voiceId)->find();
                    if ($voiceMaterial['media_id']) {
                        $data['media_id'] = $voiceMaterial['media_id'];
                    } else {
                        $data['media_id'] = D('common/Custom')->get_file_media_id($voiceMaterial['file_id'], 'voice');
                    }
                    $result = D('common/Custom')->replyVoice($k, $data['media_id'], '');
                } else if ($data['msgType'] == 'video') {
                    // 视频
                    $data['video_id'] = $videoId = input('post.video_id');
                    if (empty($videoId)) {
                        $this->error('请选择视频消息');
                    }
                    $videoMaterial = M('material_file')->where('id', $videoId)->find();
                    $data['video_title'] = $videoMaterial['title'];
                    $data['video_description'] = $videoMaterial['introduction'];
                    $data['video_thumb'] = D('common/Custom')->get_thumb_media_id();
                    
                    if ($videoMaterial['media_id']) {
                        $data['media_id'] = $videoMaterial['media_id'];
                    } else {
                        $data['media_id'] = D('common/Custom')->get_file_media_id($videoMaterial['file_id'], 'video');
                    }
                    $result = D('common/Custom')->replyVideo($k, $data['media_id'], '', $data['video_thumb'], $videoMaterial['title'], $data['video_description']);
                }
                if ($result['status'] == 1) {
                    $data['is_send'] = 1;
                } else {
                    $data['is_send'] = 0;
                    $count ++;
                }
                $data['FromUserName'] = $v;
                $data['uid'] = $k;
                $data['diff'] = $diff;
                M('custom_sendall')->insert($data);
            }
            if ($count == 0) {
                $this->success('发送成功！');
            } else {
                $this->error('有 ' . $count . ' 条消息 发送失败！');
            }
        } else {
            $map['pbid'] = get_pbid();
//             $map['manager_id'] = $this->mid;
            $map['is_del'] = 0;
            $group_list = M('auth_group')->where(wp_where($map))->select();
            $this->assign('group_list', $group_list);
            $this->assign('post_url', '');
            return $this->fetch();
        }
    }

    // 未发送成功的消息重新发
    public function sendOldMessage()
    {
        $map['manager_id'] = $this->mid;
        $map['ToUserName'] = get_pbid();
        $map['is_send'] = 0;
        $diff = I('diff');
        if ($diff) {
            $map['diff'] = $diff;
        }
        $messageData = M('custom_sendall')->where(wp_where($map))->select();
        $count = 0;
        if (! empty($messageData)) {
            foreach ($messageData as $data) {
                if ($data['msgType'] == 'text') {
                    // 文本
                    $result = D('common/Custom')->replyText($data['uid'], $data['content']);
                } else if ($data['msgType'] == 'news') {
                    // 图文
                    $result = D('common/Custom')->replyNews($data['uid'], $data['news_group_id']);
                } else if ($data['msgType'] == 'image') {
                    // 图片
                    $result = D('common/Custom')->replyImage($data['uid'], $data['media_id'], '');
                } else if ($data['msgType'] == 'voice') {
                    // 语言
                    $result = D('common/Custom')->replyVoice($data['uid'], $data['media_id'], '');
                } else if ($data['msgType'] == 'video') {
                    // 视频
                    $result = D('common/Custom')->replyVoice($data['uid'], $data['media_id'], '', $data['video_thumb'], $data['video_title'], $data['video_description']);
                }
                
                if ($result['status'] == 1) {
                    $ids[$data['id']] = $data['id'];
                }
            }
            if ($ids) {
                $map1['id'] = array(
                    'in',
                    $ids
                );
                $save['is_send'] = 1;
                $res = M('custom_sendall')->where(wp_where($map1))->update($save);
                if ($res) {
                    $count ++;
                }
            }
        }
        echo $count;
    }

    /*
     * sendType:0 按组发 1：指定opendid
     * groupid :0 指所有用户
     */
    public function _get_user_openid($sendType = 0, $groupId = 0, $openidStr = '')
    {
        $map['has_subscribe'] = 1;
        $map['pbid'] = get_pbid();
        $allUser = M('public_follow')->where(wp_where($map))->column('openid', 'uid');
        foreach ($allUser as $k => $v) {
            $uidArr[$k] = $k;
            $openidArr[$v] = $k;
        }
        if ($sendType == 0 && $groupId == 0) {
            return $allUser;
        } else if ($sendType == 0 && $groupId != 0) {
            $map1['uid'] = array(
                'in',
                $uidArr
            );
            $map1['group_id'] = $groupId;
            $groupData = M('auth_group_access')->where(wp_where($map1))->select();
            foreach ($groupData as $gr) {
                $data[$gr['uid']] = $allUser[$gr['uid']];
            }
            return $data;
        } else if ($sendType == 1) {
            $openids = wp_explode($openidStr);
            foreach ($openids as $op) {
                $uid = $openidArr[$op];
                if ($uid) {
                    $data[$uid] = $op;
                } else {
                    $this->error('Openid为: ' . $op . ' 的用户不存在');
                }
            }
            return $data;
        }
    }

    /**
     * ***********消息管理*******************
     */
    
    // 客服群发消息管理
    public function custom_sendall_lists()
    {
        $this->listNav();
        $map = $this->dayMap();
        $map['ToUserName'] = get_pbid();
//         $map['manager_id'] = $this->mid;
        $row = 20;
        
        $ids = M('custom_sendall')->where(wp_where($map))
            ->field('MAX(id) as mid')
            ->group('diff')
            ->paginate($row);
        $arr = [];
        foreach ($ids as $vv) {
            $arr[] = $vv['mid'];
        }
        $map['id'] = array(
            'in',
            $arr
        );
        $list = M('custom_sendall')->where(wp_where($map))
            ->order('id desc')
            ->paginate();
        $list = dealPage($list);
        $dao = D('common/User');
        
        foreach ($list['list_data'] as &$v) {
            $countData = M('custom_sendall')->where(wp_where(array(
            	'ToUserName'=>$map['ToUserName'],
                'diff' => $v['diff'],
                'is_send' => 1
            )))
                ->order('id desc')
                ->field('count(uid) count,diff')
                ->select();
            $v['chenggong'] = $countData[0]['count'];
            $countData = M('custom_sendall')->where(wp_where(array(
            	'ToUserName'=>$map['ToUserName'],
                'diff' => $v['diff'],
                'is_send' => 0
            )))
                ->order('id desc')
                ->field('count(uid) count,diff')
                ->select();
            $v['shibai'] = $countData[0]['count'];
            
            $v = $this->makeContent($v);
        }
        $url = U('custom_sendall_lists');
        $this->assign('searchUrl', $url);
        $this->assign($list);
        $this->assign('normal_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息');
        
        return $this->fetch('');
    }

    private function listNav()
    {
        $param['mdm'] = I('mdm');
        $act = strtolower(ACTION_NAME);
        
        $res['title'] = '高级群发消息';
        $res['url'] = U('sendall_lists', $param);
        $res['class'] = $act == 'sendall_lists' ? 'cur' : '';
        $nav[] = $res;
        
        $res['title'] = '客服接口群发消息';
        $res['url'] = U('custom_sendall_lists', $param);
        $res['class'] = $act == 'custom_sendall_lists' ? 'cur' : '';
        $nav[] = $res;
        $this->assign('sub_nav', $nav);
    }

    private function dayMap()
    {
        $day = I('send_time', 1);
        $now_day = strtotime(time_format(time(), 'Y-m-d'));
        
        $opt = 'between';
        if ($day == 2) {
            // 今天
            $opt = 'egt';
            $time = $now_day;
        } else if ($day == 3) {
            // 昨天
            $zhuo_day = $now_day - 1 * 24 * 60 * 60;
            $time = [
                $zhuo_day,
                $now_day
            ];
        } else if ($day == 4) {
            // 前天
            $qian_day = $now_day - 2 * 24 * 60 * 60;
            $time = [
                $qian_day,
                $now_day
            ];
        } else if ($day == 5) {
            // 更早
            $time = $now_day - 4 * 24 * 60 * 60;
            $opt = 'elt';
        } else {
            // 最近五天
            $time = $now_day - 4 * 24 * 60 * 60;
            $opt = 'egt';
        }
        
        $map['cTime'] = array(
            $opt,
            $time
        );
        
        return $map;
    }

    private function makeContent($v)
    {
        if ($v['send_type'] == 0 && $v['group_id'] == 0) {
            $v['send_tip'] = '所有用户';
        } else if ($v['send_type'] == 0 && $v['group_id'] != 0) {
            $v['send_tip'] = M('auth_group')->where('id', $v['group_id'])->value('title');
        } else {
            $v['send_tip'] = '指定用户';
        }
        
        $type = isset($v['msgType']) ? $v['msgType'] : $v['msgtype'];
        
        $v['Content'] = '';
        if ($type == 'text') {
            $v['msgtype_title'] = '文本';
            $v['Content'] = $v['content'];
        } else if ($type == 'news' || $type == 'appmsg') {
            $v['msgtype_title'] = '图文';
            $group_id = isset($v['news_group_id']) ? $v['news_group_id'] : $v['appmsg_id'];
            $appMsgData = M('material_news')->where('group_id', $group_id)->select();
            if (empty($appMsgData)) {
                $v['Content'] = '图文已删除';
            } else {
                foreach ($appMsgData as $vo) {
                    if (empty($vo['url'])) {
                        $art_url = U('material/Wap/news_detail', array(
                            'id' => $vo['id']
                        ));
                        // 文章内容
                        $v['Content'] = '<a href="' . $art_url . '" >' . $vo['title'] . '</a>';
                    } else {
                        $v['Content'] = '<a href="' . $vo['url'] . '" >' . $vo['title'] . '</a>';
                    }
                    $v['image_url'] = get_cover_url($vo['cover_id']);
                }
            }
        } else if ($type == 'mpvideo' || $type == 'video') {
            $v['msgtype_title'] = '视频';
            $videoMaterial = M('material_file')->where('id', $v['video_id'])->find();
            $v['Content'] = isset($videoMaterial['title']) ? $videoMaterial['title'] : '视频已删除';
        } else if ($type == 'voice') {
            $v['msgtype_title'] = '语音';
            $voiceMaterial = M('material_file')->where('id', $v['voice_id'])->find();
            $v['Content'] = isset($voiceMaterial['title']) ? $voiceMaterial['title'] : '语音已删除';
        } else if ($type == 'image') {
            $v['msgtype_title'] = '图片';
            $v['Content'] = '';
            $url = get_cover_url($v['image_id']);
            if (empty($url)) {
                $v['Content'] = '图片已删除';
            } else {
                $v['image_url'] = $url;
            }
        }
        return $v;
    }

    // 群发消息管理
    public function sendall_lists()
    {
        $this->listNav();
        $map = $this->dayMap();
        
        $map['pbid'] = get_pbid();
        $list = M('message')->where(wp_where($map))
            ->order('id desc')
            ->paginate();
        $list = dealPage($list);
        
        foreach ($list['list_data'] as &$v) {
            $v = $this->makeContent($v);
        }
        $url = U('sendall_lists', $this->get_param);
        $this->assign('searchUrl', $url);
        $this->assign($list);
//         $this->assign('normal_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息');
        
        return $this->fetch();
    }

    // 设置消息状态
    public function set_status()
    {
        $map['id'] = I('id');
        $field = I('field');
        $val = I('val');
        
        $res = M('weixin_message')->where(wp_where($map))->setField($field, $val);
        echo $res;
    }

    // 设置为文本素材
    public function set_meterial()
    {
        $id = I('id');
        $type = I('type');
        $set_sucai = I('set_sucai');
        $message = M('weixin_message')->where('id', $id)->find();
        $res = 0;
        if ($type == 'text' && $message['Content']) {
            $map['pbid'] = get_pbid();
            $map['uid'] = $this->mid;
            $map['aim_id'] = $id;
            $map['aim_table'] = 'weixin_message';
            $material = M('material_text')->where(wp_where($map))
                ->field('id,is_use')
                ->find();
            if (! empty($material)) {
                $saveUse['is_use'] = $set_sucai;
                $res1 = M('material_text')->where(wp_where($map))->update($saveUse);
            } else {
                $data['pbid'] = get_pbid();
                $data['uid'] = $this->mid;
                $data['aim_id'] = $id;
                $data['aim_table'] = 'weixin_message';
                $data['content'] = $message['Content'];
                $data['is_use'] = $set_sucai;
                $res1 = M('material_text')->insertGetId($data);
            }
        } else if ($type == 'image') {
            $content = json_decode($message['Content'], true);
            $imagemap['media_id'] = $content['image']['media_id'];
            if (! $imagemap['media_id']) {
                $imagemap['media_id'] = $message['MediaId'];
            }
            $imagemap['pbid'] = get_pbid();
            $image = M('material_image')->where(wp_where($imagemap))->find();
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
                $res1 = M('material_image')->where(wp_where($imagemap))->update($save);
                // $dd['url']=$image['cover_url'];
            } else {
                $save['is_use'] = $set_sucai;
                $save['aim_id'] = $id;
                $save['aim_table'] = 'weixin_message';
                $save['media_id'] = $imagemap['media_id'];
                $save['cTime'] = time();
                $save['manager_id'] = $this->mid;
                $save['pbid'] = get_pbid();
                $save['cover_id'] = down_media($imagemap['media_id']);
                if (! $save['cover_id']) {
                    $save['cover_id'] = do_down_image($imagemap['media_id']);
                }
                if (! $image['cover_url']) {
                    $save['cover_url'] = get_cover_url($save['cover_id']);
                }
                $res1 = M('material_image')->insertGetId($save);
            }
        } else if ($type == 'voice') {
            $content = json_decode($message['Content'], true);
            $voicemap['media_id'] = $content['voice']['media_id'];
            if (! $voicemap['media_id']) {
                $voicemap['media_id'] = $message['MediaId'];
            }
            $voicemap['pbid'] = get_pbid();
            $voicemap['manager_id'] = $this->mid;
            $voicemap['type'] = 1;
            $voice = M('material_file')->where(wp_where($voicemap))->find();
            if ($voice) {
                // 保存
                $save['is_use'] = $set_sucai;
                $save['aim_id'] = $id;
                $save['aim_table'] = 'weixin_message';
                $res1 = M('material_file')->where(wp_where($voicemap))->update($save);
                // $dd['url']=$image['cover_url'];
            } else {
                $save['is_use'] = $set_sucai;
                $save['aim_id'] = $id;
                $save['aim_table'] = 'weixin_message';
                $save['media_id'] = $voicemap['media_id'];
                $save['cTime'] = time();
                $save['manager_id'] = $this->mid;
                $save['type'] = 1;
                $save['pbid'] = get_pbid();
                $save['file_id'] = down_file_media($voicemap['media_id'], 'voice');
                $res1 = M('material_file')->insertGetId($save);
            }
        } else if ($type == 'video') {
            $content = json_decode($message['Content'], true);
            $videomap['media_id'] = $content['video']['media_id'];
            if (! $videomap['media_id']) {
                $videomap['media_id'] = $message['MediaId'];
            }
            $videomap['pbid'] = get_pbid();
            $videomap['manager_id'] = $this->mid;
            $videomap['type'] = 2;
            $video = M('material_file')->where(wp_where($videomap))->find();
            if ($video) {
                // 保存
                $save['is_use'] = $set_sucai;
                $save['aim_id'] = $id;
                $save['aim_table'] = 'weixin_message';
                $res1 = M('material_file')->where(wp_where($videomap))->update($save);
                // $dd['url']=$image['cover_url'];
            } else {
                $save['is_use'] = $set_sucai;
                $save['aim_id'] = $id;
                $save['aim_table'] = 'weixin_message';
                $save['media_id'] = $videomap['media_id'];
                $save['cTime'] = time();
                $save['manager_id'] = $this->mid;
                $save['type'] = 2;
                $save['pbid'] = get_pbid();
                $save['file_id'] = down_file_media($videomap['media_id'], 'video');
                $res1 = M('material_file')->insertGetId($save);
            }
        }
        if ($res1 !== false) {
            // $isMaterial=$message['is_material'];
            $save['is_material'] = $set_sucai;
            $res = M('weixin_message')->where(wp_where(array(
                'id' => $id
            )))->update($save);
        }
        
        echo $res;
    }
}
