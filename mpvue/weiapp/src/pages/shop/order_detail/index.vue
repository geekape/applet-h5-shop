<template>
  <div class="order-detail" :class="{'pb80': info.status_code == 3 || info.pay_status === 0}" v-if="orderId">
        <a :href="'../logistics/index?order_id=' + orderId" class="m-list link log" v-if="steps && info.send_type == 1">
        <div class="m-list__l">
        <p>{{info.status_code_name}}</p>
        <p>物流信息：{{steps.remark}}</p>
        <small class="log-time">{{stepscTime}}</small>
      </div>
      <div class="m-list__c"></div>
      <i class="iconfont icon-fanhui right"></i>
      </a>

    <div class="m-list order-line" v-if="info.send_type == 1">
			<div class="m-list__l">{{address.truename || '地址被删除啦！'}}</div>
			<div class="m-list__c">
				<p class="">{{address.mobile || ''}}</p>
				<p class="f-font-sm">{{address.address + address.address_detail || ''}}</p>
			</div>
			<div class="m-list__r">
			</div>
		</div>

    <div class="shop-list__item" v-if="info.send_type == 2">
      <div class="shop-list__bd g-flex">
        <img lazy-load class="u-goods__img" :src="shops.img_url" />
        <div class="g-flex__flex">
          <p class="shop-list__name">{{shops.name}}</p>
          <p class="shop-list__address" v-if="shops.address"><span class="iconfont icon-dingwei"></span>{{shops.address}}</p>
          <p class="shop-list__dist" v-if="shops.shop_code"><span class="iconfont icon-daohang"></span></p>
        </div>
      </div>
      <div class="shop-list__ft g-flex">
        <p v-if="shops.phone" @click="callPhone(shops.phone)" class="g-flex__item"><span class="iconfont icon-phone"></span>电话</p>
        <p v-if="shops.address" @click="callMap(shops.address)" class="g-flex__item"><span class="iconfont icon-daohang"></span>导航</p>
      </div>
    </div>

    <div class="order-detail__item">
      <a :href="'../goods_detail/index?id=' + item.id"
      class="goods-line" 
      v-for="(item,index) in goods"
      :key="index">
          <img lazy-load class="u-goods__img" :src="item.cover"/>

          <div class="goods-line__right">
            <p class="u-goods__tt overflow-dot">{{item.title}}</p>
            <div class="goods-line__ft">
              <div class="goods-line__price">
                <span>¥{{item.sale_price}}</span>
                </div>
              <p class="f-font-sm">x{{item.num}}</p>
            </div>
          </div>
        </a>
    </div>

    <div class="weui-form-preview">
        <div class="weui-form-preview__hd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">付款金额</label>
                <em class="weui-form-preview__value">¥{{totalPrice}}</em>
            </div>
        </div>
        <div class="weui-form-preview__bd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">买家留言</label>
                <span class="weui-form-preview__value">{{info.remark ? info.remark : '无'}}</span>
            </div>
            <div class="weui-form-preview__item" v-if="info.send_type != 2">
                <label class="weui-form-preview__label">邮费：</label>
                <span class="weui-form-preview__value">¥{{info.mail_money || 0}}</span>
            </div>
            <div class="weui-form-preview__item" v-if="info.dec_money>0">
                <label class="weui-form-preview__label">优惠卷:</label>
                <span class="weui-form-preview__value">- ¥{{info.dec_money}}</span>
            </div>

            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">配送方式：</label>
                <span class="weui-form-preview__value">{{info.send_type == 2 ? '自提' : '邮寄'}}</span>
            </div>
        </div>
    </div>


    <!-- 订单信息 -->
    <div class="m-card order-detail__item" v-if="info.pay_status != 0">
      <div class="m-card__tt"><div class="line"></div>订单信息</div>
      <div class="m-card__list">
        <div class="m-card__item">
          订单编号：{{info.order_number}}
        </div>
        <div class="m-card__item">
          支付方式：{{info.common}}
        </div>
        <div class="m-card__item">
          下单时间：{{payTime}}
        </div>
        <div class="m-card__item" v-show="info.refund>0">
          退款状态：{{info.refund_title}}
        </div>
      
      </div>

      <a :href="'../refund/index?order_id=' + info.id" v-if="info.status_code == 3 || info.refund == 0" class="u-button u-button--border">申请退款</a>
    </div>

    <div class="u-fixed">
      <button class="u-button u-button--primary u-button--big" v-if="info.status_code == 3" @click="goReceiving(orderId)">确认收货</button>
      <button class="u-button u-button--primary u-button--big" v-if="info.pay_status == 0" @click="goPay(info.out_trade_no,info.total_price)">立即付款</button>
    </div>
    <van-toast id="van-toast" />
   <van-dialog id="van-dialog" />
  </div>
