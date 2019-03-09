<?php
namespace app\home\model;

use app\common\model\Base;

/**
 * 微信基础模型
 */
class Weixin extends Base
{

    var $data = [];

    var $wxcpt, $sReqTimeStamp, $sReqNonce, $sEncryptMsg;

    public function __construct()
    {
        $doNotInit = input('doNotInit', 0);
        if (! empty($doNotInit)) {
            return true;
        }
        addWeixinLog(GetCurUrl(), 'weixin_url');
        $content = wp_file_get_contents('php://input');
        // $content = "<xml><ToUserName><![CDATA[gh_f500368e90ad]]></ToUserName>
        // <FromUserName><![CDATA[o8rov1d6V8MxCc3xBI9XsU27geqI]]></FromUserName>
        // <CreateTime>1532695668</CreateTime>
        // <MsgType><![CDATA[text]]></MsgType>
        // <Content><![CDATA[签到]]></Content>
        // <MsgId>6582877769250820808</MsgId>
        // </xml>";
        ! empty($content) || die('这是微信请求的接口地址，直接在浏览器里无效');
        addWeixinLog($content, 'php://input');
        if (isset($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
            require_once(env('vendor_path') . 'WXBiz/wxBizMsgCrypt.php');
            
            $this->sReqTimeStamp = I('timestamp');
            $this->sReqNonce = I('nonce');
            $this->sEncryptMsg = I('msg_signature');
            
            if (isset($_GET['appid'])) {
                if ($_GET['appid'] == 'wx570bc396a51b8ff8') {
                    $info['public_id'] = 'gh_3c884a361561';
                    $info['encodingaeskey'] = 'DfEqNBRvzbg8MJdRQCSGyaMp6iLcGOldKFT0r8I6Tnp';
                    $info['appid'] = config('COMPONENT_APPID');
                } else {
                    $map['appid'] = I('appid');
                    $info = D('common/Publics')->where(wp_where($map))->find();
                    if ($info['is_bind']) {
                        $info['appid'] = config('COMPONENT_APPID');
                    }
                }
            } else {
                $id = I('id',0);
                if (empty($id)){
                	$id=I('pbid',0);
                }
                $info = D('common/Publics')->getInfo($id);
            }
            addWeixinLog($info, 'public_info');
            
            $this->wxcpt = new \WXBizMsgCrypt(SYSTEM_TOKEN, $info['encodingaeskey'], $info['appid']);
            
            $sMsg = ""; // 解析之后的明文
            $errCode = $this->wxcpt->decryptMsg($this->sEncryptMsg, $this->sReqTimeStamp, $this->sReqNonce, $content, $sMsg);
            if ($errCode != 0) {
                addWeixinLog($_GET, "DecryptMsg Error: " . $errCode);
                addWeixinLog($content, "DecryptMsg Error: content");
                exit();
            } else {
                // 解密成功，sMsg即为xml格式的明文
                $content = $sMsg;
            }
        }
        
        $data = new \SimpleXMLElement($content);
        // $data || die ( '参数获取失败' );
        foreach ($data as $key => $value) {
            $this->data[$key] = safe(strval($value));
        }
    }

    /* 获取微信平台请求的信息 */
    public function getData($name = null)
    {
        return $this->data;
    }

    /* ========================发送被动响应消息 begin================================== */
    /* 回复文本消息 */
    public function replyText($content)
    {
        $msg['Content'] = $content;
        $this->replyData($msg, 'text');
    }

    /* 回复图片消息 */
    public function replyImage($media_id)
    {
        $msg['Image']['MediaId'] = $media_id;
        $this->replyData($msg, 'image');
    }

    /* 回复语音消息 */
    public function replyVoice($media_id)
    {
        $msg['Voice']['MediaId'] = $media_id;
        $this->replyData($msg, 'voice');
    }

    /* 回复视频消息 */
    public function replyVideo($media_id, $title = '', $description = '')
    {
        $msg['Video']['MediaId'] = $media_id;
        $msg['Video']['Title'] = $title;
        $msg['Video']['Description'] = $description;
        $this->replyData($msg, 'video');
    }

    /* 回复音乐消息 */
    public function replyMusic($media_id, $title = '', $description = '', $music_url, $HQ_music_url)
    {
        $msg['Music']['ThumbMediaId'] = $media_id;
        $msg['Music']['Title'] = $title;
        $msg['Music']['Description'] = $description;
        $msg['Music']['MusicURL'] = $music_url;
        $msg['Music']['HQMusicUrl'] = $HQ_music_url;
        $this->replyData($msg, 'music');
    }

    /*
     * 回复图文消息 articles array 格式如下： array( array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>''), array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>'') );
     */
    public function replyNews($articles)
    {
        $msg['ArticleCount'] = count($articles);
        
        if (! config('USER_OAUTH')) {
            $openid = get_openid();
            foreach ($articles as &$vo) {
                $vo['Url'] .= '&openid=' . $openid;
            }
        }
        $msg['Articles'] = $articles;
        $this->replyData($msg, 'news');
    }

    /* 发送回复消息到微信平台 */
    private function replyData($msg, $msgType)
    {
        $msg['ToUserName'] = $this->data['FromUserName'];
        $msg['FromUserName'] = $this->data['ToUserName'];
        $msg['CreateTime'] = NOW_TIME;
        $msg['MsgType'] = $msgType;
        
        if (isset($_REQUEST['doNotInit']) && $_REQUEST['doNotInit']) {
            // dump ( $msg );
            exit();
        }
        
        header("Content-type:text/xml");
        // $str = ToXml($msg);
        $xml = new \SimpleXMLElement('<xml></xml>');
        $this->_data2xml($xml, $msg);
        $str = $xml->asXML();
        
        // 记录日志
        addWeixinLog($str, 'replyData');
        if (isset($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
            $sEncryptMsg = ""; // xml格式的密文
            $errCode = $this->wxcpt->EncryptMsg($str, $this->sReqTimeStamp, $this->sReqNonce, $sEncryptMsg);
            if ($errCode == 0) {
                $str = $sEncryptMsg;
            } else {
                addWeixinLog($str, "EncryptMsg Error: " . $errCode);
            }
        }
        echo ($str);
        // exit();
    }

    /* 组装xml数据 */
    public function _data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            is_numeric($key) && ($key = $item);
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->_data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /* ========================发送被动响应消息 end================================== */
    /* 上传多媒体文件 */
    public function uploadFile($file, $type = 'image', $acctoken = '')
    {
        $post_data['type'] = $type; // 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
        $post_data['media'] = $file;
        
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$acctoken&type=image";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }

    /* 下载多媒体文件 */
    public function downloadFile($media_id, $acctoken = '')
    {
        // TODO
    }

    /**
     * GET 请求
     *
     * @param string $url
     */
    private function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     *
     * @param string $url
     * @param array $param
     * @return string content
     */
    private function http_post($url, $param)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = [];
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    // 回复选择素材产生的内容
    function material_reply($param)
    {
//         dump($param);

        $cArr = wp_explode($param, ':');
        isset($cArr[1]) || $cArr[1] = 0;
        if ($cArr[0] == 'text') {
            $config['type'] = 1;
            $config['description'] = M('material_text')->where('id', $cArr[1])->value('content');
        } elseif ($cArr[0] == 'img') {
            $config['type'] = 2;
            $config['image_id'] = $cArr[1];
        } elseif ($cArr[0] == 'news') {
            $config['type'] = 3;
            $config['appmsg_id'] = $cArr[1];
        } elseif ($cArr[0] == 'voice') {
            $config['type'] = 4;
            $config['voice_id'] = $cArr[1];
        } elseif ($cArr[0] == 'video') {
            $config['type'] = 5;
            $config['video_id'] = $cArr[1];
        }
        // 其中wpid和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
        $param = [];
        $param['wpid'] = get_wpid();
        $param['openid'] = get_openid();
        
        $sreach = array(
            '[follow]',
            '[website]',
            '[wpid]',
            '[openid]'
        );
        $replace = array(
            U('weixin/Wap/bind', $param),
            U('wei_site/Wap/index', $param),
            $param['wpid'],
            $param['openid']
        ); // dump($config);
        isset($config['description']) && $config['description'] = str_replace($sreach, $replace, $config['description']);
//         dump($config);
        switch ($config['type']) {
            case '3':
                $map['group_id'] = $config['appmsg_id'];
                $appMsgData = M('material_news')->where(wp_where($map))->select();
                foreach ($appMsgData as $vo) {
                    // 文章内容
                    if ($vo['title']) {
                    	$art=[];
                        $art['Title'] = $vo['title'];
                        $art['Description'] = $vo['intro'];
                        if (empty($vo['url'])) {
                            if (empty($vo['content'])) {
                                $art['Url'] = replace_url($vo['link']);
                            }
                            $public_info = get_pbid_appinfo();
                            if (empty($art['Url'])) {
                                $art['Url'] = U('material/Wap/news_detail', array(
                                    'id' => $vo['id'],
                                    'publicid' => $public_info['id']
                                ));
                            }
                        } else {
                            $art['Url'] = $vo['url'];
                        }
                        
                        if (! config('USER_OAUTH')) {
                            $art['Url'] .= '&openid=' . $param['openid'];
                        }
                        
                        // 获取封面图片URL
                        $art['PicUrl'] = get_cover_url($vo['cover_id']);
                        $articles[] = $art;
                    }
                }
                if (! empty($articles)) {
//                 	addWeixinLog($articles,'autoreplay_'.$config['appmsg_id']);
                    $this->replyNews($articles);
                } else {
                    exit('success');
                }
                break;
            case '2':
                $images = M('material_image')->find($config['image_id']);
                if (! empty($images)) {
                    $media_id = '';
                    if ($images['media_id']) {
                        $media_id = $images['media_id'];
                    } elseif ($images['cover_id']) {
                        $media_id = D('common/Custom')->get_image_media_id($images['cover_id']);
                    }
                    if (empty($media_id)) {
                        exit('success');
                    } else {
                        $res = $this->replyImage($media_id);
                    }
                } else {
                    exit('success');
                }
                break;
            case '4':
                // 语音
                $voice = M('material_file')->find($config['voice_id']);
                if (! empty($voice)) {
                    $media_id = '';
                    if ($voice['media_id']) {
                        $media_id = $voice['media_id'];
                    } elseif ($voice['file_id']) {
                        $media_id = D('common/Custom')->get_file_media_id($voice['file_id']);
                    }
                    if (empty($media_id)) {
                        exit('success');
                    } else {
                        $res = $this->replyVoice($media_id);
                    }
                } else {
                    exit('success');
                }
                break;
            case '5':
                // 视频
                $video = M('material_file')->find($config['video_id']);
                if (! empty($video)) {
                    $media_id = '';
                    
                    if ($video['media_id']) {
                        $media_id = $video['media_id'];
                    } elseif ($video['file_id']) {
                        $media_id = D('common/Custom')->get_file_media_id($video['file_id'], 'video');
                        if (! empty($media_id)) {
                            M('material_file')->where('id', $config['video_id'])->setField('media_id', $media_id);
                        }
                    }
                    if (empty($media_id)) {
                        exit('success');
                    } else {
                        $res = $this->replyVideo($media_id, $video['title'], $video['introduction']);
                    }
                } else {
                    exit('success');
                }
                break;
            default:
//              addWeixinLog($config['description'],'weixinkey222');
                if ($config['description']) {
                    $this->replyText($config['description']);
                } else {
                    exit('success');
                }
        }
    }
}
