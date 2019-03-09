// JavaScript Document dialog
/**
 *(**************************** 通用对话框************************* 
 */
(function(){
	var elemDialog, elemOverlay, elemContent, elemTitle,
		inited = false,
		body = document.compatMode && document.compatMode !== 'BackCompat' ?
					document.documentElement : document.body,
		cssFixed;
	
	function init(){
		if (!inited){
			createOverlay();
			createDialog();
			inited = true;
		}
	}
	
	function createOverlay(){
		if (!elemOverlay){
			elemOverlay = $('<div class="box_overlay"></div>');
			$('body').append(elemOverlay);
		}
	}
	function createDialog(){
		if (!elemDialog){
			
					elemDialog = $('<div class="dialog">'+
						'<div class="dialog_head"><span class="dialog_title"></span><span class="dialog_close" onclick="$.Dialog.close();"></span></div>'+
						'<div class="dialog_content"></div>'+
						'</div>');
					
					elemContent = $('.dialog_content', elemDialog);
					elemTitle = $('.dialog_title', elemDialog);
					$('body').append(elemDialog);
					elemDialog.show();
					
				
		}
	}
	function open(){
		elemDialog.fadeIn();
		elemOverlay.fadeIn();
		//$('select').hide();
	}
	function close(){
		elemDialog.fadeOut();
		if(elemOverlay)elemOverlay.fadeOut();
		elemContent.empty();
		//$('select').show();
	}
	
	function setHtml(html){
		elemContent.html(html);
	}	
	function setTitle(title){
		elemTitle.html(title);
		}
	function setOpts(opts){
		elemDialog.css({width:opts.width,height:opts.height});
		elemDialog.css("margin-left",-opts.width/2);
		elemDialog.css("margin-top",-opts.height/2);
		}
	var Dialog = {
		loading:function(content){
			// loading文字
			var loading_text = '请稍候';
			loading_text ? loading_text=content : loading_text;
			
			$.showLoading(loading_text)
			setTimeout(function () {
			    $.hideLoading();
			}, 3000);

		},
		success:function(successTips){
			 successTips?successTips:successTips="操作成功";
			if(arguments[0]!=null)successTips = arguments[0];
			$.toast(successTips);


			},
		fail:function(failTips){
		 	failTips?failTips:failTips="操作失败";
			if(arguments[0]!=null)failTips = arguments[0];
			$.toast(failTips, "cancel");
		},
		confirm:function(title,msg,callback,jump_url){

				$.confirm(msg, { 
					title: title,
					buttons: [{
				        label: 'NO',
				        type: 'default',
				        onClick: function(){
				        	// no

				        }
				    }, {
				        label: 'YES',
				        type: 'primary',
				        onClick: function(){
				        	// yes
				        	if(callback) {
				        		callback();
				        	}
				        	else {
				        		var confirmDom = $.confirm('手动关闭的confirm', function(){
				        		    return false; // 不关闭弹窗，可用confirmDom.hide()来手动关闭
				        		});
				        		confirmDom.hide();
				        	}
				        }
				    }]

			 	});
			},

		confirmBox:function(title,msg,opts){
				$.confirm(msg, title, function(){
					if(opts.rightCallback) {
						// 执行回调
						opts.rightCallback();
					}
				        	
				}, function() {
					// 取消了
				});				
			},	
		open: function(title,opts,html){
			init();
			setOpts(opts);
			setTitle(title);
			setHtml(html);
			open();
		},
		openHtml: function(html){
			init();
			setHtml(html);
			open();
		},
		close: close
	};
	
	$.extend($,{Dialog: Dialog});
	
})();