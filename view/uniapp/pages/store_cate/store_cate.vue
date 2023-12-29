<template>
  <!-- 商品分类 -->
  <view :style="colorStyle">
    <!-- 门店的两种样式布局 -->
	<storeCate1 v-if="storeShow==1" :info="info" :isFooter="isFooter" ref="refresh2">
	</storeCate1>
    <storeCate2 v-else-if="storeShow==2" :cart_num="cart_num" :info="info" :customerType="customerType" :isFooter="isFooter" ref="refresh">
    </storeCate2>
    <pageFooter @newDataStatus="newDataStatus"></pageFooter>
  </view>
</template>

<script>
	import {
		mapState,
		mapGetters
	} from 'vuex';
  import colors from "@/mixins/color";
  import storeCate1 from './store_cate1.vue'
  import storeCate2 from './store_cate2.vue'
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
	// #ifdef MP
	import util from '@/utils/util.js';
	// #endif
  export default {
		computed: {
			...mapState({
				nearbyStore: state => state.app.nearbyStore
			}),
			...mapGetters(['isLogin', 'uid'])
		},
    components: {
      storeCate1,
      storeCate2,
      pageFooter
    },
    mixins: [colors],
    data() {
      return {
        info: {},
        id:0,
        isFooter: false,
        category: '',
        storeShow: 0,
		  customerType:1,
        where: {
          latitude: 0,
          longitude: 0,
		  store_id: 0
        },
		cart_num:0,
		mapFrom:0,
      }
    },
	watch: {
		nearbyStore(value) {
			this.where.store_id = value;
			this.getStore();
		}
	},
		onLoad(options){
			// #ifdef MP
			if (options.scene) {
				options = util.getUrlParams(decodeURIComponent(options.scene));
			}
			// #endif
			this.where.store_id = this.nearbyStore;
			if (options.mapFrom) {
				this.mapFrom = options.mapFrom;
			}
			if (options.id) {
				this.where.store_id = options.id;
				uni.setStorageSync('user_store_id', options.id);
			}
			// this.mapFrom = options.mapFrom || 0;
			// this.where.store_id = options.id;
			// uni.setStorageSync('user_store_id', options.id);
			 try {
			 	this.where.latitude = uni.getStorageSync('user_latitude');
			 	this.where.longitude = uni.getStorageSync('user_longitude');
				if(options.mapFrom==1){
					this.getStore();
					//this.getCartNum(options.id);
				}else{
					this.selfLocation();
					if(!this.$util.checkOpenGPSServiceByAndroidIOS()){
						this.getStore();
					}
				}
			 } catch (e) {
				 this.getStore();
			 }
		},
		onUnload(){
			this.mapFrom = 0;
			uni.$off('newAttrNum')
		},
		onHide() {
			this.mapFrom = 0;
		},
    onShow(){
			// if(uni.getStorageSync('form_type_cart')){
			// 	this.getStore();
			// }
			if(this.mapFrom==0){
				if(this.storeShow==1){
					setTimeout(()=>{
					   this.$refs.refresh2.getNoCart();
					},10)
				}
			}
    },
		mounted() {},
    methods: {
		otherFun(object){
			if(!!object){
				if(this.storeShow==1){
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
					}).catch(err=>{
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
						fail:(err)=>{
							// return self.$util.Tips({
							// 	title: err.errMsg
							// });
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
					if(Object.prototype.toString.call(info) === "[object Array]"){
						this.info = {}
						this.storeShow = 2;
					}else{
						this.info = res.data.info
						this.storeShow = res.data.info.home_style;
					}
					this.id = this.info.id
					this.customerType = parseInt(res.data.info.customer_type)
					uni.setStorageSync('user_store_id', this.info.id);
					// #ifdef H5 || MP
					this.info = { ...this.info, store_splicing_switch: res.data.store_splicing_switch };
					// #endif
          if (this.storeShow === 1) {
          setTimeout(()=>{
            this.$refs.refresh2.getAllCategory();
						if(this.isLogin){
							this.$refs.refresh2.getCartList(1);
						}
            this.$refs.refresh2.getMarTop();
          },10)
          } else if (this.storeShow === 2) {
						setTimeout(()=>{
							this.$refs.refresh.where.store_id = this.id
							this.$refs.refresh.loadend = false
							this.$refs.refresh.getProducts(true)
						})
          }
        })
      },
      newDataStatus(val) {
        this.isFooter = val;
      },
    },
    onReachBottom: function() {
      if (this.storeShow === 1) {
         setTimeout(()=>{
            this.$refs.refresh2.getProducts()
         },10)
    	}
    	if(this.storeShow === 2){
    		  this.$refs.refresh.getProducts()
    	}
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
	/deep/.one .uni-badge-left-margin .uni-badge--error{
		background-color: var(--view-theme) !important;
		color: #fff;
		border-color: #fff;
		z-index: 8;
	}
	/deep/.mask{
		z-index: 99;
	}
	/deep/.good-cate {
		padding: 80rpx;
	}
	/deep/.address-window {
		/* #ifdef H5 */
		bottom: 94rpx !important;
		bottom: calc(94rpx+ constant(safe-area-inset-bottom)) !important; ///兼容 IOS<11.2/
		bottom: calc(94rpx + env(safe-area-inset-bottom)) !important; ///兼容 IOS>11.2/
		/* #endif */
		/* #ifndef H5 */
		bottom: 98rpx;
		bottom: calc(98rpx + constant(safe-area-inset-bottom)) !important; ///兼容 IOS<11.2/
		bottom: calc(98rpx + env(safe-area-inset-bottom)) !important; ///兼容 IOS>11.2/
		/* #endif */
		&.ons {
			bottom: 0 !important;
			bottom: constant(safe-area-inset-bottom) !important; ///兼容 IOS<11.2/
			bottom: env(safe-area-inset-bottom) !important; ///兼容 IOS>11.2/
		}
	}
</style>
