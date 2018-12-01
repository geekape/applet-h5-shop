## 前言

一个集**微信公众号商城**/**小程序商城**/**商城后台**的一个开源项目，后台是基于`WeiPHP5.0`开发的，`WeiPHP`是一个简洁而强大的开源微信公众平台开发框架，微信功能插件化开发,多公众号管理,配置简单。

这里主要介绍下**前端方面**（实在是后端的不太懂～），没图没真相，上图（图片有点大），文章结尾有`源码地址`和`公众号商城体验地址`：

![前端页面思维导图](https://user-gold-cdn.xitu.io/2018/11/30/16761e6c27ba41d5?w=1417&h=861&f=png&s=153482)

![前端页面部分UI图](https://user-gold-cdn.xitu.io/2018/11/30/1676342f099eba7e?w=5000&h=3000&f=jpeg&s=704611)

![商城后台部分界面](https://user-gold-cdn.xitu.io/2018/11/30/16764ba36f8437cc?w=1425&h=662&f=png&s=408701)
## 1. 目录结构

开源项目第一层的目录结构：
```
├── LICENSE.txt
├── README.md
├── application
├── build.php
├── composer.json
├── composer.lock
├── config
├── images
├── mpvue // 小程序和公众号商城源码在这
├── public
├── route
├── server.php
├── think
├── thinkphp
├── vendor
└── weiapp_demo
```

以下是**商城前端页面**的三层的目录结构：
``` js
├── wap // 公众号商城（VueCli3脚手架）
│   ├── README.md
│   ├── babel.config.js
│   ├── package-lock.json
│   ├── package.json // 所有的npm包
│   ├── postcss.config.js // px转rem
│   ├── public
│   │   ├── favicon.ico
│   │   └── index.html 
│   ├── src // 源码目录
│   │   ├── App.vue
│   │   ├── assets
│   │   ├── components // 公共组件
│   │   ├── main.js // 公共配置文件
│   │   ├── pages // 所有页面
│   │   ├── router // 页面路由
│   │   ├── store // 全局状态
│   │   └── utils // 一些公用方法
│   ├── static
│   │   ├── img //图片资源
│   │   └── styles // 样式资源，主要是Scss
│   └── vue.config.js // 项目的配置，代理/打包等
└── weiapp // 小程序商城（Mpvue脚手架）
    ├── README.md
    ├── build
    │   ├── build.js
    │   ├── check-versions.js
    │   ├── dev-client.js
    │   ├── dev-server.js
    │   ├── utils.js
    │   ├── vue-loader.conf.js
    │   ├── webpack.base.conf.js
    │   ├── webpack.dev.conf.js
    │   └── webpack.prod.conf.js
    ├── config
    │   ├── dev.env.js
    │   ├── index.js
    │   └── prod.env.js
    ├── dist // 打包的目录
    │   ├── app.js
    │   ├── app.js.map
    │   ├── app.json
    │   ├── app.wxss
    │   ├── common
    │   ├── components
    │   ├── modules
    │   ├── pages
    │   └── static
    ├── index.html
    ├── package-lock.json
    ├── package.json
    ├── project.config.json
    ├── src // 源码目录（以下同wap一样）
    │   ├── App.vue
    │   ├── app.json
    │   ├── common
    │   ├── components
    │   ├── main.js
    │   ├── pages
    │   ├── router
    │   ├── store
    │   └── utils
    ├── static // 一些UI组件和资源
    │   ├── img
    │   ├── iview
    │   ├── styles
    │   ├── vant
    │   └── wxParse // 富文本解析


```
## 2. 技术栈
前端是使用到的技术栈有`Mpvue`,`Vue全家桶`(Vue/VueRouter/Vuex/VueCli3)；后端主要是`WeiPHP`,`ThinkPHP`，`Mysql`等。

- `Mpvue` ：使用Vue开发小程序，方便移植H5
- `VueCli3`：公众号商城的脚手架，和小程序代码大致相同
- `VueRouter`：公众号商城的路由
- `VueX`：商城的全局状态
- `Vant`: 有赞的UI组件库
- `WEUI`：微信小程序的UI组件库
- `Flyio`：兼容小程序和网页端等等的请求库
- `WxParse`：小程序富文件解析库
- ....


## 3. 项目运行和打包
项目是基于`Mpvue`（根目录`mpvue/weiapp`）和`Vue`(根目录`mpvue/wap`)开发的，你必须选安装好NodeJs和npm，建议到NodeJs官网直接下载安装包。


### weiapp(微信小程序）项目
1. 下载整个包之后，进行根目录`mpvue/weiapp`文件夹。
2. 运行`npm install`，如果你安装了cnpm，你就可以使用`cnpm install`
3. 调试项目：运行`npm run dev`命令，打开微信开发者工具，把整个`weiapp`目录选进去，就可以边改边看代码
4. 打包上传项目：使用命令`npm run build`，然后在微信开发者工具右上角点击上传就可以上传开发版本了。

### wap(微信公众号）项目
1. 同上，进入根目录`mpvue/wap`文件夹。
2. 同上，运行`npm install`或`cnpm install`
3. 本地调试：项目采用的是Vue3，所以运行`npm run serve`命令，默认打开`localhost:8080`，就可以直接调试了
4. 打包上传项目：使用命令`npm run build`,默认生成的文件夹是在根目录`public/wap`下,上传打包好的文件夹就可以访问了

##### Tips:
- **本地调试:** 由于是微信公众号项目，要跳转获取用户信息，所以在本地调试的时候，在`wap/src/app.vue`文件中注释掉跳转，并且手动使用`window.localStorage` API 添加`openid`,默认 -3；打开微信开发者工具在小程序项目`Storage`中获取`PHPSSEEID`值。

```
window.localStorage.setItem("PHPSESSID", "xxxxxxxxxxxxxxxxxxxxxxx");
window.localStorage.setItem("openid", -3);
```


## 4. 阅读代码你将收获的知识
- Vue项目本地开发`接口调试`的代理配置
- `Mpvue 转 H5 `需要修改的地方
- Scss 样式文件的分类，`公共Scss样式的配置`
- VueRouter 的基本使用
- Vuex 的简单例子
- `页面适配`的方案（px转rpx/px转rem)
- 小程序UI组件的使用方法
- 组件化开发
- 等等等等....


## 5. 最后
最后说几句，项目经内部多人测试，完全可以用于商用，当然由于环境的不同，开发人员的不同还有很多潜在的问题，如果你有兴趣使用这个开源的项目，可以先看看[weiphp5.0二次开发手册](https://www.kancloud.cn/fanxing/weiphp5)，使用过程中碰到任何的问题，都可以在[在线提交Bug](https://bug.weiphp.cn/index.php?s=/w10/Bug/Wap/invite/project_id/88)，也欢迎加入我们的内测群，一起交流！

线上预览地址，一定要使用微信浏览器打开，不然获取不到信息，不会 
直达：[Github商城源码地址](https://github.com/geekape/applet-h5-shop)

预览：[微信公众号商城地址，使用微信网页打开](https://github.com/geekape/applet-h5-shop)
