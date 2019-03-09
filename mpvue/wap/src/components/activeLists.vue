<template>
  <div class="active-lists">
    <div class="goods-list">
      <div
        @click="selfType(item.id)"
        class="goods-list__item g-flex"
        v-for="(item,index) in activeData"
        :key="item.id"
      >
        <img class="goods-list__img u-goods__img" v-if="item.cover_img !=''" :src="item.cover_img">
        <img class="goods-list__img u-goods__img" v-else src="~images/not-pic.jpg">
        <div class="goods-list__info">
          <p class="u-goods__tt overflow-dot">{{item.title}}</p>
          <p class="goods-list__des overflow-dot"></p>
          <div class="g-flex goods-list__ft">
            <button class="u-button u-button--primary" v-if="activeType!=4">
              {{isStart ? "立即围观" : "默默等待"}}
            </button>
            <button class="u-button u-button--primary" v-else> {{isStart ? "立即抢卷" : "默默等待"}}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    activeData: Array,
    activeType: {
      type: String,
      default: 4
    },
    isStart: {   // 活动是否开始
      type: Boolean,
      default: true
    }
  },
  computed: {
    
  },

  methods: {
    selfType(id) {
      let type = this.activeType;
      
      // 活动为开始状态才跳转
      if (this.isStart) { 
        if (type == 1) {
          // 拼团
          this.$router.push({ path: `/collage/index/${id}` });
        } else if (type == 2) {
          // 秒杀
          this.$router.push({ path: `/seckill/index/${id}` });
        } else if (type == 3) {
          // 砍价
          this.$router.push({ path: `/haggle/index/${id}` });
        } else {
          this.$router.push({ path: `/coupon/get/${id}` });
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.goods-list {
  margin: 10px;
  &__item {
    background: #fff;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 6px;
    box-shadow: 0 0 5px rgba(234, 234, 234, 0.1);
  }

  &__info {
    margin-left: 10px;
    position: relative;
    flex: 1;
  }

  &__ft {
    position: absolute;
    bottom: 0;
    right: 0;
  }

  &__des {
    font-size: 12px;
    color: $gray;
    margin-top: 10px;
  }
}
</style>