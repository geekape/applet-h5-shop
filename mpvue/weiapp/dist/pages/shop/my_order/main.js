require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([14],{

/***/ 321:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(322);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 322:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(324);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_3d125a34_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(325);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(323)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-3d125a34"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_3d125a34_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\my_order\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3d125a34", Component.options)
  } else {
    hotAPI.reload("data-v-3d125a34", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 323:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 324:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__static_vant_toast_toast__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__static_vant_dialog_dialog__ = __webpack_require__(59);
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
//
//
//
//




/* harmony default export */ __webpack_exports__["a"] = ({
  data: function data() {
    return {
      tabActive: 0 // 索引-页面处于第几个tab
    };
  },

  computed: {
    orders: function orders() {
      return this.$store.state.allOrder;
    },
    waitPay: function waitPay() {
      return this.$store.getters.waitPay;
    },
    waitCollect: function waitCollect() {
      return this.$store.getters.waitCollect;
    },
    waitComment: function waitComment() {
      return this.$store.getters.waitComment;
    }
  },

  methods: {
    // 评价/单多商品
    goComment: function goComment(opt) {
      var allGoods = opt.goods_datas;
      var allId = [];
      allGoods.forEach(function (item, index) {
        allId.push(item.id);
      });

      wx.navigateTo({
        url: "../comment/main?order_id=" + opt.id + "&goods_id=" + allId
      });
    },

    // 支付
    goPay: function goPay(id, price) {
      Object(__WEBPACK_IMPORTED_MODULE_0__utils__["c" /* goPay */])(id);
    },


    // 收货
    goReceiving: function goReceiving(id) {
      Object(__WEBPACK_IMPORTED_MODULE_0__utils__["d" /* goReceiving */])(id);
    },
    toggleTab: function toggleTab(e) {
      this.tabActive = e.mp.detail.index;
    }
  },

  onLoad: function onLoad() {
    // 切换到相应索引
    this.tabActive = this.$root.$mp.query.active || 0;
  }
});

/***/ }),

