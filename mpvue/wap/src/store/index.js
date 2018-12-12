import Vue from 'vue'
import vuex from 'vuex'
Vue.use(vuex)

export default new vuex.Store({
    state:{
        cartShopNum:0,
        allOrder: [],
        waitPayNum: 0,
        activeOrderParams: ''   // 活动订单参数
    },
    mutations: {
        getCartShopNum (state, opt) {
            state.cartShopNum = opt.num
        },
        // 保存订单
        saveOrder (state, opt) {
            state.allOrder = opt.order
        },
        saveData (state, opt) {
            let key = opt.key
            let value = opt.value
            state[key] = value
            // console.log(`键是：${opt.key},值是${state.activeOrderParams}-----${value}`)
        }

    },
    getters: {
        // 待支付
        waitPay (state) {
            function isPay(item) {
                return item.status_code == 0
            }
            return state.allOrder.filter(isPay)
        },
        // 待收货
        waitCollect (state) {
            function isCollect(item) {
                return item.status_code > 0 && item.status_code < 4 && item.refund == 0
            }
            return state.allOrder.filter(isCollect)
        },
        // 待评价
        waitComment (state) {
            function isComment (item) {
                return item.status_code > 3 && item.status_code != 7 && item.refund == 0
            }
            return state.allOrder.filter(isComment)
        }
    },

    actions: {
    
    }
})