<template>
    <div class="c_product" v-if="configData">
        <div class="title">{{configData.title}}</div>
        <div class="list-box">
            <draggable
                    class="dragArea list-group"
                    :list="configData.list"
                    group="peoples"
                    handle=".move-icon"
            >
                <div class="item" v-for="(item,index) in configData.list" :key="index" @click="activeBtn(index)" v-model="configData.tabCur">
					<div class="acea-row">
						<div class="move-icon">
						    <span class="iconfont-diy icondrag2"></span>
						</div>
						<div class="content">
						    <div class="con-item" v-for="(list,key) in item.chiild" :key="key">
						        <span>{{list.title}}</span>
						        <div style="width: 100%">
						            <Input v-model="list.val" :placeholder="list.pla" :maxlength="list.max"/>
						        </div>
						    </div>
						</div>
					</div>
					<div class="acea-row row-right" v-if="configData.tabCur == index">
						<div class="conter">
							<div class="c_row-item">
							    <Col class="c_label">
							        选择方式<span>{{item.tabVal?'手动选择':'自动选择'}}</span>
							    </Col>
							    <Col class="color-box">
							        <RadioGroup v-model="item.tabVal" type="button" @on-change="tabChange($event)">
							            <Radio :label="0">
							                <span class="iconfont-diy iconzidongxuanze"></span>
							            </Radio>
							            <Radio :label="1">
							                <span class="iconfont-diy iconshoudongxuanze"></span>
							            </Radio>
							        </RadioGroup>
							    </Col>
							</div>
							<div v-if="item.tabVal == 0">
								<div class="c_row-item">
								    <Col class="label" span="5">商品分类</Col>
								    <Col span="18" class="slider-box">
										<Cascader
											 :data="configData.classList"
											 placeholder="请选择商品分类"
											 change-on-select
											 v-model="item.selectConfig.activeValue"
											 filterable
											 @on-change="sliderChange"
										></Cascader>
								    </Col>
								</div>
								<div class="c_row-item">
								    <Col class="label" span="5">商品标签</Col>
								    <Col span="18" class="slider-box">
										<div class="labelInput acea-row row-between-wrapper" @click="openStoreLabel(item.goodsLabel.list,index)">
										  <div style="width: 90%;">
										    <div v-if="item.goodsLabel.list.length">
										      <Tag closable v-for="(j,jindex) in item.goodsLabel.list" :key="jindex" @on-close="closeStoreLabel(j)">{{j.label_name}}</Tag>
										    </div>
										    <span class="span" v-else>选择商品标签</span>
										  </div>
										  <div class="iconfont iconxiayi"></div>
										</div>
								    </Col>
								</div>
								<div class="c_row-item">
								    <Col class="c_label">
								        商品排序<span>{{item.goodsSort==0?'综合':item.goodsSort==1?'销量':'价格'}}</span>
								    </Col>
								    <Col class="color-box">
								        <RadioGroup v-model="item.goodsSort" type="button" @on-change="radioChange($event)">
								            <Radio :label="0">
								                <span class="iconfont-diy iconComm_whole"></span>
								            </Radio>
								            <Radio :label="1">
								                <span class="iconfont-diy iconComm_number"></span>
								            </Radio>
											<Radio :label="2">
											    <span class="iconfont-diy iconComm_Price"></span>
											</Radio>
								        </RadioGroup>
								    </Col>
								</div>
								<div class="c_row-item">
									<Col class="label" span="5">
										<span>商品数量</span>
									</Col>
									<Col span="18" class="slider-box">
										<InputNumber v-model="item.numConfig.val" placeholder="请输入数量" :step="1" :max="100" :min="1" @on-change="radioChange($event)"></InputNumber>
									</Col>
								</div>
							</div>
							<div class="goods-box" v-else>
							    <draggable
							            class="dragArea list-group"
							            :list="item.goodsList.list"
							            group="peoples"
							    >
									<div class="items" v-for="(goods,gIndex) in item.goodsList.list" :key="gIndex" v-if="item.goodsList.list.length">
										<img :src="goods.image" alt="">
										<span class="iconfont-diy icondel_1" @click.stop="bindGoodDelete(gIndex)"></span>
									</div>
							        <div class="add-item items" @click="openGoods(index)"><span class="iconfont-diy iconaddto"></span></div>
							    </draggable>
							</div>
						</div>
					</div>
                    <div class="delete" @click.stop="bindDelete(index)">
                        <Icon type="ios-close-circle" size="26"/>
                    </div>
                </div>
            </draggable>
        </div>
        <div v-if="configData.list">
            <div class="add-btn" @click="addHotTxt">
                <Button type="primary" ghost style="width: 100%; height: 40px; border-color:#1890FF; color: #1890FF;">添加模块</Button>
            </div>
        </div>
		<!-- 商品标签 -->
		<Modal
		    v-model="storeLabelShow"
		    scrollable
		    title="选择商品标签"
		    :closable="true"
		    width="540"
		    :footer-hide="true"
		    :mask-closable="false"
		>
		  <storeLabelList ref="storeLabel" @activeData="activeStoreData" @close="storeLabelClose"></storeLabelList>
		</Modal>
		<Modal v-model="modals" title="商品列表" footerHide  class="paymentFooter" scrollable width="900" @on-cancel="cancel">
		    <goods-list ref="goodslist" :ischeckbox="true" :isdiy="true"  @getProductId="getProductId" v-if="modals"></goods-list>
		</Modal>
    </div>
