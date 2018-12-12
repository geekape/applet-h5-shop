require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([20],{

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
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_6c6c8a85_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(325);
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
var __vue_scopeId__ = "data-v-6c6c8a85"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_6c6c8a85_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\goods_detail\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6c6c8a85", Component.options)
  } else {
    hotAPI.reload("data-v-6c6c8a85", Component.options)
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
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__ = __webpack_require__(32);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_mpvue_wxparse__ = __webpack_require__(65);

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
      slides: [],
      goods: [],
      isCollect: false,
      arrowDir: 'top',
      selfSwiperNum: 1,
      isCartDot: false,
      detailPic: ''
    };
  },

  components: {
    wxParse: __WEBPACK_IMPORTED_MODULE_3_mpvue_wxparse__["a" /* default */]
  },
  computed: {
    totalSwiperNum: function totalSwiperNum() {
      return this.slides.length;
    },
    cartNum: function cartNum() {
      var num = this.$store.state.cartShopNum;
      if (num > 0) {
        this.isCartDot = true;
        return num;
      }
    }
  },

  methods: {
    // 跳转同款
    jump: function jump(id, tab) {
      this.GLOBAL.app.id = id;
      this.GLOBAL.app.pid = tab;
      this.GLOBAL.app.listsType = 2;
      wx.switchTab({
        url: '/pages/shop/lists/main'
      });
    },

    // 购买
    buy: function buy() {
      var goodsId = this.goods.goods_id;
      // 库存为0
      if (this.goods.stock_active == 0) {
        Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('该商品已经被抢光了');
        return false;
      }

      wx.navigateTo({
        url: '../confirm_order/main?goodsId=' + goodsId
      });
    },

    // 切换箭头方向
    toggleArrow: function toggleArrow() {
      this.arrowDir == 'top' ? this.arrowDir = 'bottom' : this.arrowDir = 'top';
    },

    // 切换轮播
    toggleSwiper: function toggleSwiper(e) {
      this.selfSwiperNum = e.target.current + 1;
    },
    addCart: function addCart() {
      var _this = this;

      this.$http.post(__WEBPACK_IMPORTED_MODULE_1__utils__["e" /* host */] + 'shop/api/addToCart', {
        goods_id: this.goods.id,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then(function (res) {
        if (res.data > 0) {
          Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('加入购物车成功');
          _this.$store.commit('getCartShopNum', {
            num: res.data
          });
        } else {
          Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('加入购物车失败,请直接下单购买');
        }
      });
    },
    pvwImg: function pvwImg(url) {
      // 预览图片
      var _this = this;
      wx.previewImage({
        current: url, // 当前显示图片的http链接  
        urls: _this.slides // 需要预览的图片http链接列表  
      });
    },
    toggleCollect: function toggleCollect(showHint) {
      var _this2 = this;

      // 收藏
      this.$http.post(__WEBPACK_IMPORTED_MODULE_1__utils__["e" /* host */] + 'shop/api/addtocollect', {
        goods_id: this.goods.id,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then(function (res) {
        if (res.data == 1) {
          Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('加入收藏成功');

          _this2.isCollect = true;
        } else {
          Object(__WEBPACK_IMPORTED_MODULE_2__static_vant_toast_toast__["a" /* default */])('取消收藏成功');

          _this2.isCollect = false;
        }
      });
    }
  },

  onLoad: function onLoad() {
    __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default()(this, this.$options.data());
    // 清空活动信息
    this.$store.commit("saveData", { key: "activeOrderParams", value: "" });

    var _this = this;
    var id = this.$root.$mp.query.id;
    Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* post */])('shop/api/goods_detail', {
      id: id,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then(function (res) {
      // 商品图
      _this.slides = res.goods.imgs_url;
      _this.goods = res.goods;
      _this.detailPic = res.goods.content.replace(/\<img/gi, '<img style="width:100%;height:auto" ');

      if (res.goods.is_collect == 0) {
        _this.isCollect = false;
      } else {
        _this.isCollect = true;
      }
    });
  }
});

/***/ }),

