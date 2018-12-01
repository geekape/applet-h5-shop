<template>
  <div class="msg">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon_msg" :class="'weui-icon-' + type"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">{{msg}}</h2>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a @click="goUrl" class="weui-btn weui-btn_primary">返回个人中心</a>
            </p>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      msg: "",
      type: "warn"
    };
  },
  computed: {},

  methods: {
    goUrl() {
      this.$router.replace("/center");
    }
  },

  created() {
    this.msg = this.$route.params.msg;
    this.type = this.$route.params.type || "warn";
  }
};
</script>



<style lang="scss" scoped>
.weui-btn {color: #fff}
</style>
