function check_subscribe(){
	//var has_subscribe = 1;
	if(has_subscribe=="0"){
	    $.WeiPHP.showSubscribeTips({'qrcode': qrcode});
		return false;	
	}else{
		return true;
	}
}
var turnplate={
    restaraunts:[],       //大转盘奖品名称
    colors:[],          //大转盘奖品区块对应背景颜色
    outsideRadius:192,      //大转盘外圆的半径
    textRadius:155,       //大转盘奖品位置距离圆心的距离
    insideRadius:68,      //大转盘内圆的半径
    startAngle:0,       //开始角度
    
    bRotate:false       //false:停止;ture:旋转
};

$(document).ready(function(){
	var draw_msg='';
  //动态添加大转盘的奖品与奖品区域背景颜色

  // 后台取的奖品数组
 if(typeof(prize_arr) === 'undefined'){
	 prize_arr = [
		   {
		      name: "谢谢参与",
		      url: "www.baidu.com",
		      id: 0,
		    }
     ];
 }


  turnplate.restaraunts = prize_arr;
  turnplate.colors = ["#7108bd", "#84a1db", "#7108bd", "#84a1db","#7108bd", "#84a1db"];

  
  var rotateTimeOut = function (){
    $('#wheelcanvas').rotate({
      angle:0,
      animateTo:2160,
      duration:8000,
      callback:function (){
        $.alert('网络超时，请检查您的网络设置！',function () {
          window.location.reload();
        });
      }
    });
  };

  //旋转转盘 item:奖品位置; txt：奖品名称
  var rotateFn = function (item, txt){
    var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
    if(angles<270){
      angles = 270 - angles; 
    }else{
      angles = 360 - angles + 270;
    }
    $('#wheelcanvas').stopRotate();
    $('#wheelcanvas').rotate({
      angle:0,
      animateTo:angles+1800,
      duration:8000,
      callback:function (){
//        $.alert('抽到了['+txt+']');
        $.alert(draw_msg, function () {
          window.location.reload();
        });
        turnplate.bRotate = !turnplate.bRotate;
        draw_msg='谢谢参与';
      }
    });
  };

  $('.pointer').click(function (){
	  if(turnplate.bRotate)return;
	  if(err_msg != ""){
		  $.alert(err_msg, function() {
          window.location.reload();
      });
		  return false;
	  }
	  if(!(check_subscribe())){
  		return false;
  	  }
	  var prize_id=0;
	  $.ajax({
		  type:'post',
		  url:join_url,
		  async:false,
		  success:function(res){
			  console.log(res);
			  prize_id= res.award_id;
			  draw_msg=res.msg;
//			  $.alert(res.msg,function(){
//   	      	   if(res.jump_url != ''){
//   	          	   window.location.href=res.jump_url;
//   	             }
//   	         });
		  }
	  });
//	  console.log(prize_id,draw_msg);

    // 取中奖奖品索引
  
    var self_winning_prize = 0;
    for(var i=0;i< prize_arr.length;i++){
    	if(prize_id == prize_arr[i]['id']){
    		self_winning_prize=i;
    	}
    }
   
    turnplate.bRotate = !turnplate.bRotate;
    

    // 旋转转盘
    rotateFn(self_winning_prize+1, turnplate.restaraunts[self_winning_prize].name);

  });
  
  drawRouletteWheel();
});

function rnd(n, m){
  var random = Math.floor(Math.random()*(m-n+1)+n);
  return random;
}


//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
//window.onload=function(){
//  drawRouletteWheel();
//};

function drawRouletteWheel() {
	console.log(turnplate.restaraunts);
  var canvas = document.getElementById("wheelcanvas");    
  if (canvas.getContext) {
    //根据奖品个数计算圆周角度
    var arc = Math.PI / (turnplate.restaraunts.length/2);
    var ctx = canvas.getContext("2d");
    //在给定矩形内清空一个矩形
    ctx.clearRect(0,0,430,430);
    //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
    ctx.strokeStyle = "#FFBE04";
    //font 属性设置或返回画布上文本内容的当前字体属性
    ctx.font = '16px Microsoft YaHei';   
    for(var i = 0; i < turnplate.restaraunts.length; i++) {       
      var angle = turnplate.startAngle + i * arc;
      ctx.fillStyle = turnplate.colors[i];
      ctx.beginPath();
      //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
      ctx.arc(215, 215, turnplate.outsideRadius, angle, angle + arc, false);    
      ctx.arc(215, 215, turnplate.insideRadius, angle + arc, angle, true);
      ctx.stroke();  
      ctx.fill();
      //锁画布(为了保存之前的画布状态)
      ctx.save();   
      
      //----绘制奖品开始----
      ctx.fillStyle = "#fff";
      var text = turnplate.restaraunts[i].name;
      var line_height = 17;

      //  绘制图片
      var img_url = turnplate.restaraunts[i].url;


      //translate方法重新映射画布上的 (0,0) 位置
      ctx.translate(215 + Math.cos(angle + arc / 2) * turnplate.textRadius, 215 + Math.sin(angle + arc / 2) * turnplate.textRadius);


      
      //rotate方法旋转当前的绘图
      ctx.rotate(angle + arc / 2 + Math.PI / 2);
      
      /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
      if(text.indexOf("M")>0){
        
      }else if(text.indexOf("M") == -1 && text.length>6){
      //奖品名称长度超过一定范围 
        text = text.substring(0,6)+"||"+text.substring(6);
        var texts = text.split("||");
        for(var j = 0; j<texts.length; j++){
          ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
        }
      }else{
        //在画布上绘制填色的文本。文本的默认颜色是黑色
        //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
        ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
      }
      
      //添加对应图标
      if(img_url){
        var img = new Image();
        // var root = '/static';
        // img.src = root + "/default/img/activity/goods.png";
        img.src = img_url;
        console.log(img);
        ctx.drawImage(img,-30,10, 50,50);  
        
        
      }
      //把当前画布返回（调整）到上一个save()状态之前 
      ctx.restore();
      //----绘制奖品结束----
    }     
  } 
}