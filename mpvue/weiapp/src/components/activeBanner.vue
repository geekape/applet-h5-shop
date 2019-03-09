<template>
	<div class="active-banner">
		<img lazy-load :src="activeInfo.cover" :alt="activeInfo.title">
	</div>
</template>

<script>
	export default {
		data() {
			return {
			};
		},
		props: {
			activeInfo: Object
		},

		computed: {
			
		},
		methods: {}

	}
</script>

<style lang="scss" scoped>
	.active-banner {
		max-height: 200px;
		overflow: hidden;
		image {width: 100%;}
	}
</style>
