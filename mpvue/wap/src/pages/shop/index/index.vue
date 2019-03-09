<template>
  <div class="index">
    <scroller>
      <search></search>
      <div class="m15 swiper">
        <van-swipe>
          <van-swipe-item class="pic-align-center" v-for="(item,index) in slides" :key="index">
            <a :href="item.url ? item.url : '#'">
              <img :src="item.img" alt>
            </a>
          </van-swipe-item>
        </van-swipe>
      </div>

      <!-- 分类 -->
      <div class="categorys">
        <van-swipe>
          <van-swipe-item v-for="(item,index) in categoryList" :key="index">
            <div
              @click="goToUrl(category.pid, category.id)"
              class="category-block__item"
              v-for="(category, idx) in item"
              :key="category.id"
            >
              <img :src="category.icon" class="category-block__img" mode="aspectFill">
              <p class="category-block__txt overflow-dot_row">{{category.title}}</p>
            </div>
          </van-swipe-item>
        </van-swipe>
      </div>

      <!-- 推荐商品 -->
      <p class="page-title">猜你喜欢</p>
      <goodsList v-if="goods" :goodsData="goods"></goodsList>
    </scroller>

    <tabbar checkedIndex="1"></tabbar>
  </div>
</template>

<script>
import search from "@/components/shop/search";
import tabbar from "@/components/tabbar";
import goodsList from "@/components/shop/goodsList";
import { get, post, wxConfig } from "@/utils";
const wx = require("weixin-js-sdk");

export default {
  config: {
    navigationBarTitleText: "圆梦云商城"
  },
  data() {
    return {
      motto: "Hello World",
      slides: [],
      categorys: [],
      goods: [],
      isLoad: false
    };
  },
  components: {
    search,
    goodsList,
    tabbar
  },
  computed: {
    // 处理分类
    categoryList() {
      let arr = JSON.parse(JSON.stringify(this.categorys));
      let len = 0;
      let arr2 = [];
      arr.length % 8 == 0
        ? (len = arr.length / 8)
        : (len = parseInt(arr.length / 8) + 1);
      for (var i = 0; i < len; i++) {
        arr2.push(arr.slice(i * 8, (i + 1) * 8));
      }
      return arr2;
    }
  },
  methods: {
    jump(url) {
      window.location.href = url;
    },
    goToUrl(pids, ids) {
      this.$router.push({ name: "lists", params: { pid: pids, id: ids } });
    }
  },
  created() {
    var _this = this;
    get("shop/api/index").then(res => {
      console.log(res);
      _this.slides = res.slideshow;
      _this.categorys = res.category;
      _this.goods = res.goods;
    });

    post("shop/api/cart/", {
      PHPSESSID: window.localStorage.getItem("PHPSESSID")
    })
      .then(res => {
        let num = res.lists.length;
        _this.$store.commit("getCartShopNum", {
          num: num
        });
      })
      .catch(err => {
        console.log("失败：" + err);
      });

      
  }
  
  // gq9nfmqafvnk96lhjqh8rarcvi
};
</script>


<style lang="scss" scoped>
._v-container {
    padding-top: 15px;
    padding-bottom: 45px;
    background: #fff;
}
.flex-wrap {
  display: flex;
  flex-wrap: wrap;
}
.index {
  background: #fff;
  

  /deep/ .search {
    margin: $box-size;
    margin-top: 0;
  }
  .goods-list {
    padding: $box-size;
    min-height: 20px;
    box-sizing: border-box;
  }
  /deep/ .van-swipe__indicator {
    background-color: rgba(0, 0, 0, 0.1);
    &--active {
      background-color: $red;
    }
  }
}

/* 推荐商品 */
.page-title {
  padding: $box-size 0 5px $box-size;
  background: #fff;
  border-top: 10px solid #f5f5f5;
}
.categorys {
  /deep/ .van-swipe {
    height: 220px;
  }
  .van-swipe-item {
    @extend .flex-wrap;
  }
}
/* 分类块 */
.category-block {
  @extend .flex-wrap;
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

// 轮播
.swiper {
  &,
  & a {
    height: 150px;
  }
  img {
    height: 100%;
  }
  /deep/ .van-swipe__indicators {
    bottom: 10px;
  }
  /deep/ .van-swipe__indicator {
    height: 1px;
    width: 13px;
    border-radius: 0;
  }

  .van-swipe {
    height: 4rem;
    box-shadow: 0px 5px 20px 0px rgba(0, 0, 0, 0.1);
  }
}
.m15 {
  margin: 15px;
}
</style>
