
const get_card = () => import("@/pages/members/get_card/index.vue")
const write_info = () => import("@/pages/members/write_info/index.vue")
const index = () => import("@/pages/members/index/index.vue")
const gift = () => import("@/pages/members/gift/index.vue") //会员有礼
const  score_exchange = () => import("@/pages/members/score_exchange/index.vue") //积分兑换
const  signin = () => import("@/pages/members/signin/index.vue") //签到
const  inform = () => import("@/pages/members/inform/index.vue") //通知
const  perfect_info = () => import("@/pages/members/perfect_info/index.vue") //完善资料


export default [
    {
        path: '/members/get_card',
        component: get_card,
        meta: { keepAlive: true }
    },
    {
        path: '/members/write_info',
        component: write_info
    },
    {
        path: '/members/gift',
        component: gift
    },
    {
        path: '/members/index',
        component: index
    },
    {
        path: '/members/score_exchange',
        component: score_exchange
    },

    {
        path: '/members/signin',
        component: signin,
		meta: { keepAlive: true }
    },

    {
        path: '/members/inform',
        component: inform
    },
    {
        path: '/members/perfect_info',
        component: perfect_info
    },
]