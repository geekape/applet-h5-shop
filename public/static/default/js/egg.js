function check_subscribe(){
  //var has_subscribe = 1;
  if(has_subscribe=="0"){
      $.WeiPHP.showSubscribeTips({'qrcode': qrcode});
    return false; 
  }else{
    return true;
  }
}
var YesorNo = true;
function aClick() {
    if(err_msg != ""){
      $.alert(err_msg);
      return false;
    }
    if(!(check_subscribe())){
      return false;
      }
   
      $(".agg").off("click", aClick);
        var _this = $(this);
        _this.parents(".lanren").addClass("paused");
        _this.html('<img src="../img/activity/hammer.png" class="hammer"><img src="../img/activity/agg-puo.png" class="agg-puo">');
        $.post(join_url,function(res){
            setTimeout(function () {
                  _this.css({background:"none"}).find(".agg-puo").show();
                  setTimeout(function () {
                      if (YesorNo == true){
                          $("#sorryBox").show();
                          YesorNo = false;
                      }else {
                          $(".lanren").hide();
                          YesorNo = true;
                      }
                       $.alert(res.msg,function(){
                         window.location.reload();
                       });
                      

                  },500);
              },250);
        })
 }

 $(".agg").on("click", aClick);