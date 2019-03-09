$(function () {
    var $hammer = $("#hammer"),
        $tips = $(".info"),
        $eggList = $(".egg li"),//金蛋父级
        $egg = $(".goldegg"),//金蛋
        $change = $("#change"),//剩余次数
        length = $egg.length,
        data = {count: 5},//次数
        arr = [],
        openArr,//记录被砸开的蛋的下标数组
        rem = 75;

    /*轮流提示*/
    $(function () {
        if (!!$.cookie("eggIndex")) {//如果存在cookie，也就是有金蛋被砸开
            openArr = $.cookie("eggIndex").split(",");//将cookie变为数组
            for (var i = 0; i < openArr.length; i++) {
                arr.push(parseFloat(openArr[i]));//将上次cookie存入数组以免上次cookie被覆盖
                $egg.eq(parseFloat(openArr[i])).prop("src", "{:ADDON_PUBLIC_PATH}/new/egg/image/step3.png");
                $egg.eq(parseFloat(openArr[i])).removeClass("init");
                $egg.eq(parseFloat(openArr[i])).data("mark", false);//更改金蛋状态为已砸开
            }
        }

        //初始跳动
        $egg.eq(length).addClass("jump");
        $tips.eq(length).show();
        setInterval(function () {
            //金蛋跳动
            length++;
            length %= 9;
            $egg.eq(length - 1 < 0 && 8 || length - 1).removeClass("jump");
            $tips.eq(length - 1 < 0 && 8 || length - 1).hide();
            reback();
            $egg.eq(length).addClass("jump");
            $tips.eq(length).show();
        }, 1000);
    });

    //跳过砸开的金蛋
    function reback() {
        if (!$egg.eq(length).hasClass("init")) {//若已砸开
            length++;
            length %= 9;
            reback();
        }
    }

    /*砸蛋事件*/
    for (var i = 0; i < length; i++) {
        $egg.eq(i).data("mark", true);//判断金蛋是否砸开，true表示可砸
        $eggList.eq(i).data("i", i);
        $eggList.eq(i).click(function () {
            //设定剩余抽奖次数，判断用户是否还能点击
            if (data.count > 0) {
                $egg.eq($(this).data("i")).data("mark") ? eggChange($(this).data("i")) : alert("这枚金蛋已经被您砸开了");//判断金蛋是否已砸开
            } else {
                alert("您当前砸蛋次数为0，无法砸蛋");
            }
        });
    }

    /*砸蛋事件的处理*/
    function eggChange(i) {
        //砸蛋次数的变化
        data.count--;
        $change.html(data.count);
        $hammer.removeClass("shak");//清除锤子晃动动画
        //锤子砸蛋的位置
        (i === 0 || i === 3 || i === 6) && ($hammer.css("left", 165 / rem + "rem"));
        (i === 1 || i === 4 || i === 7) && ($hammer.css("left", 415 / rem + "rem"));
        (i === 2 || i === 5 || i === 8) && ($hammer.css("left", 665 / rem + "rem"));
        (i === 0 || i === 1 || i === 2) && ($hammer.css("top", 60 / rem + "rem"));
        (i === 3 | i === 4 || i === 5) && ($hammer.css("top", 280 / rem + "rem"));
        (i === 6 | i === 7 || i === 8) && ($hammer.css("top", 500 / rem + "rem"));
        //锤子返回
        setTimeout(function () {
            $hammer.css("left", 665 / rem + "rem");
            $hammer.css("top", 60 / rem + "rem");
        }, 1500);

        //金蛋破裂及锤子动画
        setTimeout(function () {
            $hammer.addClass("hit");
            $egg.eq(i).prop("src", "image/step1.png");
            setTimeout(function () {
                $egg.eq(i).prop("src", "image/step2.png");
            }, 300);
            setTimeout(function () {
                $egg.eq(i).prop("src", "image/step3.png");
                $egg.eq(i).removeClass("init");
                //clickData(false);//ajax回调
                win();
                $hammer.removeClass("hit");//清除锤子砸蛋动画
                $hammer.addClass("shak");

                //记录被砸开的蛋
                arr.push(i);//存入每个砸开蛋的下标
                $.cookie("eggIndex", arr, {expires: 1});//存入cookie
            }, 600);
        }, 600);
        $egg.eq(i).data("mark", false);//更改金蛋状态为已砸开
    }
});





