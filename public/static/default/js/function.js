// 全局变量
var swiperCount = 0;  // 商品轮播图片数量

// 商品tab切换
$('.y-tab-title>li').click(function() {
	$('.y-tab-title>li').removeClass('active');
	$(this).addClass('active');
})




// row tab
$(function() {
	// 图片懒加载
	var myLazyLoad = new LazyLoad({
		elements_selector: 'img.lazyload'
	});

	// ios 300毫秒延迟
	FastClick.attach(document.body);

	// ios返回强制页面刷新
	window.addEventListener('pageshow', function(event) {
	    if(event.persisted) location.reload();  
	});

	// 回到顶部
	$('.wrap').scroll(function(){
	     // console.log($(this).scrollTop());
	     //当window的scrolltop距离大于1时，go to 
	     if($('.wrap').scrollTop() > 100){
	         $('#goToTop').fadeIn();
	     }else{
	         $('#goToTop').fadeOut();
	     }
	 });

	 $('#goToTop').click(function(){
	     $('.wrap').animate({scrollTop: 0}, 300);
	     return false;
	 });


	// tab切换效果
	$('.weui-navbar__item').click(function() {
		var selfId = $(this).attr('href');
		$(selfId).addClass('animated fadeInLeft');
	})
	// 检测是否有底部固定定位的按钮
	if($('.bottom-nav').length > 0 ||  $('.m-bottom-nav2').length > 0 || $('.weui-tabbar').length > 0) {
		$('body .wrap').css('bottom','45px');
	}
	else {
		$('body .wrap').css('bottom',0)
	}

});



$(function() {
	// 搜索动画
	$('#search-input').focus(function() {
		$(this).next().fadeIn('last');
	}).blur(function () {
		$('#close-search').fadeOut('last');
	})
	// 关闭搜索
	$('#close-search').click(function () {
		$(this).fadeOut('last');
	})


	/*
	* 安卓表单提交刷新问题
	 */ 
	
	// 如果是活动页就存个值
	var selfUrl = window.location.href;

	if(/collage|haggle|seckill/g.test(selfUrl)) sessionStorage.setItem('eventState', 1);
	
	

	// 购物车页清空订单提交
	var orderUrl = sessionStorage.getItem('orderUrl') ? sessionStorage.getItem('orderUrl') : 0;

	if(selfUrl.indexOf('cart') != -1) sessionStorage.setItem('orderUrl',0);
	// 返回上一级
	$('#go-back').click(function(){
		// 地址页、门店页、商品页、优惠劵页
		// shop_list add_address goods_detail personal
		if(/shop_list|add_address|goods_detail|personal/g.test(selfUrl)) {
			if(orderUrl == 0) {
				history.back(-1);
			}else {
				if(sessionStorage.getItem('eventState') == null) {
					window.location.href = sessionStorage.getItem('orderUrl');
				} else {
					history.back(-1);
				}
				
			}
		} else {
			history.back(-1);
		}

		
	})
	/*
	* end 安卓返回不刷新问题
	 */ 

	// 开关卡片
	$('.switch-btn').click(function() {
		$(this).parent('.switch-card-title').next().slideToggle();
	})
	$('.switch-btn2').click(function() {
		switchToggle(this);
	})
	$('#switch-btn').click(function() {
		switchToggle(this);
	})

	// 解决安卓输入框被挡住
	$('input[type="text"],input[type="number"],textarea').on('click', function () {
	  var target = this;
	  setTimeout(function(){
	        target.scrollIntoViewIfNeeded();
	        console.log('scrollIntoViewIfNeeded');
	      },400);
	});
	// 安卓固定定位失效
	$('input[type="text"],input[type="number"],textarea').focus(function () {
		$('.bottom-nav, .m-bottom-nav2').css('position', 'absolute');
	}).blur(function () {
		$('.bottom-nav, .m-bottom-nav2').css('position', 'fixed');
	})

	
})
// 开关卡片2函数
function switchToggle(thiss) {
	var _this = thiss;
	
	if($(_this).next().is(":hidden")) {
		$(_this).find('i').delay(800).removeClass('icon-more').addClass('icon-moreunfold');
	} else {
		$(_this).find('i').delay(800).removeClass('icon-moreunfold').addClass('icon-more');
	}
	$(_this).next().slideToggle();
}
/*
* 购物车操作
 */
$(function() {
	var checkboxs = $('.cart').find('input[type="checkbox"]');
	var checkboxs_len = $('.cart').find('input[type="checkbox"]');
	// 点击全选
	$('.whole-check').click(function () {
		// 如果全选已勾
		if($(this).is(':checked')) {
			checkboxs.prop("checked", true);
			getPrice();
		} else {
			checkboxs.prop("checked", false);
			getPrice();
		}
	})
	// 获取总金额
	function getPrice() {
		var total_price = 0;
		$('.price-num').each(function() {
			var self_price = $(this).text();
			console.log(self_price);
			total_price += parseInt(self_price);			
		})
		// 算出总价
		$('#total').text(total_price);
	}
	
})



