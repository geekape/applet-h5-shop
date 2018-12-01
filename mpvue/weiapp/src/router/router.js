// 首个路由为首页
module.exports = [{
  path: 'pages/shop/index/main',
  name: 'Index',
  config: {
      navigationBarTitleText: '首页',
　　　　//引入UI组件，后面会讲到
      usingComponents:{
          
      }
  }
}, {
  path: 'pages/shop/list/main',
  name: 'List',
  config: {
      navigationBarTitleText: 'list详情'
  }
}]
