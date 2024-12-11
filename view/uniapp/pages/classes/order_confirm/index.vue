<template>
  <view :style="colorStyle" class="box">
	  <template v-if="cartInfo.length">
	  	<view>
			<view class="detail">
				<image :src="cartInfo[0].productInfo.slider_image[0]" mode="aspectFill"></image>
				<view class="info">
					<view class="title">
						{{cartInfo[0].productInfo.store_name}}
					</view>
					<view class="item">
						课程类型：
						<text>{{cartInfo[0].productInfo.attrInfo.suk}}</text>
					</view>
					<view class="item">
						课程价格：
						<text class="price">{{cartInfo[0].truePrice}}</text>
					</view>
				</view>
			</view>
			
			<view class="modules">
				<view class="title">
					优惠信息
				</view>
				<view class="form">
					<view class="form-item" @click="couponTap" v-if="coupon.list.length">
						<view class="form-label">
							<text>优惠券</text>
						</view>
						<view class="form-wrapper" >
							<text :class="{complete: order_info.couponId > 0}">{{order_info.couponName}}</text>
							<text class="iconfont icon-jiantou"></text>
						</view>
					</view>
					
					<view class="form-item">
						<view class="form-label">
							<text>gaga币抵扣</text>
						</view>
						<view class="form-wrapper" >
							<text> {{useIntegral ? '剩余' : '当前'}}gaga币<text class="complete integral">{{integral}}</text></text>
							<text class="iconfont icon-xuanzhong11" v-if="useIntegral" @click="ChangeIntegral"></text>
							<text class="iconfont icon-weixuan" v-else @click="ChangeIntegral"></text>
						</view>
					</view>
				</view>
			</view>
			
			<view class="modules">
				<view class="title">
					下单信息
				</view>
				<view class="form">
					<view class="form-item">
						<view class="form-label">
							<text>上课人姓名</text>
						</view>
						<view class="form-wrapper">
							<input class="uni-input" placeholder="请输入姓名" v-model="order_info.user_name" />
						</view>
					</view>
					
					<view class="form-item">
						<view class="form-label">
							<text>手机号码</text>
						</view>
						<view class="form-wrapper">
							<input class="uni-input" placeholder="请输入手机号码" v-model="order_info.phone" />
						</view>
					</view>
					
					<view class="form-item">
						<view class="form-label">
							<text>年龄</text>
						</view>
						<view class="form-wrapper">
							<input class="uni-input" placeholder="请输入年龄" v-model="order_info.age" />
						</view>
					</view>
					
					<view class="form-item">
						<view class="form-label">
							<text>性别</text>
						</view>
						<view class="form-wrapper" @click="showActionSheet('sex')">
							<text>{{order_info.sex_name}}</text>
							<text class="iconfont icon-jiantou"></text>
						</view>
					</view>
					
					<!-- <view class="form-item" @click="goto('/pages/classes/order_confirm/selectStore/selectStore')">
						<view class="form-label">
							<text>上课门店</text>
						</view>
						<view class="form-wrapper">
							<text v-if="order_info.store_id == 0">{{order_info.store_name}}</text>
							<template v-else>
								{{order_info.store_name}}
							</template>
							<text class="iconfont icon-jiantou"></text>
						</view>
					</view>
					
					<view class="form-item">
						<view class="form-label">
							<text>门店地址</text>
						</view>
						<view class="form-wrapper" v-if="order_info.store_id == 0">
							<text>根据所选门店显示</text>
						</view>
						<view class="form-wrapper"v-else>
							{{order_info.store_address}}
						</view>
					</view> -->
				</view>
			</view>
			
			<view class="notice" v-if="cartInfo[0].productInfo.store_notes">
				<view class="title">
					注意事项
				</view>
				<view class="main" v-text="cartInfo[0].productInfo.store_notes" style="white-space: pre-wrap;">
				</view>
			</view>
			
			<view class="agreement" @click="order_info.agreement = !order_info.agreement">
				<view :class="{ iconfont: true, 'icon-xuanzhong11': order_info.agreement, 'icon-weixuan': !order_info.agreement } "></view>
				<view class="text">
					我已认真阅读并同意预约相关
				</view>
				<view class="color" @click.stop="goto('/pages/users/privacy/index?type=buy')">
					《快游鸭协议》
				</view>
			</view>
		</view>
	  </template>
	  <view class='footer acea-row row-between-wrapper'>
	    <view>合计:
	      <text class='font-color'>￥{{totalPrice || 0}}</text>
	    </view>
	    <!-- <view class='settlement' style='z-index:100' @tap.stop="goPay"
	      v-if="(((valid_count>0&&!discount_id) || (valid_count==cartInfo.length&&discount_id)) && shippingType) || (!shippingType && addressId) || product_type != 0">
	      立即结算</view>
	    <view class='settlement bg-color-hui' style='z-index:100' v-else>立即结算</view> -->
	    <view class='settlement' style='z-index:100' @tap.stop="goPay"
	      v-if="
	      (((valid_count>0&&!discount_id) || (valid_count==cartInfo.length&&discount_id))) ||
	      (deliveryType == 2 && !shippingType) ||
	      (deliveryType == 3 && shippingType) ||
	      product_type != 0"
	    >
	      提交订单</view>
	    <view class='settlement bg-color-hui' style='z-index:100' v-else>提交订单</view>
	  </view>
	
    <view class="alipaysubmit" v-html="formContent"></view>
    <view class="tipaddress" v-show="isaddress">
      <view class="top"></view>
      <view class="bottom">
        <div class="font1">更新地址</div>
        <div class="font2">当前地址功能已更新，请重新修改</div>
        <div class="btn" @tap="payAddress">前往修改</div>
      </view>
    </view>

  <view class="mark" v-show="isaddress"></view>
  <couponListWindow :coupon='coupon' @ChangCouponsClone="ChangCouponsClone" :openType='openType' :cartId='cartId'
    @ChangCoupons="ChangCoupons"></couponListWindow>
  <addressWindow ref="addressWindow" @changeTextareaStatus="changeTextareaStatus" :news='news' :address='address'
    :pagesUrl="pagesUrl" @OnChangeAddress="OnChangeAddress" @changeClose="changeClose">
  </addressWindow>
  <!-- <home v-show="!invShow && navigation"></home> -->
  <invoice-picker :inv-show="invShow" :inv-list="invList" :inv-checked="invChecked" :is-special="special_invoice"
    :url-query="urlQuery" @inv-close="invClose" @inv-change="invChange" @inv-cancel="invCancel">
  </invoice-picker>
  <payment :payMode="cartArr" :pay_close="pay_close" :isCall="true" :totalPrice="totalPrice.toString()"
    @changePayType="changePayType" @onChangeFun="onChangeFun"></payment>
  <deliveryMethod ref="deliveryMethod" :isDisplay="isDisplay" :storeList="storeList" :storeSelfMention="store_self_mention" @deliveryFun="deliveryFun" @storeFun="storeFun"></deliveryMethod>
  <!-- #ifdef MP -->
 <!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
  <!-- #endif -->
  <timeranges :isShow='isShow' :time='timeranges' @confrim="confrim" @cancel="cancels"></timeranges>
  <areaWindow ref="areaWindow" :display="display" :address='addressInfoArea' :cityShow='cityShow' @submit="OnAreaAddress" @changeClose="changeAddressClose"></areaWindow>
  </view>
