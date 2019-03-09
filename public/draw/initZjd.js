 function check_subscribe(){
    	//var has_subscribe = 1;
    	if(has_subscribe=="0"){
    	    $.WeiPHP.showSubscribeTips({'title':title,'qrcode': qrcode});
    		return false;	
    	}else{
    		return true;
    	}
    }
 
$('#shape').click(function(event){
	if(!(check_subscribe())){
		return false;
	}
	if(event.target.tagName == "SPAN"){
		$(this).unbind("click");
		$.Dialog.loading('加载中...');
		$.get(joinUrl,function(json){
				if(json){
					if(json.status == 0){
						$(event.target).addClass('on');
						$.Dialog.confirm('提示',json.msg,function(){
							window.location.href=json.jump_url;
						});
						
					}else{
						$(event.target).addClass('yes');
						$.Dialog.confirm('中奖啦',json.msg,"",json.jump_url);
					}
				}else{
					$.Dialog.fail('程序罢工');
				}
		});
	}
})