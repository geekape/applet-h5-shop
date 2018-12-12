<template>
      <div>剩余时间:{{cd.day}}天{{cd.hour}}小时{{cd.min}}分钟{{cd.sec}}秒</div>
</template>

<script>
  export default {
    data () {
      return {
      }
    },
    props: ['cd'],
    watch: {
      cd: {
        handler(newValue,oldValue){
          this.getChange();
        },
        deep: true
      }
    },
    onLoad () {
      console.log("wzh");
      this.countdown()
    }
  }
</script>

<style lang="scss" scoped>

</style>
