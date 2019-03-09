<template>
  <div class="active_goods" v-if="goodslists.length > 0">
    <div class="goods"  v-for="(goodslist ,index) in goodslists" :key="index">
      <span v-if="active.name === '拼团'" class="collage_tag">{{eventinfo.member_limit}}人团</span>
      <div class="active_img">
        <img class="good_img" v-bind:src='goodslist.cover'/>
      </div>
      <!-- 时间 -->
      <!--<i-count-down class="active_time hide" target="eventinfo.end_time" format="myformat" clear-timer="false">-->
      <!--</i-count-down>-->
      <div class="active_time" v-if="cd.endtime > cd.nowtime && cd.nowtime > cd.starttime">剩余时间:{{cd.day}}天{{cd.hour}}小时{{cd.min}}分钟{{cd.sec}}秒</div>
      <div class="active_time active_timeout" v-else-if="cd.nowtime > cd.endtime">活动已结束</div>
      <div class="active_time active_not_begin" v-else>活动未开始</div>
      <!--商品信息-->
      <div class="active_good_detail">
        <div class="good_detail">
          <p class="title">{{goodslist.title}}</p>
          <p class="red active_price">
            <span class="price-icon">¥</span><strong>{{goodslist.sale_price}}</strong>
            <small class="tag">{{active.name}}</small>
          </p>
          <p class="market_price">
            <del><span class="prize-icon">¥</span>{{goodslist.market_price}}</del>
          </p>
        </div>
        <!--活动按钮-->
        <router-link class="active-btn" :to="active.url + goodslist.id" >{{active.name}}</router-link>
        <!--<a class="active-btn" :href="active.url + goodslist.id">{{active.name}}</a>-->
      </div>
    </div>

  </div>
  <div class="active_null" v-else>
    <span>活动商品为空</span>
  </div>
</template>

<script>
  export default {
    props: {
      cd: {},
      goodslists: [],
      active: {},
      eventinfo: {},
      myformat: []
    },
    components: {
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>

</style>
