<template>
	<div>
		<Card :bordered="false" dis-hover>
			<Tabs v-model="currentTab">
				<TabPane :label="'基础设置'" name="1" />
				<TabPane :label="'配送设置'" name="2" />
			</Tabs>
		</Card>
		<Card :bordered="false" dis-hover class="mb79">
			<Form ref="formItem" :model="formItem" :label-width="labelWidth" :label-position="labelPosition" :rules="ruleValidate" @submit.native.prevent>
				<Row type="flex" style="width: 580px;">
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店照片：" prop="image">
								<div class="picBox" @click="modalPicTap('单选')">
									<div class="pictrue" v-if="formItem.image"><img v-lazy="formItem.image"></div>
									<div class="upLoad acea-row row-center-wrapper"   v-else>
										<!-- <Icon type="ios-camera-outline" size="26" class="iconfont"/> -->
										<span class="iconfont icontupian"></span>
									</div>
								</div>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店名称：" prop="name" label-for="name">
								<Input v-model="formItem.name" maxlength="20" show-word-limit placeholder="请输入门店名称" class="inputW"/>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店简介："label-for="introduction">
								<Input v-model="formItem.introduction" maxlength="100" type="textarea" show-word-limit placeholder="请输入门店简介"  class="inputW" />
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店手机号：" label-for="phone" prop="phone">
								<Input v-model="formItem.phone"  placeholder="请输入门店手机号"  class="inputW"/>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="营业状态：" label-for="is_show" prop="is_show">
								<Switch size="large" v-model="formItem.is_show" :false-value="0" :true-value="1">
									<span slot="open" :true-value="1">开启</span>
									<span slot="close" :false-value="0">关闭</span>
								</Switch>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="formItem.is_show == 1 && currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="营业时间：" label-for="day_time" prop="day_time">
								<TimePicker type="timerange" @on-change="onchangeTime" v-model="formItem.day_time"  format="HH:mm:ss" :value="formItem.day_time" placement="bottom-end" placeholder="请选择营业时间" class="inputW" ></TimePicker>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 2">
						<Col v-bind="grid">
							<FormItem label="同城配送：" label-for="city_delivery_status" prop="city_delivery_status">
								<Switch size="large" v-model="formItem.city_delivery_status" :false-value="0" :true-value="1">
									<span slot="open" :true-value="1">开启</span>
									<span slot="close" :false-value="0">关闭</span>
								</Switch>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="formItem.city_delivery_status && currentTab == 2">
						<Col v-bind="grid">
							<FormItem label="第三方配送：" required label-for="city_delivery_type" prop="city_delivery_type">
								<RadioGroup v-model="formItem.city_delivery_type" @on-change="deliveryType">
									<Radio :label="1">达达快送</Radio>
									<Radio :label="2">UU跑腿</Radio>
									<Radio :label="0">均不使用</Radio>
								</RadioGroup>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="formItem.city_delivery_status && formItem.city_delivery_type>0 && currentTab == 2">
						<Col v-bind="grid">
							<FormItem label="配送商品类型：">
								<Select v-model="formItem.business" placeholder="全部" class="input-add">
									<Option :value="item.key" v-for="item in businessList" :key="item.key">{{item.label}}</Option>
								</Select>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 2">
						<Col v-bind="grid">
							<FormItem label="到店自提：" label-for="mention" prop="mention">
								<Switch size="large" v-model="formItem.is_store" :false-value="0" :true-value="1">
									<span slot="open" :true-value="1">开启</span>
									<span slot="close" :false-value="0">关闭</span>
								</Switch>
							</FormItem>
						</Col>
					</Col>
				    <Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
						  <FormItem label="门店首页样式：" label-for="home_style" prop="home_style">
							<RadioGroup v-model="formItem.home_style">
							  <Radio :label="1">样式1</Radio>
							  <Radio :label="2">样式2</Radio>
							</RadioGroup>
						  </FormItem>
						</Col>
				    </Col>
            <Col span="24">
						<Col v-bind="grid" v-if="currentTab == 2">
						  <FormItem label="默认配送方式：">
							<RadioGroup v-model="formItem.default_delivery">
							  <Radio :label="1">配送</Radio>
							  <Radio :label="2">自提</Radio>
							</RadioGroup>
						  </FormItem>
						</Col>
				    </Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="客服类型：" label-for="customer_type" prop="customer_type">
								<RadioGroup v-model="formItem.customer_type">
									<Radio :label="1">电话</Radio>
									<Radio :label="2">二维码</Radio>
								</RadioGroup>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店地址：" label-for="address" prop="address">
								<Cascader :data="addresData" :load-data="loadData" v-model="formItem.addressSelect" @on-change="addchack" class="inputW"></Cascader>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="配送范围(半径)：" label-for="valid_range" prop="valid_range">
								<InputNumber :min="0" :step="0.001" v-model="formItem.valid_range" :formatter="value => `${formItem.valid_range}`" :parser="value => value.replace('%', '')"></InputNumber><span style="margin-left: 10px;">km</span>
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-if="currentTab == 1">
						<Col v-bind="grid">
							<FormItem label="门店详细地址：" label-for="detailed_address" prop="detailed_address">
								<Input search enter-button="查找位置" v-model="formItem.detailed_address"  placeholder="输入地址(包含城市名称,否则会影响搜索精度)" class="inputW" @on-search="onSearch" />
							<!-- 	提示：定位地址后，手动补充完详细地址，禁止再次点击查找 -->
							</FormItem>
						</Col>
					</Col>
					<Col span="24" v-show="isApi && currentTab == 1">
						<Maps v-if="mapKey" ref="mapChild" class="map-sty" :mapKey="mapKey" :lat="Number(formItem.latitude || 34.34127)" :lon="Number(formItem.longitude || 108.93984)" :address="formItem.detailed_address" @getCoordinates="getCoordinates" />
					</Col>
				</Row>
				<!-- <div class="footer acea-row row-center-wrapper">
					<Button type="primary" class="ml20" @click="handleSubmit('formItem')">保存</Button>
				</div> -->
				<Spin size="large" fix v-if="spinShow"></Spin>
			</Form>
		</Card>
		<div class="h50"></div>
		<Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
		    <Form>
		        <FormItem>
		            <Button
		                    type="primary"
		                    class="submission"
		                    @click="handleSubmit('formItem')"
		            >保存</Button
		            >
		        </FormItem>
		    </Form>
		</Card>
		<Modal v-model="modalPic" width="960px" scrollable  footer-hide closable title='上传门店照片' :mask-closable="false" :z-index="99">
			<uploadPictures :isChoice="isChoice" @getPic="getPic" :gridBtn="gridBtn" :gridPic="gridPic" v-if="modalPic"></uploadPictures>
		</Modal>
	</div>
