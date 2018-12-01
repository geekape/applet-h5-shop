var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    title: 'WeiPHP',
    logo: '../../images/icon_weiphp.png',
  },
  saveUserInfo: function (baseUrl, res) {
    wx.setStorageSync("userInfo", res.userInfo)
    wx.request({
      url: baseUrl + 'weiapp/api/saveUserInfo',
      data: {
        iv: res.iv,
        encryptedData: res.encryptedData,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      },
      success: function (res) {
        console.log('success')
        wx.switchTab({ url: '/pages/index/index' })
      },
      complete:function(res){
        console.log('complete')
        console.log(res)
      }
    })
  },
  login: function (e) {
    console.log(e.detail)
    this.saveUserInfo(app.url, e.detail)
  }
})