<template>
  <div class="coupon">
    <navbar text="优惠劵"></navbar>
    <van-tabs :active="tabActive" bind:change="onChange" swipeable border="false">
      <van-tab title="未使用">
        <div class="coupon-not" v-for="(item, index) in couponList[0]" :key="index">
          <div class="coupon-item">
            <!-- 金额 -->
            <div class="coupon-item__l" v-if="item.money">
              <span class="coupon-item__price">{{item.money}}</span>
              <span class="f-font-sm">元</span>
            </div>

            <!-- 满减 -->
            <div class="coupon-item__c">
              <p>{{item.title}}</p>
              <p class="coupon-item__time">{{item.start_time}}-{{item.end_time}}</p>
            </div>

            <!-- 按钮 -->
            <div class="coupon-item__r">
              <button class="coupon-item__btn">去使用</button>
            </div>

          </div>
        </div>
      </van-tab>
      <van-tab title="已使用">
        <div class="coupon-done" v-for="(item, index) in couponList[1]" :key="index">
          <div class="coupon-item">
            <!-- 金额 -->
            <div class="coupon-item__l">
              <span class="coupon-item__price">{{item.money}}</span>
              <span class="f-font-sm">元</span>
            </div>

            <!-- 满减 -->
            <div class="coupon-item__c">
              <p>{{item.title}}</p>
              <p class="coupon-item__time">{{item.start_time}}-{{item.end_time}}</p>
            </div>

            <!-- 按钮 -->
            <div class="coupon-item__r">
              <button class="coupon-item__btn">已使用</button>
            </div>

          </div>
        </div>
      </van-tab>
     
    </van-tabs>
    
  </div>
</template>

<script>
import {get,wx} from "@/utils"
import navbar from "@/components/navbar";
export default {
  data () {
    return {
      tabActive: 0,
      couponList: []

    }
  },
  computed: {
    
  },

  components: {
    navbar
  },

  methods: {

  },

  onLoad () {
    get('coupon/api/personal/PHPSESSID/'+ window.localStorage.getItem('PHPSESSID')).then((res) => {
      console.log(res)
      this.couponList = res.lists
      this.couponList.forEach((item,index) => {
        item.forEach((ite,idx) => {
          ite.money = parseInt(ite.money)
        })
      })
    })
  }
}
</script>


<style lang="scss" scoped>
@mixin xy-center() {
   display: flex;
   justify-content: center;
    align-items: center;
}
  .coupon {
    padding-top: 45px;
    &-item {
      background: linear-gradient(90deg,#ff0204, #ffb3b3);
      height: 100px;
      margin: 15px;
      margin-bottom: 0;
      color: #fff;
      opacity: .8;
      font-size: 16px;
      @include xy-center;
      // 按钮
      &__btn {
        width: 50px;
        height: 18px;
        color: $red;
        font-size: 12px;
        padding:0;
        border-radius:3px;
        background: #fff;
        @include xy-center;
      }

      &__r {
        flex: 1;
        height: 100px;
        max-width: 70px;
        @include xy-center;
      }
      &__c {
        flex: 2;
      }
      &__c,
      &__l {
        padding-left: 20px;
      }

      &__price {
        font-size: 32px;
      }
      &__time {margin-top: 5px;font-size: 11px;}
    }

    // 已使用
    &-done &-item {
      background: linear-gradient(90deg,#dddddd, #f5f5f5);
    }
    &-done &-item,
    &-done &-item__btn {color: #aaa}
  }

</style>
