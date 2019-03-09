<template>
  <div class="comment">
    <navbar text="评价"></navbar>
    <scroller >
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
    
    <textarea class="comment-box" name="" maxlength="999" placeholder="至少5个字哦~" v-model="text" @blur="setText"></textarea>
    <button class="u-button u-button--big u-button--primary" @click="submitFrom">评价</button>
    </scroller>
  </div>
</template>

<script>
import {post, get} from "@/utils"
import {Toast} from "vant"
import navbar from "@/components/navbar";
export default {
  data() {
    return {
      orderId: '',
      commentText: {},
      goodsId: 0,
      goodsList: [],
      text: ''
    }
  },

  components: {navbar},

  methods: {
    setText () {
      console.log('设置值')
      let e = this.text
      this.goodsId = String(this.goodsId).split(',')
      let allGoods = this.goodsId

      let obj = {}
      allGoods.forEach((item,idx) => {
        obj[item] = e
      })
      this.commentText = obj
    },
    submitFrom () {
      const _this = this
      if(this.commentText == "") {
        Toast('评语不能为空')
        return false
      }
      post('shop/api/comment', {
        order_id: _this.orderId,
        PHPSESSID: window.localStorage.getItem('PHPSESSID'),
        goodsids: _this.goodsId,
        content: _this.commentText
      }).then((res) => {
        console.log(res)
        if(res.code == 0) {
          Toast.fail(res.msg)
        } else {
          this.$router.push({
            name: "msg",
            params: { msg: '评价成功', type: "success" }
          });
        }
        
      })
    },
    getData () {
      const _this = this
      post('shop/api/confirm_order/', {
        PHPSESSID: window.localStorage.getItem("PHPSESSID"),
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
  created (opt) {
    this.orderId = this.$route.params.order_id
    this.goodsId = (this.$route.params.goods_id).join(',')
    this.getData()
  }
}
</script>

<style lang="scss" scoped>
.comment {
  padding-top: 55px;
  background:#fff;
  &-box {
    width:100%;
    padding: 10px;
    box-sizing: border-box;
    min-height: 200px;
  }
  .goods-line {
    margin-bottom: 0;
    margin-top: 10px;
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
