<template>
  <div class="tabbar">
    <div class="weui-tabbar">
        <router-link to="/" class="weui-tabbar__item" :class="{'weui-bar__item_on': checkedIndex==1}">
            <span style="display: inline-block;position: relative;">
                <img v-if="checkedIndex==1" src="~images/new_icon/icon_home_red.png" alt="" class="weui-tabbar__icon">
                <img v-else src="~images/new_icon/icon_home.png" alt="" class="weui-tabbar__icon">
            </span>
            <p class="weui-tabbar__label">首页</p>
        </router-link>
        <router-link to="/lists"  class="weui-tabbar__item" :class="{'weui-bar__item_on': checkedIndex==2}">
            <img v-if="checkedIndex==2" src="~images/new_icon/icon_goods_red.png" alt="" class="weui-tabbar__icon">
            <img v-else src="~images/new_icon/icon_goods.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">商品</p>
        </router-link>
        <router-link to="/cart"  class="weui-tabbar__item" :class="{'weui-bar__item_on': checkedIndex==3}">
          <span class="weui-badge" style="position: absolute;top: .2em;right: 2em;" v-if="cartLen > 0">{{cartLen}}</span>
            <span style="display: inline-block;position: relative;">
                <img v-if="checkedIndex==3" src="~images/new_icon/icon_cart_red.png" alt="" class="weui-tabbar__icon">
                <img v-else src="~images/new_icon/icon_cart.png" alt="" class="weui-tabbar__icon">
            </span>
            <p class="weui-tabbar__label">购物车</p>
        </router-link>
        <router-link to="/center"  class="weui-tabbar__item" :class="{'weui-bar__item_on': checkedIndex==4}">
            <img v-if="checkedIndex==4" src="~images/new_icon/icon_center_red.png" alt="" class="weui-tabbar__icon">
            <img v-else src="~images/new_icon/icon_center.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">个人中心</p>
        </router-link>
    </div>
  </div>
</template>

<script>
import {post} from '@/utils'
export default {
  props: ['checkedIndex'],
  data() {
    return {};
  },
  computed: {
    cartLen () {
      return this.$store.state.cartShopNum
    }
  }
  
};
</script>

<style lang="scss" scoped>
.tabbar {
  
  .weui-tabbar__item {position: relative;}
  .weui-bar__item_on {
    color: #333;
    .weui-tabbar__label {
      color: $red;
    }
  }
  .weui-tabbar {
    position: fixed;
    bottom: 0;
		left: 0;
  }
}
</style>
