var $ajax = require('../../utils/network_util.js')
var app = getApp();

Page({
  data: {
    mobile: '',
    code: '',
    resCode: '',
    errorCode: false,
    infoCode: '获取验证码',
    sendCode: false,
  },
  bindMobile: function (e) {
    this.setData({
      mobile: e.detail.value
    })
  },
  bindCode: function (e) {
    this.setData({
      code: e.detail.value
    })
  },
  getCode: function () {
    var url = app.url + 'weiapp/Api/sendCode?PHPSESSID=' + wx.getStorageSync('PHPSESSID')
    var that = this
    var mobile = this.data.mobile
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/
    var res = myreg.test(mobile)
    if (mobile == '' || !res) {
      wx.showToast({
        title: '请填写正确的手机号码',
        icon: 'success',
        duration: 2000
      })
      return false;
    }

    $ajax._get(
      url,
      {
        mobile: mobile
      },
      function (res) {
        // var code = res.code
        var time = ''
        var num = 60
        that.setData({
          // resCode:code,
          sendCode: true
        })

        time = setInterval(function () {
          num--
          if (num == 0) {
            clearInterval(time)
            that.setData({
              sendCode: false,
              infoCode: '重新获取验证码'
            })
          } else {
            that.setData({
              infoCode: num + ' 秒'
            })
          }


        }, 1000)

      }
    )
  },
  submit: function () {
    var url = app.url + 'weiapp/Api/register?PHPSESSID=' + wx.getStorageSync('PHPSESSID')
    var that = this
    var mobile = this.data.mobile
    var code = this.data.code

    $ajax._get(
      url,
      {
        mobile: mobile,
        code: code
      },
      function (res) {
        var status = res.status
        if (status == 1) {

          wx.showToast({
            title: '登录成功',
            icon: 'success',
            duration: 2000
          })
          wx.navigateTo({
            url: '/pages/index/index'
          })

        } else {
          that.setData({
            errorCode: true,
            errorMsg: res.msg
          })
        }
      }
    )
  }
})