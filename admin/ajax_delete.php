<?php
session_start();

include('../modle.class.php');

if(isset($_SESSION['admin'])){
	if(isset($_POST['dirs']) && isset($_POST['files']) && isset($_POST['parent_id']) && is_numeric($_POST['parent_id'])){
		$folder=new Folder($_POST['parent_id']);
		if($_POST['files']!=''){
			$folder->delete_img($_POST['files']);
		}
		if($_POST['dirs']!=''){
			$dirs=explode(',',$_POST['dirs']);
			foreach($dirs as $dir){
				$folder->set_id($dir);
				$folder->delete_self();
			}
		}
		echo '{"code":200,"info":"�ɹ���","data":""}';
		
	}else{
		echo '{"code":102,"info":"��������","data":""}';
	}
}else{
	echo '{"code":101,"info":"û�е�¼�����¼��","data":""}';
}
?>