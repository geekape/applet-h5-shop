<template>
  <div class="confirm-order">
    <navbar text="提交订单"></navbar>
    <scroller v-if="datas.goods_id || datas.id">
    <!-- 地址 -->
    <div @click="jump" class="m-list order-line" v-if="address">
      <div class="m-list__l">{{address.truename}}</div>
      <div class="m-list__c">
        <p class="">{{address.mobile}}</p>
        <p class="f-font-sm">{{address.address + address.address_detail}}</p>
      </div>
      <div class="m-list__r">
        <i class="iconfont icon-fanhui right"></i>
      </div>
    </div>

    <div @click="jump" class="m-list link" v-else>
      <div class="m-list__l">填写地址</div>
      <div class="m-list__c"></div>
      <i class="iconfont icon-fanhui right"></i>
    </div>
 
    <!-- 订单 -->
    <div class="switch-card" v-for="(item, index) in goodsList" :key="index">
      <div class="switch-card__hd">
        <p class="switch-card__tt">
        <template v-if="item.key == 3">
          <van-radio-group class="switch-card__radio" v-model="item.type">
            <van-radio name="1">邮寄</van-radio>
            <van-radio name="2">自提</van-radio>
          </van-radio-group>
        </template>

          <van-radio-group class="switch-card__radio" v-model="item.type" :data-index="index" v-else>
            <van-radio v-if="item.type == 1" name="1">邮寄</van-radio>
            <van-radio v-if="item.type == 2" name="2">自提</van-radio>
          </van-radio-group>

        </p>
        <i class="iconfont icon-fanhui iconfont" :class="item.arrowDir" @click="toggleArrow" :data-index="index"></i>
      </div>
      <div class="switch-card__bd" v-show="item.arrowDir == 'top'">
        <router-link :to="'/goods_detail/' + item.id" class="goods-line">
          <img class="u-goods__img" :src="item.cover"/>

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
        </router-link>
        
        <!-- 商品附加信息 -->
        <div class="m-list small">
          <p class="m-list__c">商品金额:</p>
          <p>¥{{item.sale_price}}</p>
        </div>
        <div class="m-list small" v-if="item.type == 1">
          <p class="m-list__c">邮费:</p>
          <p>+ ¥{{item.express}}</p>
        </div>
        <!-- <div class="m-list small" v-if="couponMoney != 0">
          <p class="m-list__c">优惠券:</p>
          <p>- ¥{{couponMoney}}</p>
        </div> -->
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
      <van-checkbox class="square-checkbox" shape="square" v-model="isTcp">我已同意</van-checkbox><span class="s-link" @click="openTcpPopup">《客户协议》</span>
    </div>
    

    </scroller>
    


    <!-- 固定底部栏 -->
    <div class="bottom-bar g-flex" v-if="datas.goods_id || datas.id">
      <div class="g-flex__item g-flex">
        <p>实付款：<p class="s-red">¥{{totalPrice}}</p></p>
        <p class="f-font-sm">含运费</p>
      </div>

      <button @click="submitOrder" class="u-button u-button--primary">提交订单</button>
    </div>
    <van-popup class="tcpPopup" v-model="isTcpPopup">
      <div class="tcpPopup-box" v-html="tcp"></div>
    </van-popup>
    <van-popup v-model="isPopup" position="bottom" @close="togglePopup">
      <div class="coupon">
      <div class="coupon-not">
        <div class="coupon-item" v-for="(coupon, index) in couponList" :key="index" :data-index="index" @click="selectCoupon(index)">
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

    <van-popup v-model="isShopPopup" position="bottom" @close="toggleShopPopup">
      <van-radio-group v-model="selfShopIdx">
        <van-cell-group>
          <van-cell v-for="(shop,shopIdx) in shopList" :key="shopIdx" :title="shop.name" clickable @click="selectShop(shopIdx)">
            <van-radio :name="shopIdx" />
          </van-cell>
        </van-cell-group>
      </van-radio-group>

    </van-popup>
  </div>
</template>

