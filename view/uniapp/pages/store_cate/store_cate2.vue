<template>
  <view class="one">
    <!-- 顶部搜索框 -->
		<!-- #ifndef MP  -->
    <view class="header">
		<!-- #endif -->
		<!-- #ifdef MP  -->
		<view class="header" :style="'height:'+(statusBarHeight+163)+'px;'">
		<!-- #endif -->		
      <image :src="imgHost+'/statics/images/store-header.png'" mode="" class="header-img"></image>
	  <!-- #ifdef MP -->
	  <view class="input-wrapper acea-row row-middle" :style="'top:'+statusBarHeight+'px'">
		  <button v-if="info.store_splicing_switch" class="group-button acea-row row-middle" hover-class="none" @tap="onCollage">
		  	<text class="iconfont icon-pindan"></text>拼单
		  </button>
		  <navigator url="/pages/goods/goods_search/index" class="input acea-row row-center-wrapper" hover-class="none">
			  <view class="search on acea-row row-middle" style="height: 43px;">
				  <view class="searchCon">
					  <text class="iconfont icon-xiazai5"></text>
				  </view>
			  </view>
		  </navigator>
	  </view>
	  <!-- #endif -->
	  <!-- #ifndef MP -->
	  <view class="input-wrapper acea-row row-middle">
		  <button v-if="isSplicingPlatform && info.store_splicing_switch" class="group-button acea-row row-middle" hover-class="none" @tap="onCollage">
			<text class="iconfont icon-pindan"></text>拼单
		  </button>
		  <navigator url="/pages/goods/goods_search/index" class="input acea-row row-center-wrapper" hover-class="none">
			  <view class="search">
				  <text class="iconfont icon-xiazai5"></text>
			  </view>
		  </navigator>
	  </view>
	  <!-- #endif -->
      <!-- 门店地址 -->
			<!-- #ifndef MP  -->
			<view class="address">
			<!-- #endif -->
			<!-- #ifdef MP  -->
      <view class="address" :style="'top:'+(statusBarHeight+68)+'px;'">
			<!-- #endif -->
        <view class="left">
          <view class="left-title" @click="goMap">
						<text class="iconfont icon-mendian"></text>
            <text class="text line1">{{info.name || '暂无门店'}}</text>
            <text class="iconfont icon-xiangyou"></text>
          </view>
          <!-- 营业时间 -->
          <view class="time">
            营业时间：{{info.day_time || '-'}}
          </view>
          <view class="distance">
               <text class="iconfont icon-chakanditu"></text>
                 距您<text>{{info.range||0}}km</text>
                   <text class="distance-name">{{info.detailed_address}}</text>
          </view>
    
        </view>
        <!-- 客服 -->
        <view class="right">
          <view class="kefu" @click="goKefu()">
            <text class="iconfont icon-kefu-mendian" />
            <text>客服</text>
          </view>
          <view class="goods" @click="shoppCart">
           <!-- <view class="cartNum" v-if="cart_num>0">
              {{cart_num}}
            </view>
            <text class="iconfont icon-gouwuche-mendian" /> -->
			<uni-badge class="uni-badge-left-margin" :text="cart_num" absolute="rightTop">
			  <view class="iconfont icon-gouwuche-mendian"></view>
			</uni-badge>
            <text>购物车</text>
          </view>
        </view>
      </view>
    </view>
    <!-- 商品排序条件 -->
    <view class="content">
      <view class="nav">
        <view @click="set_where(1)" :class="{'activeColor':active == 1}">
          综合排序
        </view>
        <view @click='set_where(2)' :class="{'activeColor':active == 2}">
          价格
          <image v-if="price==1" src="../../static/images/up.png" alt="" class="nav-img"></image>
          <image v-else-if="price==2" src='../../static/images/down.png' class="nav-img"></image>
          <image v-else src='../../static/images/horn.png' class="nav-img"></image>
        </view>
        <view @click='set_where(3)' :class="{'activeColor':active == 3}">
          销量
          <image v-if="stock==1" src="../../static/images/up.png" alt="" class="nav-img"></image>
          <image v-else-if="stock==2" src='../../static/images/down.png' class="nav-img"></image>
          <image v-else src='../../static/images/horn.png' class="nav-img"></image>
        </view>
        <view @click="set_where(4)">
          <text class="iconfont icon-shaixuan"></text>
          筛选
        </view>
      </view>
    </view>
    <!-- 商品列表 -->
   	<view class="list waterList" >
    				<waterfallsFlow ref="waterfallsFlow" :list="productList" @wapper-lick="godDetail">
    					<!--  #ifdef  MP-WEIXIN -->
    					<view v-for="(item, index) of productList" :key="index" slot="slot{{index}}">
    						<view class="waterfalls">
    							<view class='name line2'>{{item.store_name}}</view>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '1' && !promotions_type">秒杀</span>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '2' && !promotions_type">砍价</span>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '3' && !promotions_type">拼团</span>
    							<text class="label" v-if="item.promotions.title">{{item.promotions.title}}</text>
    							<view class="vip acea-row row-middle">
    								<view class='money font-color'>
    									￥<text class='num'>{{item.price.toString().split(".")[0]}}</text>
    									<text class='nums'
    										v-if="item.price.toString().split('.').length>1">.{{item.price.toString().split(".")[1]}}</text>
    								</view>
    								<view class='vip-money acea-row row-middle' v-if="item.vip_price && item.vip_price > 0">
    									<view>￥{{item.vip_price}}</view>
    									<!-- 	<image src='../../static/images/vip.png' v-if="item.price_type == 'member'"></image> -->
    									<view class="icon on" v-if="item.price_type && item.price_type == 'member'"><text
    											class="iconfont icon-huangguan4"></text>SVIP</view>
    									<view class="icon" v-if="item.price_type && item.price_type == 'level'"><text
    											class="iconfont icon-dengjitubiao"></text>{{item.level_name}}</view>
    								</view>
    							</view>
    							<view class='vip acea-row row-between-wrapper'>
    								<view>已售{{item.sales}}{{item.unit_name || '件'}}</view>
    								<view>评分 {{item.star}}</view>
    							</view>
    						</view>
    					</view>
    					<!--  #endif -->
    
    					<!-- #ifndef  MP-WEIXIN -->
    					<template v-slot:default="item">
    						<view class="waterfalls">
    							<view class='name line2'>{{item.store_name}}</view>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '1' && !promotions_type">秒杀</span>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '2' && !promotions_type">砍价</span>
    							<span class="label"
    								v-if="item.activity && item.activity.type === '3' && !promotions_type">拼团</span>
    							<text class="label" v-if="item.promotions.title">{{item.promotions.title}}</text>
    							<view class="vip acea-row row-middle">
    								<view class='money font-color'>
    									￥<text class='num'>{{item.price.toString().split(".")[0]}}</text>
    									<text class='nums'
    										v-if="item.price.toString().split('.').length>1">.{{item.price.toString().split(".")[1]}}</text>
    								</view>
    								<view class='vip-money acea-row row-middle' v-if="item.vip_price && item.vip_price > 0">
    									<view>￥{{item.vip_price}}</view>
    									<view class="icon on" v-if="item.price_type && item.price_type == 'member'"><text
    											class="iconfont icon-huangguan4"></text>SVIP</view>
    									<view class="icon" v-if="item.price_type && item.price_type == 'level'"><text
    											class="iconfont icon-v"></text>{{item.level_name}}</view>
    								</view>
    							</view>
    							<view class='vip acea-row row-between-wrapper'>
    								<view>已售{{item.sales}}{{item.unit_name || '件'}}</view>
    								<view>评分 {{item.star}}</view>
    							</view>
    						</view>
    					</template>
    					<!-- #endif -->
    				</waterfallsFlow>
    				<view class='loadingicon acea-row row-center-wrapper' v-if='productList.length > 0'>
    								<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>{{loadTitle}}
    							</view>
    			</view>
    <!-- 商品列表 -->
   <view class="default" v-if="productList.length==0 && where.page > 1">
			<image :src="imgHost+'/statics/images/no-thing.png'" mode="" class="img"></image>
      <view class="text">
        暂无商品，去添加点什么吧
      </view>
    </view>
    <!-- 筛选弹窗 -->
    <filterPopup ref="popup" :storeCategory="storeCategory" :storeBrand="storeBrand" @brandChange="brandChange"
      @categoryChange="categoryChange" @submitFn="submitFn"></filterPopup>
    <!-- 客服弹窗 -->
    <Kefu ref="kefu" @closeKefu="closeKefu" :customerList="customerList" :customerType="customerType"></Kefu>
	<!-- #ifdef H5 || MP -->
	<view v-if="isSplicingPlatform" :class="{ mask: collageVisible }" @tap="onCollageClose"></view>
	<view v-if="isSplicingPlatform" :class="{ 'dialog-footer': isFooter, active: collageVisible }" class="dialog">
		<view class="dialog-head">
			<view v-for="item in  deliveryList" :key="item.key" :class="{ active: collageDelivery == item.key }" class="button" @tap="collageDeliveryChange(item.key)">
				<text :class="['iconfont', item.icon]"></text>{{ item.name }}
			</view>
		</view>
		<view class="dialog-body">
			<view class="">{{ collageDelivery == 1 ? '配送地址' : '自提门店' }}</view>
			<view class="dialog-body-main">
				<view class="dialog-body-main-inner">
					<view v-if="collageDelivery == 2 || collageAddress.id" class="dialog-body-left">
						<view class="dialog-body-name">
							{{ collageDelivery == 1 ? `${collageAddress.province}${collageAddress.city}${collageAddress.district}${collageAddress.detail}` : collageStore.name }}
							<text v-if="collageDelivery == 2" class="tag">距离{{ collageStore.range || 0 }}km</text>
						</view>
						<view class="dialog-body-info">
							<view v-if="collageDelivery == 2" class="item">
								{{ collageStore.detailed_address }}
							</view>
							<view v-if="collageDelivery == 1" class="item">
								{{ collageAddress.real_name }} {{ collageAddress.phone }}
							</view>
							<view v-else class="item">
								<text class="iconfont"></text>营业时间：{{ collageStore.day_time || '-' }}
							</view>
						</view>
					</view>
					<view v-if="collageDelivery == 1 && !collageAddress.id" class="dialog-body-left">您还未选择配送地址，先去选择您的收货地址再下单吧~</view>
					<view v-if="collageDelivery == 2" class="dialog-body-right" @tap="showMaoLocation"><text class="iconfont icon-dingwei2"></text></view>
				</view>
				<view class="dialog-body-main-btn">
					<view v-if="collageDelivery == 1" class="" @tap="onAddress">{{ collageAddress.id ? '切换地址' : '选择地址' }}<text class="iconfont icon-xiangyou"></text></view>
					<view v-if="collageDelivery == 2" class="" @tap="goMap(1)">切换门店<text class="iconfont icon-xiangyou"></text></view>
				</view>
			</view>
		</view>
		<view class="dialog-foot">
			<button class="button" hover-class="none" @tap="onCollageClose">我再想想</button>
			<button class="button primary" hover-class="none" @tap="initCollage">发起拼单</button>
		</view>
	</view>
	<addressWindow v-if="isSplicingPlatform" ref="addressWindow" :isFooter='isFooter' :address="address" :pagesUrl="pagesUrl" @changeClose="onAddress" @OnChangeAddress="onAddressChange"></addressWindow>
	<!-- #endif -->
	<!-- #ifdef MP -->
	<!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
	<!-- #endif -->
  </view>
