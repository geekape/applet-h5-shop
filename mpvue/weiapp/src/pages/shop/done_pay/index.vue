<template>
  <div class="done-pay">
    <div class="m-card">
      <div class="m-card__bd">
        <i class="iconfont icon-success"></i>
        <p class="f-font-sm">已支付</p>
        <p class="u-price"><span class="icon_prize">¥</span>{{meoney}}</p>
      </div>
      <div class="m-card__ft g-flex">
        <div class="g-flex__item f-font-sm">支付方式</div>
        <div class="f-font-sm">微信支付</div>
      </div>
    </div>
    
    <a href="../index/main" open-type="switchTab" class="u-button u-button--primary u-button--big">返回首页</a>
  </div>
</template>

<script>

export default {
  data () {
    return {
      meoney: 0
    }
  },
  computed: {
   
  },

  components: {
    
  },

  methods: {

  },

  onLoad () {
    this.meoney = this.GLOBAL.app.payMoney || 0

  }
}
</script>


<style lang="scss" scoped>
.m-card {
  background: #fff;
  margin: 15px;
  margin-bottom: 0;
}
.m-card__bd {
  text-align: center;
  padding: 30px 0;
}
.m-card__ft {
  height: 45px;
  line-height: 45px;
  padding: 0 15px;
  border-top: 1px solid #eee;
}
.icon-success {
  font-size: 50px;
  color: #09bb07;
}
.done-pay {
  .u-price {font-size: 22px;}
  .icon_prize {font-size: 12px}
}
</style>
