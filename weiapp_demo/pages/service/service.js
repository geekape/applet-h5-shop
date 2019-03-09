//aboutme.js
//获取应用实例
var app = getApp()
Page({
  data: {
    img: '../../images/logo.png',
    title: "圆梦云：WeiPHP的技术开发公司",
    intro: "从2014年发布到现在，WeiPHP的下载量已过百万。我们把核心功能免费开源，写二次开发教程，出各种视频培训教程，努力出现在大众面前。目的只有两个：一是安利下我们的实力，不只是靠说，还有作品证明；二是希望能带动增值服务，希望做到当用户新需求要定制时，能想到我们。我们赖以生存的增值服务只有定制开发，卖插件，卖培训咨询服务。之所以叫圆梦云，是希望我们的公司，我们的产品，我们的服务能成为帮助大家（包括我们自己）圆梦的云舞台。",

    mobile: "0755-23729769",
    email: "weiphp@weiphp.cn",
    weixin: "18123611282",
  },
  callme: function () {
    wx.makePhoneCall({
      phoneNumber: this.data.mobile
    })
  },
  copy: function () {
    var data = this.data.weixin + '  ' + this.data.mobile + '  ' + this.data.email;
    wx.setClipboardData({
      data: data,
      success(res) {
        app.success('复制成功')
      }
    })
  }
})