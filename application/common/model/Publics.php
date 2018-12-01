<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * 公众号配置操作集成
 */
class Publics extends Base
{

    protected $table = DB_PREFIX . 'publics';
    protected $openCache = true;

    public function getInfoById($pbid, $filed = '', $update = false)
    {
        return $this->getFieldByInfo($pbid, $filed, $update);
    }

    public function getInfoByAppid($appid, $filed = '', $update = false)
    {
		$key = cache_key('', $this->table, 'id,appid');
        $arr = S($key);
        if ($arr === false || ! isset($arr[$appid]) || $update) {
            $arr = $this->column('id','appid');
            
            S($key, $arr, 604800); // 缓存一周
        }
        if (isset($arr[$appid])) {
            return $this->getFieldByInfo($arr[$appid], $filed, $update);
        } else {
            return empty($filed) ? [] : '';
        }
    }

    public function get_pbid_by_token($token, $update = false)
    {
		$key = cache_key('public_id:'.$token, $this->table, 'id');
        $pbid = S($key);
        if ($pbid === false || $update) {
            $pbid = $this->where('public_id', $token)->value('id');
            
            S($key, $pbid, 604800); // 缓存一周
        }
        return $pbid;
    }

    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
        $info = $this->getInfo($id, '', true);
        $this->getInfoById($id, '', true);
    }
    function updateInfo($id, $save)
    {
        $res = $this->where('id', $id)->update($save);
        if ($res !== false) {
            $this->clearCache($id);
        }
        
        return $res;
    }

    function updateRefreshToken($appid, $refresh_token)
    {
        $info = $this->where('appid', $appid)
            ->field('id')
            ->find();
        if (! $info) {
            return false;
        }
        
        $save['authorizer_refresh_token'] = $refresh_token;
        $res = $this->where('appid', $appid)->update($save);
        if ($res!==false) {
            $this->clearCache($info['id']);
        }
    }
}
