<?php
namespace app\home\controller;

/**
 * 微信交互控制器
 * 主要获取和反馈微信平台的数据
 */
class Weixin extends Home
{

    var $pbid;

    private $data = [];

    public function index()
    {
        $weixin = D('Weixin');
        // 获取数据
        $data = $weixin->getData();
        addWeixinLog($data, 'data');
        
        $this->data = $data;

        if (! empty($data['FromUserName'])) {
            get_openid($data['FromUserName']);
        }
        $this->pbid = get_pbid();
        $data['EventKey'] = isset($data['EventKey']) ? $data['EventKey'] : '';
        // 判断是否为扫码绑定
        $addon = M('qr_code')->where('scene_id', $data['EventKey'])->value('addon');
        if ($addon == 'ScanBindLogin' || $addon == 'ScanLogin') {
            $data['ScanBindLogin'] = 'ScanBindLogin';
        }
        // 初始化用户
        $data['ToUserName'] == 'gh_3c884a361561' || (($addon == 'ScanBindLogin' || $addon == 'ScanLogin') || $this->init_follow($data, $weixin));
        // 回复数据
        $this->reply($data, $weixin);
        
        // 客服接口群发消息：未发送成功的消息给用户重新发
        $this->sendOldMessage($data['ToUserName'], $data['FromUserName']);
        // 结束程序。防止oneThink框架的调试信息输出
        $length = ob_get_length();
        if (empty($length)) {
            exit('success');
        } else {
            exit();
        }
    }

