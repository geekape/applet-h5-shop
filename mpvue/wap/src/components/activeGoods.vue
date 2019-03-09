<template>
	<div class="active-goods">
		<template v-if="activeData">
			<div class="active-goods__item" v-for="(item,index) in activeData" :key="index">
				<div class="active-goods__img">
					<img :src="item.cover">
					
					<div class="u-time-bar" v-if="activeTime">剩余时间{{activeTime}}</div>
					<div class="u-time-bar due" v-else>活动已到期</div>
				</div>

				<div class="active-goods__content">
					<div class="active-goods__left">
						<p class="active-goods__tt u-goods__tt overflow-dot">{{item.title}}</p>
						<p class="active-goods__price s-red"><span class="f-mr-xs">¥</span>{{item.sale_price}}
							<span class="u-badge u-badge--sm">{{activeType}}</span>
						</p>
						<p class="active-goods__price_cost"><span class="f-mr-xs">¥</span>{{item.market_price}}</p>
					</div>
					<router-link :to="'/collage/collage_detail/' + item.id" class="u-button u-button--primary" v-if="activeTime && activeType == '拼团'">{{activeType}}</router-link>
					<router-link :to="'/seckill/detail/' + item.id" class="u-button u-button--primary" v-else-if="activeTime  && activeType == '秒杀'">{{activeType}}</router-link>
					<button class="u-button u-button--disable2" v-else>{{activeType}}</button>
				</div>

			</div>
		</template>

		<div class="hint-page" v-else>
		<img src="~images/nothing.png" alt="">
		<p class="hint-page__text">活动不存在或者已过期</p>
		</div>
	</div>
</template>

<script>
import navbar from "@/components/navbar";
	export default {
		data() {
			return {
			};
		},
		props: {
			activeType: String,
			activeData: Array,
			activeInfo: Object,
			activeTime: String
		},

		computed: {
			
		},
		methods: {}

	}
</script>

<style lang="scss" scoped>
	.active-goods {
		&__item {
			margin: 15px;
			margin-bottom: 0;
			background: #fff;
			border-radius: 6px;
    		box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
		}

		.u-badge {
			display: inline;
		}

		// 商品图
		&__img {
			position: relative;
			min-height: 200px;
			overflow: hidden;

			img {
				position: absolute;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				margin: auto;
			}
		}

		// 内容
		&__content {
			padding: 15px;
			display: flex;
			align-items: center;
		}

		&__left {
			flex: 1
		}

		&__tt {
			margin-bottom: 10px;
		}

		&__price_cost {
			font-size: 12px;
			text-decoration: line-through;
			color: #aaa;
		}
	}
</style>
