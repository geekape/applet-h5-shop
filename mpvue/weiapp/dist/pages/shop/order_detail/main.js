require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([13],{

/***/ 326:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(327);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 327:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(329);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_48ecece6_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(330);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(328)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-48ecece6"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_48ecece6_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\order_detail\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-48ecece6", Component.options)
  } else {
    hotAPI.reload("data-v-48ecece6", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 328:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 329:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__ = __webpack_require__(23);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__static_vant_dialog_dialog__ = __webpack_require__(59);

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
      orderId: 0,
      steps: [],
      info: [],
      goods: [],
      address: [],
      shops: []
    };
  },

  computed: {
    // logs () {
    //    let log = this.steps
    //    let arr = []
    //    log.forEach((item,idx) => {
    //      let obj = {
    //        text: item.remark,
    //        desc: timeChange(item.cTime)
    //      }
    //      arr.push(obj)
    //    })
    //    return arr
    //  }
  },

  components: {},

  methods: {
    goPay: function goPay(id, price) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["c" /* goPay */])(id);
    },
    goReceiving: function goReceiving(id) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["d" /* goReceiving */])(id);
    }
  },

  onLoad: function onLoad() {
    __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default()(this, this.$options.data());

    this.orderId = this.$root.$mp.query.order_id || 0;
    var _this = this;
    Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])('shop/api/order_detail', {
      id: _this.orderId,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then(function (res) {
      _this.steps = res.log;
      _this.info = res.info;
      _this.address = res.addressInfo;
      _this.goods = res.info.goods;
      _this.shops = res.store_info;

      // 处理订单信息时间戳
      _this.info.cTime = Object(__WEBPACK_IMPORTED_MODULE_1__utils__["h" /* timeChange */])(_this.info.cTime);
      _this.info.pay_time = Object(__WEBPACK_IMPORTED_MODULE_1__utils__["h" /* timeChange */])(_this.info.pay_time);
      _this.info.send_time = Object(__WEBPACK_IMPORTED_MODULE_1__utils__["h" /* timeChange */])(_this.info.send_time);
      if (_this.steps) {
        _this.steps.cTime = Object(__WEBPACK_IMPORTED_MODULE_1__utils__["h" /* timeChange */])(_this.steps.cTime);
      }
    });
  }
});

/***/ }),

