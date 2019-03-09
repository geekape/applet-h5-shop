<template>
  <div class="m-popup" v-if="isPopup">
    <van-popup v-model="isPopup" position="right">
      <div class="navbar">
        <p class="navbar-icon_left iconfont icon-fanhui" @click="$emit('closePopup')"></p>
        <p class="navbar-title overflow-dot_row">{{popupTitle}}</p>
      </div>
      <div class="m-popup__content">
          <div v-html="popupData"></div>
      </div>
    </van-popup>
  </div>
</template>

<script>
export default {
  props: {
    popupData: String,
    isPopup: Boolean,
    popupTitle: String
  },
  data() {
    return {
    };
  },
  methods: {
    
  }
}
</script>

<style lang="scss" scoped>
// popup--right
.m-popup {
    .van-popup--right {
        width: 100%;
        height: 100%;
        padding: 15px;
        box-sizing: border-box;
        background: #f9f9f9;

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
    }
    &__content {
        margin-top: 45px;
    }
    
}
</style>
