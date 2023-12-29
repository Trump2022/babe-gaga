<template>
	<!-- 搜索框 -->
	<!-- #ifdef H5  -->
	<view class="header"
		:style="'background: linear-gradient(90deg, '+ bgColor[0].item +' 50%, '+ bgColor[1].item +' 100%);margin-top:'+ mbConfig*2 +'rpx;'">
		<view class="serch-wrapper acea-row row-middle"
			:class="(tabVal===0 || tabVal===1) && txtStyle===1?'center':(tabVal===0 || tabVal===1) && txtStyle===2?'right':''"
			:style="'padding-left:'+ prConfig*2 +'rpx;padding-right:'+ (tabVal===0 || tabVal===1?prConfig*2:20) +'rpx;'">
			<view class="logo skeleton-rect" v-if="logoConfig && tabVal===2 && radioVal===2">
				<image :src="logoConfig" mode="heightFix"></image>
			</view>
			<view class="title" :class="tabVal===2?'on':''"
				:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
				v-if="tabVal===0 || (tabVal===2 && radioVal === 0)" @click="goLink">{{titleConfig}}</view>
			<view class="map acea-row row-middle" @click="chooseLocation"
				:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
				v-if="tabVal===1 || (tabVal===2 && radioVal === 1)">
				<text class="iconfont icon-dingwei4" v-if="storeVal != 0"></text>
				<view class="info on" v-if="tabVal===1 && storeVal == 1">{{addressInfos}}</view>
				<view class="info on" v-if="tabVal===1 && storeVal == 0">{{storeName.slice(0,7) || '选择门店'}}</view>
				<view class="info on1" v-if="tabVal===2 && storeVal == 1">{{addressInfo}}</view>
				<view class="info on1" v-if="tabVal===2 && storeVal == 0">{{storeName.slice(0,4) || '选择门店'}}</view>
				<text class="iconfont icon-xiangxia"></text>
			</view>
			<navigator v-if="tabVal===2" url="/pages/goods/goods_search/index"
				class="input acea-row row-middle skeleton-rect"
				:class="[boxStyle?'':'fillet',logoConfig?'':'on',txtStyle===1?'row-center':txtStyle===2?'row-right':'']"
				hover-class="none"><text class="iconfont icon-sousuo"></text>
				搜索商品</navigator>
		</view>
	</view>
	<!-- #endif -->
	<!-- #ifdef MP || APP-PLUS -->
	<view v-if="special" class="header"
		:style="'background: linear-gradient(90deg, '+ bgColor[0].item +' 50%, '+ bgColor[1].item +' 100%);'">
		<view class="serch-wrapper acea-row row-middle"
			:class="(tabVal===0 || tabVal===1) && txtStyle===1?'center':(tabVal===0 || tabVal===1) && txtStyle===2?'right':''"
			:style="'padding-left:'+ prConfig*2 +'rpx;padding-right:'+ (tabVal===0 || tabVal===1?prConfig*2:20) +'rpx;'">
			<view class="logo skeleton-rect" v-if="logoConfig && tabVal===2 && radioVal===2">
				<image :src="logoConfig" mode="heightFix"></image>
			</view>
			<view class="title" :class="tabVal===2?'on':''"
				:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
				v-if="tabVal===0 || (tabVal===2 && radioVal === 0)" @click="goLink">{{titleConfig}}</view>
			<view class="map acea-row row-middle" @click="chooseLocation"
				:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
				v-if="tabVal===1 || (tabVal===2 && radioVal === 1)">
				<text class="iconfont icon-dingwei4" v-if="storeVal != 0"></text>
				<view class="info on" v-if="tabVal===1 && storeVal == 1">{{addressInfos}}</view>
				<view class="info on" v-if="tabVal===1 && storeVal == 0">{{storeName.slice(0,7) || '选择门店'}}</view>
				<view class="info on1" v-if="tabVal===2 && storeVal == 1">{{addressInfo}}</view>
				<view class="info on1" v-if="tabVal===2 && storeVal == 0">{{storeName.slice(0,4) || '选择门店'}}</view>
				<text class="iconfont icon-xiangxia"></text>
			</view>
			<navigator v-if="tabVal===2" url="/pages/goods/goods_search/index"
				class="input acea-row row-middle skeleton-rect"
				:class="[boxStyle?'':'fillet',logoConfig?'':'on',txtStyle===1?'row-center':txtStyle===2?'row-right':'']"
				hover-class="none"><text class="iconfont icon-sousuo"></text>
				搜索商品</navigator>
		</view>
	</view>
	<view v-else>
		<view class="mp-header"
			:style="'background: linear-gradient(90deg, '+ bgColor[0].item +' 50%, '+ bgColor[1].item +' 100%);'">
			<view class="sys-head" :style="{ height: statusBarHeight + 'px' }"></view>
			<view class="serch-box" style="height: 43px;">
				<view class="serch-wrapper acea-row row-middle"
					:class="(tabVal===0 || tabVal===1) && txtStyle===1?'center':(tabVal===0 || tabVal===1) && txtStyle===2?'right':''"
					:style="'padding-left:'+ prConfig*2 +'rpx!important;padding-right:'+ (tabVal===0 || tabVal===1?prConfig*2:20) +'rpx;'">
					<view class="logo skeleton-rect" v-if="logoConfig && tabVal===2 && radioVal===2">
						<image :src="logoConfig" mode="heightFix"></image>
					</view>
					<view class="title" :class="tabVal===2?'on':''"
						:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
						v-if="tabVal===0 || (tabVal===2 && radioVal === 0)" @click="goLink">{{titleConfig}}</view>
					<view class="map acea-row row-middle" @click="chooseLocation"
						:style="{color:`${textColor}`,fontStyle:`${textStyle!='bold'?textStyle:''}`,fontWeight:`${textStyle=='bold'?textStyle:''}`}"
						v-if="tabVal===1 || (tabVal===2 && radioVal === 1)">
						<text class="iconfont icon-dingwei4" v-if="storeVal != 0"></text>
						<view class="info on" v-if="tabVal===1 && storeVal == 1">{{addressInfos}}</view>
						<view class="info on" v-if="tabVal===1 && storeVal == 0">{{storeName.slice(0,7) || '选择门店'}}</view>
						<view class="info on1" v-if="tabVal===2 && storeVal == 1">{{addressInfo}}</view>
						<view class="info on1" v-if="tabVal===2 && storeVal == 0">{{storeName.slice(0,4) || '选择门店'}}</view>
						<text class="iconfont icon-xiangxia"></text>
					</view>
					<navigator v-if="tabVal===2" url="/pages/goods/goods_search/index"
						class="input acea-row row-middle skeleton-rect"
						:class="[boxStyle?'':'fillet',logoConfig?'':'on',txtStyle===1?'row-center':txtStyle===2?'row-right':'']"
						hover-class="none">
						<text class="iconfont icon-sousuo"></text>
						搜索商品
					</navigator>
				</view>
			</view>
		</view>
		<view :style="'height:'+(statusBarHeight+43)+'px;'"></view>
		<!-- #ifdef MP -->
		<privacyAgreementPopup v-if="canGetPrivacySetting && (tabVal===1 || (tabVal===2 && radioVal === 1))" @onAgree="onAgree">
		</privacyAgreementPopup>
		<!-- #endif -->
	</view>
	<!-- #endif -->
