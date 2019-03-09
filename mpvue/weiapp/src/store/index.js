import Vue from 'vue'
import vuex from 'vuex'
Vue.use(vuex)

export default new vuex.Store({
    state:{
        cartShopNum:0,
        allOrder: [],
        waitPayNum: 0,
        activeOrderParams: '',   // 活动订单参数
        interval: 0,
        Interval: 0,
        groupmoreinterval: 0
    },
    mutations: {
        getCartShopNum (state, opt) {
            state.cartShopNum = opt.num
            if(state.cartShopNum > 0) {
                wx.setTabBarBadge({
                    index: 2,
                    text: state.cartShopNum + ''
                })
            } else {
                wx.hideTabBarRedDot({index: 2})
            }
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
                if(item.status_code > 3 && item.status_code != 7 && item.refund == 0) {
                    arr.push(item)
                }
            })
            return arr
        }
    },

    actions: {
    
    }
})
