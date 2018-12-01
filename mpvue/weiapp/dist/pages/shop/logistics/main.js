require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([4],{

/***/ 300:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(301);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 301:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(304);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_4f129644_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(305);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(302)
  __webpack_require__(303)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4f129644"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_4f129644_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\logistics\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4f129644", Component.options)
  } else {
    hotAPI.reload("data-v-4f129644", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 302:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 303:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 304:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__ = __webpack_require__(23);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(2);

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
      active: 2,
      steps: [],
      info: []

    };
  },

  methods: {},
  computed: {
    logs: function logs() {
      var log = this.steps;
      var arr = [];
      log.forEach(function (item, idx) {
        var obj = {
          text: item.remark,
          desc: Object(__WEBPACK_IMPORTED_MODULE_1__utils__["h" /* timeChange */])(item.cTime)
        };
        arr.push(obj);
      });
      return arr;
    }
  },

  onLoad: function onLoad() {
    __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default()(this, this.$options.data());
    var orderId = this.$root.$mp.query.order_id;
    var _this = this;
    Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])('shop/api/logistics', {
      order_id: orderId,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then(function (res) {
      _this.steps = res.log;
      _this.info = res.info;
    });
  }
});

/***/ }),

/***/ 305:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "logistics"
  }, [_c('div', {
    staticClass: "logistics-head"
  }, [_c('p', [_vm._v("物流公司：" + _vm._s(_vm.info.send_code_name ? _vm.info.send_code_name : '空'))]), _vm._v(" "), _c('p', [_vm._v("物流单号：" + _vm._s(_vm.info.send_number ? _vm.info.send_number : '空'))])], 1), _vm._v(" "), _c('van-steps', {
    attrs: {
      "steps": _vm.logs,
      "active": _vm.active,
      "direction": "vertical",
      "active-color": "#ff0204",
      "mpcomid": '0'
    }
  })], 1)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-4f129644", esExports)
  }
}

/***/ })

},[300]);
//# sourceMappingURL=main.js.map