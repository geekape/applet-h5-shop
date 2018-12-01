<?php

namespace app\material\controller;

use getID3\getid3;
use think\facade\Config;
use think\facade\Env;

/**
 * 素材管理控制器
 */
class Material extends \app\home\controller\Home
{

    private $mdm;

    public function initialize()
    {
        parent::initialize();

		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}

        $act = strtolower(ACTION_NAME);
        $this->mdm = I('mdm');
        $param = array(
            'mdm' => $this->mdm
        );
        $res['title'] = '图文素材';
        $res['url'] = U('material_lists', $param);
        $res['class'] = $act == 'material_lists' ? 'current' : '';
        $nav[] = $res;

        $res['title'] = '图片素材';
        $res['url'] = U('picture_lists', $param);
        $res['class'] = strpos($act, 'picture') !== false ? 'current' : '';
        $nav[] = $res;

        $res['title'] = '语音素材';
        $res['url'] = U('voice_lists', $param);
        $res['class'] = strpos($act, 'voice') !== false ? 'current' : '';
        $nav[] = $res;

        $res['title'] = '视频素材';
        $res['url'] = U('video_lists', $param);
        $res['class'] = strpos($act, 'video') !== false ? 'current' : '';
        $nav[] = $res;

        $res['title'] = '文本素材';
        $res['url'] = U('text_lists', $param);
        $res['class'] = strpos($act, 'text') !== false ? 'current' : '';
        $nav[] = $res;

