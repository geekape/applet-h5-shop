<template>
  <div class="refund comment">
    <navbar text="退款"></navbar>
    <div>
      <textarea class="comment-box" name="" maxlength="999" placeholder="退款原因" v-model="text"></textarea>
    </div>
      <button class="u-button u-button--big u-button--primary" @click="formSubmit">提交</button>
  </div>
</template>

<script>
import {post, get} from "@/utils"
import {Toast} from "vant"
import navbar from "@/components/navbar";
export default {
  data () {
    return {
     orderId: 0,
     text: ''
    }
  },
  computed: {
   
  },

  components: {
    navbar
  },

  methods: {
    formSubmit () {
      const _this = this
      let text = this.text
      if(!text) {
        Toast( '不能为空')
        return false
      }
      post('shop/api/doRefund', {
        id: _this.orderId,
        PHPSESSID: window.localStorage.getItem('PHPSESSID'),
        refund_content: text
      }).then((res) => {
        if(res.code == 1) {
          Toast(res.msg)
          this.$router.replace('/center')
        } else {
          Toast(res.msg)
        }
      })
    }
  },

  created () {
    console.log(this.$route)
    this.orderId = parseInt(this.$route.params.id) || 0
  }
}
</script>


<style lang="scss" scoped>
.comment {
  margin: 15px;
  padding-top: 45px;
  &-box {
    width:100%;
    overflow: hidden;
    background:#fff;
    padding: 10px;
    box-sizing: border-box;
    min-height: 200px;
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
