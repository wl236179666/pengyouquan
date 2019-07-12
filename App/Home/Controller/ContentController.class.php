<?php
/**
 * Created by PhpStorm.
 * User: wangliang
 * Date: 2019/7/3
 * Time: 15:31
 */

namespace Home\Controller;

use Vendor\Page;

class ContentController extends ComController
{
    //主页说说列表
    public function publish()
    {
        $uid = session('uid');
        if(!$uid){
            if(!$uid){
                $this -> redirect("Home/User/login");
            }
        }

        if(IS_POST){
            $file = $_FILES['f1'];  //得到传输的数据,以数组的形式
            $name = $file['name'];      //得到文件名称，以数组的形式
            $upload_path = $_SERVER['DOCUMENT_ROOT']."/Public/index/img/upload/"; //上传文件的存放路径
            $text = isset($_POST['t1']) ? I('post.t1') : null;
//            if($uid){
                if($text){
                    $imgcs=1;
                    $hqtype='';
//                    $pinglunsql="insert into pengyou_content(username,content,time) values('$pengyou_user','$text','$time') ";
//                    $zxplsql=mysql_query($pinglunsql);
//                    $crId=mysql_insert_id();
//                    if($zxplsql){
//                        tishi(2,"发布成功",1000,'index.php');
//                    }

                    $data['uid'] = $uid;
                    $data['text'] = $text;
                    $data['addtime'] = time();
                    $data['check'] = 1;
                    $crId = M('content') -> add($data);

                    //当前位置
                    foreach ($name as $k=>$names){
                        $type = strtolower(substr($names,strrpos($names,'.')+1));//得到文件类型，并且都转化成小写
                        $allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
                        //把非法格式的图片去除
                        if (!in_array($type,$allow_type)){
                            unset($name[$k]);
                        }else{
                            $hqtype=$type;
                        }
                    }
                    $str = '';
                    $hqtype=".".$hqtype;
                    foreach ($name as $k=>$item){
                        $type = strtolower(substr($item,strrpos($item,'.')+1));//得到文件类型，并且都转化成小写
                        $zxname=time().rand(1,100).$hqtype;
                        if (move_uploaded_file($file['tmp_name'][$k],$upload_path.$zxname)){
                            $images='images_'.$imgcs;

//                            @$sql="update pengyou_content set $images ='$zxname' where id =$crId";
                            $str .= ','.$upload_path.time().$name[$k];
////                             echo '{"success":true,"msg":"上传成功！","img","'.$zxname.'"}';
//                            mysql_query($sql);

                            M('content') -> where(['id' => $crId]) -> setField($images,$zxname);

                            $imgcs++;
                        }else{
                            echo 'failed';
                        }
                    }
                    if($crId){
                        $this -> ajaxReturn(array('code' => 200,'msg' => '发布成功！'));

                    }
                }else{
                    //没有发表文字，需有文字
                    $this -> ajaxReturn(array('code' => 400,'msg' => '你什么都还没说呢，快去重新发布吧！'));
                }
//            }
//            else{
//                if($text){
//                    $imgcs=1;
//                    $hqtype='';
//                    $pinglunsql="insert into pengyou_content(username,content,time) values('匿名','$text','$time') ";
//                    $zxplsql=mysql_query($pinglunsql);
//                    $crId=mysql_insert_id();
//                    if($zxplsql){
//                        tishi(2,"发布成功",1000,'index.php');
//                    }
//                    //当前位置
//                    foreach ($name as $k=>$names){
//
//                        $type = strtolower(substr($names,strrpos($names,'.')+1));//得到文件类型，并且都转化成小写
//                        $allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
//                        //把非法格式的图片去除
//                        if (!in_array($type,$allow_type)){
//                            unset($name[$k]);
//                        }else{
//                            $hqtype=$type;
//                        }
//                    }
//                    $str = '';
//                    $hqtype=".".$hqtype;
//
//                    foreach ($name as $k=>$item){
//                        $type = strtolower(substr($item,strrpos($item,'.')+1));//得到文件类型，并且都转化成小写
//                        $zxname=time().rand(1,100).$hqtype;
//                        if (move_uploaded_file($file['tmp_name'][$k],$upload_path.$zxname)){
//                            $images='images_'.$imgcs;
//                            @$sql="update pengyou_content set $images ='$zxname' where id =$crId";
//                            //$str .= ','.$upload_path.time().$name[$k];
//                            // echo '{"success":true,"msg":"上传成功！","img","'.$zxname.'"}';
//                            mysql_query($sql);
//                            $imgcs++;
//                        }else{
//                            echo 'failed';
//                        }
//                    }
//                }else{
//                    tishi(1,"你什么都还没说呢，快去重新发布吧",3000,"index.php");
//                }
//            }
            //向指定id插入图片地址（虽然是插入，但是是更新字段，不要迷糊了）
        }else{
            $this -> display();
        }
    }

