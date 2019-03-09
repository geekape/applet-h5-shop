<template>
  <div class="confirm-order" v-if="datas.goods_id || datas.id">
    <!-- 地址 -->
    <a href="../add_address/index?type=1" class="m-list order-line" v-if="address.truename">
      <div class="m-list__l">{{address.truename}}</div>
      <div class="m-list__c">
        <p class="">{{address.mobile}}</p>
        <p class="f-font-sm">{{address.address + address.address_detail}}</p>
      </div>
      <div class="m-list__r">
        <i class="iconfont icon-fanhui right"></i>
      </div>
    </a>
    
    <a href="../add_address/index?type=1" class="m-list link" v-else>
      <div class="m-list__l">添加地址</div>
      <p class="m-list__c"></p>
      <i class="iconfont icon-fanhui right"></i>
    </a>
    <!-- 订单 -->
    <div class="switch-card" v-for="(item, index) in goodsList" :key="index">
      <div class="switch-card__hd">
        <p class="switch-card__tt">
        <block v-if="item.key == 3">
          <van-radio-group class="switch-card__radio" value="1" :data-index="index" @change="toggleRadio" v-if="item.type == 1">
            <van-radio name="1">邮寄</van-radio>
            <van-radio name="2">自提</van-radio>
          </van-radio-group>
          <van-radio-group class="switch-card__radio" value="2" :data-index="index" @change="toggleRadio" v-if="item.type == 2">
            <van-radio name="1">邮寄</van-radio>
            <van-radio name="2">自提</van-radio>
          </van-radio-group>
        </block>

          <van-radio-group class="switch-card__radio" :value="item.type" :data-index="index" v-else>
            <van-radio v-if="item.type == 1" name="1">邮寄</van-radio>
            <van-radio v-if="item.type == 2" name="2">自提</van-radio>
          </van-radio-group>

        </p>
        <i class="iconfont icon-fanhui iconfont" :class="item.arrowDir" @click="toggleArrow" :data-index="index"></i>
      </div>
      <div class="switch-card__bd" v-show="item.arrowDir == 'top'">
        <a :href="'../goods_detail/index?id=' + item.id" open-type="redirect" class="goods-line">
          <img lazy-load class="u-goods__img" :src="item.cover"/>

          <div class="goods-line__right">
            <p class="u-goods__tt overflow-dot">
              <span class="s-red" v-if="datas.event_type == 1">【拼团】</span>
              <span class="s-red" v-else-if="datas.event_type == 2">【秒杀】</span>
              <span class="s-red" v-else-if="datas.event_type == 3">【砍价】</span>
              {{item.title}}</p>
            <div class="goods-line__ft">
              <div class="goods-line__price">
                <span>¥{{item.sale_price}}</span>
                <span class="goods__price-cost f-font-sm">¥{{item.market_price}}</span>
                </div>
              <p class="f-font-sm">x{{item.num}}</p>
            </div>
          </div>
        </a>
        
        <!-- 商品附加信息 -->
        <div class="m-list small">
          <p class="m-list__c">商品金额:</p>
          <p>¥{{item.sale_price}}</p>
        </div>
        <div class="m-list small" v-if="item.type == 1">
          <p class="m-list__c">邮费:</p>
          <p>+ ¥{{item.express}}</p>
        </div>
        <div class="m-list small" v-if="couponMoney != 0">
          <p class="m-list__c">优惠券:</p>
          <p>- ¥{{couponMoney}}</p>
        </div>
      </div>
    </div>

    <!-- 优惠券 -->
    <div class="m-list link" v-if="couponNum > 0" @click="togglePopup">
      <div class="m-list__l">优惠券</div>
      <p class="m-list__c" v-if="couponName==''">你有{{couponNum}}张优惠劵,点击使用</p>
      <p class="m-list__c" v-else>{{couponName}}</p>
      <i class="iconfont icon-fanhui right"></i>
    </div>

    <!-- 门店 -->
    <div class="m-list link" v-if="isShop" @click="toggleShopPopup">
      <div class="m-list__l">选择门店</div>
      <p class="m-list__c">{{shopName}}</p>
      <i class="iconfont icon-fanhui right"></i>
    </div>

    <!-- 留言 -->
    <div class="m-input g-flex">
      <span class="m-input__lable">买家留言:</span>
      <input  v-model="remark" type="text" placeholder="点击给卖家留言" class="g-flex__item">
    </div>

    <div class="tcp">
      <van-checkbox class="square-checkbox" :value="isTcp" @change="isTcp = !isTcp">我已同意</van-checkbox>
      <span class="s-link" @click="openTcpPopup">《客户协议》</span>
    </div>
    <van-popup class="tcpPopup" :show="isTcpPopup" @close="isTcpPopup=false">
      <wxParse :content="tcp" @preview="preview" @navigate="navigate" />
    </van-popup>

    <!-- 固定底部栏 -->
    <div class="bottom-bar g-flex">
      <div class="g-flex__item g-flex">
        <p>实付款：<p class="s-red">¥{{totalPrice}}</p></p>
        <p class="f-font-sm">含运费</p>
      </div>

      <button @click="submitOrder" class="u-button u-button--primary">提交订单</button>
    </div>
    <van-toast id="van-toast" />
    <van-popup :show="isPopup" position="bottom" @close="togglePopup">
      <div class="coupon">
      <div class="coupon-not">
        <div class="coupon-item" v-for="(coupon, index) in couponList" :key="index" :data-index="index" @click="selectCoupon">
          <!-- 金额 -->
          <div class="coupon-item__l">
            <span class="coupon-item__price">{{coupon.money}}</span>
            <span class="f-font-sm">元</span>
          </div>

          <!-- 满减 -->
          <div class="coupon-item__c">
            <p>{{coupon.title}}</p>
            <p class="coupon-item__time">{{coupon.start_time}}-{{coupon.end_time}}</p>
          </div>

          <!-- 按钮 -->
          <div class="coupon-item__r">
            <button class="coupon-item__btn">使用</button>
          </div>
        </div>
      </div>
      </div>
    </van-popup>

    <van-popup :show="isShopPopup" position="bottom" @close="toggleShopPopup">
      <van-radio-group :value="selfShopIdx" @change="selectShop">
        <van-cell-group>
          <van-cell v-for="(shop,shopIdx) in shopList" :key="shopIdx" :title="shop.name" clickable :data-index="shopIdx" @click="selectShop">
            <van-radio :name="shopIdx" />
          </van-cell>
        </van-cell-group>
      </van-radio-group>

    </van-popup>
  </div>
