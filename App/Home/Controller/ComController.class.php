<?php
/**
 *
 * 功能说明：前台公用控制器。
 *
 **/

namespace Home\Controller;

use Think\Controller;

class ComController extends Controller
{

    public function _initialize()
    {
        C(setting());
        /*
        $links = M('links')->limit(10)->order('o ASC')->select();
        $this->assign('links',$links);
        */
    }
}