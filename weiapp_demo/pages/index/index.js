//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    userImg: "../../images/defult_userimg.png",
    userName: "未登录",
  },
  //事件处理函数
  upload: function () {
    wx.navigateTo({
      url: '../upload/upload'
    })
  },
  qrcode: function () {
    wx.navigateTo({
      url: '../qrcode/qrcode'
    })
  },
  payment: function () {
    wx.navigateTo({
      url: '../payment/payment'
    })
  },
  contact: function () {
    wx.navigateTo({
      url: '../contact/contact'
    })
  },
  message: function () {
    wx.navigateTo({
      url: '../message/message'
    })
  },
  sms: function () {
    wx.navigateTo({
      url: '../sms/sms'
    })
  },
  onLoad: function () {
    var that = this,
      userInfo = wx.getStorageSync('userInfo')

    that.setData({
      userImg: userInfo.avatarUrl,
      userName: userInfo.nickName
    })
  }
})
