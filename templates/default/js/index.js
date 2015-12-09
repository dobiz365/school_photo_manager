/*
2015-11-13 浙江省新昌县城西小学 唐明 MOBI:13858591229
2015-12-7
	增加对键盘左右方向键和ESC键的处理
	修复图片显示位置错误
*/
$(document).ready(init);

var mask_div;
var dialog_div;
var show_image;
var arrow_left;
var arrow_right;
var close_image_btn;
var show_flag=false;

function init(){
	mask_div=$('#tm__mask_div');
	dialog_div=$('#tm__dialog_div');
	show_image=$('#show_image');
	arrow_left=$('#arrow_left');
	arrow_right=$('#arrow_right');
	close_image_btn=$('#close_image_btn');
	var body=$(document.body);
	
	$('#admin_login').click(show_login_dialog);
	//$('.image_a').click(set_image);
	$('#show_image').click(hide_show_image);
	arrow_left.click(function(){
		prve();
	});
	
	arrow_right.click(function(){
		next();
	});	
	close_image_btn.click(function(){
		hide_show_image();
	});
	//绑定键盘事件
	$(document).bind('keyup',function(e){
		if(show_flag){
			if(e.keyCode==39){//right
				next();
			}else if(e.keyCode==37){//left
				prev();
			}else if(e.keyCode==27){//ese
				hide_show_image();
			}
			//console.log(e);
			return true;
		}
	});
}

function next(){
	if(image_index<image_count-1){
			image_index++;
			show_image_func();
		}else{
			alert('到尾了！');
		}
}

function prev(){
	if(image_index>0){
			image_index--;
			show_image_func();
		}else{
			alert('到头了！');
		}
}

//管理ajax登录
function admin_login_ajax(){
	var admin_name=dialog_div.find('#admin_name').val();
	var admin_pwd=dialog_div.find('#admin_pwd').val();
	if(admin_name=='' || admin_pwd==''){
		alert('请输入用户名或密码！');
		return;
	}
	$.post('admin/ajax_login.php',{admin_name:admin_name,admin_pwd:admin_pwd},function(ret){
		var d=$.parseJSON(ret);
		hide_dialog();
		if(d){
			if(d.code==200){
				window.location.href='admin/index.php';
			}else{
				alert(d.info);
			}
		}
	}).error(function (){
		alert('网络不正常，请检查网络！');
	});
}


function show_login_dialog(){	
	var login_dialog_html='<ul class="login_ul"><li>用户名：<input type="text" id="admin_name" /></li>'+
	'<li>密　码：<input type="password" id="admin_pwd" /></li>'+
	'<li><button onclick="admin_login_ajax()" class="red_button">　登　录　</button></li></ul>';
	show_dialog(login_dialog_html);
	$('#admin_name').focus();
	$('#admin_pwd').on('keyup',function(evt){
		if(evt.keyCode==13){
			admin_login_ajax();
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
	dialog_div.find('#tm__content_div').html(html);
	dialog_div.show();
	//调整到中央
	var sw=$(window).width();
	var sh=$(window).height();
	var scrollx=$(document).scrollLeft();
	var scrolly=$(document).scrollTop();
	var x=scrollx+(sw-dialog_div.width())/2;
	var y=scrolly+(sh-dialog_div.height())/2;
	//alert(x+','+y);
	dialog_div.css('left',x+'px');
	dialog_div.css('top',y+'px');
}


function set_image(idx){
	image_index=idx;
	show_image_func();
}

function show_image_func(){
	mask_div.css('width',$(document).width()+'px');
	mask_div.css('height',$(document).height()+'px');
	mask_div.css('left','0px');
	mask_div.css('top','0px');
	mask_div.show();
	var sw=$(window).width();
	var sh=$(window).height();
	var scrolltop=$(document).scrollTop();
	var scrollleft=$(document).scrollLeft();
	arrow_left.height(sh);
	arrow_left.css('top',scrolltop+'px');
	arrow_left.css('line-height',sh+'px');
	arrow_right.css('line-height',sh+'px');
	arrow_right.height(sh);
	arrow_right.css('left',sw-50+'px');
	arrow_right.css('top',scrolltop+'px');
	arrow_left.css('display','block');
	arrow_right.css('display','block');
	close_image_btn.css('display','block');
	var data=images[image_index];
	var max_w=sw-120,max_h=sh-10;
	var w=max_w,
		h=max_w*1.0/data[1]*data[2];
	if(h>max_h){
		h=max_h,
		w=max_h*1.0/data[2]*data[1]
	}
	show_image.attr('width',w-20);
	show_image.attr('height',h-20);
	var image_left=scrollleft+(sw-w)/2;
	var image_top=scrolltop+(sh-h)/2;
	show_image.css('left',image_left+'px');
	show_image.css('top',image_top+'px');
	close_image_btn.css('top',image_top+10+'px');
	close_image_btn.css('left',image_left+w-24-10+'px');
	show_image.css('display','block');
	var html='<img src="'+data[0]+'" width="'+(w-20)+'px" height="'+(h-20)+'px" style="border:none;" />';
	show_image.html(html);
	show_flag=true;
	
}

function hide_show_image(){
	mask_div.hide();
	show_image.hide();
	arrow_left.hide();
	arrow_right.hide();
	close_image_btn.hide();
	show_flag=false;
}

