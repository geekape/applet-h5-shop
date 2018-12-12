require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([23],{

/***/ 278:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(279);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 279:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(281);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_7074d880_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(286);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(280)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-7074d880"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_7074d880_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\center\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7074d880", Component.options)
  } else {
    hotAPI.reload("data-v-7074d880", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 280:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 281:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_regenerator__ = __webpack_require__(282);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_regenerator___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_regenerator__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_asyncToGenerator__ = __webpack_require__(285);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_asyncToGenerator___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_asyncToGenerator__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vuex__ = __webpack_require__(83);


//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
      userData: [],
      icons: [{
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-lg1.png',
        text: '待支付',
        url: '../my_order/main?active=1'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-lg2.png',
        text: '待收货',
        url: '../my_order/main?active=2'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-lg3.png',
        text: '待评价',
        url: '../my_order/main?active=3'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-lg4.png',
        text: '全部订单',
        url: '../my_order/main?active=0'
      }],
      smallIcons: [{
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md1.png',
        text: '优惠劵',
        url: '../coupon/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md2.png',
        text: '会员卡',
        url: '#'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md3.png',
        text: '我的收藏',
        url: '../collect/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md4.png',
        text: '我的足迹',
        url: '../track/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md5.png',
        text: '我的地址',
        url: '../add_address/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md6.png',
        text: '我的评价',
        url: '../my_comment/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md8.png',
        text: '我的拼团',
        url: '../../collage/lists/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md9.png',
        text: '我的秒杀',
        url: '../../seckill/lists/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md10.png',
        text: '我的砍价',
        url: '../../haggle/lists/main'
      }, {
        img: 'https://leyao.tv/yi/images/new_icon/center-icon-md11.png',
        text: '领卷中心',
        url: '../../coupon/center/main'
      }]
    };
  },

  methods: {
    getData: function getData() {
      var _this = this;

      return __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_asyncToGenerator___default()( /*#__PURE__*/__WEBPACK_IMPORTED_MODULE_0_babel_runtime_regenerator___default.a.mark(function _callee() {
        var sessId, data;
        return __WEBPACK_IMPORTED_MODULE_0_babel_runtime_regenerator___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                sessId = wx.getStorageSync("PHPSESSID");
                _context.next = 3;
                return Object(__WEBPACK_IMPORTED_MODULE_2__utils__["b" /* get */])("/shop/api/my_order/PHPSESSID/" + sessId);

              case 3:
                data = _context.sent;

                _this.$store.commit('saveOrder', {
                  order: data.orderList
                });

              case 5:
              case 'end':
                return _context.stop();
            }
          }
        }, _callee, _this);
      }))();
    }
  },
  computed: {
    waitPayNum: function waitPayNum() {
      return this.$store.getters.waitPay.length;
    },
    waitCollectNum: function waitCollectNum() {
      return this.$store.getters.waitCollect.length;
    },
    waitCommentNum: function waitCommentNum() {
      return this.$store.getters.waitComment.length;
    }
  },
  onLoad: function onLoad() {
    var _this2 = this;

    this.userData = Object(__WEBPACK_IMPORTED_MODULE_2__utils__["f" /* login */])();
    // 设置购物车数量
    Object(__WEBPACK_IMPORTED_MODULE_2__utils__["b" /* get */])("shop/api/cart/PHPSESSID/" + wx.getStorageSync("PHPSESSID")).then(function (res) {
      var num = res.lists.length;
      _this2.$store.commit("getCartShopNum", {
        num: num
      });
    }).catch(function (err) {
      console.log("失败：" + err);
    });
  },
  onShow: function onShow() {
    /**
     * 订单逻辑
     * 1. 进入个人中心，请求订单接口，利用vuex存储所有订单
     * 2. 在vuex中利用Getter筛选每个订单类型，并把数量记录在state中
     * 3. 在我的订单页面，使用computed取vuex中各个类型的订单数据
     * 4. 使用commit或才dispatch 显式提交每次更改，以便数据是响应式的
     */
    this.getData();
  }
});

/***/ }),

/***/ 286:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "center"
  }, [_c('div', {
    staticClass: "center-hd"
  }, [_c('img', {
    staticClass: "center-hd__img",
    attrs: {
      "src": _vm.userData.avatarUrl
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "center-hd__name"
  }, [_vm._v(_vm._s(_vm.userData.nickName))])], 1), _vm._v(" "), _c('div', {
    staticClass: "icon-area"
  }, [_c('div', {
    staticClass: "icon-area__hd"
  }, [_c('a', {
    staticClass: "icon-area__item",
    attrs: {
      "href": "../my_order/main?active=0"
    }
  }, [_c('img', {
    staticClass: "icon-area__img",
    attrs: {
      "src": "../../../../static/img/new_icon/center-icon-lg4.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "icon-area__txt"
  }, [_vm._v("全部订单")])], 1), _vm._v(" "), _c('a', {
    staticClass: "icon-area__item",
    attrs: {
      "href": "../my_order/main?active=1"
    }
  }, [(_vm.waitPayNum > 0) ? _c('span', {
    staticClass: "weui-badge"
  }, [_vm._v(_vm._s(_vm.waitPayNum))]) : _vm._e(), _vm._v(" "), _c('img', {
    staticClass: "icon-area__img",
    attrs: {
      "src": "../../../../static/img/new_icon/center-icon-lg1.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "icon-area__txt"
  }, [_vm._v("待支付")])], 1), _vm._v(" "), _c('a', {
    staticClass: "icon-area__item",
    attrs: {
      "href": "../my_order/main?active=2"
    }
  }, [(_vm.waitCollectNum > 0) ? _c('span', {
    staticClass: "weui-badge"
  }, [_vm._v(_vm._s(_vm.waitCollectNum))]) : _vm._e(), _vm._v(" "), _c('img', {
    staticClass: "icon-area__img",
    attrs: {
      "src": "../../../../static/img/new_icon/center-icon-lg2.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "icon-area__txt"
  }, [_vm._v("待收货")])], 1), _vm._v(" "), _c('a', {
    staticClass: "icon-area__item",
    attrs: {
      "href": "../my_order/main?active=3"
    }
  }, [(_vm.waitCommentNum > 0) ? _c('span', {
    staticClass: "weui-badge"
  }, [_vm._v(_vm._s(_vm.waitCommentNum))]) : _vm._e(), _vm._v(" "), _c('img', {
    staticClass: "icon-area__img",
    attrs: {
      "src": "../../../../static/img/new_icon/center-icon-lg3.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "icon-area__txt"
  }, [_vm._v("待评价")])], 1)]), _vm._v(" "), _c('div', {
    staticClass: "icon-area__ct"
  }, [_vm._l((_vm.smallIcons), function(item, index) {
    return _c('a', {
      key: index,
      staticClass: "icon-area__item",
      attrs: {
        "href": item.url
      }
    }, [_c('img', {
      staticClass: "icon-area__img",
      attrs: {
        "src": item.img
      }
    }), _vm._v(" "), _c('p', {
      staticClass: "icon-area__txt"
    }, [_vm._v(_vm._s(item.text))])], 1)
  }), _vm._v(" "), _c('button', {
    staticClass: "icon-area__item contact-btn",
    attrs: {
      "open-type": "contact"
    }
  }, [_c('img', {
    staticClass: "icon-area__img",
    attrs: {
      "src": "../../../../static/img/new_icon/center-icon-md7.png",
      "alt": ""
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "icon-area__txt"
  }, [_vm._v("联系客服")])], 1)], 2)])])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-7074d880", esExports)
  }
}

/***/ })

},[278]);
//# sourceMappingURL=main.js.map