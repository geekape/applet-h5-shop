<template>
  <div class="diy-goods">
    <template v-for="(item, index) in diyData">
      <!-- diy 富文本 -->
      <div class="richtext mt15" v-if="item.id == 'richtext'" v-html="item.params.content"></div>

      <!-- diy 商品 -->
      <div class="goods mt15" :class="goodsClass2(item.params.list_style)" v-if="item.id == 'goods'">
        <!-- 大图样式 -->
        <a
          :href="'/goods_detail/'+goods.id"
          class="goods-item"
          :key="goodsIdx"
          v-for="(goods,goodsIdx) in item.params.goods_list"
        >
          <template v-if="item.params.list_style == 1">
              <div class="goods-item__img">
            <img :src="goods.img">
            <p
              class="goods-item__price goods-price"
              v-if="item.params.show_price == 1"
            >¥{{goods.market_price}}</p>
            <div class="goods-item__ft g-flex">
              <p class="goods-item__title g-flex__item overflow-dot_row">{{goods.title}}</p>
              <button class="goods-item__btn" v-if="item.params.show_btn == 1">立即购买</button>
            </div>
          </div>
          </template>

          <template v-else>
              <div class="goods-item__img">
            <img :src="goods.img">
            
          </div>
          <p class="goods-item__title g-flex__item overflow-dot_row">{{goods.title}}</p>
            <div class="goods-item__ft g-flex">
              <p
              class="goods-item__price goods-price"
              v-if="item.params.show_price == 1"
            >¥{{goods.market_price}}</p>
              <button class="goods-item__btn" v-if="item.params.show_btn == 1">立即购买</button>
            </div>
          </template>
        </a>


 
      </div>

      <!-- diy 多图商品 -->
      <div class="mutipic-goods mt15" v-if="item.id == 'mutipic_goods'">
        <a
          :href="'/goods_detail/'+goods.id"
          class="mutipic-goods-item"
          :class="goodsClass(item.params.colGoods)"
          :key="'goods-' + goodsIdx"
          v-for="(goods,goodsIdx) in item.params.goods_list"
        >
          <div class="mutipic-goods-item__img">
            <img :src="goods.img">
          </div>

          <p class="mutipic-goods-item__title g-flex__item overflow-dot_row">{{goods.title}}</p>
          <div class="mutipic-goods-item__ft g-flex">
            <p
              class="mutipic-goods-item__price goods-price g-flex__item"
              v-if="item.params.show_price == 1"
            >¥{{goods.market_price}}</p>
            <button class="mutipic-goods-item__btn" v-if="item.params.show_btn == 1">立即购买</button>
          </div>
        </a>
      </div>

      <!-- diy 幻灯片 -->
      <div class="banner mt15" v-if="item.id == 'banner'">
        <van-swipe indicator-color="red">
          <van-swipe-item
            v-for="(banner,bannerIdx) in item.params.banner_list"
            :key="'banner-'+bannerIdx"
          >
            <a :href="banner.url" class="slide-url pic-align-center">
              <img class="slide-image" mode="aspectFill" :src="banner.pic">
            </a>
            <div class="banner-ft">
              <p>{{banner.title}}</p>
            </div>
          </van-swipe-item>
        </van-swipe>
      </div>

      <!-- diy 图片-->
      <div class="pic mt15" v-if="item.id == 'pic'">
        <a :href="pic.url" v-for="(pic, picIdx) in item.params.pic_list">
          <img :src="pic.pic">
        </a>
      </div>

      <!-- diy 辅助空白-->
      <div class="blank mt15" v-if="item.id == 'blank'" :key="index">
        <p></p>
      </div>

      <!-- diy 标题-->
      <div
        class="title mt15"
        v-if="item.id == 'title'"
        :style="{align: item.params.align, background: item.params.bgColor}"
      >
        <h3 :style="{color: item.params.maincolor}">{{item.params.title}}</h3>
        <p>{{item.params.subtitle}}</p>
      </div>

      <!-- diy 辅助线-->
      <div class="blankline mt15" v-if="item.id == 'blankline'"></div>
    </template>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    diyData: [Array, Object]
  },
  computed: {
  },

  methods: {
    goodsClass(num) {
      if (num == 2) {
        return "mutipic-goods-item_two";
      } else if (num == 3) {
        return "mutipic-goods-item_three";
      } else {
        return "mutipic-goods-item_four";
      }
    },
    goodsClass2(num) {
      if (num == 2) {
        return "goods-item_small";
      } else if (num == 3) {
        return "goods-item_big";
      } else if (num == 4) {
        return "goods-item_lists";
      } else {
          return ""
      }
    },
  }
};
</script>

<style lang="scss" scoped>
.goodsBtn {
  padding: 6px 7px;
  font-size: 11px;
  background: $red;
  box-sizing: border-box;
  color: #fff;
  border-radius: 5px;
}

.flex-items-center {
  display: flex;
  align-items: center;
}
.goods-price {
  font-size: 18px;
  color: $red;
}
.mt15 {
  margin-top: 15px;
}

.position-absolute {
  position: absolute;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  left: 0;
  right: 0;
  padding: 5px 10px;
}

// 商品

// 大图样式
.goods-item {
  &__img {
    height: 200px;
    overflow: hidden;
    position: relative;
  }
  &__ft {
    @extend .position-absolute;
    @extend .flex-items-center;
  }
  &__price {
    position: absolute;
    top: 65%;
    right: 10px;
  }
  &__btn {
    @extend .goodsBtn;
  }
  &__title {
    font-size: 14px;
    color: #fff;
  }
}
// 小图
.goods-item_small {
       display: flex;
    flex-wrap: wrap;
    .goods-item__title {
        color: #000;
        padding: 5px 10px;
    }
    .goods-item__ft,
    .goods-item__price {position: static;flex: 1}
    .goods-item__ft {background: transparent}
    .goods-item {width: 50%;}
}

.goods-item_big {
  @extend .goods-item_small;
  .goods-item:first-child {width: 100%}
}
.goods-item_lists {
    @extend .goods-item_small;
    flex-direction: column;
    .goods-item {width: auto}
    .goods-item__img {
        height: auto;
        float: left;
        &,
        img {width: 100px}
    }

}


// 多图商品
.mutipic-goods {
  flex-wrap: wrap;
  display: flex;
}
.mutipic-goods-item {
  // 2行
  &_two {
    width: 50%;
  }
  &_three {
    width: 33.3%;
  }
  &_four {
    width: 25%;
  }

  &__btn {
    @extend .goodsBtn;
  }
  &__title,
  &__ft {
    padding: 0 10px;
    flex-wrap: wrap;
  }
  &__ft {
    @extend .flex-items-center;
  }
  &__img {
    max-height: 200px;
    overflow: hidden;
    @extend .g-flex-center;
  }
}

// 轮播

.banner {
  &-ft {
    @extend .position-absolute;
    color: #fff;
    height: 30px;
  }
  .van-swipe {
    height: 8.5rem;
  }
  /deep/ .van-swipe__indicators {
    left: auto;
    right: 10px;
    bottom: 15px;
  }
}
</style>