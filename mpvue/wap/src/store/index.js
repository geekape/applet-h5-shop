import Vue from 'vue'
import vuex from 'vuex'
Vue.use(vuex)

export default new vuex.Store({
    state:{
        cartShopNum:0,
        allOrder: [],
        waitPayNum: 0
    },
    mutations: {
        getCartShopNum (state, opt) {
            state.cartShopNum = opt.num
        },
        // 保存订单
        saveOrder (state, opt) {
            state.allOrder = opt.order
        }
    },
    getters: {
        // 待支付
        waitPay (state) {
            let arr = []
            state.allOrder.forEach(item => {
                if(item.status_code == 0) {
                    arr.push(item)
                }
            })
            return arr
        },
        // 待收货
        waitCollect (state) {
            let arr = []
            state.allOrder.forEach(item => {
                if(item.status_code > 0 && item.status_code < 4 && item.refund == 0 ) {
                    arr.push(item)
                }
            })
            return arr
        },
        // 待评价
        waitComment (state) {
            let arr = []
            state.allOrder.forEach(item => {
                if(item.status_code > 3 && item.status_code != 7) {
                    arr.push(item)
                }
            })
            return arr
        }
    },

    actions: {
    
    }
})