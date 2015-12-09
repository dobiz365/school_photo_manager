<?php
session_start();
header("content=text/html; charset=utf-8");
include('modle.class.php');
if(isset($_SESSION['admin'])){
	if(isset($_POST['data']) && isset($_POST['path_id']) && isset($_POST['filename'])  && is_numeric($_POST['path_id'])){
		$f=base64_decode($_POST['data']);
		$fname=date('Ymdhis').rand(1000,9999).'.jpg';
		$upload_dir='photo/';
		if(!is_dir($upload_dir)){
			mkdir($upload_dir);
		}
		$path=$upload_dir.'img_'.$_POST['path_id'].'/';
		$small_path=$path.'small/';
		if(!is_dir($path)){
			mkdir($path);
			mkdir($small_path);
		}
		file_put_contents($path.$fname, $f);
		//生成缩略图
		$img_size=make_thumb($path.$fname,$small_path.$fname,200);
		//写入数据库
		//var_dump($img_size);
		$size=strlen($f);
		$folder=new Folder($_POST['path_id']);
		$ret=$folder->add_img($_POST['filename'],$path.$fname,$small_path.$fname,$size,$img_size[0],$img_size[1]);
		echo json_encode($ret);
	}
}

/**
 * 生成缩略图函数（支持图片格式：gif、jpeg、png）
 * @param  string $src      源图片路径
 * @param  string $dest		目标缩略图路径
 * @param  int    $width    缩略图宽度（只指定高度时进行等比缩放）

 */
function make_thumb($src,$dest,$width=200) {
    $size = getimagesize($src);
    if (!$size)
        return false;

    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    switch($src_type) {
        case 1 :
            $img_type = 'gif';
            break;
        case 2 :
            $img_type = 'jpeg';
            break;
        case 3 :
            $img_type = 'png';
            break;
        default :
            return false;
    }
    $height = $src_h * ($width / $src_w);

    $imagecreatefunc = 'imagecreatefrom' . $img_type;
    $src_img = $imagecreatefunc($src);
    $dest_img = imagecreatetruecolor($width, $height);
    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

    $imagefunc = 'image' . $img_type;

    $imagefunc($dest_img, $dest);
    
    imagedestroy($src_img);
    imagedestroy($dest_img);
    return array($src_w,$src_h);
}
?>