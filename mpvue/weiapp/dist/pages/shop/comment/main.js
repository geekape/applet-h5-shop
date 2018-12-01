require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([6],{

/***/ 152:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(153);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 153:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(156);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_07fa13ac_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(169);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(154)
  __webpack_require__(155)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-07fa13ac"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_07fa13ac_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\comment\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-07fa13ac", Component.options)
  } else {
    hotAPI.reload("data-v-07fa13ac", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 154:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 155:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 156:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_get_iterator__ = __webpack_require__(36);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_get_iterator___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_get_iterator__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_slicedToArray__ = __webpack_require__(60);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_slicedToArray___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_slicedToArray__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_babel_runtime_core_js_object_entries__ = __webpack_require__(61);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_babel_runtime_core_js_object_entries___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_babel_runtime_core_js_object_entries__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_babel_runtime_core_js_object_values__ = __webpack_require__(62);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_babel_runtime_core_js_object_values___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_babel_runtime_core_js_object_values__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_babel_runtime_core_js_object_keys__ = __webpack_require__(63);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_babel_runtime_core_js_object_keys___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_babel_runtime_core_js_object_keys__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__utils__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__static_vant_toast_toast__ = __webpack_require__(16);





//
//
//
//
//
//
//
//
//
//
//
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
      orderId: '',
      commentText: {},
      goodsId: 0,
      goodsList: [],
      is_first_action: true
    };
  },


  components: {},

  methods: {
    setText: function setText(e) {
      console.log(e);
      this.goodsId = String(this.goodsId).split(',');
      var allGoods = this.goodsId;
      var obj = {};
      allGoods.forEach(function (item, idx) {
        obj[item] = e.target.value;
      });

      console.log(obj);
      this.commentText = obj;
    },
    submitFrom: function submitFrom() {
      var _this = this;
      if (__WEBPACK_IMPORTED_MODULE_4_babel_runtime_core_js_object_keys___default()(this.commentText) === 0) {
        Object(__WEBPACK_IMPORTED_MODULE_6__static_vant_toast_toast__["a" /* default */])('评语不能为空');
        return false;
      }
      if (this.is_first_action) {
        _this.is_first_action = false;
        Object(__WEBPACK_IMPORTED_MODULE_5__utils__["g" /* post */])('shop/api/comment', {
          order_id: _this.orderId,
          PHPSESSID: wx.getStorageSync('PHPSESSID'),
          goodsids: _this.goodsId,
          content: _this.commentText
        }).then(function (res) {
          if (res.code == 0) {
            Object(__WEBPACK_IMPORTED_MODULE_6__static_vant_toast_toast__["a" /* default */])(res.msg);
            _this.is_first_action = true;
          } else {
            wx.reLaunch({ url: "../msg/main?msg=" + "评价成功" });
            _this.is_first_action = true;
          }
        });
      }
    },
    getData: function getData() {
      var _this2 = this;

      var _this = this;
      Object(__WEBPACK_IMPORTED_MODULE_5__utils__["g" /* post */])('shop/api/confirm_order', {
        PHPSESSID: wx.getStorageSync("PHPSESSID"),
        goods_id: _this.goodsId
      }).then(function (res) {
        var keys = __WEBPACK_IMPORTED_MODULE_4_babel_runtime_core_js_object_keys___default.a,
            values = __WEBPACK_IMPORTED_MODULE_3_babel_runtime_core_js_object_values___default.a,
            entries = __WEBPACK_IMPORTED_MODULE_2_babel_runtime_core_js_object_entries___default.a;

        var arr = [];
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_get_iterator___default()(entries(res.lists)), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var _ref = _step.value;

            var _ref2 = __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_slicedToArray___default()(_ref, 2);

            var key = _ref2[0];
            var value = _ref2[1];

            value.forEach(function (item, idx) {
              arr.push(item);
            });
          }
        } catch (err) {
          _didIteratorError = true;
          _iteratorError = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion && _iterator.return) {
              _iterator.return();
            }
          } finally {
            if (_didIteratorError) {
              throw _iteratorError;
            }
          }
        }

        _this2.goodsList = arr;
      });
    }
  },
  onLoad: function onLoad(opt) {
    this.orderId = this.$root.$mp.query.order_id;
    this.goodsId = this.$root.$mp.query.goods_id;
  },
  onShow: function onShow() {
    this.getData();
  }
});

/***/ }),

/***/ 169:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "comment"
  }, [_vm._l((_vm.goodsList), function(item, index) {
    return _c('div', {
      key: index,
      staticClass: "goods-line"
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
    }, [_c('span', [_vm._v("¥" + _vm._s(item.sale_price))])])])], 1)])
  }), _vm._v(" "), _c('textarea', {
    staticClass: "comment-box",
    attrs: {
      "name": "",
      "maxlength": "999",
      "placeholder": "至少5个字哦~",
      "eventid": '0'
    },
    on: {
      "input": _vm.setText
    }
  }), _vm._v(" "), _c('button', {
    staticClass: "u-button u-button--big u-button--primary",
    attrs: {
      "eventid": '1'
    },
    on: {
      "click": _vm.submitFrom
    }
  }, [_vm._v("评价")]), _vm._v(" "), _c('van-toast', {
    attrs: {
      "id": "van-toast",
      "mpcomid": '0'
    }
  })], 2)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-07fa13ac", esExports)
  }
}

/***/ })

},[152]);
//# sourceMappingURL=main.js.map