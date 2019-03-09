<template>
  <div class="slide">
    <!--<swiper class="swiper" :indicator-dots="true"  :autoplay="true" :interval="5500" indicator-active-color="#ff0204" indicator-color="rgba(255,255,255,.3)" @change="toggleswiper">-->
    <van-swipe class="swiper" @change="toggleswiper">
      <van-swipe-item class="swiper-item" v-for="(item, index) in imgsurl" :key="index">
        <a class="slide-url" @click="showslide(index)">
          <img  class="slide-image" mode="aspectFill" :src="item"/>
        </a>
      </van-swipe-item>
    </van-swipe>
    <p class="padding"><span class="currentslide">{{currentnum}}</span>/<span class="totalslide">{{imgsurl.length}}</span></p>
  </div>
</template>

<script>
    import { ImagePreview} from "vant";
  export default {
    data () {
      return{
          currentnum: 1,
      }
    },
    props: {
      imgsurl: Array,
    },
    methods: {
      toggleswiper (index) {
        this.currentnum = index + 1;
      },
      showslide (index) {
        // 预览图片
          ImagePreview({
              images: this.imgsurl,
              startPosition: index
          });
      }
    }
  }
</script>

<style>
  .slide{position:relative;}
  .slide-url{display:block;}
  .padding{position:absolute;right:6%;bottom:0;}
  .currentslide{color: #505050;font-size:0.9em;}
  .totalslide{color:black;}
</style>
