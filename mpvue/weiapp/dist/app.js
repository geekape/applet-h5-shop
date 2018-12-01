require("common/manifest.js");
require("common/vendor.js");
global.webpackJsonp([2],{

/***/ 110:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_vuex__ = __webpack_require__(71);


__WEBPACK_IMPORTED_MODULE_0_vue___default.a.use(__WEBPACK_IMPORTED_MODULE_1_vuex__["a" /* default */]);

/* harmony default export */ __webpack_exports__["a"] = (new __WEBPACK_IMPORTED_MODULE_1_vuex__["a" /* default */].Store({
    state: {
        cartShopNum: 0,
        allOrder: [],
        waitPayNum: 0
    },
    mutations: {
        getCartShopNum: function getCartShopNum(state, opt) {
            state.cartShopNum = opt.num;

            if (state.cartShopNum > 0) {
                wx.setTabBarBadge({
                    index: 2,
                    text: state.cartShopNum + ''
                });
            } else {
                wx.hideTabBarRedDot({ index: 2 });
            }
        },

        // 保存订单
        saveOrder: function saveOrder(state, opt) {
            state.allOrder = opt.order;
        }
    },
    getters: {
        // 待支付
        waitPay: function waitPay(state) {
            var arr = [];
            state.allOrder.forEach(function (item) {
                if (item.status_code == 0) {
                    arr.push(item);
                }
            });
            return arr;
        },

        // 待收货
        waitCollect: function waitCollect(state) {
            var arr = [];
            state.allOrder.forEach(function (item) {
                if (item.status_code > 0 && item.status_code < 4 && item.refund == 0) {
                    arr.push(item);
                }
            });
            return arr;
        },

        // 待评价
        waitComment: function waitComment(state) {
            var arr = [];
            state.allOrder.forEach(function (item) {
                if (item.status_code > 3 && item.status_code != 7) {
                    arr.push(item);
                }
            });
            return arr;
        }
    },

    actions: {}
}));

/***/ }),

/***/ 111:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

var app = {
  id: 0,
  pid: 0,
  searchKey: '', //搜索值
  listsType: 0, //跳转到列表页类型
  payMoney: 0 // 支付金额
};

/* harmony default export */ __webpack_exports__["a"] = ({
  app: app
});

/***/ }),

/***/ 112:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 86:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__App__ = __webpack_require__(88);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__store__ = __webpack_require__(110);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__utils_global__ = __webpack_require__(111);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__static_styles_weui_wxss__ = __webpack_require__(112);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__static_styles_weui_wxss___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4__static_styles_weui_wxss__);




// 全局变量




__WEBPACK_IMPORTED_MODULE_0_vue___default.a.config.productionTip = false;
__WEBPACK_IMPORTED_MODULE_1__App__["a" /* default */].mpType = 'app';

__WEBPACK_IMPORTED_MODULE_0_vue___default.a.prototype.$store = __WEBPACK_IMPORTED_MODULE_2__store__["a" /* default */];

// 使用flyio http请求库
var Fly = __webpack_require__(72);
var fly = new Fly();
__WEBPACK_IMPORTED_MODULE_0_vue___default.a.prototype.$http = fly;

__WEBPACK_IMPORTED_MODULE_0_vue___default.a.prototype.GLOBAL = __WEBPACK_IMPORTED_MODULE_3__utils_global__["a" /* default */];

var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__App__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 88:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_App_vue__ = __webpack_require__(90);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(89)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */
var __vue_template__ = null
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_App_vue__["a" /* default */],
  __vue_template__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\App.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-22cbbdbb", Component.options)
  } else {
    hotAPI.reload("data-v-22cbbdbb", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 89:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 90:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

var common = __webpack_require__(91);
/* harmony default export */ __webpack_exports__["a"] = ({
  data: function data() {
    return {
      url: 'https://leyao.tv/yi/public/index.php?pbid=72&s=/',
      PHPSESSID: '',
      common: common
    };
  },
  onLaunch: function onLaunch(options) {

    var from_uid = options.query.from_uid == undefined ? '0' : options.query.from_uid;
    if (from_uid != 0) {
      wx.setStorageSync("from_uid", from_uid);
    }

    common.initApp(this.url + 'weiapp/Api/', true);
  }
});

/***/ }),

/***/ 91:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initApp", function() { return initApp; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "shareUrl", function() { return shareUrl; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof__ = __webpack_require__(37);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof__);

