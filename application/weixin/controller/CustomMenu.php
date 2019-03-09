<?php
namespace app\weixin\controller;

use app\common\controller\WebBase;

class CustomMenu extends WebBase
{

    var $model_name = 'custom_menu';

    function initialize()
    {
        parent::initialize();
        $act = strtolower(ACTION_NAME);
        
        $res['title'] = '默认菜单';
        $res['url'] = U('lists');
        $res['class'] = $act == 'lists' ? 'current' : '';
        $nav[] = $res;
        
        if ($act == 'add') {
            $res['title'] = '编辑菜单';
            $res['url'] = '###';
            $res['class'] = 'current';
            $nav[] = $res;
        }
        
        $this->assign('nav', $nav);
    }

    // 删除发布的菜单
    function del_menu()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . get_access_token();
        $res = get_data($url);
        dump(json_decode($res, true));
    }

    public function lists()
    {
        $this->list_content();
        return $this->fetch();
    }

    // 个性化菜单
    function custom_lists()
    {
        // 获取规则列表
        $map['pbid'] = get_pbid();
        $rules = M('custom_menu_rule')->where(wp_where($map))->select();
        // dump($rules);
        $rule_lists = [];
        if (! empty($rules)) {
            $tags = $this->getTagsHask();
            $area = $this->getAreaHask();
            $lang = $this->getLangHask();
            foreach ($rules as $vo) {
                $rule['id'] = $vo['id'];
                $rule['title'] = '';
                if (! empty($vo['tag_id']) && isset($tags[$vo['tag_id']])) {
                    $rule['title'] .= $tags[$vo['tag_id']] . '+';
                }
                if (! empty($vo['sex'])) {
                    $sexName = $vo['sex'] == 1 ? '男' : '女';
                    $rule['title'] .= $sexName . '+';
                }
                if (! empty($vo['os'])) {
                    $osArr = [
                        1 => 'IOS',
                        2 => 'Android',
                        3 => 'Others'
                    ];
                    $rule['title'] .= $osArr[$vo['os']] . '+';
                }
                if (! empty($vo['country']) && isset($area[$vo['country']])) {
                    $rule['title'] .= $area[$vo['country']] . '+';
                }
                if (! empty($vo['province']) && isset($area[$vo['province']])) {
                    $rule['title'] .= $area[$vo['province']] . '+';
                }
                if (! empty($vo['city']) && isset($area[$vo['city']])) {
                    $rule['title'] .= $area[$vo['city']] . '+';
                }
                if (! empty($vo['lang']) && isset($lang[$vo['lang']])) {
                    $rule['title'] .= $lang[$vo['lang']] . '+';
                }
                
                $rule['title'] = rtrim($rule['title'], '+');
                
                $rule_lists[] = $rule;
            }
        }
        // dump($rule_lists);
        $this->assign('rule_lists', $rule_lists);
        
        $rule_id = input('rule_id', 0);
        if (empty($rule_id) && ! empty($rule_lists)) {
            $rule_id = $rule_lists[0]['id'];
        }
        
        $this->list_content($rule_id);
        return $this->fetch();
    }

    private function list_content($rule_id = 0)
    {
        $eventArr = [
            'text' => '文本素材',
            'img' => '图片素材',
            'news' => '图文素材',
            'voice' => '语音素材',
            'video' => '视频素材',
            'click' => '点击推事件 ',
            'scancode_push' => '扫码推事件 ',
            'scancode_waitmsg' => '扫码带提示 ',
            'pic_sysphoto' => '弹出系统拍照发图  ',
            'pic_photo_or_album' => '弹出拍照或者相册发图 ',
            'pic_weixin' => '弹出微信相册发图器 ',
            'location_select' => '弹出地理位置选择器',
            'none' => ''
        ];
        // 搜索条件
        $map['rule_id'] = $rule_id;
        
        $list_data = $this->get_data($map);
        foreach ($list_data as &$vo) {
            $vo['content'] = '';
            if ($vo['from'] == 1) {
                $arr = explode(':', $vo['material']);
                $vo['content'] = isset($eventArr[$arr[0]]) ? $eventArr[$arr[0]] : '';
            } elseif ($vo['from'] == 2) {
                $vo['content'] = $vo['url'];
            } elseif ($vo['from'] == 3) {
                $vo['content'] = $eventArr[$vo['type']] . ': ' . $vo['keyword'];
            } elseif ($vo['from'] == 4) {
                $vo['content'] = '小程序：' . $vo['pagepath'];
            }
        }
        
        $this->assign('list_data', $list_data);
        $this->assign('rule_id', $rule_id);
    }

    // 添加个性化规则
    public function add_custom_tag()
    {
        if (IS_POST) {
            $data['tag_id'] = input('tag_id', 0);
            $data['sex'] = input('sex', 0);
            $data['os'] = input('os', 0);
            $data['lang'] = input('lang', 0);
            
            $data['country'] = 0;
            $data['province'] = 0;
            $data['city'] = 0;
            
            $area = input('area');
            if (! empty($area)) {
                $arr = explode(',', $area);
                isset($arr[0]) && $data['country'] = $arr[0];
                isset($arr[1]) && $data['province'] = $arr[1];
                isset($arr[2]) && $data['city'] = $arr[2];
            }
            $check = false;
            foreach ($data as $vo) {
                if (! empty($vo)) {
                    $check = true;
                    break;
                }
            }
            if ($check == false) {
                $this->error('请至少选择一项内容');
            }
            $data['pbid'] = get_pbid();
            $has = M('custom_menu_rule')->where(wp_where($data))->find();
            if ($has) {
                $this->error('已经存在相同的规则');
            }
            
            $check_map['id'] = input('rule_id', 0);
            if (empty($check_map['id'])) {
                M('custom_menu_rule')->insert($data);
                $this->success('增加成功', U('custom_lists'));
            } else {
                M('custom_menu_rule')->where(wp_where($check_map))->update($data);
                $this->success('保存成功', U('custom_lists'));
            }
        } else {
            $tags = $this->getTagsHask();
            $this->assign('tags', $tags);
            $lang = $this->getLangHask();
            $this->assign('lang', $lang);
            return $this->fetch();
        }
    }

    private function getTagsHask()
    {
        // 微信标签 暂时取用户组的数据
        $map['wechat_group_id'] = [
            'gt',
            0
        ];
        $map['wpid'] = get_wpid();
        $tags = M('auth_group')->where(wp_where($map))->column('title', 'wechat_group_id');
        return $tags;
    }

    private function getAreaHask()
    {
        $area = M('area')->column('title', 'id');
        return $area;
    }

    private function getLangHask()
    {
        $lang = [
            'zh_CN' => '简体中文',
            'zh_TW' => '繁体中文TW',
            'zh_HK' => '繁体中文HK',
            'en' => '英文',
            'id' => '印尼',
            'ms' => '马来',
            'es' => '西班牙',
            'ko' => '韩国',
            'it' => '意大利',
            'ja' => '日本',
            'pl' => '波兰',
            'pt' => '葡萄牙',
            'ru' => '俄国',
            'th' => '泰文',
            'vi' => '越南',
            'ar' => '阿拉伯语',
            'hi' => '北印度',
            'he' => '希伯来',
            'tr' => '土耳其',
            'de' => '德语',
            'fr' => '法语'
        ];
        return $lang;
    }

    private function get_data($map = [])
    {
        $map['pbid'] = get_pbid();
        $list = M('custom_menu')->where(wp_where($map))
            ->order('pid asc, sort asc')
            ->select();
        
        // 取一级菜单
        $one_arr = [];
        foreach ($list as $k => $vo) {
            if ($vo['pid'] != 0) {
                continue;
            }
            
            $one_arr[$vo['id']] = $vo;
            unset($list[$k]);
        }
        $data = [];
        foreach ($one_arr as $p) {
            $data[] = $p;
            
            $two_arr = array();
            foreach ($list as $key => $l) {
                if ($l['pid'] != $p['id']) {
                    continue;
                }
                
                $two_arr[] = $l;
                unset($list[$key]);
            }
            
            $data = array_merge($data, $two_arr);
        }
        
        return $data;
    }

    private function _deal_data($d, $pid = 0)
    {
        $res['name'] = trim(str_replace('├──', '', $d['title']));
        $len = mb_strlen($res['name'], 'UTF-8');
        $max_len = empty($pid) ? 4 : 7;
        if ($len > $max_len) {
            $this->error($res['name'] . '菜单已超过' . $max_len . '个字的限制');
        }
        
        switch ($d['from']) {
            case 0: // 一级无事件
                break;
            case 1: // 素材
                $res['type'] = 'click';
                $res['key'] = 'material::/' . trim($d['material']);
                break;
            case 2: // URL11
                $res['type'] = 'view';
                $res['url'] = $this->replaceUrl($d['url']);
                
                $len = mb_strlen($res['url'], 'UTF-8');
                if ($len > 1024) {
                    $this->error($res['name'] . ' 的URL已超过1024个字节的限制：' . $res['url']);
                }
                break;
            case 3: // 自定义
                $res['type'] = trim($d['type']);
                $res['key'] = trim($d['keyword']);
                
                $len = mb_strlen($res['key'], 'UTF-8');
                if ($len > 128) {
                    $this->error($res['name'] . ' 的关键词已超过128个字节的限制：' . $res['key']);
                }
                break;
            case 4: // 小程序
                $res['type'] = 'miniprogram';
                $res['appid'] = trim($d['appid']);
                $res['pagepath'] = trim($d['pagepath']);
                $res['url'] = $this->replaceUrl($d['appurl']);
                
                $len = mb_strlen($res['url'], 'UTF-8');
                if ($len > 1024) {
                    $this->error($res['name'] . ' 的URL已超过1024个字节的限制：' . $res['url']);
                }
                break;
        }
        
        return $res;
    }

    private function replaceUrl($url)
    {
        $url = trim($url);
        if (empty($url)) {
            return '';
        }
        
        
        $publicid = get_pbid();
        $wpid = get_wpid();
        $search = array(
            '[website]',
            '[publicid]',
            '[wpid]'
        );
        $replace = array(
            SITE_URL,
            $publicid,
            $wpid
        );
        
        return str_replace($search, $replace, $url);
    }

    // 发送菜单到微信
    public function send_menu()
    {
        $access_token = get_access_token();
        
        $map['rule_id'] = input('rule_id', 0);
        $data = $this->get_data($map);
        
        foreach ($data as $k => $d) {
            if ($d['pid'] != 0) {
                continue;
            }
            $treeArr[$d['id']] = $this->_deal_data($d);
            unset($data[$k]);
        }
        foreach ($data as $k => $d) {
            $treeArr[$d['pid']]['sub_button'][] = $this->_deal_data($d,$d['pid']);
            unset($data[$k]);
        }
        $tree['button'] = [];
        foreach ($treeArr as $vo) {
            $tree['button'][] = $vo;
        }
        
        $rule_id = input('rule_id', 0);
        if ($rule_id > 0) {
            $rule = M('custom_menu_rule')->find($rule_id);
            $areaHask = $this->getAreaHask();
            
            $tree['matchrule'] = [];
            if (! empty($rule['tag_id'])) {
                $tree['matchrule']['tag_id'] = $rule['tag_id'];
            }
            if (! empty($rule['sex'])) {
                $tree['matchrule']['sex'] = $rule['sex'];
            }
            if (! empty($rule['os'])) {
                $tree['matchrule']['client_platform_type'] = $rule['os'];
            }
            if (! empty($rule['country']) && isset($areaHask[$rule['country']])) {
                $tree['matchrule']['country'] = $areaHask[$rule['country']];
            }
            if (! empty($rule['province']) && isset($tree['matchrule']['country']) && isset($areaHask[$rule['province']])) {
                $tree['matchrule']['province'] = $areaHask[$rule['province']];
            }
            if (! empty($rule['city']) && isset($tree['matchrule']['province']) && isset($areaHask[$rule['city']])) {
                $tree['matchrule']['city'] = $areaHask[$rule['city']];
            }
            if (! empty($rule['language'])) {
                $tree['matchrule']['language'] = $rule['language'];
            }
            if (empty($tree['matchrule'])) {
                $this->error('个性化规则不能全为空');
            }
            
            $addUrl = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=' . $access_token;
        } else {
            $addUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        }
        
        // button 是 一级菜单数组，个数应为1~3个,二级菜单数组，个数应为1~5个
        $top_count = count($tree['button']);
        // dump($top_count);
        // dump($tree);
        // exit();
        if ($top_count == 0 || $top_count > 3) {
            $this->error('一级菜单数组，个数应为1~3个');
        }
        foreach ($tree['button'] as $vo) {
            $sub_count = isset($vo['sub_button']) ? count($vo['sub_button']) : 0;
            if (count($vo) < 3 && ($sub_count == 0 || $sub_count > 5)) {
                $this->error($vo['name'] . '的二级菜单数组，个数应为1~5个');
            }
        }
        
        $res = post_data($addUrl, $tree);
        if (! isset($res['errcode']) || $res['errcode'] == 0) {
            if ($rule_id > 0) {
                M('custom_menu_rule')->where('id', $rule_id)->setField('menuid', $res['menuid']);
            }
            $this->success('发送菜单成功');
        } else {
            $this->error(error_msg($res));
        }
    }

    private function check_count($data, $count)
    {
        if ($data['pid'] == 0) { // 最多 3 个一级菜单
            if ($count >= 3) {
                $this->error('最多 3 个一级菜单');
            }
        } else { // 最多 5 个二级菜单
            if ($count >= 5) {
                $this->error('该一级菜单下最多 5 个二级菜单');
            }
        }
    }

    public function add()
    {
        if (IS_POST) {
            $data = input('post.');
            if (empty($data['title'])) {
                $this->error('菜单名不能为空');
            }
            
            // 一级菜单最多4个汉字，二级菜单最多7个汉字
            $len = mb_strlen($data['title'], 'UTF-8');
            $max_len = empty($data['pid']) ? 4 : 7;
            if ($len > $max_len) {
                $this->error('菜单名已超过' . $max_len . '个字的限制');
            }
            
            if ($data['from'] == 2 && empty($data['url'])) {
                $this->error('请先填写URL地址');
            }
            if ($data['from'] == 1 && (empty($data['material']) || $data['material'] == 'text:')) {
                $this->error('请先选择素材');
            }
            
            $count = M('custom_menu')->where('pbid', PBID)
                ->where('pid', $data['pid'])
                ->count();
            // dump($count);
            
            unset($data["material_material_type"], $data["material_material_text_id"], $data["material_material_news_id"], $data["material_material_img_id"], $data["material_material_voice_id"], $data["material_material_video_id"]);
            if (isset($data['id']) && ! empty($data['id'])) {
                $pid = M('custom_menu')->where('id', $data['id'])->value('pid');
                if ($pid != $data['pid']) {
                    $this->check_count($data, $count);
                }
                
                $map['id'] = $data['id'];
                $res = M('custom_menu')->where(wp_where($map))->update($data);
            } else {
                $this->check_count($data, $count);
                $data['pbid'] = get_pbid();
                
                $res = M('custom_menu')->insertGetId($data);
            }
            if ($res !== false) {
                // 重置一级菜单
                if ($data['pid'] > 0) {
                    $pmap['id'] = $data['pid'];
                    $from = M('custom_menu')->where(wp_where($pmap))->value('from');
                    if ($from != 0) {
                        M('custom_menu')->where(wp_where($pmap))->setField('from', 0);
                    }
                }
                
                $url = empty($data['rule_id']) ? U('lists') : U('custom_lists', [
                    'rule_id' => $data['rule_id']
                ]);
                $this->success('保存菜单成功！', $url);
            } else {
                $this->error('保存菜单失败');
            }
        } else {
            // 获取一级菜单
            $map['pbid'] = get_pbid();
            $map['pid'] = 0;
            $map['rule_id'] = input('rule_id', 0);
            $list = M('custom_menu')->where(wp_where($map))
                ->field('id, title')
                ->select();
            $this->assign('pList', $list);
            
            $this->assign('normal_tips', '可创建最多 3 个一级菜单，每个一级菜单下可创建最多 5 个二级菜单。编辑中的菜单不会马上被用户看到，请放心调试');
            
            $data = [];
            $menu_map['id'] = input('id', 0);
            if (! empty($menu_map['id'])) {
                $data = M('custom_menu')->where(wp_where($menu_map))->find();
            }
            // dump($data);
            $this->assign('data', $data);
            
            return $this->fetch();
        }
    }

    public function del()
    {
        $model = $this->getModel($this->model_name);
        return parent::common_del($model);
    }

    public function delRule()
    {
        $map['id'] = input('id');
        $map['pbid'] = get_pbid();
        $info = M('custom_menu_rule')->where(wp_where($map))->find();
        if (! $info) {
            $this->error('删除失败，可能权限不足');
        }
        
        $res = M('custom_menu_rule')->where(wp_where($map))->delete();
        if ($res && ! empty($info['menuid'])) {
            $delUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=' . get_access_token();
            $param['menuid'] = $info['menuid'];
            post_data($delUrl, $param);
        }
        $this->success('删除成功');
    }

    function get_menu()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . get_access_token();
        $content = wp_file_get_contents($url);
    }
}