</template>
<script>
  const CACHE_CITY = {};
  import dayjs from '@/plugin/dayjs/dayjs.min.js';
  import {
    orderConfirm,
    getCouponsOrderPrice,
    orderCreate,
    postOrderComputed,
    checkShipping
  } from '@/api/order.js';
  import {
    getAddressDefault,
    getAddressDetail,
    invoiceList,
    invoiceOrder
  } from '@/api/user.js';
  import {
    openPaySubscribe
  } from '@/utils/SubscribeMessage.js';
  import {
    storeListApi,
	getCollagePartake,
	getCodeData
  } from '@/api/store.js';
  import {
    CACHE_LONGITUDE,
    CACHE_LATITUDE
  } from '@/config/cache.js';
  import areaWindow from '@/components/areaWindow';
  import couponListWindow from '@/components/couponListWindow';
  import addressWindow from '@/components/addressWindow';
  import orderGoods from '@/components/orderGoods';
  import deliveryMethod from '../components/deliveryMethod/index.vue';
  import timeranges from '@/components/timeranges';
  import home from '@/components/home';
  import invoicePicker from '../components/invoicePicker';
  import groupGoodsList from '@/components/groupGoodsList/index.vue'
  import {
    toLogin
  } from '@/libs/login.js';
  import {
    mapGetters
  } from "vuex";
  import payment from '@/components/payment';
  import colors from "@/mixins/color";
  export default {
    components: {
      payment,
      invoicePicker,
      couponListWindow,
      addressWindow,
      orderGoods,
      home,
      deliveryMethod,
	  timeranges,
	  areaWindow,
	  groupGoodsList
    },
    mixins: [colors],
    data() {
      return {
		addressInfoArea: [],
		cityShow: 2,
		display: false,
		timeranges:[],
		isShow:false,
        giveData: {
          give_integral: 0,
          give_coupon: []
        },
        giveCartInfo: [],
        confirm: [], //自定义留言
        id: 0,
        isaddress: false,
        textareaStatus: true,
        //支付方式
        cartArr: [{
            "name": "微信支付",
            "icon": "icon-weixin2",
            value: 'weixin',
            title: '使用微信快捷支付',
            payStatus: 1,
          },
          {
            "name": "支付宝支付",
            "icon": "icon-zhifubao",
            value: 'alipay',
            title: '使用线上支付宝支付',
            payStatus: 1,
          },
          {
            "name": "余额支付",
            "icon": "icon-yuezhifu",
            value: 'yue',
            title: '可用余额:',
            payStatus: 1,
          },
          {
            "name": "线下支付",
            "icon": "icon-yuezhifu1",
            value: 'offline',
            title: '选择线下付款方式',
            payStatus: 2,
          }
        ],
        formContent: '',
        payType: 'weixin', //支付方式
        openType: 1, //优惠券打开方式 1=使用
        active: 0, //支付方式切换
        coupon: {
          coupon: false,
          list: [],
          statusTile: '立即使用'
        }, //优惠券组件
        address: {
          address: false
        }, //地址组件
        addressInfo: {}, //地址信息
        pinkId: 0, //拼团id
        addressId: 0, //地址id
        couponId: 0, //优惠券id
        cartId: '', //购物车id
        BargainId: 0,
        combinationId: 0,
        seckillId: 0,
        discountId: 0,
        userInfo: {}, //用户信息
        mark: '', //备注信息
        couponTitle: '请选择', //优惠券
        coupon_price: 0, //优惠券抵扣金额
        promotions_detail: [], //优惠活动金额明细
        useIntegral: false, //是否使用积分
        integral_price: 0, //积分抵扣金额
        integral: 0,
        ChangePrice: 0, //使用积分抵扣变动后的金额
        formIds: [], //收集formid
        status: 0,
        is_address: false,
        toPay: false, //修复进入支付时页面隐藏从新刷新页面
        shippingType: 0,
        system_store: {},
        storePostage: 0,
        contacts: '',
        contactsTel: '',
        mydata: {},
        storeList: [],
        store_self_mention: 0,//是开启门店自提；
				store_func_status: false,//是否开启门店自提和门店配送；
        cartInfo: [],
        priceGroup: {},
        animated: false,
        totalPrice: 0,
        integralRatio: "0",
        pagesUrl: "",
        orderKey: "",
        // usableCoupon: {},
        offlinePostage: "",
        isAuto: false, //没有授权的不会自动授权
        isShowAuth: false, //是否隐藏授权
        from: '',
        news: 1,
        invTitle: '不开发票',
        special_invoice: false,
        invoice_func: false,
		    integral_ratio_status : 1,
        header_type: '',
        invShow: false,
        invList: [],
        invChecked: '',
        urlQuery: '',
        pay_close: false,
        noCoupon: 0,
        valid_count: 0,
        discount_id: 0,
        storeId: 0,
        product_type: 1,
        newImg: [],
        isDisplay: [],
		deliveryType:1,
		goodsType:0,
		product_id:0,
		selectIndex:0,
		timerangesIndex:0,
		collage_id: 0,
		goodsList: [],
		tableId: 0,
		codeData: {},
		order_info: {
			user_name: '',
			phone: '',
			age: '',
			sex: '',
			sex_name: '请选择性别',
			store_id: '',
			store_address: '',
			store_name: '请选择门店',
			agreement: false,
			couponName: '请选择优惠券',
			couponId: 0
		},
		relevance_id: 0
      };
    },
    computed: mapGetters(['isLogin']),
    onLoad: function(options) {
      // #ifdef H5
      this.from = this.$wechat.isWeixin() ? 'weixin' : 'weixinh5'
      // #endif
      // #ifdef MP
      this.from = 'routine'
      // #endif
      if (!options.cartId) return this.$util.Tips({
        title: '请选择要购买的商品'
      }, {
        tab: 3,
        url: 1
      });
			this.deliveryType = options.delivery_type || 1;
			if(this.deliveryType == 1 || this.deliveryType == 3){
				this.addressId = options.addressId || 0;
				this.system_store.name = options.store_name;
			}
			if(this.deliveryType == 2 || this.deliveryType == 3){
				this.storeId = 0;
				// this.storeId = options.store_id || 0;
				// this.system_store.id = options.store_id || 0;
				this.system_store.id = 0;
			}
      this.couponId = options.couponId || 0;
      this.relevance_id = options.relevance_id || 0;
      this.noCoupon = options.noCoupon || 0;
	  this.product_id = options.product_id || 0;
      this.pinkId = options.pinkId ? parseInt(options.pinkId) : 0;
      this.cartId = options.cartId;
      this.is_address = options.is_address ? true : false;
      this.news = !options.new || options.new === '0' ? 0 : 1;
      uni.setStorageSync('news', this.news);
      this.invChecked = options.invoice_id || '';
      this.header_type = options.header_type || '1';
      this.couponTitle = options.couponTitle || '请选择'
      switch (options.invoice_type) {
        case '1':
          this.invTitle = '增值税电子普通发票';
          break;
        case '2':
          this.invTitle = '增值税电子专用发票';
          break;
      }
      if (options.invoice_name) {
        this.invTitle = options.invoice_name;
      }
      // #ifndef APP-PLUS
      this.textareaStatus = true;
      // #endif
      if (this.isLogin && this.toPay == false) {
        this.getCheckShipping();
      } else {
        toLogin();
      }
	  this.collage_id = options.collage_id || 0;
	  if (this.collage_id) {
	  	this.getCollagePartake();
	  }
	  // #ifdef MP
	  // 桌码
	  this.tableId = options.tableId || 0;
	  if (this.tableId) {
	  	this.getCodeData();
	  }
	  // #endif
    },
    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
      uni.removeStorageSync('form_type_cart');
      let _this = this
      uni.$on("handClick", res => {
        if (res) {
          _this.system_store = res.address
          _this.storeId = _this.system_store.id
          _this.cartId = res.cartId
          _this.news = res.new
          _this.pinkId = Number(res.pinkId)
          _this.couponId = res.couponId
          _this.getConfirm()
        }
        // 清除监听
        uni.$off('handClick');
      })
	  uni.$on("store", item => {
		this.order_info.store_id = item.id
		this.order_info.store_address = item.detailed_address
		this.order_info.store_name = item.name
	    // 清除监听
	    uni.$off('store');
	  })
	  let current = (dayjs(new Date(Number(new Date().getTime()))).format('HH:mm')).split(':');
	  let currentArray = current;
	  currentArray.push(0);
	  let arrayNew = [];
	  [...currentArray,...current].forEach(item=>{
		 arrayNew.push(Number(item)) 
	  })
	  this.timeranges = arrayNew;
    },
    methods: {
		// #ifdef MP
		// 桌码信息
		getCodeData() {
			getCodeData({
				tableId: this.tableId
			}).then(res => {
				this.codeData = res.data;
			});
		},
		// #endif
		getCollagePartake() {
			getCollagePartake({
				collage_id: this.collage_id
			}).then(res => {
				this.goodsList = res.data;
			});
		},
		OnAreaAddress(address){
		  let addr = '';
		  if (address.length==4){
			  addr = address[0].label+'/'+address[1].label+'/'+address[2].label+'/'+address[3].label;
		  }else if (address.length==3){
			 addr = address[0].label+'/'+address[1].label+'/'+address[2].label;
		  }else if(address.length==2){
			 addr = address[0].label+'/'+address[1].label;
		  }else{
			 addr = address[0].label; 
		  }
		  this.confirm[this.timerangesIndex].value = addr;
		  CACHE_CITY[this.timerangesIndex] = address;
		},
		changeRegion(index){
		   if(!this.confirm[index].value){
			 this.addressInfoArea = [];
		   }
		   this.timerangesIndex = index;
		   this.cityShow = Number(this.confirm[index].valConfig.tabVal) + 1;
		   this.display = true;
		   if(CACHE_CITY[index]){
			 this.addressInfoArea = CACHE_CITY[index];  
		   }
		},
		// 关闭地址弹窗；
		changeAddressClose: function() {
			this.display = false;
		},
		maskClick(e) {
			console.log(e);
		},
			// 授权关闭
			authColse: function(e) {
			  this.isShowAuth = e
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
			// 选中配送类型
			deliveryFun(e,item){
				this.deliveryType = e;
				this.$refs.deliveryMethod.module = false;
				if(e==2){
					this.storeId = item.id;
				}else{
					if(e == 3){
					  this.storeId = item.id;
					  if(this.isDisplay.includes("2") && this.isDisplay.includes("3")){
						  this.getaddressInfo();
						  this.$nextTick(function() {
							  this.$refs.addressWindow.getAddressList();
						  })
					  } 
					}
					this.system_store = item;
				}
				if(e == 1 || e == 3){
					this.addressType(0,e);
				}else{
					this.addressType(1,e);
				}
			},
		  // 选择配送方式
		  onDelivery() {
			this.$refs.deliveryMethod.module = true
		  },
		// 子集调用门店列表接口
		storeFun(index){
			this.getList(1,index);
		},
      // 是否显示快递配送
      getCheckShipping() {
		 return this.getConfirm();
        let data = {
          cartId: this.cartId,
          new: this.news
        }
        checkShipping(data).then(res => {
					// 1：快递配送，2：到店自提，3：门店配送
					res.data.type.sort((x,y)=>x - y)//正序
          this.isDisplay = res.data.type;
          if (((this.isDisplay.includes("2") && this.isDisplay.length == 1) || (this.deliveryType == 2 && !this.tableId) || (this.deliveryType == 3 && this.storeId>0 && !this.system_store.name)) && this.isDisplay.length) {
			if((this.deliveryType == 2 || (this.deliveryType == 3 && !this.isDisplay.includes("3"))) && this.isDisplay.includes("2")){
				this.deliveryType = 2
				this.addressType(1,this.deliveryType);
			}
			if((this.deliveryType == 3 || (this.deliveryType == 2 && !this.isDisplay.includes("2"))) && this.isDisplay.includes("3")){
				this.deliveryType = 3
				this.addressType(0,this.deliveryType);
				this.getaddressInfo();
				this.$nextTick(function() {
				  this.$refs.addressWindow.getAddressList();
				})
			}
          } else {
            this.getaddressInfo();
            this.getConfirm();
            this.$nextTick(function() {
              this.$refs.addressWindow.getAddressList();
            })
          }
        }).catch(err => {
          uni.showToast({
            title: err,
            icon: 'none'
          });
        })
      },
	  
      /**
       * 删除图片
       * 
       */
      DelPic: function(index, indexs) {
        let that = this,
          pic = this.confirm[index].value;
        that.confirm[index].value.splice(indexs, 1);
        that.$set(that.confirm[index], 'value', that.confirm[index].value);
      },

      /**
       * 上传文件
       * 
       */
      uploadpic: function(index) {
        let that = this;
        this.$util.uploadImageOne('upload/image', function(res) {
          that.newImg.push(res.data.url);
          that.$set(that.confirm[index], 'value', that.newImg);
        });
      },
      // 不开发票
      invCancel() {
        this.invChecked = '';
        this.invTitle = '不开发票';
        this.invShow = false;
      },
      // 选择发票
      invChange(id) {
        this.invChecked = id;
        this.invShow = false;
        const result = this.invList.find(item => item.id === id);
        let name = '';
        name += result.header_type === 1 ? '个人' : '企业';
        name += result.type === 1 ? '普通' : '专用';
        name += '发票';
        this.invTitle = name;
      },
      // 关闭发票
      invClose() {
        this.invShow = false;
        this.getInvoiceList()
      },
      getInvoiceList() {
        uni.showLoading({
          title: '正在加载…'
        })
        invoiceList().then(res => {
          uni.hideLoading();
          this.invList = res.data.map(item => {
            item.id = item.id.toString();
            return item;
          });
          const result = this.invList.find(item => item.id == this.invChecked);
          if (result) {
            let name = '';
            name += result.header_type === 1 ? '个人' : '企业';
            name += result.type === 1 ? '普通' : '专用';
            name += '发票';
            this.invTitle = name;
          }
        }).catch(err => {
          uni.showToast({
            title: err,
            icon: 'none'
          });
        });
      },
      /**
       * 开发票
       */
      goInvoice: function() {
        this.getInvoiceList()
        this.invShow = true;
        this.urlQuery =
          `new=${this.news}&cartId=${this.cartId}&pinkId=${this.pinkId}&couponId=${this.couponId}&addressId=${this.addressId}&specialInvoice=${this.special_invoice}&couponTitle=${this.couponTitle}&delivery_type=${this.deliveryType}&store_id=${this.storeId}&store_name=${this.system_store.name}&product_id=${this.product_id}`;
      },
      /**
       * 授权回调事件
       * 
       */
      onLoadFun: function() {
				this.getCheckShipping();
				this.isShowAuth = false;
        //调用子页面方法授权后执行获取地址列表
        // this.$scope.selectComponent('#address-window').getAddressList();
      },
      /**
       * 事件回调
       *
       */
      onChangeFun: function(e) {
        let opt = e;
        let action = opt.action || null;
        let value = opt.value != undefined ? opt.value : null;
        action && this[action] && this[action](value);
      },
      payClose: function() {
        this.pay_close = false;
      },
      goPay() {
		// this.formVerify();
		if(!this.order_info.agreement) {
			return uni.showToast({
				title: '请阅读协议！',
				icon: 'none'
			})
		}
		this.pay_close = true;
      },
      payCheck(type) {
        this.payType = type;
        this.SubOrder();
      },
      payAddress() {
        uni.navigateTo({
          // /pages/users/user_address/index?id=25&cartId=76179610656654229504&pinkId=0&couponId=0&new=1
          url: '/pages/users/user_address/index?id=' + this.id + '&new=' + this.news + '&cartId=' + this.cartId +
            '&pinkId=' +
            this.pinkId +
            '&couponId=' +
            this.couponId +'&delivery_type='+this.deliveryType+'&addressId='+this.addressId+'&store_id='+this.storeId+'&store_name='+ this.system_store.name+'&product_id='+this.product_id
        })
      },
      /**
       * 获取门店列表数据
       */
      getList: function(fromType,index) {
        let longitude = uni.getStorageSync("user_longitude"); //经度
        let latitude = uni.getStorageSync("user_latitude"); //纬度
        let data = {
          latitude: latitude, //纬度
          longitude: longitude, //经度
          page: 1,
          limit: 100,
		  type: this.seckillId>0?1:0,
		      product_id: this.product_id,
			    is_store: index
        }
        storeListApi(data).then(res => {
          let list = res.data.list.list || [];
          this.$set(this, 'storeList', list);
          this.$set(this, 'storeId', this.storeId !=0?this.storeId:list[0].id);
		  list.forEach(item=>{
		  	if(this.storeId == item.id){
		  		this.$set(this, 'system_store', item);
		  	}
		  })
		  if(fromType !=1 ){
		  	this.getConfirm();
		  }
        }).catch(err => {})
      },
      // 关闭地址弹窗；
      changeClose: function() {
        this.$set(this.address, 'address', false);
      },
      /*
       * 跳转门店列表
       */
      showStoreList: function() {
        let _this = this
        if (this.storeList.length > 0) {
          uni.navigateTo({
            url: `/pages/store/store_list/index?type=1&cartId=${_this.cartId}&new=${_this.news}&pinkId=${_this.pinkId}&couponId=${_this.couponId}`
          })
        }
      },
      changePayType(type) {
        this.payType = type
        this.computedPrice()
      },
      computedPrice: function() {
        let shippingType = this.shippingType;
        postOrderComputed(this.orderKey, {
          addressId: this.addressId,
          useIntegral: this.useIntegral ? 1 : 0,
          couponId: this.priceGroup.couponPrice == 0 ? 0 : this.couponId,
          shipping_type: 2,
          payType: this.payType
        }).then(res => {
          let result = res.data.result;
          if (result) {
            this.totalPrice = result.pay_price;
            this.integral_price = result.deduction_price;
            this.coupon_price = result.coupon_price;
            this.promotions_detail = result.promotions_detail;
            this.integral = this.useIntegral ? result.SurplusIntegral : this.userInfo.integral;
            this.$set(this.priceGroup, 'storePostage', shippingType == 1 ? 0 : result.pay_postage);
            this.$set(this.priceGroup, 'storePostageDiscount', result.storePostageDiscount);
          }
        }).catch(err => {
          return that.$util.Tips({
            title: err
          });
        })
      },
      addressType: function(e,type) {
        let index = e;
        this.shippingType = parseInt(index);
        // this.computedPrice();
        if (index == 1) {
          this.getList(0,type);
        } else {
          this.getConfirm();
        }
      },
      bindPickerChange: function(e) {
        let value = e.detail.value;
        this.shippingType = value;
        this.computedPrice();
      },
      ChangCouponsClone: function() {
        this.$set(this.coupon, 'coupon', false);
      },
      changeTextareaStatus: function() {
        for (let i = 0, len = this.coupon.list.length; i < len; i++) {
          this.coupon.list[i].use_title = '';
          this.coupon.list[i].is_use = 0;
        }
        this.textareaStatus = true;
        this.status = 0;
        this.$set(this.coupon, 'list', this.coupon.list);
      },
      /**
       * 处理点击优惠券后的事件
       * 
       */
      ChangCoupons: function(e) {
        // this.usableCoupon = e
        // this.coupon.coupon = false
        let index = e,
          list = this.coupon.list,
          couponTitle = '请选择',
          couponId = 0;
        for (let i = 0, len = list.length; i < len; i++) {
          if (i != index) {
            list[i].use_title = '';
            list[i].is_use = 0;
          }
        }
        if (list[index].is_use) {
          //不使用优惠券
          list[index].use_title = '';
          list[index].is_use = 0;
        } else {
          //使用优惠券
          list[index].use_title = '不使用';
          list[index].is_use = 1;
          couponTitle = list[index].coupon_title;
          couponId = list[index].id;
        }
        this.couponTitle = couponTitle;
        this.couponId = couponId;
        this.$set(this.coupon, 'coupon', false);
        this.$set(this.coupon, 'list', list);
        this.getConfirm(1);
		this.order_info.couponId = couponId;
		this.order_info.couponName = couponTitle;
      },
      /**
       * 使用积分抵扣
       */
      ChangeIntegral: function() {
        this.useIntegral = !this.useIntegral;
        this.computedPrice();
      },
      /**
       * 选择地址后改变事件
       * @param object e
       */
      OnChangeAddress: function(e) {
        this.textareaStatus = true;
        this.addressId = e;
        this.address.address = false;
        this.getConfirm()
        this.getaddressInfo();
        this.computedPrice();
      },
      bindHideKeyboard: function(e) {
        this.mark = e.detail.value;
      },
	  // 对象转数组
	  objToArr(data) {
	  	let obj = Object.keys(data);
	  	let m = obj.map(key => data[key]);
	  	return m;
	  },
      /**
       * 获取当前订单详细信息
       * 
       */
      getConfirm: function(numType) {
        let that = this;
        let shippingType = parseInt(this.shippingType) + 1;
        let addressId = 0,storeid;
		if (this.tableId) {
			shippingType = 4;
		}
        if (shippingType == 1) {
          addressId = that.addressId
          if(this.deliveryType == 1){
            storeid = 0
          }else{
            storeid = that.storeId
          }
        } else {
          addressId = ''
          storeid = that.storeId
        }
		storeid = 0
		// relevance_id
        orderConfirm(that.cartId, that.news, addressId, shippingType, storeid, that.couponId).then(res => {
          if (res.data.upgrade_addr == 1) {
            that.id = res.data.addressInfo.id
            this.isaddress = true
          }
          if (numType != 1) {
			let confirm = this.objToArr(res.data.custom_form);
			confirm.forEach((item, index, arr)=>{
				CACHE_CITY[index] = ''; //清空省市区
				if(item.name == 'texts'){
				  if(item.defaultValConfig.value){
					  item.value = item.defaultValConfig.value
				  }else{
					  item.value = ''
				  }
				}else if(item.name == 'radios'){
					item.value = item.wordsConfig.list[0].val
				}else if(item.name == 'uploadPicture'){
				  item.value = [];
				}else if(item.name == 'dateranges'){
					if(item.valConfig.tabVal==0){
					  if(item.valConfig.tabData==0){
						 let obj = dayjs(new Date(Number(new Date().getTime()))).format('YYYY-MM-DD');
						 item.value = [obj,obj]
					  }else{
						  let data1 = dayjs(new Date(Number(new Date(item.valConfig.specifyDate[0]).getTime()))).format('YYYY-MM-DD');
						  let data2 = dayjs(new Date(Number(new Date(item.valConfig.specifyDate[1]).getTime()))).format('YYYY-MM-DD');
					      item.value = [data1,data2];
					  }
					}else{
					  item.value = [];  
					}
				}else{
					if(['times','dates','timeranges'].indexOf(item.name) != -1){
					  if(item.valConfig.tabVal==0){
						  if(item.valConfig.tabData==0){
							if(item.name == 'times'){
							  item.value = dayjs(new Date(Number(new Date().getTime()))).format('HH:mm');
							}else if(item.name == 'dates'){
							  item.value = dayjs(new Date(Number(new Date().getTime()))).format('YYYY-MM-DD');
							}else{
							  let current = dayjs(new Date(Number(new Date().getTime()))).format('HH:mm');
							  item.value = current+' - '+current;
							}
						  }else{
							if(item.name == 'times' || item.name == 'dates'){
							  item.value = item.valConfig.specifyDate;
							}else{
							  item.value = item.valConfig.specifyDate[0]+' - '+item.valConfig.specifyDate[1];
							}
						  }
					  }else{
						item.value = '';  
					  }
					}else{
					  item.value = '';
					}
				}
			})
			function sortNumber(a, b) {
				return a.timestamp - b.timestamp;
			}
			confirm.sort(sortNumber);
            that.$set(that, 'confirm', confirm);
          }
		  that.$set(that, 'goodsType', res.data.type);
          that.$set(that, 'userInfo', res.data.userInfo);
          that.$set(that, 'integral', res.data.userInfo.integral);
          that.$set(that, 'contacts', res.data.userInfo.real_name);
          that.$set(that, 'contactsTel', res.data.userInfo.record_phone || '');
          that.$set(that, 'integralRatio', res.data.integralRatio);
          that.$set(that, 'offlinePostage', res.data.offlinePostage);
          that.$set(that, 'orderKey', res.data.orderKey);
          that.$set(that, 'valid_count', res.data.valid_count);
          that.$set(that, 'discount_id', res.data.discount_id)
          that.$set(that, 'priceGroup', res.data.priceGroup);
          that.$set(that, 'seckillId', parseInt(res.data.seckill_id));
          that.$set(that, 'BargainId', parseInt(res.data.bargain_id));
          that.$set(that, 'combinationId', parseInt(res.data.combination_id));
          that.$set(that, 'discountId', parseInt(res.data.discount_id));
          that.$set(that, 'invoice_func', res.data.invoice_func);
          that.$set(that, 'special_invoice', res.data.special_invoice);
		      that.$set(that, 'integral_ratio_status', res.data.integral_ratio_status);
          that.$set(that, 'store_self_mention', res.data.store_self_mention);
					that.$set(that, 'store_func_status', res.data.store_func_status);
          that.giveData.give_integral = res.data.give_integral;
          that.giveData.give_coupon = res.data.give_coupon;
          let cartInfo = res.data.cartInfo;
          let cartObj = [],
            giftObj = [];
          cartInfo.forEach(item => {
            if (item.is_gift == 1) {
              giftObj.push(item)
            } else {
              cartObj.push(item)
            }
          })
          that.$set(that, 'cartInfo', cartObj);
          that.$set(that, 'giveCartInfo', giftObj);
          let giveType = -1;
          giftObj.forEach(item => {
            if (item.product_type == 0 || item.product_type == 4) {
              return giveType = 0
            }
          })
          that.$set(that, 'product_type', (res.data.product_type == 0 || giveType == 0 || res.data.product_type == 4) ? 0 : 1);
          //微信支付是否开启
          that.cartArr[0].payStatus = res.data.pay_weixin_open || 0
          //支付宝是否开启
          that.cartArr[1].payStatus = res.data.ali_pay_status || 0;
          //#ifdef MP
          that.cartArr[1].payStatus = 0;
          //#endif
          //余额支付是否开启
          // that.cartArr[2].title = '可用余额:' + res.data.userInfo.now_money;
          that.cartArr[2].number = res.data.userInfo.now_money;
          that.cartArr[2].payStatus = res.data.yue_pay_status == 1 ? res.data.yue_pay_status : 0
          if (res.data.offline_pay_status == 2) {
            that.cartArr[3].payStatus = 0
          } else {
            that.cartArr[3].payStatus = 1
          }
          // that.$set(that, 'cartArr', that.cartArr);
          that.$set(that, 'ChangePrice', that.totalPrice);
          that.getBargainId();
          that.getCouponList();
          that.computedPrice();
          if (this.addressId || this.couponId) {
            // this.computedPrice();
          } else {
            that.$set(that, 'totalPrice', that.$util.$h.Add(parseFloat(res.data.priceGroup.totalPrice),
              parseFloat(res.data
                .priceGroup.storePostage)));
          }
        }).catch(err => {
          return this.$util.Tips({
            title: err
          });
        });
      },
      /*
       * 提取砍价和拼团id
       */
      getBargainId: function() {
        let that = this;
        // let cartINfo = that.cartInfo;
        // let BargainId = 0;
        // let combinationId = 0;
        // let discountId = 0;
        // cartINfo.forEach(function(value, index, cartINfo) {
        // 	BargainId = cartINfo[index].bargain_id,
        // 		combinationId = cartINfo[index].combination_id,
        // 		discountId = cartINfo[index].discount_id
        // })
        // that.$set(that, 'BargainId', parseInt(BargainId));
        // that.$set(that, 'combinationId', parseInt(combinationId));
        // that.$set(that, 'discountId', parseInt(discountId));
        if (that.cartArr.length == 3 && (that.BargainId || that.combinationId || that.seckillId || that.discountId)) {
          that.cartArr[2].payStatus = 0;
          that.$set(that, 'cartArr', that.cartArr);
        }
      },
      /**
       * 获取当前金额可用优惠券
       * 
       */
      getCouponList: function() {
        let that = this;
        let data = {
          cartId: this.cartId,
          'new': this.news,
          shipping_type: 2,
          store_id: that.system_store ? that.system_store.id : 0
        }
        getCouponsOrderPrice(this.totalPrice, data).then(res => {
          that.$set(that.coupon, 'list', res.data);
          that.openType = 1;
        });
      },
      /*
       * 获取默认收货地址或者获取某条地址信息
       */
      getaddressInfo: function() {
        let that = this;
        if (that.addressId) {
          getAddressDetail(that.addressId).then(res => {
            res.data.is_default = parseInt(res.data.is_default);
            that.addressInfo = res.data || {};
            that.addressId = res.data.id || 0;
            that.address.addressId = res.data.id || 0;
          })
        } else {
          getAddressDefault().then(res => {
            res.data.is_default = parseInt(res.data.is_default);
            that.addressInfo = res.data || {};
            that.addressId = res.data.id || 0;
            that.address.addressId = res.data.id || 0;
          })
        }
      },
      payItem: function(e) {
        let that = this;
        let active = e;
        that.active = active;
        that.animated = true;
        that.payType = that.cartArr[active].value;
        that.computedPrice();
        setTimeout(function() {
          that.car();
        }, 500);
      },
      couponTap: function() {
        this.coupon.coupon = true;
        this.coupon.list.forEach((item, index) => {
          if (item.id == this.couponId) {
            item.is_use = 1
          } else {
            item.is_use = 0
          }
        })
        this.$set(this.coupon, 'list', this.coupon.list);
      },
      car: function() {
        let that = this;
        that.animated = false;
      },
      onAddress: function(name) {
        let that = this;
		if(name){
			that.textareaStatus = false;
			that.address.address = true;
			that.pagesUrl = '/pages/users/user_address_list/index?news=' + this.news + '&cartId=' + this.cartId +
			  '&pinkId=' +
			  this.pinkId +
			  '&couponId=' +
			  this.couponId +'&delivery_type='+this.deliveryType+'&addressId='+this.addressId+'&store_id='+this.storeId+'&store_name='+ this.system_store.name+'&product_id='+this.product_id
		}else{
			uni.navigateTo({
				url: '/pages/users/user_address/index?new=' + this.news + '&cartId=' + this.cartId +
			  '&pinkId=' +
			  this.pinkId +
			  '&couponId=' +
			  this.couponId +'&delivery_type='+this.deliveryType+'&addressId='+this.addressId+'&store_id='+this.storeId+'&store_name='+ this.system_store.name+'&product_id='+this.product_id
			})
		}
      },
      payment: function(data) {
        let that = this;
        orderCreate(that.orderKey, data).then(res => {
          let status = res.data.status,
            orderId = res.data.result.orderId,
            jsConfig = res.data.result.jsConfig,
            goPages = '/pages/goods/order_pay_status/index?order_id=' + orderId + '&msg=' + res.msg +
            '&type=3' + '&totalPrice=' + this.totalPrice
          switch (status) {
            case 'ORDER_EXIST':
            case 'EXTEND_ORDER':
            case 'PAY_ERROR':
              uni.hideLoading();
              return that.$util.Tips({
                title: res.msg
              }, {
                tab: 5,
                url: goPages
              });
              break;
            case 'SUCCESS':
              uni.hideLoading();
              if (that.BargainId || that.combinationId || that.pinkId || that.seckillId || that
                .discountId)
                return that.$util.Tips({
                  title: res.msg,
                  icon: 'success'
                }, {
                  tab: 4,
                  url: goPages
                });
              return that.$util.Tips({
                title: res.msg,
                icon: 'success'
              }, {
                tab: 5,
                url: goPages
              });
              break;
            case 'WECHAT_PAY':
              that.toPay = true;
              // #ifdef MP
              /* that.toPay = true; */
              let mp_pay_name=''
							if(uni.requestOrderPayment){
								mp_pay_name='requestOrderPayment'
							}else{
								mp_pay_name='requestPayment'
							}
              uni[mp_pay_name]({
                timeStamp: jsConfig.timestamp,
                nonceStr: jsConfig.nonceStr,
                package: jsConfig.package,
                signType: jsConfig.signType,
                paySign: jsConfig.paySign,
                success: function(res) {
                  uni.hideLoading();
                  if (that.BargainId || that.combinationId || that.pinkId || that
                    .seckillId || that.discountId)
                    return that.$util.Tips({
                      title: '支付成功',
                      icon: 'success'
                    }, {
                      tab: 4,
                      url: goPages
                    });
                  return that.$util.Tips({
                    title: '支付成功',
                    icon: 'success'
                  }, {
                    tab: 5,
                    url: goPages
                  });
                },
                fail: function(e) {
                  uni.hideLoading();
                  return that.$util.Tips({
                    title: '取消支付'
                  }, {
                    tab: 5,
                    url: goPages + '&status=2'
                  });
                },
                complete: function(e) {
                  uni.hideLoading();
                  //关闭当前页面跳转至订单状态
                  if (res.errMsg == 'requestPayment:cancel') return that.$util
                    .Tips({
                      title: '取消支付'
                    }, {
                      tab: 5,
                      url: goPages + '&status=2'
                    });
                },
              })
              // #endif
              // #ifdef H5
              this.$wechat.pay(res.data.result.jsConfig).then(res => {
                return that.$util.Tips({
                  title: '支付成功',
                  icon: 'success'
                }, {
                  tab: 5,
                  url: goPages
                });
              }).catch(res => {
                if (!this.$wechat.isWeixin()) {
                  uni.redirectTo({
                    url: goPages +
                      '&msg=支付失败&status=2'
                  })
                }
                if (res.errMsg == 'chooseWXPay:cancel') return that.$util.Tips({
                  title: '取消支付'
                }, {
                  tab: 5,
                  url: goPages + '&status=2'
                });
              })
              // #endif
              // #ifdef APP-PLUS
              uni.requestPayment({
                provider: 'wxpay',
                orderInfo: jsConfig,
                success: (e) => {
                  let url = goPages;
                  uni.showToast({
                    title: "支付成功"
                  })
                  setTimeout(res => {
                    uni.redirectTo({
                      url: url
                    })
                  }, 2000)
                },
                fail: (e) => {
                  let url = '/pages/goods/order_pay_status/index?order_id=' + orderId +
                    '&msg=支付失败';
                  uni.showModal({
                    content: "支付失败",
                    showCancel: false,
                    success: function(res) {
                      if (res.confirm) {
                        uni.redirectTo({
                          url: url
                        })
                      } else if (res.cancel) {
                        console.log('用户点击取消');
                      }
                    }
                  })
                },
                complete: () => {
                  uni.hideLoading();
                },
              });
              // #endif
              break;
            case 'PAY_DEFICIENCY':
              uni.hideLoading();
              //余额不足
              return that.$util.Tips({
                title: res.msg
              }, {
                tab: 5,
                url: goPages + '&status=1'
              });
              break;
            case "WECHAT_H5_PAY":
              uni.hideLoading();
              that.$util.Tips({
                title: '订单创建成功!'
              }, {
                tab: 4,
                url: goPages + '&status=0'
              });
              setTimeout(() => {
                location.href = res.data.result.jsConfig.mweb_url;
              }, 2000);
              break;

            case 'ALIPAY_PAY':
              //#ifdef H5
              if (this.from === 'weixin') {
                uni.redirectTo({
                  url: `/pages/users/alipay_invoke/index?id=${orderId}&pay_key=${res.data.result.pay_key}`
                });
              } else {
                uni.hideLoading();
                that.formContent = res.data.result.jsConfig;
                that.$nextTick(() => {
                  document.getElementById('alipaysubmit').submit();
                })
              }
              //#endif
              // #ifdef MP
              uni.navigateTo({
                url: `/pages/users/alipay_invoke/index?id=${orderId}&link=${jsConfig.qrCode}`
              });
              // #endif
              // #ifdef APP-PLUS
              uni.requestPayment({
                provider: 'alipay',
                orderInfo: jsConfig,
                success: (e) => {
                  uni.showToast({
                    title: "支付成功"
                  })
                  let url = '/pages/goods/order_pay_status/index?order_id=' + orderId +
                    '&msg=支付成功';
                  setTimeout(res => {
                    uni.redirectTo({
                      url: url
                    })
                  }, 2000)

                },
                fail: (e) => {
                  let url = '/pages/goods/order_pay_status/index?order_id=' + orderId +
                    '&msg=支付失败';
                  uni.showModal({
                    content: "支付失败",
                    showCancel: false,
                    success: function(res) {
                      if (res.confirm) {
                        uni.redirectTo({
                          url: url
                        })
                      } else if (res.cancel) {
                        console.log('用户点击取消');
                      }
                    }
                  })
                },
                complete: () => {
                  uni.hideLoading();
                },
              });
              // #endif
              break;
          }
        }).catch(err => {
          uni.hideLoading();
          return that.$util.Tips({
            title: err
          });
        });
      },
      clickTextArea() {
        this.$refs.textarea.focus()
      },
      bindDateChange: function(e, index) {
        this.confirm[index].value = e.target.value
      },
      bindTimeChange: function(e, index) {
        this.confirm[index].value = e.target.value
      },
	  bindSelectChange: function(e, index, item) {
		this.confirm[index].value = item.wordsConfig.list[e.detail.value].val
	  },
	  getTimeranges(index){
		this.isShow = true
		this.timerangesIndex = index
	  },
	  confrim(e){
	    this.isShow = false;
	    this.confirm[this.timerangesIndex].value = e.time;
		let arrayNew = [];
		e.val.forEach(item=>{
		  arrayNew.push(Number(item))
		})
		this.timeranges = arrayNew;
	  },
	  cancels(){
		this.isShow = false;
	  },
	  // 单选
	  radioChange(e, index, item){
		this.confirm[index].value = item.wordsConfig.list[e.detail.value].val
	  },
	  // 多选
	  checkboxChange(e, index, item){
		  let obj = e.detail.value;
		  let val = '';
		  item.wordsConfig.list.forEach((j,jindex)=>{
			  obj.forEach(x=>{
				  if(jindex == x){
				  	 val = val +(val?',':'') + j.val;	  
				  }
			  })
		  })
		this.confirm[index].value = val
	  },
	  formVerify(){
		  let that = this;
		  if (!that.addressId && !that.shippingType && !that.product_type && !that.tableId) return that.$util.Tips({
		    title: '请选择收货地址'
		  });
		  if (that.shippingType == 1) {
		    if (that.contacts == "" || that.contactsTel == "") {
		      return that.$util.Tips({
		        title: '请填写联系人或联系人电话'
		      });
		    }
		    if (!/^1(3|4|5|7|8|9|6)\d{9}$/.test(that.contactsTel)) {
		      return that.$util.Tips({
		        title: '请填写正确的手机号'
		      });
		    }
		    if (!/^[\u4e00-\u9fa5\w]{2,16}$/.test(that.contacts)) {
		      return that.$util.Tips({
		        title: '请填写您的真实姓名'
		      });
		    }
		    if (that.storeList.length == 0) return that.$util.Tips({
		      title: '暂无门店,请选择其他方式'
		    });
		  }
		  for (var i = 0; i < that.confirm.length; i++) {
		    let data = that.confirm[i]
		    if (['radios'].indexOf(data.name) == -1 && (data.titleShow.val || (['uploadPicture','dateranges'].indexOf(data.name) == -1 && data.value && data.value.trim()))) {
		      if ((data.name === 'texts' && data.valConfig.tabVal == 0) || ['dates','times','selects','citys','checkboxs'].indexOf(data.name) != -1) {
		        if (!data.value || (data.value && !data.value.trim())) {
		          return that.$util.Tips({
		            title: `请填写${data.titleConfig.value}`
		          });
		        }
		      }
		  	if(data.name === 'timeranges'){
		  		if(!data.value){
		  			return that.$util.Tips({
		  			  title: `请选择${data.titleConfig.value}`
		  			});
		  		}
		  	}
		  	if (data.name === 'dateranges') {
		  	  if (!data.value.length) {
		  	    return that.$util.Tips({
		  	      title: `请选择${data.titleConfig.value}`
		  	    });
		  	  }
		  	}
		      if (data.name === 'texts' && data.valConfig.tabVal == 4) {
		        if (data.value <= 0) {
		          return that.$util.Tips({
		            title: `请填写大于0的${data.titleConfig.value}`
		          });
		        }
		      }
		      if (data.name === 'texts' && data.valConfig.tabVal == 3) {
		        if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(data.value)) {
		          return that.$util.Tips({
		            title: `请填写正确的${data.titleConfig.value}`
		          });
		        }
		      }
		      if (data.name === 'texts' && data.valConfig.tabVal == 1) {
		        if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(data.value)) {
		          return that.$util.Tips({
		            title: `请填写正确的${data.titleConfig.value}`
		          });
		        }
		      }
		  
		      if (data.name === 'texts' && data.valConfig.tabVal == 2) {
		        if (!
		          /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i
		          .test(data.value)) {
		          return that.$util.Tips({
		            title: `请填写正确的${data.titleConfig.value}`
		          });
		        }
		      }
		  
		      if (data.name === 'uploadPicture') {
		        if (!data.value.length) {
		          return that.$util.Tips({
		            title: `请上传${data.titleConfig.value}`
		          });
		        }
		      }
		    }
		  }
		  this.pay_close = true;
	  },
      SubOrder: function(e) {
        let that = this,
          data = {};
        if (!that.payType) return that.$util.Tips({
          title: '请选择支付方式'
        });
        data = {
		  collate_code_id: that.collage_id || that.tableId,
          custom_form: that.confirm,
          real_name: that.contacts,
          phone: that.contactsTel,
          addressId: that.addressId,
          formId: '',
          couponId: that.priceGroup.couponPrice == 0 ? 0 : that.couponId,
          payType: that.payType,
          useIntegral: that.useIntegral,
          bargainId: that.BargainId,
          combinationId: that.combinationId,
          discountId: that.discountId,
          pinkId: that.pinkId,
          seckill_id: that.seckillId,
          mark: that.mark,
		  order_info: this.order_info,
          // store_id: that.deliveryType==1? 0 : that.system_store.id||0,
          // store_id: 0,
          'from': that.from,
          shipping_type: 2,
          'new': that.news,
          'invoice_id': that.invChecked,
          // #ifdef H5
          quitUrl: location.protocol + '//' + location.hostname +
            '/pages/goods/order_pay_status/index?' +
            '&type=3' + '&totalPrice=' + this.totalPrice
          // #endif
          // #ifdef APP-PLUS
          quitUrl: '/pages/goods/order_details/index?order_id=' + this.order_id,
          // #endif
        };
        if (data.payType == 'yue' && parseFloat(that.userInfo.now_money) < parseFloat(that.totalPrice))
          return that.$util.Tips({
            title: '余额不足！'
          });
        uni.showLoading({
          title: '订单支付中'
        });
        // #ifdef MP
        openPaySubscribe().then(() => {
          that.payment(data);
        });
        // #endif
        // #ifndef MP
        that.payment(data);
        // #endif
      },
	  
	  showActionSheet(type) {
		  const sexList = ['未知', '男', '女']
	  	uni.showActionSheet({
	  		itemList: sexList,
	  		success: (res) => {
	  			this.order_info.sex = res.tapIndex
				this.order_info.sex_name = sexList[res.tapIndex]
	  		},
	  	})
	  },
	  goto(url) {
		  uni.navigateTo({ url })
	  }
    }
  }
