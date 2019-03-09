<template>
  <div class="refund comment">
    <form action="" @submit="formSubmit">
      <textarea class="comment-box" name="" maxlength="999" placeholder="退款原因" name="text"></textarea>
      <button class="u-button u-button--big u-button--primary" form-type="submit">提交</button>
    </form>
    <van-toast id="van-toast" />
  </div>
</template>

<script>
import {post, get} from "@/utils"
import Toast from "@/../static/vant/toast/toast";
export default {
  mpType: 'page',
  data () {
    return {
     orderId: 0
    }
  },
  computed: {
   
  },

  components: {
    
  },

  methods: {
    formSubmit (e) {
      const _this = this
      let {text} = e.target.value
      if(!text) {
        Toast('不能为空')
        return false
      }
      post('shop/api/doRefund', {
        id: _this.orderId,
        PHPSESSID: wx.getStorageSync('PHPSESSID'),
        refund_content: text
      }).then((res) => {
        console.log(res)
        if(res.code == 1) {
          Toast(res.msg)
          wx.switchTab({url: '../center/index'})
        } else {
          Toast(res.msg)
        }
        
      })
    }
  },

  onLoad () {
    this.orderId = this.$root.$mp.query.order_id || 0
  }
}
</script>


<style lang="scss" scoped>
.comment {
  margin: 15px;
  &-box {
    width:100%;
    overflow: hidden;
    background:#fff;
    padding: 10px;
    box-sizing: border-box;
  }
  .goods-line {
    background: #fff;
    padding:10px 10px 5px;
    margin-bottom: 0;
    border-bottom: 1px solid #eee;
  }
  .u-goods__img {
    width: 80px;
    min-width: 80px;
    height: 80px;
  }
 
}
</style>
