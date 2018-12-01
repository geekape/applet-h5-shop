<?php

namespace app\draw\controller;

use app\common\controller\WebBase;

class Draw extends WebBase
{
    public function initialize()
    {
        parent::initialize();

        $controller = strtolower(CONTROLLER_NAME);

        // $res['title'] = '抽奖活动';
        // $res['url'] = U ( 'Draw/Draw/lists');
        // $res['class'] = $controller == 'draw' ? 'current' : '';
        // $nav[] = $res;

        /* $res ['title'] = '奖品管理';
        $res ['url'] = U ( 'Draw/Award/lists',$this->get_param );
        $res ['class'] = $controller == 'award' ? 'current' : '';
        $nav [] = $res; */

        $this->assign('nav', null);
    }
    public function lists()
    {
        $url = U ( 'Draw/Award/lists', $this->get_param);
        return redirect($url);
    }
}
