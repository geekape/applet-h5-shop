<template>
	<div class="goods-detail" ref="top_div">
		<navbar :text="goods.title"></navbar>
		<scroller v-if="goods.id">
			<!-- 商品图 -->
			<div class="slide">
				<van-swipe show-indicators @change="toggleSwiper">
					<van-swipe-item v-for="(item,index) in slides" :key="index">
						<a class="slide-url pic-align-center" @click="pvwImg(index)">
							<img class="slide-image" :src="item">
						</a>
					</van-swipe-item>
					<div v-if="slides.length>0" class="custom-indicator" slot="indicator">{{ current + 1 }}/{{totalSwiperNum}}</div>
				</van-swipe>
			</div>

			<!-- 商品信息 -->
			<div class="goods-detail__info" v-if="goods.sale_price">
				<p class="goods-detail__price s-red">
					<span class="icon-price">¥</span>
					{{goods.sale_price}}
				</p>
				<p class="goods__price-cost" v-if="goods.market_price>goods.sale_price">¥{{goods.market_price}}</p>
				<p class="s-gray goods-detail__stock">库存{{goods.stock_active}}件</p>
				<p class="goods-detail__tt">{{goods.title}}</p>
			</div>

			<div @click="jump(goods.id, goods.tab)" class="m-list link g-flex" v-if="goods.tab">
				<div class="m-list__l g-flex__item">同款</div>
				<i class="iconfont icon-fanhui right"></i>
			</div>

			<!-- switch卡片 -->
			<div class="switch-card" v-if="goodsParam.length>0">
				<div class="switch-card__hd">
					<p class="switch-card__tt">产品参数</p>
					<p class="switch-card__icon iconfont icon-fanhui" :class="arrowDir" @click="toggleArrow"></p>
				</div>
				<div class="switch-card__bd" v-show="arrowDir == 'top'">
					<div class="switch-card__item" v-for="(param, paramIdx) in goodsParam" :key="paramIdx">
						<p class="switch-card__param overflow-dot_row">{{param.title}}</p>
						<p class="switch-card__attr overflow-dot_row">{{param.param_value}}</p>
					</div>
				</div>
			</div>

			<!-- 评价 -->
			<div class="goods-comment" v-if="goods.comment_count>0">
				<div class="m-list link">
					<div class="m-list__l">评价1</div>
					<p class="m-list__c s-black" v-show="goods.comment_count>10">查看更多</p>
					<i class="iconfont icon-fanhui right" v-show="goods.comment_count>10"></i>
				</div>
				<div class="goods-comment__bd">
					<div class="goods-comment__item" v-for="(comment, commentIdx) in goods.comments" :key="commentIdx">
						<div class="goods-comment__left">
							<div class="g-flex g-flex__updown-center">
								<img class="u-head__img" :src="comment.headimgurl">
								<p class="goods-comment__name">{{comment.username}}</p>
							</div>
							<p class="goods-comment__text">{{comment.content}}</p>
						</div>

						<div class="goods-comment__right">
							<img class="u-goods__img" :src="slides[0]">
						</div>
					</div>
				</div>
			</div>

			<!-- 详情图片 -->
			<div v-html="goods.content" class="goods-detail__pic" v-if="goods.content"></div>

			<!-- diy 组件 -->
			<diyGoods :diyData="diyData" v-if="diyData"></diyGoods>
		</scroller>

		<!-- 底部栏 -->
		<div class="bottom-bar" v-if="slides.length>0">
			<router-link to="/service" open-type="contact" class="bottom-bar__service">
				<div class="bottom-bar__icon"></div>
				<p class="bottom-bar__tt">客服</p>
			</router-link>
			<div class="bottom-bar__collect" @click="toggleCollect">
				<div class="bottom-bar__icon" v-show="!isCollect"></div>
				<div class="bottom-bar__icon--active" v-show="isCollect"></div>
				<p class="bottom-bar__tt">收藏</p>
			</div>
			<router-link to="/cart" open-type="switchTab" class="bottom-bar__cart">
				<div class="bottom-bar__icon"></div>
				<span v-if="cartNum>0" class="weui-badge" style="position: absolute;top: .2em;right: .2em;">{{cartNum}}</span>
				<p class="bottom-bar__tt">购物车</p>
			</router-link>

			<button class="u-button u-button--border" @click="addCart">加入购物车</button>
			<button @click="buy" class="u-button u-button--primary">立即购买</button>
		</div>
	</div>