</template>

<script>
import { post, get, host, goPay } from "@/utils";
import Toast from "@/../static/vant/toast/toast";
import wxParse from "mpvue-wxparse";
/**
 * 配送类型
 * 1 为邮寄
 * 2 为自提
 * 3 为自提和邮寄混合
 *
 * 多个订单思路：
 * 1. 判断datas.lists 下所有key，都为1或都为2就合并订单
 * 2. key为1和3,或2和3，如果再者选择类型相同则可提交，不相同则提示警告
 * 3. key为1和2两个都不同状态的订单，警告不可以提交
 */

export default {
  mpType: 'page',
  data() {
    return {
      datas: [],
      address: {},
      goodsList: [],
      totalPrice: 0,
      isTcp: true,
      tcp: '',
      activeOrderParams: {},
      isTcpPopup: false,
      isPopup: false,
      couponNum: 0,
      couponName: "",
      couponMoney: 0,
      couponList: [],
      isShopPopup: false,
      isShop: false,
      shopList: [],
      selfShopIdx: -1,
      shopName: "点击选择门店",
      remark: "", // 留言
      goodsId: 0, //商品id
      storesId: 0, // 门店id
      snId: 0, // 优惠劵Id
      sendType: 1, // 配送类型-
      sendTypeObj: {}, // 配送商品对象
      isClickShop:0//是否已经点击门店列表，获取门店列表信息
    };
  },
  components: {
    wxParse
  },

  computed: {
    totalPrices () {
      console.log(this.goodsList)
      let totalMoeny = 0;
      const _this = this;
      this.goodsList.forEach((item,index) => {
    
        totalMoeny = parseFloat(totalMoeny) + (parseFloat(item.sale_price) * parseInt(item.num)) + parseFloat(item.express)
        _this.sendTypeObj[item.id] = item.type
        if(item.type == 2) {
          // 选中门店
          console.log('选中门店')
          totalMoeny = (parseFloat(totalMoeny) - parseFloat(item.express)).toFixed(2)
        } else {
          if(item.type != 1) totalMoeny = (parseFloat(totalMoeny) + parseFloat(item.express)).toFixed(2)
        }
      })
      this.totalPrice = totalMoeny
      return totalMoeny
    }
  },

  methods: {
    // 查看协议
    openTcpPopup() {
      this.isTcpPopup = true;
      get("shop/api/tcp").then(res => {
        this.tcp = res.tcp.replace(/\<image/gi, '<image style="width:100%;height:auto" ')
      });
    },

    // 选择优惠卷
    selectCoupon(e) {
      const idx = e.currentTarget.dataset.index;
      this.selfIdx = idx;
      this.couponName = `-${this.couponList[idx].money}元`
      this.isPopup = !this.isPopup;
      // 重新计算价格
      this.totalPrice -= parseFloat(this.couponList[idx].money);
      this.couponMoney = parseFloat(this.couponList[idx].money);
      this.snId = this.couponList[idx].sn_id; // 优惠券id
    },
    // 选择门店
    selectShop(e) {
      const idx = e.currentTarget.dataset.index;
      this.selfShopIdx = idx;
      this.shopName = this.shopList[idx].name;
      this.isShopPopup = !this.isShopPopup;
      this.storesId = this.shopList[idx].id; // 门店id
    },
    // 优惠卷弹窗
    togglePopup() {
      this.isPopup = !this.isPopup;
      const _this = this;
      if (this.isPopup == true) {
        post(
          'coupon/api/personal',{
            PHPSESSID: wx.getStorageSync("PHPSESSID"),
            str_coupon_id: _this.datas.str_coupon_id
          }).then(res => {
          console.log(res);
          _this.couponList = res.lists[0];
        });
      }
    },
    // 门店弹窗
    toggleShopPopup() {
      this.isShopPopup = !this.isShopPopup;
      let id = this.datas.allow_stores;
      if (this.isShopPopup == true) {
        post("shop/api/shop_list", {
          PHPSESSID: wx.getStorageSync("PHPSESSID"),
          is_choose: 1,
          ids: id
        }).then(res => {
          this.isClickShop=1;
          this.shopList = res.store_lists;
        });
      }
    },
    // 切换箭头方向
    toggleArrow(e) {
      console.log(e);
      let idx = e.target.dataset.index;
      let lists = JSON.parse(JSON.stringify(this.goodsList));
      let selfDir = lists[idx].arrowDir;
      if (selfDir == "top") {
        lists[idx].arrowDir = "bottom";
      } else {
        lists[idx].arrowDir = "top";
      }
      this.goodsList = lists;
    },

    submitOrder() {
      const _this = this;
      let address = this.address;
      let type = [];

      // 填写门店
      this.goodsList.forEach((item, idx) => {
        // 只要有一个为选中类型为自提
        type.push(item.type);
      });

      // 填写地址
      if (type.includes("1")) {
        if (!address.mobile) {
          Toast("请填写你的地址");
          return false;
        }
      }
      if (type.includes("2")) {
      
        if( this.isClickShop==1 && (!this.shopList || this.shopList.length<=0) ){
          Toast("商品没有共同的门店，请分开下单！");
          return false;
        }

        if (this.storesId == 0) {
          Toast("请选择配送门店");
          return false;
        }

      }

      // 同意协议
      if (!this.isTcp) {
        Toast("请同意客户协议");
        return false;
      }

      post("shop/api/add_order", {
        address_id: _this.address.id,
        remark: _this.remark, // 留言
        sn_id: _this.snId, // 优惠卷id
        stores_id: _this.storesId, // 门店id
        send_type: _this.sendType, //送货类型
        goods_send_type:_this.sendTypeObj,//各商品送货类型
        openid: wx.getStorageSync("openid"),
        is_weiapp: 1,
        PHPSESSID: wx.getStorageSync("PHPSESSID")
      }).then(res => {
        if (res.code == 0) {
          Toast(res.msg);
        } else {
          goPay(res.out_trade_no);
          // 清购物车数量
          let number = _this.goodsId.split(",").length;
          let cartNum = _this.$store.state.cartShopNum;
          let lastNum = parseInt(cartNum) - parseInt(number);
          _this.$store.commit("getCartShopNum", {
            num: lastNum
          });
        }
      });
    },

    toggleRadio(e) {
      let lists = JSON.parse(JSON.stringify(this.goodsList));
      let selfChecked = e.mp.detail;
      let idx = e.target.dataset.index;

      if (lists[idx].type != selfChecked) {
        lists[idx].type = selfChecked;
      }
      // 遍历商品
      let arr = [];
      lists.forEach((ele, idx) => {
        arr.push(parseInt(ele.type));
      });
      this.sendType = arr.join(",");
      // 判断是否有选中门点的
      if (arr.includes(2)) {
        this.isShop = true;
      } else {
        this.isShop = false;
      }

      this.goodsList = lists;
    },
    // 商品列表对象转数组
    shopListArr(obj) {
      const _this = this;
      let data = obj;

      let { keys, values, entries } = Object;
      let arr = [];
      let arr2 = [];

      for (let [key, value] of entries(data)) {
        value.forEach((item, idx) => {
          item.arrowDir = "top";

           // 从活动中进来，改变价格
          if(_this.activeOrderParams) {
            let price = JSON.parse(_this.activeOrderParams).activePrice
            item.sale_price = price
          }

          if (key != 3) {
            item.type = key;
          } else {
            item.type = 1;
            item.key = 3;
          }
          arr.push(item);
          arr2.push(parseInt(item.type));
        });
      }
      // 判断是否有选中门点的
      if (arr2.includes(2)) {
        this.isShop = true;
      } else {
        this.isShop = false;
      }

      this.goodsList = arr;
    },
    sendRequest(opt) {
      const _this = this;
      post("shop/api/confirm_order", opt)
        .then(res => {
          console.log('执行了请求')
          // 有商品错误
          if (res.code == 0) {
            wx.reLaunch({ url: "../msg/index?msg=" + res.msg + "&type=warn" });
          }
          _this.datas = res;
          _this.address = _this.datas.address;
          _this.couponNum = parseInt(_this.datas.coupon_num);
     
          _this.shopListArr(_this.datas.lists);
        })
        .catch(err => {
          console.log("错误信息：" + err);
        });
    },

    getData(type) {
      const _this = this;
      if (type == 1) {
        // 购物车进来的
        this.goodsId = this.$root.$mp.query.goodsIds;
        let opts = {
          goods_ids: this.$root.$mp.query.goodsIds,
          buyCount: this.$root.$mp.query.count,
          cart_ids: this.$root.$mp.query.cartIds,
          PHPSESSID: wx.getStorageSync('PHPSESSID')
        };
        _this.sendRequest(opts);
      }  else {
        let id = this.$root.$mp.query.goodsId;
        this.goodsId = id;
        let opts = {
          goods_id: id,
          PHPSESSID: wx.getStorageSync('PHPSESSID')
        };
        _this.sendRequest(opts);
      }
    }
  },
  onLoad() {
    Object.assign(this, this.$options.data());
    let type = this.$root.$mp.query.type || 0;
     this.activeOrderParams = this.$store.state.activeOrderParams
    if(this.activeOrderParams) {
      let opt = JSON.parse(this.$store.state.activeOrderParams)
      // 活动
      this.sendRequest(opt);
    } else {
      this.getData(type);
    }
    
  },

  onShow() {
    // 优惠劵被选择重新计算
    if (this.snId != 0) {
      this.totalPrice -= parseFloat(_this.couponMoney);
    }
    if (wx.getStorageSync("address")) {
      this.address = wx.getStorageSync("address");
    }
  }
};
</script>
<style lang="scss" scoped>
.confirm-order {
  background: transparent;
  padding-bottom: 55px;
  /deep/ .van-popup {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding-bottom: 15px;
    &--bottom {
      top: 30%;
    }
  }

  /deep/ .tcpPopup .van-popup {
    border-radius: 0;
    padding-bottom: 0;
    width: 80%;
    height: 60%;
  }
  /deep/ .van-cell {
    height: 45px;
    line-height: 45px;
    padding: 0 15px;
  }
  /deep/ .van-radio__icon--checked {
    color: #ff0204;
  }
  /deep/ .van-checkbox {
    &__icon {
      border-radius: 0;
      background-color: transparent;
      border-color: #ddd;
    }
  }
}

