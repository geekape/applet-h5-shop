<template>
  <div class="service">
    <navbar text="客服"></navbar>

    

    <p class="login-hint__tt">请在公众号内发送消息</p>

    <button class="u-button u-button--primary u-button--big" @click="exit">去公众号发送消息</button>
  </div>
</template>

<script>
import navbar from "@/components/navbar";
const wx = require("weixin-js-sdk");
export default {
  data() {
    return {
      msg: []
    };
  },
  components: {
    navbar
  },
  methods: {
    exit () {
      wx.closeWindow();
    }
  },

  created() {}
};
</script>

<style lang="scss" scoped>
.service {
  height: 100vh;
  background: #fff;
  /deep/ .hint-page {height: 60vh}
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  img {
    width:80px;
    height:80px;
    background:#f9f9f9;
    border-radius:50%;
    margin-bottom:10px;
  }
  .login-name {
    margin-bottom: 10px;
    width:80%;
    text-align:center;
    font-size:20px;
  }
  .login-hint__tt {
    font-size: 14px;
  }
  /deep/ .u-button {
    position: fixed;
    bottom: 60px;
    border-radius: 5px;
    background: $red;
    left: 0;
    right: 30px;
    width: 90%;
  }
}

</style>
