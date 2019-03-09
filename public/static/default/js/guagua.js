var canvas_w = $('.scratch_container').width();
var prize_pic = '__STATIC__/default/img/activity/guagua_thank.png';  //奖品图片(默认)
var guagua = document.getElementById('guagua');
var ctx = guagua.getContext('2d');

if(isPc()) {
  canvas_w-=15;
}

// 判断是否手机还是pc
function isPc() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
    
    function check_subscribe(){
        //var has_subscribe = 1;
        if(has_subscribe=="0"){
            $.WeiPHP.showSubscribeTips({'qrcode': qrcode});
             return false;  
        }else{
             if(err_msg != ""){

                  if(first_dialog == 0) {
              console.log(first_dialog);
              $.alert(err_msg, function() {window.location.reload();});
              first_dialog=1;
              return false;
            }
                  
              }
            return true;
        }
    }
    
    $('#guagua').on('touchstart',function(e) {

    });
    
    var isfirst=1;
    $('#guagua').on('click',function(){

    });


    // 防止弹窗重叠
    var first_dialog = 0;

    var scratch = new Scratch({
        canvasId: 'guagua',
        imageBackground: '__STATIC__/default/img/activity/guagua_thank.png',
        sceneWidth: canvas_w,
        sceneHeight: 150,
        pictureOver: '__STATIC__/default/img/activity/guagua_bg.png',
        cursor: {
            png: '__STATIC__/default/img/activity/piece.png',
            cur: '__STATIC__/default/img/activity/piece.cur',
            x: '20',
            y: '17'
        },
        radius: 25,
        nPoints: 45,
        percent: 50, //刮百分之多少回调
        callback: function () {
          if(first_dialog == 0) {
            first_dialog = 1;
            
            drawImg(prize_pic);
            $.alert(jmsg,function(){
              window.location.reload();
            });
    

          }



        },
        pointSize: { x: 8, y: 8}
    });


    guagua.addEventListener('touchstart', disTouch);
    guagua.addEventListener('touchmove', disTouch);
    guagua.addEventListener('touchend', disTouch);

    function disTouch(e) {
        if(!(check_subscribe())){
                return false;
            }
        if(isfirst==1){
            do_draw();
        }
        isfirst=0;
      e.preventDefault();
      
    }
    
    function do_draw(){
        $.post(join_url,function(res){
//    
        console.log('---------------');
            console.log(res);
//          if(res.title==''){
//              res.title='谢谢参与!';
//          }
            prize_pic=res.img;
            jmsg=res.msg;
            jump_url=res.jump_url
        });
    }


    function drawImg(url) {
      var img = new Image();
      img.src = url;
      img.onload = function () {
        ctx.drawImage(img, Math.floor(guagua.width/2), Math.floor(guagua.height/2), 50, 50);
      }
    }