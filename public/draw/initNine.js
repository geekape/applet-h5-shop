 function check_subscribe(){
    	//var has_subscribe = 1;
    	if(has_subscribe=="0"){
    	    $.WeiPHP.showSubscribeTips({'title':title,'qrcode': qrcode});
    		return false;	
    	}else{
    		return true;
    	}
    }
 
var isuseable=0;
function initLayout(){
	var prizeAreaWidth = $('.prize_list').width();
	var dw = 10;
	var prizeItemWidth = (prizeAreaWidth-3*dw)/4;
	$('.prize_list').height(prizeAreaWidth*3/4);
	$('.prize_list .item').width(prizeItemWidth).height(prizeItemWidth);
	$('.prize_list .item_2').css({'left':prizeItemWidth+dw});
	$('.prize_list .item_3').css({'left':prizeItemWidth*2+2*dw});
	$('.prize_list .item_4').css({'left':prizeItemWidth*3+3*dw});
	$('.prize_list .item_10').css({'top':prizeItemWidth+dw});
	$('.prize_list .item_5').css({'top':prizeItemWidth+dw,'left':prizeItemWidth*3+3*dw});
	$('.prize_list .item_9').css({'top':prizeItemWidth*2+2*dw,'left':0});
	$('.prize_list .item_8').css({'top':prizeItemWidth*2+2*dw,'left':prizeItemWidth+dw});
	$('.prize_list .item_7').css({'top':prizeItemWidth*2+2*dw,'left':prizeItemWidth*2+2*dw});
	$('.prize_list .item_6').css({'top':prizeItemWidth*2+2*dw,'left':prizeItemWidth*3+3*dw});
	$('.prize_list .get_prize_btn').width(prizeItemWidth*2+dw+4).height(prizeItemWidth+2).css({'top':prizeItemWidth+dw,'left':prizeItemWidth+dw,'line-height':prizeItemWidth+'px'});	
}
function drawPrize(getUrl){
	if(!(check_subscribe())){
		return false;
	}
	var thePrizeJson;
	var runIndex = 0;
	var runTotal = 0;
	var prizeCount = $('.prize_list .item').length;
	var thePrizeId=0;
	if(isuseable==1){
		return;
	}
	isuseable=1;
	$.post(getUrl,function(data){
		thePrizeJson = data;
		thePrizeType = data.award_type;
		thePrizeId = data.award_id;
	});
	var interval = setInterval(function(){
					if(runIndex < prizeCount){
						runIndex++;
					}else{
						runIndex = 0;
					}
					runTotal++;
					$('.prize_list .item').removeClass('geted_item');
					$('.prize_list .item').eq(runIndex).addClass('geted_item');
					if(runTotal*200>6000 && thePrizeJson && $('.prize_list .item').eq(runIndex).data('prizeid')==thePrizeId){
						clearInterval(interval);
						var json = thePrizeJson;
						if(json){
							if(json.status == 0){
//								$.Dialog.confirm('提示',json.msg);
								$.Dialog.confirm('提示',json.msg,function(){
//									window.location.reload();
									window.location.href=json.jump_url;
								});
							}else{
								$.Dialog.confirm('中奖啦',json.msg,"",json.jump_url);
							}
						}else{
							$.Dialog.fail('程序罢工');
						}
					}else if(runTotal*200>6000 && thePrizeJson.status==0 && $('.prize_list .item').eq(runIndex).data('prizeid')==thePrizeId){
						clearInterval(interval);
						$.Dialog.confirm('提示',thePrizeJson.msg,function(){
							window.location.reload();
						});
					}
		},250);
}
$(function() {
//	console.log()
	if(jplist && jplist.length>0){
		var html = "";
		for(i = 0;i<10;i++){
			if(i<jplist.length){
				var jp = jplist[i];
//				console.log(jp);
				html = html + '<div class="item item_'+(i+1)+'" data-prizeid = "'+jp.award_id+'" data-name="'+jp.title+'"><img src="'+jp.picUrl+'"/></div>'	
			}else{
				html = html + '<div class="item item_'+(i+1)+'" data-prizeid = "0" data-name="谢谢参与"><img src="'+default_pic+'"/></div>'	
			}
		}
		$('.prize_list').append(html);
		initLayout();
	}else{
		$.Dialog.fail('没有配置产品!');
	}
});