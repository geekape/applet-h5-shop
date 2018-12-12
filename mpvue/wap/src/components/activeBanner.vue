<template>
	<div class="active-banner">
		<img v-if="activeInfo.cover" :src="activeInfo.cover" :alt="activeInfo.title">
		<img v-else src="">
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
	}
</style>