    private function reply($data, $weixin)
    {
        if (isset($data['Content'])) {
            $key = trim($data['Content']);
        } else {
            $key = '';
            $data['Content'] = '';
        }
        
        if (isset($data['MsgType']) && empty($key)) {
            $key = $data['MsgType'];
        }
        
        $keywordArr = [];
        // 插件权限控制
        $wpid_status = D('common/AddonStatus')->getList();
        foreach ($wpid_status as $a => $s) {
            $s == 1 || $forbit_addon[$a] = $a;
        }
        // 所有安装过的微信插件
        $addon_list = (array) D('home/Addons')->getWeixinList(false, $wpid_status);
        /**
         * 微信事件转化成特定的关键词来处理
         * event可能的值：
         * subscribe : 关注公众号
         * unsubscribe : 取消关注公众号
         * scan : 扫描带参数二维码事件
         * location : 上报地理位置事件
         * click : 自定义菜单事件
         */
        if (isset($data['MsgType']) && ($data['MsgType'] == 'event' || $data['MsgType'] == 'location')) {
            $event = strtolower($data['MsgType'] == 'location' ? $data['MsgType'] : $data['Event']);
            
            if ($event == 'click' && ! empty($data['EventKey'])) {
                $key = $data['Content'] = $data['EventKey'];
            } else {
                $key = $data['Content'] = $event;
            }
        } else {
            // 数据保存到消息管理中
            $data1 = $data;
            unset($data1['EventKey']);
            M('weixin_message')->strict(false)->insert($data1);
        }
        if ($data['ToUserName'] == 'gh_3c884a361561' || (isset($data['appid']) && $data['appid'] == 'wx570bc396a51b8ff8')) {
            $addons[$key] = 'PublicBind';
        }
        // 通过获取上次缓存的用户状态来定位处理的插件
        $openid = $data['FromUserName'];
        $user_status = S('user_status_' . $openid);
        
        $accept = isset($user_status['keywordArr']['accept']) ? $user_status['keywordArr']['accept'] : '';
        if (! empty($accept)) {
            if (($accept['type'] == 'regex' && ! preg_match($accept['data'], $key)) || ($accept['type'] == 'array' && ! in_array($key, $accept['data']))) {
                $user_status = false;
                S('user_status_' . $openid, null); // 可设置规定只能接收某些值，如果用户输入的内容不是规定的值，则放弃当前状态,支持正则和数组两种规定方式
            }
        }
        if (! isset($addons[$key]) && $user_status) {
            $addons[$key] = $user_status['addon'];
            $keywordArr = $user_status['keywordArr'];
            S('user_status_' . $openid, null);
        }
        if (! isset($addons[$key]) && strpos($key, 'material::/') !== false) {
            $material = substr($key, 11);
            list ($type, $id) = explode(':', $material);
            
            $map['openid'] = $openid;
            $uid = M('public_follow')->where(wp_where($map))->value('uid');
            switch ($type) {
                case 'news':
                    // 1:图文 不用客服接口发
                    //客服接口发送图文消息（点击跳转到外链） 图文消息条数限制在1条以内，注意，如果图文数超过1，则将会返回错误码45008。
                    //客服接口发送图文消息（点击跳转到图文消息页面） 图文消息条数限制在1条以内，注意，如果图文数超过1，则将会返回错误码45008。
//                     D('common/Custom')->replyNews($uid, $id);
                	D('home/Weixin')->material_reply('news:'.$id);
                	
                    break;
                case 'text':
                    // 2:文本
                    $textMap['id'] = $id;
                    $content = M('material_text')->where(wp_where($textMap))->value('content');
                    D('common/Custom')->replyText($uid, $content);
                    break;
                case 'img':
                    // 3:图片
                    $textMap['id'] = $id;
                    D('common/Custom')->replyImage($uid, $id, 'material_image');
                    break;
                case 'voice':
                    // 4:语音
                    D('common/Custom')->replyVoice($uid, $id, 'material_file');
                    break;
                case 'video':
                    // 5:视频
                    D('common/Custom')->replyVideo($uid, $id, 'material_file', '', '', '');
                    break;
            }
            exit('success');
        }
 
        // 通过插件标识名、插件名或者自定义关键词来定位处理的插件
        if (! isset($addons[$key])) {
            $keyword_cache = S('keyword_cache');
            if ($keyword_cache === false || config('app_debug')) {
                $keyword_cache = [];
                foreach ($addon_list as $k => $vo) {
                    // $keyword_cache[$vo['name']] = $k;
                    // $keyword_cache[$vo['title']] = $k;
                    
                    $path = env('app_path') . $vo['name'] . '/keyword.php';
                    if (file_exists($path)) {
                        $keywords = include $path;
                        if (! empty($keywords)) {
                            $keyword_cache = array_merge($keyword_cache, $keywords);
                        }
                    }
                    S('keyword_cache', $keyword_cache);
                }
            }
            foreach ($keyword_cache as $k => $val) {
                $addons[$k] = $val;
            }
        }

        // 通过精准关键词来定位处理的插件 wpid=0是插件安装时初始化的模糊关键词，所有公众号都可以用
        $where = "wpid='0' OR wpid='{$this->pbid}'";
        if (! empty($forbit_addon)) {
            $like['addon'] = array(
                'not in',
                $forbit_addon
            );
        }
        // 完全匹配
        if (! isset($addons[$key])) {
            $like['keyword'] = $key;
            $like['keyword_type'] = 0;
            $keywordArr = M('keyword')->where(wp_where($where))
                ->where(wp_where($like))
                ->order('id desc')
                ->find();
            
            if (! empty($keywordArr['addon'])) {
                $addons[$key] = $keywordArr['addon'];
            }
        }
        
        // 最新匹配（前提是关键词是完全匹配）
        if (! isset($addons[$key])) {
            $like['keyword'] = $key;
            $like['keyword_type'] = 5;
            $keywordArr = M('keyword')->where(wp_where($where))
                ->where(wp_where($like))
                ->order('id desc')
                ->find();
            if (! empty($keywordArr['addon'])) {
                $addons[$key] = $keywordArr['addon'];
            }
        }
        // 通过模糊关键词来定位处理的插件
        if (! isset($addons[$key])) {
            unset($like['keyword']);
            $like['keyword_type'] = array(
                'exp',
                'in (1,2,3,4)'
            );
            $list = M('keyword')->where(wp_where($where))
                ->where(wp_where($like))
                ->order('keyword_length desc, id desc')
                ->select();
            foreach ($list as $keywordInfo) {
                $this->_contain_keyword($keywordInfo, $key, $addons, $keywordArr);
            }
        }
        // 通过通配符，查找默认处理方式
        if (! isset($addons[$key])) {
            unset($like['keyword_type']);
            $like['keyword'] = '*';
            $keywordArr = M('keyword')->where(wp_where($where))
                ->where(wp_where($like))
                ->order('id desc')
                ->find();
            if (! empty($keywordArr['addon'])) {
                $addons[$key] = $keywordArr['addon'];
            }
        }

        if (isset($data['ScanBindLogin']) && $data['ScanBindLogin']) {
            unset($data['ScanBindLogin']);
            $addons[$key] = 'weixin';
        }
        if (isset($data['Content']) && in_array($data['Content'], array(
            'unsubscribe',
            'subscribe'
        ))) {
            $addons[$key] = 'weixin';
        }
        if ($key == '自动检测') {
            return D('Weixin')->replyText('auto_check');
        } // dump($addons);dump($data);exit;
          // 关键词自动回复消息
        if (! isset($addons[$key]) && isset($data['Content']) && ! empty($data['Content'])) {
            $reply = $this->is_keyword($data['Content']);
            if ($reply) {
                $addons[$key] = 'weixin';
                $data['auto_reply'] = $reply;
            }
        }

        // 以上都无法定位插件时，如果开启了未识别回答，则默认使用未识别回答插件
        if (! isset($addons[$key]) && ($ex_arr = $this->isNoAnswer())) {
            $addons[$key] = 'weixin';
            $data['noAnswer'] = $ex_arr;
        }
        // dump($addons[$key]);
        // dump($keywordArr);
        // 最终也无法定位到插件，终止操作
        if (! isset($addons[$key]) || ! file_exists(env('app_path') . $addons[$key] . '/model/WeixinAddon.php')) {
            echo 'success';
            exit();
        }
        
        // 加载相应的插件来处理并反馈信息
        require_once env('app_path') . $addons[$key] . '/model/WeixinAddon.php';
        $model = D($addons[$key] . '/WeixinAddon');
        // addWeixinLog($addons[$key],'weixinkey');
        $model->reply($data, $keywordArr);
    }

