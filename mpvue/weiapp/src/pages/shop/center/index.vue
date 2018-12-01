<template>
  <div class="center">
    <!-- 头部 -->
    <div class="center-hd">
      <img class="center-hd__img" :src='userData.avatarUrl'/>
      <p class="center-hd__name">{{userData.nickName}}</p>
    </div>
     
    <!-- icon区域 -->
    <div class="icon-area">
        <div class="icon-area__hd">
          <a href="../my_order/main?active=0" class="icon-area__item">
            <img src='../../../../static/img/new_icon/center-icon-lg4.png' class="icon-area__img"/>
            <p class="icon-area__txt">全部订单</p>
          </a>
          <a href="../my_order/main?active=1" class="icon-area__item">
            <span class="weui-badge" v-if="waitPayNum>0">{{waitPayNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg1.png' class="icon-area__img"/>
            <p class="icon-area__txt">待支付</p>
          </a>
          <a href="../my_order/main?active=2" class="icon-area__item">
            <span class="weui-badge" v-if="waitCollectNum>0">{{waitCollectNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg2.png' class="icon-area__img"/>
            <p class="icon-area__txt">待收货</p>
          </a>
          <a href="../my_order/main?active=3" class="icon-area__item">
            <span class="weui-badge" v-if="waitCommentNum>0">{{waitCommentNum}}</span>
            <img src='../../../../static/img/new_icon/center-icon-lg3.png' class="icon-area__img"/>
            <p class="icon-area__txt">待评价</p>
          </a>
          
        </div>
        <div class="icon-area__ct">
          <a :href="item.url" class="icon-area__item" v-for="(item,index) in smallIcons" :key="index">
            <img :src='item.img' class="icon-area__img"/>
            <p class="icon-area__txt">{{item.text}}</p>
          </a>
          <button open-type="contact" class="icon-area__item contact-btn">
            <img class="icon-area__img" src="../../../../static/img/new_icon/center-icon-md7.png" alt="">
             <p class="icon-area__txt">联系客服</p>
          </button>
        </div>
    </div> 

 
  </div>
</template>

<script>
import { post, get, login } from "@/utils";
import { mapMutations } from 'vuex'
export default {
  data () {
    return {
      userData: [],
      icons: [
        {
          img: '../../../static/img/new_icon/center-icon-lg1.png',
          text: '待支付',
          url: '../my_order/main?active=1'
        },
        {
          img: '../../../static/img/new_icon/center-icon-lg2.png',
          text: '待收货',
          url: '../my_order/main?active=2'
        },
        {
          img: '../../../static/img/new_icon/center-icon-lg3.png',
          text: '待评价',
          url: '../my_order/main?active=3'
        },
        {
          img: '../../../static/img/new_icon/center-icon-lg4.png',
          text: '全部订单',
          url: '../my_order/main?active=0'
        }
      ],
      smallIcons: [
        {
          img: '../../../static/img/new_icon/center-icon-md1.png',
          text: '优惠劵',
          url: '../coupon/main'
        },
        {
          img: '../../../static/img/new_icon/center-icon-md2.png',
          text: '会员卡',
          url: '#'
        },
        {
          img: '../../../static/img/new_icon/center-icon-md3.png',
          text: '我的收藏',
          url: '../collect/main'
        },
        {
          img: '../../../static/img/new_icon/center-icon-md4.png',
          text: '我的足迹',
          url: '../track/main'
        },
        {
          img: '../../../static/img/new_icon/center-icon-md5.png',
          text: '我的地址',
          url: '../add_address/main'
        },
        {
          img: '../../../static/img/new_icon/center-icon-md6.png',
          text: '我的评价',
          url: '../my_comment/main'
        }

      ]
    }
  },
  methods: {
    async getData () {
      const sessId = wx.getStorageSync("PHPSESSID");
      const data = await get("/shop/api/my_order/PHPSESSID/" + sessId)
      this.$store.commit('saveOrder', {
        order: data.orderList
      })
    }
  },
  computed: {
    waitPayNum () {
      return (this.$store.getters.waitPay).length
    },
    waitCollectNum () {
      return (this.$store.getters.waitCollect).length
    },
    waitCommentNum () {
      return (this.$store.getters.waitComment).length
    }

  },
  onLoad () {
    this.userData = login()
    // 设置购物车数量
    get("shop/api/cart/PHPSESSID/" + wx.getStorageSync("PHPSESSID"))
    .then(res => {
      let num = res.lists.length;
      this.$store.commit("getCartShopNum", {
        num: num
      });
    })
    .catch(err => {
      console.log("失败：" + err);
    });
  },

  onShow () {
    /**
     * 订单逻辑
     * 1. 进入个人中心，请求订单接口，利用vuex存储所有订单
     * 2. 在vuex中利用Getter筛选每个订单类型，并把数量记录在state中
     * 3. 在我的订单页面，使用computed取vuex中各个类型的订单数据
     * 4. 使用commit或才dispatch 显式提交每次更改，以便数据是响应式的
     */
    this.getData()
  }

}
</script>

<style lang="scss" scoped>
  .center-hd {
    background: url(http://shengxun.weiphp.cn/uploads/picture//20181017/5bc6d86a01242.jpg) no-repeat;
    width:100%;
    height:135px;
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
      box-shadow: 0 0 0 5px rgba(255, 255, 255, .2) ;
    }
    &__name {
      color: #fff;
      font-size: 16px;
      margin-top: 13px;
    }

  }

  // icon 
  .icon-area {
    /deep/ .weui-badge {
      padding: 2px 5px;
      font-size: 10px;
      position: absolute;top: 1em;right: 2.5em;
    }

    &__hd {
      display: flex;
      border-bottom: 1px solid $b-color;
      .icon-area__item {
        position: relative;
        flex: 25%;
        padding: 14px 0;
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
