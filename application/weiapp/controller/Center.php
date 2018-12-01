<?php

namespace app\weiapp\controller;

use app\common\controller\ApiBase;

class Center extends ApiBase
{
    /*
     * 获取用户信息
     */
    public function getUserInfo()
    {
        if (isset($_REQUEST['sub_redirect_key'])) {
            $uid = think_decrypt(I('sub_redirect_key'));
        } else {
            $uid = I('uid', 0, 'intval');
        }

        $user = [];
        if ($uid) {
            $user = D('common/User')->getUserInfo($uid);
        }
        echo api_return(0, $user);
    }

    /*
     * 根据token获取公众号或小程序信息
     */
    public function getAppInfo()
    {
        $public_id = I('sub_app_token', '');
        $pbid = M('publics')->where('public_id', $public_id)->value('id');
        $appInfo = get_pbid_info($pbid);
        if (!empty($appInfo)) {
            // 小程序返回accesstoken 用于首次绑定时绑定域名
            if ($appInfo['app_type'] == 1) {
                $appInfo['access_token'] = get_access_token($pbid);
            }

        } else {
            $appInfo = [];
        }
        echo api_return(0, $appInfo);
    }

    /*
     * 获取一键绑定的AccessToken
     */
    public function getAccessToken()
    {
        $public_id = I('sub_app_token', '');
        $pbid = M('publics')->where('public_id', $public_id)->value('id');
        $access_token = get_access_token($pbid);
        echo $access_token;
    }

    /*
     * 获取代码模版库中的所有小程序代码模版
     */
    public function getTemplateList()
    {
        $data = D('PublicBind/PublicBind')->getTemplateList();
        echo $data;
    }

    /*
     * 获取第三方平台的component_access_token、第三方平台appid
     */
    public function getComponentInfo()
    {
        $data['access_token'] = D('PublicBind/PublicBind')->_get_component_access_token();
        $data['appid'] = config('COMPONENT_APPID');
        echo api_return(0, $data);
    }

    /*
     * 取消公众号绑定的平台
     */
    public function delSubProject()
    {
        $id = I('apps_id', '');
        $map['id'] = $id;
        $save['sub_project'] = '';
        $res = M('publics')->where($map)->save($save);
        D('Common/Publics')->clear($id);
        echo $res;
    }
}