</template>

<script>
import {post, timeChange, goPay, goReceiving} from "@/utils"
import Toast from "@/../static/vant/toast/toast";
import Dialog from "@/../static/vant/dialog/dialog";
export default {
  mpType: 'page',
  data () {
    return {
      orderId: 0,
      steps: [],
      info: [],
      goods: [],
      address: [],
      shops: [],
      payTime: '',
      stepscTime:''
    }
  },
  computed: {
    totalPrice () {
      return parseFloat(this.info.total_price) + parseFloat(this.info.mail_money) - parseFloat(this.info.dec_money)
    },

  },

  components: {
    
  },

  methods: {
    goPay(id, price) {
      goPay(id)
    },
    goReceiving(id) {
      goReceiving(id)
    },
    // 打电话
    callPhone(phone) {
      wx.makePhoneCall({phoneNumber: phone})
    },
    // 打开地图
    callMap(map) {
      
    }
  },

  onLoad () {
    Object.assign(this, this.$options.data());
    this.orderId = this.$root.$mp.query.order_id || 0
    const _this = this
    post('shop/api/order_detail', {
      id: _this.orderId,
      PHPSESSID: wx.getStorageSync('PHPSESSID')
    }).then((res) => {
      console.log('resres', res)

      _this.steps = res.log
      _this.info = res.info
      

      _this.address = res.addressInfo
			_this.goods = res.info.goods
      _this.shops = res.store_info
      
      // 处理订单信息时间戳
      _this.payTime = timeChange(_this.info.pay_time)
      _this.stepscTime = timeChange(_this.steps.cTime)
      
    })
  }
}
</script>


<style lang="scss" scoped>

.order-detail.pb80 {
  padding-bottom: 80px;
}
.order-detail {
  .weui-form-preview {
    margin-top: 10px;
    &:before,&:after {border: 0}
  }
  /deep/ .weui-form-preview__hd {
    line-height: 1.5em;
  }
  // 我的地址
	.order-line {
		padding: 25px 15px;
    background: #FFFDF4;
    align-items: baseline;
    background-image: url($imgRoot +'stripe-bg.png');
    background-repeat: no-repeat;
    background-size: 100%;
    margin-top: 10px;
	}
  /deep/ .u-goods__img {
    margin-right: 15px;
    margin-right: 10px;
  }

  .u-fixed {
    position: fixed;
    bottom: 0px;
    background: #f9f9f9;
    width: 100%;
  }
  &__item {
    background: #fff;
    margin-top: 10px;
    padding: 15px;
    overflow: hidden;
  }
  .u-button--primary {margin-top: 0;}
  .u-button--border {
    float: right;
    font-size: 12px;
  }
  /deep/ .goods-line {
    padding: 0;
  }

  .m-list.log {
    background: #637f8a;
    color: #fff;
    font-size: 14px;
    padding: 30px 15px;
    .icon-fanhui {color: #fff}
  }
  .log-time {
    padding-top: 10px;
    font-size: 12px;
    display: block;
  }
  .m-list__group {
    margin-top: 10px;
  }

}
.m-card {
  padding: 15px;
  font-size: 14px;
  &__tt {
    padding-left: 7px;
    position: relative;
    margin-bottom: 15px;
  }
  &__tt .line {
    height: 10px;
    width: 2px;
    background: $red;
    position: absolute;
    left: 0;
    top: calc(50% - 5px)
  }
  &__list {
    line-height: 1.8;
  }
}

.shop-list {
  &__item {
    background: #fff;
    margin-top: 10px;
  }
  &__bd {
    padding: 15px;
    .g-flex__flex {
      justify-content: center;
      display: flex;
      flex-direction: column;
    }
  }
  &__ft {
    padding: 10px 0;
    border-top: 1px solid #eee;
    font-size: 16px;
    .g-flex__item {text-align: center;}
    .iconfont {color: $red;margin-right: 2px;}
  }
  
  &__name {margin-bottom: 10px;}
  &__address {
    font-size: 14px;
    color: #999;
  }
}
</style>
