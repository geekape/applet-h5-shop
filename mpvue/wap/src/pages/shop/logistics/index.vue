<template>
  <div class="logistics">
    <navbar text="物流信息"></navbar>
    <div class="logistics-head">
      <p>物流公司：{{info.send_code_name ? info.send_code_name : '空'}}</p>
      <p>物流单号：{{info.send_number ? info.send_number : '空'}}</p>
    </div>
    <van-steps direction="vertical" :active="0" active-color="#ff0204">
      <van-step v-for="(item,index) in logs" :key="index">
        <h4>{{item.text}}</h4>
        <p class="time">{{item.desc}}</p>
      </van-step>
    </van-steps>
  </div>
</template>

<script>
import {post, timeChange} from "@/utils"
import navbar from "@/components/navbar";
import { Step, Steps } from 'vant';
export default {
  data() {
    return {
      active: 2,
      steps: [],
      info: []
     
    };
  },
  components: {
    navbar
  },
  computed: {
    logs () {
      let log = this.steps
      let arr = []
      log.forEach((item,idx) => {
        let obj = {
          text: item.remark,
          desc: timeChange(item.cTime)
        }
        arr.push(obj)
      })
      return arr
    }

  },

  created () {
    let orderId = this.$route.params.id
    const _this = this
    post('shop/api/logistics', {
      order_id: orderId,
      PHPSESSID: window.localStorage.getItem('PHPSESSID')
    }).then((res) => {
      _this.steps = res.log
      _this.info = res.info
    })
  }
};
</script>

<style lang="scss" scoped>
.logistics {
  padding-top: 45px;
  &-head {
    background: #fff;
    padding: 15px;
    margin: 10px 0;
    font-size: 16px;
    border-top: 10px solid #f9f9f9;
    border-bottom: 10px solid #f9f9f9;
  }
  .van-step__title .time {margin-top: 15px;}
}



</style>
