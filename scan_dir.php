<?php
/*
2015-11-18 浙江省新昌县城西小学 唐明 QQ；147885198
功能：照片数据迁移程序
	本程序可以将已有的照片数据整体迁移到数据库中，并按照原有的目录结构进行排列，也可以将照片挂到某个目录下面，进行增量添加。

使用方法：
	1.设置好scan_dir.php中的参数，$src_path表示源文件目录，$pid表示要挂在数据库中的哪个目录下，0表示根目录。
	2.环境变量path中添加php的路径。保证可以在scan_dir.php文件夹中可以运行PHP程序。
	3.在scan_dir.php文件夹中打开CMD窗口，并运行php scan_dir.php
	4.等待程序运行完成。
	因为照片数据可能较多，无法在短时间内转移完成，所以不能在网页上运行PHP程序（有脚本运行时间限制）。在命令行上运行则没有这样的限制。

	本程序支持png、jpg、gif三种格式图片的迁移，并会在程序中进行过滤。而且会过滤small文件夹。使用时请注意。
*/
	include('config.php');

	$s='mysql:host='.DB_HOST.';dbname='.DB_NAME;
	$conn= new PDO($s,DB_USER,DB_PWD);
	$conn->exec("set names utf8");
	$allow_ext=array('png','jpg','gif');
	
	$src_path='D:/photo/';//要迁移的照片文件夹
	$pid=0;//需要挂到哪个文件夹下面，这个文件夹在数据库中的id
	scan_dir($conn,$src_path,$pid,$allow_ext);
	

	function scan_dir($conn,$path,$pid,$allow_ext){
		$target_path='./photo/';
		$dirs=scandir($path);
		foreach($dirs as $file){
			if($file!='.' && $file!='..'){
				if(is_dir($path.$file)){
					if($file!='small'){
						echo $file;
						$ret=add_dir_byid($conn,$pid,$file,'');
						if($ret['code']==200){
							//建立目录
							mkdir($target_path.'img_'.$ret['data']);
							mkdir($target_path.'img_'.$ret['data'].'/small');
							//递归描述目录
							scan_dir($conn,$path.$file.'/',$ret['data'],$allow_ext);
						}
					}
				}else{
					$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
					if(in_array($ext,$allow_ext)){
						//复制文件
						$ori_file=$path.$file;
						$src=$target_path.'img_'.$pid.'/'.$file;
						copy($ori_file,$src);
						//创建缩略图
						$prev_img=$target_path.'img_'.$pid.'/small/'.$file;
						$f_size=filesize($ori_file);
						$size=make_thumb($src,$prev_img,200);
						//加入数据库
						add_img_byid($conn,$pid,$file,$src,$prev_img,$f_size,$size[0],$size[1]);
					}
				}
			}
		}
		
	}

function add_dir_byid($conn,$parent_id,$name,$icon){
	//$name=self::filter_sql($name);
	$sql="insert into m_dir (id,name,size,cdate,icon,parent_id,flag) values(null,'{$name}',0,now(),'',{$parent_id},0)";
	$sql=iconv('GB2312', 'UTF-8', $sql);
	if($conn->exec($sql)>0){
		$id=$conn->lastInsertId();
		return array('code'=>200,'info'=>'添加目录成功！','data'=>$id);
	}else{
		return array('code'=>104,'info'=>'数据库错误，添加目录失败！','data'=>'');
	}
}	

function add_img_byid($conn,$parent_id,$name,$src,$prev_img,$size,$width,$height){
	//$name=self::filter_sql($name);
	$sql="insert into m_file (id,name,size,cdate,prev_img,src,flag,parent_id,width,height) values (null,'{$name}',{$size},now(),'{$prev_img}','{$src}',0,{$parent_id},{$width},{$height})";
	//echo $sql;
	//echo $sql;
	$sql=iconv('GB2312', 'UTF-8', $sql);
	if($conn->exec($sql)>0){
		return array('code'=>200,'info'=>'添加图片成功！','data'=>'');
	}else{
		return array('code'=>105,'info'=>'数据库错误，添加图片失败！','data'=>'');
	}		
}

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