<template>
  <div class="index">
    <search></search>
    <slide v-if='slides' :slides='slides' :autoplay="true"></slide>

    <!-- 分类 -->
     <div class="categorys">
     <swiper indicator-dots="true" indicator-color="#eee" indicator-active-color="#ff0204">
        <swiper-item v-for="(item, index) in categoryList" :key="index"  class="category-block">
          <div @click="goToUrl(category.pid, category.id)" class="category-block__item" v-for='(category, idx) in item' 
            :key='category.id'>
            <img lazy-load :src="category.icon" class="category-block__img" mode="aspectFill"/>
            <p class="category-block__txt overflow-dot_row">{{category.title}}</p>
          </div>
        </swiper-item>
    </swiper>
        
    </div>

    <!-- 推荐商品 -->
    <p class="page-title">猜你喜欢</p>

    <goodsList v-if='goods' :goodsData='goods'></goodsList>
  </div>
</template>

<script>
import search from '@/components/shop/search'
import slide from '@/components/slide'
import goodsList from '@/components/shop/goodsList'
import {get} from "@/utils"

export default {
  
  data () {
    return {
      motto: 'Hello World',
      slides: [],
      categorys: [],
      goods: []
    }
  },
  components: {
    search,
    slide,
    goodsList,
  },
  computed: {
    // 处理分类
    categoryList () {
      let arr = JSON.parse(JSON.stringify(this.categorys))
      let len = 0
      let arr2 = []
      arr.length % 8 == 0 ? len = arr.length / 8 : len = parseInt(arr.length /8) + 1
      for(var i = 0; i< len; i++) {
        arr2.push( arr.slice(i*8, (i+1)*8 ) )
      }
      return arr2
      
    }
  },
  methods: {
    goToUrl (pid, id) {
      this.GLOBAL.app.pid = pid
      this.GLOBAL.app.id = id
      wx.switchTab({
        url: '/pages/shop/lists/index'
      })
    }
  },
  onLoad () {
    console.log('首页加载了')
    get('shop/api/index').then((res) => {
      console.log(res)
      this.slides = res.slideshow
      this.categorys = res.category
      this.goods = res.goods
    })

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
  onShareAppMessage () {
    // 分享
    return {
      title: '易商城首页',
      path: '/pages/index/index'
    }
  }
    
}
</script>
<style>
page {
  background: #fff!important;
}
</style>
<style lang="scss" scoped>

.index {
  /deep/ .search,
  /deep/ .slide {margin: $box-size}
  .goods-list {
    padding: $box-size;
    min-height: 20px;
    box-sizing: border-box;
  }
  /deep/ .slide,
  /deep/ swiper {
    height: 150px;
  }
}

/* 推荐商品 */
.page-title {
  padding:$box-size 0 5px $box-size;
  background:#fff;
  border-top: 10px solid #f5f5f5; 
}

.categorys swiper {height: 200px}
/* 分类块 */
.category-block {
  display: flex;
  flex-wrap: wrap;
  &__item {
    flex: 25%;
    max-width: 25%;
    margin-bottom: 10px;
  }
  &__img {
    background: #eee;
    width: 50px;
    height: 50px;
    margin: 0 auto;
    display: block;
    border-radius: 50%;
  }
  &__txt {
    font-size: 13px;
    text-align: center;
    margin-top: 14px;
  }
}

</style>