</template>

<script>
	import {
		post,
		host,
		wx
	} from "@/utils";
	import navbar from "@/components/navbar";
	import diyGoods from "@/components/diyGoodsDetail";
	import {
		ImagePreview,
		Toast
	} from "vant";
	export default {
		data() {
			return {
				current: 0,
				slides: [],
				goods: [],
				isCollect: false,
				arrowDir: "top",
				selfSwiperNum: 1,
				isCartDot: false,
				detailPic: "",
				goodsParam: [],
				diyData: {}
			};
		},
		components: {
			navbar,
			diyGoods
		},
		computed: {
			totalSwiperNum() {
				return this.slides.length;
			},
			cartNum() {
				let num = this.$store.state.cartShopNum;
				if (num > 0) {
					this.isCartDot = true;
					return num;
				}
			}
		},
		methods: {
			toggleSwiper(index) {
				this.current = index;
			},
			jump(ids, tabs) {
				this.$router.push({
					name: "lists",
					params: {
						id: ids,
						tab: tabs
					}
				});
			},
			// 购买
			buy() {
				let goodsId = this.goods.goods_id;
				// 库存为0
				if (this.goods.stock_active == 0) {
					Toast("该商品已经被抢光了");
					return false;
				}
				this.$router.push({
					path: `/confirm_order/${goodsId}`
				});
			},
			// 切换箭头方向
			toggleArrow() {
				this.arrowDir == "top" ?
					(this.arrowDir = "bottom") :
					(this.arrowDir = "top");
			},

			addCart() {
				var _this = this;

				this.$http
					.post(host + "shop/api/addToCart", {
						goods_id: this.goods.id,
						PHPSESSID: window.localStorage.getItem("PHPSESSID")
					})
					.then(res => {
						if (res.data > 0) {
							Toast("加入购物车成功");

							_this.$store.commit("getCartShopNum", {
								num: res.data
							});
						} else {
							Toast("加入购物车失败,请直接下单购买");
						}
					});
			},
			pvwImg(idx) {
				// 预览图片
				ImagePreview({
					images: this.slides,
					startPosition: idx
				});
			},
			toggleCollect(showHint) {
				// 收藏
				this.$http
					.post(host + "shop/api/addtocollect", {
						goods_id: this.goods.id,
						PHPSESSID: window.localStorage.getItem("PHPSESSID")
					})
					.then(res => {
						if (res.data == 1) {
							Toast("加入收藏成功");
							this.isCollect = true;
						} else {
							Toast("取消收藏成功");
							this.isCollect = false;
						}
					});
			}
		},


		activated() {
			if (!this.$route.meta.isBack) {
				Object.assign(this.$data, this.$options.data())
				this.$refs.top_div.style.background = "#f9f9f9";
				const _this = this;
				const id = this.$route.params.id;
				// 清空活动信息
				this.$store.commit("saveData", {
					key: "activeOrderParams",
					value: ""
				});
				post("shop/api/goods_detail", {
					id: id
				}).then(res => {
					// 商品图
					this.slides = res.goods.imgs_url;
					this.goods = res.goods;
					this.goodsParam = res.goods.goods_param;

					if (this.goods.diyData.config) {
						this.diyData = JSON.parse(
							decodeURIComponent(this.goods.diyData.config)
						);

						let {
							title,
							bgColor
						} = this.diyData[0]["params"];
						if (title) {
							//微页面标题
							this.goods.title = title;
						}
						if (bgColor) {
							//自定义页面背景颜色
							this.$refs.top_div.style.background = bgColor;
						}
					}

					if (res.goods.is_collect == 0) {
						this.isCollect = false;
					} else {
						this.isCollect = true;
					}
				});
			}
			this.$route.meta.isBack = false;
		},
		beforeRouteEnter(to, from, next) {
			if (from.name == "confirm_order") {
				to.meta.isBack = true
			}
			next()
		},
	};
