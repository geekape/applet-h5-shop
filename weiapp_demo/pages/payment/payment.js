// pages/payment/payment.js
var app = getApp()
Page({
  data: {
    money: 1
  },
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
  },
  order: function () {
    var url = app.url + 'weiapp/Api/payment&PHPSESSID=' + wx.getStorageSync('PHPSESSID')
    var that = this
    console.log('====openid====')
    console.log(wx.getStorageSync('openid'))
    wx.request({ //让服务器端统一下单，并返回小程序支付的参数
      url: url,
      data: {
        money: that.data.money,
        openid: wx.getStorageSync('openid')
      },
      success: function (res) {
        if (res.data.status == 0) {
          wx.showToast({
            title: res.data.msg,
            icon: '../../images/icon_wrong.png',
            duration: 2000,
          })
        } else { //服务器参数返回正常，调用小程序支付接口
          that.payment(res.data)
        }
      }
    })
  },
  payment: function (data) {
    wx.requestPayment({
      'timeStamp': data.timeStamp,
      'nonceStr': data.nonceStr,
      'package': data.package,
      'signType': data.signType,
      'paySign': data.paySign,
      success: function (res) {
        wx.showToast({
          title: '支付成功',
          icon: 'success',
          duration: 2000,
        })
        console.log(res)
      },
      fail: function (res) {
        wx.showToast({
          title: res.errMsg,
          icon: '../../images/icon_wrong.png',
          duration: 2000,
        })
        console.log(res)
      }
    })
  }
})