        $this->assign('nav', $nav);
    }

    public function lists()
    {
        return redirect(U('material_lists', array(
            'mdm' => $this->mdm
        )));
    }

    public function doAdd()
    {
        $textArr = array(
            1 => '一',
            2 => '二',
            3 => '三',
            4 => '四',
            5 => '五',
            6 => '六',
            7 => '七',
            8 => '八',
            9 => '九',
            10 => '十'
        );
        $data = json_decode(input('post.dataStr'), true);
        $ids = [];
        $group_id = I('group_id/d', 0);
        foreach ($data as $key => $vo) {
            $save = [];
            foreach ($vo as $k => $v) {
                $save[$v['name']] = safe($v['value']);
            }
            if (empty($save['title'])) {
                $this->error('请填写第' . $textArr[$key + 1] . '篇文章的标题');
            }
            if (empty($save['cover_id'])) {
                $this->error('请上传第' . $textArr[$key + 1] . '篇文章的封面图片');
            }
            if (!empty($save['id'])) {
                // 更新数据
                $map2['id'] = $save['id'];
                M('material_news')->where(wp_where($map2))->update($save);
            } else {
                // 新增加
                $save['cTime'] = NOW_TIME;
                $save['manager_id'] = $this->mid;
                $save['pbid'] = get_pbid();
                $id = M('material_news')->insertGetId($save);
                if ($id) {
                    $ids[] = $id;
                } else {
                    if (!empty($ids)) {
                        $map['id'] = array(
                            'in',
                            $ids
                        );
                        M('material_news')->where(wp_where($map))->delete();
                    }
                    $this->error('增加第' . $textArr[$key + 1] . '篇文章失败，请检查数据后重试');
                }
            }
        }
        if (!empty($ids)) {
            $map['id'] = array(
                'in',
                $ids
            );
            empty($group_id) && $group_id = $ids[0];
            M('material_news')->where(wp_where($map))->setField('group_id', $group_id);
        }
        // 如果有权限，则静默同步到微信，（用户自己手动上传）
//         $this->_syc_news($group_id, true);

        $this->success('操作成功', U('material_lists'));
    }

    public function material_lists()
    {
        $page = I('p', 1, 'intval');
        $row = 20;
        $search_url = U('material_lists', array(
            'mdm' => $this->mdm
        ));
        $this->assign('search_url', $search_url);

        $map['pbid'] = get_pbid();
        $where = "`pbid` = '" . $map['pbid'] . "'";

        $title = I('title');
        // dump($title);exit;
        if (!empty($title)) {
            $map['title'] = array(
                'like',
                "%$title%"
            );
            $where .= " and `title` like '%$title%'";
        }
        $count = M()->query("SELECT COUNT( distinct `group_id`) AS tp_count FROM `wp_material_news` WHERE {$where} LIMIT 1");
        $count = isset($count[0]['tp_count']) ? $count[0]['tp_count'] : 0;

        $field = 'id,title,cover_id,intro,group_id,cTime';
        $data = M('material_news')->where(wp_where($map))
            ->field($field . ',count(id) as count')
            ->group('group_id')
            ->order('cTime desc, group_id desc')
            ->paginate($row, $count);
        $list = dealPage($data);
        
        foreach ($list['list_data'] as &$vo) {
        	if (empty($vo['group_id'])){
				$vo ['group_id'] = $vo ['id'];
				M('material_news')->where('id',$vo['id'])->setField('group_id',  $vo ['id']);
        	}
            if ($vo['count'] == 1) {
                continue;
            }

            $map2['group_id'] = $vo['group_id'];
            $child_list = M('material_news')->field($field)
                ->order('id asc')
                ->where(wp_where($map2))
                ->select();

            $vo = array_merge($vo, $child_list[0]);
            unset($child_list[0]);
            $vo['child'] = $child_list;
        }
        /* 查询记录总数 */

        // 分页
        /*
         * if ($count > $row) {
         * $page = new \Think\Page ( $count, $row );
         * $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
         * $this->assign ( '_page', $page->show () );
         * }
         */
        $this->assign($list);
        $this->assign('add_url', U('add_material', array(
            'mdm' => $this->mdm
        )));
        return $this->fetch();
    }

    public function add_material()
    {
        $map['group_id'] = I('group_id/d', 0);
        if (!empty($map['group_id'])) {
            $list = M('material_news')->where(wp_where($map))
                ->order('id asc')
                ->select();
            $count = count($list);
            $main = $list[0];
            unset($list[0]);
            $others = [];
            if (!empty($list)) {
                $others = $list;
            }
            $this->assign('main', $main);
            $this->assign('others', $others);
        }

        $this->assign('post_url', U('doAdd', $map));
        return $this->fetch();
    }

    public function del_material_by_id()
    {
        $map['id'] = I('id');
        echo M('material_news')->where(wp_where($map))->delete();
    }

    public function del_material_by_groupid()
    {
        $map['group_id'] = I('group_id');
        $map['pbid'] = get_pbid();
        $media_id = M('material_news')->where(wp_where($map))->value('media_id');
        $res = M('material_news')->where(wp_where($map))->delete();
        if ($res) {
            $this->_del_syc_news($media_id);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function _del_syc_news($media_id)
    {
        // 静默删除远程素材
        if (public_interface('material')) {
            $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=' . get_access_token();
            $param['media_id'] = $media_id;
            post_data($url, $param);
        }
    }

    public function material_data()
    {
        $page = I('p', 1, 'intval');
        $row = 10;

        $map['pbid'] = get_pbid();

        $field = 'id,title,cover_id,intro,group_id,cTime';
        $data = M('material_news')->where(wp_where($map))
            ->field($field . ',count(id) as count')
            ->group('group_id')
            ->order('cTime DESC, group_id desc')
            ->paginate($row);
        $list_data = $this->parsePageData($data, [], [], false);

        foreach ($list_data['list_data'] as &$vo) {
            if ($vo['count'] == 1) {
                continue;
            }

            $map2['group_id'] = $vo['group_id'];
            $map2['id'] = array(
                'exp',
                '!=' . $vo['id']
            );

            $vo['child'] = M('material_news')->field($field)
                ->where(wp_where($map2))
                ->select();
        }
        $this->assign($list_data);
        // 弹框数据
        return $this->fetch();
    }

    function material_data_choice()
    {
        $map['pbid'] = get_pbid();
        $title = I('title', '');
        if ($title != '') {
            $map['title'] = array('like', '%' . $title . '%');
        }
        $field = 'id,title,cover_id,intro,group_id,cTime';
        $data = M('material_news')->where(wp_where($map))
            ->whereColumn('id', '=', 'group_id')
            ->field($field)
            ->order('cTime DESC, id desc')
            ->select();
        $this->assign('list_data', $data);
        // 弹框数据
        return $this->fetch();
    }

    public function get_news_by_group_id($group_id = '')
    {
        if ($group_id == '') {
            $group_id = $isAjax = I('group_id');
        }
        $map['group_id'] = $group_id;

        $map['pbid'] = get_pbid();
        $appMsgData = M('material_news')->where(wp_where($map))->select();
        if (empty($appMsgData)) {
            return '';
        }
        foreach ($appMsgData as $vo) {
            if ($vo['id'] == $map['group_id']) {
                $articles[] = array(
                    'id' => $vo['id'],
                    'title' => $vo['title'],
                    'intro' => empty($vo['description']) ? '' : $vo['description'],
                    'img_url' => get_cover_url($vo['cover_id'])
                );
            } else {
                // 文章内容
                $art['id'] = $vo['id'];
                $art['title'] = $vo['title'];
                $art['intro'] = empty($vo['description']) ? '' : $vo['description'];
                $art['img_url'] = get_cover_url($vo['cover_id']);
                $articles[] = $art;
            }
        }
        if (isset($isAjax) && $isAjax) {
            $this->ajaxReturn($articles);
        } else {
            return $articles;
        }
    }

    public function jump($url = '', $msg = '')
    {
        if ($url == '') {
            $url = input('url');
        }
        if ($msg == '') {
            $msg = input('msg');
        }
        $this->assign('url', $url);
        $this->assign('msg', $msg);
        return $this->fetch('common@base/loading');
        exit();
    }

    // 与微信同步
    public function syc_news_to_wechat()
    {
        // 上传本地素材
        $group_id = I('group_id/d', 0);
        if ($group_id > 0) {
            $map['group_id'] = array(
                'lt',
                $group_id
            );
        }
        $map['pbid'] = get_pbid();
        $where = 'group_id>0 and cTime!=update_time';

        $field = 'group_id';
        $list = M('material_news')->limit(1)
            ->where(wp_where($where))
            ->where(wp_where($map))
            ->field($field . ',count(id) as count')
            ->group('group_id')
            ->order('group_id desc')
            ->select();

        if (empty($list)) {
            $url = U('material_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传素材完成');
        }
        foreach ($list as $art) {
            $group_id = $art['group_id'];
            $this->_syc_news($group_id);
        }
        $url = U('syc_news_to_wechat', array(
            'group_id' => $group_id,
            'mdm' => input('mdm')
        )); // echo U('jump',['url'=>$url,'msg' => '上传本地素材到微信中，请勿关闭' ]);exit;
        // return $this->jump($url,'上传本地素材到微信中，请勿关闭');
        return $this->jump($url, '上传本地素材到微信中，请勿关闭');
    }

    /**
     * 选择图文同步
     * @return mixed|string
     */
    public function choice_news_to_wechat()
    {

        $paramData = input('data', '');
        $isJump = input('is_jump', 0);
        if ($isJump == 1) {
            $url = U('choice_news_to_wechat', array(
                'data' => $paramData,
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传本地素材到微信中，请勿关闭');
        }
        $data = json_decode($paramData, true);
        // 上传本地素材
        if (empty($data)) {
            $url = U('material_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '没有选择可上传的素材!');
        }
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        $count = 1;
        foreach ($data as $key => $art) {
            $group_id = $art['id'];
            addWeixinLog($group_id, 'choice_news_to_wechat');
            $this->_syc_news($group_id);
            unset($data[$key]);
            if ($count >= 10) {
                break;
            }
        }
        if (!empty($data)) {
            $data = json_encode($data);
            $url = U('choice_news_to_wechat', array(
                'data' => $data,
                'mdm' => input('mdm')
            ));
        } else {
            $url = U('material_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传素材完成');
        }
        // echo U('jump',['url'=>$url,'msg' => '上传本地素材到微信中，请勿关闭' ]);exit;
        // return $this->jump($url,'上传本地素材到微信中，请勿关闭');
        return $this->jump($url, '上传本地素材到微信中，请勿关闭');
    }

    public function _syc_news($group_id, $show_error = true)
    {
        if (!public_interface('material') || empty($group_id)) {
            return false;
        }

        $field = 'id,title,cover_id,link,intro,author,content,group_id,thumb_media_id,media_id,update_time';
        $map['group_id'] = $group_id;
        $list = M('material_news')->where(wp_where($map))
            ->order('id asc')
            ->field($field)
            ->select();

        if (empty($list)) {
            return false;
        }

        $get_param['media_id'] = $media_id = $list[0]['media_id'];
        foreach ($list as $vo) {
            $data['title'] = $vo['title'];
            $data['thumb_media_id'] = empty($vo['thumb_media_id']) ? D('Material')->_thumb_media_id($vo['cover_id']) : $vo['thumb_media_id'];
            $data['author'] = $vo['author'];
            $data['digest'] = getShort($vo['intro'], 60);
            $data['show_cover_pic'] = 0;
            $data['content_source_url'] = !empty($vo['link']) ? $vo['link'] : U('material/Wap/news_detail', array(
                'id' => $vo['id']
            ));
            $vo['content'] = $this->getNewContent($vo['content']);
            $data['content'] = str_replace('"', '\'', $vo['content']);

            empty($data['content']) && $data['content'] = $data['content_source_url'];
            !empty($media_id) && $data['media_id'] = $media_id;
            $news[] = $data;
        }
        $update_url = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=' . get_access_token();
        $add_url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . get_access_token();

        $need_del = false;
        if (!empty($media_id)) {
            // 多图文下有增加或者删除图文时无法直接更新，要先删除线上的再重新上传
            $get_url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
            $info = post_data($get_url, $get_param);
            if (isset($info['errcode']) && $info['errcode'] != 0) {
                $media_id = ''; // 发生错误，可能是远程的素材被删除了
            } else {
                $need_del = count($info['news_item']) != count($news);
            }
        }
        if (!empty($media_id) && !$need_del) {
            // 更新图文素材
            foreach ($news as $index => $vo) {
                $param['media_id'] = $media_id;
                $param['index'] = $index;
                unset($vo['media_id']);

                $param['articles'] = $vo;
                $res = post_data($update_url, $param);
                if (isset($res['errcode']) && $res['errcode'] != 0) {
                    if ($show_error) {
                        $this->error(error_msg($res));
                    } else {
                        return false;
                    }
                }
            }
        } else {
            // 添加图文素材
            $param['articles'] = $news;
            $res = post_data($add_url, $param);
            if (isset($res['errcode']) && $res['errcode'] != 0) {
                if ($show_error) {
                    $this->error(error_msg($res));
                } else {
                    return false;
                }
            } else {
                if ($need_del) {
                    $this->_del_syc_news($media_id);
                }
                $map3['group_id'] = $group_id;
                M('material_news')->where(wp_where($map3))->setField('media_id', $res['media_id']);
                $newsUrl = $this->_news_url($res['media_id']);
                foreach ($news as $a) {
                    $map4['group_id'] = $group_id;
                    $map4['title'] = $a['title'];
                    M('material_news')->where(wp_where($map4))->setField('url', $newsUrl[$a['title']]);
                }
            }
        }
        return true;
    }

    // 获取图文素材url
    public function _news_url($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
        $param['media_id'] = $media_id;
        $news = $this->post_data($url, $param);

        foreach ($news['news_item'] as $vo) {
            $newsUrl[$vo['title']] = $vo['url'];
        }
        return $newsUrl;
    }

    public function syc_news_from_wechat()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token();
        $param['type'] = 'news';
        $param['offset'] = I('offset/d', 0);
        $param['count'] = 20;
        $list = $this->post_data($url, $param);
        // dump($list);exit;

        if (empty($list['item'])) {
            $url = U('material_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '下载素材完成');
        }
        $map['media_id'] = array(
            'in',
            getSubByKey($list['item'], 'media_id')
        );
        $map['pbid'] = get_pbid();

        $has = M('material_news')->where(wp_where($map))->column('DISTINCT media_id, group_id');
        foreach ($list['item'] as $item) {
            $media_id = $item['media_id'];
            if (isset($has[$media_id])) {
                $old_map['group_id'] = $has[$media_id];
                $id_arr = M('material_news')->where(wp_where($old_map))
                    ->order('id asc')
                    ->field('id,update_time,thumb_media_id')
                    ->select();
                $update_time = $id_arr[0]['update_time'];
                if ($update_time == $item['content']['update_time']) {
                    // 更新时间一样，表示不需要更新
                    continue;
                }

                foreach ($item['content']['news_item'] as $index => $vo) {
                    $data = [];
                    $is_save = isset($id_arr[$index]);
                    $data['title'] = $vo['title'];
                    $data['author'] = $vo['author'];
                    $data['intro'] = $vo['digest'];
                    $vo['content'] = preg_replace('#data-src#i', 'src', $vo['content']);
                    $data['content'] = $vo['content'];
                    $data['url'] = $vo['url'];
                    $data['update_time'] = $item['content']['update_time'];

                    $thumb_media_id = $id_arr[$index]['thumb_media_id'];
                    $data['thumb_media_id'] = $vo['thumb_media_id'];
                    if ($thumb_media_id != $vo['thumb_media_id']) {
                        $data['cover_id'] = $this->_download_imgage($data['thumb_media_id'], '', $vo);
                    }

                    if ($is_save) {
                        $save_map['id'] = $id_arr[$index]['id'];
                        M('material_news')->where(wp_where($save_map))->update($data);
                    } else {
                        if (!isset($data['cover_id']) || empty($data['cover_id'])) {
                            $data['thumb_media_id'] = $vo['thumb_media_id'];
                            $data['cover_id'] = $this->_download_imgage($data['thumb_media_id'], '', $vo);
                        }
                        $data['group_id'] = $old_map['group_id'];
                        $data['cTime'] = $item['content']['create_time'];
                        $data['manager_id'] = $this->mid;
                        $data['pbid'] = get_pbid();
                        M('material_news')->insert($data);
                    }
                }

                $id_count = count($id_arr);
                $new_count = count($item['content']['news_item']);
                if ($new_count < $id_count) {
                    // 远程有删除
                    $del_map['group_id'] = $old_map['group_id'];
                    $del_map['update_time'] = array(
                        'neq',
                        $item['update_time']
                    );
                    M('material_news')->where(wp_where($del_map))->delete();
                }
            } else {
                $ids = [];
                foreach ($item['content']['news_item'] as $vo) {
                    $data['title'] = $vo['title'];
                    $data['author'] = $vo['author'];
                    $data['intro'] = $vo['digest'];
                    $vo['content'] = preg_replace('#data-src#i', 'src', $vo['content']);
                    $data['content'] = $vo['content'];
                    $data['thumb_media_id'] = $vo['thumb_media_id'];
                    $data['media_id'] = $media_id;
                    $data['cover_id'] = $this->_download_imgage($data['thumb_media_id'], '', $vo);
                    $data['url'] = $vo['url'];
                    $data['cTime'] = $item['content']['create_time'];
                    $data['update_time'] = $item['content']['update_time'];
                    $data['manager_id'] = $this->mid;
                    $data['pbid'] = get_pbid();
                    $ids[] = M('material_news')->insertGetId($data);
                }

                if (!empty($ids)) {
                    $map2['id'] = array(
                        'in',
                        $ids
                    );
                    M('material_news')->where(wp_where($map2))->setField('group_id', $ids[0]);
                }
            }
        }

        $url = U('syc_news_from_wechat', array(
            'mdm' => input('mdm'),
            'offset' => $param['offset'] + $list['item_count']
        ));
        return $this->jump($url, '下载微信素材中，请勿关闭');
    }

    public function _download_imgage($media_id, $picUrl = '', $dd = null)
    {
        $savePath = SITE_PATH . '/public/uploads/picture/' . time_format(NOW_TIME, 'Y-m-d');
        mkdirs($savePath);
        $cover_id = 0;
        if (empty($picUrl)) {
            // 获取图片URL
            $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
            $param['media_id'] = $media_id;
            // dump($url);
            $picContent = post_data($url, $param, 'json', false);
            $picjson = json_decode($picContent, true);
            // dump($picjson);die;
            if (isset($picjson['errcode']) && $picjson['errcode'] != 0) {
                $cover_id = do_down_image($media_id, $dd['thumb_url']);
                if (!$cover_id) {
                    return 0;
                    exit();
                }
            }
            $picName = NOW_TIME .uniqid(). '.jpg';
            $picPath = $savePath . '/' . $picName;
            $res = file_put_contents($picPath, $picContent);
        } else {
            $content = wp_file_get_contents($picUrl);
            // 获取图片扩展名
            $picExt = substr($picUrl, strrpos($picUrl, '=') + 1);
            if (empty($picExt) || $picExt == 'jpeg' || strpos('jpg,gif,png,jpeg,bmp', $picExt) === false) {
                $picExt = 'jpg';
            }
            $picName = NOW_TIME .uniqid(). '.' . $picExt;
            $picPath = $savePath . '/' . $picName;
            $res = file_put_contents($picPath, $content);
            if (!$res) {
                $cover_id = do_down_image($media_id);
                if (!$cover_id) {
                    return 0;
                    exit();
                }
            }
        }

        if ($res) {
            $file = array(
                'name' => $picName,
                'type' => 'application/octet-stream',
                'tmp_name' => $picPath,
                'size' => $res,
                'error' => 0
            );

            $File = D('home/Picture');
            $cover_id = $File->addFile($file);
        }
        return $cover_id;
    }

    /**
     * ********************************图片素材*************************************************
     */
    public function picture_lists()
    {
        // $config=get_info_config('Wecome');
        // dump($config);
        $this->assign('normal_tips', '温馨提示：图片大小不超过2M,支持PNG\JPEG\JPG\GIF格式');
        $map['is_use'] = 1;

        $map['pbid'] = get_pbid();
        $page_data = M('material_image')->where(wp_where($map))
            ->field('id,cover_url')
            ->order('id desc')
            ->paginate(39);
        $list = dealPage($page_data);
        $this->assign($list);
        return $this->fetch();
    }

    public function add_picture()
    {
        $save['cover_id'] = I('cover_id');
        $save['cover_url'] = I('src');
        if (empty($save['cover_id']) || empty($save['cover_url'])) {
            $this->error('图片参数出错');
        }
        if (substr($save['cover_url'], 0, 9) == '/uploads/') {
            $save['cover_url'] = SITE_URL . $save['cover_url'];
        }
        $save['cTime'] = NOW_TIME;
        $save['manager_id'] = $this->mid;
        $save['pbid'] = get_pbid();
        $data['id'] = M('material_image')->insertGetId($save);
        $this->success('增加成功', '', $data);
    }

    public function del_picture()
    {
        $id = I('id');

        $media_id = M('material_image')->where('id', $id)->value('media_id');
        $this->del_material($media_id);

        echo M('material_image')->where('id', $id)->delete();
    }

    public function picture_data()
    {
        // $this->assign ( 'normal_tips', '温馨提示：图片大小不超过5M, 格式: bmp, png, jpeg, jpg, gif' );
        $map['is_use'] = 1;
        $map['pbid'] = get_pbid();
        $page_data = M('material_image')->where(wp_where($map))
            ->field('id,cover_url')
            ->order('id desc')
            ->paginate();
        $list = dealPage($page_data);

        // $list['list_data'] = $this->parseListData($data, $model);
        $this->assign($list);
        return $this->fetch();
    }

    // 根据id获取图片素材,设置欢迎语用到
    public function ajax_picture_by_id($id = '', $width = '', $height = '')
    {
        if (I('img_id')) {
            $id = $isAjax = I('img_id');
        }

        $images = M('material_image')->where('id', $id)->find();
        $imgpath = get_cover_url($images['cover_id'], $width = '', $height = '');
        if (isset($isAjax) && $isAjax) {
            echo $imgpath;
        } else {
            return $imgpath;
        }
    }

    // 上传图片素材
    public function syc_image_to_wechat()
    {
        // 上传本地素材
        $map['media_id'] = '0';
        $map['pbid'] = get_pbid();
        $list = M('material_image')->limit(10)
            ->where(wp_where($map))
            ->field('id,cover_id,media_id')
            ->order('cTime desc')
            ->select();
        if (empty($list)) {
            $url = U('picture_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传素材完成');
        }

        foreach ($list as $vo) {
            $mediaId = D('common/Custom')->get_image_media_id($vo['cover_id']);
            if ($mediaId) {
                $save['media_id'] = $mediaId;
                M('material_image')->where('id', $vo['id'])->update($save);
            }
        }
        $url = U('syc_image_to_wechat', array(
            'mdm' => input('mdm')
        ));
        return $this->jump($url, '上传本地素材到微信中，请勿关闭');
    }

    // 下载图片
    public function syc_image_from_wechat()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token();
        $param['type'] = 'image';
        $param['offset'] = I('offset/d', 0);
        $param['count'] = 20;
        $list = $this->post_data($url, $param);

        if (empty($list['item'])) {
            $url = U('picture_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '下载素材完成');
        }

        $map['media_id'] = array(
            'in',
            getSubByKey($list['item'], 'media_id')
        );

        $map['pbid'] = get_pbid();
        $has = M('material_image')->where(wp_where($map))->column('DISTINCT media_id,id');
        // dump($map);
        // dump($has);

        foreach ($list['item'] as $item) {
            $media_id = $item['media_id'];
            if (isset($has[$media_id])) {
                continue;
            }

            if ($item['url']) {
                $ids = [];
                $data['cover_id'] = $this->_download_imgage($media_id, $item['url']);
                $data['cover_url'] = get_cover_url($data['cover_id']);
                $data['wechat_url'] = $item['url'];
                $data['media_id'] = $media_id;
                $data['cTime'] = NOW_TIME;
                $data['manager_id'] = $this->mid;
                $data['pbid'] = get_pbid();
                $ids[] = M('material_image')->insertGetId($data);
            }
        }
        $url = U('syc_image_from_wechat', array(
            'mdm' => input('mdm'),
            'offset' => $param['offset'] + $list['item_count']
        ));
        return $this->jump($url, '下载微信素材中，请勿关闭');
    }

    /**
     * ********************************音频素材*************************************************
     */
    public function do_down_file()
    {
        $fileid = I('fid');
        $path = get_file_url($fileid);
        if (empty($path)) {
            $this->error('文件不存在');
        }
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . basename($path));
        readfile($path);
    }

    // 根据id获取图片素材,设置欢迎语用到
    public function ajax_voice_by_id($id = '')
    {
        if ($id == '') {
            $id = $isAjax = I('voice_id');
        }

        $voiceMaterial = M('material_file')->where('id', $id)->find();
        if ($voiceMaterial) {
            $voiceMaterial['file_path'] = get_file_url($voiceMaterial['file_id']);
            $voiceMaterial['playtime'] = '未知时长';
            $file = M('file')->where('id', $voiceMaterial['file_id'])->find();
            $voiceMaterial['title'] = $voiceMaterial['title'] ? $voiceMaterial['title'] : $file['name'];
            $path = './uploads/download' . $file['savepath'] . $file['savename'];
            $path = realpath($path);
            require_once env('vendor_path') . 'getID3/getid3/getid3.php';
            $getID3 = new \getID3(); // 实例化类
            $voiceMaterial['playtime'] = '未知时长';
            if (file_exists($path)) {
                $info = $getID3->analyze($path);
                // 以下算法只适用于1个小时以内的时长显示
                if (isset($info['playtime_seconds']) && !empty($info['playtime_seconds'])){
	                $voiceMaterial['playtime'] = date("i:s", $info['playtime_seconds']);
                }
            }
        }
        if (isset($isAjax) && $isAjax) {
            $this->ajaxReturn($voiceMaterial);
        } else {
            return $voiceMaterial;
        }
    }

    public function voice_lists()
    {
        $this->assign('normal_tips', '温馨提示：语音大小不超过2M，长度不超过60秒，支持AMR\MP3格式');

        $map['pbid'] = get_pbid();
        $map['type'] = 1;
        $map['is_use'] = 1;
        $page_data = M('material_file')->where(wp_where($map))
            ->order('id desc')
            ->paginate(30);
        $list = dealPage($page_data);
        // require_once(env('vendor_path')."getID3/getid3/getid3.php" );
        require_once env('vendor_path') . 'getID3/getid3/getid3.php';
        $getID3 = new \getID3(); // 实例化类
        Config::load(env('app_path') . 'home/config.php');

        if (!empty($list['list_data'])) {
            $file_ids = getSubByKey($list['list_data'], 'file_id');
            $file_map['id'] = array(
                'in',
                $file_ids
            );
            $file_list = M('file')->where(wp_where($file_map))->select();
            $cpath = config('download_upload');
            $cpath['rootPath'] = './uploads/download';
            foreach ($file_list as $vo) {
                $path = $cpath['rootPath'] . $vo['savepath'] . $vo['savename'];

                $path = realpath($path);
                $vo['path'] = U("do_down_file", array(
                    'fid' => $vo['id']
                ));
                $vo['playtime'] = '未知时长';
                if (file_exists($path)) {
                    $info = $getID3->analyze($path);
                    // 以下算法只适用于1个小时以内的时长显示
                    $vo['playtime'] = date("i:s", $info['playtime_seconds']);
                }
                $file_arr[$vo['id']] = $vo;
            }
            foreach ($list['list_data'] as &$v) {
                $v['file_info'] = isset($file_arr[$v['file_id']]) ? $file_arr[$v['file_id']] : [];
            }
        }

        $this->assign($list);
        return $this->fetch();
    }

    public function voice_data()
    {
        // $this->assign ( 'normal_tips', '温馨提示：语音大小不超过5M，长度不超过60秒，支持mp3/wma/wav/amr格式' );
        $map['is_use'] = 1;

        $map['pbid'] = get_pbid();
        $map['type'] = 1;
        $list = M('material_file')->where(wp_where($map))
            ->order('id desc')
            ->paginate();
        $list = dealPage($list);

        // $list ['list_data'] = $this->parseListData($data, $model);
        require_once env('vendor_path') . 'getID3/getid3/getid3.php';
        $getID3 = new \getID3(); // 实例化类
        Config::load(env('app_path') . 'home/config.php');
        if (!empty($list['list_data'])) {
            $file_ids = getSubByKey($list['list_data'], 'file_id');
            $file_map['id'] = array(
                'in',
                $file_ids
            );
            $file_list = M('file')->where(wp_where($file_map))->select();
            $cpath = config('download_upload');
            $cpath['rootPath'] = './uploads/download';
            $file_arr=[];
            foreach ($file_list as $vo) {
                $path = $cpath['rootPath'] . $vo['savepath'] . $vo['savename'];
                $vo['path'] = $path = realpath($path);
                $vo['playtime'] = '未知时长';
                if (file_exists($path)) {
                    $info = $getID3->analyze($path);
                    // 以下算法只适用于1个小时以内的时长显示
                    $vo['playtime'] = date("i:s", $info['playtime_seconds']);
                }
                $file_arr[$vo['id']] = $vo;
            }
            foreach ($list['list_data'] as &$v) {
                $v['file_info'] =isset( $file_arr[$v['file_id']])? $file_arr[$v['file_id']]:[];
            }
        }

        $this->assign($list);
        return $this->fetch();
    }

    public function voice_add()
    {
        $model = $this->getModel('material_file');

        if (IS_POST) {
            $data = input('post.');
            $data['type'] = 1;
            $data['pbid'] = get_pbid();
            $data['cTime'] = NOW_TIME;
            $data['manager_id'] = $this->mid;

            require_once env('vendor_path') . 'getID3/getid3/getid3.php';
            $getID3 = new \getID3(); // 实例化类
            Config::load(env('app_path') . 'home/config.php');
            // require_once(env('vendor_path')."getID3/getid3/getid3.php" );
            // $getID3 = new \getID3 (); // 实例化类
            $filedata = M('file')->where('id', input('post.file_id'))->find();
            $cpath = config('download_upload');
            $cpath['rootPath'] = './uploads/download';
            $path = $cpath['rootPath'] . $filedata['savepath'] . $filedata['savename'];
            $path = realpath($path);
            if (file_exists($path)) {
                $info = $getID3->analyze($path);
                // 以下算法只适用于1个小时以内的时长显示
                if (isset($info['playtime_seconds']) && $info['playtime_seconds'] > 60) {
                    $this->error('语音长度不能超过60秒！');
                }
            }

            $this->check_file_size(input('post.file_id'), 5);
            if (empty($data['file_id'])) {
                $this->error('请上传文件');
            }
            if (empty($data['media_id'])) {
                $data['media_id'] = $this->_get_file_media_id($data['file_id'], 'voice');
            }
            $id = M('material_file')->insertGetId($data);
            if ($id) {
                $this->success('添加' . $model['title'] . '成功！', U('voice_lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error('添加失败');
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);

            $this->assign('post_url', U('voice_add'));
            $this->assign('UploadFileExts', '*.mp3;*.wma;*.wav;*.amr');

            return $this->fetch('add');
        }
    }

    public function voice_edit()
    {
        $model = $this->getModel('material_file');
        $id = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        $pbid = get_pbid();
        // if (isset ( $data ['pbid'] ) && $pbid != $data ['pbid']) {
        // $this->error ( '非法访问！' );
        // }
        if (IS_POST) {
            require_once env('vendor_path') . 'getID3/getid3/getid3.php';
            $getID3 = new \getID3(); // 实例化类
            Config::load(env('app_path') . 'home/config.php');
            $filedata = M('file')->where('id', input('post.file_id'))->find();
            $cpath = config('download_upload');
            $cpath['rootPath'] = './uploads/download';
            $path = $cpath['rootPath'] . $filedata['savepath'] . $filedata['savename'];
            $path = realpath($path);
            if (file_exists($path)) {
                $info = $getID3->analyze($path);
                // 以下算法只适用于1个小时以内的时长显示
                if ($info['playtime_seconds'] > 60) {
                    $this->error('语音长度不能超过60秒！');
                }
            }

            $this->check_file_size(input('post.file_id'), 2);
            $Model = M(parse_name($model['name'], 1));
            $data = I('post.');
            if (empty($data['file_id'])) {
                $this->error('请上传文件');
            }
            if (empty($data['media_id'])) {
                $data['media_id'] = $this->_get_file_media_id($data['file_id'], 'voice');
            }
            $data = $this->checkData($data, $model);
            $res = $Model->where(wp_where(array(
                'id' => $id
            )))->update($data);
            if ($res !== false) {
                $this->success('保存' . $model['title'] . '成功！', U('voice_lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $fields['introduction']['is_show'] = 0;
            $this->assign('fields', $fields);
            $this->assign('data', $data);

            $this->assign('post_url', U('voice_edit'));
            $this->assign('UploadFileExts', '*.mp3;*.wma;*.wav;*.amr');

            return $this->fetch('edit');
        }
    }

    // 下载音频
    public function _voice_download($media_id, $cover_url = '')
    {
        $savePath = SITE_PATH . '/public/uploads/download/' . time_format(NOW_TIME, 'Y-m-d');
        mkdirs($savePath);
        $ext = 'mp3';
        if (empty($cover_url)) {
            // 获取图片URL
            $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
            $param['media_id'] = $media_id;
            $picContent = post_data($url, $param, 'json', false);
            $picjson = json_decode($picContent, true);
            if (isset($picjson['errcode']) && $picjson['errcode'] != 0) {
                $msg = error_msg($picjson, '下载音频文件素材失败');
                addWeixinLog($msg, '_voice_download');
                return 0;
            }
            $picName = NOW_TIME .uniqid(). '.' . $ext;
            $picPath = $savePath . '/' . $picName;
            $res = file_put_contents($picPath, $picContent);
            // }
        } else {
            $content = wp_file_get_contents($cover_url);
           
            // 获取图片扩展名
            $picExt = substr($cover_url, strrpos($cover_url, '=') + 1);
            // $picExt=='jpeg'
            if (empty($picExt)) {
                $picExt = $ext;
            }
            $picName = NOW_TIME .uniqid(). '.' . $picExt;
            $picPath = $savePath . '/' . $picName;
            $res = file_put_contents($picPath, $content);
            if (!$res) {
                addWeixinLog('远程音频文件下载失败', '_voice_download');
                return 0;
            }
        }
        $cover_id = 0;
        if ($res) {
            $file = array(
                'name' => $picName,
                'type' => 'application/octet-stream',
                'tmp_name' => $picPath,
                'size' => $res,
                'error' => 0
            );

            $File = D('home/File');
            $cover_id = $File->addFile($file);
        }
        return $cover_id;
    }

    public function syc_voice_to_wechat()
    {
        // 上传本地语音素材
        $map['media_id'] = '0';

        $map['pbid'] = get_pbid();
        $map['type'] = 1;
        $list = M('material_file')->limit(1)
            ->where(wp_where($map))
            ->field('id,file_id')
            ->order('cTime desc')
            ->select();
        if (empty($list)) {
            $url = U('voice_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传素材完成');
        }
        foreach ($list as $vo) {

            $mediaId = $this->_get_file_media_id($vo['file_id'], 'voice');
            if ($mediaId) {
                $save['media_id'] = $mediaId;
                M('material_file')->where('id', $vo['id'])->update($save);
            }
        }
        $url = U('syc_voice_to_wechat', array(
            'mdm' => input('mdm')
        ));
        return $this->jump($url, '上传本地素材到微信中，请勿关闭');
    }

    /**
     * ********************************视频素材*************************************************
     */
    public function video_lists()
    {
        $this->assign('normal_tips', '温馨提示：视频不能超过10MB，支持MP4格式');
        $map['is_use'] = 1;

        $map['pbid'] = get_pbid();
        $map['type'] = 2;
        $list = M('material_file')->where(wp_where($map))
            ->order('id desc')
            ->paginate(39);
        $list = dealPage($list);

        $this->assign($list);
        return $this->fetch();
    }

    // 根据id获取图片素材,设置欢迎语用到
    public function ajax_video_by_id($id = '')
    {
        if ($id == '') {
            $id = $isAjax = I('video_id');
        }
        $videoMaterial = M('material_file')->where('id', $id)->find();
        if (!empty($videoMaterial)) {
            $videoMaterial['file_url'] = get_file_url($videoMaterial['file_id']);
            $videoMaterial['cTime'] = time_format($videoMaterial['cTime']);
        }
        if (isset($isAjax) && $isAjax) {
            $this->ajaxReturn($videoMaterial);
        } else {
            return $videoMaterial;
        }
    }

    public function video_data()
    {
        // $this->assign ( 'normal_tips', '温馨提示：视频不能超过20M，支持大部分主流视频格式，超过20M的视频可至腾讯视频上传后添加' );
        $map['is_use'] = 1;

        $map['pbid'] = get_pbid();
        $map['type'] = 2;
        $list = M('material_file')->where(wp_where($map))
            ->order('id desc')
            ->paginate();
        $list = dealPage($list);
        // dump($list);
        $this->assign($list);
        return $this->fetch();
    }

    public function video_add()
    {
        $model = $this->getModel('material_file');

        if (IS_POST) {
			$data = input ( 'post.' );
			if (empty ( $data ['title'] )) {
            	$this->error('素材名称不为空');
            }
            $data['pbid'] = get_pbid();
            $data['cTime'] = NOW_TIME;
            $data['manager_id'] = $this->mid;
            $this->check_video_size(input('post.file_id'), 20);

            $data['type'] = 2;
            $data['media_id'] = $this->_get_file_media_id($data['file_id'], 'video', $data['title'], $data['introduction']);
            $Model = M(parse_name($model['name'], 1));

            $data = $this->checkData($data, $model);
            $id = $Model->removeOption('data')->insertGetId($data);
            if ($id) {
                $this->success('添加' . $model['title'] . '成功！', U('video_lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error('添加失败！');
            }
        } else {
            $fields = get_model_attribute($model);
            $fields['introduction']['is_show'] = 1;
            $this->assign('fields', $fields);
            $this->assign('post_url', U('video_add'));
            return $this->fetch('common@base/add');
        }
    }

    public function del_file()
    {
        $id = input('id');
        $media_id = M('material_file')->where('id', $id)->value('media_id');
        $this->del_material($media_id);

        $model = $this->getModel('material_file');
        return parent::common_del($model);
    }

    function del_material($media_id)
    {
        if (!empty($media_id)) {
            $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=' . get_access_token();
            $param['media_id'] = $media_id;
            post_data($url, $param);
        }
    }

    public function video_edit()
    {
        $model = $this->getModel('material_file');
        $id = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        $pbid = get_pbid();
        if (isset($data['pbid']) && $pbid != $data['pbid']) {
            $this->error('非法访问！');
        }

        if (IS_POST) {
        	$data = I('post.');
        	if (empty ( $data ['title'] )) {
        		$this->error('素材名称不为空');
        	}
            $this->check_video_size(input('post.file_id'), 20);
            $Model = M(parse_name($model['name'], 1));
           
            $data = $this->checkData($data, $model);
            if (empty($data['media_id'])) {
                $data['media_id'] = $this->_get_file_media_id($data['file_id'], 'video', $data['title'], $data['introduction']);
            }
            $res = $Model->where(wp_where(array(
                'id' => $id
            )))->update($data);
            if ($res !== false) {
                $this->success('保存' . $model['title'] . '成功！', U('video_lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error('保存失败');
            }
        } else {
            $fields = get_model_attribute($model);
            $fields['introduction']['is_show'] = 1;
            $this->assign('fields', $fields);
            $this->assign('data', $data);

            $this->assign('post_url', U('video_edit'));

            return $this->fetch('common@base/edit');
        }
    }

    public function syc_video_to_wechat()
    {
        // 上传本地视频素材
        $map['id'] = array(
            'gt',
            I('id/d', 0)
        );
        $map['media_id'] = '0';

        $map['pbid'] = get_pbid();
        $map['type'] = 2;
        $list = M('material_file')->limit(1)
            ->where(wp_where($map))
            ->field('id,file_id,title,introduction')
            ->order('id asc')
            ->select();
        if (empty($list)) {
            $url = U('video_lists', array(
                'mdm' => input('mdm')
            ));
            return $this->jump($url, '上传素材完成');
        }

        foreach ($list as $vo) {
            $id = $vo['id'];
            $mediaId = $this->_get_file_media_id($vo['file_id'], 'video', $vo['title'], $vo['introduction']);
            if ($mediaId) {
                $save['media_id'] = $mediaId;
                M('material_file')->where('id', $vo['id'])->update($save);
            }
        }
        $url = U('syc_video_to_wechat', array(
            'id' => $id
        ));
        return $this->jump($url, '上传本地素材到微信中，请勿关闭');
    }

    // 下载音频
    public function _video_download($media_id, $cover_url = '')
    {
        $savePath = SITE_PATH . '/public/uploads/download/' . time_format(NOW_TIME, 'Y-m-d');
        mkdirs($savePath);
        $ext = 'mp4';
        if (empty($cover_url)) {
            // 获取图片URL
            $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
            $param['media_id'] = $media_id;
            $info = $this->post_data($url, $param);

            return $info;
            // }
        } else {
            $content = wp_file_get_contents($cover_url);
            // 获取图片扩展名
            $arr = parse_url($cover_url);
            $arr = explode('.', $arr['path']);
            $picExt = '';
            if (count($arr) == 2) {
                $picExt = array_pop($arr);
            }
            // $picExt=='jpeg'
            if (empty($picExt)) {
                $picExt = $ext;
            }
            $picName = NOW_TIME .uniqid(). '.' . $picExt;
            $picPath = $savePath . '/' . $picName;
            $res = file_put_contents($picPath, $content);
            if (!$res) {
                $this->error('远程视频文件下载失败');
                exit();
            }
        }
        $cover_id = 0;
        if ($res) {
            $file = array(
                'name' => $picName,
                'type' => 'application/octet-stream',
                'tmp_name' => $picPath,
                'size' => $res,
                'error' => 0
            );
            $File = D('home/File');
            $cover_id = $File->addFile($file);
        }
        return $cover_id;
    }

    /**
     * *******************多媒体共用***********************
     */
    public function syc_file_from_wechat()
    {
        $type = I('type', 1);
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token();
        $type_name = $type == 1 ? 'voice' : 'video';
        $param['type'] = $type_name;
        $param['offset'] = I('offset/d', 0);
        $param['count'] = 1;
        $list = $this->post_data($url, $param);

        if (empty($list['item'])) {
            if ($type == 1) {
                $url = U('voice_lists', array(
                    'mdm' => input('mdm')
                ));
            } else {
                $url = U('video_lists', array(
                    'mdm' => input('mdm')
                ));
            }

            return $this->jump($url, '下载素材完成');
        }
        $map['media_id'] = array(
            'in',
            getSubByKey($list['item'], 'media_id')
        );
        $map['type'] = $type;
        $map['pbid'] = get_pbid();

        $has = M('material_file')->where(wp_where($map))->column('DISTINCT media_id,id');
        foreach ($list['item'] as $item) {
            $media_id = $item['media_id'];
            if (isset($has[$media_id])) {
                continue;
            }

            $ids = [];
            if ($type == 1) {
                $data['title'] = $item['name'];
                $data['file_id'] = $this->_voice_download($media_id);
            } else {
                // 视频
                $video = $this->_video_download($media_id);
                $data['title'] = $video['title'];
                $data['introduction'] = $video['description'];
                $data['wechat_url'] = $video['down_url'];

                $data['file_id'] = $this->_video_download(0, $data['wechat_url']);
            }
            $data['wechat_url'] = isset($item['url']) ? $item['url'] : '';
            $data['media_id'] = $media_id;
            $data['cTime'] = $item['update_time'];
            $data['manager_id'] = $this->mid;
            $data['pbid'] = get_pbid();
            $data['type'] = $type;
            $ids[] = M('material_file')->insertGetId($data);
        }
        $url = U('syc_file_from_wechat', array(
            'mdm' => input('mdm'),
            'offset' => $param['offset'] + $list['item_count'],
            'type' => $type
        ));
        return $this->jump($url, '下载微信素材中，请勿关闭');
    }

    // 上传视频、语音素材
    public function _get_file_media_id($file_id, $type = 'voice', $title = '', $introduction = '')
    {
        $fileInfo = M('file')->where('id', $file_id)->find();
        // dump($fileInfo);
        if (!$fileInfo) {
            addWeixinLog('素材不存在：' . $file_id, '_get_file_media_id');
            $this->error('素材不存在：' . $file_id);
        }
        $path = SITE_PATH . '/public/uploads/download/' . $fileInfo['savepath'] . $fileInfo['savename'];
        if (!file_exists($path)) {
            addWeixinLog('素材文件不存在：' . $file_id, '_get_file_media_id');
            $this->error('素材文件不存在：' . $file_id);
        }
        $param = upload_param_by_curl($path);
        $param['type'] = $type;
        if ($type == 'video') {
            $video['title'] = $title;
            $video['introduction'] = $introduction;
            $param['description'] = json_url($video);
        }

        // dump($param);
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token();
        $res = post_data($url, $param, 'file');
        if (!$res) {
            addWeixinLog(error_msg($res, '视频/语音素材上传'), '_get_file_media_id');
            $this->error('素材文件上传失败' . $file_id);
        }
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            addWeixinLog(error_msg($res, '视频/语音素材上传'), '_get_file_media_id');
            $this->error(error_msg($res, '视频/语音素材上传'));
        }
        if (isset($res['curl_erron']) && $res['curl_error'] != 0) {
            addWeixinLog($res['curl_error'], '_thumb_media_id');
            $this->error($res['curl_error']);
        }
        // dump($res);
        return $res['media_id'];
    }

    /**
     * ********************************文本素材*************************************************
     */
    public function text_lists()
    {
        $model = $this->getModel('material_text');
        $param['mdm'] = I('mdm');
        $this->assign('add_url', U('text_add', $param));
        $this->assign('del_url', U('text_del'));
        $this->assign('search_url', U('text_lists'));

        $isAjax = I('isAjax');
        $isRadio = I('isRadio');

        $map['is_use'] = 1;
        $map['pbid'] = get_pbid();
        session('common_condition', $map);
        // 获取模型信息
        is_array($model) || $model = $this->getModel($model);

        $list_data = $this->_get_model_list($model);
        $this->assign($list_data);

        if ($isAjax) {
            $this->assign('isRadio', $isRadio);
            $this->assign($list_data);
            return $this->fetch('text_lists_data');
        } else {
            $this->assign($list_data);
            return $this->fetch('common@base/lists');
        }
    }

    // 根据id获取文本素材,设置欢迎语用到
    public function ajax_text_by_id()
    {
        $id = I('text_id');
        $text = M('material_text')->where('id', $id)->value('content');

        echo $text;
    }

    public function text_add()
    {
        $model = $this->getModel('material_text');

        if (IS_POST) {
            $data = input('post.');
            $data['pbid'] = get_pbid();
            $data['uid'] = $this->mid;
            $Model = M(parse_name($model['name'], 1));

            $data = $this->checkData($data, $model);
            $id = $Model->removeOption('data')->insertGetId($data);
            if ($id) {
                $this->success('添加' . $model['title'] . '成功！', U('text_lists?model=' . $model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);

            $this->assign('post_url', U('text_add'));

            return $this->fetch('common@base/add');
        }
    }

    public function text_del()
    {
        $model = $this->getModel('material_text');
        return parent::common_del($model);
    }

    public function text_edit()
    {
        $model = $this->getModel('material_text');
        $id = I('id');

        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');

        $pbid = get_pbid();
        if (isset($data['pbid']) && $pbid != $data['pbid']) {
            $this->error('非法访问！');
        }

        if (IS_POST) {
            $Model = M(parse_name($model['name'], 1));
            $data = I('post.');
            $data = $this->checkData($data, $model);
            $res = $Model->where(wp_where(array(
                'id' => $id
            )))->update($data);
            if ($res !== false) {
                $this->success('保存' . $model['title'] . '成功！', U('text_lists?model=' . $model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);

            $this->assign('post_url', U('text_edit'));

            return $this->fetch('common@base/edit');
        }
    }

    public function get_content_by_id()
    {
        $map['id'] = I('id');
        $content1 = M('material_text')->where(wp_where($map))->value('content');
        echo $content1;
    }

    public function check_file_size($fileId, $limSize, $strExt = 'mp3,wma,wav,amr', $checkExt = 1)
    {
        $files = M('file')->where('id', $fileId)->find();
        $size = $files['size'];
        $bs = $limSize * 1024 * 1024;
        if ($size > $bs) {
            $this->error('上传文件不能超过' . $limSize . 'M');
            exit();
        }
        if ($checkExt) {
            $ext = $files['ext'];

            $extArr = wp_explode($strExt, ',');
            if (!in_array($ext, $extArr)) {
                $this->error('上传文件类型不支持，请上传扩展名为' . $strExt . '的文件！');
            }
        }
    }

    /*
     * Yolanda
     */
    public function check_video_size($fileId, $limSize, $strExt = 'mp4,fl,f4v,webm, m4v,mov,3gp,3g2 ,rm,rmvb, wmv,avi,asf,mpg,mpeg,mpe,ts,div,dv,divx,vob,dat,mkv,swf,lavf,cpk,dirac,ram,qt,fli,flc,mod', $checkExt = 2)
    {
        $files = M('file')->where('id', $fileId)->find();
        $size = $files['size'];
        $bs = $limSize * 1024 * 1024;
        if ($size > $bs) {
            $this->error('上传文件不能超过' . $limSize . 'M');
            exit();
        }
        if ($checkExt) {
            $ext = $files['ext'];

            $extArr = wp_explode($strExt, ',');
            if (!in_array($ext, $extArr)) {
                // $this->error ( '上传文件类型不支持，请上传扩展名为' . $strExt . '的文件！' );
                $this->error('上传文件不支持' . $ext . '类型');
            }
        }
    }

    public function test()
    {
        $map['pbid'] = get_pbid();
        $field = 'id,title,cover_id,intro,group_id';
        $list = M('material_news')->where(wp_where($map))
            ->field($field . ',count(id) as count')
            ->group('group_id')
            ->order('group_id asc,id asc')
            ->select();
        $arr = [];
        foreach ($list as $vo) {
            if (isset($arr[$vo['title']])) {
                $map['group_id'] = $vo['group_id'];
                $media_id = M('material_news')->where(wp_where($map))->value('media_id');
                $res = M('material_news')->where(wp_where($map))->delete();
                if ($res) {
                    $this->_del_syc_news($media_id);
                }
            } else {
                $arr[$vo['title']] = 1;
            }
        }
    }

    // 图文消息的内容图片，上传到微信并获取新的链接覆盖
    public function getNewContent($content)
    {
        if (!$content) {
            return;
        }

        $newUrl = [];
        // 获取文章中图片img标签
        // $match=$this->getImgSrc($content);
        preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $content, $match);
        foreach ($match[1] as $mm) {
            $oldUrl = $mm;

            if (!preg_match("/^(http:\/\/|https:\/\/).*$/", $mm)) {
                //没有
                $mm = SITE_URL . $mm;
                $mm = str_replace('public./', '', $mm);
            }

            $newUrl[$oldUrl] = uploadimg($mm);
        }

        if (count($newUrl)) {
            $content_new = strtr($content, $newUrl);
        }
        return empty($content_new) ? $content : $content_new;
    }
}
