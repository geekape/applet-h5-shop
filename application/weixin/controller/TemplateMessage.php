<?php

namespace app\weixin\controller;
use app\common\controller\WebBase;

/**
 * 模板消息群发给用户
 */
class TemplateMessage extends WebBase {
	function initialize() {
		parent::initialize ();
		
		$act = strtolower ( ACTION_NAME );
		
		$res ['title'] = '配置模板ID';
		$res ['url'] = U ( 'config' );
		$res ['class'] = $act == 'config' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '模板消息群发';
		$res ['url'] = U ( 'send_template_message' );
		$res ['class'] = $act == 'send_template_message' ? 'current' : '';
		$nav [] = $res;
		$res ['title'] = '模板消息群发记录';
		$res ['url'] = U ( 'tmessage_lists' );
		$res ['class'] = $act == 'tmessage_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
	}
	//配置模板
	function config(){
	    $pbid = get_pbid();
	    if (empty($pbid)||$pbid == -1){
	        $this->error('数据异常，请重新登录');
	    }
	    $config = D('common/PublicConfig')->getConfig('template_message', 'weixin_template_message', $pbid);
	    if (IS_POST){
	    	$data = input();
	    	if (empty($data['template_id'])){
	            $this->error('请添加模板消息ID');
	        }
	        $save['template_id'] = $data['template_id'];
	        
	        $map['name'] = parse_name(MODULE_NAME, 0);
	        $controller = parse_name(CONTROLLER_NAME, 0);
	        $pkey = $map['name'] . '_' . $controller;
	        $flag = D('Common/PublicConfig')->setConfig($pkey, $save);
	        
	        if ($flag !== false) {
	        	$this->success('保存成功');
	        } else {
	        	$this->error('保存失败');
	        }
	        
	    }else {
	    	$tid = isset($config['template_id'])?$config['template_id']:'';
	        $this->assign('template_id',$tid);
// 	        $this->assign('public_id',$public['id']);

	        return $this->fetch();
	    }
	   
	}
	/////////////模板消息群发给用户/////////////////////
	function send_template_message(){
	// 	    $this->assign ( 'normal_tips', '温馨提示<br/>客服群发接口是指：管理者可以给 在48小时内主动发消息给公众号的用户群发消息 ，发送次数没有限制；如果没有成功接收到消息的用户，则在他主动发消息给公众号时，再重新发给该用户。' );
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
	    if (IS_POST) {
	        $data ['send_openids'] = $sendOpenid = input('send_openids');
	        if (input('send_type') == 1 && $sendOpenid == '') {
	            $this->error ( '指定的Openid值不能为空' );
	        }
	    	$pbid=get_pbid();
	    	
	    	$config = D('common/PublicConfig')->getConfig('template_message', 'weixin_template_message', $pbid);
	    	//发消息给指定人
	    	$count=0;
	    	$openidArr = $this->_get_user_openid ( input('send_type'), input('group_id'), $sendOpenid );
	    	$templateDao = D('common/TemplateMessage');
	    	foreach ($openidArr as $openid){
	    		$tRes = $templateDao->replyMessage($openid,input('content'),input('title'),input('sender'),$config['template_id'],input('jamp_url'));
	    		 //addWeixinLog($tRes,'templatemesaadf');
	    		if (isset($tRes['status']) && $tRes['status']==1){
	    			$count++;
	    		}
	    	}
	    	if ($count>0){
	    		$model = $this->getModel ( 'template_messages' );
	    		// 获取模型的字段信息
	    		$data = I('post.');
	    		$data['pbid']=$pbid;
	    		$data['cTime']=time();
	    		$data['send_count']=$count;
	    		$id = M ('template_messages' )->insertGetId($data);
// 	    		M('template_messages')->where('id',$id)->setField('send_count',$count);
	    		$this->success ( '添加' . $model ['title'] . '成功！', U ( 'send_template_message?model=' . $model ['name']  ) );
	    	}else{
	    		$this->error('群发失败');
	    	}
	    	
	    } else {
	        $map ['pbid'] = get_pbid ();
// 	        $map ['manager_id'] = $this->mid;
	        $map ['is_del'] = 0;
	        $group_list = M ( 'auth_group' )->where ( $map )->select ();
	        $this->assign ( 'group_list', $group_list );
	        
	        return $this->fetch();
	    }
	}
	
	// 群发的信息列表
	function tmessage_lists(){
	    $model = $this->getModel ( 'template_messages' );
	    $map['pbid']=get_pbid();
	    session('common_condition',$map);
	    $list_data = $this->_get_model_list ( $model );
        $this->assign ( $list_data ); 
        return $this->fetch('common@base/lists');
	}
	
	
	/*
	 * sendType:0 按组发 1：指定opendid
	 * groupid :0 指所有用户
	 */
	public function _get_user_openid($sendType = 0, $groupId = 0, $openidStr = '')
	{
		$map['has_subscribe'] = 1;
		$map['pbid']         = get_pbid();
		$allUser              = M('public_follow')->where(wp_where($map))->column('openid', 'uid');
		$uidArr= $openidArr=[];
		foreach ($allUser as $k => $v) {
			$uidArr[$k]    = $k;
			$openidArr[$v] = $k;
		}
		if ($sendType == 0 && $groupId == 0) {
			return $allUser;
		} else if ($sendType == 0 && $groupId != 0) {
			$map1['uid'] = array(
					'in',
					$uidArr,
			);
			$map1['group_id'] = $groupId;
			$groupData        = M('auth_group_access')->where(wp_where($map1))->select();
			foreach ($groupData as $gr) {
				$data[$gr['uid']] = $allUser[$gr['uid']];
			}
			return $data;
		} else if ($sendType == 1) {
			$openids = wp_explode($openidStr);
			foreach ($openids as $op) {
				$uid = $openidArr[$op];
				if ($uid) {
					$data[$uid] = $op;
				} else {
					$this->error('Openid为: ' . $op . ' 的用户不存在');
				}
			}
			return $data;
		}
	}
	
}