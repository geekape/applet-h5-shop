 function check_subscribe(){
    	//var has_subscribe = 1;
    	if(has_subscribe=="0"){
    	    $.WeiPHP.showSubscribeTips({'title':title,'qrcode': qrcode});
    		return false;	
    	}else{
    		return true;
    	}
    }
function checkCanvas(){
	try {
		document.createElement('canvas').getContext('2d');
		return truel
	} catch (e) {
		var addDiv = document.createElement('div');
		return false;
	}

}
var u = navigator.userAgent, mobile = 'PC';
if (u.indexOf('iPhone') > -1) mobile = 'iphone';
if (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1) mobile = 'Android';
function createCanvas(parent, width, height) {
	var canvas = {};
	canvas.node = document.createElement('canvas');
	canvas.context = canvas.node.getContext('2d');
	canvas.node.width = width || 100;
	canvas.node.height = height || 100;
	parent.appendChild(canvas.node);
	return canvas;
}
function initGuaGuaKa(container, width, height, fillColor,picUrl,prizeUrl) {
	var type = mobile;
	var canvas = createCanvas(container, width, height);
	var ctx = canvas.context;
	ctx.fillCircle = function (x, y, radius, fillColor) {
		this.fillStyle = fillColor;
		this.beginPath();
		this.moveTo(x, y);
		this.arc(x, y, radius, 0, Math.PI * 2, false);
		this.fill();
	};
	ctx.clearTo = function (isColor,fillColor,picUrl) {
		if(isColor){
			ctx.fillStyle = fillColor;
			ctx.fillRect(0, 0, width, height);
		}else{
			var coverImg = new Image();
			coverImg.src = picUrl;
			coverImg.onload=function(){
				ctx.drawImage(coverImg,0,0,width,height);    
				//	console.log('a');   
			}
		}
	};
	ctx.clearTo(false,fillColor,picUrl);
	canvas.node.addEventListener(mobile == "PC" ? "mousedown" : "touchstart", function (e) {
		e.preventDefault();  
		canvas.isDrawing = true;
	}, false);
	canvas.node.addEventListener(mobile == "PC" ? "mouseup" : "touchend", function (e) {
		e.preventDefault();  
		canvas.isDrawing = false;
		guaguaDone(ctx,width,height,prizeUrl);
	
	}, false);
	canvas.node.addEventListener(mobile == "PC" ? "mousemove" : "touchmove", function (e) {
		e.preventDefault();  
		if (!canvas.isDrawing) {
			return;
		}
		if (type == 'Android') {
			var x = e.changedTouches[0].pageX - this.offsetLeft;
			var y = e.changedTouches[0].pageY - this.offsetTop;
		} else {
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;

		}
		var radius = 20;
		var fillColor = '#ff0000';
		ctx.globalCompositeOperation = 'destination-out';
		ctx.fillCircle(x, y, radius, fillColor);
	}, false);
}
function guaguaDone(ctx,width,height,prizeUrl){
	var data=ctx.getImageData(0,0,width,height).data;
	for(var i=0,j=0;i<data.length;i+=4){
		if(data[i] && data[i+1] && data[i+2] && data[i+3]){
			j++;
		}
	}
	//console.log(j+"=="+(width*height*0.6))
	if(j<(width*height*0.8)){
		//加载中奖信息
		$.get(prizeUrl,function(json){
			$(container).css({'background-image':'url('+json.img+') '});
			if(json.status == 0){
				$.Dialog.confirm('提示',json.msg,"",json.jump_url);
				
			}else{
				$.Dialog.confirm('中奖啦',json.msg,"",json.jump_url);
			}
		})
	}
	
}