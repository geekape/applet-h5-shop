// pages/message/message.js
var app = getApp()
Page({
  data: {},
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
  },
  formSubmit: function (e) {
    console.log('form发生了submit事件，formId 为：', e.detail.formId)
    var formId = e.detail.formId
    wx.request({
      url: app.url + 'weiapp/Api/send_message&PHPSESSID=' + wx.getStorageSync('PHPSESSID'),
      data: { formId: formId, openid: wx.getStorageSync('openid') },
      success: function (res) {
        // success
        console.log(res)
        app.success('发送完成')
      }
    })
  },
})