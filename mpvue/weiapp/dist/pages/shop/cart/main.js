require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([24],{

/***/ 273:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(274);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 274:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(276);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_0529e4cb_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(277);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(275)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-0529e4cb"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_0529e4cb_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\cart\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0529e4cb", Component.options)
  } else {
    hotAPI.reload("data-v-0529e4cb", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 275:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 276:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify__ = __webpack_require__(38);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__static_vant_dialog_dialog__ = __webpack_require__(66);

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




// 基本思路
// 1. 单/取选
// 2. 全/取选
// 3. 计算总价/数量

/* harmony default export */ __webpack_exports__["a"] = ({
  components: {},
  data: function data() {
    return {
      carts: [],
      totalPrice: 0,
      totalCount: 0,
      isCheckAll: false,
      freight: 0
    };
  },

  methods: {
    // 增加减少数量
    toggleNum: function toggleNum(e) {
      var idx = e.target.dataset.index;
      var lists = JSON.parse(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_json_stringify___default()(this.carts));
      lists[idx].num = e.mp.detail;
      this.carts = lists;
      this.totalMoney();
    },

    // 选择单个
    isCheckSingle: function isCheckSingle(e) {
      var idx = e.target.dataset.index;
      var newArr = this.carts[idx];
      newArr.isCheck = e.mp.detail;
      this.carts.splice(idx, 1, newArr);

      this.totalMoney();

      // 是否全选
      var num = 0;
      this.carts.forEach(function (item, index) {
        if (item.isCheck == true) {
          num++;
        }
      });
      if (num == this.carts.length) {
        this.isCheckAll = true;
      } else {
        this.isCheckAll = false;
      }
    },

    // 全选
    allChecked: function allChecked(e) {
      var _this2 = this;

      console.log(e);
      this.isCheckAll = !this.isCheckAll;
      this.carts.forEach(function (item, index) {
        if (_this2.isCheckAll) {
          item.isCheck = true;
        } else {
          item.isCheck = false;
        }
      });

      this.totalMoney();
    },

    // 计算总价
    totalMoney: function totalMoney() {
      var money = 0;
      var num = 0;
      var freight = 0;
      this.carts.forEach(function (item, index) {
        if (item.isCheck) {
          money += parseFloat(item.price) * parseInt(item.num);
          num++;
          freight += item.goods.express;
        }
      });
      this.totalPrice = money;
      this.totalCount = num;
      this.freight = parseFloat(freight).toFixed(2);
    },

    // 删除商品
    delGoods: function delGoods() {
      var that = this;
      var lists = this.carts;
      // 计算总金额
      var cartIds = [];
      var left = [];
      for (var i = 0; i < lists.length; i++) {
        if (lists[i].isCheck) {
          cartIds.push(lists[i].id);
        } else {
          left.push(lists[i]);
        }
      }
      cartIds = cartIds.join();
      if (cartIds == "") {
        Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])("请选择要删除的购物车物品");
      } else {
        __WEBPACK_IMPORTED_MODULE_3__static_vant_dialog_dialog__["a" /* default */].confirm({
          title: "提示",
          message: "确认删除？"
        }).then(function () {
          // on confirm
          Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])("shop/api/delCart", {
            ids: cartIds,
            PHPSESSID: wx.getStorageSync("PHPSESSID")
          }).then(function (res) {
            that.carts = left;
          });
          var delLen = cartIds.split(",").length;
          var num = that.$store.state.cartShopNum;
          var lastCartNum = num - delLen;
          that.$store.commit("getCartShopNum", {
            num: lastCartNum
          });
        }).catch(function () {
          // on cancel
        });
      }
    },


    // 结算
    goPay: function goPay() {
      // 遍历选中的商品
      var checkedId = [];
      var cartId = [];
      var goodsCount = {};

      this.carts.forEach(function (item, index) {
        if (item.isCheck == true) {
          checkedId.push(item.goods_id);
          cartId.push(item.id);

          // 多个商品
          goodsCount[item.goods_id] = item.num;
        }
      });
      checkedId = checkedId.join(",");
      cartId = cartId.join(",");

      if (checkedId == "") {
        Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])("请选择购买的商品");
      } else {
        var opt = {
          goodsIds: checkedId,
          cartIds: cartId,
          count: goodsCount
        };
        wx.setStorageSync("cartsOpt", opt);
        wx.navigateTo({
          url: "../confirm_order/main?type=1"
        });
      }
    },
    getData: function getData() {
      var _this3 = this;

      var _this = this;
      // 设置购物车数量
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])("shop/api/cart/", {
        PHPSESSID: wx.getStorageSync("PHPSESSID")
      }).then(function (res) {
        _this3.carts = res.lists;
        _this3.carts.forEach(function (item, index) {
          item.isCheck = false;
        });
        var num = res.lists.length;
        _this.$store.commit("getCartShopNum", {
          num: num
        });
        _this3.isCheckAll = false;
        _this3.freight = 0;
      }).catch(function (err) {
        console.log("失败：" + err);
      });
    }
  },
  onShow: function onShow() {
    this.getData();
    this.totalPrice = 0;
    this.totalCount = 0;
  },
  onLoad: function onLoad() {
    // 清空活动信息
    this.$store.commit("saveData", { key: "activeOrderParams", value: "" });

    this.getData();
  }
});

