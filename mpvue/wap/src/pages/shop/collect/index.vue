<template>
  <div class="collect">
    <navbar text="我的收藏"></navbar>
    <div class="collect_list">
      <a  @click.prevent="jump(item.id)" class="goods-line" v-for="(item,index) in collectList" :key="index">
        <img class="u-goods__img" :src="item.cover" />

        <div class="goods-line__right">
          <p class="u-goods__tt overflow-dot">{{item.title}}</p>
          <div class="goods-line__ft">
            <div class="goods-line__price u-goods__price"><span class="icon-price">¥</span>{{item.sale_price}}</div>
            <div class="goods-line__icon" catchtap="" @click.stop="addCart" :data-index="index"></div>
          </div>
        </div>
      </a>
    </div>
  </div>
</template>

<script>
import {post,get,wx} from "@/utils"
import {Toast} from "vant"
import navbar from "@/components/navbar";
export default {
  data () {
    return {
      collectList: []
    }
  },

  components: {
    navbar
  },
  computed: {
    
  },

  methods: {
    jump (id) {
      this.$router.push('/goods_detail/' + id)
    },
    addCart (e) {
      console.log(e)
      const idx = e.target.dataset.index
      const id = this.collectList[idx].id
      post('shop/api/addToCart', {
        goods_id: id,
        PHPSESSID: window.localStorage.getItem('PHPSESSID')
      }).then((res) => {
        if (res.data > 0) {
          Toast('加入购物车成功')
        } else {
          Toast(res.msg || '加入购物车成功')
        }
      })
    }
  },

  created () {
    post('/shop/api/my_collect', {
      PHPSESSID: window.localStorage.getItem('PHPSESSID')
    }).then((res) => {
      console.log(res.myCollect)
      this.collectList = res.myCollect
    })
  }
}
</script>

<style lang="scss" scoped>
.collect {
  padding-top: 45px;
  height: 100vh;
  background: #fff;
}
.collect_list {
  margin-top: 10px;
 
}

</style>
