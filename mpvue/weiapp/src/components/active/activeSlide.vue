<template>
  <div class="slide">
    <!--<swiper class="swiper" :indicator-dots="true"  :autoplay="true" :interval="5500" indicator-active-color="#ff0204" indicator-color="rgba(255,255,255,.3)" @change="toggleswiper">-->
    <swiper class="swiper"  :autoplay="true" :interval="2500" @change="toggleswiper">
      <swiper-item class="swiper-item" v-for="(item, index) in imgsurl" :key="index">
        <a class="slide-url" @click="showslide">
          <image :src="item" class="slide-image" mode="aspectFill"/>
        </a>
      </swiper-item>
    </swiper>
    <p class="padding"><span class="currentslide">{{currentnum}}</span>/<span class="totalslide">{{imgsurl.length}}</span></p>
  </div>
</template>

<script>
  export default {
    data (){
      return{
        currentnum: 1
      }
    },
    props: {
      imgsurl: [],
    },
    methods: {
      toggleswiper (e) {
        this.currentnum = e.target.current + 1
      },
      showslide (e) {
        // 预览图片
        const current = e.target.dataset.src;
        const _this = this;
        console.log(_this);
        wx.previewImage({
          current: current, // 当前显示图片的http链接
          urls: _this.imgsurl // 需要预览的图片http链接列表
        })
      }
    }
  }
</script>

<style>
  .slide{position:relative;}
  .padding{position:absolute;right:6%;bottom:0;}
  .currentslide{color: #505050;font-size:0.9em;}
  .totalslide{color:black;}
</style>
