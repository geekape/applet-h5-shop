 function check_subscribe(){
    	//var has_subscribe = 1;
    	if(has_subscribe=="0"){
    	    $.WeiPHP.showSubscribeTips({'title':title,'qrcode': qrcode});
    		return false;	
    	}else{
    		return true;
    	}
    }
$(function() {
				if(typeof(jplist) == "undefined"){jplist={};}
 
				$("#wheelcanvas").rotate({
                                        duration: 1000,
                                        angle: 0,
                                        animateTo: -90,
                                        easing: $.easing.easeOutSine
										});	
                var jsonData = {};
                var isStart = false;
                function lottery() {
                    var start = function() {
                    	if(!(check_subscribe())){
                    		return false;
                    	}
                    	if(checkstatus == 0){
                    		$.Dialog.confirm('提示',msg);
                    		return false;
                    	}
                    	
//                    	$.post(checkurl,{'games_id':games_id},function(dd){
//            				if(dd.status == 0){
//            					$.Dialog.confirm('提示',dd.msg);
//            					return false;
//            				}
//            			})
            			
                        if (isStart) {
                            return false;
                        }
                        isStart = true;
                        $.ajax({
                            type: 'POST',
                            url: joinurl,
                            dataType: 'json',
                            cache: false,
                            error: function() {
								$.Dialog.confirm('温馨提示','哎呀，转盘罢工了,快联系客服来修理吧！');
								isStart = false;
                            },
                            success: function(json) {
									$("#wheelcanvas").rotate({
										duration: 10000,
										angle: -90,
										animateTo: 3600+json.angle,
										easing: $.easing.easeOutSine,
										callback: function() {
											if(json.status == 0){
												$.Dialog.confirm('提示',json.msg);
											}else{
												$.Dialog.confirm('中奖啦',json.msg,"",json.jump_url);
											}
												$.ajax({
													    type:'POST',
													    async:false,
													    url:checkurl,
													    data:{'games_id':games_id},
													    success:function(dd){
													    	checkstatus = dd.status;
															if(dd.status == 0){
																msg = dd.msg;
															}
														}
													});
		
											isStart = false;
										}
									});
								
										
                            }
                        });
                    };
                    $("#startbtn").bind("click", start);
                }
                var colors = ["#ff6501", "#f6f09f", "#FFFFFF"];     
				var colors1 = ["#f6f09f", "#ff6501"];     				
                var cwidth = 250;
                var startAngle = 0;
                var arc = Math.PI / jplist.length*2;
                var spinTimeout = null;
                var spinArcStart = 10;
                var spinTime = 0;
                var spinTimeTotal = 0;
                var ctx;
                function draw() {
                    drawRouletteWheel();
                }
                function drawRouletteWheel() {
                    var canvas = document.getElementById("wheelcanvas");
                    if (canvas.getContext) {
                        var outsideRadius = 117;
                        var textRadius = 95;
                        var insideRadius = 20;
                        ctx = canvas.getContext("2d");
						//创建新的图片对象
						var img = new Image();						
						//指定图片的URL						
						img.src = zpimg;					
						//浏览器加载图片完毕后再绘制图片						
						img.onload = function(){						
							ctx.clearRect(0, 0, cwidth, cwidth);											
							ctx.drawImage(img, 0, 0);
							ctx.strokeStyle = "#f9c700";
							ctx.lineWidth = 4;
							ctx.font = '10px Microsoft Yahei';
							for (var i = 0; i < jplist.length; i++) {
								var angle = startAngle + i * arc;
								if (i % 2 == 1) {
									gradient = ctx.createRadialGradient(cwidth / 2, cwidth / 2, 0, cwidth / 2, cwidth / 2, cwidth / 2);
									gradient.addColorStop(0, '#fff');
									gradient.addColorStop(0.7, '#f6f09f');
									ctx.fillStyle = gradient;
								} else {
									ctx.fillStyle = colors[(i % 2)];
								}
								ctx.beginPath();
								//计算片数								
								ctx.arc(cwidth / 2, cwidth / 2, outsideRadius, angle, angle + arc, false);
								ctx.arc(cwidth / 2, cwidth / 2, insideRadius, angle + arc, angle, true);
								ctx.shadowBlur = 5;
								//ctx.shadowColor = "#666";
								ctx.stroke();
								ctx.fill();
								ctx.save();
								//ctx.shadowOffsetX = -1;
								//ctx.shadowOffsetY = -1;
								ctx.shadowBlur = 0;
								//ctx.shadowColor = "rgb(220,220,220)";
								ctx.fillStyle = "black";
								ctx.translate(cwidth / 2 + Math.cos(angle + arc / 2) * textRadius, cwidth / 2 + Math.sin(angle + arc / 2) * textRadius);
								ctx.rotate(angle + arc / 2 + Math.PI / 2);
								var text = jplist[i].title;
								var pic = jplist[i].picUrl;
								//var pic = 'http://a.36krcnd.com/photo/2014/fd7062bf51eed9b623d7007a5c030e05.png';
								//绘制文字
								drawText(text,pic,ctx,colors1[(i % 2)]);
								//ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
								ctx.restore();
							}
							
						};
						
                    }
                } 
				
				
				
				function drawText(text,pic,ctx,c){	
					ctx.fillStyle = c;
					//绘制图片
					var tempImg = new Image();
					tempImg.src = pic;
					ctx.drawImage(tempImg, -20, 7,40,40);
					
					
					//绘制文字
					for(var i=0;i<text.length;i++){						
						ctx.fillText(text[i], -ctx.measureText(text).width / 2+i*11, 0);
					}
				}
				$(window).load(function(){
					draw();
					lottery();
				});

});