</template>

<script>
    import vuedraggable from 'vuedraggable'
    import storeLabelList from "@/components/storeLabelList";
	import goodsList from '@/components/goodsList'
    export default {
        name: 'c_promotion',
        props: {
            configObj: {
                type: Object
            },
            configNme: {
                type: String
            },
            index: {
                type: null
            }
        },
        components: {
            draggable: vuedraggable,
			storeLabelList,
			goodsList
        },
        data () {
            return {
                defaults: {},
                configData: {},
                itemObj: {},
				storeLabelShow:false,
				modals: false,
            }
        },
        mounted () {
            this.$nextTick(() => {
                this.defaults = this.configObj
                this.configData = this.configObj[this.configNme]
            })
        },
        watch: {
            configObj: {
                handler (nVal, oVal) {
                    this.defaults = nVal
                    this.configData = nVal[this.configNme]
                },
                deep: true
            }
        },
        methods: {
			openGoods(){
				this.modals = true;
			},
			//对象数组去重；
			unique(arr) {
			    const res = new Map();
			    return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1))
			},
			getProductId (data) {
			    this.modals = false;
			    let list = this.configData.list[this.configData.tabCur].goodsList.list.concat(data);
			    this.configData.list[this.configData.tabCur].goodsList.list = this.unique(list);
			},
			cancel () {
			    this.modals = false;
			},
			bindGoodDelete (index) {
			    this.configData.list[this.configData.tabCur].goodsList.list.splice(index, 1)
			},
			openStoreLabel(row,index) {
			  this.storeLabelShow = true;
			  this.$refs.storeLabel.storeLabel(JSON.parse(JSON.stringify(row)));
			},
			closeStoreLabel(label){
			  let list = this.configData.list[this.configData.tabCur].goodsLabel.list;
			  let index = list.indexOf(list.filter(d=>d.id == label.id)[0]);
			  list.splice(index,1);
			  this.getLabelId(list);
			},
			activeStoreData(storeDataLabel){
			  this.storeLabelShow = false;
			  this.configData.list[this.configData.tabCur].goodsLabel.list = storeDataLabel;
			  this.getLabelId(storeDataLabel);
			},
			getLabelId(storeDataLabel){
			  let storeActiveIds = [];
			  storeDataLabel.forEach((item)=>{
			    storeActiveIds.push(item.id)
			  });
			  this.configData.list[this.configData.tabCur].goodsLabel.activeValue = storeActiveIds;
			  this.$emit('getConfig', { name: 'goodsLabel'})
			},
			// 标签弹窗关闭
			storeLabelClose() {
			  this.storeLabelShow = false;
			},
            addHotTxt () {
                if (this.configData.list.length == 0) {
                    let storage = window.localStorage;
                    this.itemObj = JSON.parse(storage.getItem('itemObj'));
                    if(this.itemObj.link){
                        this.itemObj.link.activeVal = 0;
                    }
                    this.itemObj.chiild[0].val='首发新品';
                    this.itemObj.chiild[1].val='最新出炉';
					this.itemObj.tabVal = 0;
					this.itemObj.selectConfig.activeValue = [];
					this.itemObj.goodsLabel.activeValue = [];
					this.itemObj.goodsLabel.list = [];
					this.itemObj.goodsSort = 0;
					this.itemObj.numConfig.val = 6;
					this.itemObj.goodsList.list = [];
					this.itemObj.productList.list = [];
                    this.configData.list.push(this.itemObj);
                } else {
                    let obj = JSON.parse(JSON.stringify(this.configData.list[this.configData.list.length - 1]));
                    if(obj.chiild[0].empty){
                        obj.chiild[0].val='';
                        obj.chiild[1].val='';
                    }
					obj.tabVal = 0;
					obj.selectConfig.activeValue = [];
					obj.goodsLabel.activeValue = [];
					obj.goodsLabel.list = [];
					obj.goodsSort = 0;
					obj.numConfig.val = 6;
					obj.goodsList.list = [];
					obj.productList.list = [];
                    this.configData.list.push(obj);
                }
            },
            // 删除数组
            bindDelete (index) {
                if (this.configData.list.length == 1) {
                    let itemObj = this.configData.list[0];
                    this.itemObj = itemObj;
                    let storage = window.localStorage;
                    storage.setItem('itemObj', JSON.stringify(itemObj));
                }
                this.configData.list.splice(index, 1)
                this.configData.tabCur = 0;
                this.$emit('getConfig', { name: 'delete', indexs: 0})
            },
            activeBtn (index) {
                this.configData.tabCur = index;
                // this.$emit('getConfig', { name: 'product', indexs: index })
            },
			radioChange (e) {
				this.$emit('getConfig', { name: 'promotion', values: e })
			},
			//商品分类
			sliderChange (e) {
				this.configData.list[this.configData.tabCur].selectConfig.activeValue = e;
				this.$emit('getConfig', { name: 'cascader', values: e })
			},
			tabChange(e){
				this.$emit('getConfig', e)
			}
        }
    }
