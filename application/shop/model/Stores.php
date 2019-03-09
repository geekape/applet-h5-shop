<?php
namespace app\shop\model;

use app\common\model\Base;

class Stores extends Base
{

    function getListByIds($ids)
    {
        $lists = [];
        if (! empty($ids)) {
            $idArr = is_array($ids) ? $ids : explode('_', $ids);
            $lists = $this->where('wpid', WPID)
                ->whereIn('id', $idArr)
                ->select();
        }
        
        return $lists;
    }

    function getListByLinkIds($ids)
    {
        $lists = [];
        if (! empty($ids)) {
            $links = D('shop/GoodsStoreLink')->whereIn('id', $ids)->column('id', 'store_id');
            $idArr = array_keys($links);
            $lists = $this->where('wpid', WPID)
                ->whereIn('id', $idArr)
                ->select();
            foreach ($lists as &$v) {
                $v['link_id'] = $links[$v['id']];
            }
        }
        
        return $lists;
    }

    function getList()
    {
        return $this->where('wpid', WPID)->select();
    }

    function getDefaultStore($uid)
    {
        $map['wpid'] = WPID;
        $map['uid'] = $uid;
        $store_id = M('stores_user')->where(wp_where($map))->value('store_id');
        if ($store_id > 0) {
            return $this->where('id', $store_id)->find();
        } else {
            return $this->where('wpid', WPID)->find();
        }
    }

    function setDefault($uid, $store_id)
    {
        M('stores_user')->where('uid', $uid)
            ->where('wpid', WPID)
            ->delete();
        return M('stores_user')->insert([
            'uid' => $uid,
            'store_id' => $store_id,
            'wpid' => WPID
        ]);
    }
}