<?php
namespace app\draw\controller;

use app\common\controller\WebBase;

// use think\cache\driver\Redis;
class Games extends WebBase
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

    function getprize()
    {
        $id = I('id');
        $res = D('draw/EventPrizes')->getEventPrize($id);
        dump($res);
    }

    function test()
    {
        $info = D('draw/Games')->getInfo(12);
        
        $jurl = U('Draw/Wap/index', array(
            'games_id' => $info['id']
        ));
        // 推送图文消息
        // 积分
        if (! empty($info['winning_score_text'])) {
            $replaceArr['{val}'] = 3;
            $info['winning_score_text'] = strtr($info['winning_score_text'], $replaceArr);
            dump($info['winning_score_text']);
            $res = D('common/Custom')->replyText(1, $info['winning_score_text']);
        }
        dump($res);
        exit();
        
        $dOpenid = 'okLkdww1P-b4W525RH67rwdYoL_c';
        $money = 1 * 100; // 单位：分
        $more_param = array(
            'act_name' => '抽奖游戏',
            'act_id' => 12,
            'act_mod' => 'draw'
        ); // 红包其它参数，默认为空
        $redRes = D('Common/Transfer')->add_pay($dOpenid, $money, $more_param, true);
        $redRes['defaultopenid'] = $dOpenid; // 记入缓存
        dump($redRes);
        
        exit();
        $eventId = 12;
        // $res = D('draw/LotteryGamesAwardLink')->getAwardCurrentNum($eId);
        // dump($res);
        // $rediesKey = 'rdraw_lottery_'.$eId;
        $rediesKey = 'test';
        // phpinfo();
        // die;
        $redis = new \think\cache\driver\Redis();
        $rkey = 'rdraw_winlists_' . $eventId;
        $redis->rm($rkey);
        $rediesKey = 'rdraw_lottery_' . $eventId;
        $redis->rm($rediesKey);
        
        $cNum = $redis->get($rediesKey);
        $wlist = $redis->get($rkey);
        dump($cNum);
        dump($wlist);
        
        exit();
        // 存数组
        $arr[11] = 11;
        $arr[332] = 332;
        $arr[324] = 324;
        $arr[889] = 889;
        dump(implode(',', $arr));
        
        $redis->set('list', $arr);
        dump($redis->get('list'));
        // 删除缓存
        // $redis->rm('test');
        $isSet = input('test', 0);
        $kk = 'test11';
        if ($isSet) {
            $redis->set('test', "1");
        }
        $res = $redis->get($rediesKey);
        // dump($redis->inc("test")); //结果：int(124)
        // dump($redis->inc("test")); //结果：int(125)
        
        dump('-----------');
        $arr = [];
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        $pArr = [
            [
                'prize_id' => 'A',
                'prize_num' => 100
            ],
            [
                'prize_id' => 'B',
                'prize_num' => 200
            ],
            [
                'prize_id' => 'C',
                'prize_num' => 750
            ],
            [
                'prize_id' => 'D',
                'prize_num' => 5000
            ]
        ];
        // D('draw/EventPrizes')->setPrizeList(1,$pArr,100000);
        $dao = D('draw/EventPrizes');
        $drawCount = 10;
        for ($i = 1; $i <= $drawCount; $i ++) {
            $res = $dao->getPrize(12, $i);
            if ($res !== 0) {
                $arr[$i] = $res;
            }
        }
        dump($arr);
        die();
    }

    public function lists()
    {
        $isAjax = I('isAjax');
        $isRadio = I('isRadio');
        // $this->assign ( 'search_button', false );
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        $model = $this->getModel('lottery_games');
        $list_data = $this->_get_model_list($model, 'id desc', true);
        // 判断该活动是否已经设置投票调查
        $dao = D('draw/Games');
        // 获取参与人数
        $attendUser = M('draw_follow_log')->where('wpid', get_wpid())
            ->group('sports_id')
            ->field('count(distinct follow_id) num,sports_id')
            ->select();
        $userNum = [];
        foreach ($attendUser as $vv) {
            if (isset($vv['sports_id']) && isset($vv['num'])) {
                $userNum[$vv['sports_id']] = intval($vv['num']);
            }
        }
        
        foreach ($list_data['list_data'] as &$vo) {
            if ($vo['status_db'] == 0) {
                $vo['status'] = '已关闭';
            } else {
                if ($vo['start_time_db'] > NOW_TIME) {
                    $vo['status'] = '未开始';
                } elseif ($vo['end_time_db'] < NOW_TIME) {
                    $vo['status'] = '已结束';
                } else {
                    $vo['status'] = '进行中';
                }
            }
            // 获取参与人数
            // $vo['attend_num'] = $dao->getAttendNum($vo['id']);
            
            $vo['attend_num'] = isset($userNum[$vo['id']]) ? $userNum[$vo['id']] : 0;
            $winUrl = U("draw/LuckyFollow/games_lucky_lists", array(
                'games_id' => $vo['id'],
                'mdm' => input('mdm')
            ));
            $vo['winners_list'] = "<a href='" . $winUrl . "' >中奖人列表</a>";
        }
        if ($isAjax) {
            $this->assign('isRadio', $isRadio);
            $this->assign($list_data);
            return $this->fetch('ajax_lists_data');
        } else {
            $this->assign($list_data);
            return $this->fetch();
        }
    }

    public function edit()
    {
        $model = $this->getModel('lottery_games');
        $id = I('id');
        
        // 获取数据
        $data = M($model['name'])->where('id', $id)->find();
        $data || $this->error('数据不存在！');
        if (IS_POST) {
            $oldCount = $data['draw_count'];
            $data = I('post.');
            $data = $this->checkAward($data);
            $Model = D('Games');
            $data = $this->checkData($data, $model);
            $res = $Model->allowField(true)->save($data, [
                'id' => $id
            ]);
            if ($res !== false) {
                $this->_add_award($id, $data, $oldCount);
                if ($res) {
                    $this->_saveKeyword($model, $id);
                }
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'edit');
                $this->success('保存' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $awardList = D('draw/LotteryGamesAwardLink')->getGamesAwardlists($id, true);
            $this->assign('award_list', $awardList);
            $this->assign('fields', $fields);
            // dump($fields);
            $this->assign('data', $data);
            // var_dump($data);
            
            return $this->fetch();
        }
    }

    public function add()
    {
        $where['wpid'] = get_wpid();
        $res = M('award')->where(wp_where($where))->select();
        $this->assign('award', $res);
        $model = $this->getModel('lottery_games');
        if (IS_POST) {
            $data = I('post.');
            // $this->checkTime ( strtotime ( input('post.start_time') ), strtotime ( input('post.end_time') ) );
            
            $Model = D('Games');
            $data = $this->checkData($data, $model);
            $data = $this->checkAward($data);
            $id = $Model->allowField(true)->insertGetId($data);
            if ($id) {
                $this->_add_award($id, $data);
                $this->_saveKeyword($model, $id);
                // 清空缓存
                method_exists($Model, 'clearCache') && $Model->clearCache($id, 'add');
                $this->success('添加' . $model['title'] . '成功！', U('lists?model=' . $model['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model);
            $this->assign('fields', $fields);
            $this->assign('award_list', []);
            
            return $this->fetch();
        }
    }

    public function del()
    {
        $model = $this->getModel('lottery_games');
        
        ! empty($ids) || $ids = I('id');
        ! empty($ids) || $ids = array_filter(array_unique((array) I('ids', 0)));
        ! empty($ids) || $this->error('请选择要操作的数据!');
        
        $Model = D('draw/Games');
        $mapId = is_array($ids) ? implode(',', $ids) : $ids;
        $map[] = array(
            'id',
            'in',
            $mapId
        );
        
        // 插件里的操作自动加上Token限制
        $dataTable = D('Common/Models')->getFileInfo($model);
        $wpid = get_wpid();
        if (! empty($wpid) && isset($dataTable->fields['wpid'])) {
            $map[] = [
                'wpid',
                '=',
                $wpid
            ];
        }
        $wpid = get_wpid();
        if (! empty($wpid) && isset($dataTable->fields['wpid'])) {
            $map[] = [
                'wpid',
                '=',
                $wpid
            ];
        }
        if ($Model->where(wp_where($map))->delete()) {
            // 清空缓存
            D('draw/Games')->clearCache($ids, 'del');
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 添加抽奖奖品
    public function _add_award($gameId, $postData, $oldCount = 0)
    {
        $awardIdArr = $postData['award_id'];
        
        $gradeArr = $postData['grade'];
        $numArr = $postData['num'];
        $sortArr = $postData['sort'];
        $unRealNumArr = $postData['unreal_num'];
        // $maxCountArr = $postData['max_count'];
        $awardDao = D('Draw/Award');
        $map['games_id'] = $gameId;
        $map['wpid'] = get_wpid();
        
        $lotteryData1 = M('lottery_games_award_link')->where(wp_where($map))
            ->field('award_id,id,grade,num,max_count')
            ->select();
        $lotteryData = [];
        foreach ($lotteryData1 as $vo) {
            $lotteryData[$vo['award_id']] = $vo;
        }
        // 奖品总数量
        $allNum = 0;
        $updateNum = 0;
        foreach ($awardIdArr as $awardId) {
            
            if (isset($lotteryData[$awardId])) {
                // 保存
                $saveData['grade'] = $gradeArr[$awardId];
                $saveData['num'] = $numArr[$awardId];
                $saveData['sort'] = $sortArr[$awardId];
                $saveData['unreal_num'] = $unRealNumArr[$awardId];
                // $saveData['max_count'] = $maxCountArr[$awardId];
                $map['award_id'] = $awardId;
                $allNum += $saveData['num'];
                $res = M('lottery_games_award_link')->where(wp_where($map))->update($saveData);
                if ($res ) {
                    $updateNum ++;
                }
            } else {
                // 添加
                $addData['games_id'] = $gameId;
                $addData['award_id'] = $awardId;
                $addData['wpid'] = $map['wpid'];
                $addData['grade'] = $gradeArr[$awardId];
                $addData['num'] = $numArr[$awardId];
                $addData['sort'] = $sortArr[$awardId];
                $addData['unreal_num'] = $unRealNumArr[$awardId];
                $allNum += $addData['num'];
                // $addData['max_count'] = $maxCountArr[$awardId];
                $addDatas[] = $addData;
            }
        }
        if (! empty($addDatas)) {
            $res = M('lottery_games_award_link')->insertAll($addDatas);
            if ($res) {
                $updateNum ++;
            }
        }
        foreach ($lotteryData as $key => $v) {
            if (! in_array($key, $awardIdArr)) {
                $ids[] = $v['id'];
            }
        }
        if (! empty($ids)) {
            $map1['id'] = array(
                'in',
                $ids
            );
            $res = M('lottery_games_award_link')->where(wp_where($map1))->delete();
            if ($res) {
                $updateNum ++;
            }
        }
        if ($updateNum > 0) {
            $keys = 'LotteryGamesAwardLink_getGamesAwardlists_' . $gameId;
            S($keys, null);
            // $awardList=D('Draw/LotteryGamesAwardLink')->getGamesAwardlists($gameId,true);
        }
        if ($updateNum > 0 || $oldCount != $postData['draw_count']) {
            // 生成抽奖时奖品排位列表
            // 当前奖品
            $prizeArr = D('draw/LotteryGamesAwardLink')->getAwardCurrentNum($gameId);
            // 获取是否百分百中奖
            $isFixed = $allNum == $postData['draw_count'] ? 1 : 0;
            D('draw/EventPrizes')->setPrizeList($gameId, $prizeArr, $postData['draw_count'], $isFixed);
        }
        return $res;
    }

    /* 预览 */
    public function preview()
    {
        $id = input('games_id', 0, "intval");
        $url = U('draw/Wap/index', array(
            'games_id' => $id
        ));
        $this->assign('url', $url);
        return $this->fetch('common@base/preview');
    }

    // 检查奖品数量
    public function checkAward($postData)
    {
        if (isset($postData['title']) && ! $postData['title']) {
            $this->error('活动名称不为空');
        }
        if (isset($postData['keyword']) && mb_strlen($postData['keyword']) > 30) {
            $this->error('关键词不能超过30个字');
        }
        if (empty($postData['game_type'])) {
            $this->error('请选择游戏类型');
        }
        if (isset($postData['start_time']) && ! $postData['start_time']) {
            $this->error('请选择开始时间');
        }
        if (isset($postData['end_time']) && ! $postData['end_time']) {
            $this->error('请选择结束时间');
        }
        if ($postData['start_time'] >= $postData['end_time']) {
            $this->error('开始时间不能大于或等于结束时间');
        }
        if (empty($postData['award_id'])) {
            $this->error('请添加奖品');
        }
        if ($postData['day_attend_limit'] > $postData['attend_limit'] && $postData['attend_limit'] !== '0') {
            $this->error('每人每天抽奖次数不能大于总共的抽奖次数');
        }
        $gradeArr = $postData['grade'];
        $numArr = $postData['num'];
        // 奖品总数
        $allNum = array_sum($numArr);
        if ($postData['draw_count'] != 0 && $allNum > $postData['draw_count']) {
            $this->error('奖品总数量不能超过活动抽奖总次数');
        }
        // $maxCountArr = $postData['max_count'];
        $awardDao = D('Draw/Award');
        $awardIdArr = $postData['award_id'];
        foreach ($awardIdArr as $awardId) {
            $award = $awardDao->getInfo($awardId);
            if (! $gradeArr[$awardId]) {
                $this->error($award['name'] . ' 的等级名称不能为空');
            }
            // if (! $numArr[$awardId]) {
            // $this->error($award['name'] . ' 的奖品数量不能为空');
            // }
            // if (! $maxCountArr[$awardId]) {
            // return $this->error($award['name'] . ' 的最多抽奖数必须大于1');
            // }
            if ($numArr[$awardId] < 0) {
                $this->error($award['name'] . ' 的奖品数量不能小于0');
            }
            // if ($maxCountArr[$awardId] < $numArr[$awardId]) {
            // return $this->error($award['name'] . ' 的最多抽奖数不能小于奖品数量');
            // }
        }
        return $postData;
    }

    /**
     * ***************统计*********************
     */
    public function statistics()
    {
        $action = strtolower(ACTION_NAME);
        
        $res['title'] = '抽奖游戏';
        $res['url'] = U('Draw/Games/lists', array(
            'mdm' => input('mdm')
        ));
        $res['class'] = $action == 'lists' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '统计分析';
        $res['url'] = U('Draw/Games/statistics', array(
            'mdm' => input('mdm'),
            'games_id' => input('games_id')
        ));
        $res['class'] = $action == 'statistics' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '详细列表';
        $res['url'] = U('Draw/Games/follow_lists', array(
            'mdm' => input('mdm'),
            'games_id' => input('games_id')
        ));
        $res['class'] = $action == 'lists' ? 'follow_lists' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);
        
        $gamesId = input('games_id');
        $info = D('Games')->getInfo($gamesId);
        $map['sports_id'] = $gamesId;
        $map['wpid'] = get_wpid();
        $fcount = M('draw_follow_log')->where(wp_where($map))
            ->group('follow_id')
            ->count('follow_id');
        $info['follow_count'] = intval($fcount);
        $info['pv'] = M('draw_pv_log')->where(wp_where(array(
            'draw_id' => $gamesId,
            'wpid' => $map['wpid']
        )))->count('1');
        
        $this->assign('info', $info);
        $this->assign('now_day', time_format(NOW_TIME, 'Y-m-d'));
        return $this->fetch();
    }

    public function ajax_detail_data()
    {
        $isDown = input('is_down/d', 0);
        $data = input();
        $fMap['sports_id'] = $map['draw_id'] = $gamesId = input('games_id/d', 0);
        $startTime = $data['start_day'];
        $endTime = $data['end_day'];
        $startTime = strtotime($startTime);
        $fMap['cTime'] = $map['cTime'] = array(
            'egt',
            $startTime
        );
        if (! empty($endTime)) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $fMap['cTime'] = $map['cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        }
        $all = $this->_get_xAxis($startTime, $endTime);
        $xAxis = $all['xaxis'];
        $pvdayArr = $dayArr = $all['count'];
        // 浏览次数
        $data = M('draw_pv_log')->where(wp_where($map))->column('cTime', 'id');
        foreach ($data as $key => $rv) {
            $day = time_format($rv, 'Y-m-d');
            $pvdayArr[$day]['count'] ++;
        }
        // 参与人数
        $logData = M('draw_follow_log')->where(wp_where($fMap))
            ->group('follow_id')
            ->column('cTime', 'follow_id');
        foreach ($logData as $key => $uv) {
            $day = time_format($uv, 'Y-m-d');
            $dayArr[$day]['count'] ++;
        }
        $oArr['data'] = getSubByKey($dayArr, 'count');
        $oArr['name'] = '参与人数';
        $charArr[] = $oArr;
        $sArr['data'] = getSubByKey($pvdayArr, 'count');
        $sArr['name'] = '浏览量';
        $charArr[] = $sArr;
        if ($isDown) {
            // 下载表格
            $titleArr[] = '日期';
            $titleArr[] = '参与人数';
            $titleArr[] = '浏览量';
            $dataArr[] = $titleArr;
            foreach ($dayArr as $key => $vo) {
                $arr['title'] = $key;
                $arr['fcount'] = $vo['count'];
                $arr['pvcount'] = isset($pvdayArr[$key]['count']) ? $pvdayArr[$key]['count'] : 0;
                $dataArr[] = $arr;
                unset($arr);
            }
            outExcel($dataArr, '抽奖游戏');
        } else {
            $count = count($dayArr);
            $highcharts['title'] = '抽奖活动统计数据';
            $highcharts['xAxis'] = $xAxis;
            $highcharts['series'] = $charArr;
            $highcharts['x_space'] = floor($count / 14);
            $highcharts['x_space'] = $highcharts['x_space'] < 1 ? 1 : $highcharts['x_space'];
            // dump($highcharts);die;
            $this->ajaxReturn($highcharts, 'JSON');
        }
    }

    public function _get_xAxis($startTime, $endTime = '', $addField = '')
    {
        empty($endTime) && $endTime = NOW_TIME;
        $endTime = strtotime(time_format($endTime, 'Y-m-d') . ' 23:59:59');
        // 生成时间数组
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $thisDate = time_format($i, 'Y-m-d');
            $xAxis[] = time_format(strtotime($thisDate), 'm/d');
            $dayArr[$thisDate]['count'] = 0;
            if ($addField) {
                $dayArr[$thisDate][$addField] = 0;
            }
        }
        $data['xaxis'] = $xAxis;
        $data['count'] = $dayArr;
        return $data;
    }

    public function follow_lists()
    {
        $action = strtolower(ACTION_NAME);
        
        $res['title'] = '抽奖游戏';
        $res['url'] = U('Draw/Games/lists', array(
            'mdm' => input('mdm')
        ));
        $res['class'] = $action == 'lists' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '统计分析';
        $res['url'] = U('Draw/Games/statistics', array(
            'mdm' => input('mdm'),
            'games_id' => input('games_id')
        ));
        $res['class'] = $action == 'statistics' ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '详细列表';
        $res['url'] = U('Draw/Games/follow_lists', array(
            'mdm' => input('mdm'),
            'games_id' => input('games_id')
        ));
        $res['class'] = $action == 'follow_lists' ? 'current' : '';
        $nav[] = $res;
        $this->assign('nav', $nav);
        // $this->assign('add_button',false);
        $this->assign('del_button', false);
        $this->assign('search_button', false);
        $this->assign('check_all', false);
        
        $isDown = input('is_down/d', 0);
        
        $gamesId = input('games_id');
        $info = D('Games')->getInfo($gamesId);
        $this->assign('info', $info);
        $startTime = input('start_time');
        $endTime = input('end_time');
        if ($startTime && $endTime) {
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime) + 86400 - 1;
            $map['cTime'] = array(
                'between',
                array(
                    $startTime,
                    $endTime
                )
            );
        } elseif ($startTime) {
            $startTime = strtotime($startTime);
            $map['cTime'] = array(
                'egt',
                $startTime
            );
        } elseif ($endTime) {
            $endTime = strtotime($endTime) + 86400 - 1;
            $map['cTime'] = array(
                'elt',
                $endTime
            );
        }
        $map['sports_id'] = $gamesId;
        $model = $this->getModel('draw_follow_log');
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $data = M('draw_follow_log')->where(wp_where($map))
            ->group('follow_id')
            ->field('id,follow_id,cTime')
            ->select();
        // 获取中奖用户
        $lMap['draw_id'] = $gamesId;
        $lMap['aim_table'] = 'lottery_games';
        $luckData = M('lucky_follow')->where(wp_where($lMap))->column('id', 'follow_id');
        if ($isDown) {
            $titleArr = array(
                '微信名称',
                'openID',
                '地区',
                '性别',
                '是否中奖',
                '参与时间'
            );
            $dataArr[] = $titleArr;
        }
        foreach ($data as &$vo) {
            $user = getUserInfo($vo['follow_id']);
            $vo['follow_id'] = isset($user['nickname']) ? $user['nickname'] : '';
            $openid = D('common/Follow')->getOpenidByUid($user['uid']);
            $vo['openid'] = $openid ? $openid : '';
            $vo['area'] = isset($user['city']) ? $user['city'] : '';
            $vo['sex'] = isset($user['sex_name']) ? $user['sex_name'] : '保密';
            $vo['has_prize'] = isset($luckData[$user['uid']]) ? '中奖' : '未中奖';
            // $vo['cTime'] = time_format($vo['cTime']);
            // $vo['truename'] = isset($user['truename']) ? $user['truename'] : '';
            // $vo['mobile'] = isset($user['mobile']) ? $user['mobile'] : '';
            if ($isDown) {
                $arr['follow_id'] = $vo['follow_id'];
                $arr['openid'] = $vo['openid'];
                $arr['area'] = $vo['area'];
                $arr['sex'] = $vo['sex'];
                $arr['has_prize'] = $vo['has_prize'];
                $arr['cTime'] = time_format($vo['cTime']);
                // $arr['truename'] = $vo['truename'];
                // $arr['mobile'] = $vo['mobile'];
                $dataArr[] = $arr;
                unset($arr);
            }
        }
        
        if ($isDown) {
            // 下载表格
            outExcel($dataArr, '详细列表');
        } else {
            $list_data['list_data'] = $this->parseListData($data, $model);
            $this->assign($list_data);
            return $this->fetch();
        }
    }
}
