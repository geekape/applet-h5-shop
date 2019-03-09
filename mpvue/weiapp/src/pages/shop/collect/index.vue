<template>
  <div class="collect">
    <div class="collect_list" v-for="(item,index) in collectList" :key="index">
      <a :href="'../goods_detail/index?id=' + item.id" hover-class="none" class="goods-line">
        <image class="u-goods__img" :src="item.cover"></image>

        <div class="goods-line__right">
          <p class="u-goods__tt overflow-dot">{{item.title}}</p>
          <div class="goods-line__ft">
            <div class="goods-line__price u-goods__price"><span class="icon-price">¥</span>{{item.sale_price}}</div>
            <div class="goods-line__icon" catchtap="" @click="addCart" :data-index="index"></div>
          </div>
        </div>
      </a>
    </div>
    <van-toast id="van-toast" />
  </div>
</template>

<script>
import {post} from "@/utils"
import Toast from "@/../static/vant/toast/toast";
export default {
  mpType: 'page',
  data () {
    return {
      collectList: []
    }
  },

  components: {
    
  },
  computed: {
    
  },

  methods: {
    addCart (e) {
      const idx = e.target.dataset.index
      const id = this.collectList[idx].id
      post('shop/api/addToCart', {
        goods_id: id,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      }).then((res) => {
        if (res > 0) {
          Toast('加入购物车成功')
        } else {
          Toast(res.msg)
        }
      })
    }
  },

  onLoad () {
    post('/shop/api/my_collect', {
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then((res) => {
      console.log(res.myCollect)
      this.collectList = res.myCollect
    })
  }
}
</script>
<style>
page {
  background: #fff!important;
}
</style>

<style lang="scss" scoped>
.collect_list {
  margin-top: 10px;
  .goods-line__right {
    border-bottom: 1px solid #eee!important
  }
}

</style>
