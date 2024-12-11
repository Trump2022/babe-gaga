<template>
	<view class="load-more">
		{{loadText}}
	</view>
</template>

<script>
	export default {
		props: {
			loadText: {
				type: String,
				default: () => '加载更多'
			}
		}
	}
</script>

<style scoped lang="scss">
	.load-more {
		text-align: center;
		height: 76rpx;
		line-height: 76rpx;
		color: #B3B3B3;
		font-size: 24rpx;
		position: relative;
		
		&::before {
			content: '';
			display: block;
			width: 180rpx;
			height: 1rpx;
			background-color: #ddd;
			position: absolute;
			left: 82rpx;
			top: 50%;
		}
		
		&::after {
			content: '';
			display: block;
			width: 180rpx;
			height: 1rpx;
			background-color: #ddd;
			position: absolute;
			right: 82rpx;
			top: 50%;
		}
	}
</style>