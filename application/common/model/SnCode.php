<?php
namespace app\common\model;

use app\common\model\Base;

/**
 * SnCode模型
 */
class SnCode extends Base
{

    protected $table = DB_PREFIX . 'sn_code';

    public function getInfoById($id, $field = '', $update = false, $data = [])
    {
        $key = cache_key('id:' . $id, $this->table);
        $info = S($key);
        if ($info === false || $update) {
            if (empty($data)) {
                $info = $this->findById($id);
            } else {
                $info = $data;
            }
            if (! empty($info)) {
                $this->clearCache($info['target_id'], $info['uid']);
            }
            S($key, $info, 86400);
        }
        
        return empty($field) ? $info : $info[$field];
    }

    public function delayAdd($data = [], $delay = 10)
    {
        if (empty($data)) {
            return true;
        }
        
        $data['server_addr'] = get_server_ip();
        $id = $this->insertGetId($data);
        
        // 更新相关缓存
        $this->getCollectCount($data['target_id'], true);
        $this->getMyList($data['uid'], $data['target_id'], true);
        $this->getMyAll($data['uid'], true);
        D('Coupon/Coupon')->updateCollectCount($data['target_id'], true);
        
        return $id;
    }

    // 延时插入，也thinkphp的延时插入不同，它同时需要更新相关的缓存，保证数据的实时性
    public function delayAdd_bar($data = [], $delay = 10)
    {
        $key_time = 'SnCode_delayAdd_time';
        $time = S($key_time);
        
        // $key_id = 'SnCode_delayAdd_lastID';
        // $lastID = intval ( S ( $key_id ) );
        // if ($lastID == 0) {
        // $lastID = $this->value( 'max(id)' );
        // }
        
        $key_data = 'SnCode_delayAdd_data';
        $dataArr = S($key_data);
        $dataArr === false && $dataArr = [];
        
        // 非插入时把缓存数据写入数据库，解决最后一批缓存数据无法写入数据库的问题
        if (empty($data)) {
            if (! empty($dataArr)) {
                foreach ($dataArr as $k => $v) {
                    unset($dataArr[$k]['id']);
                }
                $this->insertAll($dataArr);
                
                S($key_time, NOW_TIME);
                S($key_data, []);
                // S ( $key_id, null );
                
                // 更新相关缓存
                foreach ($dataArr as $d) {
                    $this->getCollectCount($d['target_id'], true);
                    $this->getMyList($d['uid'], $d['target_id'], true);
                    $this->getMyAll($d['uid'], true);
                    D('Coupon/Coupon')->updateCollectCount($d['target_id'], true);
                }
            }
            return true;
        }
        
        // $lastID += 1;
        // $data ['id'] = $lastID;
        $data['server_addr'] = get_server_ip();
        $dataArr[] = $data;
        if (NOW_TIME > $time + $delay) {
            // 延时更新时间到了，删除缓存数据 并实际写入数据库
            $this->insertAll($dataArr);
            
            S($key_time, NOW_TIME);
            S($key_data, []);
            
            // 更新相关缓存
            $this->getCollectCount($data['target_id'], true);
            $this->getMyList($data['uid'], $data['target_id'], true);
            $this->getMyAll($d['uid'], true);
            D('Coupon/Coupon')->updateCollectCount($data['target_id'], true);
        } else {
            // 追加数据到缓存
            S($key_data, $dataArr);
            
            // 更新相关缓存
            $this->getCollectCount($data['target_id'], false, true);
            $this->getMyList($data['uid'], $data['target_id'], false, $data['id']);
            $this->getMyAll($d['uid'], false, $data['id']);
        }
        // S ( $key_id, $lastID );
        
        return true;
    }

    public function getCollectCount($target_id, $update = false, $cache_update = false)
    {
        $map['target_id'] = $target_id;
        $key = cache_key($map, $this->table, 'id');
        $count = S($key);
        if ($cache_update) {
            $count += 1;
            S($key, $count);
        } else if ($count === false || $update) {
            $count = $this->where(wp_where($map))->count();
            S($key, $count);
        }
        return intval($count);
    }

    public function getMyList($uid, $target_id = '', $update = true, $cache_id = '')
    {
        $map['uid'] = $uid;
        $map['target_id'] = $target_id;
        $key = cache_key($map, $this->table, 'id');
        $ids = S($key);
        
        if (! empty($cache_id)) {
            $ids === false && $ids = [];
            array_unshift($ids, $cache_id);
            S($key, $ids, 86400);
        } else if ($ids === false || $update) {
            $ids = $this->where(wp_where($map))->column('id');
            S($key, $ids, 86400);
        }
        
        foreach ($ids as $id) {
            $list[] = $this->getInfoById($id);
        }
        $list = isset($list) ? $list : [];
        return $list;
    }

    public function getMyAll($uid, $update = false, $cache_id = '', $can_use = '')
    {
    	if (empty($uid)){
    		return [];
    	}
        $map['uid'] = $uid;
        if ($can_use !== '') {
            $map['can_use'] = $can_use;
        }
        $key = cache_key($map, $this->table, 'id');
        $ids = S($key);
        
        if (! empty($cache_id)) {
            $ids === false && $ids = [];
            array_unshift($ids, $cache_id);
            S($key, $ids, 86400);
        } else if ($ids == false || $update) {
            $ids = $this->where(wp_where($map))
                ->order('is_use asc, id desc')
                ->column('id');
            S($key, $ids, 86400);
        }
        if (empty($ids))
            return [];
        
        foreach ($ids as $id) {
            $list[] = $this->getInfoById($id);
        }
        
        $list = isset($list) ? $list : [];
        return $list;
    }

    public function updateInfo($id, $save = [])
    {
        $map['id'] = $id;
        $res = $this->save($save, $map);
        $this->getInfoById($id, '', true);
        return $res;
    }

    public function set_use($id)
    {
        $data = $this->getInfoById($id);
        
        if (! $data) {
            return - 1;
        }
        
        if ($data['is_use']) {
            $data['is_use'] = 0;
            $data['use_time'] = '';
        } else {
            $data['is_use'] = 1;
            $data['use_time'] = time();
            $data['can_use'] = 0;
            $data['admin_uid'] = $GLOBALS['mid'];
        }
        
        $res = $this->updateInfo($id, $data);
        
        return $res;
    }

    // 清缓存
    function clear($target_id = 0, $uid = 0)
    {
        $uid = $uid <= 0 ? get_mid() : $uid;
        $this->getCollectCount($target_id, true);
        $this->getMyList($uid, $target_id, true);
        $this->getMyAll($uid, true);
    }
}
