require("../../../common/manifest.js");
require("../../../common/vendor.js");
global.webpackJsonp([18],{

/***/ 269:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__index__ = __webpack_require__(270);



var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a(__WEBPACK_IMPORTED_MODULE_1__index__["a" /* default */]);
app.$mount();

/***/ }),

/***/ 270:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__ = __webpack_require__(272);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_5b280840_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__ = __webpack_require__(293);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(271)
}
var normalizeComponent = __webpack_require__(0)
/* script */

/* template */

/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-5b280840"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_script_index_0_index_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_template_compiler_index_id_data_v_5b280840_hasScoped_true_transformToRequire_video_src_source_src_img_src_image_xlink_href_node_modules_mpvue_loader_1_1_2_mpvue_loader_lib_selector_type_template_index_0_index_vue__["a" /* default */],
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src\\pages\\shop\\lists\\index.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key.substr(0, 2) !== "__"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] index.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-loader/node_modules/vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5b280840", Component.options)
  } else {
    hotAPI.reload("data-v-5b280840", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),

/***/ 271:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 272:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_set__ = __webpack_require__(273);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_set___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_set__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray__ = __webpack_require__(288);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_shop_search__ = __webpack_require__(83);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__components_shop_goodsList__ = __webpack_require__(84);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__utils__ = __webpack_require__(2);


//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {
    goodsList: __WEBPACK_IMPORTED_MODULE_3__components_shop_goodsList__["a" /* default */],
    search: __WEBPACK_IMPORTED_MODULE_2__components_shop_search__["a" /* default */]
  },

  data: function data() {
    return {
      datas: [],
      goods: [],
      isPopup: false,
      minPrice: 0,
      maxPrice: 0,
      sortList: [],
      checkSort: []
    };
  },

  computed: {
    checkList: function checkList() {}
  },
  methods: {
    toggleCheckbox: function toggleCheckbox(id) {
      var arr = this.checkSort;
      arr.push(id);
      this.checkSort = [].concat(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray___default()(new __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_set___default.a(arr)));
      console.log(this.checkSort);
    },
    checkboxChange: function checkboxChange(e) {
      console.log("checkbox发生change事件，携带value值为：", e.detail.value);
    },
    filterData: function filterData(e) {
      console.log(e);
      var _this = this;
      var opt = {
        cate_id: _this.checkSort.join(",")
      };
      if (this.minPrice && this.maxPrice) {
        opt.min_price = this.minPrice;
        opt.max_price = this.maxPrice;
      }
      Object(__WEBPACK_IMPORTED_MODULE_4__utils__["g" /* post */])("shop/api/lists", opt).then(function (data) {
        console.log(data);
        _this.goods = data.goods;
      });

      this.isPopup = false;
    },
    setValue: function setValue(e) {
      console.log(e);
      var val = e.target.value;
      var index = e.target.dataset.index;
      console.log(e, val, index);
      if (index == 1) {
        this.minPrice = val || 0;
      } else {
        this.maxPrice = val || 0;
      }
    },

    // 发送请求
    getData: function getData(opt) {
      var _this = this;
      console.log(_this.GLOBAL.app.id, _this.GLOBAL.app.pid);
      Object(__WEBPACK_IMPORTED_MODULE_4__utils__["g" /* post */])(__WEBPACK_IMPORTED_MODULE_4__utils__["e" /* host */] + "shop/api/lists", opt).then(function (data) {
        console.log(data);
        _this.goods = data.goods;

        // 清空值
        _this.GLOBAL.app.id = 0;
        _this.GLOBAL.app.pid = 0;
        _this.GLOBAL.app.listsType = 0;
      });
    },
    search: function search() {
      var _this = this;
      console.log("search_key:" + _this.GLOBAL.app.searchKey);
      Object(__WEBPACK_IMPORTED_MODULE_4__utils__["g" /* post */])("shop/api/lists", {
        search_key: _this.GLOBAL.app.searchKey
      }).then(function (data) {
        console.log(data);
        _this.goods = data.goods;

        // 清空值
        _this.GLOBAL.app.listsType = 0;
        _this.GLOBAL.app.listsTysearchKeype = "";
      });
    },
    togglePopup: function togglePopup() {
      this.isPopup = !this.isPopup;
    }
  },
  onShow: function onShow() {
    var _this = this;
    if (this.GLOBAL.app.listsType == 1) {
      // 搜索跳转
      console.log("搜索跳转");
      this.search();
    } else if (this.GLOBAL.app.listsType == 2) {
      // 同款跳转
      this.getData({
        tab_goods_id: _this.GLOBAL.app.id,
        tab: _this.GLOBAL.app.pid
      });
    } else {
      this.getData({
        cate_id: _this.GLOBAL.app.id,
        pid: _this.GLOBAL.app.pid
      });
    }
  },
  onLoad: function onLoad() {
    var _this2 = this;

    // 设置购物车数量
    Object(__WEBPACK_IMPORTED_MODULE_4__utils__["b" /* get */])("shop/api/cart/PHPSESSID/" + wx.getStorageSync("PHPSESSID")).then(function (res) {
      var num = res.lists.length;
      _this2.$store.commit("getCartShopNum", {
        num: num
      });
    }).catch(function (err) {
      console.log("失败：" + err);
    });
    // 获取分类
    Object(__WEBPACK_IMPORTED_MODULE_4__utils__["b" /* get */])("shop/api/category").then(function (res) {
      _this2.sortList = res;
    });
  }
});

