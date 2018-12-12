(function(){
	// uploadUrl : {:addons_url("EditorForAdmin://Upload/ue_upimg")}
	//imageManagerUrl : {:addons_url("EditorForAdmin://Upload/ue_mgimg")}
	//styleUrl {:addons_url('EditorForAdmin://Style/get_article_style')}
	function initEditor(name,uploadUrl,imageManagerUrl,styleUrl){
				$('textarea[name="'+name+'"]').attr('id', 'editor_id_'+name);
				window.UEDITOR_HOME_URL = STATIC + "/ueditor";
				window.UEDITOR_CONFIG.initialFrameHeight = 300;
				window.UEDITOR_CONFIG.scaleEnabled = true;
				window.UEDITOR_CONFIG.imageUrl = uploadUrl;
				window.UEDITOR_CONFIG.imagePath = '';
				window.UEDITOR_CONFIG.imageFieldName = 'imgFile';
				//在这里扫描图片
				window.UEDITOR_CONFIG.imageManagerUrl=imageManagerUrl;//图片在线管理的处理地址
        		window.UEDITOR_CONFIG.imageManagerPath='';        
				imageEditor = UE.getEditor('editor_id_'+name,{
						toolbars: [
							['fullscreen','source', 'undo', 'redo',  
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall',  
                 'lineheight',  
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', 'indent',
                'justifyleft', 'justifycenter', 'justifyright',
                'link', 'unlink',  'insertimage', 'emotion', 'insertvideo', 'music', 'attachment', 'map']
						],
						autoHeightEnabled: false,
						autoFloatEnabled: true,
						initialFrameHeight:300
					});
				imageEditor.styleUrl = styleUrl;
				//添加一下判断是否是单个按钮管理图片 需要执行一下代码
			return imageEditor;
	}
	var Editor = {
		initEditor:initEditor
	}
	$.extend({Editor:Editor});
})()