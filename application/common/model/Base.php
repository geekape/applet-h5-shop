<?php
namespace app\common\model;

use think\Model;

/**
 * 公众号配置操作集成
 */
class Base extends Model
{

    // 当前登录的用户ID
    protected $mid = 0;

    // 模型是否启用缓存，只有设置为true时getInfo等通用的方法才会使用缓存
    protected $openCache = false;

    // getInfo方法中允许缓存的字段，为空时表示缓存全部字段
    protected $cacheFiledAllow = '';

    // getInfo方法中禁止缓存的字段，为空时表示缓存全部字段，在$cacheFiledAllow有值的情况会自动忽略此配置
    protected $cacheFiledFobit = '';

    function initialize()
    {
        parent::initialize();
    }

    function setMid($mid)
    {
        $this->mid = intval($mid);
    }

    function error($msg)
    {
        $data['code'] = 0;
        $data['msg'] = $msg;
        return $data;
    }

    function success($msg, $url = '')
    {
        $data['code'] = 1;
        $data['msg'] = $msg;
        $data['url'] = $url;
        return $data;
    }

    function findById($id, $field = [], $allow = true)
    {
        $Obj = $this->where('id', $id);
        if (empty($field)) {
            $infoObj = $Obj->find();
        } else {
            if ($allow) {
                $infoObj = $Obj->field($field)->find();
            } else {
                $infoObj = $Obj->field($field, true)->find();
            }
        }
        
        if (! empty($infoObj)) {
            $info = $infoObj->toArray();
        } else {
            $info = [];
        }
        return $info;
    }

    public function getInfo($id, $update = false, $data = [])
    {
        if (empty($id)) {
            return [];
        }
        
        $field = '';
        $allow = true;
        if (! empty($this->cacheFiledAllow)) {
            $field = wp_explode($this->cacheFiledAllow);
        } elseif (! empty($this->cacheFiledFobit)) {
            $allow = false;
            $field = wp_explode($this->cacheFiledFobit);
        }
        
        if (! $this->openCache) {
            $info = $this->findById($id, $field, $allow);
        } else {
            $key = cache_key('id:' . $id, $this->name);
            $info = S($key);
            if ($info === false || $update || ! empty($data)) {
                if (empty($data)) {
                    $info = $this->findById($id, $field, $allow);
                } else {
                    $info = $data;
                }
                S($key, $info);
            }
        }
        return $info;
    }

    /*
     * clearCache是model里统一清缓存的方法，建议开发者自己重写它，实现自己model里的缓存更新机制
     * 以下的方式只实现了清空本类里的getInfo里的缓存，更多的业务缓存需要开发在自己的model里实现
     *
     * @param act_type string 操作的类型，如 add edit delete等 主要是方便重写此方法时可以传入更多业务参数
     * @param uid string 要更新的用户ID，主要是方便重写此方法时可以传入更多业务参数
     * @param more_param array 主要是方便重写此方法时可以传入更多业务参数
     */
    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
        if (! $this->openCache) {
            return true;
        }
        if (is_array ( $id )) {
			foreach ( $id as $ii ) {
				$this->getInfo ( $ii, true );
			}
			return true;
		} else {
			return $this->getInfo ( $id, true );
		}
    }

    public function getFieldByInfo($id, $filed = '', $update = false, $data = [])
    {
        $info = $this->getInfo($id, $update, $data);
        if (empty($info)) {
            return empty($filed) ? [] : '';
        } else {
            return empty($filed) ? $info : $info[$filed];
        }
    }

    public function updateId($id, $save)
    {
        $res = $this->where('id', $id)->update($save);
        $info = $this->getInfo($id, true);
        return $res;
    }

    /**
     * 查询数据集
     *
     * @access public
     * @param array $options
     *            表达式参数
     * @return mixed
     */
    public function selectPage($row = 20, $count = false)
    {
        if ($count === false) {
            $list_data['list_data'] = $this->paginate($row);
        } else {
            $list_data['list_data'] = $this->paginate($row, $count);
        }
        
        $list_data['_page'] = $this->render();
        return $list_data;
    }

    public function subscribeAndCardCheck($is_subscribe, $is_member, $event_name, $event_id)
    {
        $info['need_subscribe'] = $info['need_card_member'] = 0;
        $info['public_name'] = $info['qrcode'] = '';
        if ($is_subscribe == 1) {
            $has_subscribe = D('common/Follow')->where('uid', intval(session('mid_'.get_pbid())))
                ->where('wpid', WPID)
                ->value('has_subscribe');
            
            if ($has_subscribe != 1) { // 未关注
                $info['need_subscribe'] = 1;
                $info['qrcode'] = D('home/QrCode')->add_qr_code('QR_SCENE', $event_name, $event_id);
                $info['public_name'] = D('Publics')->where('id', PBID)->value('public_name');
            }
        }
        if ($is_member == 1 && is_install('card')) {
            $card_id = M('card_member')->where('uid', intval(session('mid_'.get_pbid())))
                ->where('wpid', WPID)
                ->value('id');
            if (! ($card_id > 0)) { // 非会员
                $info['need_card_member'] = 1;
            }
        }
        
        return $info;
    }
    function wx_config(){
        $pbid = get_pbid();
        $info = get_pbid_appinfo($pbid);
        $url = input('url');

        require_once env('vendor_path') . 'jssdk/jssdk.php';
        $jssdk = new \JSSDK($info['appid'], $info['secret']);
        $jsapiParams = $jssdk->GetsignPackage($url);

        return $jsapiParams;
    }
}

?>
