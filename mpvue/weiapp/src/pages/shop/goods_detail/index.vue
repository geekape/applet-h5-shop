<template>
  <div class="goods-detail" v-if="goods">


    <!-- 商品图 -->
    <div class="slide">
      <swiper class="swiper" :indicator-dots="false" autoplay @change="toggleSwiper">
          <swiper-item v-for="(item, index) in slides" :key="index" @click="pvwImg(item)">
              <a class="slide-url">
                <img :src="item" class="slide-image" mode="aspectFill"/>
              </a>
          </swiper-item>
      </swiper>
      <!-- 计数 -->
      <p class="slide-count">{{selfSwiperNum}}/<span class="s-gray">{{totalSwiperNum}}</span></p>
    </div>

    <!-- 商品信息 -->
    <div class="goods-detail__info">
      <p class="goods-detail__price s-red"><span class="icon-price">¥</span>{{goods.sale_price}}</p>
      <p class="goods__price-cost" v-if="goods.market_price>goods.sale_price">¥{{goods.market_price}}</p>
      <p class="s-gray goods-detail__stock">库存{{goods.stock_active}}件</p>
      <p class="goods-detail__tt">{{goods.title}}</p>
    </div>

    <a @click="jump(goods.id, goods.tab)" class="m-list link g-flex" v-if="goods.tab">
      <div class="m-list__l g-flex__item">同款</div>
      <i class="iconfont icon-fanhui right"></i>
    </a>

    <!-- switch卡片 -->
    <div class="switch-card" v-if="goods.goods_param">
      <div class="switch-card__hd">
        <p class="switch-card__tt">产品参数</p>
        <p class="switch-card__icon iconfont icon-fanhui" :class="arrowDir" @click="toggleArrow"></p>
      </div>
      <div class="switch-card__bd" v-show="arrowDir == 'top'">
        <div class="switch-card__item" v-for="(param, paramIdx) in goods.goods_param" :key="paramIdx">
          <p class="switch-card__param overflow-dot_row">{{param.title}}</p>
          <p class="switch-card__attr">{{param.param_value}}</p>
        </div>
      </div>
    </div>

    <!-- 评价 -->
    <div class="goods-comment" v-if="goods.comment_count>0">
       <div class="m-list link">
        <div class="m-list__l">评价</div>
        <p class="m-list__c s-black" v-show="goods.comment_count>10">查看更多</p>
        <i class="iconfont icon-fanhui right" v-show="goods.comment_count>10"></i>
      </div>

      <scroll-view class="goods-comment__bd" scroll-x="true">
        <div class="goods-comment__item" v-for="(comment, commentIdx) in goods.comments" :key="commentIdx">
          <div class="goods-comment__left">
            <div class="g-flex g-flex__updown-center">
              <img class="u-head__img" />
              <p class="goods-comment__name">{{comment.username}}</p>
            </div>
            <p class="goods-comment__text">{{comment.content}}</p>
          </div>

          <div class="goods-comment__right">
            <img class="u-goods__img" :src="slides[0]"/>
          </div>
        </div>
      </scroll-view>

    </div>

    <!-- 详情图片 -->
    <div class="goods-detail__pic">
      <!-- <rich-text :nodes="detailPic" type="text"></rich-text> -->
      <wxParse :content="detailPic" @preview="preview" @navigate="navigate" />
    </div>
    

    
    <!-- 底部栏 -->
    <div class="bottom-bar">
      <button open-type="contact" class="bottom-bar__service">
        <div class="bottom-bar__icon"></div>
        <p class="bottom-bar__tt">客服</p>
      </button>
      <div class="bottom-bar__collect" @click="toggleCollect">
        <div class="bottom-bar__icon" v-show="!isCollect"></div>
        <div class="bottom-bar__icon--active" v-show="isCollect"></div>
        <p class="bottom-bar__tt">收藏</p>
      </div>
      <a href="../cart/main" open-type="switchTab" class="bottom-bar__cart">
        
        <div class="bottom-bar__icon"><span v-if="cartNum>0" class="weui-badge" style="position: absolute;top: -.2em;right: -.4em;">{{cartNum}}</span></div>
        <p class="bottom-bar__tt">购物车</p>
      </a>

      <button class="u-button u-button--border" @click="addCart">加入购物车</button>
      <button @click="buy" class="u-button u-button--primary">立即购买</button>

    </div>
    <van-toast id="van-toast" />
  </div>
  
</template>

<script>
import {post,host} from '@/utils'
import Toast from "@/../static/vant/toast/toast";
import wxParse from 'mpvue-wxparse'

