<template>
	<!-- 商品属性规格弹窗 -->
	<view>
		<view class="product-window"
			:class="(attr.cartAttr === true ? 'on' : '') + ' ' + (iSbnt?'join':'') + ' ' + (iScart?'joinCart':'')">
			<view class="textpic acea-row row-between-wrapper">
				<view class="pictrue" @click="showImg()">
					<image :src="attr.productSelect.image"></image>
					<view class="icon acea-row row-center-wrapper" v-if="cusPreviewImg">
						<view class="iconfont icon-fangda1"></view>
					</view>
				</view>
				<view class="text">
					<view class="line1">
						{{ type == 'setMeal'?title : attr.productSelect.store_name }}
					</view>
					<view class="money font-color">
						<view class="acea-row row-middle">
							<text v-if="type =='points'">
								<text v-if="parseFloat(attr.productSelect.integral)"><text class="num">{{ attr.productSelect.integral }}</text>积分</text>
								<text v-if="parseFloat(attr.productSelect.price) && parseFloat(attr.productSelect.integral)">+</text>
								<text v-if="parseFloat(attr.productSelect.price)">￥<text class="num">{{ attr.productSelect.price }}</text></text>
							</text>
							<text v-else>￥<text class="num">{{ attr.productSelect.price }}</text></text>
							<text class='vip-money'
								v-if="is_vip>0 && attr.productSelect.vip_price && storeInfo && storeInfo.price_type == 'member'">￥{{attr.productSelect.vip_price}}</text>
							<view class="vipImg" v-if="is_vip>0 && attr.productSelect.vip_price && storeInfo && storeInfo.price_type == 'member'">
								<image src="../../static/images/vip.png"></image>
							</view>
							
							<view class="icon" v-if="is_vip>0 && attr.productSelect.vip_price && storeInfo && storeInfo.price_type == 'level'"><text class="iconfont icon-dengjitubiao"></text>{{storeInfo.level_name}}</view>
							
						</view>
						<text class="stock" v-if='isShow'>库存: {{ attr.productSelect.stock }}</text>
						<text class='stock' v-if="limitNum">{{type == 'seckill' ? '限量' : type == 'points'?'剩余':'库存'}}: {{attr.productSelect.quota}}</text>
						<slot name="bottom" :attr="attr"></slot>
					</view>
				</view>
				<view class="iconfont icon-guanbi" @click="closeAttr"></view>
			</view>
			<view class="rollTop">
				<view class="productWinList">
					<view class="item" v-for="(item, indexw) in attr.productAttr" :key="indexw">
						<view class="title">{{ item.attr_name }}</view>
						<view class="listn acea-row row-middle">
							<view class="itemn" :class="item.index === itemn.attr ? 'on' : ''"
								v-for="(itemn, indexn) in item.attr_value" @click="tapAttr(indexw, indexn)"
								:key="indexn">
								{{ itemn.attr }}
							</view>
						</view>
					</view>
					<view class="item" v-if="isDelivery">
						 <view class="title">配送方式</view>
						 <view class="listn acea-row row-middle">
							 <view class="itemn" v-if="attr.deliveryType.includes('1')" @click="getstoreInfo('1')" :class="flag==1?'on':isStoreBuy?'on2':''">商城配送</view>
							 <view class="itemn" v-if="attr.deliveryType.includes('2') && attr.store_self_mention && selfStoreList.length" @click="getstoreInfo('2')" :class="flag==2?'on':''">门店自提</view>
							 <view class="itemn" v-if="attr.deliveryType.includes('3') && storeList.length" @click="getstoreInfo('3')" :class="flag==3?'on':''">门店配送</view>
						 </view>
					</view>
					<view class="address acea-row row-middle" v-if="flag ==1 && isDelivery" @click="openAddress">
						<view class="adsInfo">{{addressInfo}}</view>
						<view class="iconfont icon-gengduo3"></view>
					</view>
					<view class="address acea-row row-middle" v-if="flag ==3 && isDelivery" @click="openStore">
						<view class="adsInfo">{{deliveryName}}{{deliveryAddress}}{{distance}}</view>
						<view class="iconfont icon-gengduo3" v-if="attr.isType != 1"></view>
					</view>
					<view class="address" v-if="flag ==2 && isDelivery"  @click="openStore">
						<view>{{deliveryName}}</view>
						<view class="info acea-row row-between-wrapper">
							<view class="con">{{deliveryAddress}}</view>
							<view>{{distance}}<text class="iconfont icon-gengduo3" v-if="attr.isType != 1"></text></view>
						</view>
					</view>
				</view>
				<view class="cart acea-row row-between-wrapper" v-if="type != 'setMeal' && type !='points'">
				  <view class="title">数量</view>
					<view class="carnum acea-row row-left">
						<view class="item reduce acea-row row-center-wrapper"
							:class="attr.productSelect.cart_num <= 1 ? 'on' : ''" v-if="attr.productSelect.cart_num <= 1">
							<text class="iconfont icon-shangpinshuliang-jian"></text>
						</view>
						<view class="item reduce acea-row row-center-wrapper"
							:class="attr.productSelect.cart_num <= 1 ? 'on' : ''" @click="CartNumDes" v-else>
							<text class="iconfont icon-shangpinshuliang-jian"></text>
						</view>
						<view class='item num acea-row row-middle'>
							<input type="number" v-model="attr.productSelect.cart_num"
								data-name="productSelect.cart_num" :always-embed="true" :adjust-position="true" cursor-spacing="30"
								@input="bindCode(attr.productSelect.cart_num)"></input>
						</view>
						<view v-if="iSplus" class="item plus acea-row row-center-wrapper" :class="
				      attr.productSelect.cart_num >= attr.productSelect.stock
				        ? 'on'
				        : ''
				    " @click="CartNumAdd">
							<text class="iconfont icon-shangpinshuliang-jia"></text>
						</view>
						<view v-else class='item plus'
							:class='(attr.productSelect.cart_num >= attr.productSelect.quota) || (attr.productSelect.cart_num >= attr.productSelect.product_stock) || (attr.productSelect.cart_num >= attr.productSelect.num) || (type=="seckill" && attr.productSelect.cart_num >= attr.productSelect.once_num)? "on":""'
							@click='CartNumAdd'>+</view>
					</view>
				</view>
			</view>
			<view class="joinBnt bg-color"
				v-if="iSbnt && attr.productSelect.product_stock>0 &&attr.productSelect.quota>0" @click="goCat">我要参团
			</view>
			<view class="joinBnt on"
				v-else-if="(iSbnt && attr.productSelect.quota<=0)||(iSbnt &&attr.productSelect.product_stock<=0)">已售罄
			</view>
			<view class="joinBnt bg-color" v-if="iScart && attr.productSelect.stock" @click="goCat">确定</view>
			<view class="joinBnt on" v-else-if="iScart && !attr.productSelect.stock">已售罄</view>
		</view>
		<!-- 选择门店 -->
    <view class="product-window" :class="isStore?'store':''">
			<view class="storeTitle">选择门店<text class="iconfont icon-guanbi5" @click="closeStore"></text></view>
			<view class="storeList">
				<view class="item" :class="active == index?'on':''" v-for="(item,index) in storeList" :key="index" @click="tapStore(index,item)">
					<view class="name line1">{{item.name}}</view>
					<view class="address acea-row row-between">
						<view class="iconfont icon-dingwei2"></view>
						<view class="info">{{item.address}}</view>
					</view>
					<view class="time acea-row row-middle">
						<view class="iconfont icon-yingyeshijian2"></view>
						<view>营业时间：{{item.day_time}}</view>
					</view>
					<view class="iconfont icon-xuanzhong6" v-if="active == index"></view>
				</view>
			</view>
    </view>
    <addressWindow ref="addressWindow" :isFooter="isFooter" :pagesUrl="pagesUrl" :fromType="1" :address="address" @changeClose="changeClose" @OnChangeAddress="OnChangeAddress">
    </addressWindow>
		<view class="mask" @touchmove.prevent :hidden="attr.cartAttr === false" @click="closeAttr"></view>
		<view class="mask on" @touchmove.prevent :hidden="isStore === false" @click="closeStore"></view>
	</view>
