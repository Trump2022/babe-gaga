<template>
	<!-- 商品分类 -->
	<view :style="colorStyle">
		<!-- 门店的两种样式布局 -->
		<storeCate1 :info="info" :isFooter="isFooter" ref="refresh2">
		</storeCate1>
		<pageFooter @newDataStatus="newDataStatus"></pageFooter>
	</view>
</template>

<script>
	import {
		mapGetters
	} from 'vuex';
	import colors from "@/mixins/color";
	import storeCate1 from './cart.vue'
	import pageFooter from '@/components/pageFooter/index.vue'
	import {
		getnearbyStore
	} from '@/api/new_store.js'
	import {
		colorChange
	} from '@/api/api.js';
	import {
		getCartCounts
	} from '@/api/order.js';
	export default {
		computed: mapGetters(['isLogin', 'uid']),
		components: {
			storeCate1,
			pageFooter
		},
		mixins: [colors],
		data() {
			return {
				info: {},
				id: 0,
				isFooter: false,
				category: '',
				storeShow: 1,
				customerType: 1,
				where: {
					latitude: 0,
					longitude: 0,
					store_id: 0
				},
				cart_num: 0,
				delivery_type: 3
			}
		},
		onLoad(options) {
			this.collage_id = options.collage_id;
			this.delivery_type = options.delivery_type;
			this.where.store_id = options.id;
			// uni.setStorageSync('user_store_id', options.id);
			try {
				this.where.latitude = uni.getStorageSync('user_latitude');
				this.where.longitude = uni.getStorageSync('user_longitude');
				this.selfLocation();
				if (!this.$util.checkOpenGPSServiceByAndroidIOS()) {
					this.getStore();
				}
			} catch (e) {}
		},
		onShow() {
			// if (uni.getStorageSync('form_type_cart')) {
			// 	this.getStore();
			// }
		},
		mounted() {},
		methods: {
			otherFun(object) {
				if (!!object) {
					if (this.storeShow == 1) {
						this.$refs.refresh2.updateFun(object);
					}
				}
			},
			selfLocation() {
				let self = this
				// #ifdef H5
				if (self.$wechat.isWeixin()) {
					self.$wechat.location().then(res => {
						// this.where.latitude = res.latitude;
						// this.where.longitude = res.longitude;
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						self.getStore();
					}).catch(err => {
						self.getStore();
					})
				} else {
					// #endif
					uni.getLocation({
						type: 'gcj02',
						success: (res) => {
							try {
								// this.where.latitude = res.latitude;
								// this.where.longitude = res.longitude;
								uni.setStorageSync('user_latitude', res.latitude);
								uni.setStorageSync('user_longitude', res.longitude);
							} catch {}
							// self.getStore();
						},
						fail: (err) => {
							self.$util.Tips({
								title: err
							});
						},
						complete: function() {
							self.getStore();
						}
					});
					// #ifdef H5	
				}
				// #endif
			},
			// 获取接口附近门店
			getStore() {
				this.where.latitude = uni.getStorageSync('user_latitude');
				this.where.longitude = uni.getStorageSync('user_longitude');
				getnearbyStore(this.where).then(res => {
					let info = res.data.info;
					this.cart_num = res.data.cart_num;
					if (Object.prototype.toString.call(info) === "[object Array]") {
						this.info = {}
					} else {
						this.info = res.data.info
					}
					this.info = {...this.info, collage_id: this.collage_id, delivery_type: this.delivery_type };
					this.id = this.info.id
					this.customerType = parseInt(res.data.info.customer_type)
					setTimeout(() => {
						this.$refs.refresh2.getAllCategory();
						this.$refs.refresh2.getCartNum();
						if (this.isLogin) {
							this.$refs.refresh2.getCartList(1);
						}
					}, 10)
				})
			},
			newDataStatus(val) {
				this.isFooter = val;
			},
		},
		onReachBottom: function() {
			setTimeout(() => {
				this.$refs.refresh2.getProducts()
			}, 10)
		}
	}
</script>
<style scoped lang="scss">
	/deep/.goodCate .uni-badge-left-margin .uni-badge--error{
		background-color: #fff !important;
		color: var(--view-theme);
		border-color: var(--view-theme);
		z-index: 8;
	}
	/deep/.goodCate .footer .cartIcon .uni-badge-left-margin .uni-badge--error{
		right: 0 !important;
		top: 10px !important;
	}
	/deep/.mask {
		z-index: 99;
	}

	/deep/.good-cate {
		padding: 80rpx;
	}
</style>
