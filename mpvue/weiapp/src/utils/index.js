
const host = 'https://localhost/index.php?pbid=72&s=/'

var Fly = require("flyio/dist/npm/wx")
var fly = new Fly()



// -------------------------------
/** 
** 请求封装
*/
function request(url, method, data, header = {}) {
  wx.showLoading({
    title: '加载中', //数据请求前loading
    mask: true
  })

  return new Promise((resolve, reject) => {

    if (method == "GET") {
      fly.get(host + url)
        .then(function (res) {
          wx.hideLoading()
          console.log(res)
          if(res.code == 0) {
            alert(res.msg)
          }
          resolve(res.data)
        })
        .catch(function (error) {
          wx.hideLoading()
          console.log(error)
        })
    }
    else {
      fly.post(host + url, data)
        .then(function (res) {
          wx.hideLoading()
          resolve(res.data)
        })
        .catch(function (error) {
          wx.hideLoading()
          console.log(error);
        })
    }



  })
}

function get(url, data) {
  return request(url, 'GET', data)
}

function post(url, data) {
  return request(url, 'POST', data)
}

// -------------------------------
/** 
** 登录封装
*/

// 判断登录
function toLogin() {
  const userInfo = wx.getStorageSync('userInfo');

  if (!userInfo) {
    wx.navigateTo({
      url: "/pages/shop/login/main"
    });
  } else {
    return true
  }
}

// 获取登录信息
function login() {
  const userInfo = wx.getStorageSync('userInfo');
  if (userInfo) {
    return userInfo;
  } else {
    return false
  }
}

// -------------------------------
/** 
** 时间戳转换时间
*/

function timeChange(time, isHsm) {
  var date = new Date(time * 1000);
  var Y = date.getFullYear() + '.';
  var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '.';
  var D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + ' ';
  var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
  var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
  var s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
  // 只传日期
  if(isHsm) {
      return Y + M + D
  } else {
      return Y + M + D + h + m + s
  }
  
}


function dateDiff(timestamp) {
  // 补全为13位
  var arrTimestamp = (timestamp + '').split('');
  for (var start = 0; start < 13; start++) {
    if (!arrTimestamp[start]) {
      arrTimestamp[start] = '0';
    }
  }
  timestamp = arrTimestamp.join('') * 1;

  var minute = 1000 * 60;
  var hour = minute * 60;
  var day = hour * 24;
  var halfamonth = day * 15;
  var month = day * 30;
  var now = new Date().getTime();
  var diffValue = now - timestamp;

  // 如果本地时间反而小于变量时间
  if (diffValue < 0) {
    return '不久前';
  }

  // 计算差异时间的量级
  var monthC = diffValue / month;
  var weekC = diffValue / (7 * day);
  var dayC = diffValue / day;
  var hourC = diffValue / hour;
  var minC = diffValue / minute;

  // 数值补0方法
  var zero = function (value) {
    if (value < 10) {
      return '0' + value;
    }
    return value;
  };

  // 使用
  if (monthC > 12) {
    // 超过1年，直接显示年月日
    return (function () {
      var date = new Date(timestamp);
      return date.getFullYear() + '年' + zero(date.getMonth() + 1) + '月' + zero(date.getDate()) + '日';
    })();
  } else if (monthC >= 1) {
    return parseInt(monthC) + "月前";
  } else if (weekC >= 1) {
    return parseInt(weekC) + "周前";
  } else if (dayC >= 1) {
    return parseInt(dayC) + "天前";
  } else if (hourC >= 1) {
    return parseInt(hourC) + "小时前";
  } else if (minC >= 1) {
    return parseInt(minC) + "分钟前";
  }
  return '刚刚';
}


// -------------------------------
/** 
 ** 公共方法封装
 */


//  支付
function goPay(id) {
  get("shop/api/do_pay?out_trade_no=" + id).then(res => {
    if (res.code == 0) {
      wx.showToast({
        title: res.msg,
        icon: 'none'
      });
      return false
    }
    // 测试专用
    if (res.code == 2) {
      wx.showToast({
        title: '支付成功',
        icon: 'success'
      });
      setTimeout(function () {
        wx.switchTab({ url: "../center/main" });
      }, 1000);
      return false
    }
    wx.requestPayment({
      timeStamp: res.pay.timeStamp + "",
      nonceStr: res.pay.nonceStr,
      package: res.pay.package,
      signType: "MD5",
      paySign: res.pay.paySign,
      success(res) {
        wx.switchTab({ url: "../center/main" });
      },
      fail(res) { wx.switchTab({ url: "../center/main" }); }
    })
  })
}

//  确认收货
function goReceiving(id) {
  wx.showModal({
    title: "提示",
    content: "确定收货？",
    success(res) {
      if (res.confirm) {
        get("shop/api/confirm_get?id=" + id).then(res => {
          if (res.code == 1) {
            wx.showToast({
              title: '收货成功',
              icon: 'none'
            });
            wx.switchTab({ url: "../center/main" });
          } else {
            wx.showToast({
              title: '收货失败',
              icon: 'none'
            });
          }
        });
      } else if (res.cancel) {
        console.log('用户点击取消')
      }
    }
  })
}


export {
  host,
  get,
  post,
  toLogin,
  login,
  timeChange,
  dateDiff,
  goPay,
  goReceiving
}





