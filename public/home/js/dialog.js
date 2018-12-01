// JavaScript Document dialog
/**
 *(**************************** 通用对话框************************* 
 */
(function(){
	var elemDialog, elemOverlay, elemContent, elemTitle,options,
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
			elemOverlay = $('<div class="box_overlay" onclick="$.Dialog.close();"></div>');
			$('body').append(elemOverlay);
		}
	}
	function createDialog(){
		if (!elemDialog){
			if (!elemDialog){
					elemDialog = $('<div class="dialog">'+
						'<div class="dialog_head"><span class="dialog_title"></span><span class="dialog_close" onclick="$.Dialog.close();"></span></div>'+
						'<div class="dialog_content"></div>'+
						'</div>');
					elemContent = $('.dialog_content', elemDialog);
					elemTitle = $('.dialog_title', elemDialog);
					$('body').append(elemDialog);
					elemDialog.fadeIn(300)
				}
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
		if(options.closeCallback){
			options.closeCallback();
		}
		//$('select').show();
	}
	
	function setHtml(html){
		elemContent.html(html);
	}	
	function setTitle(title){
		elemTitle.html(title);
		}
	function setOpts(opts){
		options = opts;
		elemDialog.css({width:opts.width,height:opts.height});
		elemDialog.css("margin-left",-opts.width/2);
		elemDialog.css("margin-top",-opts.height/2);
		}
	var Dialog = {
		loading:function(){
			this.open("<p class='dialog_loading'></p>");
			},
		success:function(){
			var successTips = "操作成功!";
			if(arguments[0]!=null)successTips = arguments[0];
			this.open("<p class='dialog_success'>"+successTips+"</p>");
			setTimeout(function(){
				$.Dialog.close();
				},2000)
			},
		fail:function(){
			var failTips = "操作失败!";
			if(arguments[0]!=null)failTips = arguments[0];
			this.open("<p class='dialog_fail'>"+failTips+"</p>");
			setTimeout(function(){
				$.Dialog.close();
				},2000)
			},
		open: function(title,opts,html){
			init();
			setOpts(opts);
			setTitle(title);
			setHtml(html);
			open();
		},
		close: close
	};
	$.extend({Dialog: Dialog});
})();