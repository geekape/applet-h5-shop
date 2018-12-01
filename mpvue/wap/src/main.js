import Vue from 'vue'
import App from './App.vue'
import router from "./router"
import store from './store'



// 引入ui库
import 'lib-flexible'
import '../static/styles/weui.min.css'

Vue.config.productionTip = false

var fly = require("flyio")
Vue.prototype.$http = fly

import Vant from 'vant';
import {Lazyload} from 'vant';
import 'vant/lib/index.css';

Vue.use(Vant)
Vue.use(Lazyload, {
    lazyComponent: true,
    preLoad: 1.3
})



Vue.prototype.$store = store

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#app')