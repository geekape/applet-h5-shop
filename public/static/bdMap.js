//先要加载接口，要在函数外，保证先加载
document.write('<script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak=A739765f9a84bee561d30fa0b537ccb9"></script>');

//显示地图
//参数：显示容器ID，属性(city,addr,title,lawfirm,tel,user,mapx,pic,ismove,piobj,zoom)
function ShowMap(objname,options){
	if(options){
		this._city = options.city ? options.city : ""; //城市
		this._addr = options.addr ? options.addr : ""; //地址
		this._title = options.title ? options.title : ""; //信息窗口标题
		this._lawfirm = options.lawfirm ? options.lawfirm : ""; //律所
		this._tel = options.tel ? options.tel : ""; //电话
		this._user = options.user ? options.user : ""; //主任
		this._mapx = options.mapx ? options.mapx : ""; //地图坐标
		this._pic = options.pic ? options.pic : ""; //图片
		this._ismove = options.ismove ? options.ismove : "0"; //是否拖动，1为拖动为设置标注，0为显示。默认0
		this._piobj = options.piobj ? options.piobj : ""; //接收拖动坐标的表单ID
		this._zoom = options.zoom ? options.zoom : "14"; //放大级别，默认14
	}
	//设定初始坐标
	var point=new BMap.Point(34.83794,113.63156);
	//范围为3-18级
	var zoom=this._zoom;

	//创建地图
	var map = new BMap.Map(objname);
	map.enableScrollWheelZoom();
	map.centerAndZoom(point, zoom);//设初始化地图。

	//设置版权控件位置
	var cr = new BMap.CopyrightControl({anchor: BMAP_ANCHOR_TOP_LEFT});
	map.addControl(cr); //添加版权控件
	var bs = map.getBounds(); //返回地图可视区域
	cr.addCopyright({id: 1, content: "<a href='http://www.weiphp.cn' style='font-size:12px;'>WeiPHP</a>", bounds: bs});

	//坐标不为空时按坐标显示
	if (this._mapx != ""){
		var mx=this._mapx.substr(0,this._mapx.indexOf(","));
		var my=this._mapx.substr(this._mapx.indexOf(",")+1);
		point=new BMap.Point(mx,my);
		map.centerAndZoom(point, zoom); //重新调整位置
	}
	//否则按地址显示
	else if (this._addr != ""){
		//创建地址解析器实例   
		var myGeo = new BMap.Geocoder();    
		//将地址解析结果显示在地图上，并调整地图视野。此过程为异步，所以要重设标注 
		myGeo.getPoint(this._addr, function(poi){
			map.centerAndZoom(poi, zoom);
			marker.setPosition(poi); //重调标注位置
		}, this._city);
	}
	//否则按城市显示
	else if (this._city != ""){
		map.setCenter(this._city); //设置地图中心点。
		//此定位无具体坐标，所以显示模式时要清除标注。要延时处理
		if (this._ismove=="0"){setTimeout(function(){map.clearOverlays();}, 1000);}
	}
	//都为空按IP定位
	else{
		//创建一个获取本地城市位置的实例
		var myCity = new BMap.LocalCity();
		//获取城市
		myCity.get(function(result){map.setCenter(result.name);});
		if (this._ismove=="0"){setTimeout(function(){map.clearOverlays();}, 1000);}
	}

	//创建标注
	 var myIcon = new BMap.Icon(IMG_PATH+"/bdmap.png", new BMap.Size(29, 29));  
     var marker = new BMap.Marker(point,{icon:myIcon});
	map.addOverlay(marker); // 将标注添加到地图中
	
	//设置标注时
	if (this._ismove=="1"){
		marker.enableDragging(); //可拖拽
		var label = new BMap.Label("拖拽到您的位置",{offset:new BMap.Size(20,-15)});
		label.setStyle({ backgroundColor:"red", color:"white", fontSize : "12px" });
		marker.setLabel(label);

		var poj=this._piobj; //过程里不支持this，要传给变量
		
		//拖拽设置位置
		marker.addEventListener("dragend", function(e){
			try{document.getElementById(poj).value = e.point.lat + "," + e.point.lng;}catch (ex) {}
		});
		//点击设置位置
		map.addEventListener("click", function(e){
			marker.setPosition(e.point); //重调标注位置
			try{document.getElementById(poj).value = e.point.lat + "," + e.point.lng;}catch (ex) {}
		});
	}

	//显示标注时
	if (this._ismove=="0"){
		//marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
		
		//显示窗口设置
		var opts = {width:250,height:110,title : "<font color=green size=3>" + this._title + "</font>"} //窗口标题
		var infotxt="<table border='0'><tr><td valign='top'>"; //窗口内容
		if (this._pic != ""){infotxt += "<img src='"+this._pic+"' id='picid' style='float:left;margin-right:5px;padding-top:8px;' width=50>";}
		infotxt += "</td><td><p style='font-size:12px;line-height:16px;padding-top:8px;'>";
		if (this._lawfirm !=""){infotxt += "<b>律所：</b>" + this._lawfirm + "<br/>";};
		if (this._addr !=""){infotxt += "<b>地址：</b>" + this._addr + "<br/>";};
		if (this._tel !=""){infotxt += "<b>电话：</b>" + this._tel + "<br/>";};
		if (this._user !=""){infotxt += "<b>主任：</b>" + this._user + "<br/>";};
		infotxt += "</p></td></tr></table>";
		
		//显示文本标题
		var label2 = new BMap.Label(this._title,{offset:new BMap.Size(20,-15)});
		label2.setStyle({ backgroundColor:"red", color:"white", fontSize : "12px" });
		marker.setLabel(label2);

		//创建信息窗口
		var infoWindow = new BMap.InfoWindow(infotxt,opts);
		marker.addEventListener("mouseover", function(){
			this.openInfoWindow(infoWindow);
			//图片加载完毕重绘infowindow。防止在网速较慢，图片未加载时，生成的信息框高度比图片的总高度小，导致图片部分被隐藏
			document.getElementById('picid').onload = function (){infoWindow.redraw();}
		});
	}
}


//获取地理位置,间隔符
//百度查询接口为异步，所以这里要用异步回调方式
function getBDAddress(callBackFun,spStr){
	if (!spStr){spStr="";} //分隔符，默认空
	var geolocation = new BMap.Geolocation();
	geolocation.getCurrentPosition(function(r){
		if(this.getStatus() == BMAP_STATUS_SUCCESS){
			var point = new BMap.Point(r.point.lng,r.point.lat);
			var gc = new BMap.Geocoder();    
			gc.getLocation(point, function(rs){
				var addComp = rs.addressComponents;
				var addVal = addComp.province + spStr + addComp.city + spStr + addComp.district + spStr + addComp.street + spStr + addComp.streetNumber;
				callBackFun(addVal);
			});
		}
	},{enableHighAccuracy: true})
}