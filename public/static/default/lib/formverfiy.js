// 封装的表单验证插件
;
(function($, window, document, undefined) {
  //定义formVerify的构造函数
  var formVerify = function(ele, opt) {
    this.$element = ele,
      this.defaults = {
        intTest: true
      },
      this.options = $.extend({}, this.defaults, opt)
  }
  //定义formVerify的方法
  formVerify.prototype = {
    // 检测正整数
    intTest: function() {
      return this.$element.find('input[type="number"]').bind('input porpertychange', function() {
        var selfVal = $(this).val();
        if (selfVal.indexOf('.') != -1 || selfVal.indexOf('-') != -1) {
          var toast = swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000
          });
          toast({
            type: 'error',
            title: '只能输入整数'
          })
          $(this).val('');
        }
      })
    }
  }
  //在插件中使用formVerify对象
  $.fn.formVerify = function(options) {
    //创建formVerify的实体
    var formverify = new formVerify(this, options);
    //调用其方法
    return formverify.intTest();
  }
})(jQuery, window, document);