export default {
  data () {
    return {
      slides: [],
      goods: [],
      isCollect: false,
      arrowDir: 'top',
      selfSwiperNum: 1,
      isCartDot: false,
      detailPic: ''
    }
  },
  components: {
    wxParse
  },
  computed: {
    totalSwiperNum () {
      return this.slides.length
    },
    cartNum () {
      let num = this.$store.state.cartShopNum
      if(num > 0) {
        this.isCartDot = true
        return num
      }
    }
  
  },

  methods: {
    // 跳转同款
    jump (id, tab) {
      this.GLOBAL.app.id = id
      this.GLOBAL.app.pid = tab
      this.GLOBAL.app.listsType = 2
      wx.switchTab({
        url: '/pages/shop/lists/main'
      })
    },
    // 购买
    buy () {
      let goodsId = this.goods.goods_id
      // 库存为0
      if(this.goods.stock_active == 0) {
        Toast('该商品已经被抢光了')
        return false
      }
      
      wx.navigateTo({
        url: '../confirm_order/main?goodsId=' + goodsId
      })
    },
    // 切换箭头方向
    toggleArrow () {
      this.arrowDir == 'top' ? this.arrowDir = 'bottom' : this.arrowDir = 'top'
    },
    // 切换轮播
    toggleSwiper (e) {
      this.selfSwiperNum = e.target.current + 1
    },
    addCart () {
      var _this = this
      
      this.$http.post(host + 'shop/api/addToCart', {
        goods_id: this.goods.id,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then((res) => {
        if (res.data > 0) {
          Toast('加入购物车成功')
        _this.$store.commit('getCartShopNum', {
          num: res.data
        })

        } else {
          Toast('加入购物车失败,请直接下单购买')
         
        }
      })
    },
    pvwImg (url) {
      // 预览图片
      const _this = this
      wx.previewImage({
        current: url, // 当前显示图片的http链接  
        urls: _this.slides // 需要预览的图片http链接列表  
      })
    },
    toggleCollect(showHint) {
      // 收藏
      this.$http.post(host + 'shop/api/addtocollect', {
        goods_id: this.goods.id,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then((res) => {
        if (res.data == 1) {
          Toast('加入收藏成功')
          
          this.isCollect = true

        } else {
          Toast('取消收藏成功')
         
          this.isCollect = false
        }
        
      })
    },


  },

  onLoad () {
    Object.assign(this, this.$options.data());
    // 清空活动信息
    this.$store.commit("saveData", {key: "activeOrderParams",value: "" });
    
    const _this = this
    const id = this.$root.$mp.query.id
    post('shop/api/goods_detail', {
      id: id,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then((res) => {
      // 商品图
      _this.slides = res.goods.imgs_url 
      _this.goods = res.goods
      _this.detailPic = res.goods.content.replace(/\<img/gi, '<img style="width:100%;height:auto" ')
      

      if(res.goods.is_collect == 0) {
        _this.isCollect = false
      } else {
        _this.isCollect = true
      }
    })
    

  }
}
</script>
<style lang="scss" scoped>

@mixin overflow-dot($line: 2) {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: $line;
  -webkit-box-orient: vertical;
}

.goods-detail {
  overflow: hidden;
  padding-bottom:55px;
  // badge
  .weui-badge {
    font-size:20PX;
  }

  swiper img {
    max-width: 100%;
  }
  
  /deep/ .icon-fanhui {font-size: 14px;}
  .slide,
  swiper,
  .slide image {height: 300px}

  .slide {
    position: relative;
    height: 300px;
    &-count {
      position: absolute;
      right: 10px;
      bottom: 10px;
      .s-gray {font-size: 12px;}
    }
  }

  // 商品信息
  &__info {
    padding: 18px;
    background: #fff;
  }
  &__price {
    font-size: 20px;
    margin-right: 5px;
  }
  &__price,
  .goods__price-cost {display: inline-block;}

  &__tt {
    font-size: 16px;
  }
  &__cell {
    display: block;
  }
  &__tt,x
  &__cell {margin-top: $box-size}
  &__pic {
    font-size: 14px;
    margin-top: 15px;
    
  }

  // 库存
  &__stock {font-size: 12px; display: inline-block;float: right;margin-top: 10px;}
}

// 商品评价
.goods-comment {
  margin-top: $box-size;
  background: #fff;
  
  &__bd {
    padding: 10px 15px;
    white-space: nowrap;
    width: 100%;
    display: block;
    box-sizing: border-box;
  }
  &__item {
    display: inline-flex;
    align-items: center;
    border: 1px solid #ececec;
    width: 300px;
    margin-right:10px;
    overflow:hidden;
    margin-right:10px;
    border-radius: 3px;
    height: 130px;
    &:last-child {
      margin-right: 0;
    }
  }
  &__left {
    margin: 15px 10px 15px;
    flex: 1;
  }
  &__right {
    background: #f9f9f9;
    padding: 15px 10px;
  }
  &__name {font-size: 14px;margin-left: 5px;}
  &__text {
    margin-top: 10px;
    font-size: 14px;
    @include overflow-dot(3);
    white-space:normal;

  }
  /deep/ .u-goods__img {background-color: #ddd}
}


// 底部购物栏
.bottom-bar {
  display: flex;
  align-items: center;
  box-shadow: 1px 0 10px rgba(0,0,0,.1);
  position:fixed;
  bottom:0;
  width:100%;
  height:55px;
  background:#fff;

  &__collect  &__icon{ background-image: url('~images/new_icon/icon_heart.png')}
  &__collect  &__icon--active{ background-image: url('~images/new_icon/icon_heart_active.png')}
  &__service  &__icon{ background-image: url('~images/new_icon/icon_service2.png')}
  &__cart  &__icon{ background-image: url('~images/new_icon/icon_cart2.png')}
  &__icon--active,
  &__icon {
    position: relative;
    width: 52px;
    height: 52px;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    zoom: .5;
    margin: 0 auto;
  }
  &__collect,
  &__service,
  &__cart {
    width: 24%;
    height: 55px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    cursor: pointer;
    position: relative;
  }
  &__service {
    line-height: 1.4;
    background: #fff;
  }

  &__tt {
    font-size: 12px;
    text-align: center;
    margin-top: 2px;
  }

  /deep/ .u-button {
    min-width: 95px;
    margin-left: 10px;
    padding: 0 10px;
  }

  /deep/ .u-button--primary {margin-right: 10px}
  // /deep/ .weui-badge {font-size: 12PX}
}
  
</style>
