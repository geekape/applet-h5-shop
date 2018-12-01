<template>
  <div class="center">
    <!-- 头部 -->
    <div class="center-hd" v-if="userData">
      <img class="center-hd__img" :src='userData.headimgurl'/>
      <p class="center-hd__name">{{userData.nickname}}</p>
    </div>
    <div class="center-hd" v-else>
      <img class="center-hd__img"/>
      <p class="center-hd__name">微信用户</p>
    </div>
     
    <!-- icon区域 -->
    <div class="icon-area">
        <div class="icon-area__hd">
          <router-link :to="{name: 'order', params: {index: 0}}" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-lg4.png' class="icon-area__img"/>
            <p class="icon-area__txt">全部订单</p>
          </router-link>
          <router-link :to="{name: 'order', params: {index: 1}}" class="icon-area__item">
          <span class="weui-badge" v-if="waitPayNum>0">{{waitPayNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg1.png' class="icon-area__img"/>
            <p class="icon-area__txt">待支付</p>
          </router-link>
          <router-link :to="{name: 'order', params: {index: 2}}" class="icon-area__item">
          <span class="weui-badge" v-if="waitCollectNum>0">{{waitCollectNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg2.png' class="icon-area__img"/>
            <p class="icon-area__txt">待收货</p>
          </router-link>
          <router-link :to="{name: 'order', params: {index: 3}}" class="icon-area__item">
          <span class="weui-badge" v-if="waitCommentNum>0">{{waitCommentNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg3.png' class="icon-area__img"/>
            <p class="icon-area__txt">待评价</p>
          </router-link>
        </div>
        <div class="icon-area__ct">
          <router-link to="/coupon" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md1.png' class="icon-area__img"/>
            <p class="icon-area__txt">优惠劵</p>
          </router-link>
          <router-link to="" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md2.png' class="icon-area__img"/>
            <p class="icon-area__txt">会员卡</p>
          </router-link>
          <router-link to="/collect" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md3.png' class="icon-area__img"/>
            <p class="icon-area__txt">我的收藏</p>
          </router-link>
          <router-link to="/track" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md4.png' class="icon-area__img"/>
            <p class="icon-area__txt">我的足迹</p>
          </router-link>
          <router-link to="/address" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md5.png' class="icon-area__img"/>
            <p class="icon-area__txt">我的地址</p>
          </router-link>
          <router-link to="/my_comment" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-md6.png' class="icon-area__img"/>
            <p class="icon-area__txt">我的评价</p>
          </router-link>
          <router-link to="/service" class="icon-area__item contact-btn">
            <img class="icon-area__img" src="../../../../static/img/new_icon/center-icon-md7.png" alt="">
             <p class="icon-area__txt">联系客服</p>
          </router-link>
        </div>
    </div> 
    <tabbar :checkedIndex="4"></tabbar>
  </div>
</template>

<script>
import {get, post, wx} from '@/utils'
import tabbar from "@/components/tabbar";
export default {
  data() {
    return {
      userData: [],

      smallIcons: [
        {
          img: "../../../../static/img/new_icon/center-icon-md1.png",
          text: "代金劵",
          url: "../coupon/main"
        },
        {
          img: "../../../../static/img/new_icon/center-icon-md2.png",
          text: "会员卡",
          url: "#"
        },
        {
          img: "../../../../static/img/new_icon/center-icon-md3.png",
          text: "我的收藏",
          url: "../collect/main"
        },
        {
          img: "../../../../static/img/new_icon/center-icon-md4.png",
          text: "我的足迹",
          url: "../track/main"
        },
        {
          img: "../../../../static/img/new_icon/center-icon-md5.png",
          text: "我的地址",
          url: "../my_address/main"
        },
        {
          img: "../../../../static/img/new_icon/center-icon-md6.png",
          text: "我的评价",
          url: "../my_comment/main"
        }
      ]
    };
  },
  components: {
    tabbar
  },
  methods: {
    async getData() {
      const sessId = window.localStorage.getItem("PHPSESSID");
      const data = await get("/shop/api/my_order/PHPSESSID/" + sessId);
      this.$store.commit("saveOrder", {
        order: data.orderList
      })
    }
  },
  computed: {
    waitPayNum() {
      return this.$store.getters.waitPay.length;
    },
    waitCollectNum() {
      return this.$store.getters.waitCollect.length;
    },
    waitCommentNum() {
      return this.$store.getters.waitComment.length;
    }
  },
  created() {
    this.getData()
    this.userData = JSON.parse(window.localStorage.getItem('userInfo'))
  }
};
</script>

<style lang="scss" scoped>
.center-hd {
  background: url(~images/center-bg.jpg)
    no-repeat;
  width: 100%;
  height: 135px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  &__img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: #eee;
    display: inline-block;
    box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.2);
  }
  &__name {
    color: #fff;
    font-size: 16px;
    margin-top: 13px;
  }
}

// icon
.icon-area {
  .weui-badge {
    position: absolute;
    right: 20%;
  }
  &__hd {
    display: flex;
    border-bottom: 1px solid $b-color;
    .icon-area__item {
      flex: 25%;
      padding: 14px 0;
      position: relative;
    }
    .icon-area__img {
      width: 33px;
      height: 33px;
      margin: 0 auto;
      display: block;
    }
  }
  &__txt {
    text-align: center;
    font-size: 14px;
    margin-top: 5px;
  }

  /* 小icon */
  &__ct {
    display: flex;
    flex-wrap: wrap;

    .icon-area__item {
      flex: 33.3%;
      max-width: 33.3%;
      padding: 25px 20px;
      box-sizing: border-box;
      text-align: center;
      margin: 0;
      border-radius: 0;
    }
    .icon-area__img {
      width: 27px;
      height: 27px;
      margin: 0 auto;
      display: inline-block;
    }
  }
}
</style>
