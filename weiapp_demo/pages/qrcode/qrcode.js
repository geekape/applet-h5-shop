// pages/qrcode/qrcode.js
var app = getApp()
Page({
  data: {
    a_src: '',
    b_src: '',
    c_src: '',
  },

  // 获取A模式下的二维码
  getCodeByA: function () {
    var that = this,
      url = app.url + 'weiapp/Api/getwxacode&PHPSESSID=' + wx.getStorageSync('PHPSESSID');
    wx.request({
      url: url,
      data: { type: 'A', param: { path: 'pages/qrcode/qrcode', width: 400 } },
      success: function (res) {
        if (res.data.status == 0) {
          that.showError(res.data.msg)
        } else {
          that.setData({ 'a_src': res.data.url })
        }
      }
    })
  },
  // 获取B模式下的二维码
  getCodeByB: function () {
    var that = this,
      url = app.url + 'weiapp/Api/getwxacode&PHPSESSID=' + wx.getStorageSync('PHPSESSID');
    wx.request({
      url: url,
      data: { type: 'B', param: { scene: 'qrcode', width: 400 } },
      success: function (res) {
        if (res.data.status == 0) {
          that.showError(res.data.msg)
        } else {
          that.setData({ 'b_src': res.data.url })
        }
      }
    })

  },
  // 获取C模式下的二维码
  getCodeByC: function () {
    var that = this,
      url = app.url + 'weiapp/Api/getwxacode&PHPSESSID=' + wx.getStorageSync('PHPSESSID');
    wx.request({
      url: url,
      data: { type: 'C', param: { path: 'pages/qrcode/qrcode', width: 400 } },
      success: function (res) {
        if (res.data.status == 0) {
          that.showError(res.data.msg)
        } else {
          that.setData({ 'c_src': res.data.url })
        }
      }
    })
  },
  showError: function (msg) {
    wx.showToast({
      title: msg,
      icon: 'success',
      duration: 3000
    })
  },
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
    this.getCodeByA()
    this.getCodeByB()
    this.getCodeByC()
  },
})