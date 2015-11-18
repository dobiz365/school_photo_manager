<?php
session_start();

include('../modle.class.php');

if(isset($_SESSION['admin'])){
	if(isset($_POST['f_ids']) && isset($_POST['f_type']) && isset($_POST['new_name']) && is_numeric($_POST['f_ids'])
		&& is_numeric($_POST['f_type'])){
		$folder=new Folder(0);
		$ret=$folder->rename_by_id($_POST['f_ids'],$_POST['f_type'],$_POST['new_name']);
		if($ret['code']==200){
			echo '{"code":200,"info":"成功！","data":""}';
		}else{
			echo '{"code":102,"info":"数据库错误！","data":""}';
		}
	}else{
		echo '{"code":101,"info":"参数不正确！","data":""}';
	}
}else{
	echo '{"code":100,"info":"未登录，请先登录！","data":""}';
}
?>