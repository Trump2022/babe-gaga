<template>
	<view :style="colorStyle">
		<view class="authorize">
			<view class="pictrue">
				<image :src="logoUrl"></image>
				<view class="iconfont icon-guanbi4" @click='close'></view>
			</view>
			<view class="title">账号登录</view>
			<view class="info">
				<checkbox-group @change='ChangeIsDefault' v-if="agreement">
					<checkbox :checked="protocol ? true : false" />您已同意商城<text class="agree"
						@click="privacy('user')">《用户协议》</text>
					与<text class="agree" @click="privacy('privacy')">《隐私协议》</text>
				</checkbox-group>
				<view v-else>
					登录注册即同意商城
					<text class="agree" @click="privacy('user')">《用户协议》</text>与<text class="agree" @click="privacy('privacy')">《隐私协议》</text>
				</view>
			</view>
			<template v-if="routineAuthType.indexOf(1) != -1">
				<button hover-class="none" v-if="mp_is_new" @tap="userLogin"
					class="btn1">授权登录</button>
				<button v-else-if="canUseGetUserProfile && code" hover-class="none" @tap="getUserProfile"
						class="btn1">授权登录</button>
				<button v-else hover-class="none" open-type="getUserInfo" @getuserinfo="setUserInfo"
					class="btn1">授权登录</button>
			</template>
			<!-- <button v-if="routineAuthType.indexOf(2) != -1"  hover-class="none" @click="isUp = true" class="btn2 acea-row row-center-wrapper">手机号登录</button> -->
		</view>
		<!-- <block v-if="isUp">
			<mobileLogin :isUp="isUp" @close="maskClose" :authKey="authKey" @wechatPhone="wechatPhone"></mobileLogin>
		</block> -->
		<block v-if="isPhoneBox">
			<!-- <routinePhone :logoUrl="logoUrl" :isPhoneBox="isPhoneBox" @close="bindPhoneClose" :authKey="authKey">
			</routinePhone> -->
		</block>
		<block>
			<editUserModal :isShow="isShow" @closeEdit="closeEdit" @editSuccess="editSuccess">
			</editUserModal>
		</block>
		<view class="mask" @click='close'></view>
	</view>
</template>

