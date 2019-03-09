<template>
  <div class="comment">
    <div class="comment-item" v-for="(item, index) in commentList" :key="index">
      <div class="comment-item__hd">
        <img lazy-load class="u-head__img" :src="userInfo.avatarUrl" />
        <p class="comment-item__name">{{userInfo.name}}</p>
        <p class="comment-item__time">{{item.cTime}}</p>
      </div>
      <div class="comment-item__bd">
        {{item.content}}
      </div>
      <div class="comment-item__ft">
        <a class="goods-line">
          <img lazy-load class="u-goods__img" :src="item.goods_img"/>

          <div class="goods-line__right">
            <p class="u-goods__tt overflow-dot_row">{{item.goods_title}}</p>
            <div class="goods-line__ft">
              <div class="goods-line__price">
                <span>¥{{item.sale_price}}</span>
                </div>
            </div>
          </div>
        </a>
        </div>
    </div>


  </div>
</template>

<script>
import {post,get,dateDiff} from "@/utils"
export default {
  data () {
    return {
      comments: [],
      userInfo: wx.getStorageSync('userInfo')
    }
  },
  computed: {
    // 时间处理
    commentList () {
      let arr = []
      let { keys, values, entries } = Object;
      for (let [key, value] of entries(this.comments)) {
        value.cTime = dateDiff(value.cTime)
        arr.push(value)
      }
      return arr
    }
  },

  methods: {

  },

  onLoad () {
    var _this = this
    post('shop/api/my_comment', {
      PHPSESSID: wx.getStorageSync('PHPSESSID'),
      uid: 1
    }).then(res => {
      _this.comments = res.lists
    })
  }
}
</script>


<style lang="scss" scoped>
.comment-item {
    background: #fff;
    padding: 15px;
    margin-top: 10px;

    &__hd {
      display: flex;
    }
    &__name {
      font-size: 14px;
      flex: 1;
      margin-left: 10px;
    }
    &__time {
      float: right;
      font-size: 12px;
      color: $gray;
    }
    &__name,&__time {line-height: 30px;}
    &__bd {
      margin-top: 5px;
      font-size: 16px;
    }
    &__ft {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #eee;
      /deep/ .u-goods__tt {
        font-size: 14px;
        max-width: 300PX;
      }
      /deep/ .u-goods__price{font-size: 14PX}
      .goods-line {padding: 0}
      .u-goods__img {width: 50px;min-width: 50px;height: 50px;}
    }

  }


</style>