/***/ 325:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "my-order"
  }, [_c('van-tabs', {
    attrs: {
      "active": _vm.tabActive,
      "swipeable": "",
      "border": "false",
      "swipe-threshold": "6",
      "eventid": '6',
      "mpcomid": '4'
    },
    on: {
      "change": _vm.toggleTab
    }
  }, [_c('van-tab', {
    attrs: {
      "title": "全部",
      "mpcomid": '0'
    }
  }, _vm._l((_vm.orders), function(order, index) {
    return _c('div', {
      key: index,
      staticClass: "order-item"
    }, [_vm._l((order.goods_datas), function(goods, idx) {
      return _c('a', {
        key: goods.id,
        staticClass: "order-item__hd",
        attrs: {
          "href": '../order_detail/main?order_id=' + order.id
        }
      }, [_c('image', {
        staticClass: "u-goods__img",
        attrs: {
          "src": goods.cover
        }
      }), _vm._v(" "), _c('div', {
        staticClass: "order-item__right g-flex g-flex__column g-flex__space"
      }, [_c('p', {
        staticClass: "u-goods__tt overflow-dot"
      }, [_vm._v(_vm._s(goods.title))]), _vm._v(" "), _c('div', {
        staticClass: "g-flex"
      }, [_c('p', {
        staticClass: "order-item__price g-flex__item"
      }, [_vm._v("¥" + _vm._s(goods.sale_price))]), _vm._v(" "), _c('p', {
        staticClass: "order-item__num"
      }, [_vm._v("x" + _vm._s(goods.num))])], 1)], 1)])
    }), _vm._v(" "), (order.pay_status == 0 && order.refund == 0 || order.status_code > 0 && order.status_code < 4 && order.refund == 0 || order.refund == 0 && order.status_code == 3 || order.refund == 0 && order.pay_status == 1 && order.status_code > 3 && order.status_code != 7) ? _c('div', {
      staticClass: "order-item__ft"
    }, [(order.pay_status == 0) ? _c('a', {
      staticClass: "u-button u-button--border",
      attrs: {
        "href": "",
        "eventid": '0-' + index
      },
      on: {
        "click": function($event) {
          _vm.goPay(order.id, order.goods_datas.total_price)
        }
      }
    }, [_vm._v("去支付")]) : _vm._e(), _vm._v(" "), (order.status_code > 0 && order.status_code < 4) ? _c('a', {
      staticClass: "u-button u-button--border",
      attrs: {
        "href": '../logistics/main?order_id=' + order.id
      }
    }, [_vm._v("查看物流")]) : _vm._e(), _vm._v(" "), (order.status_code == 3) ? _c('a', {
      staticClass: "u-button u-button--primary",
      attrs: {
        "eventid": '1-' + index
      },
      on: {
        "click": function($event) {
          _vm.goReceiving(order.id)
        }
      }
    }, [_vm._v("确认收货")]) : _vm._e(), _vm._v(" "), (order.pay_status == 1 && order.status_code > 3 && order.status_code != 7) ? _c('button', {
      staticClass: "u-button u-button--primary",
      attrs: {
        "eventid": '2-' + index
      },
      on: {
        "click": function($event) {
          _vm.goComment(order)
        }
      }
    }, [_vm._v("去评价")]) : _vm._e()], 1) : _c('div', {
      staticClass: "order-item__ft"
    }, [(order.refund > 0) ? _c('button', {
      staticClass: "u-button u-button--disable"
    }, [_vm._v(_vm._s(order.refund_title))]) : _c('button', {
      staticClass: "u-button u-button--disable"
    }, [_vm._v("已完成")])], 1)], 2)
  })), _vm._v(" "), _c('van-tab', {
    attrs: {
      "title": "待支付",
      "mpcomid": '1'
    }
  }, _vm._l((_vm.waitPay), function(Pay, index) {
    return _c('div', {
      key: index,
      staticClass: "order-item"
    }, [_vm._l((Pay.goods_datas), function(goods, idx) {
      return _c('a', {
        key: goods.id,
        staticClass: "order-item__hd",
        attrs: {
          "href": '../order_detail/main?order_id=' + Pay.id
        }
      }, [_c('img', {
        staticClass: "u-goods__img",
        attrs: {
          "src": goods.cover
        }
      }), _vm._v(" "), _c('div', {
        staticClass: "order-item__right g-flex g-flex__column g-flex__space"
      }, [_c('p', {
        staticClass: "u-goods__tt overflow-dot"
      }, [_vm._v(_vm._s(goods.title))]), _vm._v(" "), _c('div', {
        staticClass: "g-flex"
      }, [_c('p', {
        staticClass: "order-item__price g-flex__item"
      }, [_vm._v("¥" + _vm._s(goods.sale_price))]), _vm._v(" "), _c('p', {
        staticClass: "order-item__num"
      }, [_vm._v("x" + _vm._s(goods.num))])], 1)], 1)])
    }), _vm._v(" "), _c('div', {
      staticClass: "order-item__ft"
    }, [(Pay.pay_status == 0) ? _c('a', {
      staticClass: "u-button u-button--border",
      attrs: {
        "href": "",
        "eventid": '3-' + index
      },
      on: {
        "click": function($event) {
          _vm.goPay(Pay.id, Pay.goods_datas.total_price)
        }
      }
    }, [_vm._v("去支付")]) : _vm._e()])], 2)
  })), _vm._v(" "), _c('van-tab', {
    attrs: {
      "title": "待收货",
      "mpcomid": '2'
    }
  }, _vm._l((_vm.waitCollect), function(collect, index) {
    return (_vm.waitCollect) ? _c('div', {
      key: index,
      staticClass: "order-item"
    }, [_vm._l((collect.goods_datas), function(goods, idx) {
      return _c('a', {
        key: goods.id,
        staticClass: "order-item__hd",
        attrs: {
          "href": '../order_detail/main?order_id=' + collect.id
        }
      }, [_c('image', {
        staticClass: "u-goods__img",
        attrs: {
          "src": goods.cover
        }
      }), _vm._v(" "), _c('div', {
        staticClass: "order-item__right g-flex g-flex__column g-flex__space"
      }, [_c('p', {
        staticClass: "u-goods__tt overflow-dot"
      }, [_vm._v(_vm._s(goods.title))]), _vm._v(" "), _c('div', {
        staticClass: "g-flex"
      }, [_c('p', {
        staticClass: "order-item__price g-flex__item"
      }, [_vm._v("¥" + _vm._s(goods.sale_price))]), _vm._v(" "), _c('p', {
        staticClass: "order-item__num"
      }, [_vm._v("x" + _vm._s(goods.num))])], 1)], 1)])
    }), _vm._v(" "), _c('div', {
      staticClass: "order-item__ft"
    }, [(collect.status_code > 0 && collect.status_code < 4) ? _c('a', {
      staticClass: "u-button u-button--border",
      attrs: {
        "href": '../logistics/main?order_id=' + collect.id
      }
    }, [_vm._v("查看物流")]) : _vm._e(), _vm._v(" "), (collect.refund == 0 && collect.status_code == 3) ? _c('button', {
      staticClass: "u-button u-button--primary",
      attrs: {
        "eventid": '4-' + index
      },
      on: {
        "click": function($event) {
          _vm.goReceiving(collect.id)
        }
      }
    }, [_vm._v("确认收货")]) : _vm._e()], 1)], 2) : _vm._e()
  })), _vm._v(" "), _c('van-tab', {
    attrs: {
      "title": "待评价",
      "mpcomid": '3'
    }
  }, _vm._l((_vm.waitComment), function(comment, index) {
    return _c('div', {
      key: index,
      staticClass: "order-item"
    }, [_vm._l((comment.goods_datas), function(goods, idx) {
      return _c('a', {
        key: goods.id,
        staticClass: "order-item__hd",
        attrs: {
          "href": '../order_detail/main?order_id=' + comment.id
        }
      }, [_c('image', {
        staticClass: "u-goods__img",
        attrs: {
          "src": goods.cover
        }
      }), _vm._v(" "), _c('div', {
        staticClass: "order-item__right g-flex g-flex__column g-flex__space"
      }, [_c('p', {
        staticClass: "u-goods__tt overflow-dot"
      }, [_vm._v(_vm._s(goods.title))]), _vm._v(" "), _c('div', {
        staticClass: "g-flex"
      }, [_c('p', {
        staticClass: "order-item__price g-flex__item"
      }, [_vm._v("¥" + _vm._s(goods.sale_price))]), _vm._v(" "), _c('p', {
        staticClass: "order-item__num"
      }, [_vm._v("x" + _vm._s(goods.num))])], 1)], 1)])
    }), _vm._v(" "), _c('div', {
      staticClass: "order-item__ft"
    }, [(comment.refund == 0 && comment.pay_status == 1 && comment.status_code > 3 && comment.status_code != 7) ? _c('button', {
      staticClass: "u-button u-button--primary",
      attrs: {
        "eventid": '5-' + index
      },
      on: {
        "click": function($event) {
          _vm.goComment(comment)
        }
      }
    }, [_vm._v("去评价")]) : _vm._e()], 1)], 2)
  }))], 1), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '5'
    }
  }), _vm._v(" "), _c('van-dialog', {
    attrs: {
      "id": "van-dialog",
      "mpcomid": '6'
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
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-3d125a34", esExports)
  }
}

/***/ })

},[321]);
//# sourceMappingURL=main.js.map