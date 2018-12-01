// JavaScript Document dialog
/**
 *(**************************** 素材管理弹框 ************************* 
 */
(function(){
	var elemBase,elemBody,elemLocal,elemWeb, elemOnline, elemIcon, elemLogo,options;
	var selectPicData = [];
	function initBase(){
		elemBase = $('<div class="image_manager">'
				+'<div class="image_nav"><a href="javascript:;" class="upload_local cur">上传照片</a><a href="javascript:;" class="upload_web">网址上传</a><a href="javascript:;" class="online">在线图库</a><a href="javascript:;" class="icon">图标</a><a href="javascript:;" class="background">背景</a><a href="javascript:;" class="logo">LOGO</a></div>'
				+'<div class="image_body"></div>'
				+'<div class="image_footer"><button class="btn confirm fr">确定</button></div>'
				+'</div>');	
		$('.upload_web',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initWeb();
		});
		$('.upload_local',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initLocal();
		});
		$('.online',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initOnline();
		});
		$('.icon',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initOnline();
		});
		$('.background',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initOnline();
		});
		$('.logo',elemBase).click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			initOnline();
		});
		
		$('.confirm',elemBase).click(function(){
			if(options.type==0 && options.muti==false && selectPicData.length>0){
				$("#"+options.picId).val(selectPicData[0].id);
				$("#"+options.previewId).html('<div class="upload-pre-item"><img width="120" height="120" src="' + selectPicData[0].src + '"/></div>').show();
			}
			
			$.Dialog.close();
		});
	}
	
	function initLocal(){
		if(!elemLocal)elemLocal = $('<div class="upload_wrap" style="width:220px;"><div class="local"><input type="file" id="upload_picture_file"></div><div class="preview"></div></div>');
		elemBody.html(elemLocal);
		$('.upload_wrap .web',elemLocal).click(function(){
			$('.upload_wrap .local',elemLocal).hide();
			});
		$("#upload_picture_file",elemLocal).uploadify({
							        "height"          : 40,
							        "swf"             : STATIC+"/uploadify/uploadify.swf",
							        "fileObjName"     : "download",
							        "buttonText"      : "上传图片",
							        "uploader"        : options.uploadUrl,
							        "width"           : 120,
							        'removeTimeout'	  : 1,
							        'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
							        "onUploadSuccess" : function(file, data){
										var data = $.parseJSON(data);
							    		var src = '';
										if(data.code){
											src = data.url || ROOT + data.path;
											$(".preview",elemLocal).html('<img src="'+src+'"/>');
											var json = new Object();
											json.src = src;
											json.id = data.id;
											selectPicData.push(json);
										} else {
											updateAlert(data.msg);
											setTimeout(function(){
												$('#top-alert').find('button').click();
											},1500);
										}
									}
							    });
	}
	function initWeb(){
		if(!elemWeb)elemWeb = $('<div class="upload_wrap" style="width:520px;"><div class="web"><input type="text" id="webPicLink"/><button class="btn">上传 </button></div></div>');
		$('.upload_wrap .web',elemWeb).click(function(){
			$('.upload_wrap .local',elemWeb).hide();
			});
		elemBody.html(elemWeb);
	}
	function initOnline(){
		if(!elemOnline)elemOnline = $('<div class="online_wrap"><div class="pic_list"></div><div class="image_page"></div></div>');
		var elemCate = $('<div class="cate_wrap"><div class="cate_list"></div><div class="cate_switch">选择分类</div></div>');
		var elemList = $('.pic_list',elemOnline);
		var elemPage = $('.image_page',elemOnline);
		var elemCateSwitch = $('.cate_switch',elemCate);
		var elemCateList = $('.cate_list',elemCate);
		elemCateSwitch.click(function(){
			if(!elemCateSwitch.hasClass('open')){
					elemCateList.show();
					elemCateSwitch.text("关闭分类").hide().show();
					elemCateSwitch.addClass("open");
				}else{
					elemCateList.hide();
					elemCateSwitch.text("选择分类");
					elemCateSwitch.removeClass("open");
					}
			});
		
		//类别加载
		elemCateList.html("");
		for(var i=0;i<21;i++){
			var $cate = $('<a href="javasctip:;">小图标</a>');
			elemCateList.append($cate);
			$cate.click(function(){
				
				alert("选择了分类 "+$(this).text());
				elemCateList.hide();
				elemCateSwitch.removeClass("open").text("选择图标");
				});
			}
		
		
		//图片加载
		 
		var imageCount = 100;
		var pages = Math.ceil(imageCount/21);
		elemList.html("");
		for(var i=0;i<20;i++){
			var $img = $('<img src="http://gtms01.alicdn.com/tps/T1f5KiFCRcXXXXXXXX_!!0-item_pic.jpg_160x160.jpg"/>');
			elemList.append($img);
			$img.click(function(){
				$(this).addClass('select').siblings().removeClass('select');
				});
			}
		//页码
		elemPage.html("");
		for(var i=0;i<pages;i++){
			var $a = $('<a href="javascript:;" id="p_p_'+parseInt(i+1)+'">'+parseInt(i+1)+'</a>');
			if(i==0){
				$a.addClass('cur');
				}
			elemPage.append($a);
			$a.click(function(){
				loadImage(1);
				});
		}
		elemBody.html(elemOnline);
		elemBody.append(elemCate);
	}
	function init(data){
		options = data;
		initBase();
		$.Dialog.open("素材管理",{"width":800,"height":520},elemBase);
		elemBody = $('.image_body',elemBase);
		initLocal();
	}
	
	
	var ImageManager = {
		
		init:init,
		close: $.Dialog.close
	};
	$.extend({ImageManager: ImageManager});
})();