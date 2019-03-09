// 关于月份： 在设置时要-1，使用时要+1
$(function () {

  $('#calendars').calendars({
    ifSwitch: true, // 是否切换月份
    hoverDate: true, // hover是否显示当天信息
    backToday: true // 是否返回当天
  });

  

});

;(function ($, window, document, undefined) {

  var Calendar = function (elem, options) {
    this.$calendars = elem;

    this.defaults = {
      ifSwitch: true,
      hoverDate: false,
      backToday: false
    };

    this.opts = $.extend({}, this.defaults, options);

    // console.log(this.opts);
  };

  Calendar.prototype = {
    showHoverInfo: function (obj) { // hover 时显示当天信息
      var _dateStr = $(obj).attr('data');
      var offset_t = $(obj).offset().top + (this.$calendars_today.height() - $(obj).height()) / 2;
      var offset_l = $(obj).offset().left + $(obj).width();
      var changeStr = _dateStr.substr(0, 4) + '-' + _dateStr.substr(4, 2) + '-' + _dateStr.substring(6);
      var _week = changingStr(changeStr).getDay();
      var _weekStr = '';

      this.$calendars_today.show();

      this.$calendars_today
            .css({left: offset_l, top: offset_t-10})
            .stop()
            .animate({left: offset_l - 70, top: offset_t-10, opacity: 1});

      switch(_week) {
        case 0:
          _weekStr = '星期日';
        break;
        case 1:
          _weekStr = '星期一';
        break;
        case 2:
          _weekStr = '星期二';
        break;
        case 3:
          _weekStr = '星期三';
        break;
        case 4:
          _weekStr = '星期四';
        break;
        case 5:
          _weekStr = '星期五';
        break;
        case 6:
          _weekStr = '星期六';
        break;
      }

      this.$calendarsToday_date.text(changeStr);
      this.$calendarsToday_week.text(_weekStr);
    },
    getDays: function () {
      if (typeof(Storage) !== "undefined") {

        var self_day = $('.item-curDay').attr('data');

        // 取已经存储的时间
        if(localStorage.selfDay) self_day = localStorage.selfDay;

        // Store
        localStorage.setItem("selfDay", self_day);

        return self_day;

      } else {
          $.alert('抱歉！您的浏览器不支持 Web Storage ...');
      }
      
    },

    showCalendar: function () { // 输入数据并显示
      var self = this;
      var year = dateObj.getDate().getFullYear();
      var month = dateObj.getDate().getMonth() + 1;
      var dateStr = returnDateStr(dateObj.getDate());
      var firstDay = new Date(year, month - 1, 1); // 当前月的第一天

      this.$calendarsTitle_text.text(year + '/' + dateStr.substr(4, 2));

      this.$calendarsDate_item.each(function (i) {
        // allDay: 得到当前列表显示的所有天数
        var allDay = new Date(year, month - 1, i + 1 - firstDay.getDay());
        var allDay_str = returnDateStr(allDay);

        $(this).text(allDay.getDate()).attr('data', allDay_str);

        if (returnDateStr(new Date()) === allDay_str) {
          $(this).attr('class', 'item item-curDay');
        } else if (returnDateStr(firstDay).substr(0, 6) === allDay_str.substr(0, 6)) {
          $(this).attr('class', 'item item-curMonth');
        } else {
          $(this).attr('class', 'item');
        }
      });
    },

    renderDOM: function () { // 渲染DOM
      this.$calendars_title = $('<div class="calendars-title"></div>');
      this.$calendars_week = $('<ul class="calendars-week"></ul>');
      this.$calendars_date = $('<ul class="calendars-date"></ul>');
      this.$calendars_today = $('<div class="calendars-today"></div>');


      var _titleStr = '<span class="arrow-prev"><i class="iconfont icon-back"></i></span>'+
                      '<a href="#" class="title"></a>'+
                      '<span class="arrow-next"><i class="iconfont icon-more"></i></span>';
      var _weekStr = '<li class="item">日</li>'+
                      '<li class="item">一</li>'+
                      '<li class="item">二</li>'+
                      '<li class="item">三</li>'+
                      '<li class="item">四</li>'+
                      '<li class="item">五</li>'+
                      '<li class="item">六</li>';
      var _dateStr = '';
      var _dayStr = '<i class="triangle"></i>'+
                    '<p class="date"></p>'+
                    '<p class="week"></p>';

      for (var i = 0; i < 35; i++) {
        _dateStr += '<li class="item">26</li>';
      }


      this.$calendars_title.html(_titleStr);
      this.$calendars_week.html(_weekStr);
      this.$calendars_date.html(_dateStr);
      this.$calendars_today.html(_dayStr);


      this.$calendars.append(this.$calendars_title, this.$calendars_week, this.$calendars_date, this.$calendars_today);
      this.$calendars.show();
    },

    inital: function () { // 初始化
      var self = this;

      this.renderDOM();

      this.$calendarsTitle_text = this.$calendars_title.find('.title');
      this.$backToday = $('#backToday');
      this.$arrow_prev = this.$calendars_title.find('.arrow-prev');
      this.$arrow_next = this.$calendars_title.find('.arrow-next');
      this.$calendarsDate_item = this.$calendars_date.find('.item');
      this.$calendarsToday_date = this.$calendars_today.find('.date');
      this.$calendarsToday_week = this.$calendars_today.find('.week');

      this.showCalendar();

      if (this.opts.ifSwitch) {

        this.$arrow_prev.bind('click', function () {
          var _date = dateObj.getDate();

          dateObj.setDate(new Date(_date.getFullYear(), _date.getMonth() - 1, 1));

          self.showCalendar();

          pastTimeDiscolor();
          initSigninDay();
        });

        this.$arrow_next.bind('click', function () {
          var _date = dateObj.getDate();

          dateObj.setDate(new Date(_date.getFullYear(), _date.getMonth() + 1, 1));

          self.showCalendar();

          pastTimeDiscolor();
          initSigninDay();
        });

      }

      if (this.opts.backToday) {
        this.$backToday.bind('click', function () {
          if (!self.$calendarsDate_item.hasClass('item-curDay')) {
            dateObj.setDate(new Date());

            self.showCalendar();
          }
        });
      }

      this.$calendarsDate_item.click(function () {
        self.showHoverInfo($(this));
      }, function () {
        self.$calendars_today.css({left: 0, top: 0}).hide();
      });


      pastTimeDiscolor();
      initSigninDay();
    },

    constructor: Calendar
  };

  $.fn.calendars = function (options) {
    var calendars = new Calendar(this, options);

    return calendars.inital();
  };


  // ========== 使用到的方法 ==========

  var dateObj = (function () {
    var _date = new Date();

    return {
      getDate: function () {
        return _date;
      },

      setDate: function (date) {
        _date = date;
      }
    }
  })();

  function returnDateStr(date) { // 日期转字符串
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    month = month <= 9 ? ('0' + month) : ('' + month);
    day = day <= 9 ? ('0' + day) : ('' + day);

    return year + month + day;
  };

  function changingStr(fDate) { // 字符串转日期
    var fullDate = fDate.split("-");
    
    return new Date(fullDate[0], fullDate[1] - 1, fullDate[2]); 
  };


    // 过去的时间变灰色
    function pastTimeDiscolor() {
      var self_day = $('.item-curDay').attr('data');

      var storage = window.localStorage;

      if(!storage.selfDay) {
        storage.setItem('selfDay', self_day)
      }
      else {
        self_day = storage.selfDay;
      }
      console.log('------------' + storage.selfDay);

      // 当前日历所有项
      var all_day = $('.calendars-date .item');
      var all_day_arr = [];
      for(var j=0; j<all_day.length; j++) {
        all_day_arr.push($(all_day[j]).attr('data'));
      }

      console.log(all_day_arr);
      for(var i =0; i<all_day_arr.length; i++) {
        if(parseInt(all_day_arr[i]) < parseInt(self_day)) {
           $('.item[data=' + all_day_arr[i] +']').addClass('gray');
        }

    }
  }

  
    // 初始化已经签到到的天数
    function initSigninDay() {
      
      // 取已经签到的天数
      console.log(signin_day);
      for(var i=0; i<signin_day.length; i++) {
        $('.item[data=' + signin_day[i] +']').addClass('active');
      }
    }




})(jQuery, window, document);