/***/ 325:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return (_vm.goods) ? _c('div', {
    staticClass: "goods-detail"
  }, [_c('div', {
    staticClass: "slide"
  }, [_c('swiper', {
    staticClass: "swiper",
    attrs: {
      "indicator-dots": false,
      "autoplay": "",
      "eventid": '1'
    },
    on: {
      "change": _vm.toggleSwiper
    }
  }, _vm._l((_vm.slides), function(item, index) {
    return _c('swiper-item', {
      key: index,
      attrs: {
        "eventid": '0-' + index,
        "mpcomid": '0-' + index
      },
      on: {
        "click": function($event) {
          _vm.pvwImg(item)
        }
      }
    }, [_c('a', {
      staticClass: "slide-url"
    }, [_c('img', {
      staticClass: "slide-image",
      attrs: {
        "src": item,
        "mode": "aspectFill"
      }
    })])])
  })), _vm._v(" "), _c('p', {
    staticClass: "slide-count"
  }, [_vm._v(_vm._s(_vm.selfSwiperNum) + "/"), _c('span', {
    staticClass: "s-gray"
  }, [_vm._v(_vm._s(_vm.totalSwiperNum))])])], 1), _vm._v(" "), _c('div', {
    staticClass: "goods-detail__info"
  }, [_c('p', {
    staticClass: "goods-detail__price s-red"
  }, [_c('span', {
    staticClass: "icon-price"
  }, [_vm._v("¥")]), _vm._v(_vm._s(_vm.goods.sale_price))]), _vm._v(" "), (_vm.goods.market_price > _vm.goods.sale_price) ? _c('p', {
    staticClass: "goods__price-cost"
  }, [_vm._v("¥" + _vm._s(_vm.goods.market_price))]) : _vm._e(), _vm._v(" "), _c('p', {
    staticClass: "s-gray goods-detail__stock"
  }, [_vm._v("库存" + _vm._s(_vm.goods.stock_active) + "件")]), _vm._v(" "), _c('p', {
    staticClass: "goods-detail__tt"
  }, [_vm._v(_vm._s(_vm.goods.title))])], 1), _vm._v(" "), (_vm.goods.tab) ? _c('a', {
    staticClass: "m-list link g-flex",
    attrs: {
      "eventid": '2'
    },
    on: {
      "click": function($event) {
        _vm.jump(_vm.goods.id, _vm.goods.tab)
      }
    }
  }, [_c('div', {
    staticClass: "m-list__l g-flex__item"
  }, [_vm._v("同款")]), _vm._v(" "), _c('i', {
    staticClass: "iconfont icon-fanhui right"
  })], 1) : _vm._e(), _vm._v(" "), (_vm.goods.goods_param) ? _c('div', {
    staticClass: "switch-card"
  }, [_c('div', {
    staticClass: "switch-card__hd"
  }, [_c('p', {
    staticClass: "switch-card__tt"
  }, [_vm._v("产品参数")]), _vm._v(" "), _c('p', {
    staticClass: "switch-card__icon iconfont icon-fanhui",
    class: _vm.arrowDir,
    attrs: {
      "eventid": '3'
    },
    on: {
      "click": _vm.toggleArrow
    }
  })], 1), _vm._v(" "), _c('div', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.arrowDir == 'top'),
      expression: "arrowDir == 'top'"
    }],
    staticClass: "switch-card__bd"
  }, _vm._l((_vm.goods.goods_param), function(param, paramIdx) {
    return _c('div', {
      key: paramIdx,
      staticClass: "switch-card__item"
    }, [_c('p', {
      staticClass: "switch-card__param overflow-dot_row"
    }, [_vm._v(_vm._s(param.title))]), _vm._v(" "), _c('p', {
      staticClass: "switch-card__attr"
    }, [_vm._v(_vm._s(param.param_value))])], 1)
  }))]) : _vm._e(), _vm._v(" "), (_vm.goods.comment_count > 0) ? _c('div', {
    staticClass: "goods-comment"
  }, [_c('div', {
    staticClass: "m-list link"
  }, [_c('div', {
    staticClass: "m-list__l"
  }, [_vm._v("评价")]), _vm._v(" "), _c('p', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.goods.comment_count > 10),
      expression: "goods.comment_count>10"
    }],
    staticClass: "m-list__c s-black"
  }, [_vm._v("查看更多")]), _vm._v(" "), _c('i', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.goods.comment_count > 10),
      expression: "goods.comment_count>10"
    }],
    staticClass: "iconfont icon-fanhui right"
  })], 1), _vm._v(" "), _c('scroll-view', {
    staticClass: "goods-comment__bd",
    attrs: {
      "scroll-x": "true"
    }
  }, _vm._l((_vm.goods.comments), function(comment, commentIdx) {
    return _c('div', {
      key: commentIdx,
      staticClass: "goods-comment__item"
    }, [_c('div', {
      staticClass: "goods-comment__left"
    }, [_c('div', {
      staticClass: "g-flex g-flex__updown-center"
    }, [_c('img', {
      staticClass: "u-head__img"
    }), _vm._v(" "), _c('p', {
      staticClass: "goods-comment__name"
    }, [_vm._v(_vm._s(comment.username))])], 1), _vm._v(" "), _c('p', {
      staticClass: "goods-comment__text"
    }, [_vm._v(_vm._s(comment.content))])], 1), _vm._v(" "), _c('div', {
      staticClass: "goods-comment__right"
    }, [_c('img', {
      staticClass: "u-goods__img",
      attrs: {
        "src": _vm.slides[0]
      }
    })])])
  }))], 1) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "goods-detail__pic"
  }, [_c('wxParse', {
    attrs: {
      "content": _vm.detailPic,
      "eventid": '4',
      "mpcomid": '1'
    },
    on: {
      "preview": _vm.preview,
      "navigate": _vm.navigate
    }
  })], 1), _vm._v(" "), _c('div', {
    staticClass: "bottom-bar"
  }, [_c('button', {
    staticClass: "bottom-bar__service",
    attrs: {
      "open-type": "contact"
    }
  }, [_c('div', {
    staticClass: "bottom-bar__icon"
  }), _vm._v(" "), _c('p', {
    staticClass: "bottom-bar__tt"
  }, [_vm._v("客服")])], 1), _vm._v(" "), _c('div', {
    staticClass: "bottom-bar__collect",
    attrs: {
      "eventid": '5'
    },
    on: {
      "click": _vm.toggleCollect
    }
  }, [_c('div', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (!_vm.isCollect),
      expression: "!isCollect"
    }],
    staticClass: "bottom-bar__icon"
  }), _vm._v(" "), _c('div', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.isCollect),
      expression: "isCollect"
    }],
    staticClass: "bottom-bar__icon--active"
  }), _vm._v(" "), _c('p', {
    staticClass: "bottom-bar__tt"
  }, [_vm._v("收藏")])], 1), _vm._v(" "), _c('a', {
    staticClass: "bottom-bar__cart",
    attrs: {
      "href": "../cart/main",
      "open-type": "switchTab"
    }
  }, [_c('div', {
    staticClass: "bottom-bar__icon"
  }, [(_vm.cartNum > 0) ? _c('span', {
    staticClass: "weui-badge",
    staticStyle: {
      "position": "absolute",
      "top": "-.2em",
      "right": "-.4em"
    }
  }, [_vm._v(_vm._s(_vm.cartNum))]) : _vm._e()]), _vm._v(" "), _c('p', {
    staticClass: "bottom-bar__tt"
  }, [_vm._v("购物车")])], 1), _vm._v(" "), _c('button', {
    staticClass: "u-button u-button--border",
    attrs: {
      "eventid": '6'
    },
    on: {
      "click": _vm.addCart
    }
  }, [_vm._v("加入购物车")]), _vm._v(" "), _c('button', {
    staticClass: "u-button u-button--primary",
    attrs: {
      "eventid": '7'
    },
    on: {
      "click": _vm.buy
    }
  }, [_vm._v("立即购买")])], 1), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '2'
    }
  })], 1) : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-6c6c8a85", esExports)
  }
}

/***/ })

},[321]);
//# sourceMappingURL=main.js.map