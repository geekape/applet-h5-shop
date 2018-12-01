var $maskRule = $("#mask-rule"),//规则遮罩层
    $mask = $("#mask"),//红包遮罩层
    $winning = $(".winning"),//红包
    $card = $("#card"),
    $close = $("#close");
    //link = false;//判断是否在链接跳转中

//规则
$(".rule").click(function () {
    $maskRule.show();
});
$("#close-rule").click(function () {
    $maskRule.hide();
});

/*中奖信息提示*/
function win() {
    //遮罩层显示
    $mask.show();
    $winning.addClass("reback");
    setTimeout(function () {
        $card.addClass("pull");
    }, 500);

    //关闭弹出层
    $("#close,.win,.btn").click(function () {
    //$close.click(function () {
        $mask.hide();
        $winning.removeClass("reback");
        $card.removeClass("pull");
    });
    /*$(".win,.btn").click(function () {
        link = true;
    });*/
}

//此处可以在commonjs中合并
function queryString(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.search);
    if(results === null) {
        return "";
    }
    else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}



