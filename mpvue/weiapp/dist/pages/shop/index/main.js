require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([1],{

/***/ 253:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(254);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 254:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(257);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_acdfce46_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(268);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(255)
  __webpack_require__(256)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-acdfce46"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_acdfce46_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\index\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-acdfce46", Component.options)
  } else {
    hotAPI.reload("data-v-acdfce46", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 255:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 256:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 257:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify__ = __webpack_require__(58);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__components_shop_search__ = __webpack_require__(83);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_slide__ = __webpack_require__(261);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__components_shop_goodsList__ = __webpack_require__(84);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__utils__ = __webpack_require__(2);

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
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["a"] = ({
  data: function data() {
    return {
      motto: 'Hello World',
      slides: [],
      categorys: [],
      goods: []
    };
  },

  components: {
    search: __WEBPACK_IMPORTED_MODULE_1__components_shop_search__["a" /* default */],
    slide: __WEBPACK_IMPORTED_MODULE_2__components_slide__["a" /* default */],
    goodsList: __WEBPACK_IMPORTED_MODULE_3__components_shop_goodsList__["a" /* default */]
  },
  computed: {
    // 处理分类
    categoryList: function categoryList() {
      var arr = JSON.parse(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify___default()(this.categorys));
      var len = 0;
      var arr2 = [];
      arr.length % 8 == 0 ? len = arr.length / 8 : len = parseInt(arr.length / 8) + 1;
      for (var i = 0; i < len; i++) {
        arr2.push(arr.slice(i * 8, (i + 1) * 8));
      }
      return arr2;
    }
  },
  methods: {
    goToUrl: function goToUrl(pid, id) {
      this.GLOBAL.app.pid = pid;
      this.GLOBAL.app.id = id;
      wx.switchTab({
        url: '/pages/shop/lists/main'
      });
    }
  },
  onLoad: function onLoad() {
    var _this = this;

    console.log('首页加载了');
    Object(__WEBPACK_IMPORTED_MODULE_4__utils__["b" /* get */])('shop/api/index').then(function (res) {
      console.log(res);
      _this.slides = res.slideshow;
      _this.categorys = res.category;
      _this.goods = res.goods;
    });
  },
  onShow: function onShow() {
    var _this2 = this;

    // 设置购物车数量
    Object(__WEBPACK_IMPORTED_MODULE_4__utils__["b" /* get */])("shop/api/cart/PHPSESSID/" + wx.getStorageSync("PHPSESSID")).then(function (res) {
      var num = res.lists.length;
      _this2.$store.commit("getCartShopNum", {
        num: num
      });
    }).catch(function (err) {
      console.log("失败：" + err);
    });
  },
  onShareAppMessage: function onShareAppMessage() {
    // 分享
    return {
      title: '易商城首页',
      path: '/pages/index/main'
    };
  }
});

/***/ }),

/***/ 261:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_slide_vue__ = __webpack_require__(263);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_1357d9ae_hasScoped_false_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_slide_vue__ = __webpack_require__(264);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(262)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_slide_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_1357d9ae_hasScoped_false_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_slide_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\components\\slide.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] slide.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1357d9ae", Component.options)
  } else {
    hotAPI.reload("data-v-1357d9ae", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 262:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 263:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
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
  props: {
    autoplay: '',
    interval: {
      type: Number,
      default: 3000
    },
    slides: Array,
    isDot: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    goUrl: function goUrl(url) {
      if (!url) {
        return false;
      }
      wx.navigateTo({
        url: '/pages/shop/web_view/main?url=' + url
      });
    }
  }
});

/***/ }),

/***/ 264:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "slide"
  }, [_c('swiper', {
    staticClass: "swiper",
    attrs: {
      "indicator-dots": _vm.isDot,
      "autoplay": _vm.autoplay,
      "interval": _vm.interval,
      "indicator-active-color": "#ff0204",
      "indicator-color": "rgba(255,255,255,.3)"
    }
  }, _vm._l((_vm.slides), function(item, index) {
    return _c('swiper-item', {
      key: index,
      attrs: {
        "mpcomid": '0-' + index
      }
    }, [_c('div', {
      staticClass: "slide-url",
      attrs: {
        "eventid": '0-' + index
      },
      on: {
        "click": function($event) {
          _vm.goUrl(item.url)
        }
      }
    }, [_c('image', {
      staticClass: "slide-image",
      attrs: {
        "src": item.img,
        "mode": "aspectFill"
      }
    })])])
  }))], 1)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-1357d9ae", esExports)
  }
}

/***/ }),

/***/ 268:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "index"
  }, [_c('search', {
    attrs: {
      "mpcomid": '0'
    }
  }), _vm._v(" "), (_vm.slides) ? _c('slide', {
    attrs: {
      "slides": _vm.slides,
      "autoplay": true,
      "mpcomid": '1'
    }
  }) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "categorys"
  }, [_c('swiper', {
    attrs: {
      "indicator-dots": "true",
      "indicator-color": "#eee",
      "indicator-active-color": "#ff0204"
    }
  }, _vm._l((_vm.categoryList), function(item, index) {
    return _c('swiper-item', {
      key: index,
      staticClass: "category-block",
      attrs: {
        "mpcomid": '2-' + index
      }
    }, _vm._l((item), function(category, idx) {
      return _c('div', {
        key: category.id,
        staticClass: "category-block__item",
        attrs: {
          "eventid": '0-' + index + '-' + idx
        },
        on: {
          "click": function($event) {
            _vm.goToUrl(category.pid, category.id)
          }
        }
      }, [_c('img', {
        staticClass: "category-block__img",
        attrs: {
          "src": category.icon,
          "mode": "aspectFill"
        }
      }), _vm._v(" "), _c('p', {
        staticClass: "category-block__txt overflow-dot_row"
      }, [_vm._v(_vm._s(category.title))])], 1)
    }))
  }))], 1), _vm._v(" "), _c('p', {
    staticClass: "page-title"
  }, [_vm._v("猜你喜欢")]), _vm._v(" "), (_vm.goods) ? _c('goodsList', {
    attrs: {
      "goodsData": _vm.goods,
      "mpcomid": '3'
    }
  }) : _vm._e()], 1)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-acdfce46", esExports)
  }
}

/***/ })

},[253]);
//# sourceMappingURL=main.js.map