var app = getApp();

function initApp(baseUrl, needUserInfo) {
  var openid = wx.getStorageSync('openid');
  var PHPSESSID = wx.getStorageSync('PHPSESSID');

  if (openid == '' || PHPSESSID == '') {
    setLogin(baseUrl, needUserInfo);
  } else {
    //小程序登录状态检查
    wx.checkSession({
      success: function success() {
        console.log('小程序登录态正常');
        //小程序登录态正常，接着检查后端PHP用户登录态
        var sessid = wx.getStorageSync('PHPSESSID');
        if (sessid == '') {
          setLogin(baseUrl, needUserInfo);
        } else {
          //判断是否已登录
          console.log('需要从后端判断用户有没有登录过');
          wx.request({
            url: baseUrl + 'checkLogin', //需要从后端判断用户有没有登录过
            data: {
              PHPSESSID: wx.getStorageSync('PHPSESSID') //一定要带上PHPSESSID，否则后端系统不知道哪个用户
            },
            success: function success(res) {
              console.log('checkLogin的结果：' + res.data.status);
              if (res.data.status == 0) {
                setLogin(baseUrl, needUserInfo);
              }
            }
          });
        }
      },
      fail: function fail() {
        console.log('登录态过期');
        //登录态过期
        setLogin(baseUrl, needUserInfo); //重新登录
      }
    });
  }
}

function setLogin(baseUrl, needUserInfo) {
  console.log('setLogin:' + baseUrl);
  setTimeout(function () {
    wx.login({
      success: function success(res) {
        console.log('setLogin res');
        console.log(res);
        if (res.code) {
          //使用小程序登录接口完成后端用户登录
          wx.request({
            url: baseUrl + 'sendSessionCode',
            data: {
              code: res.code,
              from_uid: wx.getStorageSync('from_uid')
            },
            success: function success(res) {
              console.log(res);
              if (typeof res.data == "string") {
                res = JSON.parse(res.data);
              } else {
                res = res.data;
              }
              console.log('获取到了phpsessid:' + res.data.PHPSESSID);
              console.log('openid:' + res.data.openid);
              //把sessid保存到缓存里
              wx.setStorageSync("PHPSESSID", res.data.PHPSESSID);
              wx.setStorageSync("openid", res.data.openid);
              wx.setStorageSync("uid", res.data.uid);

              //登录成功后判断用户是否已初始化化，如没则自动初始化化
              if (needUserInfo) {
                autoReg(baseUrl);
              }
            }
          });
        } else {
          console.log('获取用户登录态失败！' + res.errMsg);
        }
      }
    });
  }, 17);
}

function autoReg(baseUrl) {
  //判断是否完成自动注册
  var checkReg = true;
  try {
    var userInfo = wx.getStorageSync('userInfo'); //通过缓存判断就行，即使用户清缓存也无影响，顶多再保存一次而已
    if (!userInfo) {
      checkReg = false;
    }
  } catch (e) {
    checkReg = false;
  }
  console.log('checkReg:');
  console.log(checkReg);
  if (checkReg == true) {
    return true;
  }
  console.log('autoReg');
  wx.redirectTo({
    url: '/pages/shop/login/main'
  });
}

function shareUrl() {
  var pages = getCurrentPages(); //获取加载的页面
  var currentPage = pages[pages.length - 1]; //获取当前页面的对象
  var url = currentPage.route; //当前页面url
  var options = currentPage.options; //如果要获取url中所带的参数可以查看options

  return url + '?from_uid=' + wx.getStorageSync('uid') + urlEncode(options);
}
/** 
 * param 将要转为URL参数字符串的对象 
 * key URL参数字符串的前缀 
 * encode true/false 是否进行URL编码,默认为true 
 *  
 * return URL参数字符串 
 */
var urlEncode = function urlEncode(param, key, encode) {
  if (param == null) return '';
  var paramStr = '';
  var t = typeof param === 'undefined' ? 'undefined' : __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof___default()(param);
  if (t == 'string' || t == 'number' || t == 'boolean') {
    paramStr += '&' + key + '=' + (encode == null || encode ? encodeURIComponent(param) : param);
  } else {
    for (var i in param) {
      var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
      paramStr += urlEncode(param[i], k, encode);
    }
  }
  return paramStr;
};



/***/ })

},[86]);
//# sourceMappingURL=app.js.map