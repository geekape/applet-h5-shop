<template>
  <div class="search">
    <input type="text" class="search-input" placeholder="输入关键字搜索商品" confirm-type="search" placeholder-style="color:#aaa" @confirm="startSearch">
  </div>
</template>

<script>
import {host} from '@/utils'
export default {
  props: ['type'],
  methods: {
    startSearch(e) {
      this.GLOBAL.app.searchKey = e.mp.detail.value
      this.GLOBAL.app.listsType = 1

      // 在列表页搜索
      if(this.type == 2) {
        wx.reLaunch({
          url: '/pages/shop/lists/index'
        })
      } else {
        wx.switchTab({
          url: '/pages/shop/lists/index'
        })
      }
      

    },
  }
}
</script>

<style lang="scss" scoped>
.search {
    background: url('http://shengxun.weiphp.cn/uploads/picture//20181017/5bc6afa9716ee.png') no-repeat -10px -10px;
    background-size: 34px;
    background-position: 10px;
    height: 35px;
    border: 1px solid $red;
    border-radius: 30px;
    &-input {
      margin-left:50px;
      height:35px;
      font-size: 16px;
    }
}

</style>