</template>

<script>
	let statusBarHeight = uni.getSystemInfoSync().statusBarHeight;
  import waterfallsFlow from "@/pages/goods/components/maramlee-waterfalls-flow/maramlee-waterfalls-flow.vue";
  import filterPopup from "@/components/filterPopup/index.vue";
  import Kefu from "@/components/kefu/index.vue"
  import {
    mapState,
    mapGetters
  } from 'vuex';
  import {
    getProducts,
    getCustomerList,
    getStoreCategory,
    getStoreBrand
  } from '@/api/new_store.js';
  	import {
  		goShopDetail
  	} from '@/libs/order.js';
	import {
	  HTTP_REQUEST_URL
	} from '@/config/app';
	import {
		hasCollage,
		initCollage,
		isWithin,
	} from '@/api/store.js';
	// #ifdef H5 || MP
	import { getAddressDefault } from '@/api/user.js';
	import addressWindow from '@/components/addressWindow';
	// #endif
	// #ifdef H5
	import {
	  toLogin
	} from '@/libs/login.js';
	// #endif
  export default {
    components: {
      filterPopup,
      waterfallsFlow,
      Kefu,
	  // #ifdef H5 || MP
	  addressWindow
	  // #endif
    },
    props: {
		
      info: {
        type: Object,
        default: {}
      },
		customerType:{
			type:Number,
			default:1
		},
		cart_num:{
			type:Number,
			default:0
		},
		isFooter: {
		  type: Boolean,
		  default: false
		}
    },
    computed: {
      ...mapState({
        cartNum: state => state.indexData.cartNum
      }),
      ...mapGetters(['isLogin', 'uid', 'cartNum']),
    },
    data() {
      return {
				statusBarHeight:statusBarHeight,
				imgHost: HTTP_REQUEST_URL,
        price: 0,
        stock: 0,
        productList: [], // 商品数据
        customerList: [], // 客服数据
        storeCategory: [], // 分类数据
        storeBrand: [], // 品牌数据
        show: false,
        active: 1,
        where: {
          cid: 0,
          sid: 0,
          keyword: '',
          priceOrder: '',
          salesOrder: '',
          news: 0,
          page: 1,
          limit: 5,
          store_id: 0,
          brand_id: '',
        },
        loading: false,
        loadend: false,
        loadTitle: '加载更多',
		deliveryList: [
			{name:'自提',key:2,icon:'icon-ziti'},
			{name:'配送',key:1,icon:'icon-peisong1'},
		],
		// #ifdef H5 || MP
		collageVisible: false,
		collageDelivery: 2,
		collageAddress: {},
		collageStore: this.info,
		address: {
			address: false
		},
		pagesUrl: '',
		isShowAuth: false,
		// #endif
		// #ifdef MP
		isSplicingPlatform: true,
		// #endif
		// #ifndef MP
		isSplicingPlatform: false,
		// #endif
      }
    },
	mounted() {
		// #ifdef H5
		this.isSplicingPlatform = this.$wechat.isWeixin();
		// #endif
		// #ifdef H5 || MP
		if (this.isSplicingPlatform && this.info.store_splicing_switch) {
			this.collageDelivery = this.info.is_store ? 2 : 1;
			uni.$on('activeFn', data => {
				this.collageStore = data;
			});
			uni.$on('addressChange', data => {
				this.collageAddress = data;
				this.address.addressId = data.id;
			});
		}
		// #endif
	},
    methods: {
		// #ifdef H5 || MP
		showMaoLocation() {
			let data = {
			  latitude: Number(this.collageStore.latitude),
			  longitude: Number(this.collageStore.longitude),
			  name: this.collageStore.name,
			  address: `${this.collageStore.address}-${this.collageStore.detailed_address}`,
			};
			// #ifdef H5
			if (this.$wechat.isWeixin()) {
			  data.scale = 13;
			  return this.$wechat.seeLocation(data);
			}
			// #endif
			uni.openLocation(data);
		},
		// #endif
		// #ifdef MP
		// 授权关闭
		authColse: function(e) {
		  this.isShowAuth = e
		},
		// 授权回调
		onLoadFun() {
			setTimeout(function(){
				this.isShowAuth = false;
			},10)
		},
		// #endif
      // 选择品牌
      brandChange(val) {
        this.where.brand_id=val
      },
      // 选择分类
      categoryChange(val) {
        this.where.cid = val.cid
        this.where.sid = val.sid
      },
      // 确认提交
      submitFn(val) {
        if (val == 1) {
          this.getProducts(true)
          this.$refs.popup.visible = false
        } else if (val == 2) {
          this.where.brand_id = ""
          this.where.cid = ""
          this.where.sid = ""
          this.getProducts(true)
            this.$refs.popup.visible = false
        }
      },
      // 打开附近门店
      goMap(val = 0) {
        uni.navigateTo({
          url: `/pages/store/store_list/index?storeFrom=1&type=1&storeId=${val ? this.collageStore.id : this.info.id}&isCollage=${val}`
        });
      },
      // 打开客服
      goKefu() {
        this.getCustomerList()
        this.$refs.kefu.show = true
      },
      // 购物车
      shoppCart() {
        uni.switchTab({
          url: "/pages/order_addcart/order_addcart"
        })
      },
      // 关闭客服
      closeKefu() {
        this.$refs.kefu.show = false
      },
      // 获取客服列表数据
      getCustomerList() {
        getCustomerList(this.where.store_id).then(res => {
          this.customerList = res.data
        })
      },
    	// 去详情页
    			godDetail(item) {
    				this.currentPage = false
    				if (this.promotions_type) {
    					uni.navigateTo({
    						url: `/pages/goods_details/index?id=${item.id}`
    					})
    				} else {
    					goShopDetail(item, this.uid).then(res => {
    						uni.navigateTo({
    							url: `/pages/goods_details/index?id=${item.id}`
    						})
    					})
    				}
    			},
      // 操作
      set_where: function(e) {
        switch (e) {
          case 1:
            this.active = 1
            this.where.priceOrder = ""
            this.where.salesOrder = ""
            this.where.brand_id = ""
            this.price= 0
            break;
          case 2:
            if (this.price == 0) this.price = 1;
            else if (this.price == 1) this.price = 2;
            else if (this.price == 2) this.price = 0;
            if (this.price == 1) {
              this.where.priceOrder = "asc"
            } else if (this.price == 2) {
              this.where.priceOrder = "desc"
            } else {
              this.where.priceOrder = ""
            }
            this.active = 2
            this.stock = 0;

            break;
          case 3:
            if (this.stock == 0) this.stock = 1;
            else if (this.stock == 1) this.stock = 2;
            else if (this.stock == 2) this.stock = 0;

            if (this.stock == 1) {
              this.where.priceOrder = ""
              this.where.salesOrder = "asc"
            } else if (this.stock == 2) {
              this.where.priceOrder = ""
              this.where.salesOrder = "desc"
            } else {
              this.where.salesOrder = ""
            }
            this.active = 3
            this.price = 0
            break;
          case 4:
					  console.log('1111111111');
            this.getStoreCategory()
            this.getStoreBrand()
            this.$refs.popup.visible = true
						this.loadend = false;
						this.loading = false;
            break;
        }
				if(e<4){
					this.loadend = false;
					this.$set(this.where, 'page', 1);
					this.getProducts(true)
				}
      },
      // 商品列表
      getProducts(isPage) {
        let that = this;
        if (that.loadend) return;
        if (that.loading) return;
        if (isPage === true) {
					 that.$refs.waterfallsFlow.refresh();
					 that.where.page = 1;
					 that.$set(that, 'productList', []);
        }
        that.loading = true;
        that.loadTitle = '';
        getProducts(this.where).then(res => {
            	let list = res.data;
      				let productList = that.$util.SplitArray(list, that.productList);
      				let loadend = list.length < that.where.limit;
      				that.loadend = loadend;
      				that.loading = false;
      				that.loadTitle = loadend ? '没有更多内容啦~' : '加载更多';
      				that.$set(that, 'productList', productList);
      				that.$set(that.where, 'page', that.where.page + 1);
          
        }).catch(err => {
          that.loading = false;
          that.loadTitle = '加载更多'
        });
      },
      // 筛选-分类数据
      getStoreCategory() {
        let data = {
          pid: 0
        }
        getStoreCategory(data).then(res => {
          res.data.map(item => {
            this.$set(item, 'disabled', false)
            this.$set(item, 'current', -1)
            item.children.unshift({
              'id': 0,
              'cate_name': '全部'
            })
          })

          this.storeCategory = res.data
        })
      },
      // 筛选-品牌数据
      getStoreBrand() {
        getStoreBrand(this.where).then(res => {
          this.storeBrand = res.data
        })
      },
      // 触底刷新
      onReachBottom() {

        this.getProducts()

      },
	  // #ifdef H5 || MP
	  // 用户默认地址
	  getAddressDefault() {
	    getAddressDefault().then(res => {
	  	  const data = res.data;
	  	  if (Array.isArray(data)) {
	  		return;
	  	  }
	  	  this.onAddressChange(data.id, data, true);
	    });
	  },
	  onCollage() {
		  if (!this.isLogin) {
			  return toLogin();
		  }
		  hasCollage().then(res => {
			  const collageId = res.data.collageId;
			  if (collageId) {
				  uni.navigateTo({
					url: `/pages/store/group_buy/index?collage_id=${collageId}`
				  });
			  } else{
				  this.getAddressDefault();
				  this.$refs.addressWindow.getAddressList();
				  this.collageVisible = true;
			  }
		  });
	  },
	  onCollageClose() {
		  this.collageVisible = false;
	  },
	  initCollage() {
		  let data = {
			  store_id: this.collageStore.id,
			  address_id: this.collageAddress.id || 0,
			  shipping_type: this.collageDelivery == 1 ? 3 : 2
		  };
		  initCollage(data).then(res => {
			  this.collageVisible = false;
			  uni.navigateTo({
				url: `/pages/store/group_buy/index?collage_id=${res.data.collageId}`
			  });
		  }).catch(err => {
			  this.$util.Tips({
				title: err
			  });
		  });
	  },
	  collageDeliveryChange(key) {
		  if (key != 2 || this.info.is_store) {
		  	this.collageDelivery = key;
		  }
	  },
	  onAddress() {
		  this.address.address = !this.address.address;
		  this.pagesUrl = `/pages/users/user_address_list/index?isCollage=1&store_id=${this.info.id}`
		  this.$refs.addressWindow.getAddressList();
	  },
	  onAddressChange(id, data, isInit) {
	    isWithin({
	  	  store_id: this.collageStore.id,
	  	  address_id: id
	    }).then(() => {
	  	 if (!isInit) {
	  		this.onAddress();
	  	 }
	  	 this.collageAddress = data;
	  	 this.address.addressId = data;
	    }).catch(() => {
	  	  if (!isInit) {
	  		  this.$util.Tips({
	  			  title: '您选择的地址超出门店配送范围，请重新选择'
	  		  });
	  	  }
	    });
	  },
	  // #endif
    }
  }
