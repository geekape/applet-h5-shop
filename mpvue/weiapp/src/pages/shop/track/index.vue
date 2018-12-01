<template>
  <div class="track">
    <div class="card-list">
      <div class="card-list__item" v-for="(item,index) in datas" :key="index">
        <div class="card-list__hd">
          <p class="card-list__tt">{{item[0]}}</p>
        </div>

        <div class="card-list__bd">
          <div :href="'../goods_detail/main?id=' + goods.id" class="goods-line" v-for="(goods, idx) in item[1]" :key="idx">
            <img class="u-goods__img" :src="goods.cover" />

            <div class="goods-line__right">
              <p class="u-goods__tt overflow-dot">{{goods.title}}</p>
              <div class="goods-line__ft">
                <div class="goods-line__price u-goods__price"><span class="icon_prize">¥</span>{{goods.sale_price}}</div>
                <div class="f-font-sm">浏览{{goods.view_count}}次</div>
                </div>
              </div>
            </div>
          </div>
        
        </div>

      </div>
    </div>
    
  </div>
</template>

<script>
import {post} from "@/utils"
export default {
  data() {
    return {
      trackList: []
     
    };
  },

  components: {},

  methods: {},
  computed: {
    datas () {
      let data = this.trackList
      let {keys, values, entries} = Object
      let arr = []
      for (let [key, value] of entries(data)) {  
        arr.push([key, value])
      }
      return arr
    }
  },

  onLoad () {
    post('shop/api/my_track', {
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then((res) => {
      console.log(res)
      this.trackList = res.track
    })
  }
};
</script>

<style lang="scss" scoped>
.card-list {
  &__item {
    background: #fff;
  }
  &__hd {
    border-bottom: 1px solid #eee;
    font-size: 16px;
  }

  &__bd {
    padding: 5px 0;
    
  }

}


.track {
  .card-list {
    &__item {margin-top: 15px;}

    &__hd {
      height: 45px;
      line-height: 45px;
      padding: 0 15px;
    }

  }
}


</style>
