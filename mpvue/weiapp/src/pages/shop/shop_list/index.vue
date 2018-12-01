<template>
  <div class="shop-list">
    <div class="shop-list__item" v-for="(item, index) in shops" :key="index">
      <div class="shop-list__bd g-flex">
        <image class="u-goods__img" :src="item.img_url"></image>
        <div class="g-flex__flex">
          <p class="shop-list__name">{{item.name}}</p>
          <p class="shop-list__address" v-if="item.address"><span class="iconfont icon-dingwei"></span>{{item.address}}</p>
          <p class="shop-list__dist" v-if="item.shop_code"><span class="iconfont icon-daohang"></span></p>
        </div>
      </div>
      <div class="shop-list__ft g-flex">
        <p class="g-flex__item"><span class="iconfont icon-phone"></span>电话</p>
        <p class="g-flex__item"><span class="iconfont icon-daohang"></span>导航</p>
      </div>
    </div>
  </div>
</template>

<script>
import {post} from "@/utils"
export default {
  data () {
    return {
      shops: []
    }
  },
  computed: {
    
  },

  methods: {

  },
  onLoad () {
    const is_choose = 1
    const ids = -1
    const _this = this
    post("shop/api/shop_list", {
        is_choose: is_choose,
        ids: ids,
        PHPSESSID: wx.getStorageSync('PHPSESSID')
      })
      .then((res) => {
        console.log(res)
        _this.shops = res.store_lists
      })
  }

}
</script>


<style lang="scss" scoped>

.shop-list {
  &__item {
    background: #fff;
    margin-top: 10px;
  }
  &__bd {
    padding: 15px;
    .g-flex__flex {
      justify-content: center;
      display: flex;
      flex-direction: column;
    }
  }
  &__ft {
    padding: 10px 0;
    border-top: 1px solid #eee;
    font-size: 16px;
    .g-flex__item {text-align: center;}
    .iconfont {color: $red;margin-right: 2px;}
  }
  /deep/ .u-goods__img {
    margin-right: 15px;
  }
  &__name {margin-bottom: 10px;}
  &__address {
    font-size: 14px;
    color: #999;
  }
}


</style>