</script>

<style lang="scss">
	page {
	  background-color: #F5F5F5 !important;
	}

  .activeColor {
    color: var(--view-theme) !important;
  }

  .header {
		// #ifndef MP
    margin-bottom: 40rpx;
    // #endif
    .header-img {
      width: 100%;
      height: 300rpx;
			display: block;
    }

    .search {
      // position: absolute;
			// #ifndef MP
      // top: 56rpx;
			// #endif
      // left: 20rpx;
			// #ifdef MP
			// width: 520rpx;
			// #endif
      // #ifndef MP
      // width: 710rpx;
      // #endif
	  flex: 1;
      height: 64rpx;
      background: rgba(255, 255, 255, 0.2800);
      border-radius: 43rpx;
      font-weight: 400;
      color: #FFFFFF;
      font-size: 26rpx;
      line-height: 64rpx;
			&.on{
				background:unset;
			}
			.searchCon{
				width: 100%;
				height: 64rpx;
				background: rgba(255, 255, 255, 0.2800);
				border-radius: 43rpx;
			}
      .icon-xiazai5 {
        margin-left: 32rpx;
        margin-right: 12rpx;
      }
    }

    .address {
      position: absolute;
      top: 160rpx;
      margin: 0 20rpx;
      width: 710rpx;
      height: 192rpx;
      background: #fff;
      border-radius: 14rpx;
      padding: 30rpx 20rpx;
      display: flex;
			justify-content: space-between;

      .left {
        .left-title {
          height: 30rpx;
          line-height: 30rpx;
          display: flex;

          image {
            width: 30rpx;
            height: 28rpx;
          }

          .text {
            font-weight: 600;
            color: #333333;
            font-size: 30rpx;
            margin: 0 10rpx;
						max-width: 390rpx;
          }

          .icon-xiangyou {
            color: #333;
            font-size: 24rpx;
          }
        }

        .time {
          margin: 20rpx 0;
          font-size: 22rpx;
          font-weight: 400;
          color: #666666;
        }

        .distance {
          width: 500rpx;
          font-size: 22rpx;
          color: #666666;
          font-weight: 400;
          .icon-chakanditu {
            margin-right: 8rpx;
            font-size: 28rpx;
            color: #ccc;
          }
          .distance-name {
            display: inline-block;
            width: 286rpx;
            height: 22rpx;
            border-left: 1px solid #000;
            margin-left: 16rpx;
            line-height: 22rpx;
            text-align: left;
            padding-left: 16rpx;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

          }
        }
      }

      .right {
        margin-top: 36rpx;
        display: flex;
        font-size: 20rpx;
        color: #333333;

        .kefu {
          margin-right: 30rpx;
          display: flex;
          flex-direction: column;
          align-items: center;

          .icon-kefu-mendian {
            font-size: 36rpx;
          }
        }
      }

      .goods {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;

        .icon-gouwuche-mendian {
          font-size: 36rpx;

        }

        .cartNum {
          position: absolute;
          top: -8rpx;
          right: 2rpx;
          width: 30rpx;
          height: 30rpx;
          line-height: 30rpx;
          text-align: center;
          font-weight: 500;
          color: #FFFFFF;
          border-radius: 50%;
          background: var(--view-theme);
          border: 1px solid #FFFFFF;
        }

      }
    }
  }

  .goods-list {
    padding: 0 30rpx;
    margin-bottom: 50rpx !important;

  }

  .content {
    height: 100%;
	position: -webkit-sticky;
	position: sticky;
	top: var(--window-top);
	z-index: 99;

    // margin-bottom: 200rpx;
    .nav {
      padding: 40rpx 50rpx 40rpx 50rpx;
      display: flex;
      justify-content: space-between;
      font-size: 28rpx;
      font-weight: 500;
      color: #666666;
	  background: #f5f5f5;

      .nav-img {
        width: 15rpx;
        height: 18rpx;
        margin-left: 10rpx;
      }

      .icon-shaixuan {
        font-size: 24rpx;
        margin-right: 10rpx;
      }
    }
  }
  .vip {
    margin: 20rpx 0 10rpx 0;
    .price {
      font-size: 34rpx;
      font-weight: 600;
      color: var(--view-theme);
    }
    .sold {
      font-size: 24rpx;
      font-weight: 400;
      color: #999999;
    }
  }
	.list {
		padding: 0  20rpx 90rpx 20rpx;
	}

