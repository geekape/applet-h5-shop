require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([11],{

/***/ 414:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(415);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 415:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(417);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_774abbdc_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(418);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(416)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-774abbdc"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_774abbdc_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\shop_list\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-774abbdc", Component.options)
  } else {
    hotAPI.reload("data-v-774abbdc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 416:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 417:
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
//


/* harmony default export */ __webpack_exports__["a"] = ({
  data: function data() {
    return {
      shops: []
    };
  },

  computed: {},

  methods: {},
  onLoad: function onLoad() {
    var is_choose = 1;
    var ids = -1;
    var _this = this;
    Object(__WEBPACK_IMPORTED_MODULE_0__utils__["g" /* post */])("shop/api/shop_list", {
      is_choose: is_choose,
      ids: ids,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then(function (res) {
      console.log(res);
      _this.shops = res.store_lists;
    });
  }
});

/***/ }),

/***/ 418:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "shop-list"
  }, _vm._l((_vm.shops), function(item, index) {
    return _c('div', {
      key: index,
      staticClass: "shop-list__item"
    }, [_c('div', {
      staticClass: "shop-list__bd g-flex"
    }, [_c('image', {
      staticClass: "u-goods__img",
      attrs: {
        "src": item.img_url
      }
    }), _vm._v(" "), _c('div', {
      staticClass: "g-flex__flex"
    }, [_c('p', {
      staticClass: "shop-list__name"
    }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), (item.address) ? _c('p', {
      staticClass: "shop-list__address"
    }, [_c('span', {
      staticClass: "iconfont icon-dingwei"
    }), _vm._v(_vm._s(item.address))]) : _vm._e(), _vm._v(" "), (item.shop_code) ? _c('p', {
      staticClass: "shop-list__dist"
    }, [_c('span', {
      staticClass: "iconfont icon-daohang"
    })]) : _vm._e()], 1)]), _vm._v(" "), _c('div', {
      staticClass: "shop-list__ft g-flex"
    }, [_c('p', {
      staticClass: "g-flex__item"
    }, [_c('span', {
      staticClass: "iconfont icon-phone"
    }), _vm._v("电话")]), _vm._v(" "), _c('p', {
      staticClass: "g-flex__item"
    }, [_c('span', {
      staticClass: "iconfont icon-daohang"
    }), _vm._v("导航")])], 1)])
  }))
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-774abbdc", esExports)
  }
}

/***/ })

},[414]);
//# sourceMappingURL=main.js.map