<template>
  <div class="slide">
    <swiper class="swiper" :indicator-dots="isDot"  :autoplay="autoplay" :interval="interval" indicator-active-color="#ff0204" indicator-color="rgba(255,255,255,.3)">
        <swiper-item v-for="(item, index) in slides" :key="index">
            <div class="slide-url" @click="goUrl(item.url)">
              <image :src="item.img" class="slide-image" mode="aspectFill"/>
            </div>
        </swiper-item>
    </swiper>
  </div>
</template>

<script>
export default {
  props: {
    autoplay: '',
    interval: {
      type: Number,
      default: 3000
    },
    slides: Array,
    isDot: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    goUrl (url) {
      if(!url) {
        return false
      }
      wx.navigateTo({
        url: '/pages/shop/web_view/main?url=' + url
      })
    }
  }
}
</script>

<style>


</style>
