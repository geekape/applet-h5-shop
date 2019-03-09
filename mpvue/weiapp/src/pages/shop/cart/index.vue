<template>
  <div class="cart">
    <i class="iconfont icon-shanchu" @click="delGoods" v-show="carts.length>0"></i>
    <div class="cart-goods" v-if="carts.length>0">
      <div hover-class="none" class="cart-goods__item g-flex" v-for="(item,index) in carts" :key="item.id">
        <van-checkbox :value="item.isCheck" @change="isCheckSingle" :data-index="index"></van-checkbox>
        <img lazy-load class="cart-goods__img u-goods__img" :src="item.goods.cover" />
        <div class="cart-goods__info">
          <p class="u-goods__tt overflow-dot">{{item.goods_name}}</p>

          <div class="g-flex cart-goods__ft">
            <div class="cart-goods__price">¥ {{item.price}}</div>
            <div class="cart-goods__count">
              <van-stepper :value="item.num" integer @change="toggleNum" :data-index="index" />
            </div>
          </div>
        </div>
      </div>

    </div> 
    <div class="hint-page" v-else>
      <img lazy-load :src="imgRoot+'null.png'" alt="">
      <p class="hint-page__text">购物车空空如也</p>
      <a href="../index/index" open-type="switchTab" class="u-button u-button--primary">随便逛逛</a>
    </div>

    <!-- 结算栏 -->
   <div class="closing-bar" v-if="carts.length > 0">
     <van-checkbox :value="isCheckAll" @change="allChecked">全选</van-checkbox>

     <div class="closing-bar__info">
       <p class="closing-bar__price">总计(不含运费): <span class="s-red">¥{{totalPrice}}</span></p>
       <p class="s-gray">运费¥{{freight}}</p>
     </div>

     <button class="closing-bar__btn" @click="goPay">
       去结算({{totalCount}})
     </button>
   </div>
   <van-toast id="van-toast" />
   <van-dialog id="van-dialog" />
  </div>
   
</template>

<script>
import { post } from "@/utils";
import Toast from "@/../static/vant/toast/toast";
import Dialog from "@/../static/vant/dialog/dialog";
// 基本思路
// 1. 单/取选
// 2. 全/取选
// 3. 计算总价/数量

export default {
  config: {
    navigationBarTitleText: '购物车'
  },
  components: {},
  data() {
    return {
			imgRoot: this.imgRoot,
      carts: [],
      totalPrice: 0,
      totalCount: 0,
      isCheckAll: false,
      freight: 0
    };
  },
  methods: {
    // 增加减少数量
    toggleNum(e) {
      const idx = e.target.dataset.index;
      let lists = JSON.parse(JSON.stringify(this.carts));
      lists[idx].num = e.mp.detail;
      this.carts = lists;
      this.totalMoney();
    },
    // 选择单个
    isCheckSingle(e) {
      var idx = e.target.dataset.index;
      var newArr = this.carts[idx];
      newArr.isCheck = e.mp.detail;
      this.carts.splice(idx, 1, newArr);

      this.totalMoney();

      // 是否全选
      var num = 0;
      this.carts.forEach((item, index) => {
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
    allChecked(e) {
      console.log(e)
      this.isCheckAll = !this.isCheckAll;
      this.carts.forEach((item, index) => {
        if (this.isCheckAll) {
          item.isCheck = true;
        } else {
          item.isCheck = false;
        }
      });

      this.totalMoney();
    },
    // 计算总价
    totalMoney() {
      let money = 0;
      let num = 0;
      let freight = 0;
      this.carts.forEach((item, index) => {
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
    delGoods() {
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
              PHPSESSID: wx.getStorageSync("PHPSESSID")
            }).then(res => {
              that.carts = left;
            });
            that.totalPrice = 0;
            that.totalCount = 0;
            that.freight = 0;
            
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

    // 结算
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
        wx.navigateTo({
          url: `../confirm_order/index?type=1&goodsIds=${checkedId}&cartIds=${cartId}&count=${goodsCount}`
        });
      }
    },
    getData() {
      const _this = this;
      // 设置购物车数量
      post("shop/api/cart/", {
        PHPSESSID: wx.getStorageSync("PHPSESSID")
      })
        .then(res => {
          this.carts = res.lists;
          this.carts.forEach((item, index) => {
            item.isCheck = false;
          });
          let num = res.lists.length;
          _this.$store.commit("getCartShopNum", {
            num: num
          });
          this.isCheckAll = false;
          this.freight = 0;
        })
        .catch(err => {
          console.log("失败：" + err);
        });
    }
  },
  onShow() {
    this.getData();
  },
  onLoad () {
    // 清空活动信息
    this.$store.commit("saveData", {key: "activeOrderParams",value: "" });
  }
};
</script>



<style lang="scss" scoped>
.cart {
  padding-bottom: 50px;

  /deep/ .hint-page__text {
    margin-top: 10px;
  }
  /deep/ .u-button--primary {
    margin-top: 30px;
    min-width: 140px;
  }

  .icon-shanchu {
    font-size: 22px;
    text-align: right;
    padding: 2px 15px;
  }
  .van-button--large {
    background: $red;
    color: #fff;
    border-radius: 5px;
  }
  // van-checkbox
  /deep/ ._van-checkbox {
    height: 100px;
    padding-right: 10px;
    display: flex;
    align-items:center;
  }
  /deep/ .van-checkbox__icon--checked {
    border-color: $red;
    background-color: $red;
  }

  /deep/ .van-stepper {
    &__minus {border-radius: 15px 0 0 15px;border-right: 0;}
    &__plus {border-radius: 0 15px 15px 0;border-left: 0;}
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
  display: flex;
  position: fixed;
  bottom: 0;
  font-size: 12px;
  width: 100%;
  padding: 5px 10px;
  background: #fff;
  height: 45px;
  align-items: center;
  z-index: 99;
  /deep/ .van-checkbox__label {
    margin-left: 5px;
  }
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
