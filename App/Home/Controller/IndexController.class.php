<?php
/**
 *
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class IndexController extends ComController
{
    public function index()
    {
//        session('uid',null);
        $uid = session('uid');
        if($uid){
            $info = M('user') -> where(['id' => $uid]) -> field('username,nickname,avatar,background') -> find();
            $this -> assign('uid',$uid);
            $this -> assign('info',$info);
        }

        //获取说说列表
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if(!is_numeric($page)){
            $this -> error('参数错误！');
        }
        //每页显示的条数
        $pageSize = 20;
        $limit = ($page - 1) * $pageSize;
        $list=M('content')->where(['check' => 1])->order('addtime desc')->limit($limit.','.$pageSize)->select();
        foreach($list as $key  => $val){
            //每条说说的图片
            for($i=1;$i<10;$i++){
                if($val['images_'.$i] != null){
                    $list[$key]['pic'][] = $val['images_'.$i];
                    unset($list[$key]['images_'.$i]);
                }else{
                    break;
                }
            }
            //点赞用户
            $zan_list_nickname = M('content_zan') -> where(['content_id' => $val['id']]) -> order('id asc') -> getField('dianzan_nickname',true);
            $zan_list_uid = M('content_zan') -> where(['content_id' => $val['id']]) -> getField('dianzan_user',true);
            $list[$key]['dianzan_list_nickname'] = $zan_list_nickname;
            $list[$key]['dianzan_list_user'] = $zan_list_uid;
            //说说评论
            $comment_list = M('content_comment')
                -> where(['content_id' => $val['id']])
                -> field('id,comment_uid,comment_nickname,content,to_user')
                -> order('id asc')
                -> select();

            $list[$key]['comment'] = $comment_list;
        }
//        var_dump($list[0]['comment']);die;
        $this -> assign('list',$list);
        $this->display();
    }
}