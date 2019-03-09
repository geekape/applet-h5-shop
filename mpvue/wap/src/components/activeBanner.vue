<template>
	<div class="active-banner">
		<navbar v-if="activeCover" :isClear="true"></navbar>
		<navbar v-else :text="activeInfo.title"></navbar>
		<img :src="activeCover" :alt="activeInfo.title">
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
			activeInfo: Object
		},
		components: {
			navbar
		},

		computed: {
			// 主图
			activeCover() {
				if(this.activeInfo.share_cover) {
					return this.activeInfo.share_cover
				} else if (this.activeInfo.cover != '') {
					return this.activeInfo.cover
				} else {
					return ''
				}
			}
		},
		methods: {}

	}
</script>

<style lang="scss" scoped>
	.active-banner {
		max-height: 200px;
		overflow: hidden;
		margin-bottom: 35px;
		img {width: 100%;}
	}
</style>
