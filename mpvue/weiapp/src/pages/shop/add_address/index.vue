<template>
  <div class="add-address">
    <from class="m-from">
      <div class="m-from__input">
        <label for="name" class="m-from__label">姓名</label>
        <input id="name" @input="bindName" type="text" :value="name" placeholder="收货人姓名" placeholder-style="color: #aaa">
      </div>
      <div class="m-from__input">
        <label for="moblie" class="m-from__label">电话</label>
        <input id="moblie" @input="bindMoblie" type="number" :value="moblie" placeholder="手机号码" placeholder-style="color: #aaa">
      </div>
      <div class="m-from__picker">
        <label for="address" class="m-from__label">地区</label>
         <picker id="address" :class="{active: addressArea != '选择省/市/区'}" mode="region" @change="bindRegionChange" :value="addressArea" :custom-item="customItem">
           {{addressArea}}
          </picker>
      </div>
      <div class="m-from__input">
        <label for="addressInfo" class="m-from__label">详细地址</label>
        <input id="addressInfo" @input="bindAddressInfo" type="text" placeholder="街道、小区门牌等详细地址" placeholder-style="color: #aaa" :value="addressInfo">
      </div>
    </from>
    <button @click="saveAddress" class="u-button u-button--primary u-button--big">保存</button>
    <van-toast id="van-toast" />
  </div>
</template>

<script>
import {post, get} from '@/utils'
import Toast from "@/../static/vant/toast/toast";
export default {
  mpType: 'page',
  data () {
    return {
      name: '',
      moblie: '',
      addressArea: "选择省/市/区",
      addressInfo: '',
      customItem:[],
      type: 0
    }
  },
  computed: {
  },

  components: {
  },

  methods: {
    bindName (e) {
      console.log(e)
      this.name = e.mp.detail.value
    },
    bindMoblie (e) {
      this.moblie = e.mp.detail.value
    },
    bindAddressInfo (e) {
      this.addressInfo = e.mp.detail.value
    },
    bindRegionChange: function (e) {
      this.addressArea = e.mp.detail.value
    },
    saveAddress () {
      var _this = this
      if(this.name == '' || this.moblie == '' || this.addressArea == '' || this.addressInfo == '') {
        Toast('还有表单没有填')
        return falseaddress_detail
      }

      if((typeof this.addressArea) == 'object') {
        this.addressArea = this.addressArea.join(',')
      }
      let obj = {
        PHPSESSID: wx.getStorageSync('PHPSESSID'),
        truename: _this.name,
        mobile: _this.moblie,
        address: _this.addressArea,
        address_detail: _this.addressInfo,
        is_use: 1,
        is_choose: 1
      }
      post("/shop/api/add_address", obj)
      .then((res) => {
        Toast('保存地址成功')
        // 从订单页进来的
        if(this.type == 1) {
          wx.navigateBack({
            url: '../confirm_order/index'
          })
          
        } else {
          wx.switchTab({
            url: '../center/index'
          })
        }
        wx.setStorageSync('address', obj)
      })
      }
  },

  onLoad () {
    this.type = this.$root.$mp.query.type || 0
    const _this = this
    // 修改地址
    get("/shop/api/add_address/PHPSESSID/"+wx.getStorageSync('PHPSESSID'))
    .then((res) => {
      console.log(res)
      _this.name = res.info.truename
      _this.moblie = res.info.mobile
      _this.addressArea ? _this.addressArea = res.info.address : _this.addressArea = "选择省/市/区"
      _this.addressInfo = res.info.address_detail
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
  .m-from {
    font-size: 16px;
    &__input,
    &__picker {
      height: 45px;
      line-height: 45px;
      margin: 0 15px;
      border-bottom: 1px solid #ececec;
      display: flex;
      align-items: center;
    }
    picker {color: #aaa; width: 100%}
    picker.active {color: #333}
    &__textarea {
      min-height: 90px;
      padding: 15px;
      border-bottom: 1px solid #ececec;
    }
    &__label {
      min-width: 80px;
    }
    &__input input,
    picker {flex: 1}
  }

</style>
