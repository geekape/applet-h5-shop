<template>
  <div class="login">
    <img lazy-load :src="imgRoot+'head.png'" class="login-logo">
    <p class="login-name">圆梦云商城</p>

    <p class="login-hint__tt">圆梦云商城 - 品质生活，极速到家</p>

    <!-- <div class="login-hint">
      <div class="login-hint__item">获得你的公开信息（昵称、头像等）</div>
    </div>-->
    <button
      open-type="getUserInfo"
      @getuserinfo="doLogin"
      class="u-button u-button--primary u-button--big"
    >微信登录</button>
  </div>
</template>

<script>
import {post, get} from "@/utils"
export default {
  components: {

  },

  data () {
    return {
      logs: [],
			imgRoot: this.imgRoot
    }
  },
  methods: {
    doLogin(e) {
      const _this = this
      //登录
      wx.getUserInfo({
      success: function (res) {
        wx.setStorageSync('userInfo',e.mp.detail.userInfo)
        _this.saveUserInfo(res)
        wx.switchTab({
          url: '/pages/shop/index/index'
        })
      },
      fail: function (res) {
        console.log('失败了')
      }
    })
    },

    saveUserInfo (opt) {
      console.log(opt)
      post('weiapp/api/saveUserInfo', {
        iv: opt.iv,
        encryptedData: opt.encryptedData,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then(res => {
         console.log('success')
          wx.switchTab({ url: '/pages/shop/index/index' })
      })
      
    }
  },

  created () {
  },

  onLoad() {
  }
}
</script>
<style>
page {background: #fff!important}
</style>

<style lang="scss" scoped>
.login {
  /deep/ .hint-page {height: 60vh}
  display: flex;
  flex-direction: column;
  padding-top: 60px;
  justify-content: center;
  align-items: center;
  image {
    width:80px;
    height:80px;
    background:#f9f9f9;
    border-radius:50%;
    margin-bottom:10px;
  }
  &-name {
    margin-bottom: 10px;
    width:80%;
    text-align:center;
    font-size:20px;
  }
  &-hint__tt {
    font-size: 14px;
  }
  /deep/ .u-button {
    position: fixed;
    bottom: 60px;
    border-radius: 5px;
    background: $red;
    left: 30px;
    right: 30px;
    width: auto;
  }
}


</style>
