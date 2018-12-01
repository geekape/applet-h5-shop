<template>
  <div class="logistics">
    <div class="logistics-head">
      <p>物流公司：{{info.send_code_name ? info.send_code_name : '空'}}</p>
      <p>物流单号：{{info.send_number ? info.send_number : '空'}}</p>
    </div>
    <van-steps
        :steps="logs"
        :active="active"
        direction="vertical"
        active-color="#ff0204"
      />
      
  </div>
</template>

<script>
import {post, timeChange} from "@/utils"
export default {
  data() {
    return {
      active: 2,
      steps: [],
    info: []
     
    };
  },
  methods: {},
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

  onLoad () {
    Object.assign(this, this.$options.data());
    let orderId = this.$root.$mp.query.order_id
    const _this = this
    post('shop/api/logistics', {
      order_id: orderId,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then((res) => {
      _this.steps = res.log
      _this.info = res.info
    })
  }
};
</script>
<style>
 page {background: #fff!important}
</style>

<style lang="scss" scoped>
.logistics {
  &-head {
    background: #fff;
    padding: 15px;
    margin: 10px 0;
    font-size: 16px;
    border-top: 10px solid #f9f9f9;
    border-bottom: 10px solid #f9f9f9;
  }
}



</style>
