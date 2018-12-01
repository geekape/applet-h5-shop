<template>
  <div class="my-order">

    <!-- <div class="navbar">
      <p class="navbar-icon_left iconfont">&#xe697;</p>
      <p class="navbar-title overflow-dot_row">商品详情</p>
    </div> -->
    <navbar text="我的订单"></navbar>

    <van-tabs v-model="tabActive" @change="toggleTab" border="false" sticky swipeable>
      <van-tab title="全部">
        <div class="order-item" v-for="(order, index) in orders" :key="index">
          <router-link :to="'/order_detail/'+order.id" class="order-item__hd" v-for="(goods, idx) in order.goods_datas" :key="goods.id">
            <img class="u-goods__img" :src="goods.cover"/>
            
            <div class="order-item__right g-flex g-flex__column g-flex__space">
              <p class="u-goods__tt overflow-dot">{{goods.title}}</p>
              <div class="g-flex">
                <p class="order-item__price g-flex__item">¥{{goods.sale_price}}</p>
                <p class="order-item__num">x{{goods.num}}</p>
              </div>
            </div>
          </router-link>
          <div class="order-item__ft" v-if="order.pay_status == 0 && order.refund == 0 || order.status_code > 0 && order.status_code < 4 && order.refund == 0 || order.refund == 0 && order.status_code == 3|| order.refund == 0 && order.pay_status == 1 && order.status_code >3 && order.status_code != 7">
            <button @click="goPay(order.id, order.total_price)" v-if="order.pay_status == 0" class="u-button u-button--border">去支付</button>

            <router-link :to="'/logistics/' + order.id" v-if="order.status_code > 0 && order.status_code < 4" class="u-button u-button--border">查看物流</router-link>

            <button  v-if="order.status_code == 3" @click="goReceiving(order.id)" class="u-button u-button--primary">确认收货</button>

            <button @click="goComment(order)" v-if="order.pay_status == 1 && order.status_code >3 && order.status_code != 7" class="u-button u-button--primary">去评价</button>

          </div>
          <div class="order-item__ft" v-else>
            <button v-if="order.refund > 0" class="u-button u-button--disable">{{order.refund_title}}</button>
            <button class="u-button u-button--disable" v-else>已完成</button>
          </div>
        </div>

      </van-tab>
      <van-tab title="待支付">
        <div class="order-item" v-for="(Pay, index) in waitPay" :key="index">
          <router-link :to="'/order_detail/'+Pay.id" class="order-item__hd" v-for="(goods, idx) in Pay.goods_datas" :key="goods.id">
            <img class="u-goods__img" :src="goods.cover"/>
            
            <div class="order-item__right g-flex g-flex__column g-flex__space">
              <p class="u-goods__tt overflow-dot">{{goods.title}}</p>
              <div class="g-flex">
                <p class="order-item__price g-flex__item">¥{{goods.sale_price}}</p>
                <p class="order-item__num">x{{goods.num}}</p>
              </div>
            </div>
          </router-link>
          <div class="order-item__ft">
            <button @click="goPay(Pay.id, Pay.total_price)" v-if="Pay.pay_status == 0" class="u-button u-button--border" href=''>去支付</button>
          </div>

        </div>

      </van-tab>
      <van-tab title="待收货">
        <div class="order-item" v-for="(collect, index) in waitCollect" :key="index" v-if="waitCollect">
          <router-link :to="'/order_detail/'+collect.id" class="order-item__hd" v-for="(goods, idx) in collect.goods_datas" :key="goods.id">
            <img class="u-goods__img" :src="goods.cover"/>
            
            <div class="order-item__right g-flex g-flex__column g-flex__space">
              <p class="u-goods__tt overflow-dot">{{goods.title}}</p>
              <div class="g-flex">
                <p class="order-item__price g-flex__item">¥{{goods.sale_price}}</p>
                <p class="order-item__num">x{{goods.num}}</p>
              </div>
            </div>
          </router-link>
          <div class="order-item__ft">
            
            <router-link :to="'/logistics/' + collect.id" v-if="collect.status_code > 0 && collect.status_code < 4" class="u-button u-button--border">查看物流</router-link>

            <button class="u-button u-button--primary" v-if="collect.refund == 0 && collect.status_code == 3" @click="goReceiving(collect.id)">确认收货</button>

          </div>

        </div>
      </van-tab>
      <van-tab title="待评价">
        <div class="order-item" v-for="(comment, index) in waitComment" :key="index">
          <router-link :to="'/order_detail/'+comment.id" class="order-item__hd" v-for="(goods, idx) in comment.goods_datas" :key="goods.id">
            <img class="u-goods__img" :src="goods.cover"/>
            
            <div class="order-item__right g-flex g-flex__column g-flex__space">
              <p class="u-goods__tt overflow-dot">{{goods.title}}</p>
              <div class="g-flex">
                <p class="order-item__price g-flex__item">¥{{goods.sale_price}}</p>
                <p class="order-item__num">x{{goods.num}}</p>
              </div>
            </div>
          </router-link>
          <div class="order-item__ft">
            <button v-if="comment.refund == 0 && comment.pay_status == 1 && comment.status_code >3 && comment.status_code != 7" class="u-button u-button--primary" @click="goComment(comment)">去评价</button>
            
          </div>
        </div>
      </van-tab>
    </van-tabs>
  </div>
