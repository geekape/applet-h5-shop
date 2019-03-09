// JavaScript shop by jacy
$(function(){
	//购物添加数量
	$('.buy_count .add').click(function(){
		var cart_id = $(this).attr('rel');
		var stockNum=$('#stockNum_'+cart_id).text();
		var val = parseInt($(this).siblings('input').val());
		if(val != stockNum){
			$(this).siblings('input').val(val+1);
		}
		$(this).siblings('.reduce').addClass('active');
		
		updatePriceAndCount();
		minusState(this);
	})
	$('.buy_count .reduce').click(function(){
		var val = parseInt($(this).siblings('input').val());
		if(val>1){
			$(this).siblings('input').val(val-1);
			updatePriceAndCount();
		}
		minusState(this);
	})

	$('.buy_count input[type="number"]').blur(function(){
		if($(this).val()<=0){
			$.Dialog.fail("购物数量不能小于1件");
			$(this).val(1);
		}else{
			updatePriceAndCount();
		}
	})

	$('.buy_count input[type="number"]').bind("input propertychange",function(event){
	       minusState(this);
	});


	$('input[name="goods_ids[]"]').change(function(){
		updatePriceAndCount();
	})
	
	//全选的实现
	$(".check_all").click(function(){
		if($('input[name="checkAll"]').prop('checked')==true){
		$('input[name="goods_ids[]"]').prop('checked',true);
		
		}else{
			$('input[name="goods_ids[]"]').prop('checked',false);
		}
		updatePriceAndCount();
	});
	
})

// 更新减号的状态
function minusState(thiss) {
	var val = $(thiss).parent().children('.sum').val();
	if(val > 1) {
		$(thiss).parent().children('.reduce').addClass('active');
	} else {
		$(thiss).parent().children('.reduce').removeClass('active');
	}
	console.log(val);
}



//更新购物车价格和数量
function updatePriceAndCount(){
	console.log('updatePriceAndCount')
	var totalCount = 0;
	var totalPrice = 0;

	if($('input[name="goods_ids[]"]:checked').length==$('input[name="goods_ids[]"]').length){
		$('input[name="checkAll"]').prop('checked',true);
	}else{
		$('input[name="checkAll"]').prop('checked',false);
	}
	$('input[name="goods_ids[]"]:checked').each(function(index, element) {
		var itemElem = $(this).parents('.m-goods');
		var price = parseFloat(itemElem.find('.price-num').text());
		//var express = parseFloat(itemElem.find('.price-express').text());
		var count = parseInt(itemElem.find('input[rel="buyCount"]').val());
		totalCount += count;
		totalPrice += count*price
	});
	totalPrice = Math.round(totalPrice * 100)/100;
	console.log(totalPrice)
	console.log(totalCount)
	totalPrice = isPoint(totalPrice);
	$('#totalCount').text(totalCount);
	$('#totalPrice').text(totalPrice);
}

// 给金额没有小数点的加上小数点
function isPoint(num) {
  var reg = /\./;
  if(!reg.test(num)) {
    var number = num + '.00';
    return number;
  }
  return num;
}

