/*
2015-11-13 浙江省新昌县城西小学 唐明 MOBI:13858591229

*/
$(document).ready(init);

var mask_div;
var dialog_div;

function init(){
	mask_div=$('<div class="__mask" id="__mask_div"></div>');
	dialog_div=$('<div class="__dialog" id="__dialog_div">'+
	'<button class="__close_btn" onclick="hide_dialog()">&#10005;</button><div class="__content" id="__content_div"></div>'+
	'</div>');
	var body=$(document.body);
	body.append(dialog_div);
	body.append(mask_div);
	$('#admin_logout').click(ajax_admin_logout);
	$('#new_folder').click(show_new_folder_dialog);
	$('#file_upload').on('change',upload_files);
	$('.check_box').on('selectstart',function(){
		return false;
	});
	$('.check_box').click(function(){
		var obj=$(this);
		var selected=obj.attr('data');
		if(selected>0){
			$(this).html('&#9744;');
			obj.attr('data',0);
		}else{
			$(this).html('&#9745;');
			obj.attr('data',1);
		}
	});
	
	$('#delete_btn').click(delete_all);
	$('#select_all').click(select_all);
	$('#select_none').click(select_none);
	$('#rename_btn').click(rename);
}

function rename(){
	var dirs=$('.dirclass');
	var files=$('.fileclass');
	var f_ids=[];
	for(var i=0,len=files.length;i<len;i++){
		var file=$($(files[i]).find('.check_box')[0]);
		if(file.attr('data')>0){
			f_ids[f_ids.length]=file.attr('dataid');
		}
	}
	var d_ids=[];
	for(var i=0,len=dirs.length;i<len;i++){
		var dir=$($(dirs[i]).find('.check_box')[0]);
		if(dir.attr('data')>0){
			d_ids[d_ids.length]=dir.attr('dataid');
		}
	}
	if(f_ids.length+d_ids.length>1){
		alert('只能对一个文件夹或一张图片改名！');
		return;
	}
	var new_name=prompt('请输入新名字：');
	var f_type=0,f_id=0;
	if(f_ids.length>0){
		f_id=f_ids[0];
	}else{
		f_ids=d_ids[0];
		f_type=1;
	}
	$.post('ajax_rename.php',{f_ids:f_ids,f_type:f_type,new_name:new_name},function(ret){
		var d=$.parseJSON(ret);
		if(d.code==200){
			window.location.reload();
		}else{
			alert(d.data);
		}
	});
}

function select_all(){
	var files=$('.check_box');
	for(var i=0,len=files.length;i<len;i++){
		var file=$(files[i]);
		file.html('&#9745;');
		file.attr('data',1);
	}
}

function select_none(){

	var files=$('.check_box');
	for(var i=0,len=files.length;i<len;i++){
		var file=$(files[i]);
		file.html('&#9744;');
		file.attr('data',0);
	}
}

//删除所有选中的目录或图片
function delete_all(){
	var dirs=$('.dirclass');
	var files=$('.fileclass');
	//删除所有图片
	var f_ids=[];
	for(var i=0,len=files.length;i<len;i++){
		var file=$($(files[i]).find('.check_box')[0]);
		if(file.attr('data')>0){
			f_ids[f_ids.length]=file.attr('dataid');
		}
	}
	var d_ids=[];
	for(var i=0,len=dirs.length;i<len;i++){
		var dir=$($(dirs[i]).find('.check_box')[0]);
		if(dir.attr('data')>0){
			d_ids[d_ids.length]=dir.attr('dataid');
		}
	}
	if(f_ids.length+d_ids.length>0){
		if(confirm('真的要删除这些图片或文件夹吗？')){
			var d=d_ids.join(',');
			var f=f_ids.join(',');
			$.post('ajax_delete.php',{dirs:d,files:f,parent_id:path_id},function(ret){
				var d=$.parseJSON(ret);
				if(d.code==200){
					window.location.reload();
				}else{
					alert(d.data);
				}
			});
		}
	}else{
		alert('请选中文件或文件夹，再删除！');
	}
}

function upload_files(){
	//console.log(this.files);
	var fileList = this.files;
	var upload=new $.upload_muti(fileList,'../upload_do.php',{max:2000,callback:upload_close,post_data:{path_id:path_id}});
	upload.start();
}

function upload_close(){
	window.location.reload();
}
//管理ajax登录
function ajax_admin_logout(){
	$.post('ajax_logout.php',{},function(ret){
		window.location.href='../index.php';
	}).error(function (){
		alert('网络不正常，请检查网络！');
	});
}

function ajax_create_folder(){
	var folder_name=dialog_div.find('#folder_name').val();
	if(folder_name=='') {
		alert('请输入文件夹名称！');
		return false;
	}
	$.post('ajax_create_folder.php',{folder_name:folder_name,path_id:path_id},function(ret){
	//console.log(ret);
		var d=$.parseJSON(ret);
		hide_dialog();
		if(d){
			if(d.code==200){
				window.location.reload();
			}else{
				alert(d.info);
			}
		}
	}).error(function (){
		alert('网络不正常，请检查网络！');
	});
}

function show_new_folder_dialog(){
	var new_folder_html='<ul class="create_folder_ul"><li>文件夹名：<input type="text" id="folder_name" /></li>'+
	'<li><button onclick="ajax_create_folder()" class="red_button">创建文件夹</button></li></ul>';
	show_dialog(new_folder_html);
	$('#folder_name').focus();
	$('#folder_name').on('keyup',function(evt){
		//console.log(evt);
		if(evt.keyCode==13){
			ajax_create_folder();
		}
	});
}


function hide_dialog(){
	mask_div.hide();
	dialog_div.hide();
}

function show_dialog(html){
	mask_div.css('width',$(document).width()+'px');
	mask_div.css('height',$(document).height()+'px');
	mask_div.show();
	dialog_div.find('#__content_div').html(html);
	dialog_div.show();
	//调整到中央
	var sw=$(window).width();
	var sh=$(window).height();
	dialog_div.css('left',(sw-dialog_div.width())/2);
	dialog_div.css('top',(sh-dialog_div.height())/2);
}


