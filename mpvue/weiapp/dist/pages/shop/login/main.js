require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([5],{

/***/ 294:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(295);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 295:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(298);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_9b3e6298_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(299);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(296)
  __webpack_require__(297)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-9b3e6298"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_9b3e6298_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\login\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-9b3e6298", Component.options)
  } else {
    hotAPI.reload("data-v-9b3e6298", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 296:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 297:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 298:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__utils__ = __webpack_require__(2);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["a"] = ({
  components: {},

  data: function data() {
    return {
      logs: []
    };
  },

  methods: {
    doLogin: function doLogin(e) {
      var _this = this;
      //登录
      wx.getUserInfo({
        success: function success(res) {
          wx.setStorageSync('userInfo', e.mp.detail.userInfo);
          _this.saveUserInfo(res);
          wx.switchTab({
            url: '/pages/shop/index/main'
          });
        },
        fail: function fail(res) {
          console.log('失败了');
        }
      });
    },
    saveUserInfo: function saveUserInfo(opt) {
      console.log(opt);
      Object(__WEBPACK_IMPORTED_MODULE_0__utils__["g" /* post */])('weiapp/api/saveUserInfo', {
        iv: opt.iv,
        encryptedData: opt.encryptedData,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then(function (res) {
        console.log('success');
        wx.switchTab({ url: '/pages/shop/index/index' });
      });
    }
  },

  created: function created() {},
  onLoad: function onLoad() {}
});

/***/ }),

/***/ 299:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "login"
  }, [_c('img', {
    staticClass: "login-logo",
    attrs: {
      "src": "../../../../static/img/head.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "login-name"
  }, [_vm._v("圆梦云商城")]), _vm._v(" "), _c('p', {
    staticClass: "login-hint__tt"
  }, [_vm._v("圆梦云商城 - 品质生活，极速到家")]), _vm._v(" "), _c('button', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "open-type": "getUserInfo",
      "eventid": '0'
    },
    on: {
      "getuserinfo": _vm.doLogin
    }
  }, [_vm._v("微信登录")])], 1)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-9b3e6298", esExports)
  }
}

/***/ })

},[294]);
//# sourceMappingURL=main.js.map