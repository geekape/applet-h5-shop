import Vue from "vue";
import VueRouter from "vue-router";

// 引入组件
import center from "@/pages/shop/center/index.vue";
import lists from "@/pages/shop/lists/index.vue";
import cart from "@/pages/shop/cart/index.vue";
import index from "@/pages/shop/index/index.vue";
import goodsDetail from "@/pages/shop/goods_detail/index.vue";
import order from "@/pages/shop/my_order/index.vue";
import coupon from "@/pages/shop/coupon/index.vue";
import collect from "@/pages/shop/collect/index.vue";
import track from "@/pages/shop/track/index.vue";
import address from "@/pages/shop/add_address/index.vue";
import my_comment from "@/pages/shop/my_comment/index.vue";
import comment from "@/pages/shop/comment/index.vue";
import confirm_order from "@/pages/shop/confirm_order/index.vue";
import done_pay from "@/pages/shop/done_pay/index.vue";
import order_detail from "@/pages/shop/order_detail/index.vue";
import logistics from "@/pages/shop/logistics/index.vue";
import refund from "@/pages/shop/refund/index.vue";
import msg from "@/pages/shop/msg/index.vue";
import service from "@/pages/shop/service/index.vue";
import shop_lists from "@/pages/shop/shop_list/index.vue";



// 要告诉 vue 使用 vueRouter
Vue.use(VueRouter);

const routes = [
  {
    path: '/lists',
    name: 'lists',
    component: lists
  },
  {
    path: '/service',
    name: 'service',
    component: service
  },
  {
    path: '/center',
    name: 'center',
    component: center
  },
  {
    path: '/cart',
    name: 'cart',
    component: cart
  },
  {
    path: '/',
    name: 'index',
    component: index,
  },

  {
    path: '/goods_detail/:id',
    name: 'goods_detail',
    component: goodsDetail
  },
  {
    path: '/order',
    name: 'order',
    component: order
  },
  {
    path: '/coupon',
    name: 'coupon',
    component: coupon
  },
  {
    path: '/collect',
    name: 'collect',
    component: collect
  },
  {
    path: '/track',
    name: 'track',
    component: track
  },
  {
    path: '/address',
    name: 'address',
    component: address
  },
  {
    path: '/my_comment',
    name: 'my_comment',
    component: my_comment
  },
  {
    path: '/comment',
    name: 'comment',
    component: comment
  },
  {
    path: '/confirm_order/:id',
    name: 'confirm_order',
    component: confirm_order
  },
  {
    path: '/order_detail/:id',
    name: 'order_detail',
    component: order_detail
  },
  {
    path: '/refund/:id',
    name: 'refund',
    component: refund
  },
  {
    path: '/logistics/:id',
    name: 'logistics',
    component: logistics
  },
  {
    path: '/msg',
    name: 'msg',
    component: msg
  },
  {
    path: '/done_pay/:id',
    name: 'done_pay',
    component: done_pay
  },
  {
    path: '/shop_lists/:id',
    name: 'shop_lists',
    component: shop_lists
  }

]

var router = new VueRouter({
  // mode: 'history',
  routes
})
export default router;