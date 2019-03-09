/**
 * 提示弹窗
 * @param  {String} title   提示文字
 * @param  {String} btnText 按钮文字
 * @return {[type]}         [description]
 */
function hintDialog(title, btnText) {
	// 设置默认值
	title ? title=title : title="您还不是会员!需要开通会员后才可以参与活动";
	btnText ? btnText=btnText : btnText="开通会员";

    var hint_box = '<div class="m-hint-box"><div class="block"><div class="text">'
    + title + '</div><a href = "'
     + CARD_URL + '" >' 
     + btnText + '</a></div></div>"';

     $('body').append(hint_box);

     $('.m-hint-box').click(function() {
     	$(this).remove();
     })
}


/**
 * 带图的弹窗
 * @param  {String} imgUrl [图片地址]
 * @param  {String} title  [标题]
 * @param  {String} msg    [提示文字]
 * @return {[type]}        [description]
 */
function imHintDialog(imgUrl, title, msg) {
	// 设置默认值
	imgUrl ? imgUrl=imgUrl : imgUrl="__STATIC__/default/img/head.jpg";
	title ? title=title : title="长按二维码关注公众号";
	msg ? msg=msg : msg="需关注公众号才能查看";


    var hint_box = '<div class="m-hint-box"><div class="block"><div class="pic"><img src="'
    + imgUrl + '"></div><h3>'
    + title + '</h3><p class="hint-text">'
    + msg + '</p></div></div>';

     $('body').append(hint_box);

     $('.m-hint-box').click(function() {
     	$(this).remove();
     })
}