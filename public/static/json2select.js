/*
 * json2select
 *
 * Copyright (c) 2008 Shawphy (shawphy.com)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/*
 * Create selects from JSON
 * 
 * @example $("#selectt").json2select( json, dft, name, deep);
 * @desc 在#selectt中通过d创建一组关联的select
 *
 * @param json,格式如下
 *   var json=[
 *   	{
 *   		t:"欧洲某地",
 *   		a:"欧洲"
 *   	},
 *   	{
 *   		t:"中国某地",
 *   		a:"中国",
 *   		d:[
 *   			{
 *   				t:"上海",
 *   				a:"上海"
 *   			},
 *   			{
 *   				t:"云南某地",
 *   				a:"云南某地",
 *   				d:[
 *   					{
 *   						t:"大理",
 *   						a:"大理"
 *   					}
 *   				]
 *   			}
 *   		]
 *   	},
 *   	{
 *   		t:"日本某地",
 *   		a:"日本",
 *   		d:[
 *   			{
 *   				t:"东京",
 *   				a:"东京"
 *   			},
 *   			{
 *   				t:"北海道",
 *   				a:"北海道",
 *   				d:[
 *   					{
 *   						t:"北海道的某个地方",
 *   						a:"北海道的某个地方"
 *   					}
 *   				]
 *   			}
 *   		]
 *   	}
 *   ];
 * @param dft,数组，设置默认值，如["中国","云南","大理"]
 * @param name,字符串，默认值：sel，用于设置select的name的前缀
 * @param deep,整形数字，默认值：0，用于设置初始的深度，如设置为0，则第一个select的name属性就是sel0
 * @return 调用它的对象
 * @type jQuery对象
 *
 */

;(function($) {
$.fn.json2select=function(json,dft,name,deep,css) {
	//参数初始化
	var _this=this,				//保存呼叫的对象
		name=name||"sel",		//如果未提供名字，则为默认为sel
		deep=deep||0,			//深度，默认为0，即生成的select的name=sel0
		dft=dft||[],			//默认值
		css=css||'height: 150px; width: 140px;';
	//换内容的时候删除旧的select
	$("[name="+name+deep+"]",_this).nextAll().remove();
	if (json[0]) {
		//新建一个select
		var slct=$("<select name='"+name+$("select",_this).length+"' id='"+name+$("select",_this).length+"'></select>");
		//建立一个默认项，value为空，修改请保留为空
		$.each(json,function(i,sd) {
			//添加项目，并用data将其子元素附加在这个option上以备后用。
			if(i == 0){
				$("<option value='"+sd.a+"' selected='selected' id='xxx'>"+sd.t+"</option>").appendTo(slct).data("d",sd.d||[]);
			}else {
				$("<option value='"+sd.a+"' >"+sd.t+"</option>").appendTo(slct).data("d",sd.d||[]);
			}
		});

		$("#xxx").select(); 
		//绑定这个select的change事件
		slct.change(function(e,dftflag) {
			//如果选的不是value为空的，则调用方法本身。如果已经初始化过了,即，不是由trigger触发的，而是手工点的，则不将dft传递进去。
			$(this).val()&&_this.json2select($(":selected",this).data("d"),dftflag?dft.slice(1):[],name,$(this).attr("name").match(/\d+/)[0]);
			//设置初始值，并且触发change事件，传递true参数进去。
			
			var arrayObj = new Array();
			$('#cascade_'+name+' select').each(function() {
                var val = $(this).val();
				if(val){
					arrayObj. push(val);
				}
            });
			var res = arrayObj.join(',');
			$('#data_'+name).val(res);
			
		}).appendTo(_this).val(dft[0]||0).trigger("change",[true]);
	}
	//返回jQuery对象
	return _this;
};
})(jQuery);