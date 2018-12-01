require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([16],{

/***/ 311:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(312);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 312:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(314);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_fce77668_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(315);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(313)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-fce77668"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_fce77668_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\my_address\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-fce77668", Component.options)
  } else {
    hotAPI.reload("data-v-fce77668", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 313:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 314:
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
      address: []
    };
  },

  computed: {},

  components: {},

  methods: {
    toggleCheckbox: function toggleCheckbox(item) {
      this.address.forEach(function (ele, index) {
        ele.isDefault = false;
      });
      item.isDefault = true;
    },


    // 删除地址
    delAddress: function delAddress() {
      wx.showModal({
        title: '提示',
        content: '确定删除当前地址',
        success: function success(res) {
          if (res.confirm) {
            wx.showToast({
              title: '删除成功',
              icon: 'none',
              duration: 2000
            });
          } else if (res.cancel) {}
        }
      });
    }
  },

  onLoad: function onLoad() {
    var _this = this;
    Object(__WEBPACK_IMPORTED_MODULE_0__utils__["b" /* get */])("/shop/api/add_address/PHPSESSID" + wx.getStorageSync('PHPSESSID')).then(function (res) {
      console.log(res);
      _this.address = res.info;
    });
  }
});

/***/ }),

/***/ 315:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "my-address"
  }, [(_vm.address.truename) ? _c('div', {
    staticClass: "address-item"
  }, [_c('div', {
    staticClass: "address-item__bd"
  }, [_c('div', {
    staticClass: "g-flex"
  }, [_c('p', {
    staticClass: "address-item__name g-flex__item"
  }, [_vm._v(_vm._s(_vm.address.truename))]), _vm._v(" "), _c('p', {
    staticClass: "address-item__moblie"
  }, [_vm._v(_vm._s(_vm.address.mobile))])], 1), _vm._v(" "), _c('p', {
    staticClass: "address-item__info"
  }, [_vm._v(_vm._s(_vm.address.address + _vm.address.address_detail))])], 1), _vm._v(" "), _c('div', {
    staticClass: "address-item__ft g-flex"
  }, [(_vm.address.is_use == 1) ? _c('van-checkbox', {
    staticClass: "address-item__checkbox",
    attrs: {
      "value": _vm.address.is_use == 1,
      "eventid": '0',
      "mpcomid": '1'
    },
    on: {
      "change": function($event) {
        _vm.toggleCheckbox(_vm.item)
      }
    }
  }, [_vm._v("默认地址")]) : _c('van-checkbox', {
    staticClass: "address-item__checkbox active",
    attrs: {
      "value": _vm.address.is_use == 0,
      "mpcomid": '0'
    }
  }, [_vm._v("默认地址")])], 1)]) : _c('a', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "open-type": "redirect",
      "href": "../add_address/main?id=0"
    }
  }, [_vm._v("新增地址")]), _vm._v(" "), (_vm.address.truename) ? _c('a', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "open-type": "redirect",
      "href": "../add_address/main?id=1"
    }
  }, [_vm._v("修改地址")]) : _vm._e()])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-fce77668", esExports)
  }
}

/***/ })

},[311]);
//# sourceMappingURL=main.js.map