<template>
  <div class="payment">
    <div>
      <div class="pay_card">
        <div class="card_content">
          <i class="iconfont icon-success"></i>
          <span>已支付</span><br>
          <span>¥999</span>
        </div>
        <div class="card_footer">
          <span>支付方式</span><span class="pay_method">微信支付</span>
        </div>
      </div>
    </div>

    <div class="back_to_main">
      <a :href="activeurl">返回首页</a>
    </div>
  </div>
</template>
<script>
  export default {
    props: ['activeurl']
  }
</script>
<style>
  .pay_card{width:90%;margin:10px 5%;background:#ddd;border-radius:8px;}
  .card_content{width:100%;height:100px;padding: 30px 0;font-size:14px;text-align: center;border-bottom:1px solid #ddd}
  .iconfont.icon-success{font-family:"iconfont" !important;font-size:30px;color:#ff0204;font-style:normal;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;margin-top:20px;}
  /*.pay_img{width:40px;height:40px;}*/
  .card_footer{height:20px;padding: 5px ;font-size: 16px;}
  .pay_method{float:right;}
  .back_to_main{width:90%;margin:10px 5%;padding:5px 0;background:#ff0204;color:white;border-radius:4px;text-align: center;}
</style>