    public function is_keyword($content)
    {
        $res = M('auto_reply')->where('pbid', PBID)
            ->order('id desc')
            ->select();
        foreach ($res as $v) {
            $tmp = explode(' ', $v['keyword']);
            if (in_array($content, $tmp)) {
                return $v['msg_type'] . ':' . $v[$v['msg_type'] . '_id'];
            }
        }
        return false;
    }

    public function isNoAnswer()
    {
        $config = D('common/PublicConfig')->getConfig('', 'weixin_no_answer');
        if (empty($config)) {
            return false;
        }
        
        $ex_arr = explode(':', $config['stype']);
        if (isset($ex_arr[1]) && $ex_arr[1] > 0) {
            return $config['stype'];
        }
        return false;
    }

    // 处理关键词包含的算法
    private function _contain_keyword($keywordInfo, $key, &$addons, &$keywordArr)
    {
        if (isset($addons[$key])) {
            return false;
        }
        // 支持正则匹配
        if ($keywordInfo['keyword_type'] == 4) {
            if (preg_match($keywordInfo['keyword'], $key)) {
                $addons[$key] = $keywordInfo['addon'];
                $keywordArr = $keywordInfo;
            }
            return false;
        }
        
        $arr = explode($keywordInfo['keyword'], $key);
        if (count($arr) > 1) {
            // 在关键词不相等的情况下进行左右匹配判断，否则相等的情况肯定都匹配
            if ($keywordInfo['keyword'] != $key) {
                // 左边匹配
                if ($keywordInfo['keyword_type'] == 1 && ! empty($arr[0])) {
                    return false;
                }
                
                // 右边 匹配
                if ($keywordInfo['keyword_type'] == 2 && ! empty($arr[1])) {
                    return false;
                }
            }
            
            $addons[$key] = $keywordInfo['addon'];
            
            $keywordArr = $keywordInfo;
            $keywordArr['prefix'] = trim($arr[0]); // 关键词前缀，即包含关键词的前面部分
            $keywordArr['suffix'] = trim($arr[1]); // 关键词后缀，即包含关键词的后面部分
        }
    }