</template>

<script>
  import addressWindow from '@/components/addressWindow';
  import {getAddressList} from '@/api/user.js'
  import {storeListApi} from '@/api/store.js'
	import {
		mapGetters,
		mapState
	} from 'vuex';
	export default {
		computed: {
			...mapState({
				nearbyStore: state => state.app.nearbyStore
			}),
			...mapGetters(['isLogin'])
		},
		components:{
		  addressWindow
		},
		props: {
			isStoreBuy: {
				type: Number,
				value: 0
			},
			productId: {
				type: Number | String,
				value: 0
			},
			productType: {
				type: Number | String,
				value: 0
			},
			cusPreviewImg: {
				type: Number,
				value: 0
			},
			title: {
				type: String,
				default: ''
			},
			attr: {
				type: Object,
				default: () => {}
			},
			storeInfo: {
				type: Object,
				default: () => {}
			},
			limitNum: {
				type: Number,
				value: 0
			},
			isShow: {
				type: Number,
				value: 0
			},
			iSbnt: {
				type: Number,
				value: 0
			},
			iSplus: {
				type: Number,
				value: 0
			},
			iScart: {
				type: Number,
				value: 0
			},
			is_vip: {
				type: Number,
				value: 0
			},
			type: {
				type: String,
				default: ''
			},
			isFooter: {
			  type: Boolean,
			  default: false
			}
		},
		data() {
			return {
        flag:1,
        addressInfo:'', // 商城快递
        deliveryName:'', // 门店配送
				distance:'',
				deliveryAddress:'',
				address: {
					address: false
				},
				pagesUrl:'',
			 user_latitude: 0,
			 user_longitude: 0,
			 isDelivery: false,
			 storeList: [],
			 deliveryStoreList: [],
			 selfStoreList: [],
			 active:0,
			 isStore: false
      }
		},
    
		mounted() {
		 try {
		 	this.user_latitude = uni.getStorageSync('user_latitude');
		 	this.user_longitude = uni.getStorageSync('user_longitude');
		 } catch (e) {}
    },
		watch: {
			// 'attr.deliveryType': {
			// 	handler(newV) {
			// 		if(newV){
			// 			if(this.active<1){
			// 				if (this.user_latitude && this.user_longitude) {
			// 					this.getList();
			// 				} else {
			// 					this.selfLocation();
			// 				}
			// 			}
			// 			this.flag = newV[0];
			// 			this.$emit('deliveryFun',newV[0]);
			// 		}
			// 	},
			// 	deep: true
			// }
			'attr.deliveryType'(newValue, oldValue) {
				if (JSON.stringify(newValue) != JSON.stringify(oldValue)) {
					if (newValue.length) {
						if(this.active<1){
							this.getList();
							// if (this.user_latitude && this.user_longitude) {
							// 	this.getList();
							// } else {
							// 	this.selfLocation();
							// }
						}
						let num = 1;
						if(newValue[0] == 1 && this.isStoreBuy){
							num = newValue[1];
						}else{
							num = newValue[0];
						}
						this.flag = num
						this.$emit('deliveryFun',num);
					}
				}
			}
		},
		methods: {
			closeStore(){
				this.isStore = false;
			},
			openStore(){
				if(this.attr.isType != 1){
					this.isStore = true;
				}
			},
			// 切换门店
				tapStore(index,item){
					this.active = index;
					this.deliveryName = item.name;
					this.deliveryAddress = item.detailed_address+'\xa0';
					this.distance = '距您'+item.range+'km';
					this.isStore = false;
					this.$emit('onstoreId',item,this.flag);
				},
				// 切换地址
				OnChangeAddress: function(e,row) {
				  this.address.address = false;
					this.addressInfo = row.province+'省'+'\xa0'+row.city+'\xa0'+row.district+'\xa0'+row.street+'\xa0'+row.detail
					this.$emit('onAddressId',row)
				},
				// 打开弹窗
			openAddress(){
				this.$refs.addressWindow.getAddressList();
				this.address.address = true;
				this.pagesUrl = '/pages/users/user_address/index'
			},
			goCat: function() {
				this.$emit('goCat');
			},
      // 配送地址
      getAddressList() {
        getAddressList({
					page: 1,
					limit: 1
				}).then(res=>{
					let data = res.data[0];
					if(res.data.length){
						 this.addressInfo = data.province+'省'+'\xa0'+data.city+'\xa0'+data.district+'\xa0'+data.street+'\xa0'+data.detail
						 this.$emit('onAddressId',data)
					}else{
						this.addressInfo = '点击添加地址'
					}
        })
      },
			// 关闭地址弹窗；
			changeClose: function() {
			  this.$set(this.address, 'address', false);
			},
			selfLocation() {
				let self = this
				// #ifdef H5
				if (self.$wechat.isWeixin()) {
					self.$wechat.location().then(res => {
						this.user_latitude = res.latitude;
						this.user_longitude = res.longitude;
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						self.getList();
					})
				} else {
					// #endif	
					uni.getLocation({
						type: 'wgs84',
						success: (res) => {
							try {
								this.user_latitude = res.latitude;
								this.user_longitude = res.longitude;
								uni.setStorageSync('user_latitude', res.latitude);
								uni.setStorageSync('user_longitude', res.longitude);
							} catch {}
							self.getList();
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
						// complete: function() {
						// 	self.getList();
						// }
					});
					// #ifdef H5	
				}
				// #endif
			},
			getList: function(id) {
				let data = {
					latitude: this.user_latitude || "", //纬度
					longitude: this.user_longitude || "", //经度
					page: 1,
					limit: 100,
					product_id: this.productId,
					is_store:'',   //查找所有门店列表
					type: this.type == 'seckill'?1:0,
					store_id: id?id:this.nearbyStore
				};
				storeListApi(data)
					.then(res => {
						let list = res.data.list.list;
						if(!id && list.length && this.attr && ((this.attr.deliveryType.indexOf('2') != -1 && this.attr.store_self_mention) || this.attr.deliveryType.indexOf('3') != -1)){
							this.isDelivery = true;
							if(this.$store.getters.isLogin){
								this.getAddressList()
							}
						}
						// 拆分自提门店列表和配送门店列表
						let selfStoreList = [];
						list.forEach(function(item,index){
							if(item.is_store === 1) selfStoreList.push(item);
						});
						this.storeList = this.flag==2?selfStoreList:list;
						this.selfStoreList = selfStoreList;//门店自提
						this.deliveryStoreList = list;//门店配送
						this.active = 0;
						this.deliveryName = list[0].name;
						this.deliveryAddress = list[0].detailed_address+'\xa0';
						this.distance = '距您'+list[0].range+'km';
						this.$emit('onstoreId',list[0],this.flag);
					})
					.catch(err => {
						this.$util.Tips({
							title: err
						})
					});
			},
			/**
			 * 购物车手动输入数量
			 * 
			 */
			bindCode: function(e) {
				this.$emit('iptCartNum', this.attr.productSelect.cart_num);
			},
			closeAttr: function() {
				this.$emit('myevent');
			},
			CartNumDes: function() {
				this.$emit('ChangeCartNum', false);
			},
			CartNumAdd: function() {
				this.$emit('ChangeCartNum', true);
			},
			tapAttr: function(indexw, indexn) {
				let that = this;
				that.$emit("attrVal", {
					indexw: indexw,
					indexn: indexn
				});
				this.$set(this.attr.productAttr[indexw], 'index', this.attr.productAttr[indexw].attr_values[indexn]);
				let value = that
					.getCheckedValue()
					.join(",");
				that.$emit("ChangeAttr", value);

			},
			//获取被选中属性；
			getCheckedValue: function() {
				let productAttr = this.attr.productAttr;
				let value = [];
				for (let i = 0; i < productAttr.length; i++) {
					for (let j = 0; j < productAttr[i].attr_values.length; j++) {
						if (productAttr[i].index === productAttr[i].attr_values[j]) {
							value.push(productAttr[i].attr_values[j]);
						}
					}
				}
				return value;
			},
		  // 选择配送方式
		  getstoreInfo(index) {
					if(index == 1 && this.isStoreBuy){
						return false
					}
					if(index === '2' || index === '3'){
						if ((!this.user_latitude || !this.user_longitude) && !uni.getStorageSync('refuseLocation')) {
							this.selfLocation();
						}
					}
					if(index === '2') this.storeList = this.selfStoreList; //门店自提
					if(index === '3') this.storeList = this.deliveryStoreList; //门店配送
					this.flag = index;
					this.active = 0;
					this.deliveryName = this.storeList[0].name;
					this.deliveryAddress = this.storeList[0].detailed_address+'\xa0';
					this.distance = '距您'+this.storeList[0].range+'km';
					this.$emit('deliveryFun',index);
					this.$emit('onstoreId',this.storeList[0],index);
		  },
		  showLocation(){
			  if ((!this.user_latitude || !this.user_longitude) && !uni.getStorageSync('refuseLocation')) {
			  	this.selfLocation();
			  }
		  },
		  showImg() {
		  	this.$emit('getImg');
		  }
		}
	}
</script>

<style scoped lang="scss">
	.mask.on{
		z-index: 100 !important;
	}
	.product-window{
		 .productWinList{
			 .address{
				 width: 690rpx;
				 background: #F5F5F5;
				 padding: 30rpx 24rpx;
				 margin: 0 auto;
				 margin-top: 20rpx;
				 font-weight: 400;
				 color: #333333;
				 font-size: 26rpx;
				 border-radius: 10rpx;
				 .adsInfo{
					 max-width: 594rpx;
				 }
				 .iconfont{
					 font-size: 18rpx;
					 margin-left: 12rpx;
				 }
				 .info{
					 font-weight: 400;
					 color: #999999;
					 margin-top: 12rpx;
					 .con{
						 width: 416rpx;
					 }
				 }
			 }
		 }
	}
  .active {
    position: relative;
    border: 1px solid var(--view-theme);
    .icon-xuanzhong6 {
      font-size: 46rpx;
      position: absolute;
      bottom: -4rpx;
      right: -6rpx;
      color: var(--view-theme);
    }
  }
	.vip-money {
		color: #282828;
		font-size: 28rpx;
		font-weight: 700;
		margin-left: 6rpx;
	}
	.vipImg {
		width: 56rpx;
		height: 20rpx;
		margin-left: 6rpx;

		image {
			width: 100%;
			height: 100%;
			display: block;
		}
	}
	
	.product-window{
		&.store{
			background-color: #F5F5F5;
			border-radius: 20rpx 20rpx 0 0;
			transform: translate3d(0, 0, 0);
			z-index: 102;
			margin: 0;
		}
		.storeTitle{
			text-align: center;
			height: 100rpx;
			line-height: 100rpx;
			font-size: 32rpx;
			font-weight: 500;
			color: #282828;
			position: relative;
			.iconfont{
				position: absolute;
				font-size: 35rpx;
				right: 30rpx;
			}
		}
		.storeList{
			max-height: 690rpx;
			overflow: auto;
			.item{
				width: 690rpx;
				background: #FFFFFF;
				border-radius: 12rpx;
				margin: 0 auto 20rpx auto;
				padding: 28rpx 30rpx;
				border: 1px solid #fff;
				position: relative;
				&.on{
					border-color: var(--view-theme);
				}
				.icon-xuanzhong6{
					position: absolute;
					right: -6rpx;
					bottom: -6rpx;
					color: var(--view-theme);
					font-size: 54rpx;
				}
				.name{
					font-weight: 500;
					color: #333333;
					font-size: 28rpx;
				}
				.time{
					font-weight: 400;
					color: #888888;
					font-size: 22rpx;
					margin-top: 15rpx;
					.iconfont{
						font-size: 20rpx;
						margin-right: 8rpx;
					}
				}
				.address{
					font-weight: 400;
					color: #888888;
					font-size: 22rpx;
					margin-top: 13rpx;
					.iconfont{
						font-size: 20rpx;
						margin-right: 8rpx;
						margin-top: 6rpx;
					}
					.info{
						width: 590rpx;
						line-height: 1.5;
					}
				}
			}
		}
	}

	.product-window {
		position: fixed;
		bottom: 0;
		width: 100%;
		left: 0;
		background-color: #fff;
		z-index: 100;
		border-radius: 16rpx 16rpx 0 0;
		transform: translate3d(0, 100%, 0);
		transition: all .3s cubic-bezier(.25, .5, .5, .9);
		padding-bottom: 140rpx;
		padding-bottom: calc(140rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
		padding-bottom: calc(140rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
	}

	.product-window.on {
		transform: translate3d(0, 0, 0);
	}

	.product-window.join {
		padding-bottom: 30rpx;
	}

	.product-window.joinCart {
		padding-bottom: 30rpx;
		z-index: 10000;
	}

	.product-window .textpic {
		padding: 0 130rpx 0 30rpx;
		margin-top: 29rpx;
		position: relative;
	}

	.product-window .textpic .pictrue {
		width: 150rpx;
		height: 150rpx;
		position: relative;
		.icon{
			width: 30rpx;
			height: 30rpx;
			background-color: rgba(0,0,0,0.4);
			border-radius: 4rpx;
			position: absolute;
			bottom: 8rpx;
			right: 8rpx;
			text-align: center;
			line-height: 23rpx;
			.iconfont{
				color: #fff;
				font-size: 20rpx;
			}
		}
	}

	.product-window .textpic .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 10rpx;
	}

	.product-window .textpic .text {
		width: 410rpx;
		font-size: 32rpx;
		color: #202020;
	}

	.product-window .textpic .text .money {
		font-size: 24rpx;
		margin-top: 40rpx;
		.icon{
			display: inline-block;
			font-size: 16rpx;
			font-weight: normal;
			background: #FF9500;
      color: #fff;
			border-radius: 18rpx;
			padding: 2rpx 6rpx;
			margin-left: 10rpx;
			.iconfont{
				font-size: 16rpx;
				margin-right: 4rpx;
				color: #fff;
			}
		}
	}

	.product-window .textpic .text .money .num {
		font-size: 36rpx;
	}

	.product-window .textpic .text .money .stock {
		color: #999;
		margin-left: 6rpx;
	}

	.product-window .textpic .icon-guanbi {
		position: absolute;
		right: 30rpx;
		top: -5rpx;
		font-size: 35rpx;
		color: #8a8a8a;
	}

	.product-window .rollTop {
		max-height: 535rpx;
		overflow: auto;
		margin-top: 36rpx;
	}

	.product-window .productWinList .item~.item {
		margin-top: 36rpx;
	}

	.product-window .productWinList .item .title {
		font-size: 30rpx;
		color: #999;
		padding: 0 30rpx;
	}

	.product-window .productWinList .item .listn {
		padding: 0 30rpx 0 16rpx;
	}

	.product-window .productWinList .item .listn .itemn {
		border: 1px solid #F2F2F2;
		font-size: 26rpx;
		color: #282828;
		padding: 7rpx 33rpx;
		border-radius: 25rpx;
		margin: 20rpx 0 0 14rpx;
		background-color: #F2F2F2;
		word-break: break-all;
	}

	.product-window .productWinList .item .listn .itemn.on {
		color: var(--view-theme);
		background: var(--view-minorColorT);
		border-color: var(--view-theme);
	}
	.product-window .productWinList .item .listn .itemn.on2{
		background: #bbb;
		color: #fff;
		border-color: #bbb;
	}
	.product-window .productWinList .item .listn .itemn.limit {
		color: #999;
		text-decoration: line-through;
	}

	.product-window .cart {
		margin-top: 36rpx;
		padding: 0 30rpx;
    
	}
	.product-window .cart .title {
		font-size: 30rpx;
		color: #999;
	}

	.product-window .cart .carnum {
		height: 54rpx;
		margin-top: 24rpx;
	}

	.product-window .cart .carnum .iconfont {
		font-size: 25rpx;
	}

	.product-window .cart .carnum view {
		// border: 1px solid #a4a4a4;
		width: 84rpx;
		text-align: center;
		height: 100%;
		line-height: 54rpx;
		color: #282828;
		font-size: 45rpx;
	}

	.product-window .cart .carnum .reduce {
		border-right: 0;
		border-radius: 6rpx 0 0 6rpx;
		line-height: 48rpx;
		font-size: 60rpx;
	}

	.product-window .cart .carnum .reduce.on {
		color: #DEDEDE;
	}

	.product-window .cart .carnum .plus {
		border-left: 0;
		border-radius: 0 6rpx 6rpx 0;
		line-height: 46rpx;
	}

	.product-window .cart .carnum .plus.on {
		// border-color: #e3e3e3;
		color: #dedede;
	}

	.product-window .cart .carnum .num {
		background: rgba(242, 242, 242, 1);
		color: #282828;
		font-size: 28rpx;
	}

	.product-window .joinBnt {
		font-size: 30rpx;
		width: 620rpx;
		height: 86rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 86rpx;
		color: #fff;
		margin: 21rpx auto 0 auto;
	}

	.product-window .joinBnt.on {
		background-color: #bbb;
		color: #fff;
	}
</style>
