<?php
/**
 *
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class UploadController extends ComController
{
    //上传文件（头像）
    public function file()
    {
        $uid = session('uid');
        if(!$uid){
            if(!$uid){
                $this -> redirect("Home/User/login");
            }
        }

        $type = isset($_GET['type']) ? I('get.type') : 1;

        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["file"]["name"]);
//        echo $_FILES["file"]["name"];
        //echo $_FILES["file"]["size"];
        //echo $_FILES["file"]["type"];die;
        $extension = strtolower(end($temp));     // 获取文件后缀名
        if ((($_FILES["file"]["type"] == "image/gif")
                || ($_FILES["file"]["type"] == "image/jpeg")
                || ($_FILES["file"]["type"] == "image/jpg")
                || ($_FILES["file"]["type"] == "image/pjpeg")
                || ($_FILES["file"]["type"] == "image/x-png")
                || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 1145728)   // 小于 200 kb
            && in_array($extension, $allowedExts))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                echo "错误：: " . $_FILES["file"]["error"] . "<br>";
            }
            else
            {
                //echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
                //echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
                //echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                //echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"] . "<br>";

                // 判断当期目录下的 upload 目录是否存在该文件
                // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                @$date=date('Ymdhis');//得到当前时间,如;20070705163148
                @$fileName=$_FILES["file"]["name"];//得到上传文件的名字
                $name=explode('.',$fileName);//将文件名以'.'分割得到后缀名,得到一个数组
                $newPath=$date.'.'.$name[1];//得到一个新的文件为'20070705163148.jpg',即新的路径
                $oldPath=$_FILES['file']['tmp_name'];//临时文件夹,即以前的路径
                if($type == 1){
                    $file_path = $_SERVER['DOCUMENT_ROOT']."/Public/index/img/avatar/".$newPath;
                }else{
                    $file_path = $_SERVER['DOCUMENT_ROOT']."/Public/index/img/bg/".$newPath;
                }

                if (file_exists($file_path))
                {
                    //echo $_FILES["file"]["name"] . " 文件已经存在。 ";
                }
                else
                {
                    if($type == 1){
                        $mulu = $_SERVER['DOCUMENT_ROOT']."/Public/index/img/avatar";
                    }else{
                        $mulu = $_SERVER['DOCUMENT_ROOT']."/Public/index/img/bg";
                    }

                    if(!is_dir($mulu)){
                        mkdir($mulu,0777,true);
                    }
                    // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                    if($type == 1){
                        move_uploaded_file($_FILES["file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT']."/Public/index/img/avatar/".$newPath);
                        $res = M('user') -> where(['id' => $uid]) -> setField('avatar',$newPath);
                    }else{
                        move_uploaded_file($_FILES["file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT']."/Public/index/img/bg/".$newPath);
                        $res = M('user') -> where(['id' => $uid]) -> setField('background',$newPath);
                    }


                    $_SESSION['pengyou_tximg']=$newPath;
                    echo '<meta charset="utf-8">';
                    echo '<link href="/Public/index/css/yiqi.css" rel="stylesheet" />';
                    echo '<script type="text/JavaScript" src="/Public/index/js/yiqi.js"></script>';
                    echo '<body></body>';
                    echo '<script type="text/javascript">';
                    echo 'tishi(2,"更换成功",500,"/index.php/Home/Index/index");';
                    echo '</script>';
                }
            }
        }
        else
        {
            echo '<meta charset="utf-8">';
            echo '<link href="/Public/index/css/yiqi.css" rel="stylesheet" />';
            echo '<script type="text/JavaScript" src="/Public/index/js/yiqi.js"></script>';
            echo '<body></body>';
            echo '<script type="text/javascript">';
            echo 'tishi(2,"图片文件必须小于1MB",500,"/index.php/Home/Index/index");';
            echo '</script>';
        }
    }
}