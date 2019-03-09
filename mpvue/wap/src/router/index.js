
import Vue from "vue";
import VueRouter from "vue-router";

// 商城


const center = () => import("@/pages/shop/center/index.vue") 
const lists = () => import("@/pages/shop/lists/index.vue") 
const cart = () => import("@/pages/shop/cart/index.vue") 
const index = () => import("@/pages/shop/index/index.vue") 
const goodsDetail = () => import("@/pages/shop/goods_detail/index.vue") 
const order = () => import("@/pages/shop/my_order/index.vue") 
const collect = () => import("@/pages/shop/collect/index.vue") 
const track = () => import("@/pages/shop/track/index.vue") 
const address = () => import("@/pages/shop/add_address/index.vue") 
const my_comment = () => import("@/pages/shop/my_comment/index.vue") 
const comment = () => import("@/pages/shop/comment/index.vue") 
const confirm_order = () => import("@/pages/shop/confirm_order/index.vue") 
const done_pay = () => import("@/pages/shop/done_pay/index.vue") 
const order_detail = () => import("@/pages/shop/order_detail/index.vue") 
const logistics = () => import("@/pages/shop/logistics/index.vue") 
const refund = () => import("@/pages/shop/refund/index.vue") 
const msg = () => import("@/pages/shop/msg/index.vue") 
const service = () => import("@/pages/shop/service/index.vue") 
const shop_lists = () => import("@/pages/shop/shop_list/index.vue") 

// 活动
import seckill from './seckill'
import haggle from './haggle'
import collage from './collage'
import coupon from './coupon'
import members from './members'

// 要告诉 vue 使用 vueRouter
Vue.use(VueRouter);

const routes = [
  {
    path: '/lists',
    name: 'lists',
    component: lists,
    meta: { keepAlive: true, isBack: false }
  },
  {
    path: '/service',
    name: 'service',
    component: service
  },
  {
    path: '/center',
    name: 'center',
    component: center,
    meta: { keepAlive: true, isBack: false }
  },
  {
    path: '/cart',
    name: 'cart',
    component: cart,
    meta: { keepAlive: true, isBack: false }
  },
  {
    path: '/',
    name: 'index',
    component: index,
    meta: { keepAlive: true }
  },

  {
    path: '/goods_detail/:id',
    name: 'goods_detail',
    component: goodsDetail,
    meta: { keepAlive: true, isBack: false }
    
  },
  {
    path: '/order',
    name: 'order',
    component: order
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
    component: confirm_order,

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
  },
].concat([...seckill], [...haggle], [...collage], [...coupon], [...members])



var router = new VueRouter({
  // mode: 'history',
  routes
})
router.beforeEach((to, from, next) => {
  // console.log('从:', from)
  // console.log('到:', to)
  next();

  // 指定页面跳转才重载数据
  if (from.name == "coupon_lists") {
    to.meta.isBack = true
  }
})

export default router;