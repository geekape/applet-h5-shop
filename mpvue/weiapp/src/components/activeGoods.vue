<template>
	<div class="active-goods">
		<div class="active-goods__item" v-for="(item,index) in activeData" :key="index">
			<div class="active-goods__img">
				<img lazy-load :src="item.cover" alt="">
				<div class="u-time-bar" v-if="activeTime">剩余时间{{activeTime}}</div>
				<div class="u-time-bar due" v-else>活动已到期</div>
			</div>

			<div class="active-goods__content">
				<div class="active-goods__left">
					<p class="active-goods__tt overflow-dot">{{item.title}}</p>
					<p class="active-goods__price s-red"><span class="f-mr-xs">¥</span>{{item.sale_price}}
						<span class="u-badge u-badge--sm">{{activeType}}</span>
					</p>
					<p class="active-goods__price_cost"><span class="f-mr-xs">¥</span>{{item.market_price}}</p>
				</div>

				<a :href="'../detail/index?id='+item.id" class="u-button u-button--primary" v-if="activeTime">{{activeType}}</a>
				<button class="u-button u-button--disable2" v-else>{{activeType}}</button>
			</div>

		</div>
	</div>
</template>

<script>
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
		padding-bottom: 15px;

		&__item {
			border-radius: 6px;
			margin: 15px;
			margin-bottom: 0;
			background: #fff;
			border-radius: 5px;
		}

		.u-badge {
			display: inline;
		}

		// 商品图
		&__img {
			position: relative;
			min-height: 200px;
			overflow: hidden;
			img,image {
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
