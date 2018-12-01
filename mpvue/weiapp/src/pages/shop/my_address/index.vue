<template>
  <div class="my-address">
    <!-- 地址 -->
     <!-- v-for="(item,index) in address" :key="index" -->
    <div class="address-item" v-if="address.truename">
      <div class="address-item__bd">
        <div class="g-flex">
          <p class="address-item__name g-flex__item">{{address.truename}}</p>
          <p class="address-item__moblie">{{address.mobile}}</p>
        </div>
        <p class="address-item__info">{{address.address + address.address_detail}}</p>
      </div>

      <div class="address-item__ft g-flex">
        <van-checkbox @change="toggleCheckbox(item)" v-if="address.is_use == 1" class="address-item__checkbox" :value="address.is_use == 1">默认地址</van-checkbox>

        <van-checkbox v-else class="address-item__checkbox active" :value="address.is_use == 0">默认地址</van-checkbox>
        <!-- <div class="address-item__del" @click="delAddress">
          <i class="iconfont icon-shanchu"></i>删除
        </div> -->
      </div>
    </div>
    
    <a open-type="redirect" v-else="address.truename" href="../add_address/main?id=0" class="u-button u-button--primary u-button--big">新增地址</a>
    <a open-type="redirect" v-if="address.truename" href="../add_address/main?id=1" class="u-button u-button--primary u-button--big">修改地址</a>
  </div>
</template>

<script>
import {get} from "@/utils"
export default {
  data () {
    return {
      address: []
    }
  },
  computed: {
    
  },

  components: {
    
  },

  methods: {
    toggleCheckbox(item) {
      this.address.forEach((ele, index) => {
        ele.isDefault = false
      })
      item.isDefault = true
    },

    // 删除地址
    delAddress () {
      wx.showModal({
        title: '提示',
        content: '确定删除当前地址',
        success (res) {
          if (res.confirm) {
            wx.showToast({
              title: '删除成功',
              icon: 'none',
              duration: 2000
            })
          } else if (res.cancel) {
            
          }
        }
      })
    }
  },

  onLoad () {
    var _this = this
    get("/shop/api/add_address/PHPSESSID"+wx.getStorageSync('PHPSESSID'))
    .then((res) => {
      console.log(res)
      _this.address = res.info
    })
  }
}
</script>


<style lang="scss" scoped>
.address-item {
  font-size: 16px;
  background: #fff;
  margin: $box-size;
  margin-bottom: 0;
  &__bd {
    padding: $box-size;
  }

  &__info {color: #999;font-size: 12px;margin-top: 5px}

  &__ft {
    border-top: 1px solid #eee;
    padding: 10px $box-size;
  }
  ._van-checkbox {color: $red;font-size: 12px;flex: 1}

  &__checkbox.active {color: #333}

  // &__del {
  //   display: flex;
  //   align-items: center;
  //   font-size: 12px;
  // }
  // &__del .iconfont {
  //   display: inline-block;
  //   font-size: 20px;
  //   margin-right: 2px;
  // }
}
</style>