<script>
import { post, get, host, goPay,wxConfig } from "@/utils";
import navbar from "@/components/navbar";
import { Popup, Toast } from "vant";
const wx = require("weixin-js-sdk");

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
  data() {
    return {
      one: "1",
      tow: "2",
      datas: [],
      address: {},
      goodsList: [],
      totalPrice: 0,
      tcp: '',
      isTcp: true,
      isPopup: false,
      isTcpPopup: false,
      couponNum: 0,
      couponName: "",
      couponMoney: 0,
      couponList: [],
      isShopPopup: false,
      shopList: [],
      selfShopIdx: -1,
      shopName: "点击选择门店",
			activeOrderParms: {},
      remark: "", // 留言
      goodsId: 0, //商品id
      storesId: 0, // 门店id
      snId: 0, // 优惠劵Id
      sendType: 1, // 配送类型
      sendTypeObj: {}, // 配送商品对象
      isClickShop:0//是否已经点击门店列表，获取门店列表信息
    };
  },
  components: {
    navbar
  },

  computed: {
    isShop() {
      const _this = this
      let isShop = false;
      let money = 0;
      let sendArr = [];
      this.goodsList.forEach((item, index) => {
        money = (parseFloat(money) + (parseFloat(item.sale_price) * parseInt(item.num)) + parseFloat(item.express)).toFixed(2)
        sendArr.push(item.type);
        _this.sendTypeObj[item.id] = item.type
        if (item.type == 2) {
          isShop = true;
          money = (parseFloat(money) - parseFloat(item.express)).toFixed(2)
        } else {
          // console.log('有选邮寄的')
        }
      });
      this.totalPrice = parseFloat(money) // 总价
      this.sendType = sendArr.join(","); //配送方式
      return isShop;
    },
    totalPrices() {}
  },

  methods: {
    // 查看协议
    openTcpPopup () {
      this.isTcpPopup = true
      get('shop/api/tcp').then(res => {
        this.tcp = res.tcp
      })
    },
    jump() {
      this.$router.push({ name: "address", params: { type: 1 } });
    },
    // 选择优惠卷
    selectCoupon(idx) {
      this.couponName = '-' + this.couponList[idx].money + "元优惠劵";
      this.isPopup = !this.isPopup;
      // 重新计算价格
      this.totalPrice -= parseFloat(this.couponList[idx].money);
      this.totalPrice = this.totalPrice < 0 ?  0 : this.totalPrice
      
      this.couponMoney = parseFloat(this.couponList[idx].money);
      this.snId = this.couponList[idx].sn_id; // 优惠券id
    },
    // 选择门店
    selectShop(idx) {
      this.shopName = this.shopList[idx].name;
      this.storesId = this.shopList[idx].id; // 门店id
      this.isShopPopup = !this.isShopPopup;
    },
    // 优惠卷弹窗
    togglePopup() {
      this.isPopup = !this.isPopup;
      const _this = this;
      if (this.isPopup == true) {
        post('coupon/api/personal',{
            PHPSESSID: window.localStorage.getItem("PHPSESSID"),
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
      const _this = this;
      let id = this.datas.allow_stores;
      if (this.isShopPopup == true) {
        post("shop/api/shop_list", {
          PHPSESSID: window.localStorage.getItem("PHPSESSID"),
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
      let idx = e.target.dataset.index || 0;
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
      let type = 0;

      // 填写地址
      if (!address.mobile) {
        Toast("请填写你的地址");
        return false;
      }

      // 同意协议
      if (!this.isTcp) {
        Toast("请同意协议");
        return false;
      }

      // 配送门店
      if (this.sendType.includes(2)) {

        if( this.isClickShop==1 && (!this.shopList || this.shopList.length<=0) ){
         Toast("商品没有共同的门店，请分开下单！");
          return false;
        }

        if (this.storesId == 0) {
          Toast("请选择配送门店");
          return false;
        }
      }
    
      post("shop/api/add_order", {
        address_id: _this.address.id,
        remark: _this.remark, // 留言
        sn_id: _this.snId, // 优惠卷id
        stores_id: _this.storesId, // 门店id
        send_type: _this.sendType, //送货类型
        goods_send_type:_this.sendTypeObj,//各商品送货类型
        openid: window.localStorage.getItem("openid"),
        is_weiapp: 0,
        PHPSESSID: window.localStorage.getItem("PHPSESSID")
      }).then(res => {
        if (res.code == 0) {
           let msg=res.msg!=''?res.msg:'请求错误';
           Toast(msg);
        } else {
          console.log("开始改善get请求");
          goPay(res.out_trade_no, this.totalPrice);
        }
      });
    },
    // 商品列表对象转数组
    shopListArr(obj) {
      const _this = this;
      let data = JSON.parse(JSON.stringify(obj));
      let { keys, values, entries } = Object;
      let arr = [];

      for (let [key, value] of entries(data)) {
        value.forEach((item, idx) => {
          item.arrowDir = "top";

          // 从活动中进来，改变价格
          if(_this.activeOrderParams) {
            console.log(_this.activeOrderParams)
            item.sale_price = JSON.parse(_this.activeOrderParams).activePrice
          }

          if (key != 3) {
            item.type = key;
          } else {
            item.type = 1 + "";
            console.log("item.type:", item.type);
            item.key = 3;
          }
          arr.push(item);
        });
      }
      this.goodsList = arr;
    },
    sendRequest(opt) {
      const _this = this;
      post("shop/api/confirm_order", opt)
        .then(res => {
          // 有商品错误
          if (res.code == 0) {
            console.log("错误了");
            _this.$router.push({
              name: "msg",
              params: { msg: res.msg, type: "warn" }
            });
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
      this.activeOrderParams = this.$store.state.activeOrderParams

      if (type == 1) {
        console.log(this.$route);
        // 购物车进来的
        let opts = {
          goods_ids: this.$route.params.goodsId,
          PHPSESSID: window.localStorage.getItem("PHPSESSID"),
          buyCount: this.$route.params.count,
          cart_ids: this.$route.params.cartIds
        };
        _this.sendRequest(opts);
      } else if(this.activeOrderParams) {
        let opt = JSON.parse(this.activeOrderParams)
        // 活动
        this.sendRequest(opt);
        console.log('商品格格：', opt.activePrice)
        
      } else {
        let id = this.$route.params.id;
        console.log(this.$route);
        this.goodsId = id;
        let opts = {
          goods_id: id,
          PHPSESSID: window.localStorage.getItem("PHPSESSID")
        };
        _this.sendRequest(opts);
        
      }
    }
  },
  created() {
    let type = this.$route.params.type;
		this.activeOrderParms = this.$store.state.activeOrderParams
    this.getData(type);
    wxConfig();
  },

  onShow() {
    // 优惠劵被选择重新计算
    if (this.snId != 0) {
      this.totalPrice -= parseFloat(_this.couponMoney);
    }
  }
};
</script>
<style lang="scss" scoped>


.confirm-order {
  background: transparent;
  padding-top: 45px;
  padding-bottom: 55px;
  /deep/ ._v-container > ._v-content {padding-bottom: 100px;}
  /deep/ .van-popup {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding-bottom: 15px;
    &--bottom {
      top: 30%;
    }
  }
  .tcpPopup {
    border-radius: 0;
    padding-bottom: 0;
    width: 80%;
    height: 60%;
  }
  .tcpPopup-box {
    height: 100%;
    padding: 10px;
    box-sizing: border-box;
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;
    /deep/ img,image {width:100%!important}
  }
  /deep/ .van-cell {
    height: 45px;
    line-height: 45px;
    padding: 0 15px;
    &__value {
      flex: none;
    }
  }
  /deep/ .van-radio .van-icon-checked {
    color: $red;
  }
  /deep/ .van-radio {
    display: flex;
    height: 1.2rem;
    align-items: center;
    margin-right: 20px;
  }
  /deep/ .van-radio__input {
    line-height: 1;
  }
  /deep/ .van-radio__label {
    margin-left: 5px;
  }
  /deep/ .van-checkbox__icon--checked .van-icon {
    color: #333;
    border-color: #ddd;
    background-color: transparent;
  }
}

// 协议复选框
.square-checkbox {
  /deep/ .van-checkbox__icon {
    border-radius: 0;
    background-color: transparent;
    border-color: #ddd;
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
    display: flex;
    align-items: center;
  }
  &__lable,
  input {
    height: 20px;
    display: flex;
  }
}

.order-line {
  padding: 30px 15px;
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

</style>