/***/ 330:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "order-detail",
    class: {
      'pb80': _vm.info.status_code == 3 || _vm.info.pay_status === 0
    }
  }, [(_vm.steps && _vm.info.send_type == 1) ? _c('a', {
    staticClass: "m-list link log",
    attrs: {
      "href": '../logistics/main?order_id=' + _vm.orderId
    }
  }, [_c('div', {
    staticClass: "m-list__l"
  }, [_c('p', [_vm._v(_vm._s(_vm.info.status_code_name))]), _vm._v(" "), _c('p', [_vm._v("物流信息：" + _vm._s(_vm.steps.remark))]), _vm._v(" "), _c('small', {
    staticClass: "log-time"
  }, [_vm._v(_vm._s(_vm.steps.cTime))])], 1), _vm._v(" "), _c('div', {
    staticClass: "m-list__c"
  }), _vm._v(" "), _c('i', {
    staticClass: "iconfont icon-fanhui right"
  })], 1) : _vm._e(), _vm._v(" "), (_vm.info.send_type == 1) ? _c('div', {
    staticClass: "m-list order-line"
  }, [_c('div', {
    staticClass: "m-list__l"
  }, [_vm._v(_vm._s(_vm.address.truename))]), _vm._v(" "), _c('div', {
    staticClass: "m-list__c"
  }, [_c('p', {}, [_vm._v(_vm._s(_vm.address.mobile))]), _vm._v(" "), _c('p', {
    staticClass: "f-font-sm"
  }, [_vm._v(_vm._s(_vm.address.address + _vm.address.address_detail))])], 1), _vm._v(" "), _c('div', {
    staticClass: "m-list__r"
  })]) : _c('div', {
    staticClass: "shop-list__item"
  }, [_c('div', {
    staticClass: "shop-list__bd g-flex"
  }, [_c('img', {
    staticClass: "u-goods__img",
    attrs: {
      "src": _vm.shops.img_url
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "g-flex__flex"
  }, [_c('p', {
    staticClass: "shop-list__name"
  }, [_vm._v(_vm._s(_vm.shops.name))]), _vm._v(" "), (_vm.shops.address) ? _c('p', {
    staticClass: "shop-list__address"
  }, [_c('span', {
    staticClass: "iconfont icon-dingwei"
  }), _vm._v(_vm._s(_vm.shops.address))]) : _vm._e(), _vm._v(" "), (_vm.shops.shop_code) ? _c('p', {
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
  }), _vm._v("导航")])], 1)]), _vm._v(" "), _c('div', {
    staticClass: "m-list__group"
  }, [_c('div', {
    staticClass: "m-list"
  }, [_c('div', {
    staticClass: "m-list__l"
  }, [_vm._v("买家留言：" + _vm._s(_vm.info.remark ? _vm.info.remark : '无'))])]), _vm._v(" "), _c('div', {
    staticClass: "m-list"
  }, [_c('div', {
    staticClass: "m-list__l"
  }, [_vm._v("配送方式：" + _vm._s(_vm.info.send_type == 2 ? '自提' : '邮寄'))])])]), _vm._v(" "), _c('div', {
    staticClass: "order-detail__item"
  }, _vm._l((_vm.goods), function(item, index) {
    return _c('a', {
      key: index,
      staticClass: "goods-line",
      attrs: {
        "href": '../goods_detail/main?id=' + item.id
      }
    }, [_c('img', {
      staticClass: "u-goods__img",
      attrs: {
        "src": item.cover
      }
    }), _vm._v(" "), _c('div', {
      staticClass: "goods-line__right"
    }, [_c('p', {
      staticClass: "u-goods__tt overflow-dot"
    }, [_vm._v(_vm._s(item.title))]), _vm._v(" "), _c('div', {
      staticClass: "goods-line__ft"
    }, [_c('div', {
      staticClass: "goods-line__price"
    }, [_c('span', [_vm._v("¥" + _vm._s(item.sale_price))])]), _vm._v(" "), _c('p', {
      staticClass: "f-font-sm"
    }, [_vm._v("x" + _vm._s(item.num))])], 1)], 1)])
  })), _vm._v(" "), (_vm.info.pay_status != 0) ? _c('div', {
    staticClass: "m-card order-detail__item"
  }, [_vm._m(0), _vm._v(" "), _c('div', {
    staticClass: "m-card__list"
  }, [_c('div', {
    staticClass: "m-card__item"
  }, [_vm._v("\n          订单编号：" + _vm._s(_vm.info.order_number) + "\n        ")]), _vm._v(" "), _c('div', {
    staticClass: "m-card__item"
  }, [_vm._v("\n          支付方式：" + _vm._s(_vm.info.common) + "\n        ")]), _vm._v(" "), _c('div', {
    staticClass: "m-card__item"
  }, [_vm._v("\n          下单时间：" + _vm._s(_vm.info.pay_time) + "\n        ")]), _vm._v(" "), _c('div', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.info.refund > 0),
      expression: "info.refund>0"
    }],
    staticClass: "m-card__item"
  }, [_vm._v("\n          退款状态：" + _vm._s(_vm.info.refund_title) + "\n        ")])]), _vm._v(" "), (_vm.info.status_code == 3 || _vm.info.refund == 0) ? _c('a', {
    staticClass: "u-button u-button--border",
    attrs: {
      "href": '../refund/main?order_id=' + _vm.info.id
    }
  }, [_vm._v("申请退款")]) : _vm._e()]) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "u-fixed"
  }, [(_vm.info.status_code == 3) ? _c('button', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "eventid": '0'
    },
    on: {
      "click": function($event) {
        _vm.goReceiving(_vm.orderId)
      }
    }
  }, [_vm._v("确认收货")]) : _vm._e(), _vm._v(" "), (_vm.info.pay_status == 0) ? _c('button', {
    staticClass: "u-button u-button--primary u-button--big",
    attrs: {
      "eventid": '1'
    },
    on: {
      "click": function($event) {
        _vm.goPay(_vm.info.out_trade_no, _vm.info.total_price)
      }
    }
  }, [_vm._v("立即付款")]) : _vm._e()], 1), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '0'
    }
  }), _vm._v(" "), _c('van-dialog', {
    attrs: {
      "id": "van-dialog",
      "mpcomid": '1'
    }
  })], 1)
}
var staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "m-card__tt"
  }, [_c('div', {
    staticClass: "line"
  }), _vm._v("订单信息")])
}]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-48ecece6", esExports)
  }
}

/***/ })

},[326]);
//# sourceMappingURL=main.js.map