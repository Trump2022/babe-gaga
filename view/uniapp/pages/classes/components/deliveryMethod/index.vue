<template>
	<view>
		<!-- 选择门店 -->
		<view class="product-window on" :class="[module?'ons':'']">
			<view class="store-box">
				<view class="store-title" :class="isDisplay.length>1?'on':''">配送</view>
				<view class="iconfont icon-guanbi5" @click="module=false" />
				<view class="cart acea-row row-middle" v-if="isDisplay.length>1">
					<view class="mode acea-row row-center-wrapper" v-if="isDisplay.includes('1')"
						@click="storeNewInfofN(1)" :class="flag==1?'select':''">商城配送
					</view>
					<view class="mode acea-row row-center-wrapper" v-if="isDisplay.includes('2') && storeSelfMention"
						@click="storeNewInfofN(2)" :class="flag==2?'select':''">门店自提
					</view>
					<view class="mode acea-row row-center-wrapper" v-if="isDisplay.includes('3')"
						@click="storeNewInfofN(3)" :class="flag==3?'select':''">门店配送
					</view>
				</view>
				<view class="list-box" v-if="flag!=1">
					<view class="list acea-row row-between-wrapper" v-for="(item,index) in storeList" :key="index" @click="activeFn(index,item)"
						:class="{active:sortIndex===index}">
						<text class="iconfont icon-xuanzhong6" v-if="sortIndex===index"></text>
						<view class="left">
							<view class="name line1">{{item.name}}</view>
							<view class="adress acea-row">
								<text class="iconfont icon-dingwei2" />
								<view class="detail">{{item.detailed_address}}</view>
							</view>
							<view class="adress">
								<text class="iconfont icon-yingyeshijian2" />
								营业时间：{{item.day_time}}
							</view>
						</view>
						<view class="right">
							<view class="font-num" @click.stop="goStore(item)">进店选购</view>
							<view class="km">距离{{item.range}}km</view>
							<view class="icon acea-row row-center">
								<view class="iconfont icon-dianhua" @click.stop="call(item.phone)"></view>
								<view class="iconfont icon-dingwei2" @click.stop="showMaoLocation(item)"></view>
							</view>
						</view>
					</view>
				</view>
				<view class="btn" @click="define">确认</view>
			</view>
		</view>
		<view class="mask" @touchmove.prevent @click="module=false" v-if="module"></view>
	</view>
</template>

<script>
	export default {
		props: {
			isDisplay: {
				type: Array,
				default:()=>[]
			},
			storeList: {
				type: Array,
				default:()=>[]
			},
			storeSelfMention: {
				type: Boolean | Number,
				default: false
			}
		},
		data() {
			return {
				flag: 1,
				sortIndex: 0,
				module: false,
				storeItem: {}
			};
		},
		watch:{
			isDisplay(v) {
				this.flag = v[0]
				if(this.flag == 2 || this.flag == 3){
					this.storeNewInfofN(this.flag)
				}
			},
			storeList(v){
				this.storeItem = v[0];
			}
		},
		mounted() {},
		methods: {
			// 跳转门店
			goStore(item){
				uni.setStorageSync('user_latitude',item.latitude)
				uni.setStorageSync('user_longitude',item.longitude)
				uni.setStorageSync('store_id',item.id)
				uni.switchTab({
				  url:'/pages/store_cate/store_cate'
				})
			},
			showMaoLocation(e) {
				let self = this;
				// #ifdef H5
				if (self.$wechat.isWeixin()) {
					self.$wechat.seeLocation({
						latitude: Number(e.latitude),
						longitude: Number(e.longitude),
						name: e.name,
						scale: 13,
						address: `${e.address}-${e.detailed_address}`,
					}).then(res => {})
				} else {
					// #endif	
					uni.openLocation({
						latitude: Number(e.latitude),
						longitude: Number(e.longitude),
						name: e.name,
						address: `${e.address}-${e.detailed_address}`,
						success: function() {
							Number
						}
					});
					// #ifdef H5	
				}
				// #endif
			},
			call(phone) {
				uni.makePhoneCall({
					phoneNumber: phone,
				});
			},
			define(){
				this.$emit('deliveryFun',this.flag,this.storeItem);
			},
			// 选择配送方式
			storeNewInfofN(index) {
				this.flag = index;
				this.sortIndex = 0;
				if(index != 1){
					this.$emit('storeFun',index)
				}
			},
			// 点击高亮
			activeFn(index,item) {
				this.sortIndex = index
				this.storeItem = item
			}
		}
	}
</script>

