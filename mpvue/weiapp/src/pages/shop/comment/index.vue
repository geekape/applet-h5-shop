<template>
  <div class="comment">
    <div  class="goods-line" v-for="(item,index) in goodsList" :key="index">
      <img class="u-goods__img" :src="item.cover" />
      <div class="goods-line__right">
        <p class="u-goods__tt overflow-dot">{{item.title}}</p>
        <div class="goods-line__ft">
          <div class="goods-line__price">
            <span>¥{{item.sale_price}}</span>
            </div>
        </div>
      </div>
    </div>
    
    <textarea class="comment-box" name="" maxlength="999" placeholder="至少5个字哦~" @input="setText"></textarea>
    <button class="u-button u-button--big u-button--primary" @click="submitFrom">评价</button>
    <van-toast id="van-toast" />
  </div>
</template>

<script>
import {post, get} from "@/utils"
import Toast from "@/../static/vant/toast/toast";

export default {
  data() {
    return {
      orderId: '',
      commentText: {},
      goodsId: 0,
      goodsList: [],
      is_first_action: true
    }
  },

  components: {},

  methods: {
    setText (e) {
      console.log(e)
      this.goodsId = String(this.goodsId).split(',')
      let allGoods = this.goodsId
      let obj = {}
      allGoods.forEach((item,idx) => {
        obj[item] = e.target.value
      })
      
      
      console.log(obj)
      this.commentText = obj
    },
    submitFrom () {
      const _this = this
      if(Object.keys(this.commentText) === 0) {
        Toast('评语不能为空')
        return false
      }
      if(this.is_first_action) {
        _this.is_first_action = false
        post('shop/api/comment', {
          order_id: _this.orderId,
          PHPSESSID: wx.getStorageSync('PHPSESSID'),
          goodsids: _this.goodsId,
          content: _this.commentText
        }).then((res) => {
          if(res.code == 0) {
            Toast(res.msg)
            _this.is_first_action = true
          } else {
            wx.reLaunch({ url: "../msg/main?msg=" + "评价成功" });
            _this.is_first_action = true
          }
        })
      }
      
      
    },
    getData () {
      const _this = this
      post('shop/api/confirm_order', {
        PHPSESSID: wx.getStorageSync("PHPSESSID"),
        goods_id: _this.goodsId
      }).then(res => {
        let { keys, values, entries } = Object;
        let arr = [];
        for (let [key, value] of entries(res.lists)) {
          value.forEach((item, idx) => {arr.push(item)})
        }
        this.goodsList = arr
      })
    }
  },
  onLoad (opt) {
    this.orderId = this.$root.$mp.query.order_id
    this.goodsId = this.$root.$mp.query.goods_id
  },
  onShow () {
    this.getData()
  }
}
</script>
<style>
page {background: #fff!important}
</style>

<style lang="scss" scoped>
.comment {
  &-box {
    width:100%;
    background:#fff;
    padding: 10px;
    box-sizing: border-box;
  }
  .goods-line {
    margin-bottom: 0;
    &__right {
      border: 0;
      border-bottom: 0.02667rem solid #ececec;
    }
  }
  .u-goods__img {
    width: 80px;
    min-width: 80px;
    height: 80px;
  }
 
}


</style>
