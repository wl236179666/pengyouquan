<?php
/**
 * Created by PhpStorm.
 * User: wangliang
 * Date: 2019/7/3
 * Time: 15:31
 */

namespace Api\Controller;

use Vendor\Page;

class QqloginController extends ComController
{
    public function qq_login()
    {
        @$response_type = 'code';
        $appKey = "2b1e973729b07e46";
        $client_id = '101516388';
        $redirect_uri = URLEncode("http://www.q05.cc/api/pyapi.php");
        $state = "test";
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=$response_type&appKey=$appKey&client_id=$client_id&redirect_uri=$redirect_uri&state=$state";
        header("location:$url");
    }
}