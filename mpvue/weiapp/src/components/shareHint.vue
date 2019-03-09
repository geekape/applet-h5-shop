<template>
  <div class="share-hint animated zoomIn" v-show="isShow" @click="$emit('closeHint')">
    <img :src="imgRoot+'share_tips.png'" alt="" class="share-img">
  </div>
</template>

<script>
export default {
  props: {
    isShow: Boolean
  },
  data() {
    return {
			imgRoot: this.imgRoot,
		};
  }
}
</script>

<style lang="scss" scoped>
    .share-hint {
        background: #000;
        position: fixed;
        top: 10px;
        right: 10px;
        max-width: 200px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
        image{
          width: auto;
          height: auto;
        }
    }
    
</style>
