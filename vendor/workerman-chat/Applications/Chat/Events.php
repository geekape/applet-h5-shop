<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
// declare(ticks=1);
/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;

// 以POST方式提交数据
function post_data($url, $param, $timeOut = 30)
{
    $param = json_encode($param, JSON_UNESCAPED_UNICODE);
    // 初始化curl
    $ch = curl_init();
    // 设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // 设置header
    $header[] = "Content-Type: application/json; charset=UTF-8";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
    // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    // 要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    $flat = curl_errno($ch);
    $msg = '';
    if ($flat) {
        $msg = curl_error($ch);
    }
    curl_close($ch);
    if ($flat) {
        $res = [
            'curl_erron' => $flat,
            'curl_error' => $msg
        ];
    } else {
        $res = json_decode($res, true);
    }
    
    return $res;
}

class Events
{

    /**
     * 有消息时
     *
     * @param int $client_id            
     * @param mixed $message            
     */
    public static function onMessage($client_id, $message)
    {
        // debug
        echo "client_id:$client_id onMessage:" . $message . "\n";
        
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if (! $message_data) {
            echo 'message_data is null' . "\n";
            return;
        }
        
        $wpid = 'admin_' . $message_data['wpid'];
        // 根据类型执行不同的业务
        switch ($message_data['type']) {
            // 客户端回应服务端的心跳
            case 'pong':
                return;
            // 客户端登录 message格式: {"type":"login","name":"'+name+'","head":"'+head+'","uid":"'+mid+'","wpid":"'+wpid+'"}
            case 'login':
                Gateway::bindUid($client_id, $message_data['uid']);
                
                if ($message_data['uid'] != $wpid) {
                    // 转播给当前公众号客服，xx进入聊天室
                    $message_data['time'] = date('Y-m-d H:i:s');
                    Gateway::sendToUid($wpid, json_encode($message_data));
                    echo "sendToUid:$wpid onMessage:" . json_encode($message_data) . "\n";
                }
                return;
            
            // 客户端发言 message: {"name":"'+name+'", "head":"'+head+'", "to_uid":"'+mid+'", "wpid":"'+wpid+'", "type":"say", "content":"'+content+'"}
            case 'say':
                // 私聊
                $message_data['time'] = date('Y-m-d H:i:s');
                Gateway::sendToUid($message_data['to_uid'], json_encode($message_data));
                
                // 内容保存到数据库并识别
                $param['uid'] = $message_data['uid'];
                $param['to_uid'] = $message_data['to_uid'];
                $param['wpid'] = $message_data['wpid'];
                $param['content'] = $message_data['content'];
                $param['come_from'] = $message_data['come_from'];
                $param['referer'] = $message_data['referer'];
                $param['create_at'] = time();
                $param['is_read'] = 0;
                
                $url = 'http://demo.weiphp.cn/index.php/home/index/chat'; // TODO 上传部署时修改成正式的网址
                $res = post_data($url, $param);
                
                return;
        }
    }

    /**
     * 当客户端断开连接时
     *
     * @param integer $client_id
     *            客户端id
     */
    public static function onClose($client_id)
    {
        // debug
        echo "client_id:$client_id onClose:''\n";
        
        // 从房间的客户端列表中删除
        if (isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            $new_message = array(
                'type' => 'logout',
                'from_client_id' => $client_id,
                'from_client_name' => $_SESSION['client_name'],
                'time' => date('Y-m-d H:i:s')
            );
            Gateway::sendToGroup($room_id, json_encode($new_message));
        }
    }
}
