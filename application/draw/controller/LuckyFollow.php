<?php
namespace app\draw\controller;

use app\common\controller\WebBase;
use think\Db;

class LuckyFollow extends WebBase
{

    public function initialize()
    {
        parent::initialize();
        
        $controller = strtolower(CONTROLLER_NAME);
        $res['title'] = '抽奖游戏';
        $res['url'] = U('Draw/Games/lists', $this->get_param);
        $res['class'] = $controller == 'games' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '奖品库管理';
        $res['url'] = U('Draw/Award/lists', $this->get_param);
        $res['class'] = $controller == 'award' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '中奖人列表';
        $res['url'] = U('Draw/LuckyFollow/games_lucky_lists', $this->get_param);
        $res['class'] = $controller == 'luckyfollow' ? 'current' : '';
        $nav[] = $res;
        
        $this->assign('nav', $nav);
    }

    public function do_fafang()
    {
        $id = input('id');
        $data = M('lucky_follow')->where('id', $id)->find();
        
        if (request()->isPost()) {
            if ($data['state'] == 0) {
                $this->error('该用户还未通过核销验证！', '', true);
            }
            $save['state'] = input('state');
            $save['remark'] = input('remark');
            $res = M('lucky_follow')->where('id', $id)->update($save);
            if ($res!==false) {
                D('Draw/LuckyFollow')->getUserAward($id, true);
                $this->success('发放成功！', U('games_lucky_lists', array(
                    'games_id' => $data['draw_id']
                )), true);
            } else {
                $this->error('发放失败！', '', true);
            }
        } else {
            $this->assign('data', $data);
        }
        
        return $this->fetch();
    }