</template>


<script>
	import { keyApi, storeUpdateApi, storeGetInfoApi, cityApi, getBusiness } from '@/api/setting';
	import { mapState,mapMutations } from "vuex";
	import uploadPictures from '@/components/uploadPictures';
	import Maps from '@/components/map/map.vue'
	export default {
		name: 'systemStore',
		components: { uploadPictures,Maps },
		props: { },
		data () {
			const validatePhone = (rule, value, callback) => {
				if (!value) {
					return callback(new Error('请填写手机号'));
				} else if (!/^1[3456789]\d{9}$/.test(value)) {
					callback(new Error('手机号格式不正确!'));
				} else {
					callback();
				}
			};
			const validateUpload = (rule, value, callback) => {
				if (!this.formItem.image) {
					callback(new Error('请上传门店照片'))
				} else {
					callback()
				}
			};
			return {
				currentTab:'1',
				formItem: {
					image: '',
					name: '',
					introduction: '',
					phone: '',
					is_show: true,
					day_time: [],
					is_store: true,
					address: '',
					detailed_address: '',
					latitude:'',
					longitude:'',
					province:0,
					city:0,
					area:0,
					street:0,
					addressSelect:[],
					valid_range:0,
					city_delivery_status:1,
					city_delivery_type:0,
				    home_style:1,//门店首页样式
				    business:0,//同城配送商品类型
					customer_type:1,//客服类型1：电话，2：二维码
                default_delivery: 1
				},
				spinShow: false,
				addresData: [],
				ruleValidate: {
					name: [
						{ required: true, message: '请输入门店名称', trigger: 'blur' }
					],
					phone: [
						{ required: true, validator: validatePhone, trigger: 'blur' }
					],
					detailed_address: [
						{ required: true, message: '请输入详细地址', trigger: 'blur' }
					],
					image: [
						{ required: true, validator: validateUpload, trigger: 'change' }
					],
					day_time: [
						{required: true,type: "array", message: "请选择营业时间",trigger: "change"},
						{validator(rule, value, callback, source, options) 
							{
								if (value[0] === "") {
								callback("请选择营业时间");
								}
							 callback();//这个一定要有。不然无法验证通过
							}
						}
					],//TimePicker-timerange，自定义的
				},
				mapKey: '',
				grid: {
					xl: 20,
					lg: 20,
					md: 20,
					sm: 24,
					xs: 24
				},
				gridPic: {
					xl: 6,
					lg: 8,
					md: 12,
					sm: 12,
					xs: 12
				},
				gridBtn: {
					xl: 4,
					lg: 8,
					md: 8,
					sm: 8,
					xs: 8
				},
				modalPic: false,
				isChoice: '单选',
				pid:0,
				isApi:0,
				businessList:[]
			}
		},
		created () {
			this.getKey();
			this.getInfo();
			let data = {pid:0};
			this.cityInfo(data);
		},
		computed: {
			...mapState('store/layout', [
				'isMobile','menuCollapse'
			]),
			labelWidth () {
				return this.isMobile ? undefined : 164;
			},
			labelPosition () {
				return this.isMobile ? 'top' : 'right';
			}
		},
		mounted: function () {
			this.setCopyrightShow({ value: false });
			console.log('5555');
		},
		destroyed () {
		    this.setCopyrightShow({ value: true });
			console.log('4444');
		},
		methods: {
			...mapMutations('admin/layout', [
			    'setCopyrightShow'
			]),
			deliveryType(){
				if(this.formItem.city_delivery_type>0){
					this.getBusinessList();
				}
			},
			getBusinessList(){
				getBusiness(this.formItem.city_delivery_type).then(res=>{
					this.businessList = res.data;
				}).catch(err=>{
					this.$Message.error(err.msg)
				})
			},
			addchack(e,selectedData){
				e.forEach((i,index)=>{
					if(index==0){
						this.formItem.province = i
					}else if(index==1){
						this.formItem.city = i
					}else if(index==2){
						this.formItem.area = i
					}else {
						this.formItem.street = i
					}
				})
				this.formItem.address = (selectedData.map(o => o.label)).join("/");
			},
			cityInfo(data){
				cityApi(data).then(res=>{
					this.addresData = res.data
				})
			},
			loadData (item, callback) {
				item.loading = true;
				cityApi({pid:item.value}).then(res=>{
					item.children = res.data;
					item.loading = false;
					callback();
				});
			},
			// 地图信息获取
			getCoordinates(data) {
				if(this.isApi){
					this.formItem.latitude = data.location.lat || 34.34127
					this.formItem.longitude = data.location.lng || 108.93984
					// // this.formItem.detailed_address = data.address
					let components = data.addressComponents;
					if(this.formItem.detailed_address.indexOf(components.street) == -1){
						this.formItem.detailed_address = data.address+(components.town?components.town:'');
					}
				}
			},
			// 查找位置
			onSearch() {
				this.$refs.mapChild.searchKeyword(this.formItem.detailed_address)
			},
			// key值
			getKey () {
				keyApi().then(res => {
					this.mapKey = res.data.tengxun_map_key
				}).catch(res => {
					this.$Message.error(res.msg)
				})
			},
			// 详情
			getInfo () {
				let that = this;
				that.spinShow = true;
				storeGetInfoApi().then(res => {
					this.isApi = 1;
					let addressSelect = []
					this.formItem = res.data;
					if(res.data.province){
						addressSelect.push(res.data.province)
					}
					if(res.data.city){
						addressSelect.push(res.data.city)
					}
					if(res.data.area){
						addressSelect.push(res.data.area)
					}
					if(res.data.street){
						addressSelect.push(res.data.street)
					}
					this.formItem.addressSelect = addressSelect;
					this.$set(this.formItem,'valid_range',(this.formItem.valid_range)/1000)
					that.spinShow = false;
					this.deliveryType();
				}).catch(function (res) {
					that.spinShow = false;
					that.$Message.error(res.msg);
				})
			},
			// 选择图片
			modalPicTap () {
				this.modalPic = true;
			},
			// 选中图片
			getPic (pc) {
				this.formItem.image = pc.att_dir;
				this.modalPic = false;
			},
			// 营业时间
			onchangeTime (e) {
				this.formItem.day_time = e;
			},
			// 提交
			handleSubmit (name) {
				this.$refs[name].validate((valid) => {
					if (valid) {
						if(this.formItem.day_time[0] == ''){
							this.formItem.day_time = ['00:00:00', '23:59:59']
						}
						
						if(this.formItem.valid_range == ''||this.formItem.valid_range<0){
							return this.$Message.error('请输入有效的门店范围');
						}
						storeUpdateApi(this.formItem).then(async res => {
							this.$Message.success(res.msg);
						}).catch(res => {
							this.$Message.error(res.msg);
						})
					} else {
						this.$Message.error('请完善信息');
					}
				})
			}
		}
	}