<script>
	const app = getApp();
	// import mobileLogin from '../loginMobile/index.vue';
	import routinePhone from '../loginMobile/routine_phone.vue';
	import editUserModal from '@/components/eidtUserModal/index.vue'
	import {
		getLogo,
		silenceAuth,
		wechatAuthV2,
		authLogin
	} from '@/api/public';
	import {
		LOGO_URL,
		EXPIRES_TIME,
		USER_INFO,
		STATE_R_KEY
	} from '@/config/cache';
	import {
		getUserInfo
	} from '@/api/user.js';
	import Routine from '@/libs/routine';
	import wechat from '@/libs/wechat';
	import colors from '@/mixins/color.js';
	export default {
		mixins:[colors],
		props: {
			isShowAuth: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				isShow: false,//判断获取用户头像是否出现
				isUp: false,
				phone: '',
				isPhoneBox: false,
				logoUrl: '',
				code: '',
				authKey: '',
				options: '',
				userInfo: {},
				codeNum: 0,
				canUseGetUserProfile: false,
				mp_is_new: this.$Cache.get('MP_VERSION_ISNEW') || false,
				agreement: 0,
				protocol: false,
				routineAuthType: []
			};
		},
		components: {
			// mobileLogin,
			routinePhone,
			editUserModal
		},
		mounted(options) {
			if (uni.getUserProfile) {
				this.canUseGetUserProfile = true;
			}
			getLogo().then(res => {
				this.logoUrl = res.data.logo_url;
				this.agreement = res.data.store_user_agreement;
				this.routineAuthType = res.data.routine_auth_type;
			});
			let that = this;
			// #ifdef MP
			Routine.getCode()
				.then(code => {
					this.code = code
				})
			// #endif
		},
		methods: {
			close(){
				this.$emit('authColse', false);
			},
			privacy(type) {
				uni.navigateTo({
					url: "/pages/users/privacy/index?type=" + type
				})
			},
			ChangeIsDefault(e) {
				this.$set(this, 'protocol', !this.protocol);
			},
			// 小程序 22.11.8日删除getUserProfile 接口获取用户昵称头像
			userLogin() {
				if (!this.protocol && this.agreement) {
					return this.$util.Tips({
						title: '请先阅读并同意协议'
					});
				}
				Routine.getCode()
					.then(code => {
						uni.showLoading({
							title: '正在登录中'
						});
						authLogin({
							code,
							spread_spid: app.globalData.spid,
							spread_code: app.globalData.code
						}).then(res => {
							if (res.data.key !== undefined && res.data.key) {
								uni.hideLoading();
								this.authKey = res.data.key;
								this.isPhoneBox = true;
							} else {
								uni.hideLoading();
								let time = res.data.expires_time - this.$Cache.time();
								this.$store.commit('LOGIN', {
									token: res.data.token,
									time: time
								});
								this.getUserInfo(res.data.store_user_avatar || 0)
							}
						}).catch(err => {
							uni.hideLoading();
							uni.showToast({
								title: err,
								icon: 'none',
								duration: 2000
							});
						})
					})
					.catch(err => {
						uni.hideLoading();
						uni.showToast({
							title: err,
							icon: 'none',
							duration: 2000
						});
					});
			},
			editSuccess() {
				this.isShow = false
				this.$emit('onLoadFun');
			},
			closeEdit() {
				this.isShow = false
				this.$emit('onLoadFun');
				this.$util.Tips({
					title: '登录成功',
					icon: 'success'
				});
			},
			// 弹窗关闭
			// maskClose(store_user_avatar) {
			// 	this.isUp = false;
			// 	if (store_user_avatar) {
			// 	   this.$emit('onLoadFun');
			// 	}
			// },
			bindPhoneClose(data) {
				if (data.isStatus) {
					uni.hideLoading();
					this.isPhoneBox = false;
					if(data.store_user_avatar){
						this.isShow = true
					}else{
						this.$emit('onLoadFun');
						this.$util.Tips({
							title: '登录成功',
							icon: 'success'
						});
					}
					// this.getUserInfo(data.store_user_avatar)
					// this.$util.Tips({
					// 	title: '登录成功',
					// 	icon: 'success'
					// }, {
					// 	tab: 3
					// });
				} else {
					this.isPhoneBox = false;
				}
			},
			// #ifdef MP
			/**
			 * 获取个人用户信息
			 */
			getUserInfo: function(store_user_avatar) {
				let that = this;
				getUserInfo().then(res => {
					uni.hideLoading();
					that.userInfo = res.data;
					that.$store.commit('SETUID', res.data.uid);
					that.$store.commit('UPDATE_USERINFO', res.data);
					if(store_user_avatar){
						that.isShow = true
					}else{
						that.$emit('onLoadFun');
						that.$util.Tips({
							title: '登录成功',
							icon: 'success'
						});
					}
				});
			},
			setUserInfo(e) {
				if (!this.protocol && this.agreement) {
					return this.$util.Tips({
						title: '请先阅读并同意协议'
					});
				}
				this.close();
				uni.showLoading({
					title: '正在登录中'
				});
				Routine.getCode()
					.then(code => {
						this.getWxUser(code);
					})
					.catch(res => {
						uni.hideLoading();
					});
			},
			//小程序授权api替换 getUserInfo
			getUserProfile() {
				if (!this.protocol && this.agreement) {
					return this.$util.Tips({
						title: '请先阅读并同意协议'
					});
				}
				this.close();
				uni.showLoading({
					title: '正在登录中'
				});
				let self = this;
				Routine.getUserProfile()
					.then(res => {
						let userInfo = res.userInfo;
						userInfo.code = this.code;
						userInfo.spread_spid = app.globalData.spid || this.$Cache.get('spid'); //获取推广人ID
						userInfo.spread_code = app.globalData.code; //获取推广人分享二维码ID
						Routine.authUserInfo(userInfo)
							.then(res => {
								if (res.data.key !== undefined && res.data.key) {
									uni.hideLoading();
									self.authKey = res.data.key;
									self.isPhoneBox = true;
								} else {
									uni.hideLoading();
									let time = res.data.expires_time - self.$Cache.time();
									self.$store.commit('LOGIN', {
										token: res.data.token,
										time: time
									});
									this.getUserInfo()
								}
							})
							.catch(res => {
								uni.hideLoading();
								uni.showToast({
									title: res.msg,
									icon: 'none',
									duration: 2000
								});
							});
					})
					.catch(res => {
						uni.hideLoading();
					});
			},
			getWxUser(code) {
				let self = this;
				Routine.getUserInfo()
					.then(res => {
						let userInfo = res.userInfo;
						userInfo.code = code;
						userInfo.spread_spid = app.globalData.spid; //获取推广人ID
						userInfo.spread_code = app.globalData.code; //获取推广人分享二维码ID
						Routine.authUserInfo(userInfo)
							.then(res => {
								if (res.data.key !== undefined && res.data.key) {
									uni.hideLoading();
									self.authKey = res.data.key;
									self.isPhoneBox = true;
								} else {
									uni.hideLoading();
									let time = res.data.expires_time - self.$Cache.time();
									self.$store.commit('LOGIN', {
										token: res.data.token,
										time: time
									});
									self.$emit('onLoadFun');
									self.$util.Tips({
										title: res.msg,
										icon: 'success'
									});
								}
							})
							.catch(res => {
								uni.hideLoading();
								uni.showToast({
									title: res.msg,
									icon: 'none',
									duration: 2000
								});
							});
					})
					.catch(res => {
						uni.hideLoading();
					});
			},
			// #endif
		}
	};
</script>

<style lang="scss">
	.mask{
		z-index: 99;
	}
	.authorize{
		width: 100%;
		height: 560rpx;
		background-color: #fff;
		border-radius: 48rpx 48rpx 0 0;
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 667;
		padding-top: 50rpx;
		text-align: center;
		.pictrue{
			width: 152rpx;
			height: 152rpx;
			border-radius: 50%;
			margin: 0 auto;
			position: relative;
			image{
				width: 100%;
				height: 100%;
				border-radius: 50%;
				border:1px solid #eee;
			}
			.iconfont{
				position: absolute;
				width: 52rpx;
				height: 52rpx;
				background: #EEE;
				border-radius: 50%;
				color: #888;
				font-size: 30rpx;
				text-align: center;
				line-height: 52rpx;
				right: -267rpx;
				top: -20rpx;
			}
		}
		.title{
			margin-top: 28rpx;
			font-size: 36rpx;
			color: #333333;
		}
		.info{
			color: #9E9E9E;
			font-size: 28rpx;
			margin-top: 14rpx;
			.agree{
				color: #333;
			}
		}
		.btn1{
			width: 536rpx;
			height: 86rpx;
			border-radius: 43rpx;
			color: #fff;
			text-align: center;
			line-height: 86rpx;
			margin: 50rpx auto 0 auto;
			background-color: var(--view-theme);
			font-size: 30rpx;
		}
		.btn2{
			width: 536rpx;
			height: 86rpx;
			border-radius: 43rpx;
			border: 2rpx solid  var(--view-theme);
			color:  var(--view-theme);
			font-size: 30rpx;
			margin: 40rpx auto 0 auto;
		}
	}
</style>
