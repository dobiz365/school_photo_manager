<?php
session_start();

include('../config.php');

if(isset($_POST['admin_name']) && isset($_POST['admin_pwd'])){
	if(ADMIN_USER==$_POST['admin_name'] &&  ADMIN_PWD==$_POST['admin_pwd']){
		$_SESSION['admin']=true;
		echo '{"code":200,"info":"成功","data":""}';
	}else{
		echo '{"code":100,"info":"用户名或密码不正确！","data":""}';
	}
}else{
	echo '{"code":101,"info":"空的用户名和密码！","data":""}';
}
?>