.list .waterList {
  margin-bottom: 60rpx;
		// margin-top: 192rpx;
	}

  .default {
    margin-top: 100rpx;
    display: flex;
    flex-direction: column;
    align-items: center;

    .img {
      width: 414rpx;
      height: 256rpx;
    }

    .text {
      font-size: 26rpx;
      font-weight: 400;
      color: #999999;
    }
  }
  	.waterfalls {
  		padding: 10rpx 16rpx 16rpx 16rpx;
  		color: #222;
  
  		.name {
  			font-size: 28rpx;
  		}
  
  		.label {
  			font-size: 20rpx;
  			color: var(--view-theme);
  			border-radius: 4rpx;
  			border: 1px solid var(--view-theme);
  			padding: 0 6rpx;
  			display: inline-block;
  			margin-top: 10rpx;
  			margin-right: 10rpx;
  		}
  
  		.money {
  			font-size: 26rpx;
  			font-weight: 700;
  
  			.num {
  				font-size: 34rpx;
  			}
  
  			.nums {
  				font-size: 28rpx;
  			}
  		}
  
  		.vip {
  			font-size: 22rpx;
  			color: #aaa;
  			margin-top: 6rpx;
  
  			.vip-money {
  				font-size: 24rpx;
  				color: #282828;
  				font-weight: bold;
					.icon{
						font-size: 15rpx;
						background: #FF9500;
						color: #fff;
						border-radius: 18rpx;
						padding: 1rpx 6rpx;
						margin-left: 10rpx;
						min-width: 60rpx;
						
						.iconfont {
							font-size: 15rpx;
							margin-right: 5rpx;
						}
						
						&.on {
							background: #333;
							color: #FDDAA4;
							min-width: unset;
						}
					}
  
  				image {
  					width: 46rpx;
  					height: 21rpx;
  					margin-left: 4rpx;
  				}
  			}
  		}
  	}
	.input-wrapper {
		position: absolute;
		left: 20rpx;
		/* #ifndef MP */
		top: 56rpx;
		right: 20rpx;
		/* #endif */
		/* #ifdef MP */
		width: 520rpx;
		/* #endif */
		
		.input {
			flex: 1;
		}
		
		.group-button {
		  height: 64rpx;
		  padding: 0 24rpx;
		  border: 1rpx solid #DDDDDD;
		  border-radius: 32rpx;
		  margin-right: 40rpx;
		  font-weight: 500;
		  font-size: 26rpx;
		  color: #333333;
		  
		  .iconfont {
			  margin-right: 10rpx;
			  font-size: 26rpx;
			  color: var(--view-theme);
		  }
		}
	}
	
	/* #ifdef H5 || MP */
	.dialog {
		  position: fixed;
		  right: 0;
		  left: 0;
		  z-index: 101;
		  padding: 36rpx 40rpx 40rpx;
		  border-radius: 12rpx 12rpx 0 0;
		  background-color: #FFFFFF;
		  transform: translateY(100%);
		  transition: 0.3s;
		  /* #ifdef H5 */
		  bottom: 94rpx;
		  bottom: calc(94rpx + constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
		  bottom: calc(94rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
		  /* #endif */
		  /* #ifdef MP */
		  bottom: 0rpx;
		  bottom: calc(0rpx + constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
		  bottom: calc(0rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
		  /* #endif */
		  
		  &.dialog-footer {
			  /* #ifdef MP */
			  bottom: 98rpx;
			  bottom: calc(98rpx + constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
			  bottom: calc(98rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
			  /* #endif */
		  }
		  
		  &.active {
			  transform: translateY(0);
		  }
	}
	.dialog-head {
		  display: flex;
		  
		  .button {
			  flex: 1;
			  display: flex;
			  justify-content: center;
			  align-items: center;
			  height: 138rpx;
			  border: 4rpx solid #DDDDDD;
			  border-radius: 12rpx;
			  font-weight: 500;
			  font-size: 32rpx;
			  color: #333333;
			  cursor: pointer;
			  
			  +.button {
				  margin-left: 46rpx;
			  }
			  
			  .iconfont {
				  margin-right: 40rpx;
				  font-size: 80rpx;
				  color: #999999;
			  }
			  
			  &.active {
				  border: 4rpx solid var(--view-theme);
				  
				  .iconfont {
					  color: var(--view-theme);
				  }
			  }
		  }
	}
	.dialog-body {
		  margin-top: 60rpx;
		  font-size: 24rpx;
		  color: #999999;
		  
		  .dialog-body-main {
			  .dialog-body-main-inner {
				  display: flex;
				  margin-top: 32rpx;
			  }
			  
			  .dialog-body-main-btn {
				  display: flex;
				  justify-content: center;
				  align-items: center;
				  margin-top: 56rpx;
				  
				  .iconfont {
					  margin-left: 12rpx;
					  font-size: 22rpx;
					  color: #666666;
				  }
			  }
		  }
		  
		  .dialog-body-left {
			  flex: 1;
			  min-width: 0;
			  margin-right: 20rpx;
		  }
		  
		  .dialog-body-right {
			  display: flex;
			  justify-content: center;
			  align-items: center;
			  width: 40rpx;
			  height: 40rpx;
			  border-radius: 50%;
			  background-color: var(--view-minorColorT);
			  font-size: 22rpx;
			  color: var(--view-theme);
			  cursor: pointer;
		  }
		  
		  .dialog-body-name {
			  font-weight: 500;
			  font-size: 24rpx;
			  line-height: 1.2;
			  color: #333333;
			  
			  .tag {
				  margin-left: 14rpx;
				  font-size: 24rpx;
				  color: #888888;
			  }
		  }
		  
		  .dialog-body-info {
			  margin-top: 28rpx;
			  
			  .item {
				  display: flex;
				  font-size: 22rpx;
				  line-height: 1.5;
				  color: #888888;
				  
				  +.item {
					  margin-top: 20rpx;
				  }
			  }
		  }
	}
	.dialog-foot {
		  display: flex;
		  margin-top: 78rpx;
		  
		  .button {
			  display: flex;
			  justify-content: center;
			  align-items: center;
			  flex: 1;
			  min-width: 0;
			  height: 92rpx;
			  border-radius: 46rpx;
			  background-color: #EEEEEE;
			  font-size: 30rpx;
			  color: #666666;
			  cursor: pointer;
			  
			  +.button {
				  margin-left: 46rpx;
			  }
			  
			  &.primary {
				  background-color: var(--view-theme);
				  color: #FFFFFF;
			  }
		  }
	}
	/* #endif */
</style>
