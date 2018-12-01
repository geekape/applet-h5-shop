<template>
  <div class="msg">
    <div class="weui-msg">
        <div class="weui-msg__icon-area">
            <icon :type="type" size="93"></icon>
        </div>
        <div class="weui-msg__text-area">
            <div class="weui-msg__title">{{msg}}</div></div>
        </div>
        <div class="weui-msg__opr-area">
            <div class="weui-btn-area">
                <button @click="goUrl" class="weui-btn weui-btn_primary" type="primary">返回个人中心</button>
            </div>
        </div>
     
    </div>
  </div>
</template>

<script>

export default {
  data () {
    return {
      msg: '',
      type: 'success'
    }
  },
  computed: {
   
  },

  methods: {
    goUrl() {
      wx.switchTab({url: '../center/main'})
    }
  },

  onLoad () {
    this.msg = this.$root.$mp.query.msg
    this.type = this.$root.$mp.query.type || "success"
   
  }
}
</script>

<style>
page{background: #fff!important}
</style>

