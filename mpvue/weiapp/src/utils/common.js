var app = getApp()

function initApp(baseUrl, needUserInfo) {
  var openid = wx.getStorageSync('openid')
  var PHPSESSID = wx.getStorageSync('PHPSESSID')

  if (openid == '' || PHPSESSID == '') {
    setLogin(baseUrl, needUserInfo)
  } else {
    //小程序登录状态检查
    wx.checkSession({
      success: function () {
        console.log('小程序登录态正常')
        //小程序登录态正常，接着检查后端PHP用户登录态
        var sessid = wx.getStorageSync('PHPSESSID')
        if (sessid == '') {
          setLogin(baseUrl, needUserInfo);
        } else {
          //判断是否已登录
          console.log('需要从后端判断用户有没有登录过')
          wx.request({
            url: baseUrl + 'checkLogin',  //需要从后端判断用户有没有登录过
            data: {
              PHPSESSID: wx.getStorageSync('PHPSESSID')  //一定要带上PHPSESSID，否则后端系统不知道哪个用户
            },
            success: function (res) {
              console.log('checkLogin的结果：' + res.data.status)
              if (res.data.status == 0) {
                setLogin(baseUrl, needUserInfo);
              }
            }
          })
        }
      },
      fail: function () {
        console.log('登录态过期')
        //登录态过期
        setLogin(baseUrl, needUserInfo) //重新登录
      }
    })
  }

}

function setLogin(baseUrl, needUserInfo) {
  console.log('setLogin:' + baseUrl)
  setTimeout(function () {
    wx.login({
      success: function (res) {
        console.log('setLogin res')
        console.log(res)
        if (res.code) { //使用小程序登录接口完成后端用户登录
          wx.request({
            url: baseUrl + 'sendSessionCode',
            data: {
              code: res.code,
              from_uid: wx.getStorageSync('from_uid')
            },
            success: function (res) {
              console.log(res)
              if (typeof res.data == "string") {
                res = JSON.parse(res.data)
              } else {
                res = res.data
              }
              console.log('获取到了phpsessid:' + res.data.PHPSESSID)
              console.log('openid:' + res.data.openid)
              //把sessid保存到缓存里
              wx.setStorageSync("PHPSESSID", res.data.PHPSESSID)
              wx.setStorageSync("openid", res.data.openid)
              wx.setStorageSync("uid", res.data.uid)

              //登录成功后判断用户是否已初始化化，如没则自动初始化化
              if (needUserInfo) {
                autoReg(baseUrl)
              }
            }
          })
        } else {
          console.log('获取用户登录态失败！' + res.errMsg)
        }
      }
    });
  }, 17)
}

function autoReg(baseUrl) {
  //判断是否完成自动注册
  var checkReg = true
  try {
    var userInfo = wx.getStorageSync('userInfo') //通过缓存判断就行，即使用户清缓存也无影响，顶多再保存一次而已
    if (!userInfo) {
      checkReg = false
    }
  } catch (e) {
    checkReg = false
  }
  console.log('checkReg:')
  console.log(checkReg)
  if (checkReg == true) {
    return true
  }
  console.log('autoReg')
  wx.redirectTo({
    url: '/pages/shop/login/index'
  })


}

function shareUrl() {
  var pages = getCurrentPages()    //获取加载的页面
  var currentPage = pages[pages.length - 1]    //获取当前页面的对象
  var url = currentPage.route    //当前页面url
  var options = currentPage.options    //如果要获取url中所带的参数可以查看options

  return url + '?from_uid=' + wx.getStorageSync('uid') + urlEncode(options);
}
/** 
 * param 将要转为URL参数字符串的对象 
 * key URL参数字符串的前缀 
 * encode true/false 是否进行URL编码,默认为true 
 *  
 * return URL参数字符串 
 */
var urlEncode = function (param, key, encode) {
  if (param == null) return '';
  var paramStr = '';
  var t = typeof (param);
  if (t == 'string' || t == 'number' || t == 'boolean') {
    paramStr += '&' + key + '=' + ((encode == null || encode) ? encodeURIComponent(param) : param);
  } else {
    for (var i in param) {
      var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
      paramStr += urlEncode(param[i], k, encode);
    }
  }
  return paramStr;
}

export {
  initApp,
  shareUrl
}