    function setState()
    {
        $id = I('id/d', 0);
        if (empty($id)) {
            $this->error('找不到中奖记录');
        }
        $res = M('lucky_follow')->where('id', $id)->update([
            'state' => Db::raw('1-state')
        ]);
        if ($res!==false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    public function games_lucky_lists()
    {
        $this->assign('search_button', false);
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $gamesId = I('games_id/d', 0);
        $map['wpid'] = get_wpid();
        $map['aim_table'] = 'lottery_games';
        if ($gamesId) {
            $map['draw_id'] = $gamesId;
        }
        $state = I('s_state/d', - 1);
        if ($state != - 1) {
            $map['state'] = $state;
        }
        $this->assign('state', $state);
        $awardId = I('awardId/d', 0);
        if ($awardId) {
            $map['award_id'] = $awardId;
        }
        
        $search = input('award_name');
        if ($search) {
        	$this->assign('search', $search);
        	$map1['name'] = array(
        			'like',
        			'%' . htmlspecialchars($search) . '%'
        	);
        	$award_ids = D('award')->where(wp_where($map1))->column('id');
        	if (! empty($award_ids)) {
        		$map['award_id'] = array(
        				'in',
        				$award_ids
        		);
        	} 
        	unset($_REQUEST['award_name']);
        }
        
        session('common_condition', $map);
        $model = $this->getModel('lucky_follow');
        
        $list_data = $this->_get_model_list($model);
        $gamesDao = D('Draw/Games');
        $awardDao = D('Draw/LotteryGamesAwardLink');
        // $addressDao = D('common/Address' );
        foreach ($list_data['list_data'] as &$v) {
            
            $awardLists = $awardDao->getGamesAwardlists($v['draw_id']);
            foreach ($awardLists as $a) {
                if (isset($a['award_id'])) {
                    $awardData[$a['award_id']] = $a;
                }
            }
            if (isset($awardData[$v['award_id']]) && $awardData[$v['award_id']]) {
                $awardId = $v['award_id'];
                $v['award_id'] = '';
                if (isset($awardData[$awardId]['name'])) {
                    $v['award_id'] = $awardData[$awardId]['name'];
                }
//                 if (isset($awardData[$awardId]['grade'])) {
//                     $v['award_id'] .= '（' . $awardData[$awardId]['grade'] . '）';
//                 }
                
                // $v['award_name'] = $awardData[$awardId]['name'];
            }
            $user = get_userinfo($v['follow_id']);
            $v['follow_id'] = isset($user['nickname']) ? $user['nickname'] : '';
            $v['openid'] = D('common/Follow')->getOpenidByUid($user['uid']);
            $v['truename'] = isset($user['truename']) ? $user['truename'] : '';
            $v['mobile'] = isset($user['mobile']) ? $user['mobile'] : '';
            
            /*
             * $address_id = intval ( $v ['address'] );
             * if ($address_id) {
             * $address = $addressDao->getInfo ( $address_id );
             * $v ['address'] = $address ['address'];
             * $v ['truename'] = $address ['truename'];
             * $v ['mobile'] = $address ['mobile'];
             * }
             */
            // $v['state'] = $v['state'] == 1 ? '已发奖' : '未发奖';
            $gamesInfo = $gamesDao->getInfo($v['draw_id']);
            $v['draw_id'] = isset($gamesInfo['title']) ? $gamesInfo['title'] : '';
            $v['draw_time'] = '';
            if (isset($gamesInfo['start_time'])) {
                $v['draw_time'] = time_format($gamesInfo['start_time']);
            }
            if (isset($gamesInfo['end_time'])) {
                $v['draw_time'] .= '至' . time_format($gamesInfo['end_time']);
            }
        }
        unset($list_data['list_grids']['urls']);
        $this->assign($list_data);
        return $this->fetch();
    }

    public function lists()
    {
        $url = U('games_lucky_lists', $this->get_param);
        return redirect($url);
        
        $nav[0]['title'] = '奖品库管理';
        $nav[0]['url'] = U('Draw/Award/lists', $this->get_param);
        $nav[0]['class'] = '';
        $this->assign('nav', $nav);
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $uid = $map['uid'] = $this->mid;
        $awardtype = I('award_type');
        if ($awardtype == 2 || $awardtype == 1) {
            if ($awardtype == 2) {
                $awardtype = 0;
            }
            $award_ids = D('award')->where(wp_where(array(
                'award_type' => $awardtype,
                'uid' => $uid
            )))->column('id');
            $award_ids = implode(',', $award_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else {
                $map['award_id'] = 0;
            }
        }
        
        $search = input('award_id');
        if ($search) {
            $this->assign('search', $search);
            $map1['name'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map1['uid'] = $uid;
            $award_ids = D('award')->where(wp_where($map1))->column('id');
            $award_ids = implode(',', $award_ids);
            
            $map2['truename'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map3['mobile'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $mobile_follow_ids = $truename_follow_ids = D('user')->where(wp_where($map2))->column('uid');
            
            $truename_follow_ids = implode(',', $truename_follow_ids);
            $mobile_follow_ids = implode(',', $mobile_follow_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else if (! empty($truename_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $truename_follow_ids . ') '
                );
            } else if (! empty($mobile_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $mobile_follow_ids . ') '
                );
            } else {
                $map['id'] = 0;
            }
            unset($_REQUEST['award_id']);
        }
        $awardid = I('award');
        if ($awardid) {
            $map['award_id'] = $awardid;
        }
        
        if (I('sports_id')) {
            $map['sport_id'] = I('sports_id');
        }
        empty($map) || session('common_condition', $map);
        
        $model = $this->getModel('lucky_follow');
        $model['search_key'] = 'award_id:输入搜索奖品名称、姓名、手机号码';
        $list_data = $this->_get_model_list($model, 'id desc', true);
        // unset($list_data['list_grids'][12]);
        unset($list_data['list_grids'][13]);
        
        // dump($list_data);
        $type = I('type');
        $flag = I('flag');
        $dao = D('LuckyFollow');
        
        if (! empty($type) && ($flag == 0 || $flag == 1)) {
            $page = I('p', 1, 'intval');
            $row = empty($model['list_row']) ? 20 : $model['list_row'];
            $fix = config("DB_PREFIX");
            $sortStr = '';
            switch ($type) {
                case 'area':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.province desc ';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.province asc';
                        $flag = 0;
                    }
                    $sortAreaStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                        'type' => 'area',
                        'flag' => $flag
                    )) . '">地区</a>';
                    break;
                case 'mobile':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.mobile desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.mobile asc';
                        $flag = 0;
                    }
                    $sortMobileStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                        'type' => 'mobile',
                        'flag' => $flag
                    )) . '">手机号</a>';
                    
                    break;
                case 'truename':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.truename desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.truename asc';
                        $flag = 0;
                    }
                    $sortTruenameStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                        'type' => 'truename',
                        'flag' => $flag
                    )) . '">姓名</a>';
                    
                    break;
                case 'award':
                    if ($flag == 0) {
                        $orderstr = $fix . 'award.name desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'award.name asc';
                        $flag = 0;
                    }
                    $sortAwardStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                        'type' => 'award',
                        'flag' => $flag
                    )) . '">中奖奖品</a>';
                    break;
                default:
                    break;
            }
            if ($type == 'award') {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'award', $fix . 'lucky_follow.award_id=' . $fix . 'award.id')
                    ->where($fix . 'lucky_follow.uid', $uid)
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->paginate($row);
                
                // lastsql();
            } else {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'follow', $fix . 'lucky_follow.follow_id=' . $fix . 'follow.id ')
                    ->where('uid', $uid)
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->paginate($row);
                // lastsql();
            }
            $list_data = $this->parsePageData($qulist, $model, $list_data, false);
            // dump($qulist);
            foreach ($list_data['list_data'] as &$v) {
                $info = $dao->getInfo($v['id']);
                $v = array_merge($v, $info);
            }
        } else {
            foreach ($list_data['list_data'] as &$vo) {
                $info = $dao->getInfo($vo['id']);
                $vo = array_merge($vo, $info);
            }
        }
        
        foreach ($list_data['list_grids'] as $key => &$v) {
            if ($key == 'area') {
                $v['title'] = $sortAreaStr ? $sortAreaStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                    'type' => 'area',
                    'flag' => 1
                )) . '">地区</a>';
            } elseif ($key == 'mobile') {
                $v['title'] = $sortMobileStr ? $sortMobileStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                    'type' => 'mobile',
                    'flag' => 1
                )) . '">手机号</a>';
            } elseif ($key == 'truename') {
                $v['title'] = $sortTruenameStr ? $sortTruenameStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                    'type' => 'truename',
                    'flag' => 1
                )) . '">姓名</a>';
            } elseif ($key == 'award_id') {
                $v['title'] = $sortAwardStr ? $sortAwardStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lists', array(
                    'type' => 'award',
                    'flag' => 1
                )) . '">中奖奖品</a>';
            }
        }
        // dump($qulist);
        // 根据奖品过滤
        $awards = D('Award')->where('uid', $uid)
            ->field('id,name')
            ->select();
        $this->assign('awards', $awards);
        // 根据比赛场次过滤
        $sports = M('sports')->field('id,home_team,visit_team,start_time')->select();
        $teamdao = D('Sports/Team');
        foreach ($sports as &$s) {
            $home = $teamdao->getInfo($s['home_team']);
            $visit = $teamdao->getInfo($s['visit_team']);
            $s['sports_team'] = $home['title'] . '&nbsp; VS &nbsp;' . $visit['title'] . '&nbsp;&nbsp;' . time_format($s['start_time'], 'Y/m/d');
        }
        $this->assign('sports', $sports);
        // dump($list_data);
        $this->assign($list_data);
        
        return $this->fetch();
    }

    // 根据奖品id，获取中奖者列表
    public function getlistByAwardId()
    {
        $param['awardId'] = I('awardId', 0, "intval");
        $url = U('games_lucky_lists', $param);
        return redirect($url);
    }

    // 根据奖品id，获取奖品名称数组
    private function getAwardNames($op)
    {
        if (! empty($op)) {
            $map1['id'] = array(
                'in',
                $op
            );
            $awardName = M('award')->where(wp_where($map1))
                ->field('id,name')
                ->select();
            foreach ($awardName as $v) {
                $aname[$v['id']] = $v['name'];
            }
        }
        return $aname;
    }

    // 根据场次id获取场次名
    private function getSports($sp)
    {
        if (! empty($sp)) {
            $sport = D('Sports/Sports');
            foreach ($sp as $v) {
                $s[] = $sport->getInfo($v);
            }
            foreach ($s as $v) {
                $team[$v['id']] = $v['vs_team'];
            }
        }
        return $team;
    }

    // 获取粉丝信息
    private function getFollow($fids)
    {
        if (! empty($fids)) {
            foreach ($fids as $v) {
                $follow[] = get_followinfo($v);
            }
            foreach ($follow as $v) {
                $followinfo[$v['id']] = $v;
            }
        }
        
        return $followinfo;
    }

    // 查看中奖人信息
    public function luckyFollowDetail()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('search_button', false);
        $this->assign('check_all', false);
        
        $grid['field'][0] = 'sports_name';
        $grid['title'] = '参与的场次';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'join_count';
        $grid['title'] = '摇一摇参与次数';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'drum_count';
        $grid['title'] = '擂鼓参与次数';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'draw_count';
        $grid['title'] = '抽奖参与次数';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'support_team';
        $grid['title'] = '支持的球队';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'comment';
        $grid['title'] = '发表的评论';
        $list_grids[] = $grid;
        
        $list_data['list_grids'] = $list_grids;
        
        $id = I('id/d', 0);
        $luckyfollow = D('LuckyFollow')->getInfo($id);
        
        $follow_id = $luckyfollow['follow_id'];
        
        $map['follow_id'] = $follow_id;
        $support_team = M('sports_support')->where(wp_where($map))
            ->field('sports_id,team_id')
            ->select();
        $sportsDao = D('Sports/Sports');
        $teamDao = D('Sports/Team');
        if ($support_team) {
            foreach ($support_team as &$v) {
                $sports = $sportsDao->getInfo($v['sports_id']);
                $v['sports_name'] = $sports['vs_team'];
                $team = $teamDao->getInfo($v['team_id']);
                $v['support_team'] = $team['title'];
                $data[] = $v;
                $sportsIdArr[$v['sports_id']] = $v['sports_id'];
            }
        }
        
        // 获取擂鼓数
        // dump($data);
        if ($sportsIdArr) {
            $map2['follow_id'] = $follow_id;
            $map2['sports_id'] = array(
                'in',
                $sportsIdArr
            );
        }
        
        $drumCountData = M('sports_drum')->where(wp_where($map2))
            ->field('sports_id,sum( `drum_count` ) as drum_count')
            ->group('sports_id')
            ->select();
        if ($drumCountData) {
            foreach ($drumCountData as $d) {
                $drumCount[$d['sports_id']] = $d['drum_count'];
            }
        }
        // 获取抽奖数
        $drawCountData = M('draw_follow_log')->where(wp_where($map2))
            ->field('sports_id,count')
            ->select();
        if ($drawCountData) {
            foreach ($drawCountData as $d) {
                $drawCount[$d['sports_id']] = $d['count'];
            }
        }
        
        // $commentData=M( 'comment' )->where( wp_where($map3) )->field('aim_id,content')->order('cTime desc')->select();
        // dump($commentData);
        
        // $commentData=M( 'comment' )->where( wp_where($map3) )->field('aim_id,content')->order('cTime desc')->group('aim_id')->select();
        // dump($commentData);
        // 获取摇摇数
        if ($sportsIdArr) {
            $map3['follow_id'] = $follow_id;
            $map3['aim_id'] = array(
                'in',
                $sportsIdArr
            );
        }
        D('common/Count')->write();
        $joinCountData = M('join_count')->where(wp_where($map3))
            ->field('aim_id,count')
            ->select();
        if ($joinCountData) {
            foreach ($joinCountData as $j) {
                $joinCount[$j['aim_id']] = $j['count'];
            }
        }
        
        if ($data) {
            foreach ($data as &$v) {
                $v['drum_count'] = intval($drumCount[$v['sports_id']]);
                $v['draw_count'] = intval($drawCount[$v['sports_id']]);
                $v['join_count'] = intval($joinCount[$v['sports_id']]);
                $v['comment'] = M('comment')->where(wp_where(array(
                    'follow_id' => $follow_id,
                    'aim_id' => $v['sports_id']
                )))
                    ->order('cTime desc')
                    ->value('content');
            }
        }
        rsort($data);
        // dump($data);
        $list_data['list_data'] = $data;
        $this->assign('luckyfollow', $luckyfollow);
        $this->assign($list_data);
        return $this->fetch();
    }

    // 修改状态
    public function changeState()
    {
        $state = I('state');
        $id = I('id');
        $data['state'] = $state == 0 ? '1' : '0';
        $info = D('LuckyFollow')->getInfo($id);
        // 奖品数量减1
        $res = D('LotteryPrizeList')->setCount($info['sportsid'], $info['prizeid']);
        $data['djtime'] = NOW_TIME;
        $result = D('LuckyFollow')->updateInfo($id, $data);
        $this->success('修改成功');
    }

    // 导出数据
    public function output()
    {
        $awardtype = I('award_type');
        $uid = $map['uid'] = $this->mid;
        if ($awardtype == 2 || $awardtype == 1) {
            if ($awardtype == 2) {
                $awardtype = 0;
            }
            $award_ids = D('award')->where(wp_where(array(
                'award_type' => $awardtype,
                'uid' => $uid
            )))->column('id');
            $award_ids = implode(',', $award_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else {
                $map['award_id'] = 0;
            }
        }
        
        $search = I('search');
        if ($search) {
            $this->assign('search', $search);
            $map1['name'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map1['uid'] = $uid;
            $award_ids = D('award')->where(wp_where($map1))->column('id');
            $award_ids = implode(',', $award_ids);
            
            $map2['truename'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map3['mobile'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $mobile_follow_ids = $truename_follow_ids = D('user')->where(wp_where($map2))->column('uid');
            
            $truename_follow_ids = implode(',', $truename_follow_ids);
            $mobile_follow_ids = implode(',', $mobile_follow_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else if (! empty($truename_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $truename_follow_ids . ') '
                );
            } else if (! empty($mobile_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $mobile_follow_ids . ') '
                );
            } else {
                $map['id'] = 0;
            }
        }
        $awardid = I('award');
        if ($awardid) {
            $map['award_id'] = $awardid;
        }
        
        if (I('sports_id')) {
            $map['sport_id'] = I('sports_id');
        }
        empty($map) || session('common_condition', $map);
        
        $list_data['list_data'] = M('lucky_follow')->field('id')
            ->where(wp_where($map))
            ->select();
        $type = I('type');
        $flag = I('flag');
        $dao = D('LuckyFollow');
        
        if (! empty($type) && ($flag == 0 || $flag == 1)) {
            $fix = config("DB_PREFIX");
            $sortStr = '';
            switch ($type) {
                case 'area':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.province desc ';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.province asc';
                        $flag = 0;
                    }
                    break;
                case 'mobile':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.mobile desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.mobile asc';
                        $flag = 0;
                    }
                    break;
                case 'truename':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.truename desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.truename asc';
                        $flag = 0;
                    }
                    
                    break;
                case 'award':
                    if ($flag == 0) {
                        $orderstr = $fix . 'award.name desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'award.name asc';
                        $flag = 0;
                    }
                    break;
                default:
                    break;
            }
            if ($type == 'award') {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'award', $fix . 'lucky_follow.award_id=' . $fix . 'award.id')
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->select();
            } else {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'follow', $fix . 'lucky_follow.follow_id=' . $fix . 'follow.id')
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->select();
            }
            $allDatas = [];
            foreach ($qulist as $v) {
                $dd = $dao->getInfo($v['id']);
                $datas['nickname2'] = $dd['nickname2'];
                $datas['mobile'] = $dd['mobile'];
                $datas['truename'] = $dd['truename'];
                $datas['award_id'] = $dd['award_id'];
                $datas['sport_id'] = $dd['sport_id'];
                $datas['zjtime'] = time_format($dd['zjtime']);
                $datas['djtime'] = time_format($dd['djtime']);
                $datas['drum_count'] = $dd['drum_count'];
                $allDatas[] = $datas;
            }
        } else {
            foreach ($list_data['list_data'] as $vo) {
                $dd = $dao->getInfo($vo['id']);
                $datas['nickname2'] = $dd['nickname2'];
                $datas['mobile'] = $dd['mobile'];
                $datas['truename'] = $dd['truename'];
                $datas['award_id'] = $dd['award_id'];
                $datas['sport_id'] = $dd['sport_id'];
                $datas['zjtime'] = time_format($dd['zjtime']);
                $datas['djtime'] = time_format($dd['djtime']);
                $datas['drum_count'] = $dd['drum_count'];
                $allDatas[] = $datas;
            }
        }
        $fieldArr = array(
            'nickname2' => '昵称',
            'mobile' => '手机号',
            'truename' => '姓名',
            'award_id' => '中奖奖品',
            'sport_id' => '中奖场次',
            'zjtime' => '中奖时间',
            'djtime' => '兑奖时间',
            'drum_count' => '擂鼓次数'
        );
        foreach ($fieldArr as $k => $vv) {
            $fields[] = $k;
            $titleArr[] = $vv;
        }
        $dataArr[] = $titleArr;
        foreach ($allDatas as $v) {
            $dataArr[] = $v;
        }
        require_once env('vendor_path') . 'out-csv.php';
        export_csv($dataArr, 'LuckyFollow_output');
    }

    // ///////////靓妆频道//////////////////////
    public function lzwg_lists()
    {
        $nav[0]['title'] = '奖品库管理';
        $nav[0]['url'] = U('Draw/Award/lzwg_lists');
        $nav[0]['class'] = '';
        
        $nav[1]['title'] = '中奖列表';
        $nav[1]['url'] = U('Draw/LuckyFollow/lzwg_lists');
        $nav[1]['class'] = 'current';
        
        $this->assign('nav', $nav);
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $uid = $this->mid;
        $awardtype = I('award_type');
        if ($awardtype == 2 || $awardtype == 1) {
            if ($awardtype == 2) {
                $awardtype = 0;
            }
            $award_ids = D('award')->where(wp_where(array(
                'award_type' => $awardtype,
                'uid' => $uid
            )))->column('id');
            $award_ids = implode(',', $award_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else {
                $map['award_id'] = 0;
            }
        }
        
        $search = input('award_id');
        if ($search) {
            $this->assign('search', $search);
            $map1['name'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map1['uid'] = $uid;
            $award_ids = D('award')->where(wp_where($map1))->column('id');
            $award_ids = implode(',', $award_ids);
            
            $map2['truename'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map3['mobile'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $mobile_follow_ids = $truename_follow_ids = D('user')->where(wp_where($map2))->column('uid');
            
            $truename_follow_ids = implode(',', $truename_follow_ids);
            $mobile_follow_ids = implode(',', $mobile_follow_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else if (! empty($truename_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $truename_follow_ids . ') '
                );
            } else if (! empty($mobile_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $mobile_follow_ids . ') '
                );
            } else {
                $map['id'] = 0;
            }
            unset($_REQUEST['award_id']);
        }
        $awardid = I('award');
        if ($awardid) {
            $map['award_id'] = $awardid;
        }
        
        if (I('lzwg_id')) {
            $map['draw_id'] = I('lzwg_id');
        }
        $map['uid'] = $uid;
        empty($map) || session('common_condition', $map);
        
        $model = $this->getModel('lucky_follow');
        $model['search_key'] = 'award_id:输入搜索奖品名称、姓名、手机号码';
        $list_data = $this->_get_model_list($model, 'id desc', true);
        unset($list_data['list_grids'][13]);
        
        $type = I('type');
        $flag = I('flag');
        $dao = D('LuckyFollow');
        
        if (! empty($type) && ($flag == 0 || $flag == 1)) {
            $page = I('p', 1, 'intval');
            $row = empty($model['list_row']) ? 20 : $model['list_row'];
            $fix = config("DB_PREFIX");
            $sortStr = '';
            switch ($type) {
                case 'area':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.province desc ';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.province asc';
                        $flag = 0;
                    }
                    $sortAreaStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                        'type' => 'area',
                        'flag' => $flag
                    )) . '">地区</a>';
                    break;
                case 'mobile':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.mobile desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.mobile asc';
                        $flag = 0;
                    }
                    $sortMobileStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                        'type' => 'mobile',
                        'flag' => $flag
                    )) . '">手机号</a>';
                    
                    break;
                case 'truename':
                    if ($flag == 0) {
                        $orderstr = $fix . 'follow.truename desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'follow.truename asc';
                        $flag = 0;
                    }
                    $sortTruenameStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                        'type' => 'truename',
                        'flag' => $flag
                    )) . '">姓名</a>';
                    
                    break;
                case 'award':
                    if ($flag == 0) {
                        $orderstr = $fix . 'award.name desc';
                        $flag = 1;
                    } else {
                        $orderstr = $fix . 'award.name asc';
                        $flag = 0;
                    }
                    $sortAwardStr = '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                        'type' => 'award',
                        'flag' => $flag
                    )) . '">中奖奖品</a>';
                    break;
                default:
                    break;
            }
            if ($type == 'award') {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'award', $fix . 'lucky_follow.award_id=' . $fix . 'award.id ')
                    ->where($fix . 'lucky_follow.uid', $uid)
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->paginate($row);
                // lastsql();
            } else {
                $qulist = M('lucky_follow')->join('left join ' . $fix . 'follow', $fix . 'lucky_follow.follow_id=' . $fix . 'follow.id ')
                    ->where('uid', $uid)
                    ->field($fix . "lucky_follow.id")
                    ->order($orderstr)
                    ->paginate($row); // lastsql();
                                                                                                                                                                                                                                          // lastsql();
            }
            $list_data = $this->parsePageData($qulist, $model, $list_data, false);
            // dump($qulist);
            foreach ($list_data['list_data'] as &$v) {
                $info = $dao->getLzwgLuckyFollowInfo($v['id']);
                $v = array_merge($v, $info);
            }
        } else {
            foreach ($list_data['list_data'] as &$vo) {
                $info = $dao->getLzwgLuckyFollowInfo($vo['id']);
                $vo = array_merge($vo, $info);
            }
        }
        
        foreach ($list_data['list_grids'] as $k => &$v) {
            if ($k == 'area') {
                $v['title'] = $sortAreaStr ? $sortAreaStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                    'type' => 'area',
                    'flag' => 1
                )) . '">地区</a>';
            } elseif ($k == 'mobile') {
                $v['title'] = $sortMobileStr ? $sortMobileStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                    'type' => 'mobile',
                    'flag' => 1
                )) . '">手机号</a>';
            } elseif ($k == 'truename') {
                $v['title'] = $sortTruenameStr ? $sortTruenameStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                    'type' => 'truename',
                    'flag' => 1
                )) . '">姓名</a>';
            } elseif ($k == 'award_id') {
                $v['title'] = $sortAwardStr ? $sortAwardStr : '<a class="sort_a" href="' . U('Draw/LuckyFollow/lzwg_lists', array(
                    'type' => 'award',
                    'flag' => 1
                )) . '">中奖奖品</a>';
            } elseif ($k == 'sport_id') {
                $v['title'] = '中奖活动';
            } elseif ($k == 'drum_count') {
                unset($list_data['list_grids'][$k]);
            }
        }
        
        // dump($qulist);
        // 根据奖品过滤
        $awards = D('Award')->where('uid', $uid)
            ->field('id,name')
            ->select();
        $this->assign('awards', $awards);
        // // 根据比赛场次过滤
        // $sports = M( 'sports' )->field ( 'id,home_team,visit_team,start_time' )->select ();
        // $teamdao = D ( 'Sports/Team' );
        // foreach ( $sports as &$s ) {
        // $home = $teamdao->getInfo ( $s ['home_team'] );
        // $visit = $teamdao->getInfo ( $s ['visit_team'] );
        // $s ['sports_team'] = $home ['title'] . '&nbsp; VS &nbsp;' . $visit ['title'] . '&nbsp;&nbsp;' . time_format ( $s ['start_time'], 'Y/m/d' );
        // }
        $lzwg = M('lzwg_activities')->field('id,title as sports_team')->select();
        
        $this->assign('sports', $lzwg);
        $list_data['list_grids'][12]['href'] = 'lzwgFollowDetail?id=[id]|查看';
        $this->assign($list_data);
        
        return $this->fetch();
    }

    public function lzwg_output()
    {
        $awardtype = I('award_type');
        $uid = $map['uid'] = $this->mid;
        if ($awardtype == 2 || $awardtype == 1) {
            if ($awardtype == 2) {
                $awardtype = 0;
            }
            $award_ids = D('award')->where(wp_where(array(
                'award_type' => $awardtype,
                'uid' => $uid
            )))->column('id');
            $award_ids = implode(',', $award_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else {
                $map['award_id'] = 0;
            }
        }
        
        $search = I('search');
        if ($search) {
            $this->assign('search', $search);
            $map1['name'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map1['uid'] = $uid;
            $award_ids = D('award')->where(wp_where($map1))->column('id');
            $award_ids = implode(',', $award_ids);
            
            $map2['truename'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $map3['mobile'] = array(
                'like',
                '%' . htmlspecialchars($search) . '%'
            );
            $mobile_follow_ids = $truename_follow_ids = D('user')->where(wp_where($map2))->column('uid');
            
            $truename_follow_ids = implode(',', $truename_follow_ids);
            $mobile_follow_ids = implode(',', $mobile_follow_ids);
            if (! empty($award_ids)) {
                $map['award_id'] = array(
                    'exp',
                    ' in (' . $award_ids . ') '
                );
            } else if (! empty($truename_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $truename_follow_ids . ') '
                );
            } else if (! empty($mobile_follow_ids)) {
                $map['follow_id'] = array(
                    'exp',
                    ' in (' . $mobile_follow_ids . ') '
                );
            } else {
                $map['id'] = 0;
            }
        }
        $awardid = I('award');
        if ($awardid) {
            $map['award_id'] = $awardid;
        }
        
        if (I('lzwg_id')) {
            $map['draw_id'] = I('lzwg_id');
        }
        empty($map) || session('common_condition', $map);
        
        $list_data['list_data'] = M('lucky_follow')->field('id')
            ->where(wp_where($map))
            ->select();
        // $type = I ( 'type' );
        // $flag = I ( 'flag' );
        $dao = D('LuckyFollow');
        
        foreach ($list_data['list_data'] as $vo) {
            $dd = $dao->getLzwgLuckyFollowInfo($vo['id']);
            $datas['nickname2'] = $dd['nickname2'];
            $datas['mobile'] = $dd['mobile'];
            $datas['truename'] = $dd['truename'];
            $datas['award_id'] = $dd['award_id'];
            $datas['sport_id'] = $dd['sport_id'];
            $datas['zjtime'] = time_format($dd['zjtime']);
            $datas['djtime'] = time_format($dd['djtime']);
            // $datas ['drum_count'] = $dd ['drum_count'];
            $allDatas[] = $datas;
        }
        $fieldArr = array(
            'nickname2' => '昵称',
            'mobile' => '手机号',
            'truename' => '姓名',
            'award_id' => '中奖奖品',
            'sport_id' => '中奖活动',
            'zjtime' => '中奖时间',
            'djtime' => '兑奖时间'
        );
        foreach ($fieldArr as $k => $vv) {
            $fields[] = $k;
            $titleArr[] = $vv;
        }
        $dataArr[] = $titleArr;
        foreach ($allDatas as $v) {
            $dataArr[] = $v;
        }
        require_once env('vendor_path') . 'out-csv.php';
        export_csv($dataArr, 'LuckyFollow_lzwg_output');
    }

    // 未改
    public function lzwgFollowDetail()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('search_button', false);
        $this->assign('check_all', false);
        $grid['field'][0] = 'sports_name';
        $grid['title'] = '参与的活动';
        $list_grids[] = $grid;
        
        $grid['field'][0] = 'join_count';
        $grid['title'] = '参与次数';
        $list_grids[] = $grid;
        
        // $grid ['field'] [0] = 'drum_count';
        // $grid ['title'] = '擂鼓参与次数';
        // $list_grids [] = $grid;
        
        $grid['field'][0] = 'draw_count';
        $grid['title'] = '抽奖参与次数';
        $list_grids[] = $grid;
        
        // $grid ['field'] [0] = 'support_team';
        // $grid ['title'] = '支持的球队';
        // $list_grids [] = $grid;
        
        $grid['field'][0] = 'comment';
        $grid['title'] = '发表的评论';
        $list_grids[] = $grid;
        
        $list_data['list_grids'] = $list_grids;
        
        $id = I('id/d', 0);
        $luckyfollow = D('LuckyFollow')->getLzwgLuckyFollowInfo($id);
        
        $follow_id = $luckyfollow['follow_id'];
        
        $map['follow_id'] = $follow_id;
        $support_team = M('sports_support')->where(wp_where($map))
            ->field('sports_id,team_id')
            ->select();
        $sportsDao = D('Sports/Sports');
        $teamDao = D('Sports/Team');
        if ($support_team) {
            foreach ($support_team as &$v) {
                $sports = $sportsDao->getInfo($v['sports_id']);
                $v['sports_name'] = $sports['vs_team'];
                $team = $teamDao->getInfo($v['team_id']);
                $v['support_team'] = $team['title'];
                $data[] = $v;
                $sportsIdArr[$v['sports_id']] = $v['sports_id'];
            }
        }
        
        // 获取擂鼓数
        // dump($data);
        if ($sportsIdArr) {
            $map2['follow_id'] = $follow_id;
            $map2['sports_id'] = array(
                'in',
                $sportsIdArr
            );
        }
        
        $drumCountData = M('sports_drum')->where(wp_where($map2))
            ->field('sports_id,sum( `drum_count` ) as drum_count')
            ->group('sports_id')
            ->select();
        if ($drumCountData) {
            foreach ($drumCountData as $d) {
                $drumCount[$d['sports_id']] = $d['drum_count'];
            }
        }
        // 获取抽奖数
        $drawCountData = M('draw_follow_log')->where(wp_where($map2))
            ->field('sports_id,count')
            ->select();
        if ($drawCountData) {
            foreach ($drawCountData as $d) {
                $drawCount[$d['sports_id']] = $d['count'];
            }
        }
        
        // $commentData=M( 'comment' )->where( wp_where($map3) )->field('aim_id,content')->order('cTime desc')->select();
        // dump($commentData);
        
        // $commentData=M( 'comment' )->where( wp_where($map3) )->field('aim_id,content')->order('cTime desc')->group('aim_id')->select();
        // dump($commentData);
        // 获取摇摇数
        if ($sportsIdArr) {
            $map3['follow_id'] = $follow_id;
            $map3['aim_id'] = array(
                'in',
                $sportsIdArr
            );
        }
        D('common/Count')->write();
        $joinCountData = M('join_count')->where(wp_where($map3))
            ->field('aim_id,count')
            ->select();
        if ($joinCountData) {
            foreach ($joinCountData as $j) {
                $joinCount[$j['aim_id']] = $j['count'];
            }
        }
        
        rsort($data);
        // dump($data);
        $list_data['list_data'] = $data;
        $this->assign('luckyfollow', $luckyfollow);
        // $this->assign ( $list_data );
        return $this->fetch();
    }

    public function getlzwgListByAwardId()
    {
        $nav[0]['title'] = '奖品库管理';
        $nav[0]['url'] = U('Draw/Award/lzwg_lists');
        $nav[0]['class'] = '';
        
        $nav[1]['title'] = '中奖者列表';
        $nav[1]['class'] = 'current';
        
        $nav[2]['title'] = '中奖列表';
        $nav[2]['url'] = U('Draw/LuckyFollow/lzwg_lists');
        $nav[2]['class'] = '';
        
        $this->assign('nav', $nav);
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $this->assign('search_button', false);
        
        $awardId = I('awardId', 0, "intval");
        $this->assign('awardid', $awardId);
        $map['uid'] = $this->mid;
        $map['award_id'] = $awardId;
        session('common_condition', $map);
        
        $model = $this->getModel('lucky_follow');
        $model['search_key'] = 'award_id:输入搜索姓名、手机号码';
        $list_data = $this->_get_model_list($model, 'id desc', true);
        // unset($list_data['list_grids'][12]);
        unset($list_data['list_grids'][13]);
        
        $dao = D('LuckyFollow');
        foreach ($list_data['list_data'] as &$vo) {
            $info = $dao->getInfo($vo['id']);
            $vo = array_merge($vo, $info);
        }
        $this->assign($list_data);
        // dump($list_data);
        
        return $this->fetch('getListByAwardId');
    }

    public function lzwgChangeState()
    {
        $state = I('state');
        $id = I('id');
        $data['state'] = $state == 0 ? '1' : '0';
        $info = D('LuckyFollow')->getLzwgLuckyFollowInfo($id);
        // 奖品数量减1
        $res = D('LotteryPrizeList')->setCount($info['draw_id'], $info['prizeid']);
        $data['djtime'] = NOW_TIME;
        $result = D('LuckyFollow')->updateInfo($id, $data);
        $this->success('修改成功');
    }
}
