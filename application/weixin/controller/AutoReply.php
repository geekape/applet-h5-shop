<?php
namespace app\weixin\controller;

use app\common\controller\WebBase;

class AutoReply extends WebBase
{

    public function initialize()
    {
        parent::initialize();
        $act = strtolower(ACTION_NAME);
        $type = I('type');
        $mdm = input('mdm');
        
        $res['title'] = '自动回复管理';
        $res['url'] = U('weixin/AutoReply/lists', array(
            'mdm' => $mdm
        ));
        $res['class'] = $act == 'video' || $type == 'video' ? 'current' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);
    }

    private function get_materal_info($type, $id)
    {
        $mobj = controller('material/Material');
        $html = '';
        switch ($type) {
            case 'text':
                $text = M('material_text')->where('id', $id)->value('content');
                return $text;
            case 'img':
                $re = $mobj->ajax_picture_by_id($id); // echo $re;exit;
                $re = "<img src='$re' class='list_img'>";
                return $re;
            case 'news': // news
                $re = $mobj->get_news_by_group_id($id);
                if (empty($re)) {
                    return '';
                }
                
                if (count($re) == 1) {
                    $re = $re[0];
                    $html = '<div class="appmsg_item"><h6>' . $re['title'] . '</h6><div class="main_img"><img class="list_img" src="' . $re['img_url'] . '"/></div><p class="desc">' . $re['intro'] . '</p></div>';
                } else {
                    foreach ($re as $vo) {
                        if ($vo['id'] == $id) {
                            $html = '<div class="appmsg_item"><div class="main_img"><img src="' . $vo['img_url'] . '"/><h6 class="ellipsis"">' . $vo['title'] . '</h6></div><p class="desc">' . $vo['intro'] . '</p></div>';
                        } else {
                            $html .= ' <div class="appmsg_sub_item"><p class="title">' . $vo['title'] . '</p><div class="main_img"><img src="' . $vo['img_url'] . '"/></div></div>';
                        }
                    }
                    $html .= '';
                }
                // $html = "<img src='$re' class='list_img'>";
                return $html;
            case 'voice':
                $re = $mobj->ajax_voice_by_id($id); // dump($re);exit;
                if (! empty($re)) {
                    $html = '<div class="picture_item"><div class="sound_item" class="playSound" data-id="' . $id . '"><img class="icon_sound" src="' . __ROOT__ . '/static/base/images/icon_sound.png"><p class="audio_name ellipsis"">' . $re['title'] . '<span class="fr colorless">' . $re['playtime'] . '</span></p><audio id="sound_' . $id . '" src="' . $re['file_path'] . '"></audio></div></div>';
                }
                return $html;
            case 'video':
                $re = $mobj->ajax_video_by_id($id); // echo $re;exit;
                if (! empty($re)) {
                    $html = '<div class="picture_item"><div class="video_item"><p class="title ellipsis"">' . $re['title'] . '</p> <p class="ctime colorless">' . $re['cTime'] . '</p><div class="video_area"><video src="' . $re['file_url'] . '" controls="controls">您的浏览器不支持 video 标签。</video></div><p></p></div></div>';
                }
                return $html;
            default:
                return '';
        }
    }

    public function lists()
    {
        // 获取模型信息
        $title = input('title');
        if (! empty($title)) {
            $map['keyword'] = [
                'like',
                "%{$title}%"
            ];
            session('common_condition', $map);
        }
        $list_data = $this->_get_model_list($this->getModel('auto_reply'), 'id desc', true);
        
        // dump($list_data);
        foreach ($list_data['list_data'] as &$v) {
            $exp = explode(' ', $v['keyword']);
            $v[$v['msg_type'] . '_id'] = $v[$v['msg_type'] . '_id'];
            $v['content'] = $this->get_materal_info($v['msg_type'], $v[$v['msg_type'] . '_id']);
        }
        $this->assign($list_data);
        return $this->fetch();
    }

    public function news()
    {
        $list_data = $this->_get_data('news');
        $this->assign('normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置');
        unset($list_data['list_grids']['content'], $list_data['list_grids']['image_id'], $list_data['list_grids']['video_id'], $list_data['list_grids']['voice_id']);
        
        foreach ($list_data['list_data'] as &$d) {
            $map2['news_id'] = $d['news_id'];
            $titles = M('material_news')->where(wp_where($map2))->column('title');
            $d['news_id'] = implode('<br/>', $titles);
        }
        
        $this->assign($list_data);
        // dump ( $list_data );
        
        return $this->fetch();
    }

    public function image()
    {
        $list_data = $this->_get_data('image');
        $this->assign('normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置');
        unset($list_data['list_grids']['news_id'], $list_data['list_grids']['content'], $list_data['list_grids']['video_id'], $list_data['list_grids']['voice_id']);
        
        foreach ($list_data['list_data'] as &$d) {
            if ($d['image_id']) {
                $d['image_id'] = url_img_html(get_cover_url($d['image_id']));
            } else if ($d['image_material']) {
                $map2['id'] = $d['image_material'];
                $url = M('material_image')->where(wp_where($map2))->value('cover_url');
                $d['image_id'] = url_img_html($url);
            }
        }
        
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch();
    }

    public function voice()
    {
        $list_data = $this->_get_data('voice');
        $this->assign('normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置');
        unset($list_data['list_grids']['news_id'], $list_data['list_grids']['content'], $list_data['list_grids']['image_id'], $list_data['list_grids']['video_id']);
        foreach ($list_data['list_data'] as &$d) {
            $map2['id'] = $d['voice_id'];
            $d['voice_id'] = M('material_file')->where(wp_where($map2))->value('title');
        }
        
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch('voice');
    }

    public function video()
    {
        $list_data = $this->_get_data('video');
        $this->assign('normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置');
        unset($list_data['list_grids']['news_id'], $list_data['list_grids']['content'], $list_data['list_grids']['image_id'], $list_data['list_grids']['voice_id']);
        foreach ($list_data['list_data'] as &$d) {
            $map2['id'] = $d['video_id'];
            $d['video_id'] = M('material_file')->where(wp_where($map2))->value('title');
        }
        
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch('video');
    }

    public function _get_data($type)
    {
        $model = $this->getModel('AutoReply');
        
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        
        // 搜索条件
        $map = $this->_search_map($model, $list_data['db_fields']);
        $map['msg_type'] = $type;
        
        $row = empty($model['list_row']) ? 20 : $model['list_row'];
        
        // 读取模型数据列表
        $name = parse_name($model['name'], true);
        $data = M($name)->field(true)
            ->where(wp_where($map))
            ->order('id desc')
            ->paginate($row);
        $list_data = $this->parsePageData($data, $model, $list_data, false);
        
        $this->assign('add_url', U('add?type=' . $type));
        
        return $list_data;
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $model = $this->getModel('AutoReply');
        $id = I('id') or $this->error('参数错误！');
        
        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        
        $pbid = get_pbid();
        if (is_admin() && (isset($data['pbid']) && $pbid != $data['pbid'])) {
            $this->error('非法访问！');
        }
        
        if (IS_POST) {
            $this->check_keyword(input('keyword'), $id);
            $setkeyword = input('setkeyword');
            $exp_arr = explode(':', $setkeyword);
            ! is_numeric($exp_arr[1]) && $this->error('请选择素材');
            $material_setkeyword_type = input('material_setkeyword_type');
            $data = array(
                'keyword' => input('keyword'),
                "{$material_setkeyword_type}_id" => $exp_arr[1],
                'msg_type' => $material_setkeyword_type,
                'manager_id' => $this->mid
            );
            $res = M('auto_reply')->where('id', $id)->update($data);
            if ($res !== false) {
                $this->success('修改成功！', U('lists'));
            } else {
                $this->error(M('auto_reply')->getError());
            }
        } else {
            
            $data['name'] = 'setkeyword';
            
            $data['value'] = $data['msg_type'] . ':' . $data[$data['msg_type'] . '_id'];
            
            $data['cover_url'] = '';
            $fields[] = array(
                "id" => $id,
                "name" => "keyword",
                'title' => '关键字',
                "keyword" => "视频素材id",
                "field" => "10",
                "type" => "textarea",
                "value" => "",
                "remark" => "多个关键字用空格隔开，如：谢谢   再见（请不要重复添加）",
                "is_show" => 1,
                "is_must" => 1
            );
            // $m_info = $this->get_materal_info($data, $id);
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            return $this->fetch('edit');
        }
    }

    function check_keyword($key, $id = 0)
    {
        $key = trim($key);
        if (empty($key)) {
            $this->error('关键词不能为空');
        }
        $pbid = get_pbid();
        
        $key_arr = array_filter(explode(' ', $key));
        foreach ($key_arr as $k) {
            $res = M('auto_reply')->where('pbid', $pbid)->where("keyword='{$k}' OR keyword like '% {$k} %' OR keyword like '{$k} %' OR keyword like '% {$k}'");
            
            if ($id > 0) {
                $count = $res->where('id', '<>', $id)->count();
            } else {
                $count = $res->count();
            }
            
            if ($count > 0) {
                $this->error('该关键词已存在，请换一个试试');
            }
        }
    }

    // 通用插件的增加模型
    public function add()
    {
        $model = $this->getModel('AutoReply');
        if (IS_POST) {
            $this->check_keyword(input('keyword'));
            $setkeyword = input('setkeyword');
            $exp_arr = explode(':', $setkeyword);
            ! is_numeric($exp_arr[1]) && $this->error('请选择素材');
            $material_setkeyword_type = input('material_setkeyword_type');
            $data = array(
                'keyword' => input('keyword'),
                'pbid' => get_pbid(),
                $material_setkeyword_type . "_id" => $exp_arr[1],
                'msg_type' => $material_setkeyword_type,
                'manager_id' => $this->mid
            );
            
            $res = M('auto_reply')->insert($data);
            if ($res) {
                $this->success('添加关键词' . $model['title'] . '成功！', U('lists'));
            } else {
                $this->error(M('auto_reply')->getError());
            }
        } else {
            $fields[] = array(
                "id" => '',
                "name" => "keyword",
                'title' => '关键字',
                "keyword" => "视频素材id",
                "field" => "",
                "type" => "textarea",
                "value" => "",
                "remark" => "多个关键字用空格隔开，如：谢谢   再见（请不要重复添加）",
                "is_show" => 1,
                "is_must" => 1
            );
            $data = array(
                'name' => 'setkeyword',
                'value' => '',
                'keyword' => '',
                'cover_url' => '',
                'image_material' => ''
            );
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            return $this->fetch('edit');
        }
    }

    public function _deal_fields($fields, $type)
    {
        // dump ( $type );
        switch ($type) {
            case 'news':
                unset($fields['content'], $fields['image_id']);
                break;
            case 'image':
                unset($fields['news_id'], $fields['content']);
                break;
            case 'voice':
                unset($fields['news_id'], $fields['content'], $fields['image_id']);
                break;
            case 'video':
                unset($fields['news_id'], $fields['content'], $fields['image_id']);
                break;
            default:
                unset($fields['news_id'], $fields['image_id']);
        }
        // dump ( $fields );
        return $fields;
    }

    public function checkPostData()
    {
        $type = I('type', 'text');
        if ($type == 'text') {
            $content = I('post.content');
            if (empty($content)) {
                $this->error('文本不能为空');
            }
        } elseif ($type == 'news') {
            $news_id = I('post.news_id');
            if (empty($news_id)) {
                $this->error('图文不能为空');
            }
        } elseif ($type == 'image') {
            $image_material = I('post.image_material');
            $image_id = I('post.image_id');
            if (empty($image_material) && empty($image_id)) {
                $this->error('图片不能为空');
            }
        }
    }
}
