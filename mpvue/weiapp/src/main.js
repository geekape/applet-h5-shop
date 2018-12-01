import Vue from 'vue'
import App from './App'
import store from './store'




// 全局变量
import global_ from '@/utils/global'

import '../static/styles/weui.wxss'

Vue.config.productionTip = false
App.mpType = 'app'

Vue.prototype.$store = store

// 使用flyio http请求库
var Fly = require('flyio/dist/npm/wx')
var fly = new Fly()
Vue.prototype.$http = fly


Vue.prototype.GLOBAL = global_

const app = new Vue(App)
app.$mount()
