<template>
  <div class="comment">
    <navbar text="我的评价"></navbar>
    <div class="comment-item" v-for="(item, index) in commentList" :key="index">
      <div class="comment-item__hd">
        <img class="u-head__img" :src="userInfo.headimgurl" />
        <p class="comment-item__name">{{userInfo.nickname}}</p>
        <p class="comment-item__time">{{item.cTime}}</p>
      </div>
      <div class="comment-item__bd">
        {{item.content}}
      </div>
      <div class="comment-item__ft">
        <a :href="'../goods_detail/main/id=' + item.goods_id" class="goods-line">
          <img class="u-goods__img" :src="item.goods_img"/>

          <div class="goods-line__right">
            <p class="u-goods__tt overflow-dot_row">{{item.goods_title}}</p>
            <div class="goods-line__ft">
              <div class="goods-line__price">
                <span class="u-goods__price">¥{{item.sale_price}}</span>
                </div>
            </div>
          </div>
        </a>
        </div>
    </div>


  </div>
</template>

<script>
import {post,get,wx, dateDiff} from "@/utils"
import navbar from "@/components/navbar";
export default {
  data () {
    return {
      comments: [],
      userInfo: JSON.parse(window.localStorage.getItem('userInfo'))
    }
  },
  components: {
    navbar
  },
  computed: {
    // 时间处理
    commentList () {
      let arr = this.comments
      arr.forEach((item,idx) => {
        item.cTime = dateDiff(item.cTime)
      })
      return arr
    }
  },

  methods: {

  },

  created () {
    post('shop/api/my_comment', {
      PHPSESSID: window.localStorage.getItem('PHPSESSID'),
      uid: 1
    }).then(res => {
      this.comments = res.lists
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
  .comment {padding-top: 45px;}

</style>