/***/ }),

/***/ 277:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "cart"
  }, [_c('i', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.carts.length > 0),
      expression: "carts.length>0"
    }],
    staticClass: "iconfont icon-shanchu",
    attrs: {
      "eventid": '0'
    },
    on: {
      "click": _vm.delGoods
    }
  }), _vm._v(" "), (_vm.carts.length > 0) ? _c('div', {
    staticClass: "cart-goods"
  }, _vm._l((_vm.carts), function(item, index) {
    return _c('a', {
      key: item.id,
      staticClass: "cart-goods__item g-flex",
      attrs: {
        "href": '../goods_detail/main?id=' + item.goods.id,
        "hover-class": "none"
      }
    }, [_c('van-checkbox', {
      attrs: {
        "catchtap": "",
        "value": item.isCheck,
        "data-index": index,
        "eventid": '1-' + index,
        "mpcomid": '0-' + index
      },
      on: {
        "change": _vm.isCheckSingle
      }
    }), _vm._v(" "), _c('img', {
      staticClass: "cart-goods__img u-goods__img",
      attrs: {
        "src": item.goods.cover
      }
    }), _vm._v(" "), _c('div', {
      staticClass: "cart-goods__info"
    }, [_c('p', {
      staticClass: "u-goods__tt overflow-dot"
    }, [_vm._v(_vm._s(item.goods_name))]), _vm._v(" "), _c('div', {
      staticClass: "g-flex cart-goods__ft"
    }, [_c('div', {
      staticClass: "cart-goods__price"
    }, [_vm._v("¥ " + _vm._s(item.price))]), _vm._v(" "), _c('div', {
      staticClass: "cart-goods__count"
    }, [_c('van-stepper', {
      attrs: {
        "catchtap": "",
        "value": item.num,
        "integer": "",
        "data-index": index,
        "eventid": '2-' + index,
        "mpcomid": '1-' + index
      },
      on: {
        "change": _vm.toggleNum
      }
    })], 1)])], 1)], 1)
  })) : _c('div', {
    staticClass: "hint-page"
  }, [_c('img', {
    attrs: {
      "src": "../../../../static/img/null.png",
      "alt": ""
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "hint-page__text"
  }, [_vm._v("购物车空空如也")]), _vm._v(" "), _c('a', {
    staticClass: "u-button u-button--primary",
    attrs: {
      "href": "../index/main",
      "open-type": "switchTab"
    }
  }, [_vm._v("随便逛逛")])], 1), _vm._v(" "), (_vm.carts.length > 0) ? _c('div', {
    staticClass: "closing-bar"
  }, [_c('van-checkbox', {
    attrs: {
      "value": _vm.isCheckAll,
      "eventid": '3',
      "mpcomid": '2'
    },
    on: {
      "change": _vm.allChecked
    }
  }, [_vm._v("全选")]), _vm._v(" "), _c('div', {
    staticClass: "closing-bar__info"
  }, [_c('p', {
    staticClass: "closing-bar__price"
  }, [_vm._v("总计(不含运费): "), _c('span', {
    staticClass: "s-red"
  }, [_vm._v("¥" + _vm._s(_vm.totalPrice))])]), _vm._v(" "), _c('p', {
    staticClass: "s-gray"
  }, [_vm._v("运费¥" + _vm._s(_vm.freight))])], 1), _vm._v(" "), _c('button', {
    staticClass: "closing-bar__btn",
    attrs: {
      "eventid": '4'
    },
    on: {
      "click": _vm.goPay
    }
  }, [_vm._v("\n     去结算(" + _vm._s(_vm.totalCount) + ")\n   ")])], 1) : _vm._e(), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '3'
    }
  }), _vm._v(" "), _c('van-dialog', {
    attrs: {
      "id": "van-dialog",
      "mpcomid": '4'
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
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-0529e4cb", esExports)
  }
}

/***/ })

},[273]);
//# sourceMappingURL=main.js.map