/***/ }),

/***/ 293:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "lists"
  }, [_c('div', {
    staticClass: "g-flex"
  }, [_c('search', {
    attrs: {
      "type": 2,
      "mpcomid": '0'
    }
  }), _vm._v(" "), _c('span', {
    staticClass: "icon-filter",
    attrs: {
      "eventid": '0'
    },
    on: {
      "click": _vm.togglePopup
    }
  })], 1), _vm._v(" "), (_vm.goods[0]) ? _c('goodsList', {
    attrs: {
      "goodsData": _vm.goods,
      "mpcomid": '1'
    }
  }) : _c('div', {
    staticClass: "hint-page"
  }, [_c('img', {
    attrs: {
      "src": "../../../../static/img/nothing.png"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "hint-page__text"
  }, [_vm._v("还没有任何商品")])], 1), _vm._v(" "), _c('van-popup', {
    staticClass: "popup",
    attrs: {
      "show": _vm.isPopup,
      "position": "right",
      "eventid": '6',
      "mpcomid": '3'
    },
    on: {
      "close": _vm.togglePopup
    }
  }, [_c('form', {
    attrs: {
      "eventid": '5'
    },
    on: {
      "submit": _vm.filterData
    }
  }, [_c('div', {
    staticClass: "popup-item"
  }, [_c('p', {
    staticClass: "popup-item__tt"
  }, [_vm._v("价格区间(元)")]), _vm._v(" "), _c('div', {
    staticClass: "popup-item__price"
  }, [_c('input', {
    staticClass: "popup-item__search",
    attrs: {
      "type": "number",
      "data-index": "1",
      "eventid": '1'
    },
    on: {
      "blur": _vm.setValue
    }
  }), _c('div', {
    staticClass: "popup-item__line"
  }, [_vm._v("—")]), _c('input', {
    staticClass: "popup-item__search",
    attrs: {
      "type": "number",
      "data-index": "2",
      "eventid": '2'
    },
    on: {
      "blur": _vm.setValue
    }
  })])], 1), _vm._v(" "), _vm._l((_vm.sortList.top_list), function(value, key) {
    return (_vm.sortList.sub_list[key]) ? _c('div', {
      key: value.pid,
      staticClass: "popup-item"
    }, [_c('p', {
      staticClass: "popup-item__tt"
    }, [_vm._v(_vm._s(value.title))]), _vm._v(" "), _c('div', {
      staticClass: "popup-item__sort"
    }, _vm._l((_vm.sortList.sub_list[key]), function(item, index) {
      return _c('checkbox-group', {
        key: item.id,
        attrs: {
          "eventid": '4-' + key + '-' + index,
          "mpcomid": '2-' + key + '-' + index
        },
        on: {
          "click": function($event) {
            _vm.toggleCheckbox(item.id)
          }
        }
      }, [_c('input', {
        directives: [{
          name: "model",
          rawName: "v-model",
          value: (_vm.checkSort),
          expression: "checkSort"
        }],
        attrs: {
          "type": "checkbox",
          "id": item.id,
          "value": item.id,
          "eventid": '3-' + key + '-' + index
        },
        domProps: {
          "checked": Array.isArray(_vm.checkSort) ? _vm._i(_vm.checkSort, item.id) > -1 : (_vm.checkSort)
        },
        on: {
          "__c": function($event) {
            var $$a = _vm.checkSort,
              $$el = $event.target,
              $$c = $$el.checked ? (true) : (false);
            if (Array.isArray($$a)) {
              var $$v = item.id,
                $$i = _vm._i($$a, $$v);
              if ($$c) {
                $$i < 0 && (_vm.checkSort = $$a.concat($$v))
              } else {
                $$i > -1 && (_vm.checkSort = $$a.slice(0, $$i).concat($$a.slice($$i + 1)))
              }
            } else {
              _vm.checkSort = $$c
            }
          }
        }
      }), _vm._v(" "), _c('label', {
        attrs: {
          "for": item.id
        }
      }, [_vm._v(_vm._s(item.title))])], 1)
    }))], 1) : _vm._e()
  }), _vm._v(" "), _c('div', {
    staticClass: "popup-button"
  }, [_c('button', {
    staticClass: "popup-button__reset",
    attrs: {
      "form-type": "reset"
    }
  }, [_vm._v("重置")]), _vm._v(" "), _c('button', {
    staticClass: "popup-button__sure",
    attrs: {
      "form-type": "submit"
    }
  }, [_vm._v("确定")])], 1)], 2)], 1)], 1)
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-loader/node_modules/vue-hot-reload-api").rerender("data-v-5b280840", esExports)
  }
}

/***/ })

},[269]);
//# sourceMappingURL=main.js.map