//提交检查
function checkCartSubmit(){
	if($('input[name="goods_ids[]"]:checked').length==0){
		// $.Dialog.fail("请先选择要购买的商品");
		$.toast("请先选择要购买的商品", "text");
		return false;
	}
	var cartids="";
	var istrue=1;
	$('input[name="goods_ids[]"]:checked').each(function(){
		var cid =  $(this).attr('rel');
		cartids += cid+',';
		var buy_num=parseInt($("#setnum_"+cid).val());
		var snum= parseInt($("#stockNum_"+cid).text());
		
		if(isNaN(buy_num)){
			buy_num=0;
		}
		if(istrue==1 && buy_num <= 0 ){
			istrue=0;
		}else if(istrue==1 &&buy_num >snum ){
			istrue=2;
		}
	});
	if( istrue==0){
		$.toast("购物数量不能小于1件", "text");
		return false;
	}else if( istrue==2 ){
		$.Dialog.fail("库存数量不足");
		return false;
	}
	$("input[name='cart_ids']").val(cartids);
}
function confirmGetGoods(url){
	$.Dialog.confirmBox('温馨提示','确认已收货？',{rightCallback:function(){
		$.Dialog.loading();
		$.post(url,function(res){
			if(res.code==1){
				 setTimeout(function(){
					 location.reload();	
				},1500);
			}else{
				$.Dialog.fail(res.msg);
			}
	    });
	}});
}
function showSubCate(_this,id){
	$(_this).addClass('cur').parent().siblings().find('a').removeClass('cur');
	$('#cate_'+id).show().siblings().hide();
}
function initDiy(dataConfig){
	var head_data = JSON.parse(decodeURIComponent(dataConfig));
	//$('title').text(head_data[0]['params']['title']);
	var app = angular.module('app', []).controller('commonCtrl', function($scope) {
		$scope.activeModules = JSON.parse(decodeURIComponent(dataConfig));
		$scope.headItem = $.extend({},true,$scope.activeModules[0]);
		$scope.activeModules.shift();
		$scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
			//下面是在table render完成后执行的js
			try{
				if($('.scrollNotice').html()){
					var iRight = 0;
					setInterval(function(){
						$('.scrollNotice').css('right',iRight++);
						if(iRight==$('.scrollNotice').width())iRight= -$('.scrollNotice').width();
					},70);
				}
				$('.banner').each(function(index,ele){
					var conId = $(ele).attr('id');
					$.WeiPHP.initBanner('#'+conId,true,5000,2);
				})
				//
				$('.mutipic_banner').each(function(index,ele){
					var conId = $(ele).attr('id');
					$.WeiPHP.initMutipicBanner('#'+conId,true,5000,$(ele).data('col'));
				})
				$('.mutipic_goods').each(function(index,ele){
					var conId = $(ele).attr('id');
					$.WeiPHP.initMutipicBanner('#'+conId,true,5000,$(ele).attr('data-colGoods'));
				})				
				
			}catch(e){
					
			}
			
		});
		
	});
	app.directive('onFinishRenderFilters', function ($timeout) {
		return {
			restrict: 'A',
			link: function(scope, element, attr) {
				if (scope.$last === true) {
					$timeout(function() {
						scope.$emit('ngRepeatFinished');
					});
				}
			}
		};
	});
	app.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }]);
	angular.bootstrap(document, ['app']);
	return app;
}
function addToCart(id, url){
	var selfUrl = window.location.href;
	// $.Dialog.loading();
	$.ajax({
		url:url,
		data:{goods_id:id},
		dataType:'json',
		type:"POST",
		success:function(res){
			if(res.code==0){
                $.toast(res.msg,'text');
			}else{
				if(res>0){selfUrl
					if(/collect/.test(selfUrl)) $.toast('加入购物车成功', 'text');
					$('#cartCount').addClass('active');
					$('#cartCount').text(res);
				}else{
				    $.toast('加入购物车失败，请直接下单购买','text');
				}
			}
		}
	})
}
//检查是不是两位数字，不足补全
function check(str){
    str=str.toString();
    if(str.length<2){
        str='0'+ str;
    }
    return str;
}
function parse_time(time){
	if(time<=0 || time=='') return '00:00:00';

    var day=parseInt(time/86400/1000);
	
    var hour = check(parseInt(time / 1000 / 60 / 60 % 24));
    var minute = check(parseInt(time / 1000 / 60 % 60));
    var seconds = check(parseInt(time / 1000 % 60));
	
	var str = hour + ":" + minute + ":" + seconds;
	if(day>0){
		str = day+"天 "+str;	
	}
	return str;	
}
//倒计时
function countdown(start_time, end_time, nowtime){
	var start = parseInt(start_time)*1000;
	var end = parseInt(end_time)*1000;

	var status = 2
	var msg = '活动已结束';
	if(nowtime<start){
	    msg = '距开始还有 ' + parse_time(start-nowtime);
		status = 0
	}else if(end>nowtime){
		msg = '距结束还有 ' + parse_time(end-nowtime);
		status = 1
	}
	
	return {'msg':msg,'status':status}
}
function joinCheck(need_subscribe,need_card_member,title, qrcode, url){
	if(need_subscribe=='1'){
	    $.WeiPHP.showSubscribeTips({'title':title,'qrcode': qrcode});
		return false;
	}else if(need_card_member=='1'){
		$.WeiPHP.showCardTips();
		return false;
	}else if(url!=''){
		window.location.href = url;
		return false;
	}else{
	    return true;
	}
}
function parseMoney(money){
	var val = parseFloat( money )
	return isNaN(val) ? 0 : val;
}