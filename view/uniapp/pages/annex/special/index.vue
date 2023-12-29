<template>
<view v-if="pageShow" class="page"
		:class="bgTabVal==2?'fullsize noRepeat':bgTabVal==1?'repeat ysize':'noRepeat ysize'"
		:style="'background-color:'+bgColor+';background-image: url('+bgPic+');min-height:'+windowHeight+'px;'">	
	<view>
		<!-- #ifdef H5 -->
		<view v-for="(item, index) in styleConfig" :key="index">
			<component :is="item.name" :index="index" :dataConfig="item" :isSortType="isSortType" @changeBarg="changeBarg"
				@detail="goDetail" @bindSortId="bindSortId" :isScrolled="isScrolled"></component>
		</view>
		<!-- #endif -->
		<!-- #ifdef MP || APP-PLUS-->
		<block v-for="(item, index) in styleConfig" :key="index">
			<homeComb v-if="item.name == 'homeComb'" :dataConfig="item" @bindSortId="bindSortId" :isScrolled="isScrolled" :special="1"></homeComb>
			<shortVideo v-if="item.name == 'shortVideo'" :dataConfig="item" :isSortType="isSortType">
			</shortVideo>
			<userInfor v-if="item.name == 'userInfor'" :dataConfig="item" :isSortType="isSortType">
			</userInfor>
			<newVip v-if="item.name == 'newVip'" :dataConfig="item" :isSortType="isSortType">
			</newVip>
			<activeParty v-if="item.name == 'activeParty'" :dataConfig="item" :isSortType="isSortType"></activeParty>
			<articleList v-if="item.name == 'articleList'" :dataConfig="item" :isSortType="isSortType"></articleList>
			<bargain v-if="item.name == 'bargain'" :dataConfig="item" @changeBarg="changeBarg" :isSortType="isSortType"></bargain>
			<blankPage v-if="item.name == 'blankPage'" :dataConfig="item" :isSortType="isSortType"></blankPage>
			<combination v-if="item.name == 'combination'" :dataConfig="item" :isSortType="isSortType"></combination>
			<coupon v-if="item.name == 'coupon'" :dataConfig="item" :isSortType="isSortType"></coupon>
			<customerService v-if="item.name == 'customerService'" :dataConfig="item" :isSortType="isSortType"></customerService>
			<goodList v-if="item.name == 'goodList'" :dataConfig="item" @detail="goDetail" :isSortType="isSortType"></goodList>
			<guide v-if="item.name == 'guide'" :dataConfig="item" :isSortType="isSortType"></guide>
			<headerSerch v-if="item.name == 'headerSerch'" :dataConfig="item" :special="1"></headerSerch>
			<liveBroadcast v-if="item.name == 'liveBroadcast'" :dataConfig="item" :isSortType="isSortType"></liveBroadcast>
			<menus v-if="item.name == 'menus'" :dataConfig="item" :isSortType="isSortType"></menus>
			<news v-if="item.name == 'news'" :dataConfig="item" :isSortType="isSortType"></news>
			<pictureCube v-if="item.name == 'pictureCube'" :dataConfig="item" :isSortType="isSortType">
			</pictureCube>
			<promotionList v-if="item.name == 'promotionList'" :dataConfig="item" @detail="goDetail" :isSortType="isSortType"></promotionList>
			<richText v-if="item.name == 'richText'" :dataConfig="item" :isSortType="isSortType"></richText>
			<seckill v-if="item.name == 'seckill'" :dataConfig="item" :isSortType="isSortType"></seckill>
			<swiperBg v-if="item.name == 'swiperBg'" :dataConfig="item" :isSortType="isSortType"></swiperBg>
			<swipers v-if="item.name == 'swipers'" :dataConfig="item" :isSortType="isSortType"></swipers>
			<tabNav v-if="item.name == 'tabNav'" :dataConfig="item" @bindSortId="bindSortId" :fromType="1"></tabNav>
			<titles v-if="item.name == 'titles'" :dataConfig="item" :isSortType="isSortType"></titles>
		</block>
		<!-- #endif -->
		<view class="sort-product" v-if="isSortType == 1" style="margin-top: 0;">
			<scroll-view scroll-x="true" class="sort-scroll">
				<view class="sort-box" v-if="sortList.children && sortList.children.length">
					<view class="sort-item" v-for="(item, index) in sortList.children" :key="index"
						@click="changeSort(item, index)" :class="{ on: curSort == index }">
						<image :src="item.pic" mode="" v-if="item.pic"></image>
						<image src="/static/images/sort-img.png" mode="" v-else></image>
						<view class="txt">{{ item.cate_name }}</view>
					</view>
				</view>
			</scroll-view>
		    <!-- 首页商品列表 -->
			<view class="product-list" v-if="goodList.length">
				<view class="product-item" v-for="(item, index) in goodList" @click="goGoodsDetail(item)">
					<image :src="item.image" mode="aspectFill"></image>
					<span class="pictrue_log_big pictrue_log_class"
						v-if="item.activity && item.activity.type === '1'">秒杀</span>
					<span class="pictrue_log_big pictrue_log_class"
						v-if="item.activity && item.activity.type === '2'">砍价</span>
					<span class="pictrue_log_big pictrue_log_class"
						v-if="item.activity && item.activity.type === '3'">拼团</span>
					<view class="info">
						<view class="title line1">{{ item.store_name }}</view>
						<view class="price-box">
							<text>￥</text>
							{{ item.price }}
						</view>
					</view>
				</view>
			</view>
			<Loading :loaded="loaded" :loading="loading"></Loading>
			<view v-if="goodList.length == 0 && loaded" class="sort-scroll">
				<view class="empty-box">
					<image :src="imgHost + '/statics/images/no-thing.png'"></image>
					<view class="tips">暂无商品，去看点别的吧</view>
				</view>
				<recommend :hostProduct="hostProduct"></recommend>
			</view>
		</view>
	<!-- 	<view class="loadingicon acea-row row-center-wrapper"
			v-if="tempArr.length && styleConfig[styleConfig.length - 1].name == 'promotionList'">
			<text class="loading iconfont icon-jiazai" :hidden="loading == false"></text>
			{{ loadTitle }}
		</view> -->
		<view class="uni-p-b-98"></view>
		<pageFooter></pageFooter>
		<!-- #ifdef MP -->
		  <!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
		  <!-- #endif -->
	</view>
