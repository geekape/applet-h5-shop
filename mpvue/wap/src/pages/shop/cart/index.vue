<template>
  <div class="cart">
    <scroller v-if="datas.wpid">
      

      <template v-if="carts.length">
        <i class="iconfont icon-shanchu" @click="delGoods"></i>
        <div class="cart-goods">
          <div class="cart-goods__item g-flex" v-for="(item,index) in carts" :key="item.id">
            <!-- <van-checkbox v-model="item.isCheck"></van-checkbox> -->
            <div class="cart-goods_checkbox" @click="singleChecked(index)">
              <van-icon name="circle" v-show="!item.isCheck"/>
              <van-icon name="checked" v-show="item.isCheck"/>
            </div>
          
            <img class="cart-goods__img u-goods__img" :src="item.goods.cover">
            <div class="cart-goods__info">
              <p class="u-goods__tt overflow-dot">{{item.goods_name}}</p>

              <div class="g-flex cart-goods__ft">
                <div class="cart-goods__price">¥ {{item.price}}</div>
                <div class="cart-goods__count">
                  <van-stepper v-model="item.num" :data-index="index"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="hint-page">
          <img src="../../../../static/img/null.png" alt>
          <p class="hwint-page__text">购物车空空如也</p>
          <router-link to="/" class="u-button u-button--primary">随便逛逛</router-link>
        </div>
      </template>
    </scroller>

    <!-- 结算栏 -->
    <div class="closing-bar" v-if="datas.wpid">
      <!-- <van-checkbox v-model="isCheckAll" @change="toggleAllCheck">全选</van-checkbox> -->
      <div class="closing-bar__checkbox" @click="toggleAllCheck">
        <van-icon name="circle" v-show="!isCheckAll"/>
        <van-icon name="checked" v-show="isCheckAll"/>全选
      </div>

      <div class="closing-bar__info">
        <p class="closing-bar__price">
          总计(不含运费):
          <span class="s-red">¥{{totalPrice}}</span>
        </p>
        <p class="s-gray">运费¥{{freight}}</p>
      </div>
      <button class="closing-bar__btn" @click="goPay">去结算({{totalCount}})</button>
    </div>
    <tabbar :checkedIndex="3"></tabbar>
  </div>
</template>

<script>
import { post, get, wx } from "@/utils";
import { Dialog, Toast } from "vant";
import tabbar from "@/components/tabbar";
// 基本思路
// 1. 单/取选
// 2. 全/取选
// 3. 计算总价/数量

export default {
  components: { tabbar },
  data() {
    return {
      datas: [],
      carts: [],
      totalPrices: 0,
      totalCount: 0,
      isCheckAll: false,
      freight: 0
    };
  },
  computed: {
    // 总价
    totalPrice() {
      let money = 0;
      let num = 0;
      let freight = 0;
      this.carts.forEach((item, idx) => {
        if (item.isCheck == true) {
          money += parseFloat((item.price * item.num).toFixed(2));
          num++;
          freight += parseFloat(item.goods.express);
        }
      });
      this.totalCount = num;
      this.freight = parseFloat(freight).toFixed(2);
      return money;
    }
  },
  methods: {
    toggleAllCheck() {
      console.log('我触发了')
      const _this = this
      this.isCheckAll = !this.isCheckAll
      this.carts.forEach((item,index) => {item.isCheck = _this.isCheckAll})
    },
    // 单个选中
    singleChecked (index) {
      console.log(index);
      let checkedNum = 0
      this.carts[index].isCheck = !this.carts[index].isCheck
      this.carts.forEach((item,index) => {
        if(item.isCheck) {
          checkedNum++
        }
      })
      this.isCheckAll = this.carts.length == checkedNum ? true : false
    },
    
    goPay() {
      // 遍历选中的商品
      let checkedId = [];
      let cartId = [];
      let goodsCount = {};

      this.carts.forEach((item, index) => {
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
        Toast("请选择购买的商品");
      } else {
        this.$router.push({
          name: `confirm_order`,
          params: {
            type: 1,
            goodsId: checkedId,
            cartIds: cartId,
            count: goodsCount
          }
        });
      }
    },
    delGoods() {
      var that = this;
      var lists = this.carts;
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
        Toast("请选择要删除的购物车物品");
      } else {
        Dialog.confirm({
          title: "提示",
          message: "确认删除？"
        })
          .then(() => {
            // on confirm
            post("shop/api/delCart", {
              ids: cartIds,
              PHPSESSID: window.localStorage.getItem("PHPSESSID")
            }).then(res => {
              that.carts = left;
            });
            let delLen = cartIds.split(",").length;
            let num = that.$store.state.cartShopNum;
            let lastCartNum = num - delLen;
            that.$store.commit("getCartShopNum", {
              num: lastCartNum
            });
          })
          .catch(() => {
            // on cancel
          });
      }
    },
    getData() {
      const _this = this;
      // 设置购物车数量
      post("shop/api/cart/", {
        PHPSESSID: window.localStorage.getItem("PHPSESSID")
      })
        .then(res => {
          res.lists.forEach((item, index) => {
            item.isCheck = false;
          });
          this.datas = res;
          this.carts = res.lists;
          let num = res.lists.length;
          console.log(num, res);
          this.isCheckAll = false;
          _this.$store.commit("getCartShopNum", {
            num: num
          });
        })
        .catch(err => {
          console.log("失败：" + err);
        });
    }
  },
  activated() {
    if (this.$route.meta.isBack) {
      Object.assign(this.$data, this.$options.data())
      this.getData()
    }
    this.$route.meta.isBack = false;
  },
  beforeRouteEnter (to, from, next) {
    console.log(`从${from.name}到${to.name}`)
    if(from.name == "goods_detail") {
      to.meta.isBack = true
    }
    next()
  },
  mounted() {
    console.log('只加载一次购物车')
    this.getData()
  }
};
</script>



