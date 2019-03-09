function exchangeCard(_this){
	var flag = $(_this).data('flag');
	var exurl = $(_this).data('exurl');
	var addurl = $(_this).data('addurl');
	if(flag==0){
		$.Dialog.confirmBox("温馨提示","你现在还没有自己的名片，要马上创建吗？",{rightCallback:function(){
			$.Dialog.loading();
			$.post(exurl,function(data){
				$.Dialog.close();
				window.location.href = addurl;
			});
		}});	
	}else{
		$.Dialog.loading();
		$.post(exurl,function(data){
			setTimeout(function(){
				if(data==1){
					$.Dialog.success('交换成功！名片已添加到名片夹');
					window.location.reload();
				}else{
					$.Dialog.fail('交换失败！请重试');
				}
			},1500);
			
		});
	}
	return false;
}
function delCard(_this){
	var url = $(_this).data('href');
	var myurl = $(_this).data('myurl');
	$.Dialog.confirmBox("温馨提示","确定要删除这个名片吗？",{rightCallback:function(){
			$.post(url,function(data){
				if(data==1){
					$.Dialog.success('删除成功！返回你的名片...');
					setTimeout(function(){
						window.location.href = myurl;
					},1500);
				}else{
					$.Dialog.fail('删除失败！请重试');
				}
			});
	}});	
	return false;
}
function previewCard(_this,url,myUrl){
	var temp = $(_this).data('temp');
	if($(_this).parent().hasClass('selected')){
		return;
	}
	$.Dialog.confirmBox("温馨提示","确认要使用该模板吗？",{rightBtnText:"使用",leftBtnText:"重新选择",rightCallback:		function(){
			$.Dialog.loading();
			$.post(url,{'temp':temp},function(data){
				if(data==0){
					$.Dialog.fail("模板选择失败，请重试!");
				}else{
					$.Dialog.success("选择成功!即将进入你的个人名片...");
					setTimeout(function(){
						window.location.href = myUrl;
					},1500);
				}
			})	
	}});
	
}