</view>	
</template>

<script>
	const app = getApp();
	import couponWindow from '@/components/couponWindow/index'
	import {
		getCouponV2,
		getCouponNewUser
	} from '@/api/api.js'
	import {
		getShare
	} from '@/api/public.js';
	// #ifdef H5
	import mConfig from '@/pages/index/components/index.js';
	import {
		silenceAuth
	} from '@/api/public.js';
	// #endif
	// #ifdef MP || APP-PLUS
	import homeComb from '@/pages/index/components/homeComb';
	import userInfor from '@/pages/index/components/userInfor';
	import newVip from '@/pages/index/components/newVip';
	import shortVideo from '@/pages/index/components/shortVideo';
	import activeParty from '@/pages/index/components/activeParty';
	import headerSerch from '@/pages/index/components/headerSerch';
	import swipers from '@/pages/index/components/swipers';
	import coupon from '@/pages/index/components/coupon';
	import articleList from '@/pages/index/components/articleList';
	import bargain from '@/pages/index/components/bargain';
	import blankPage from '@/pages/index/components/blankPage';
	import combination from '@/pages/index/components/combination';
	import customerService from '@/pages/index/components/customerService';
	import goodList from '@/pages/index/components/goodList';
	import guide from '@/pages/index/components/guide';
	import liveBroadcast from '@/pages/index/components/liveBroadcast';
	import menus from '@/pages/index/components/menus';
	import news from '@/pages/index/components/news';
	import promotionList from '@/pages/index/components/promotionList';
	import richText from '@/pages/index/components/richText';
	import seckill from '@/pages/index/components/seckill';
	import swiperBg from '@/pages/index/components/swiperBg';
	import tabNav from '@/pages/index/components/tabNav';
	import titles from '@/pages/index/components/titles';
	import pictureCube from '@/pages/index/components/pictureCube';

	import {
		getTemlIds
	} from '@/api/api.js';
	import {
		SUBSCRIBE_MESSAGE,
		TIPS_KEY
	} from '@/config/cache';

	// #endif
	import {
		mapGetters
	} from 'vuex';
	import {
		getDiy,
		getIndexData,
		getDiyVersion
	} from '@/api/api.js';
	import {
		getCategoryList,
		getProductslist,
		getProductHot,
	} from '@/api/store.js';
	import {
		goShopDetail
	} from '@/libs/order.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {HTTP_REQUEST_URL} from '@/config/app';
	import pageFooter from '@/components/pageFooter/index.vue'
	import recommend from '@/components/recommend';
	import Loading from '@/components/Loading/index.vue';
	export default {
		computed: mapGetters(['isLogin', 'uid']),
		components: {
			Loading,
			recommend,
			pageFooter,
			couponWindow,
			// #ifdef H5
			...mConfig,
			// #endif
			// #ifdef MP || APP-PLUS
			homeComb,
			newVip,
			userInfor,
			shortVideo,
			activeParty,
			headerSerch,
			swipers,
			coupon,
			articleList,
			bargain,
			blankPage,
			combination,
			customerService,
			goodList,
			guide,
			liveBroadcast,
			menus,
			news,
			promotionList,
			richText,
			seckill,
			swiperBg,
			tabNav,
			titles,
			pictureCube
			// #endif
		},
		data() {
			return {
				styleConfig: [],
				loading: false,
				loadend: false,
				loadTitle: '加载更多', //提示语
				page: 1,
				limit: this.$config.LIMIT,
				numConfig: 0,
				code: '',
				isCouponShow: false,
				couponObj: {},
				couponObjs: {},
				shareInfo: {},
				footConfig: {},
				pageId: '',
				sortMpTop: 0,
				bgColor: '',
				bgPic: '',
				bgTabVal: '',
				pageShow: true,
				windowHeight: 0,
				isShowAuth: false,
				isScrolled: false,
				sortList: '',
				sortAll: [],
				isSortType: 0,
				hostProduct: [],
				hotScroll: false,
				hotPage: 1,
				hotLimit: 10,
				curSort: 0,
				loaded: false,
				goodPage: 1,
				goodList: [],
				sid: 0,
				imgHost:HTTP_REQUEST_URL,
			};
		},
		onLoad(options) {
			let that = this
			this.$nextTick(function() {
				uni.getSystemInfo({
					success: function(res) {
						that.windowHeight = res.windowHeight;
					}
				});
			})
			const {
				state,
				scope
			} = options;
			this.pageId = options.id
			// #ifdef MP
			if (options.scene) {
				let value = that.$util.getUrlParams(decodeURIComponent(options.scene));
				this.pageId = value.id
			}
			// #endif
			uni.setNavigationBarTitle({
				title: '专题栏'
			});
			
			// #ifdef APP-PLUS
			this.sortMpTop = -50
			// #endif
			this.diyData();
			this.getIndexData();
			// #ifdef H5
			this.setOpenShare();
			// #endif
			// #ifdef MP || APP-PLUS
			this.getTemlIds();
			// #endif
			getShare().then(res => {
				this.shareInfo = res.data;
			})
		},
		onUnload(){
			// 清除监听
			uni.$off('activeFn');
		},
		watch: {
			isLogin: {
				deep: true, //深度监听设置为 true
				handler: function(newV, oldV) {
					// 优惠券弹窗
					var newDates = new Date().toLocaleDateString();
					if (newV) {
						try {
							var oldDate = uni.getStorageSync('oldDate') || ''
						} catch {}
						if (oldDate != newDates) {
							this.getCoupon();

						}
					}
				}
			}
		},
		onShow() {
			uni.removeStorageSync('form_type_cart');
			// 优惠券弹窗
			var newDates = new Date().toLocaleDateString();
			if (this.isLogin) {
				try {
					var oldDate = uni.getStorageSync('oldDate') || ''
				} catch {}
				if (oldDate != newDates) {
					this.getCoupon();
				}
				let oldUser = uni.getStorageSync('oldUser') || 0;
				if (!oldUser) {
					this.getCouponOnce();
				}
			}
		},
		mounted() {},
		methods: {
			// 分类点击
			changeSort(item, index) {
				if (this.curSort == index) return;
				this.curSort = index;
				this.sid = item.id;
				this.goodList = [];
				this.goodPage = 1;
				this.loaded = false;
				this.getGoodsList();
			},
			// 获取分类id
			bindSortId(data) {
				this.isSortType = data == -99 ? 0 : 1;
				this.getProductList(data);
				if (this.hostProduct.length == 0) {
					this.get_host_product();
				}
			},
			getProductList(data) {
				this.curSort = 0;
				this.loaded = false;
				if (this.sortAll.length > 0) {
					this.sortAll.forEach((el, index) => {
						if (el.id == data) {
							this.$set(this, 'sortList', el);
							this.sid = el.children.length ? el.children[0].id : '';
						}
					});
					this.goodList = [];
					this.goodPage = 1;
					this.$nextTick(() => {
						if (this.sortList != '') this.getGoodsList();
					});
				} else {
					getCategoryList().then(res => {
						this.sortAll = res.data;
						res.data.forEach((el, index) => {
							if (el.id == data) {
								this.sortList = el;
								this.sid = el.children.length ? el.children[0].id : '';
							}
						});
						this.goodList = [];
						this.goodPage = 1;
						this.$nextTick(() => {
							if (this.sortList != '') this.getGoodsList();
						});
					});
				}
			},
			// 商品列表
			getGoodsList() {
				if (this.loading || this.loaded) return;
				this.loading = true;
				getProductslist({
					sid: this.sid,
					keyword: '',
					priceOrder: '',
					salesOrder: '',
					news: 0,
					page: this.goodPage,
					limit: 10,
					cid: this.sortList.id
				}).then(res => {
					this.loading = false;
					this.loaded = res.data.length < 10;
					this.goodPage++;
					this.goodList = this.goodList.concat(res.data);
				});
			},
			/**
			 * 获取我的推荐
			 */
			get_host_product: function() {
				let that = this;
				if (that.hotScroll) return;
				getProductHot(that.hotPage, that.hotLimit).then(res => {
					that.hotPage++;
					that.hotScroll = res.data.length < that.hotLimit;
					that.hostProduct = that.hostProduct.concat(res.data);
				});
			},
			onLoadFun() {
				this.isShowAuth = false
			},
			authColse: function(e) {
			  this.isShowAuth = e
			},
			// 新用户优惠券
			getCouponOnce() {
				getCouponNewUser().then(res => {
					this.couponObjs = res.data;
				});
			},
			couponCloses() {
				this.couponObjs.show = false;
				try {
					uni.setStorageSync('oldUser', 1);
				} catch (e) {

				}
			},
			// 优惠券弹窗
			getCoupon() {
				getCouponV2().then(res => {
					this.couponObj = res.data
					if (res.data.list.length > 0) {
						this.isCouponShow = true
					}
				})
			},
			// 优惠券弹窗关闭
			couponClose() {
				this.isCouponShow = false
				try {
					uni.setStorageSync('oldDate', new Date().toLocaleDateString());
				} catch {}
			},
			// #ifdef H5
			// 获取url后面的参数
			getQueryString(name) {
				var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
				var reg_rewrite = new RegExp("(^|/)" + name + "/([^/]*)(/|$)", "i");
				var r = window.location.search.substr(1).match(reg);
				var q = window.location.pathname.substr(1).match(reg_rewrite);
				if (r != null) {
					return unescape(r[2]);
				} else if (q != null) {
					return unescape(q[2]);
				} else {
					return null;
				}
			},
			// #endif

			// #ifdef MP || APP-PLUS
			getTemlIds() {
				let messageTmplIds = wx.getStorageSync(SUBSCRIBE_MESSAGE);
				if (!messageTmplIds) {
					getTemlIds().then(res => {
						if (res.data) wx.setStorageSync(SUBSCRIBE_MESSAGE, JSON.stringify(res.data));
					});
				}
			},
			// #endif
			// 对象转数组
			objToArr(data) {
				const keys = Object.keys(data)
				keys.sort((a, b) => a - b)
				const m = keys.map(key => data[key]);
				return m;
			},
			setDiyData(data) {
				// let data = res.data;
				if (data.length == 0) {
					return this.$util.Tips({
						title: '暂无数据'
					}, {
						tab: 3
					})
				}
				
				if (data.is_bg_color) {
					this.bgColor = data.color_picker
				}
				if (data.is_bg_pic) {
					this.bgPic = data.bg_pic
					this.bgTabVal = data.bg_tab_val
				}
				this.pageShow = data.is_show
				uni.setNavigationBarTitle({
					title: data.title
				})
				let temp = []
				let lastArr = this.objToArr(data.value)
				lastArr.forEach((item, index, arr) => {
					if (item.name == 'pageFoot') {
						uni.setStorageSync('pageFoot', item)
						this.$store.commit('FOOT_UPLOAD', item)
						arr.splice(index, 1)
					}
					temp = arr
				});
				this.styleConfig = temp;
			},
			getDiyData() {
				getDiy(this.pageId).then(res => {
					uni.setStorageSync('specialDiyData', JSON.stringify(res.data));
					this.setDiyData(res.data);
				});
			},
			diyData() {
				let that = this;
				let diyData = uni.getStorageSync('specialDiyData');
				if (diyData) {
					getDiyVersion(this.pageId).then(res => {
						let diyVersion = uni.getStorageSync('specialDiyVersion');
						if ((res.data.version + this.pageId) === diyVersion) {
							this.setDiyData(JSON.parse(diyData));
						} else{
							uni.setStorageSync('specialDiyVersion', (res.data.version + this.pageId));
							this.getDiyData();
						}
					});
				} else{
					this.getDiyData();
				}
				// getDiy(this.pageId).then(res => {
				// 	let data = res.data;
				// 	if (res.data.length == 0) {
				// 		return this.$util.Tips({
				// 			title: '暂无数据'
				// 		}, {
				// 			tab: 3
				// 		})
				// 	}
					
				// 	if (data.is_bg_color) {
				// 		this.bgColor = data.color_picker
				// 	}
				// 	if (data.is_bg_pic) {
				// 		this.bgPic = data.bg_pic
				// 		this.bgTabVal = data.bg_tab_val
				// 	}
				// 	this.pageShow = data.is_show
				// 	uni.setNavigationBarTitle({
				// 		title: res.data.title
				// 	})
				// 	let temp = []
				// 	let lastArr = that.objToArr(res.data.value)
				// 	lastArr.forEach((item, index, arr) => {
				// 		if (item.name == 'pageFoot') {
				// 			uni.setStorageSync('pageFoot', item)
				// 			that.$store.commit('FOOT_UPLOAD', item)
				// 			arr.splice(index, 1)
				// 		}
				// 		temp = arr
				// 	});
				// 	that.styleConfig = temp;
				// });
			},
			getIndexData() {
			},
			changeBarg(item) {
				if (!this.isLogin) {
					toLogin()
				} else {
					uni.navigateTo({
						url: `/pages/activity/goods_bargain_details/index?id=${item.id}&spid=${this.uid}`
					});
				}
			},
			goDetail(item) {
				goShopDetail(item, this.uid).then(res => {
					uni.navigateTo({
						url: `/pages/goods_details/index?id=${item.id}`
					});
				});

			},
			// #ifdef H5
			// 微信分享；
			setOpenShare: function() {
				let that = this;
				if (that.$wechat.isWeixin()) {
					getShare().then(res => {
						let data = res.data.data;
						let configAppMessage = {
							desc: data.synopsis,
							title: data.title,
							link: location.href,
							imgUrl: data.img
						};
						that.$wechat.wechatEvevt(['updateAppMessageShareData', 'updateTimelineShareData'],
							configAppMessage);
					});
				}
			}
			// #endif
		},
		onReachBottom: function() {
		},
		onPageScroll(e) {
			if(e.scrollTop>10){
				this.isScrolled = true;
			}else{
				this.isScrolled = false;
			}
		},
		//#ifdef MP || APP-PLUS
		onShareAppMessage() {
			return {
				title: this.shareInfo.title,
				path: '/pages/index/index',
				imageUrl: this.storeInfo.img,
			};
		},
		//#endif
	};
