<template>
  <div class="navbar">
    <p class="navbar-icon_left iconfont icon-fanhui" @click="goBack"></p>
    <p class="navbar-title overflow-dot_row">{{text}}</p>
  </div>
</template>

<script>
export default {
  props: ['text'],
  data() {
    return {};
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    }
  }
}
</script>

<style lang="scss" scoped>
// 顶部导航
.navbar {
  left: 0;
  height: 45px;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
  border-bottom: 1px solid #f2f2f26b;
  position: fixed;
  top: 0;
  width: 100%;
}
.navbar-title {
  flex: 1;
  text-align: center;
  padding: 0 30px;
}
.navbar-icon_left {
  font-size: 20px;
  position: absolute;
  left: 10px;
}
</style>
