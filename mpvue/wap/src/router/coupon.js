
const coupon_center = () => import("@/pages/coupon/center/index.vue") 
const coupon_lists = () => import("@/pages/coupon/lists/index.vue") 
const coupon_get = () => import("@/pages/coupon/get/index.vue") 
const coupon_show = () => import("@/pages/coupon/show/index.vue") 
const dopay = () => import("@/pages/coupon/dopay/index.vue") 

export default [
    {
        path: '/coupon/lists',
        name: 'coupon_lists',
        component: coupon_lists
      },
      {
        path: '/coupon/center',
        name: 'coupon_center',
        component: coupon_center
      },
      {
        path: '/coupon/get/:id',
        name: 'coupon_get',
        component: coupon_get
      },
      {
        path: '/coupon/show',
        name: 'coupon_show',
        component: coupon_show,
        meta: {
          keepAlive: true,
          isBack: false
        }
      },
      {
        path: '/coupon/dopay/:id',
        name: 'dopay',
        component: dopay
      }
]