</script>
<style lang="scss" scoped>
	@import "../../../../static/styles/mixin.scss";

	.navbar {
		background: $body-bg;
	}

	.goods-detail {
		padding-top: 45px;
		padding-bottom: 55px;
		overflow: hidden;
		height: auto !important;

		/deep/ ._v-container>._v-content {
			padding-bottom: 90px;
		}

		.van-swipe {
			position: relative;
			height: 10rem;
		}

		.custom-indicator {
			position: absolute;
			right: 10px;
			bottom: 10px;
		}

		/deep/ .icon-fanhui {
			font-size: 14px;
		}

		&__pic {
			background: #fff;
			font-size: 14px;
			margin-top: 15px;

			/deep/ p {
				padding: 10px;
			}

			/deep/ table,
			/deep/ img {
				width: 100%;
			}
		}

		.slide {
			position: relative;
			height: 375px;
			overflow: hidden;

			&-count {
				position: absolute;
				right: 10px;
				bottom: 10px;

				.s-gray {
					font-size: 12px;
				}
			}

			.slide-url {
				background: #fff;
			}
		}

		// 商品信息
		&__info {
			padding: 18px;
			background: #fff;
		}

		&__price {
			font-size: 20px;
			margin-right: 5px;
		}

		&__price,
		.goods__price-cost {
			display: inline-block;
		}

		&__tt {
			font-size: 16px;
		}

		&__cell {
			display: block;
		}

		&__tt,
		&__cell {
			margin-top: $box-size;
		}

		// 库存
		&__stock {
			font-size: 12px;
			display: inline-block;
			float: right;
			margin-top: 10px;
		}
	}

	// 商品评价
	.goods-comment {
		margin-top: $box-size;
		background: #fff;

		&__bd {
			padding: 10px 15px;
			white-space: nowrap;
			width: 100%;
			display: block;
			overflow-x: scroll;
			box-sizing: border-box;
			-webkit-overflow-scrolling: touch;
		}

		&__item {
			display: -webkit-inline-box;
			align-items: center;
			border: 1px solid #ececec;
			width: 300px;
			margin-right: 10px;
			overflow: hidden;
			border-radius: 3px;
			height: 130px;

			&:last-child {
				margin-right: 0;
			}
		}

		&__left {
			margin: 15px 10px 15px;
			flex: 1;
		}

		&__right {
			background: #f9f9f9;
			padding: 15px 10px;
		}

		&__name {
			font-size: 14px;
			margin-left: 5px;
		}

		&__text {
			margin-top: 10px;
			font-size: 14px;
			@include overflow-dot(3);
			white-space: normal;
		}

		/deep/ .u-goods__img {
			background-color: #ddd;
		}
	}

	// 底部购物栏
	.bottom-bar {
		display: flex;
		align-items: center;
		box-shadow: 1px 0 10px rgba(0, 0, 0, 0.1);
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		height: 55px;
		background: #fff;

		&__collect &__icon {
			background-image: url("~images/new_icon/icon_heart.png");
		}

		&__collect &__icon--active {
			background-image: url("~images/new_icon/icon_heart_active.png");
		}

		&__service &__icon {
			background-image: url("~images/new_icon/icon_service2.png");
		}

		&__cart &__icon {
			background-image: url("~images/new_icon/icon_cart2.png");
		}

		&__icon--active,
		&__icon {
			position: relative;
			width: 30px;
			height: 30px;
			background-repeat: no-repeat;
			background-size: 100% 100%;
			zoom: 0.5;
			margin: 0 auto;
		}

		&__collect,
		&__service,
		&__cart {
			width: 24%;
			height: 55px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			cursor: pointer;
			position: relative;
		}

		&__tt {
			font-size: 12px;
			text-align: center;
			margin-top: 2px;
		}

		/deep/ .u-button {
			min-width: 95px;
			margin-left: 10px;
			padding: 0 10px;
		}

		/deep/ .u-button--primary {
			margin-right: 10px;
		}
	}

	.scroll-view {
		touch-action: none;
		position: fixed;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
		padding: 1rem;
		overflow: hidden;
	}

	.mutipic_banner_title {
		background-color: RGBA(0, 0, 0, 0.5);
		height: 30px;
		color: #fff;
		line-height: 30px;
		padding-left: 10px;
		position: absolute;
		left: 0;
		bottom: 0;
		width: 100%;
		z-index: 1000;
	}
</style>