</template>

<script>
	import {
		getGeocoder
	} from '@/api/user.js';
	import { getnearbyStore } from '@/api/new_store.js';
	import { mapMutations } from 'vuex';
	let statusBarHeight = uni.getSystemInfoSync().statusBarHeight;
	import privacyAgreementPopup from '@/components/privacyAgreementPopup/index.vue'
	export default {
		name: 'headerSerch',
		props: {
			dataConfig: {
				type: Object,
				default: () => {}
			},
			special: {
				type: Number,
				default: 0
			}
		},
		components: {
			privacyAgreementPopup
		},
		data() {
			return {
				statusBarHeight: statusBarHeight,
				marTop: 63,
				bgColor: this.dataConfig.bgColor.color,
				boxStyle: this.dataConfig.boxStyle.type,
				logoConfig: this.dataConfig.logoConfig.url,
				mbConfig: this.dataConfig.mbConfig.val,
				txtStyle: this.dataConfig.txtStyle.type,
				hotWords: this.dataConfig.hotWords.list,
				prConfig: this.dataConfig.prConfig.val,
				tabVal: this.dataConfig.tabConfig.tabVal,
				radioVal: this.dataConfig.radioConfig.tabVal,
				storeVal: this.dataConfig.mapConfig.tabVal,
				textColor: this.dataConfig.textColor.color[0].item,
				textStyle: this.dataConfig.textStyle.list[this.dataConfig.textStyle.type].style,
				titleConfig: this.dataConfig.titleConfig.value,
				storeName: '',
				addressInfo:'选择地址',
				addressInfos:"选择地址",
				location: {},
				canGetPrivacySetting: false,
			};
		},
		watch: {
			location(value) {
				this.getnearbyStore(value);
			}
		},
		mounted() {
			let that = this;
			// #ifdef MP
			if (wx.getPrivacySetting){
				this.canGetPrivacySetting = true
			}
			// #endif
			uni.setStorageSync('hotList', that.hotWords);
			that.$store.commit('hotWords/setHotWord', that.hotWords);
			// #ifdef MP || APP-PLUS
			setTimeout(() => {
				// 获取小程序头部高度
				let info = uni.createSelectorQuery().in(this).select(".mp-header");
				info.boundingClientRect(function(data) {
					that.marTop = data.height
				}).exec()
			}, 100)
			// #endif
			if(this.tabVal===1 || (this.tabVal===2 && this.radioVal === 1)){
				this.selfLocation();
				let that = this;
				// #ifdef MP
				uni.$on('canGetPrivacySetting', function(data) {
					if(data && !uni.getStorageSync('user_latitude')){
						that.selfLocation();
					}
				});
				// #endif
			}
			uni.$on('activeFn', data => {
				if(data){
					this.storeName = data.name;
					this.SET_NEARBY(data.id);
				}
			});
		},
		methods: {
			...mapMutations(['SET_NEARBY']),
			onAgree() {
				this.selfLocation();
			},
			selfLocation() {
				let self = this
				// #ifdef MP || APP-PLUS
				uni.getLocation({
					type: 'gcj02',
					success: (res) => {
						try {
							uni.setStorageSync('user_latitude', res.latitude);
							uni.setStorageSync('user_longitude', res.longitude);
							self.getGeocoderCity(res.latitude,res.longitude);
							self.location = { latitude: res.latitude, longitude: res.longitude };
						} catch {}
					},
					fail:(res)=>{
						console.log(res)
						// #ifdef MP
						uni.getSetting({
							success: res=>{
								if(typeof(res.authSetting['scope.userLocation']) != 'undefined' && !res.authSetting['scope.userLocation']){
								  uni.setStorageSync('refuseLocation', true);
								}
							}
						})
						// #endif
					}
				});
				// #endif
				// #ifdef H5
				if (this.$wechat.isWeixin()) {
					this.$wechat.location().then(res => {
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						self.getGeocoderCity(res.latitude,res.longitude);
					})
				} else {
					uni.getLocation({
						type: 'gcj02',
						success: function(res) {
							try {
								uni.setStorageSync('user_latitude', res.latitude);
								uni.setStorageSync('user_longitude', res.longitude);
								self.getGeocoderCity(res.latitude,res.longitude);
							} catch {}
						}
					});
				}
				// #endif
			},
			getGeocoderCity(latitude,longitude){
				getGeocoder({
					lat: latitude,
					long: longitude
				}).then(res=>{
					let address = res.data.address_component;
					this.addressInfo = address?address.city.slice(0,4) : '选择地址';
					this.addressInfos = address?(address.city+address.district).slice(0,7) : '选择地址';
				})
			},
			goLink(){
				let url = this.dataConfig.linkConfig.value;
				this.$util.JumpPath(url);
			},
			chooseLocation: function() {
				let that = this;
				if(that.storeVal == 0){
					uni.navigateTo({
						url:'/pages/store/store_list/index?type=1&isCollage=1'
					})
				}else{
					uni.chooseLocation({
						success: (res) => {
							let address = that.$util.addressInfo(res.address);
							this.addressInfo = address?address.city.slice(0,4) : '选择地址';
							this.addressInfos = address?(address.city+address.district).slice(0,7) : '选择地址';
							that.location = { latitude: res.latitude, longitude: res.longitude };
						},
						fail: (err)=>{
							console.log(err)
						}
					})
				}
			},
			getnearbyStore(data) {
				getnearbyStore(data).then(res => {
					this.SET_NEARBY(res.data.info.id);
					this.storeName = res.data.info.name;
				});
			}
		}
	}
