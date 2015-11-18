<?php
/*
2015-11-18 �㽭ʡ�²��س���Сѧ ���� QQ��147885198
���ܣ���Ƭ����Ǩ�Ƴ���
	��������Խ����е���Ƭ��������Ǩ�Ƶ����ݿ��У�������ԭ�е�Ŀ¼�ṹ�������У�Ҳ���Խ���Ƭ�ҵ�ĳ��Ŀ¼���棬����������ӡ�

ʹ�÷�����
	1.���ú�scan_dir.php�еĲ�����$src_path��ʾԴ�ļ�Ŀ¼��$pid��ʾҪ�������ݿ��е��ĸ�Ŀ¼�£�0��ʾ��Ŀ¼��
	2.��������path�����php��·������֤������scan_dir.php�ļ����п�������PHP����
	3.��scan_dir.php�ļ����д�CMD���ڣ�������php scan_dir.php
	4.�ȴ�����������ɡ�
	��Ϊ��Ƭ���ݿ��ܽ϶࣬�޷��ڶ�ʱ����ת����ɣ����Բ�������ҳ������PHP�����нű�����ʱ�����ƣ�������������������û�����������ơ�

	������֧��png��jpg��gif���ָ�ʽͼƬ��Ǩ�ƣ������ڳ����н��й��ˡ����һ����small�ļ��С�ʹ��ʱ��ע�⡣
*/
	include('config.php');

	$s='mysql:host='.DB_HOST.';dbname='.DB_NAME;
	$conn= new PDO($s,DB_USER,DB_PWD);
	$conn->exec("set names utf8");
	$allow_ext=array('png','jpg','gif');
	
	$src_path='D:/photo/';//ҪǨ�Ƶ���Ƭ�ļ���
	$pid=0;//��Ҫ�ҵ��ĸ��ļ������棬����ļ��������ݿ��е�id
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
							//����Ŀ¼
							mkdir($target_path.'img_'.$ret['data']);
							mkdir($target_path.'img_'.$ret['data'].'/small');
							//�ݹ�����Ŀ¼
							scan_dir($conn,$path.$file.'/',$ret['data'],$allow_ext);
						}
					}
				}else{
					$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
					if(in_array($ext,$allow_ext)){
						//�����ļ�
						$ori_file=$path.$file;
						$src=$target_path.'img_'.$pid.'/'.$file;
						copy($ori_file,$src);
						//��������ͼ
						$prev_img=$target_path.'img_'.$pid.'/small/'.$file;
						$f_size=filesize($ori_file);
						$size=make_thumb($src,$prev_img,200);
						//�������ݿ�
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
		return array('code'=>200,'info'=>'���Ŀ¼�ɹ���','data'=>$id);
	}else{
		return array('code'=>104,'info'=>'���ݿ�������Ŀ¼ʧ�ܣ�','data'=>'');
	}
}	

function add_img_byid($conn,$parent_id,$name,$src,$prev_img,$size,$width,$height){
	//$name=self::filter_sql($name);
	$sql="insert into m_file (id,name,size,cdate,prev_img,src,flag,parent_id,width,height) values (null,'{$name}',{$size},now(),'{$prev_img}','{$src}',0,{$parent_id},{$width},{$height})";
	//echo $sql;
	//echo $sql;
	$sql=iconv('GB2312', 'UTF-8', $sql);
	if($conn->exec($sql)>0){
		return array('code'=>200,'info'=>'���ͼƬ�ɹ���','data'=>'');
	}else{
		return array('code'=>105,'info'=>'���ݿ�������ͼƬʧ�ܣ�','data'=>'');
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