</template>

<script>
import { post, get, host, goPay, goReceiving } from "@/utils";
import { Toast, Dialog, Tab, Tabs } from "vant";

import navbar from "@/components/navbar";
export default {
  data() {
    return {
      tabActive: 0 // 索引-页面处于第几个tab
    };
  },
  computed: {
    orders() {
      return this.$store.state.allOrder;
    },
    waitPay() {
      return this.$store.getters.waitPay;
    },
    waitCollect() {
      return this.$store.getters.waitCollect;
    },
    waitComment() {
      return this.$store.getters.waitComment;
    }
  },
  components: {
    navbar
  },

  methods: {
    // 评价/单多商品
    goComment(opt) {
      let allGoods = opt.goods_datas;
      let allId = [];
      allGoods.forEach((item, index) => {
        allId.push(item.id);
      });
      this.$router.push({
        name: "comment",
        params: { order_id: opt.id, goods_id: allId }
      });
    },
    // 支付
    goPay(id, price) {
      goPay(id, price)
    },
    // 收货
    goReceiving(id) {
      goReceiving(id)
    },

    toggleTab(e) {
      console.log(e)
      this.tabActive = e;
    }
  },

  created() {
    // 切换到相应索引
    this.tabActive = this.$route.params.index || 0;
  }
};
</script>

<style lang="scss" scoped>
.my-order {
  padding-top: 45px;
  .u-goods__img {
    margin-right: 10px;
  }

  // tab
  /deep/ .van-tab {
    color: $gray;
  }
  /deep/ .van-tab--active {
    color: $black;
  }
  /deep/ .van-tabs__line {
    background-color: $red;
  }
  /deep/ .van-tabs--line {
    padding-top: 0;
  }
  /deep/ .van-tabs__wrap {
    position: sticky;
    top: 0;
  }
}

.order-item {
  background: #fff;
  margin-top: $box-size;

  // 按钮
  .u-button {
    margin-left: 10px;
  }

  &__hd {
    display: flex;
    padding: 10px;
  }

  &__right {
    width: 100%;
  }
  &__ft {
    padding: 10px;
    text-align: right;
    border-top: 1px solid #ececec;

    // 按钮
    /deep/ .van-button {
      height: 35px;
      line-height: 34px;
      border-radius: 30px;
    }
  }

  &__tt {
    margin-bottom: 10px;
  }
  &__num {
    font-size: 12px;
    color: $gray;
  }
  &__price {
    font-size: 14px;
  }
  &__space {
    font-size: 14px;
  }
}
</style>
