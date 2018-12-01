<template>
  <div class="back-top" v-show="isTop" @click="">

  </div>
</template>

<script>
export default {
  data() {
    return {
        isTop: false
    };
  },
  methods: {
    // 获取滚动条高度
    handleScroll() {
        console.log(document.getElementById('orderFullScreen').scrollTop)
    },
  },
  mounted() {
        document.getElementById('orderFullScreen').addEventListener('scroll', this.handleScroll)
    }

}
</script>

<style lang="scss" scoped>
.back-top {
  position: fixed;
  right: 0;
  z-index: 999;
  bottom: 80px;
  display: none;
  width: 40px;
  height: 40px;
  background: rgba(0, 0, 0, 0.7);
  background-size: 20px;
  overflow: hidden;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyBAMAAADsEZWCAAAAFVBMV…vs5ApJLskFaaUKSS71skN6602GjRMc82GSkDA/+RhpLBdlCiXsyNDDDQAAAABJRU5ErkJggg==);
  background-repeat: no-repeat;
  background-position: 50% 50%;
  background-size: 25px;
  border-radius: 3px 0 0 3px;
}
</style>
