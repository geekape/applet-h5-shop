<template>
  <div class="center">
    <!-- 头部 -->
    <div class="center-hd">
      <img lazy-load class="center-hd__img" :src='userData.avatarUrl'/>
      <p class="center-hd__name">{{userData.nickName}}</p>
    </div>
     
    <!-- icon区域 -->
    <div class="icon-area">
        <div class="icon-area__hd">
          <a href="../my_order/index?active=0" class="icon-area__item">
            <img lazy-load :src="imgRoot+'new_icon/center-icon-lg4.png'" class="icon-area__img"/>
            <p class="icon-area__txt">全部订单</p>
          </a>
          <a href="../my_order/index?active=1" class="icon-area__item">
            <span class="weui-badge" v-if="waitPayNum>0">{{waitPayNum}}</span>
            <img lazy-load :src="imgRoot+'new_icon/center-icon-lg1.png'" class="icon-area__img"/>
            <p class="icon-area__txt">待支付</p>
          </a>
          <a href="../my_order/index?active=2" class="icon-area__item">
            <span class="weui-badge" v-if="waitCollectNum>0">{{waitCollectNum}}</span>
            <img lazy-load :src="imgRoot+'new_icon/center-icon-lg2.png'" class="icon-area__img"/>
            <p class="icon-area__txt">待收货</p>
          </a>
          <a href="../my_order/index?active=3" class="icon-area__item">
            <span class="weui-badge" v-if="waitCommentNum>0">{{waitCommentNum}}</span>
            <img lazy-load :src="imgRoot+'new_icon/center-icon-lg3.png'" class="icon-area__img"/>
            <p class="icon-area__txt">待评价</p>
          </a>
          
        </div>
        <div class="icon-area__ct">
          <a :href="item.url" class="icon-area__item" v-for="(item,index) in smallIcons" :key="index">
            <img lazy-load :src='item.img' class="icon-area__img"/>
            <p class="icon-area__txt">{{item.text}}</p>
          </a>
          <button open-type="contact" class="icon-area__item contact-btn">
            <img lazy-load class="icon-area__img" :src="imgRoot+'new_icon/center-icon-md7.png'" alt="">
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
      userData: {},
			imgRoot: this.imgRoot,
      icons: [
        {
          img: this.imgRoot+'new_icon/center-icon-lg1.png',
          text: '待支付',
          url: '../my_order/index?active=1'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-lg2.png',
          text: '待收货',
          url: '../my_order/index?active=2'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-lg3.png',
          text: '待评价',
          url: '../my_order/index?active=3'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-lg4.png',
          text: '全部订单',
          url: '../my_order/index?active=0'
        }
      ],
      smallIcons: [
        {
          img: this.imgRoot+'new_icon/center-icon-md1.png',
          text: '优惠劵',
          url: '../../coupon/lists/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md2.png',
          text: '会员卡',
          url: '../../members/index/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md3.png',
          text: '我的收藏',
          url: '../collect/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md4.png',
          text: '我的足迹',
          url: '../track/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md5.png',
          text: '我的地址',
          url: '../add_address/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md6.png',
          text: '我的评价',
          url: '../my_comment/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md8.png',
          text: '我的拼团',
          url: '../../collage/lists/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md9.png',
          text: '我的秒杀',
          url: '../../seckill/lists/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md10.png',
          text: '我的砍价',
          url: '../../haggle/lists/index'
        },
        {
          img: this.imgRoot+'new_icon/center-icon-md11.png',
          text: '领卷中心',
          url: '../../coupon/center/index'
        }
      ]
    }
  },
  methods: {
    getData () {
      const _this = this
      const sessId = wx.getStorageSync("PHPSESSID");
      get("shop/api/my_order/PHPSESSID/" + sessId).then(res => {
        _this.$store.commit('saveOrder', {
          order: res.orderList
        })
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
    this.userData = wx.getStorageSync('userInfo')
  },

  onShow () {
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
      /deep/ button {line-height: 1.5;}
    }

  }



</style>