</script>

<style scoped lang="stylus">
	.goods-box{
		padding 16px 0
		margin-bottom 16px
		border-top 1px solid rgba(0,0,0,0.05)
		.list-group{
			display flex
			flex-wrap wrap
		}
		.add-item{
			display flex
			align-items center
			justify-content center
			width 80px
			height 80px
			margin-bottom 10px
			background #fff
			.iconfont-diy{
				font-size 20px
				color #D8D8D8
			}
		}
		.items{
			position relative
			width 80px
			height 80px
			margin-bottom 20px
			margin-right 12px
			img{
				width 100%
				height 100%
			}
			.icondel_1{
				position absolute
				right -10px
				top -16px
				color #999999
				font-size 25px
				cursor pointer
			}
		}
	}
	.ivu-input-number{
	  width 100%
	}
	.labelInput{
	  border: 1px solid #dcdee2;
	  width 100%;
	  padding 0 5px;
	  border-radius 5px;
	  min-height 30px;
	  background-color: #fff;
	  cursor pointer;
	  .span{
	    color: #c5c8ce;
	  }
	  .iconxiayi{
	    font-size 12px
	  }
	}
	.conter{
		width: 329px;
		background: #FAFAFA;
		padding: 20px 14px 0 14px;
		margin-top: 20px;
	}
	.txt_tab{
		margin-top 20px
	}
	.c_row-item{
		margin-bottom 20px
		.c_label{
			span{
				margin-left: 14px;
			}
		}
	}
	.row-item{
		display flex
		justify-content space-between
		align-items center
	}
	.iconfont{
		font-size 18px
	}
    /deep/.ivu-input{
		font-size 13px!important
	}
    .c_product{
		border-bottom 1px solid rgba(0,0,0,0.05);
		padding-bottom 20px
		margin-bottom 20px
		.list-box{
			.item{
				position relative
				margin-top 23px
				padding 18px 20px 18px 0
				border: 1px solid rgba(238, 238, 238, 1);
				// box-shadow: 0 0 10px #eee;
						
				.delete{
					position absolute
					right 0
					top 0
					right: -13px;
					top: -14px;
					color #999999
					cursor pointer
				}
			}
					
			.move-icon{
				display flex
				align-items center
				justify-content center
				width 50px
				cursor move
			}
					
			.content{
				flex 1
						
				.con-item{
					display flex
					align-items center
					margin-bottom 15px
							
					&:last-child{
						margin-bottom 0
					}
					span{
						width 45px
						font-size 13px
					}
				}
			}
		}
		
		.add-btn{
			margin-top 18px
		}
	}
    .title{
		padding-top 20px
		font-size 12px
		color #999
	}
    .icondrag2{
		color #DDDDDD
		font-size 38px
	}
</style>
