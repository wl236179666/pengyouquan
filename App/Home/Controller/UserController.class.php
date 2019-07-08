<?php
/**
 *
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class UserController extends ComController
{
    //用户登录
    public function login()
    {
        if(IS_POST){
            $username = I('post.username');
            $password = I('post.password');
            if(!$username){
                $this -> ajaxReturn(array('code' => 401,'msg' => '用户名不能为空！'));
            }
            if(!$password){
                $this -> ajaxReturn(array('code' => 402,'msg' => '密码不能为空！'));
            }

            $is_exist = M('user') -> where(['username' => $username]) -> field('id,username,password') -> find();
            if(!$is_exist){
                $this -> ajaxReturn(array('code' => 403,'msg' => '用户名或密码错误！'));
            }else{
                if($password !== $is_exist['password']){
                    $this -> ajaxReturn(array('code' => 404,'msg' => '用户名或密码错误！'));
                }
            }

            session('uid',$is_exist['id']);
            $map['username'] = $is_exist['username'];
            $map['ip'] = getIP();
            $map['login_time'] = time();
            $res = M('user_login') ->data($map) -> add();
            if($res){
                $this -> ajaxReturn(array('code' => 200,'msg' => '登录成功！'));
            }else{
                $this -> ajaxReturn(array('code' => 500,'msg' => '登录失败，请重试！'));
            }

        }else{
            $this->display();
        }
    }

    //用户注册
    public function register()
    {
        if(IS_POST && IS_AJAX){
           $username = I('user');
           $password = I('pass');
           $repassword = I('okpass');
           $mail = I('email');

           if(!$username){
               $this -> ajaxReturn(array('code' => 401,'msg' => '用户名不能为空！'));
           }
            if(!$password){
                $this -> ajaxReturn(array('code' => 402,'msg' => '密码不能为空！'));
            }else{
                if($password !== $repassword){
                    $this -> ajaxReturn(array('code' => 403,'msg' => '两次密码输入不一致！'));
                }
            }
            if(!$username){
                $this -> ajaxReturn(array('code' => 404,'msg' => '邮箱不能为空！'));
            }

            $data['username'] = $username;
            $data['password'] = $password;
            $data['mail'] = $mail;
            $data['addtime'] = time();

            $res = M('user') -> add($data);
            if($res){
                $this -> ajaxReturn(array('code' => 200,'msg' => '创建成功！'));
            }else{
                $this -> ajaxReturn(array('code' => 500,'msg' => '创建失败，请刷新后重试！'));
            }
        }else{
            $this->display();
        }
    }

    //修改用户信息
    public function userinfo()
    {
        $uid = session('uid');
        if(!$uid){
            $this -> redirect("Home/User/login");
        }

        if(IS_POST && IS_AJAX){
            $nickname = isset($_POST['nickname']) ? I('post.nickname') : null;
            $qq = isset($_POST['qq']) ? I('post.qq') : null;
            $mail = isset($_POST['mail'])? I('post.mail') : null;
            $age = isset($_POST['age']) ? I('post.age') : null;
            $phone = isset($_POST['phone']) ? I('post.phone') : null;
            $gender = isset($_POST['gender']) ? I('post.gender') : 3;

            $data['nickname'] = $nickname;
            $data['qq'] = $qq;
            $data['mail'] = $mail;
            $data['age'] = $age;
            $data['phone'] = $phone;
            $data['gender'] = $gender;

            $res = M('user') -> where(['id' => $uid]) -> save($data);
            if($res){
                $this -> ajaxReturn(array('code' => 200,'msg' => '操作成功！'));
            }else{
                $this -> ajaxReturn(array('code' => 500,'msg' => '未修改内容！'));
            }

        }else{
            $info = M('user') -> where(['id' => $uid]) -> field('username,nickname,qq,mail,phone,gender,age,avatar,background,addtime') -> find();

            $this -> assign('info',$info);
            $this -> display();
        }
    }
}