<style lang="scss" scoped>
%f-align-center {
  display: flex;
  align-items: ceter;
}
// 购物车icon
.van-icon-circle:before {
  content: '';
  width: 20px;
  height: 20px;
  border: 1px solid #eee;
  border-radius: 50%;
}
.van-icon-checked {
  font-size: 22px;
  color: red;
}
.cart-goods_checkbox {
  
  .van-icon {
      line-height: 100px;
      margin-right: 10px;
  }
}

.cart {
  padding-bottom: 115px;
  /deep/ ._v-container > ._v-content {padding-bottom: 100px;}
  .hint-page__text {
    margin-top: 10px;
  }
  .u-button--primary {
    margin-top: 1rem;
    min-width: 140px;
  }
  .icon-shanchu {
    font-size: 22px;
    text-align: right;
    padding: 2px 15px;
    display: block;
  }
  .van-button--large {
    background: $red;
    color: #fff;
    border-radius: 5px;
  }
  
  /deep/ .van-stepper {
    &__minus,
    &__plus {
      width: 0.9rem;
    }
    &__minus {
      border-radius: 15px 0 0 15px;
      border-right: 0;
    }
    &__plus {
      border-radius: 0 15px 15px 0;
      border-left: 0;
    }
  }

  &-goods {
    &__item {
      background: #fff;
      margin-bottom: 10px;
      padding: 15px;
    }
    /deep/ .van-checkbox {
      height: 100px;
      display: flex;
      align-items: center;
    }

    &__info {
      margin-left: 10px;
      position: relative;
      width: 100%;
    }
    // 商品价格数量
    &__ft {
      position: absolute;
      bottom: 0;
      width: 100%;
    }
    &__price {
      color: $red;
      flex: 1;
    }
  }
}

// 结算栏
.closing-bar {
  @extend %f-align-center;
  position: fixed;
  bottom: 55PX;
  font-size: 12px;
  width: 100%;
  padding: 5px 10px;
  background: #fff;
  height: 45px;
  &__checkbox {
    display: flex;
    align-items: center;
    height: 1.2rem;
  }
  .van-icon {margin-right: 5px;}

  &__info {
    flex: 1;
    text-align: right;
    margin-right: 10px;
  }
  &__btn {
    min-width: 120px;
    border-radius: 30px;
    background: linear-gradient(90deg, #ff0204 60%, #ff5b5c, #ffb3b3);
    color: #fff;
    margin-right: 20px;
    height: 35px;
    line-height: 35px;
    font-size: 16px;
  }
  &__price {
    margin-bottom: 5px;
    font-size: 14px;
  }
  /deep/ ._van-checkbox {
    height: 45px;
  }
}
</style>
