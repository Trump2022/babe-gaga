<template>
	<view class="main">
		<guide v-if="guidePages" :advData="advData"></guide>
	</view>
</template>

<script>
	let app = getApp();
	import guide from '@/components/guide/index.vue'
	import Cache from '@/utils/cache';
	import shareScence from "@/libs/spread";
	import {
		getOpenAdv
	} from '@/api/api.js'
	import {
		mapGetters
	} from 'vuex';
	export default {
		components: {
			guide
		},
		computed: mapGetters(['isLogin']),
		data() {
			return {
				guidePages: false,
				advData: []
			}
		},
		onLoad(options) {
			let that = this
			//扫码携带参数处理
			// #ifdef MP
			if (options.scene) {
				let value = that.$util.getUrlParams(decodeURIComponent(options.scene));
				//记录推广人uid
				if (value.spid) app.globalData.spid = value.spid;
			}
			shareScence({puid: app.globalData.spid}, that.isLogin)
			// #endif
		},
		onShow() {
			// #ifdef H5
			if(this.$wechat.isWeixin()){
				this.$wechat.wechat();
			}
			// #endif
			this.loadExecution()
		},
		methods: {
			loadExecution() {
				const tagDate = uni.getStorageSync('guideDate') || 0,
					nowDate = new Date().getTime();
				if ((nowDate - tagDate)<=uni.getStorageSync('intervalTime')) {
					uni.switchTab({
						url: '/pages/class/index/index'
					});
					return
				}
				
				getOpenAdv().then(res => {
					if (res.data.status == 0 || res.data.value.length == 0) {
						uni.switchTab({
							// url: '/pages/index/index'
							url: '/pages/class/index/index'
						});
					} else if (res.data.status && (res.data.value.length || res.data.video_link)) {
						this.advData = res.data
						let intervalTime = parseFloat(res.data.interval_time)*60*60*1000 || 0;
						uni.setStorageSync('intervalTime', intervalTime);
						uni.setStorageSync('guideDate', new Date().getTime());
						this.guidePages = true
					}
				}).catch(err => {
					uni.switchTab({
						url: '/pages/class/index/index'
					});
				})
			}
		},
		onHide() {
			this.guidePages = false
		}
	}
</script>

<style>
	page,
	.main {
		width: 100%;
		height: 100%;
	}
</style>