    //点赞
    public function zan()
    {
        $uid = session('uid');
        if(!$uid){
            if(!$uid){
                $this -> redirect("Home/User/login");
            }
        }

        $shuoshuo_id = I('post.id');
        if(!$shuoshuo_id){
            $this -> ajaxReturn(array('code' => 400,'msg' => '参数错误！'));
        }
        $qx = I('post.qx'); //有值，则为取消点赞
        if($qx){
            $res = M('content_zan') -> where(['content_id' => $shuoshuo_id,'dianzan_user' => $uid]) -> delete();
            if($res){
//                $zan_username = getNicknameById($uid);
                $zan_username_arr = M('content_zan') -> where(['content_id' => $shuoshuo_id]) -> getField('dianzan_nickname',true);
//                $zan_username = implode(',',$zan_username_arr);
                $this -> ajaxReturn(array('code' => 201,'user' => $zan_username_arr,'msg' => '取消赞操作成功！'));
            }else{
                $this -> ajaxReturn(array('code' => 500,'msg' => '取消赞操作失败'));
            }
        }else{
            //判断是否已存在记录
            $is_exist = M('content_zan') -> where(['content_id' => $shuoshuo_id,'dianzan_user' => $uid]) -> getField('id');
            if(!$is_exist){
                $data['content_id'] = $shuoshuo_id;
                $data['dianzan_user'] = $uid;
                $data['dianzan_nickname'] = getNicknameById($uid);
                $data['addtime'] = time();

                $res = M('content_zan') -> data($data) -> add();
                if($res){
                    $zan_username = getNicknameById($uid);
                    $this -> ajaxReturn(array('code' => 200,'user' => $zan_username,'msg' => '点赞操作成功！'));
                }else{
                    $this -> ajaxReturn(array('code' => 500,'msg' => '点赞操作失败！'));
                }
            }else{
                $this -> ajaxReturn(array('code' => 501,'msg' => '您已点过赞了！'));
            }
        }
    }

    //评论
    public function comment()
    {
        $uid = session('uid');
        if(!$uid){
            if(!$uid){
                $this -> redirect("Home/User/login");
            }
        }

        $shuoshuo_id = I('post.biaoshi');
        $content = I('post.content');
        $to_user = I('post.to_user');

        if(!$shuoshuo_id){
            $this -> ajaxReturn(array('code' => 400,'msg' => '参数错误！'));
        }
        if(!$content){
            $this -> ajaxReturn(array('code' => 401,'msg' => '没有内容！'));
        }

        $nickname = getNicknameById($uid);

        $data['content_id'] = $shuoshuo_id;
        $data['comment_uid'] = $uid;
        $data['comment_nickname'] = $nickname;
        $data['content'] = $content;
        $data['addtime'] = time();
        $data['to_user'] = $to_user;

        $res = M('content_comment') -> data($data) -> add();
        $to_user_nickname = null;
        if($res){
            if($to_user != 0){
                $to_user_nickname = getNicknameById($to_user);
            }
            $this -> ajaxReturn(array('code' => 200,'msg' => '发布成功！','id' => $uid,'name' => $nickname,'content' => $content,'content_id' => $shuoshuo_id,'to_user' => $to_user,'to_user_nickname' => $to_user_nickname,'dangqian_id' => $res));
        }else{
            $this -> ajaxReturn(array('code' => 500,'msg' => '发布失败！'));
        }
    }

    //删除评论
    public function comment_del()
    {
        if(IS_POST && IS_AJAX){
            $id = I('post.id');
            if(!$id){
                $this -> ajaxReturn(array('code' => 401,'msg' => '缺少参数！'));
            }
            $uid = session('uid');
            if(!$uid){
                $this -> ajaxReturn(array('code' => 402,'msg' => '您没有登录！'));
            }
            $res = M('content_comment') -> where(['id' => $id]) -> delete();
            if($res){
                $this -> ajaxReturn(array('code' => 200,'msg' => '删除成功！'));
            }else{
                $this -> ajaxReturn(array('code' => 500,'msg' => '删除失败，请重试！'));
            }
        }
    }
}