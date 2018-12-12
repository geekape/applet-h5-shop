require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([8],{

/***/ 267:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(268);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 268:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(271);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_31071161_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(272);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(269)
  __webpack_require__(270)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-31071161"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_31071161_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\add_address\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-31071161", Component.options)
  } else {
    hotAPI.reload("data-v-31071161", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 269:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 270:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 271:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof__ = __webpack_require__(43);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__ = __webpack_require__(13);

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
      name: '',
      moblie: '',
      addressArea: "选择省/市/区",
      addressInfo: '',
      customItem: [],
      type: 0
    };
  },

  computed: {},

  components: {},

  methods: {
    bindName: function bindName(e) {
      console.log(e);
      this.name = e.mp.detail.value;
    },
    bindMoblie: function bindMoblie(e) {
      this.moblie = e.mp.detail.value;
    },
    bindAddressInfo: function bindAddressInfo(e) {
      this.addressInfo = e.mp.detail.value;
    },

    bindRegionChange: function bindRegionChange(e) {
      this.addressArea = e.mp.detail.value;
    },
    saveAddress: function saveAddress() {
      var _this2 = this;

      var _this = this;
      if (this.name == '' || this.moblie == '' || this.addressArea == '' || this.addressInfo == '') {
        Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('还有表单没有填');
        return falseaddress_detail;
      }

      if (__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_typeof___default()(this.addressArea) == 'object') {
        this.addressArea = this.addressArea.join(',');
      }
      var obj = {
        PHPSESSID: wx.getStorageSync('PHPSESSID'),
        truename: _this.name,
        mobile: _this.moblie,
        address: _this.addressArea,
        address_detail: _this.addressInfo,
        is_use: 1,
        is_choose: 1
      };
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])("/shop/api/add_address", obj).then(function (res) {
        Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('保存地址成功');
        // 从订单页进来的
        if (_this2.type == 1) {
          wx.navigateBack({
            url: '../confirm_order/main'
          });
        } else {
          wx.switchTab({
            url: '../center/main'
          });
        }
        wx.setStorageSync('address', obj);
      });
    }
  },

  onLoad: function onLoad() {
    this.type = this.$root.$mp.query.type || 0;
    var _this = this;
    // 修改地址
    Object(__WEBPACK_IMPORTED_MODULE_1__utils__["b" /* get */])("/shop/api/add_address/PHPSESSID/" + wx.getStorageSync('PHPSESSID')).then(function (res) {
      console.log(res);
      _this.name = res.info.truename;
      _this.moblie = res.info.mobile;
      _this.addressArea ? _this.addressArea = res.info.address : _this.addressArea = "选择省/市/区";
      _this.addressInfo = res.info.address_detail;
    });
  }
});

/***/ }),

/***/ 272:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "add-address"
  }, [_c('from', {
    staticClass: "m-from",
    attrs: {
      "mpcomid": '0'
    }
  }, [_c('div', {
    staticClass: "m-from__input"
  }, [_c('label', {
    staticClass: "m-from__label",
    attrs: {
      "for": "name"
    }
  }, [_vm._v("姓名")]), _vm._v(" "), _c('input', {
    attrs: {
      "id": "name",
      "type": "text",
      "value": _vm.name,
      "placeholder": "收货人姓名",
      "placeholder-style": "color: #aaa",
      "eventid": '0'
    },
    on: {
      "input": _vm.bindName
    }
  })], 1), _vm._v(" "), _c('div', {
    staticClass: "m-from__input"
  }, [_c('label', {
    staticClass: "m-from__label",
    attrs: {
      "for": "moblie"
    }
  }, [_vm._v("电话")]), _vm._v(" "), _c('input', {
    attrs: {
      "id": "moblie",
      "type": "number",
      "value": _vm.moblie,
      "placeholder": "手机号码",
      "placeholder-style": "color: #aaa",
      "eventid": '1'
    },
    on: {
      "input": _vm.bindMoblie
    }
  })], 1), _vm._v(" "), _c('div', {
    staticClass: "m-from__picker"
  }, [_c('label', {
    staticClass: "m-from__label",
    attrs: {
      "for": "address"
    }
  }, [_vm._v("地区")]), _vm._v(" "), _c('picker', {
    class: {
      active: _vm.addressArea != '选择省/市/区'
    },
    attrs: {
      "id": "address",
      "mode": "region",
      "value": _vm.addressArea,
      "custom-item": _vm.customItem,
      "eventid": '2'
    },
    on: {
      "change": _vm.bindRegionChange
    }
  }, [_vm._v("\n         " + _vm._s(_vm.addressArea) + "\n        ")])], 1), _vm._v(" "), _c('div', {
    staticClass: "m-from__input"
  }, [_c('label', {
    staticClass: "m-from__label",
    attrs: {
      "for": "addressInfo"
    }
  }, [_vm._v("详细地址")]), _vm._v(" "), _c('input', {
    attrs: {
      "id": "addressInfo",
      "type": "text",
      "placeholder": "街道、小区门牌等详细地址",
      "placeholder-style": "color: #aaa",
      "value": _vm.addressInfo,
      "eventid": '3'
    },
    on: {
      "input": _vm.bindAddressInfo
    }
  })], 1)]), _vm._v(" "), _c('button', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "eventid": '4'
    },
    on: {
      "click": _vm.saveAddress
    }
  }, [_vm._v("保存")]), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '1'
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
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-31071161", esExports)
  }
}

/***/ })

},[267]);
//# sourceMappingURL=main.js.map