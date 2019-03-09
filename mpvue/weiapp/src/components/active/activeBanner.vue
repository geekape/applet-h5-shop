<template>
  <div class="active_banner" >
    <!-- 头部导航 -->
    <img lazy-load class="active_bg" v-if="eventinfo.cover == null || eventinfo.cover == ''" :src="active.img">
    <img lazy-load class="active_bg" v-else :src='eventinfo.cover'>
    <!-- /秒杀banner -->
  </div>
</template>
<script>
  export default {
    props: {
      active: {},
      eventinfo: {
        default: function () {
          return {cover: null}
        }
      }
    }
  }
</script>

<style>

</style>
