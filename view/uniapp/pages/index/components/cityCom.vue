<template>
	<view class="">
		<view class="mask"></view>
		<view class="close" @click="$emit('close')"><text class='iconfont icon-guanbi'></text></view>
		<view class="city-box">
			<view class="city city-item">
				<text>当前选择</text>
				<view>{{checkedCity}}</view>
			</view>
			<view class="city city-item">
				<text>定位城市</text>
				<view class="city-label">{{curCity}}</view>
			</view>
			<view class="city city-all">
				<text>全部城市</text>
				<view class="all-city">
					<view class="city-label" v-for="item in storeList" :key="item.city_id" @click="selectCityItem(item)">{{item.city_name}}</view>
					<view class="city-label no-city"></view>
					<view class="city-label no-city"></view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { storeCities } from '@/api/store'
	export default {
		data() {
			return {
				curCity: uni.getStorageSync('curCity'),
				checkedCity: uni.getStorageSync('checkedCity'),
				storeList: []
			}
		},
		mounted() {
			this.getStoreCities()
		},
		methods: {
			getStoreCities() {
				storeCities().then(res => {
					console.log(res)
					this.storeList = res.data
				})
			},
			selectCityItem(item) {
				this.checkedCity = item.city_name
				uni.setStorageSync('checkedCity', item.city_name)
				this.$emit('close', item)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.mask {
		position: fixed;
		left: 0;
		top: 0;
		width: 100vw;
		height: 100vh;
		background-color: #0C0C0C;
		z-index: 9999;
	}
	
	.city-box {
		position: fixed;
		left: 50%;
		top: 50%;
		width: 662rpx;
		height: 900rpx;
		margin-top: -450rpx;
		margin-left: -331rpx;
		background-color: #fff;
		z-index: 10000;
		border-radius: 18rpx;
		padding: 0 30rpx;
		
		.city-item {
			height: 116rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 1rpx solid #EBEBEB;
		}
		
		.city-label {
			background-color: #F5F5F5;
			padding: 8rpx 50rpx;
			border-radius: 6rpx;
			font-size: 28rpx;
		}
		
		text {
			color: #97A1AB;
		}
		
		.city-all {
			padding-top: 30rpx;
		}
		
		.all-city {
			margin-top: 18rpx;
			display: flex;
			justify-content: space-between;
			flex-wrap: wrap;
			
			.no-city {
				height: 0;
				padding: 0 50rpx;
				// padding: 0;
				// min-width: 100rpx;
			}
			
			.city-label {
				margin-bottom: 20rpx;
			}
		}
	}
	
	.close {
		position: fixed;
		right: 46rpx;
		top: 50%;
		margin-top: -530rpx;
		width: 56rpx;
		height: 56rpx;
		border-radius: 50%;
		border: 1rpx solid #fff;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 9999;
	}
</style>