// JavaScript Document
var defaultGoodsList = new Array();
var initFinish = 0;
for(var i=0;i<4;i++){
	var obj = new Object();
	obj.id = 0;
	obj.title = "商品标题";
	obj.img = IMG_PATH +'/default_goods_pic.jpg';
	obj.market_price = 0.00;
	obj.stock = 0.00;
	obj.url = "";
	defaultGoodsList.push(obj);
}
var defaultCaseList = new Array();
for(var i=0;i<3;i++){
	var obj = new Object();
	obj.picId = 0;
	obj.pic = IMG_PATH+"/no_cover_pic_s.png";
	obj.url = "";
	obj.title = "";
	var str = RandomString(10);
	obj.rel="case3_"+str;
	defaultCaseList.push(obj);
}
var defaultCase2List = new Array();
for(var i=0;i<4;i++){
	var obj = new Object();
	obj.picId = 0;
	obj.pic = IMG_PATH+"/no_cover_pic_s.png";
	obj.url = "";
	obj.title = "";
	var str = RandomString(10);
	obj.rel="case2_"+str;
	defaultCase2List.push(obj);
}
var app = angular.module('app', []).controller('commonCtrl', ["$scope", function($scope) {
	var id= $('input[name="id"]').val();
	if(id && activeModels!=''){
		$scope.activeModules = JSON.parse(activeModels);
		//console.log($scope.activeModules );
	}
	else{
		$scope.activeModules = [{"id":"header","name":"\u5fae\u9875\u9762\u6807\u9898","params":{"title":"","description":"","bgColor":"#fff",'is_show':0,'is_index':0},"issystem":1,"index":0,"displayorder":"0"}];
		if(useFor=="goodsDetail"){
			$scope.activeModules.push({"id":"goodsdetail","name":"商品详情页","params":{},"issystem":1,"index":1,"disable":1,"displayorder":"0"});
		}else if(useFor=="userCenter"){
			$scope.activeModules.push({"id":"usercenter","name":"个人详情页","params":{},"issystem":1,"disable":1,"index":1,"displayorder":"0"});
		}else if(useFor=="index"){
			$scope.activeModules.push({"id":"fixedmodule","name":"首页","params":{'title':'首页固定模块','desc':'商城基本信息，商城LOGO，推荐商品和分类'},"issystem":1,"disable":1,"index":1,"displayorder":"0"});	
		}else if(useFor=="cart"){
			$scope.activeModules.push({"id":"fixedmodule","name":"购物车页","params":{'title':'购物车固定模块','desc':'我的购物车列表'},"issystem":1,"disable":1,"index":1,"displayorder":"0"});	
		}else if(useFor=="orderlist"){
			$scope.activeModules.push({"id":"fixedmodule","name":"订单列表页","params":{'title':'订单固定模块','desc':'我的订单列表'},"issystem":1,"disable":1,"index":1,"displayorder":"0"});	
		}
	}

	$scope.activeItem = $scope.activeModules[0];
	$scope.editors = ['header'];
	$scope.modules = [
		{"id":"richtext","name":"富文本","params":{"content":"",'bgColor':'','color':'','fontsize':'','align':''},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"goods","name":"商品","params":{"list_style":1,'hasTestData':1,'show_price':1,'show_btn':1,"goods_list":defaultGoodsList},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"mutipic_goods","name":"多图商品","params":{"colGoods":2,"list_style":1,'hasTestData':1,'show_price':1,'show_btn':1,"goods_list":defaultGoodsList},"issystem":0,"index":0,"displayorder":"0"},	
		{"id":"banner","name":"幻灯片","params":{"show_cursor":1,"show_title":1,"is_auto":1,'banner_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"mutipic_banner","name":"多图滑动","params":{"col":2,"show_cursor":1,"show_title":1,"is_auto":1,'banner_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"piclist","name":"图片","params":{"list_style":1,"show_title":0,'pic_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"blank","name":"辅助空白","params":{"height":10},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"title","name":"标题","params":{"title":"","subtitle":"",'bgColor':'','maincolor':'','subcolor':'','align':''},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"textnav","name":"文本导航","params":{"title":"",'bgColor':'','color':'','text_nav_style':1,'text_nav_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"picnav","name":"图片导航","params":{"title":"",'nav_style':2,'pic_nav_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"searchgoods","name":"商品搜索","params":{},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"blankline","name":"辅助线","params":{'borderWidth':1,'borderColor':'#ccc','borderStyle':'dotted'},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"case","name":"橱窗","params":{'title':'','contentTitle':'','content':'','style':2,'show_title':1,'pic_list':defaultCaseList},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"notice","name":"公告","params":{'notice_content':'','bgColor':'','color':''},"issystem":0,"index":0,"displayorder":"0"},
		//{"id":"case2","name":"橱窗二","params":{'title':'','contentTitle':'','content':'','style':2,'show_title':1,'position':1,'pic_list':defaultCaseList,'pic_list_3':defaultCase2List},"issystem":0,"index":0,"displayorder":"0"}
	];

	$scope.addItem = function(id){
		try{
			//var addItem = jQuery.extend(true,{}, $scope.getModelById(id));
			var addItem = angular.copy($scope.getModelById(id));
			addItem.index = $scope.activeModules.length;
			$scope.activeModules.push(addItem);
			$scope.activeItem = addItem;
			$scope.initWidget(id);
			setTimeout(function(){
				$scope.initEditorTop(addItem.index);
			},100);
			if($scope.UEditor && id =="richtext"){
				$scope.UEditor.setContent($scope.activeItem.params.content);

			}else if($scope.NUEditor && id =="notice"){
				$scope.NUEditor.setContent($scope.activeItem.params.notice_content);
			}			
			
		}catch(e){
			console.log(e);
		}
	}

	$scope.editItem = function(mudule){
		$scope.activeItem = mudule;
		var tempId =  mudule.id;
		$scope.initWidget(tempId);
		setTimeout(function(){
			$scope.initEditorTop(mudule.index);
		},100);
		if($scope.UEditor && tempId=="richtext"){
			$scope.UEditor.setContent($scope.activeItem.params.content==""?'<p></p>':$scope.activeItem.params.content);   
			// $scope.UEditor.setContent(self_content);   
		}else if($scope.NUEditor && tempId =="notice"){
			$scope.NUEditor.setContent($scope.activeItem.params.notice_content==""?'<p></p>':$scope.activeItem.params.notice_content);                           
		}		
	}

	$scope.UEditor = null;
	$scope.NUEditor = null;


	$scope.initWidget = function(id){
		if($.inArray(id, $scope.editors)<0){
			$scope.editors.push(id);
			setTimeout(function(){
				
				if(id=="richtext"){
					$scope.UEditor = $.Editor.initEditor(
					'diy_editor_richcontent',
					editorUrl.ue_upimg,
					editorUrl.ue_mgimg,
					editorUrl.get_article_style
					)
					if($scope.UEditor){
						$scope.UEditor.addListener("contentChange",function(){
							$scope.activeItem.params.content = $scope.UEditor.getContent();
							$('.temp_click').click();
						});
					}
				}else if(id=="notice"){
					$scope.NUEditor = $.Editor.initEditor(
					'diy_editor_noticecontent',
					editorUrl.ue_upimg,
					editorUrl.ue_mgimg,
					editorUrl.get_article_style
					)
					if($scope.NUEditor){
						$scope.NUEditor.addListener("contentChange",function(){
							$scope.activeItem.params.notice_content = $scope.NUEditor.getContent();
							$('.temp_click').click();
						});
					}
				}
			},200)
		}
	}


	$scope.deleteItem = function(mudule){
		if(confirm('确认删除该模块吗，删除后不可恢复。')){
			//console.log(mudule);
			//return;
			//$scope.activeModules.remove(mudule);
			//for(var i=0;i<$scope.activeModules.legnth;i++){
			//	$scope.activeModules[i].index = i;
			//}
			mudule.is_del = 1;
			//console.log(mudule);
			$('#module-'+mudule.index).hide();
			if(mudule == $scope.activeItem){
				$scope.activeItem = $scope.activeModules[0];
			}	
		}
				
	}


	$scope.initEditorTop =function(index){
		var oTop = $('#module-'+index).offset().top;
		var nTop = $('.app_inner').offset().top;
		$('#editor'+$scope.activeItem.id).css({'margin-top':oTop-nTop});
	}


	$scope.getModelById = function(id){
		var tempItem = new Object();
		for(var m in $scope.modules){
			if($scope.modules[m].id==id){
				tempItem = $scope.modules[m];
				break;
			}
		}
		return tempItem;
	}


	$scope.submitForm =function(){
		var url = $('#form').attr('action');
		var id = $('input[name="id"]').val();
		var tempModules = new Array();
		var tempIndex = 0;
		$('#modules>div').each(function(index, element) {
			var placeIndex = parseInt($(element).attr('index'));
			if(!$scope.activeModules[placeIndex].is_del){
				$scope.activeModules[placeIndex].index = tempIndex;
				tempModules.push($scope.activeModules[placeIndex]);
				tempIndex++;
			}
			
		});
		var title = $scope.activeModules[0].params.title;
		if(title==''){
			title = $('#page_title').val()
		}
		
		$.post(url,

			{	
				id:id,
				title:title,
				desc:$scope.activeModules[0].params.description,
				is_show:$scope.activeModules[0].params.is_show,
				is_index:$scope.activeModules[0].params.is_index,
				config:encodeURIComponent(JSON.stringify(tempModules))
				//config:JSON.stringify(tempModules)
			},
			function(data){
//				console.log(data);
//				return false;
//				var url = '';
//				url = '//' + window.location.host + '/shop/diy_page/lists';
				var url=data.url;
				if(data){
					updateAlert(data.msg,'success');
					setTimeout(function(){
						console.log(url);
						window.location.href = url;
					},300);
				}	
			}
		)
	}


	$scope.colorPicker = function($event){
		try{
			var ele = $($event.toElement);
			var top = ele.offset().top;
			var left = ele.offset().left;
			var w = ele.width();
			var h = ele.height();
			ele.addClass('active-color');
			$('.colpick').show().css({'top':top+h,'left':left+w});
		}catch(e){
			
		}
	}


	$('.color_picker_hide').colpick({
		colorScheme:'white',
		submitText:"确定",
		layout:'rgbhex',
		color:'ff8800',
		onSubmit:function(hsb,hex,rgb,el) {
			$scope.activeItem.params[$('.active-color').data('color')] = '#'+hex;
			$('.active-color').css('background-color', '#'+hex);
			$(el).colpickHide();
			$('.active-color').removeClass('active-color');
			$('.temp_click').click();
		}
	})


	//添加商品
	$scope.addGoodsDialog = function(dataUrl){
		$.WeiPHP.openSelectGoods(dataUrl,function(goodsList){
			if(goodsList.length>0){
				if($scope.activeItem.params.hasTestData==1){
						$scope.activeItem.params.goods_list = new Array();
				}
				for(var i=0;i<goodsList.length;i++){
					$scope.activeItem.params.goods_list.push(goodsList[i]);
				}
				$scope.activeItem.params.hasTestData = 0;
				//console.log($scope.activeItem.params.goods_list);
				$('.temp_click').click();
			}
		});
	}


	//删除商品
	$scope.deleteGoods = function(obj){
		$scope.activeItem.params.goods_list.remove(obj);
	}
	
	//添加幻灯片
	$scope.addBanner = function(){
		var str = RandomString(10);
		var obj = new Object();
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.title = "";
		obj.url = "";
		obj.rel="banner_"+str;
		$scope.activeItem.params.banner_list.push(obj);
	}
	$scope.addBannerPic = function(obj){
		console.log('--------------');
		console.log($scope.activeItem.params);
		$.WeiPHP.uploadImgDialog(obj);
		// $.WeiPHP.uploadImgDialog(1,function(data){
		// 	obj.pic = data[0].src;
		// 	obj.picId = data[0].id;
		// 	$('.temp_click').click();
		// })
	}
	$scope.deleteBanner = function(obj){
		$scope.activeItem.params.banner_list.remove(obj);
	}
	$scope.tempClick = function($event){
		//TODo nothing
	}
	$scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
		//下面是在table render完成后执行的js
		try{
			//console.log('init done!')
			$("#modules").dragsort('destroy');
			$("#modules").dragsort({
				itemSelector: ".js-sorttable", dragSelector: ".js-sorttable", dragBetween: false, placeHolderTemplate: "<div class='js-sorttable'></div>",dragSelectorExclude:'.aciton_wrap',dragEnd: function() {
					//$(".js-sorttable").attr('style','');
					
				}
			});
				$('.banner').each(function(index,ele){
					var conId = $(ele).attr('id');
					$.WeiPHP.initBanner('#'+conId,false,5000,2);
				})
			//if(initFinish==0){
				//多图滑动
				$('.mutipic_banner').each(function(index,ele){
						var conId = $(ele).attr('id');
						//$(ele).data('col');
						var col = $(ele).attr('data-col')
						$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
					})
				$('.mutipic_goods').each(function(index,ele){
					var conId = $(ele).attr('id');
					var col = $(ele).attr('data-colGoods')
					$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
				})	
			//}
			initFinish = 1;
		}catch(e){
		
		}
	});
}]).controller('picListController', ["$scope", function($scope) {
	//添加图片
	$scope.addPicList = function(){
		var str = RandomString(10);
		var obj = new Object();
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.title = "";
		obj.url = "";
		obj.rel="piclist_"+str;
		$scope.activeItem.params.pic_list.push(obj);
	}
	$scope.addPicListPic = function(obj){
		$.WeiPHP.uploadImgDialog(obj);
//		$.WeiPHP.uploadImgDialog(1,function(data){
//			obj.pic = data[0].src;
//			obj.picId = data[0].id;
//			$('.temp_click').click();
//			//console.log($scope.activeItem.params.pic_list);
//		})
	}
	$scope.deletePicListPic = function(obj){
		$scope.activeItem.params.pic_list.remove(obj);
	}
}]).controller('textNavListController',["$scope", function($scope){
	$scope.addTextNav = function(){
		var obj = new Object();
		obj.title = "";
		obj.url = "";
		$scope.activeItem.params.text_nav_list.push(obj);
	}
	$scope.deleteTextNav = function(b){
		$scope.activeItem.params.text_nav_list.remove(b);
	}
}]).controller('picNavController',["$scope", function($scope){
	$scope.addPicNav = function(){
		var str = RandomString(10);
		var obj = new Object();
		obj.title = "";
		obj.url = "";
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.rel = "picNav_"+str;
		$scope.activeItem.params.pic_nav_list.push(obj);
	}
	$scope.addPicNavPic = function(obj){
		$.WeiPHP.uploadImgDialog(obj);
//		$.WeiPHP.uploadImgDialog(1,function(data){
//			obj.pic = data[0].src;
//			obj.picId = data[0].id;
//			$('.temp_click').click();
//			//console.log($scope.activeItem.params.pic_list);
//		})
	}
	$scope.deletePicNav = function(b){
		$scope.activeItem.params.pic_nav_list.remove(b);
	}
}]).controller('caseController',["$scope", function($scope){
	$scope.addCasePic = function(obj){
		var str = RandomString(10);
		obj.rel="case_"+str;
		$.WeiPHP.uploadImgDialog(obj);
//		$.WeiPHP.uploadImgDialog(1,function(data){
//			console.log('=====addCasePic======')
//			console.log(data)
//			obj.pic = data[0].src;
//			obj.picId = data[0].id;
//			$('.temp_click').click();
//			//console.log($scope.activeItem.params.pic_list);
//		})
	}
}]).controller('mutipicBannerController',["$scope", function($scope){
	$scope.changeCol = function(obj){
		var conId = 'mutipic_banner_'+$scope.activeItem.index;
	    var col = $(obj.target).val();
	    // console.log(col);
		$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
	}
	$scope.changeColGood = function(obj){
		var conId = 'mutipic_goods'+$scope.activeItem.index;
	    var col = $(obj.target).val();
	    // console.log(col);
		$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
	}
	$scope.addMutiBanner = function(){
		//console.log('aaa')
		var str = RandomString(10);
		var obj = new Object();
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.title = "";
		obj.url = "";
		obj.rel = "mutibanner_"+str;
		$scope.activeItem.params.banner_list.push(obj);
		//var conId = 'mutipic_banner_'+$scope.activeItem.index;
	    //var col = $scope.activeItem.params.col;
		//$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
	}
	$scope.deleteMutiBanner = function(obj){
		var conId = 'mutipic_banner_'+$scope.activeItem.index;
	    var col = $scope.activeItem.params.col;
		$scope.activeItem.params.banner_list.remove(obj);
		$.WeiPHP.initMutipicBanner('#'+conId,false,5000,col);
	}
	
}])
app.directive('onFinishRenderFilters', ["$timeout", function ($timeout) {
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
}]);
app.filter('to_trusted', ['$sce', function($sce){
	return function(text) {
		return $sce.trustAsHtml(text);
	};
}]);
angular.bootstrap(document, ['app']);

function RandomString(length) {
	var str = '';
	for ( ; str.length < length; str += Math.random().toString(36).substr(2) );
	return str.substr(0, length);
}