    private function init_follow($data, $dao = '')
    {
        if (input('?appid')) {
            $info = D('common/Publics')->getInfoByAppid(input('appid'));
        } elseif (input('?id')) {
            $info = D('common/Publics')->getInfoById(input('id'));
        } else {
            return false;
        }
        
        $config = S('PUBLIC_AUTH_' . $info['type']);
        if (! $config) {
            $config = M('public_auth')->column('name,type_' . $info['type'] . ' as val');
            
            S('PUBLIC_AUTH_' . $info['type'], $config, 86400);
        }
        if (is_array($config)) {
            foreach ($config as $c => $v) {
                config($c, $v); // 公众号接口权限
            }
        }
        // 初始化用户信息
        $GLOBALS['mid'] = $uid = D('common/Follow')->init_follow($data['FromUserName']);
        $user = getUserInfo($uid);
        // 绑定配置
        $config = getAddonConfig('UserCenter', $info['id']);
        $guestAccess = strtolower(CONTROLLER_NAME) != 'weixin';
        $userNeed = ($user['uid'] > 0 && $user['status'] < 2) || (empty($user) && $guestAccess);
        if (! empty($config) && $config['need_bind'] == 1 && $userNeed && config('USER_OAUTH')) {
            addWeixinLog($userNeed, 'data00000005');
            $bind_url = U('weixin/Wap/bind');
            if ($config['bind_start'] != 0 && strtolower($data['Event']) != 'subscribe') {
                $dao->replyText('请先<a href="' . $bind_url . '">绑定账号</a>再使用');
                exit();
            }
        }
    }

    function downloadPic()
    {
        $mediaId = I('media_id');
        if ($mediaId) {
            $id = down_media($mediaId);
            if ($id) {
                $this->ajaxReturn(array(
                    'picUrl' => get_cover_url($id),
                    'id' => $id,
                    'result' => 'success'
                ), 'JSON');
            } else {
                $this->ajaxReturn(array(
                    'id' => 0,
                    'result' => 'fail'
                ), 'JSON');
            }
        } else {
            $this->ajaxReturn(array(
                'id' => 0,
                'result' => 'fail'
            ), 'JSON');
        }
    }

    // 未发送成功的消息重新发
    function sendOldMessage($wpid, $openid)
    {
        $map['ToUserName'] = $wpid;
        $map['is_send'] = 0;
        $map['FromUserName'] = $openid;
        
        $messageData = M('custom_sendall')->where(wp_where($map))->select();
        $count = 0;
        if (empty($messageData)) {
            return false;
        }
        
        foreach ($messageData as $data) {
            switch ($data['msgType']) {
                case 'text': // 文本
                    $result = D('common/Custom')->replyText($data['uid'], $data['content']);
                    break;
                case 'news': // 图文
                    $result = D('common/Custom')->replyImage($data['uid'], $data['media_id'], '');
                    break;
                case 'text': // 图片
                    $result = D('common/Custom')->replyImage($data['uid'], $data['media_id'], '');
                    break;
                case 'text': // 语音
                    $result = D('common/Custom')->replyVoice($data['uid'], $data['media_id'], '');
                    break;
                case 'text': // 视频
                    $result = D('common/Custom')->replyVoice($data['uid'], $data['media_id'], '', $data['video_thumb'], $data['video_title'], $data['video_description']);
                    break;
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
            if ($res !== false) {
                $count ++;
            }
        }
    }
}