</script>

<style scoped lang="stylus">
	/deep/.ivu-tabs {
	    background-color: #ffffff;
	    padding: 3px 20px 0 20px;
	    border-radius: 6px;
	}
	/deep/.ivu-tabs-nav .ivu-tabs-tab {
	    padding: 4px 16px 20px !important;
	    font-weight: 500;
	}
	.fixed-card {
	    position: fixed;
	    right: 0;
	    bottom: 0;
	    left: 200px;
	    z-index: 20;
	    box-shadow: 0 -1px 2px rgb(240, 240, 240);
		
	    /deep/ .ivu-card-body {
	        padding: 15px 16px 14px;
	    }
		
	    .ivu-form-item {
	        margin-bottom: 0;
	    }
		
	    /deep/ .ivu-form-item-content {
	        margin-right: 124px;
	        text-align: center;
	    }
		
	    .ivu-btn {
	        height: 36px;
	        padding: 0 20px;
	    }
	}
	.map-sty {
		width: 90%;
		text-align: right;
		margin: 0 0 0 10%;
	}
	.footer{
		width 100%;
		height 50px;
		box-shadow: 0px -2px 4px 0px rgba(0, 0, 0, 0.05);
		margin-top 50px;
	}
	/deep/.ivu-btn-primary{
		width 86px;
	}
	.inputW{
		width 400px;
	}
	.ivu-mt{
		min-width 580px;
	}
	.picBox
		display: inline-block;
		cursor: pointer;
		.upLoad
			width: 58px;
			height: 58px;
			line-height: 58px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			border-radius: 4px;
			background: rgba(0, 0, 0, 0.02);
		.pictrue
			width: 60px;
			height: 60px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			margin-right: 10px;
			img
				width: 100%;
				height: 100%;
		.iconfont
			color: #898989;
</style>