</script>

<style lang="scss">
	.sort-scroll {
	  background-color: #fff;
	}
	.empty-box {
		text-align: center;
		padding-top: 50rpx;
		.tips{
			color: #aaa;
			font-size: 26rpx;
		}
		image {
			width: 414rpx;
			height: 304rpx;
		}
	}
	.sort-product {
		margin-top: 20rpx;
	
		.sort-box {
			display: flex;
			width: 100%;
			border-radius: 16rpx;
			padding: 30rpx 0;
	
			.sort-item {
				width: 20%;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				flex-shrink: 0;
	
				image {
					width: 90rpx;
					height: 90rpx;
					border-radius: 50%;
				}
	
				.txt {
					color: #272727;
					font-size: 24rpx;
					margin-top: 10rpx;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					width: 140rpx;
					text-align: center;
				}
	
				.pictrues {
					width: 90rpx;
					height: 90rpx;
					background: #f8f8f8;
					border-radius: 50%;
					margin: 0 auto;
				}
	
				.icon-gengduo1 {
					color: #333;
				}
	
				&.on {
					.txt {
						color: #fc4141;
					}
	
					image {
						border: 1px solid #fc4141;
					}
				}
			}
		}
	
		.product-list {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			margin-top: 30rpx;
			padding: 0 20rpx;
	
			.product-item {
				position: relative;
				width: 344rpx;
				background: #fff;
				border-radius: 10rpx;
				margin-bottom: 20rpx;
				overflow: hidden;
				
				.pictrue{
					position: relative;
				}
	
				image {
					width: 100%;
					height: 344rpx;
					border-radius: 10rpx 10rpx 0 0;
				}
	
				.info {
					padding: 14rpx 16rpx;
	
					.title {
						font-size: 28rpx;
					}
	
					.price-box {
						font-size: 34rpx;
						font-weight: 700;
						margin-top: 8px;
						color: #fc4141;
	
						text {
							font-size: 26rpx;
						}
					}
				}
			}
		}
	}
	.page {
		padding-bottom: 50px;
	}
	
	.ysize {
		background-size: 100%;
	}
	
	.fullsize {
		background-size: 100% 100%;
	}
	
	.repeat {
		background-repeat: repeat;
	}
	
	.noRepeat {
		background-repeat: no-repeat;
	}
	
</style>