<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Category extends Base
{

    protected $table = DB_PREFIX . 'shop_goods_category';

    function updateById($id, $data)
    {
        $map['id'] = $id;
        $res = $this->where(wp_where($map))->update($data);
        if ($res) {
            $this->getInfo($id, true);
        }
    }

    private function lists($wpid, $map = [], $w = 90, $h = 70)
    {
        $map['wpid'] = WPID;
        $map['is_show'] = 1;
        $list = $this->where(wp_where($map))
            ->order('sort asc,id asc')
            ->field('id,title,icon,pid,is_recommend')
            ->select();
        foreach ($list as &$vo) {
            $vo['icon'] = get_cover_url($vo['icon'], $w, $h);
        }
        return $list;
    }

    function getShopCategory()
    {
        return $this->lists(WPID, [], 180, 140);
    }

    function getRecommendList()
    {
        $map['is_recommend'] = 1;
        return $this->lists(WPID, $map, 90, 70);
    }

    // 获取父级ID集
    function get_parent_ids($id, &$ids = [])
    {
        $ids[$id] = $map['id'] = $id;
        $info = $this->where(wp_where($map))->find();
        if (! empty($info['pid'])) {
            $ids[$info['pid']] = $info['pid'];
            $this->get_parent_ids($info['pid'], $ids);
        }
    }

    function getCategory()
    { // 获取所有的分类
        $where['is_show'] = 1;
        $res = $this->where(wp_where($where))
            ->order('sort asc,id asc')
            ->select();
        $res = isset($res) ? $res : [];
        foreach ($res as $k => $v) {
            $list['id'] = $v['id'];
            $list['title'] = $v['title'];
            $data[$k] = $list;
        }
        
        return isset($data) ? $data : [];
    }

    // 获取所属分类
    function getCateData($currentId = 0, $is_show_limit = true)
    {
        $is_show_limit && $map['is_show'] = 1;
        $map['wpid'] = get_wpid();
        $list = M('shop_goods_category')->where(wp_where($map))
            ->order('sort asc, id asc')
            ->select();
        $extra = 0 . ':' . "无\r\n";
        foreach ($list as $v) {
            $pid = $v['pid'];
            if ($pid == 0 && $currentId != $v['id']) {
                $extra .= $v['id'] . ':' . $v['title'] . "\r\n";
            }
        }
        return $extra;
    }

    public function getCateDatalists()
    {
        // $map ['is_show'] = 1;
        $map['wpid'] = get_wpid();
        $list = M('shop_goods_category')->where(wp_where($map))
            ->order('sort asc, id asc')
            ->select();
        foreach ($list as $vo) {
            if ($vo['pid'] == 0) {
                $first[] = $vo;
            } else {
                $second[$vo['pid']][] = $vo;
            }
        }
        $data['first'] = isset($first) ? $first : '';
        $data['second'] = isset($second) ? $second : '';
        // dump($data);
        return $data;
    }
}
