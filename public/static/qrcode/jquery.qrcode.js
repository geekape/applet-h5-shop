(function( $ ){
	var globle_options;
	$.fn.qrcode = function(options) {
		// if options is string, 
		if( typeof options === 'string' ){
			options	= { text: options };
		}

		// set default values
		// typeNumber < 1 for automatic calculation
		options	= $.extend( {}, {
			render		: "canvas",
			width		: 256,
			height		: 256,
			typeNumber	: -1,
			correctLevel	: QRErrorCorrectLevel.H,
                        background      : "#ffffff",
                        foreground      : "#000000"
		}, options);
		globle_options = options;
		var createCanvas	= function(){
			// create the qrcode itself
			var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
			qrcode.addData(options.text);
			qrcode.make();

			// create canvas element
			var canvas	= document.createElement('canvas');
			canvas.width	= options.width;
			canvas.height	= options.height;
			var ctx		= canvas.getContext('2d');

			// compute tileW/tileH based on options.width/options.height
			var tileW	= options.width  / qrcode.getModuleCount();
			var tileH	= options.height / qrcode.getModuleCount();

			// draw in the canvas
			for( var row = 0; row < qrcode.getModuleCount(); row++ ){
				for( var col = 0; col < qrcode.getModuleCount(); col++ ){
					ctx.fillStyle = qrcode.isDark(row, col) ? options.foreground : options.background;
					var w = (Math.ceil((col+1)*tileW) - Math.floor(col*tileW));
					var h = (Math.ceil((row+1)*tileW) - Math.floor(row*tileW));
					ctx.fillRect(Math.round(col*tileW),Math.round(row*tileH), w, h);  
				}	
			}
			// return just built canvas
			return canvas;
		}

		// from Jon-Carlos Rivera (https://github.com/imbcmdth)
		var createTable	= function(){
			// create the qrcode itself
			var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
			qrcode.addData(options.text);
			qrcode.make();
			
			// create table element
			var $table	= $('<table></table>')
				.css("width", options.width+"px")
				.css("height", options.height+"px")
				.css("border", "0px")
				.css("border-collapse", "collapse")
				.css('background-color', options.background);
		  
			// compute tileS percentage
			var tileW	= options.width / qrcode.getModuleCount();
			var tileH	= options.height / qrcode.getModuleCount();

			// draw in the table
			for(var row = 0; row < qrcode.getModuleCount(); row++ ){
				var $row = $('<tr></tr>').css('height', tileH+"px").appendTo($table);
				
				for(var col = 0; col < qrcode.getModuleCount(); col++ ){
					$('<td></td>')
						.css('width', tileW+"px")
						.css('background-color', qrcode.isDark(row, col) ? options.foreground : options.background)
						.appendTo($row);
				}	
			}
			// return just built canvas
			return $table;
		}
		if(options.refresh_count!=1){
			doQrCodeInterval(options);
		}
		return this.each(function(){
			var element	= options.render == "canvas" ? createCanvas() : createTable();
			$(this).html('');
			jQuery(element).appendTo(this);
			
		});
		
	};
	
	function doQrCodeInterval(options){
		if(options.refresh && /^(http:\/\/)/gi.test(globle_options.text)){
			options.time = options.time?options.time:30000;
			options.refresh_count = 1;
			countDown(options.time);
			setInterval(function(){
						if(/(cTime=)/gi.test(globle_options.text)){
							globle_options.text = globle_options.text.replace(/(cTime=\d+)/,'cTime='+new Date().getTime());
						}else{
							if(/(\?)/gi.test(globle_options.text)){
								globle_options.text = globle_options.text +'&cTime='+ new Date().getTime();
							}else{
								globle_options.text = globle_options.text +'?cTime='+ new Date().getTime();	
							}
						}
					$('#qrCode').qrcode(globle_options);
					countDown(options.time);
					//console.log(globle_options.text);
				},options.time);
			
			
		}
	}
	function countDown(time){
		var tempTime = time/1000;
		$('.qr_time_tips').text('二维码有效期剩余'+tempTime+'秒').css('text-align','center');
		var tempInteval = setInterval(function(){
			tempTime--;
			$('.qr_time_tips').text('二维码有效期剩余'+tempTime+'秒').css('text-align','center');
			if(tempTime==0){
				clearInterval(tempInteval);
			}
		},1000);
	}
	
})( jQuery );