// 协议复选框
.square-checkbox {
  /deep/ .van-icon {
    color: #f9f9f9;
  }
  /deep/ .van-checkbox__icon--checked .van-icon {
    color: #333;
  }
}

// 表单input
.m-input {
  background: #fff;
  height: 45px;
  align-items: center;
  margin-top: 15px;
  padding: 0 15px;
  font-size: 16px;
  &__lable {
    margin-right: 10px;
  }
}

.order-line {
  padding: 60rpx 30rpx;
  align-items: flex-start;
  .f-font-sm {
    margin-top: 8px;
  }
  .m-list__r {
    height: 100%;
  }
}

.switch-card__radio {
  display: flex;
  align-items: center;
}
.switch-card__radio ._van-radio:first-child {
  margin-right: 60rpx;
}
._van-radio {
  display: inline-flex;
  height: 45px;
  align-items: center;
}
.switch-card__bd {
  border-top: 1px solid #ececec;
  .goods__price-cost {
    margin-left: 5px;
  }
}
.goods-line {
  padding: 15px 0;
  font-size: 16px;
}
.goods-line__right {
  border: 0;
  width: 100%;
}

// 底部栏
.bottom-bar {
  z-index: 99;
  display: flex;
  align-items: center;
  box-shadow: 1px 0 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  bottom: 0;
  width: 92%;
  height: 55px;
  background: #fff;
  padding: 0 15px;
  font-size: 16px;
  .s-red {
    font-size: 20px;
  }
  .u-button {
    width: 105px;
    text-align: center;
    margin: 0;
  }
  .g-flex__item {
    align-items: center;
  }
  .f-font-sm {
    color: #aaa;
    margin-left: 15px;
  }
}
@mixin xy-center() {
  display: flex;
  justify-content: center;
  align-items: center;
}
.coupon {
  &-item {
    background: linear-gradient(90deg, #ff0204, #ffb3b3);
    height: 100px;
    margin: 15px;
    margin-bottom: 0;
    color: #fff;
    opacity: 0.8;
    font-size: 16px;
    @include xy-center;
    // 按钮
    &__btn {
      width: 50px;
      height: 18px;
      color: $red;
      font-size: 12px;
      padding: 0;
      border-radius: 3px;
      background: #fff;
      @include xy-center;
    }

    &__r {
      flex: 1;
      height: 100px;
      max-width: 70px;
      @include xy-center;
    }
    &__c {
      flex: 2;
    }
    &__c,
    &__l {
      padding-left: 20px;
    }

    &__price {
      font-size: 32px;
    }
    &__time {
      margin-top: 5px;
      font-size: 11px;
    }
  }

  // 已使用
  &-done &-item {
    background: linear-gradient(90deg, #dddddd, #f5f5f5);
  }
  &-done &-item,
  &-done &-item__btn {
    color: #aaa;
  }
}

.tcp {
  display: flex;
  font-size: 13px;
  padding: 10px 15px;
}
.tcpPopup {
  padding: 10px;
  box-sizing: border-box;
  /deep/ img,
  image {
    width: 100% !important;
  }
}
</style>