</script>

<style lang="scss" scoped>
  /deep/.uni-date-x--border{
	border: 0;
  }
  /deep/.uni-icons{
	font-size: 0 !important;
  }
  /deep/.uni-date-x{
	color: #999;
	font-size: 15px;
  }
  /deep/.uni-date__x-input{
	font-size: 15px;
  }
  .height-add {
	height: calc(120rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
	height: calc(120rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
  }

  /deep/uni-checkbox[disabled] .uni-checkbox-input {
    background-color: #eee;
  }

  .confirmImg {
    width: 100%;
  }

  /deep/.orderGoods {
    margin-top: 20rpx;
    border-radius: 14rpx;
  }

  .confirmImg .upload {
    padding-bottom: 36rpx;
  }

  .confirmImg .upload .pictrue {
    margin: 22rpx 23rpx 0 0;
    width: 146rpx;
    height: 146rpx;
    position: relative;
    font-size: 24rpx;
    color: #bbb;
  }

  .confirmImg .upload .pictrue:nth-of-type(4n) {
    margin-right: 0;
  }

  .confirmImg .upload .pictrue image {
    width: 100%;
    height: 100%;
    border-radius: 8rpx;
  }

  .confirmImg .upload .pictrue .icon-guanbi1 {
    position: absolute;
    font-size: 45rpx;
    top: -10rpx;
    right: -10rpx;
  }
  
  .confirmImg .upload .pictrue .close{
	position: absolute;
	width: 26rpx;
	height: 26rpx;
	border-radius: 0 8rpx 0 8rpx;
	background-color: rgba(0, 0, 0, 0.6);
	top: 0;
	right: 0;
  }
  
  .confirmImg .upload .pictrue .close .iconfont{
	font-size: 24rpx;
  }

  .confirmImg .upload .pictrue .icon-icon25201 {
    color: #bfbfbf;
    font-size: 50rpx;
  }

  .confirmImg .upload .pictrue:nth-last-child(1) {
    border: 1rpx solid #ddd;
    box-sizing: border-box;
  }

  .alipaysubmit {
    display: none;
  }

  .order-submission .line {
    width: 100%;
    height: 4rpx;
  }

  .order-submission .line image {
    width: 100%;
    height: 100%;
    display: block;
  }

  .order-submission .headerCon {
    background-color: #fff;
    box-sizing: border-box;

    .icon-s-bianji {
			width: 44rpx;
			height: 44rpx;
			background: #F5F5F5;
			border-radius: 50%;
			text-align: center;
			line-height: 44rpx;
			font-size: 17rpx;
			color: #666;
    }

    .add-title {
			height: 72rpx;
			padding: 0 26rpx;
			border-bottom: 1px solid #eee;
      .icon {
        height: 32rpx;
        background: #1890FF;
        border-radius: 4rpx;
        font-size: 20rpx;
        font-weight: 400;
        color: #FFFFFF;
        text-align: center;
        line-height: 32rpx;
				padding: 0 6rpx;
				&.orange{
					background: #FE960F;
				}
				&.red{
					background: #E93323;
				}
      }

      .add-text {
				margin-left: 14rpx;
				width: 360rpx;
      }

      .text {
        font-size: 24rpx;
        font-weight: 400;
        color: #999999;

        .icon-jiantou {
          display: inline-block;
          font-size: 20rpx;
        }
      }
    }
  }
  .order-submission .address{
		padding: 0 26rpx;
		height: 130rpx;
		.icon{
			.iconfont{
				width: 44rpx;
				height: 44rpx;
				background: var(--view-minorColorT);
				font-size: 20rpx;
				border-radius: 50%;
				text-align: center;
				line-height: 44rpx;
				color: var(--view-theme);
				margin-left: 26rpx;
			}
		}
	}
  .order-submission .address .addressCon {
    width: 502rpx;
    font-size: 26rpx;
    color: #666;
  }

  .order-submission .address .addressCon .name {
    font-size: 30rpx;
    color: #282828;
    font-weight: bold;
    margin-bottom: 10rpx;
		.nameCon{
			max-width: 244rpx;
			&.on{
				max-width: unset;
			}
		}
  }

  .order-submission .address .addressCon .name .phone {
    margin-left: 50rpx;
  }

  .order-submission .address .addressCon .default {
    margin-right: 12rpx;
  }

  .order-submission .address .addressCon .setaddress {
    color: #333;
    font-size: 28rpx;
  }

  .order-submission .allAddress {
    width: 100%;
    background: linear-gradient(to bottom, var(--view-theme) 0%, #f5f5f5 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-top: 24rpx;

  }

  .order-submission .allAddress .nav {
    width: 710rpx;

    // margin: 0 auto;
  }

  .order-submission .allAddress .nav .item {
    width: 355rpx;
  }

  .order-submission .allAddress .nav .item.on {
    position: relative;
    width: 250rpx;
  }

  .order-submission .allAddress .nav .item.on::before {
    position: absolute;
    bottom: 0;
    content: "快递配送";
    font-size: 28rpx;
    display: block;
    height: 0;
    width: 336rpx;
    border-width: 0 20rpx 80rpx 0;
    border-style: none solid solid;
    border-color: transparent transparent #fff;
    z-index: 2;
    border-radius: 7rpx 30rpx 0 0;
    text-align: center;
    // line-height: 80rpx;
  }

  .header-title {

    font-size: 36rpx;
    font-weight: 600;
    color: #FFFFFF;
  }

  .order-submission .allAddress .nav .item:nth-of-type(2).on::before {
    content: "到店自提";
    border-width: 0 0 80rpx 20rpx;
    border-radius: 30rpx 7rpx 0 0;
  }

  .order-submission .allAddress .nav .item.on2 {
    position: relative;
  }

  .order-submission .allAddress .nav .item.on2::before {
    position: absolute;
    bottom: 0;
    content: "到店自提";
    font-size: 28rpx;
    display: block;
    height: 0;
    width: 400rpx;
    border-width: 0 0 60rpx 60rpx;
    border-style: none solid solid;
    border-color: transparent transparent rgba(255, 255, 255, 0.6);
    border-radius: 40rpx 6rpx 0 0;
    text-align: center;
    // line-height: 60rpx;
  }

  .order-submission .allAddress .nav .item:nth-of-type(1).on2::before {
    content: "快递配送";
    border-width: 0 60rpx 60rpx 0;
    border-radius: 6rpx 40rpx 0 0;
  }
	.order-submission .allAddress .headerCon{
		width: 710rpx;
		margin: 0 auto;
		border-radius: 14rpx;
		background-color: #fff;
		
	}

  .order-submission .allAddress .line {
    width: 710rpx;
    margin: 0 auto;
  }

  .order-submission .wrapper .item .discount .placeholder {
    color: #ccc;
  }

  .placeholder-textarea {
    position: relative;

    .placeholder {
      position: absolute;
      color: #ccc;
      top: 26rpx;
      left: 30rpx;
    }
  }

  .order-submission .wrapper {
    background-color: #fff;

    width: 710rpx;
    border-radius: 14rpx;
    margin: 0 auto;
    margin-top: 14rpx;
  }

  .order-submission .wrapper .item .name {
    position: relative;
	width: 190rpx;
  }

  .order-submission .wrapper .item .asterisk {
    position: absolute;
    color: red;
    left: -15rpx
  }

  .order-submission .wrapper .item {
    padding: 27rpx 30rpx;
    font-size: 30rpx;
    color: #282828;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .order-submission .wrapper .item.on{
	padding: 17rpx 14rpx 17rpx 30rpx;
	align-items: baseline;
  }
  
  .order-submission .wrapper .item.on3{
	align-items: baseline;  
  }
  
  .order-submission .wrapper .item.on2{
	padding: 17rpx 30rpx;
  }
  
  .order-submission .wrapper .item .discount {
    font-size: 30rpx;
    color: #999;
  }
  
  .order-submission .wrapper .item.on .discount,
  .order-submission .wrapper .item.on3 .discount{
	width: 460rpx;
	text-align: right;
  }
  
  .order-submission .wrapper .item.on3 .discount .city{
	width: 400rpx;
  }

  .order-submission .wrapper .item .discount input {
    text-align: right;
    width: 450rpx;
  }

  .order-submission .wrapper .item .discount .iconfont {
    color: #515151;
    font-size: 30rpx;
    margin-left: 15rpx;
  }

  .order-submission .wrapper .item .discount .num {
    font-size: 32rpx;
    margin-right: 20rpx;
  }
  
  .order-submission .wrapper .item .discount .radio{
	  margin: 0 22rpx 0 22rpx;
	  padding: 10rpx 0;
  }
  
  .order-submission .wrapper .item .discount .radio /deep/uni-checkbox .uni-checkbox-input{
	  border-radius: 0  }

  .order-submission .wrapper .item .shipping {
    font-size: 30rpx;
    color: #999;
    position: relative;
    padding-right: 58rpx;
  }

  .order-submission .wrapper .item .shipping .iconfont {
    font-size: 35rpx;
    color: #707070;
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    margin-left: 30rpx;
  }

  .order-submission .wrapper .item textarea {
    background-color: #f9f9f9;
    width: 650rpx;
    height: 140rpx;
    border-radius: 3rpx;
    margin-top: 30rpx;
    padding: 25rpx 28rpx;
    box-sizing: border-box;
  }

  .order-submission .wrapper .item .placeholder {
    color: #ccc;
  }

  .order-submission .wrapper .item .list {
    margin-top: 35rpx;
  }

  .order-submission .wrapper .item .list .payItem {
    border: 1px solid #eee;
    border-radius: 6rpx;
    height: 86rpx;
    width: 100%;
    box-sizing: border-box;
    margin-top: 20rpx;
    font-size: 28rpx;
    color: #282828;
  }

  .order-submission .wrapper .item .list .payItem.on {
    border-color: #fc5445;
    color: #e93323;
  }

  .order-submission .wrapper .item .list .payItem .name {
    width: 50%;
    text-align: center;
    border-right: 1px solid #eee;
    padding-left: 80rpx;
  }

  .order-submission .wrapper .item .list .payItem .name .iconfont {
    width: 44rpx;
    height: 44rpx;
    border-radius: 50%;
    text-align: center;
    line-height: 44rpx;
    background-color: #fe960f;
    color: #fff;
    font-size: 30rpx;
    margin-right: 15rpx;
  }

  .order-submission .wrapper .item .list .payItem .name .iconfont.icon-weixin2 {
    background-color: #41b035;
  }

  .order-submission .wrapper .item .list .payItem .name .iconfont.icon-zhifubao {
    background-color: #1677FF;
  }

  .order-submission .wrapper .item .list .payItem .tip {
    width: 49%;
    text-align: center;
    font-size: 26rpx;
    color: #aaa;
  }

  .order-submission .moneyList {

    background-color: #fff;
    padding: 30rpx;
    width: 710rpx;
    border-radius: 14rpx;
    margin: 0 auto;
    margin-top: 14rpx;

  }

  .order-submission .moneyList .item {
    font-size: 28rpx;
    color: #282828;
  }

  .order-submission .moneyList .item~.item {
    margin-top: 20rpx;
  }

  .order-submission .moneyList .item .money {
    color: #868686;
  }

  .footer {
    width: 100%;
    height: 100rpx;
    background-color: #fff;
    padding: 0 30rpx;
    font-size: 28rpx;
    color: #333;
    box-sizing: border-box;
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 9;
	height: calc(100rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
	height: calc(100rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
	padding-bottom: calc(0rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
	padding-bottom: calc(0rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
  }

  .footer .settlement {
    font-size: 30rpx;
    color: #fff;
    width: 240rpx;
    height: 70rpx;
    background-color: var(--view-theme);
    border-radius: 50rpx;
    text-align: center;
    line-height: 70rpx;
  }

  .footer .transparent {
    opacity: 0
  }

  .tipaddress {
    position: fixed;
    left: 13%;
    top: 25%;
    // margin-left: -283rpx;
    width: 560rpx;
    height: 614rpx;
    background-color: #fff;
    border-radius: 10rpx;
    z-index: 100;
    text-align: center;

    .top {
      width: 560rpx;
      height: 270rpx;
      border-top-left-radius: 10rpx;
      border-top-right-radius: 10rpx;
      background-image: url(../../../static/images/address.png);
      background-repeat: round;
      background-color: var(--view-theme);

      .tipsphoto {
        display: inline-block;
        width: 200rpx;
        height: 200rpx;
        margin-top: 73rpx;
      }
    }

    .bottom {
      font-size: 32rpx;
      font-weight: 400;

      .font1 {

        font-size: 36rpx;
        font-weight: 600;
        color: #333333;
        margin: 32rpx 0rpx 22rpx;
      }

      .font2 {
        color: #666666;
        margin-bottom: 48rpx;
      }

      .btn {
        width: 360rpx;
        height: 82rpx;
        border-radius: 42rpx;
        background: linear-gradient(to left, var(--view-theme) 0%, #f5f5f5 100%);
        color: #FFFFFF;
        line-height: 82rpx;
        margin: 0 auto;
      }
    }

  }

  .mark {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99;
  }
  
  .table-header {
	  width: 710rpx;
	  padding: 52rpx 24rpx 46rpx;
	  border-radius: 14rpx;
	  margin: 0 30rpx;
	  background-color: #FFFFFF;
	  font-size: 24rpx;
	  color: #999999;
	  
	  .top {
	  	display: flex;
	  	margin-bottom: 18rpx;
	  }
	  
	  .name {
	  	flex: 1;
	  	min-width: 0;
	  	font-weight: 600;
	  	font-size: 34rpx;
	  	color: #333333;
	  }
	  
	  .number {
	  	color: #333333;
	  }
	  
	  .light {
	  	margin-right: 6rpx;
	  	font-weight: 600;
	  	font-size: 34rpx;
	  	color: var(--view-theme);
	  }
  }
  
  .detail {
  	display: flex;
  	padding: 30rpx 36rpx;
  	background-color: #fff;
  	
  	image {
  		width: 160rpx;
  		height: 160rpx;
  		border-radius: 12rpx;
  		margin-right: 18rpx;
		flex-shrink: 0;
  	}
  	
  	.info {
  		display: flex;
  		flex-direction: column;
  		justify-content: space-around;
  		font-size: 24rpx;
  		color: #999;
  		
  		.title {
  			font-size: 36rpx;
  			color: #222;
  			font-weight: bold;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
			width: calc(100vw - 260rpx);
  		}
  		
  		text {
  			color: #666;
  		}
  		
  		.price {
  			font-weight: bold;
  			font-size: 36rpx;
  			color: #E81A1A;
  		}
  	}
  }
  
  .modules {
  	background-color: #fff;
  	margin-top: 18rpx;
  	
  	.title {
  		font-weight: bold;
  		font-size: 30rpx;
  		color: #222;
  		height: 90rpx;
  		line-height: 90rpx;
  		border-bottom: 1rpx solid #EBEBEB;
  		padding-left: 36rpx;
  	}
  	
  	.form {
  		.form-item {
  			min-height: 85rpx;
  			line-height: 85rpx;
  			display: flex;
  			justify-content: space-between;
  			flex-wrap: wrap;
  			padding: 0 36rpx;
  			
  			text, .uni-input {
  				color: #999;
  				font-size: 26rpx;
  			}
  			
  			.form-wrapper {
  				display: flex;
  				text-align: right;
				align-items: center;
				max-width: 450rpx;
				// overflow: hidden;
				// white-space: nowrap;
				// text-overflow: ellipsis;
				line-height: 38rpx;
  			}
  			
  			.integral {
  				margin: 0 10rpx;
  			}
  			
  			.iconfont {
  				font-size: 28rpx;
  				color: var(--view-theme);
  			}
  			
  			.icon-jiantou {
  				color: #999
  			}
  			
  			.complete {
  				color: #222;
  				overflow: hidden;
  				white-space: nowrap;
  				text-overflow: ellipsis;
  				width: 400rpx;
  				color: #E81A1A;
  			}
  			
  			.form-label {
  				color: #999999;
  				.asterisk {
  					color: #E81A1A;
  					margin-right: 6rpx;
  				}
  			}
  			.textarea {
  				width: 100%;
  				.mark {
  					background-color: #F5F5F5;
  					border-radius: 12rpx;
  					width: 100%;
  					padding: 8rpx 16rpx;
  					height: 150rpx;
  					line-height: 1em;
  					box-sizing: border-box;
  				}
  			}
  		}
  	}
  }
  
  .notice {
  	padding: 36rpx;
  	font-size: 24rpx;
  	color: #999;
  	.title {
  		color: #222;
  		margin-bottom: 12rpx;
		
  	}
  }
  
  .agreement {
  	display: flex;
  	flex-wrap: wrap;
  	font-size: 24rpx;
  	color: #999;
  	padding-left: 36rpx;
	margin-top: 10rpx;
  	
  	.iconfont {
  		margin-right: 10rpx;
  		color: #FB4095;
  	}
  	
  	.color {
  		color: #FB4095;
  	}
  }
  
  .box {
	  padding-bottom: calc(140rpx + env(safe-area-inset-bottom));
  }
</style>