<style lang="scss">
	.select {
		color: var(--view-theme) !important;
		background: var(--view-minorColorT) !important;
		border: 1px solid var(--view-theme) !important;
	}

	.active {
		position: relative;
		border: 1px solid var(--view-theme) !important;

		.icon-xuanzhong6 {
			font-size: 46rpx;
			position: absolute;
			bottom: -4rpx;
			right: -6rpx;
			color: var(--view-theme);
		}
	}

	.product-window {
		position: fixed;
		bottom: 0;
		width: 100%;
		left: 0;
		background-color: #f5f5f5;
		z-index: 120;
		border-radius: 16rpx 16rpx 0 0;
		transform: translate3d(0, 100%, 0);
		transition: all .3s cubic-bezier(.25, .5, .5, .9);
		padding-bottom: 140rpx;
		padding-bottom: calc(140rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
		padding-bottom: calc(140rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
		&.on{
			background-color: #fff;
		}
		&.ons{
			transform: translate3d(0, 0, 0);
		}
	}

	.product-window .cart {
		display: flex;
		margin-top: 20rpx;
		padding: 0 28rpx 20rpx 28rpx;
	}

	.product-window .cart .mode {
		margin-top: 16rpx;
		width: 220rpx;
		height: 90rpx;
		border-radius: 8rpx;
		background: #f5f5f5;
		font-size: 26rpx;
		font-weight: 400;
		color: #333;
		margin-right: 16rpx;
		border-radius: 1px solid #f5f5f5;
		&:nth-of-type(3n){
			margin-right: 0;
		}
	}

	.store-title {
		font-size: 30rpx;
		font-family: PingFangSC-Semibold, PingFang SC;
		font-weight: 600;
		color: #333333;
		text-align: center;
		padding-bottom: 15px;
	}

	.store-header {
		display: flex;
		font-size: 26rpx;
		font-weight: 400;
		color: #999999;

		.img {
			width: 28rpx;
			height: 26rpx;
			margin-right: 10rpx;
			margin-top: 4rpx;
		}

		.icon-gengduo3 {
			margin-top: 10rpx;
			margin-left: 4rpx;
			font-size: 16rpx;
		}
	}

	.store-box {
		height: 900rpx;
		position: relative;
		padding-top: 30rpx;

		.icon-guanbi5 {
			position: absolute;
			right: 30rpx;
			top: 28rpx;
			font-size: 38rpx;
			color: #888888;
		}

		.list-box {
			height: 700rpx;
			overflow-x: hidden;
			overflow-y: auto;
			background: #f5f5f5;
			padding-bottom: 20rpx;

			.list {
				width: 690rpx;
				background: #FFFFFF;
				border-radius: 12rpx;
				margin: 20rpx auto 0 auto;
				border: 1px solid #fff;
				padding: 28rpx 0 28rpx 30rpx;
						
				.left {
					width: 434rpx;

					.name {
						font-size: 28rpx;
						font-weight: 500;
						color: #333333;
					}

					.adress {
						margin-top: 18rpx;
						word-wrap: break-word;
						font-size: 22rpx;
						font-weight: 400;
						color: #888888;
						.detail{
							width: 396rpx;
						}

						.icon-dingwei2 {
							margin-right: 8rpx;
							font-size: 18rpx;
							color: #ccc;
							margin-top:4rpx;
						}

						.icon-yingyeshijian2 {
							margin-right: 8rpx;
							font-size: 18rpx;
							color: #ccc;
						}
					}
				}
				.right{
					text-align: center;
					width: 214rpx;
					height: 136rpx;
					font-size: 22rpx;
					font-weight: 400;
					.km{
						color: #999999;
						font-size: 20rpx;
						margin-top: 10rpx;
					}
					.icon{
						margin-top: 16rpx;
						.iconfont{
							width: 40rpx;
							height: 40rpx;
							background: var(--view-minorColorT);
							border-radius: 50%;
							color: var(--view-theme);
							font-size: 22rpx;
							text-align: center;
							line-height: 40rpx;
							&.icon-dingwei2{
								margin-left: 24rpx;
							}
						}
					}
				}
			}

		}
	}

	.mask {
		z-index: 0;
	}

	.btn {
		position: fixed;
		bottom: 20rpx;
		left: 30rpx;
		width: 690rpx;
		height: 86rpx;
		background: var(--view-theme);
		border-radius: 43rpx;
		text-align: center;
		line-height: 86rpx;
		font-size: 30rpx;
		font-weight: 500;
		color: #FFFFFF;
	}
</style>
