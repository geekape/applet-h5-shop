<template>
  <div class="categorys">
     <swiper indicator-dots="true" indicator-color="#eee" indicator-active-color="#ff0204">
        <swiper-item v-for="(item, index) in categoryList" :key="index"  class="category-block">
          <div @click="goToUrl(item.pid, item.id)" class="category-block__item" v-for='(category, idx) in item' 
            :key='category.id'>
            <img lazy-load :src="category.icon" class="category-block__img" mode="aspectFill"/>
            <p class="category-block__txt overflow-dot_row">{{category.title}}</p>
          </div>
        </swiper-item>
    </swiper>
        
    </div>
</template>

<script>
export default {
  props: ['categorys'],
  data () {
    return {
    }
  },
  methods: {
    goToUrl (pid, id) {
      this.GLOBAL.app.pid = pid
      this.GLOBAL.app.id = id
      wx.switchTab({
        url: '/pages/shop/lists/index'
      })
    }
  },
  computed: {
    // 处理分类
    categoryList () {
      let arr = JSON.parse(JSON.stringify(this.categorys))
      let len = 0
      let arr2 = []
      arr.length % 8 == 0 ? len = arr.length / 8 : len = parseInt(arr.length /8) + 1
      for(var i = 0; i< len; i++) {
        arr2.push( arr.slice(i*8, (i+1)*8 ) )
      }
      return arr2
      
    }
  },
}
</script>

<style lang="scss" scoped>
.categorys swiper {height: 200px}
/* 分类块 */
.category-block {
  display: flex;
  flex-wrap: wrap;
}
.category-block__item {
  flex: 25%;
  max-width: 25%;
  margin-bottom: 10px;
}
.category-block__img {
  background: #eee;
  width: 50px;
  height: 50px;
  margin: 0 auto;
  display: block;
  border-radius: 50%;
}
.category-block__txt {
  font-size: 13px;
  text-align: center;
  margin-top: 14px;
}

</style>