</script>

<style lang="scss">
	.serch-wrapper {
	
		&.center {
			justify-content: center;
		}
	
		&.right {
			justify-content: flex-end;
			/* #ifdef MP */
			padding-right: 185rpx !important;
			/* #endif */
		}
	}
	
	.title {
		font-size: 30rpx;
		&.on{
			margin-right: 20rpx;
		}
	}
	
	.map {
		color: #fff;
		font-size: 30rpx;
		margin-right: 20rpx;
	
		.info {
			&.on {
				max-width: 260rpx;
			}
	
			&.on1 {
				max-width: 156rpx;
			}
		}
	
		.iconfont {
			font-size: 32rpx;
			margin-right: 6rpx;
		}
	
	 .icon-xiangxia {
			font-size: 28rpx;
			margin-left: 10rpx;
		}
	}
	.header {
		width: 100%;
		height: 100rpx;
		background: linear-gradient(90deg, $bg-star 50%, $bg-end 100%);
		
		.serch-wrapper {
			padding: 20rpx 20rpx 0 0;

			.logo {
				height: 60rpx;
				margin-right: 20rpx;
				image {
					width: 100%;
					height: 100%;
				}
			}

			.input {
				flex: 1;
				height: 58rpx;
				padding: 0 20rpx 0 30rpx;
				background: rgba(247, 247, 247, 1);
				border: 1px solid rgba(241, 241, 241, 1);
				color: #BBBBBB;
				font-size: 28rpx;

				.iconfont {
					margin-right: 20rpx;
				}

				// 没有logo，直接搜索框
				&.on {
					width: 100%;
				}

				// 设置圆角
				&.fillet {
					border-radius: 29rpx;
				}

				// 文本框文字居中
				&.row-center {
					padding: 0;
				}
			}
		}
	}

	/* #ifdef MP || APP-PLUS */
	.mp-header {
		z-index: 30;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		background: linear-gradient(90deg, $bg-star 50%, $bg-end 100%);

		.serch-wrapper {
			height: 100%;
			/* #ifdef MP */
			padding: 0 220rpx 0 0 !important;
			/* #endif */
			/* #ifdef APP-PLUS */
			padding: 0 50rpx 0 40rpx;

			/* #endif */
			.logo {
				height: 60rpx;
				margin-right: 30rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.input {
				flex: 1;
				height: 50rpx;
				padding: 0 0 0 30rpx;
				background: rgba(247, 247, 247, 1);
				border: 1px solid rgba(241, 241, 241, 1);
				color: #BBBBBB;
				font-size: 28rpx;

				.iconfont {
					margin-right: 20rpx;
				}

				// 没有logo，直接搜索框
				&.on {
					/* #ifdef MP */
					width: 70%;
					/* #endif */
					/* #ifdef APP-PLUS */
					width: 100%;
					/* #endif */
				}

				// 设置圆角
				&.fillet {
					border-radius: 29rpx;
				}

				// 文本框文字居中
				&.row-center {
					padding: 0;
				}
			}
		}
	}

	/* #endif */
</style>