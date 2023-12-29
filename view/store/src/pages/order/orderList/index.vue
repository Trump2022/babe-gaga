<template>
  <div>
    <Card :bordered="false" dis-hover class="mt15">
      <Form
        ref="orderData"
        :model="orderData"
        :label-width="labelWidth"
        :label-position="labelPosition"
        class="tabform"
        @submit.native.prevent
      >
        <Row>
          <Col
            class="ivu-text-left mr"
          >
            <FormItem label="订单状态：">
              <Select
                v-model="orderData.status"
                style="width: 250px"
                @on-change="userSearchs"
              >
                <Option
                  v-for="item in statusList"
                  :value="item.value"
                  :key="item.value"
                  >{{ item.label }}</Option
                >
              </Select>
            </FormItem>
          </Col>
          <Col class="ivu-text-left mr">
            <FormItem label="创建时间：">
              <DatePicker
                :editable="false"
                :clearable="true"
                @on-change="onchangeTime"
                :value="timeVal"
                format="yyyy/MM/dd"
                type="datetimerange"
                placement="bottom-start"
                placeholder="自定义时间"
                style="width: 250px"
                :options="options"
              >
              </DatePicker>
            </FormItem>
          </Col>
          <Col class="mr">
            <FormItem label="活动类型：">
              <Select
                v-model="orderData.type"
                clearable
                @on-change="userSearchs"
                style="width: 250px"
              >
                <Option value="0">普通订单</Option>
                <Option value="1">秒杀订单</Option>
                <Option value="2">砍价订单</Option>
                <Option value="3">拼团订单</Option>
                <Option value="4">套餐订单</Option>
                <Option value="9">新人订单</Option>
                <Option value="10">抽奖订单</Option>
                <Option value="11">拼单订单</Option>
                <Option value="12">桌码订单</Option>
              </Select>
            </FormItem>
          </Col>
          <Col class="ivu-text-left mr">
            <FormItem label="选择店员：">
              <Select
                v-model="orderData.staff_id"
                clearable
                @on-change="userSearchs"
                style="width: 250px"
              >
                <Option
                  v-for="item in staffData"
                  :value="item.value"
                  :key="item.value"
                  >{{ item.label }}
                </Option>
              </Select>
            </FormItem>
          </Col>
		  <Col class="mr">
		  				<FormItem label="支付方式：">
		  				  <Select
		  				    v-model="orderData.pay_type"
		  				    clearable
		  				    style="width: 250px"
		  				    @on-change="userSearchs"
		  				    placeholder="全部"
		  				  >
		  				    <Option
		  				      v-for="item in payList"
		  				      :value="item.val"
		  				      :key="item.id"
		  				      >{{ item.label }}</Option
		  				    >
		  				  </Select>
		  				</FormItem>
		  </Col>
		  <Col class="mr">
		    <FormItem
		      label="订单搜索："
		      label-for="real_name"
		    >
		      <Input
		      
		        enter-button
		        placeholder="订单号/商品名称"
		        v-model="orderData.real_name"
		        style="width: 250px"
		      />
		         <Button type="primary" class="ml20 search"  @click="userSearchs">搜索</Button>
		    </FormItem>
		  </Col>
        </Row>
      </Form>
    </Card>
    <Card :bordered="false" dis-hover class="mt15">
      <div class="product_tabs tabbox mb20" v-if="Object.keys(tablists).length">
        <Tabs v-model="currentTab" @on-click="onClickTab">
          <TabPane :label="'收银台订单（' + tablists.cashier + '）'" name="6" />
          <TabPane :label="'配送订单（' + tablists.delivery + '）'" name="7" />
          <TabPane :label="'核销订单（' + tablists.writeoff + '）'" name="5" />
        </Tabs>
      </div>
      <Row type="flex">
        <Col v-if="currentTab == 6">
          <Button
            v-auth="['order-cashier-cashier_scan']"
            type="primary"
            class="mr"
            @click="qrcodeShow"
            >下载收银码</Button
          >
        </Col>
        <Col>
          <Button
            v-auth="['order-export']"
            icon="ios-share-outline"
            @click="exports"
            >导出</Button
          >
        </Col>
        <Col>
		  <Tooltip
		    content="本页至少选中一项"
		    :disabled="!!checkUidList.length && isAll==0"
		  >
		    <Button
		      class="ml15"
		      type="primary"
		      :disabled="!checkUidList.length && isAll==0"
		      @click="printOreder"
		      >打印配货单</Button
		    >
		  </Tooltip>
        </Col>
      </Row>
	  <vxe-table
	      ref="xTable"
	      class="ivu-mt"
	      :loading="loading"
	      row-id="id"
	      :expand-config="{accordion: true}"
	      :checkbox-config="{reserve: true}"
	      @checkbox-all="checkboxAll"
	      @checkbox-change="checkboxItem"
	      :data="tableList">
	    <vxe-column type="" width="0"></vxe-column>
	    <vxe-column type="expand" width="35">
	      <template #content="{ row }">
	        <div class="tdinfo">
	          <Row class="expand-row">
	            <Col span="8">
	              <span class="expand-key">商品总价：</span>
	              <span class="expand-value" v-text="row.total_price"></span>
	            </Col>
	            <Col span="8">
	              <span class="expand-key">下单时间：</span>
	              <span class="expand-value" v-text="row.add_time"></span>
	            </Col>
	            <Col span="8">
	              <span class="expand-key">推广人：</span>
	              <span class="expand-value" v-text="row.spread_nickname?row.spread_nickname:'无'"></span>
	            </Col>
	          </Row>
	          <Row class="expand-row">
	            <Col span="8">
	              <span class="expand-key">用户备注：</span>
	              <span class="expand-value" v-text="row.mark?row.mark:'无'"></span>
	            </Col>
	            <Col span="8">
	              <span class="expand-key">商家备注：</span>
	              <span class="expand-value" v-text="row.remark?row.remark:'无'"></span>
	            </Col>
	          </Row>
	        </div>
	      </template>
	    </vxe-column>
	    <vxe-column type="checkbox" width="100">
	      <template #header>
	        <div>
	          <Dropdown transfer @on-click="allPages">
	            <a href="javascript:void(0)" class="acea-row row-middle">
	              <span>全选({{isAll==1?(total-checkUidList.length):checkUidList.length}})</span>
	              <Icon type="ios-arrow-down"></Icon>
	            </a>
	            <template #list>
	              <DropdownMenu>
	                <DropdownItem name="0">当前页</DropdownItem>
	                <DropdownItem name="1">所有页</DropdownItem>
	              </DropdownMenu>
	            </template>
	          </Dropdown>
	        </div>
	      </template>
	    </vxe-column>
	    <vxe-column field="order_id" title="订单号" min-width="175">
	      <template v-slot="{ row }">
			<span v-text="row.order_id" style="display: block"></span>
			<span
			  v-show="row.is_del === 1 && row.delete_time == null"
			  style="color: #ed4014; display: block"
			  >用户已删除</span
			>
	      </template>
	    </vxe-column>
	    <vxe-column field="nickname" title="用户信息" min-width="210">
	      <template v-slot="{ row }">
	        <div
	          class="tabBox"
	        >
	          <div class="tabBox_img" v-viewer>
	            <img v-lazy="row.avatar" v-if="row.uid" />
	            <img src="@/assets/images/yonghu.png" v-else />
	          </div>
	          <span class="tabBox_tit" v-if="row.uid">
	            <a @click="showUserInfo(row)"
	              >{{ row.nickname
	              }}<span style="margin-left: 5px">/{{ row.uid }}</span></a
	            >
	          </span>
	          <span class="tabBox_tit" v-else>
	            游客<span style="margin-left: 5px">/{{ row.uid }}</span>
	          </span>
	          <span style="color: #ed4014" v-if="row.delete_time != null">
	            (已注销)</span
	          >
	        </div>
	      </template>
	    </vxe-column>
	    <vxe-column field="info" title="商品信息" min-width="330">
	      <template v-slot="{ row }">
	        <div class="tabBox" v-for="(val, i) in row._info" :key="i">
	          <div class="tabBox_img" v-viewer>
	            <img
	              v-lazy="
	                val.cart_info.productInfo.attrInfo
	                  ? val.cart_info.productInfo.attrInfo.image
	                  : val.cart_info.productInfo.image
	              "
	            />
	          </div>
	          <span class="tabBox_tit">
	            <span class="font-color-red" v-if="val.cart_info.is_gift"
	              >[赠品]</span
	            >{{ val.cart_info.productInfo.store_name + ' | '
	            }}{{
	              val.cart_info.productInfo.attrInfo
	                ? val.cart_info.productInfo.attrInfo.suk
	                : ''
	            }}
	          </span>
	          <span class="tabBox_pice">{{
	            '￥' + val.cart_info.truePrice + ' x ' + val.cart_info.cart_num
	          }}</span>
	        </div>
	      </template>
	    </vxe-column>
	    <vxe-column field="pay_price" title="实际支付" align="center" min-width="70">
	      <template v-slot="{ row }">
	        <span>{{ row.paid > 0 ? row.pay_price : 0 }}</span>
	      </template>
	    </vxe-column>
	    <vxe-column field="pay_type_name" title="支付方式" min-width="100"></vxe-column>
		<vxe-column field="clerk_name" title="收银店员" min-width="100"></vxe-column>
	    <vxe-column field="add_time" title="下单时间" min-width="130"></vxe-column>
	    <vxe-column field="statusName" title="订单状态" min-width="100">
	      <template v-slot="{ row }">
	        <div
	          v-html="row.status_name.status_name"
	          class="pt5"
	          v-if="row.status_name"
	        ></div>
	        <div class="trip" v-if="!row.is_all_refund && row.refund.length">
	          部分退款中
	        </div>
	        <div
	          class="trip"
	          v-if="
	            row.is_all_refund && row.refund.length && row.refund_type != 6
	          "
	        >
	          退款中
	        </div>
	        <div class="pictrue-box" v-if="row.status_name">
	          <div
	            v-viewer
	            v-if="row.status_name.pics"
	            v-for="(item, index) in row.status_name.pics || []"
	            :key="index"
	          >
	            <img class="pictrue mr10" v-lazy="item" :src="item" />
	          </div>
	        </div>
	      </template>
	    </vxe-column>
	    <vxe-column field="action" title="操作" align="center" width="140" fixed="right">
	      <template v-slot="{ row }">
	        <a>
	          <a
	            @click="sendOrder(row)"
	            v-if="
	              (row._status === 2 || row._status === 8 || row.status === 4) &&
	              (row.pinkStatus === null || row.pinkStatus === 2) &&
	              row.delete_time == null
	            "
	            >发送货</a
	          >
	          <a
	            @click="bindWrite(row)"
	            v-if="
	              row.shipping_type == 2 &&
	              (row.status == 0 || row.status == 5) &&
	              row.paid == 1 &&
	              row.refund_status === 0 &&
	              row.delete_time == null
	            "
	            >立即核销</a
	          >
	          <Divider
	            type="vertical"
	            v-if="
	              ((row._status === 2 || row.split.length) &&
	                (row.pinkStatus === null || row.pinkStatus === 2) &&
	                row.delete_time == null) ||
	              (row.shipping_type == 2 &&
	                (row.status == 0 || row.status == 5) &&
	                row.paid == 1 &&
	                row.delete_time == null &&
	                row.refund_status === 0)
	            "
	          />
	          <a @click="changeMenu(row, '2')">订单详情</a>
	        </a>
	      </template>
	    </vxe-column>
	  </vxe-table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="orderData.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="orderData.limit"
        />
      </div>
    </Card>
    <orderWriteOff
      ref="writeOff"
      :orderNumId="orderNumId"
      @submitSuccess="submitSuccess"
    ></orderWriteOff>
    <!-- 用户详情-->
    <user-details ref="userDetails"></user-details>
    <!-- 编辑 配送信息表单数据 退款 退积分 不退款-->
    <edit-from
      ref="edits"
      :FromData="FromData"
      @submitFail="submitFail"
    ></edit-from>
    <!-- 详情 -->
    <details-from
      ref="detailss"
      :orderDatalist="orderDatalist"
      :orderId="orderId"
      :row-active="rowActive"
      :formType="1"
    ></details-from>
    <!-- 备注 -->
    <order-remark
        ref="remarks"
        :orderId="orderId"
        @submitFail="submitFail"
        :currentTab="currentTab"
    ></order-remark>
    <!-- 记录 -->
    <order-record ref="record"></order-record>
    <!-- 发送货 -->
    <order-send
      ref="send"
      :orderId="orderId"
      :status="status"
      :pay_type="pay_type"
      @submitFail="submitFail"
    >
    </order-send>
    <Modal v-model="modalCode" title="收款码" footer-hide>
      <div>
        <div v-viewer class="acea-row row-around code">
          <Spin fix v-if="spin"></Spin>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcode && qrcode.wechat" />
            </div>
            <span class="mt10">公众号收款码</span>
          </div>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcode && qrcode.routine" />
            </div>
            <span class="mt10">小程序收款码</span>
          </div>
        </div>
      </div>
    </Modal>
    <Modal v-model="refundModal" title="手动退款" width="960" class-name="refund-modal" @on-visible-change="visibleChange">
      <Form :label-width="100">
        <FormItem label="退款金额：" required>
          <InputNumber v-model="refundMoney" style="width: 408px;"></InputNumber>
        </FormItem>
        <FormItem v-if="this.refundProductNum > 1" label="分单退款：">
          <i-switch v-model="is_split_order" :true-value="1" :false-value="0" size="large">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </i-switch>
          <div class="tips">可选择表格中的商品单独退款，退款后且不能撤回，请谨慎操作！</div>
          <Table v-show="is_split_order" ref="refundTable" max-height="500" :columns="refundColumns" :data="refundProduct" @on-selection-change="refundSelectionChange">
            <template slot-scope="{ row }" slot="product">
              <div class="image-wrap" v-viewer><img :src="row.attrInfo.image" class="image"></div>
              <div class="title">{{ row.productInfo.store_name }}</div>
            </template>
            <template slot-scope="{ row }" slot="action">
              <InputNumber v-model="row.refundNum" :max="row.cart_num - row.refund_num" :min="1" :precision="0" controls-outside @on-change="refundNumChange(row)"></InputNumber>
            </template>
          </Table>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button @click="cancelRefundModal">取消</Button>
        <Button type="primary" @click="putOpenRefund">提交</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Setting from "@/setting";
import expandRow from './components/tableExpand.vue'
import userDetails from '@/pages/user/components/userDetails'
import editFrom from '@/components/from/from'
import orderSend from './components/orderSend'
import detailsFrom from './components/orderDetails'
import orderRecord from './components/orderRecord'
import orderWriteOff from './components/orderWriteOff'
import exportExcel from '@/utils/newToExcel.js'
import timeOptions from '@/utils/timeOptions'
import orderRemark from './components/orderRemark'
import {
  orderList,
  orderChart,
  orderHeader,
  getOrdeDatas, //编辑表单数据
  getDistribution,
  writeUpdate,
  getDataInfo,
  orderExport,
  cashierScan,
  getRefundFrom,
  refundRecharge,
  orderWriteForm,
  putOpenRefund
} from '@/api/order'
import { staffallInfo } from '@/api/staff'
export default {
  name: 'index',
  components: {
    userDetails,
    editFrom,
    detailsFrom,
    orderRecord,
    orderWriteOff,
    orderSend,
    expandRow,
    orderRemark
  },
  data() {
    return {
	  isCheckBox: false,
	  checkUidList: [],
	  isAll: 0,
      qrcode: {},
      spin: false,
      modalCode: false,
      delfromData: {},
      pay_type: '',
      status: 0, //发货状态判断
      FromData: null,
      orderDatalist: null,
      orderId: 0,
      orderNumId: '',
      options: timeOptions,
	  payList: [
	    { label: '微信支付', val: '1' },
	    { label: '支付宝支付', val: '4' },
	    { label: '余额支付', val: '2' },
	    { label: '线下支付', val: '3' },
	  ],
      statusList: [
        {
          // 订单状态

          value: '',
          label: '全部',
        },
        {
          value: '0',
          label: '未支付',
        },
        {
          value: '1',
          label: '待配送',
        },
        {
          value: '2',
          label: '配送中',
        },
        {
          value: '5',
          label: '待核销',
        },
        {
          value: '6',
          label: '已核销',
        },
        {
          value: '3',
          label: '待评价',
        },
        {
          value: '4',
          label: '已完成',
        },
        {
          value: '-4',
          label: '已删除',
        },
      ],
      staffData: [],
      tablists: {},
      orderChartType: {},
      timeVal: [],
      // 订单列表
      orderData: {
        page: 1,
        limit: 10,
        type: '',
        status: '',
        time: '',
        real_name: '',
        staff_id: '',
        order_type: 6,
      },
      currentTab: '6',
      tableList: [],
      total: 0,
      loading: false,
      columns: [
        {
          type: 'expand',
          width: 30,
          render: (h, params) => {
            return h(expandRow, {
              props: {
                row: params.row,
              },
            })
          },
        },
        {
          type: "selection",
          width: 60,
          align: "center"
        },
        {
          title: '订单号',
          slot: 'order_id',
          minWidth: 160,
        },
        {
          title: '用户信息',
          slot: 'userInfo',
          minWidth: 250,
        },
        {
          title: '商品信息',
          slot: 'info',
          minWidth: 350,
        },
        {
          title: '实际支付',
          slot: 'pay_price',
          minWidth: 120,
        },
        {
          title: '支付方式',
          key: 'pay_type_name',
          minWidth: 120,
        },
        {
          title: '收银店员',
          key: 'clerk_name',
          minWidth: 120,
        },
        {
          title: '下单时间',
          key: 'add_time',
          minWidth: 130,
        },
        {
          title: '订单状态',
          slot: 'statusName',
          minWidth: 120,
        },
        {
          title: '操作',
          slot: 'action',
          fixed: 'right',
          minWidth: 200,
          align: 'center',
        },
      ],
      rowActive: {},
      refundModal: false,
      refundColumns: [
        {
          type: 'selection',
          width: 60,
          align: 'center'
        },
        {
          title: '商品信息',
          width: 210,
          slot: 'product'
        },
        {
          title: '规格',
          render: (h, params) => {
            return h('div', params.row.attrInfo.suk);
          }
        },
        {
          title: '售价',
          render: (h, params) => {
            return h('div', params.row.attrInfo.price);
          }
        },
        {
          title: '优惠价',
          key: 'refundPrice'
        },
        {
          title: '总数',
          key: 'cart_num'
        },
        {
          title: '退款数量',
          slot: 'action',
          width: 160,
        }
      ],
      refundProduct: [],
      refundSelection: [],
      refundMoney: 0,
      is_split_order: 0,
    }
  },
  watch: {
    $route() {
      if (this.$route.fullPath === `${Setting.routePre}/order/index?order_type=7&status=1`) {
        this.getPath()
      }
    },
    refundSelection: {
      handler(value) {
        this.refundMoney = value.reduce((total, { refundPrice, refundNum }) => {
          return this.$computes.Add(total, this.$computes.Mul(refundPrice, refundNum));
        }, 0);
      },
      deep: true
    },
    is_split_order(value) {
      this.$nextTick(() => {
        this.$refs.refundTable.selectAll(!!value);
      });
    },
    refundMoney(value) {
      this.$nextTick(() => {
        if (typeof value != 'number') {
          return;
        }
        if (parseFloat(value) == parseInt(value)) {
          return;
        }
        if (value.toString().length - (value.toString().indexOf('.') + 1) > 2) {
          this.refundMoney = Number(value.toFixed(2));
        }
      });
    }
  },
  computed: {
    ...mapState('store/layout', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : 80
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right'
    },
    refundProductNum() {
      return this.refundProduct.reduce((total, value) => {
        return total + value.refundNum;
      }, 0);
    }
  },
  created() {
    this.getTabs()
    this.staffList()
    if (this.$route.fullPath === `${Setting.routePre}/order/index?order_type=7&status=1`) {
      this.getPath()
    } else {
      this.getList()
    }
  },
  mounted() {},
  methods: {
    visibleChange(visible) {
      this.is_split_order = 0;
      if (!visible) {
        this.refundSelection = [];
      }
    },
    cancelRefundModal() {
      this.refundModal = false;
    },
    putOpenRefund() {
      let data = {
        id: this.orderId,
        refund_price: this.refundMoney,
        type: 1,
        is_split_order: this.is_split_order
      };
      if (this.is_split_order) {
        if (!this.refundSelection.length) {
          return this.$Message.error('请选择需要退款的商品');
        }
        data.cart_ids = this.refundSelection.map(({ id, refundNum }) => ({
          cart_id: id,
          cart_num: refundNum
        }));
      }
      putOpenRefund(data).then(res => {
        this.$Message.success(res.msg);
        this.refundModal = false;
        this.getData(this.orderId);
      }).catch(err => {
        this.$Message.error(err.msg);
      });
    },
    refundSelectionChange(selection) {
      this.refundSelection = selection;
    },
    refundNumChange({ id, refundNum }) {
      let result = this.refundSelection.find(item => item.id === id);
      if (result) {
        result.refundNum = refundNum;
      }
    },
	allReset(){
		this.isAll = 0;
		this.isCheckBox = false;
		this.$refs.xTable.setAllCheckboxRow(false);
		this.checkUidList = [];
	},
	allPages(e){
	  this.isAll = e;
	  if(e==0){
	    this.$refs.xTable.toggleAllCheckboxRow();
	    // this.checkboxAll();
	  }else{
	    if(!this.isCheckBox){
	      this.$refs.xTable.setAllCheckboxRow(true);
	      this.isCheckBox = true;
	      this.isAll = 1;
	    }else{
	      this.$refs.xTable.setAllCheckboxRow(false);
	      this.isCheckBox = false;
	      this.isAll = 0;
	    }
	    this.checkUidList = []
	  }
	},
	checkboxItem(e){
	  let id = parseInt(e.rowid);
	  let index = this.checkUidList.indexOf(id);
	  if(index !== -1){
	    this.checkUidList = this.checkUidList.filter((item)=> item !== id);
	  }else{
	    this.checkUidList.push(id);
	  }
	},
	checkboxAll(){
	  // 获取选中当前值
	  let obj2 = this.$refs.xTable.getCheckboxRecords(true);
	  // 获取之前选中值
	  let obj = this.$refs.xTable.getCheckboxReserveRecords(true);
	  if(this.isAll == 0 && this.checkUidList.length <= obj.length && !this.isCheckBox){
	  	 obj = [];
	  }
	  obj = obj.concat(obj2);
	  let ids = [];
	  obj.forEach((item)=>{
	    ids.push(parseInt(item.id))
	  })
	  this.checkUidList = ids;
	  if(!obj2.length){
	    this.isCheckBox = false;
	  }
	},
    printOreder(){
	  if (this.checkUidList.length > 10 || (this.isAll==1 && this.total>10)) {
	    return this.$Message.error('最多批量打印10个订单')
	  }
	  let ids = []
	  if (this.isAll==1 && this.total<=10){
	    this.tableList.forEach(item=>{
	      ids.push(parseInt(item.id))
	    })
	  }
      let pathInfo = this.$router.resolve({
        path:`${Setting.routePre}/order/distribution`,
        query:{
		  id: this.isAll==1?ids.join(','):this.checkUidList.join(','),
        }
      });
      window.open(pathInfo.href, '_blank');
    },
    //跳转刷新
    getPath() {
      this.orderData.page = 1
      this.currentTab = this.$route.query.order_type.toString()
      this.orderData.order_type = this.currentTab
      this.orderData.status = this.$route.query.status
      this.getList()
    },
    // 收银台跳移动端支付列表二维码
    qrcodeShow() {
      this.spin = true
      this.modalCode = true
      cashierScan()
        .then((res) => {
          this.spin = false
          this.qrcode = res.data
        })
        .catch((err) => {
          this.spin = false
          this.$Message.error(err.msg)
        })
    },
    // 查看子订单
    splitOrderDetail(row) {
      this.$router.push({
        path: 'split_list',
        query: {
          id: row.id,
          orderChartType: this.orderData.status,
        },
      })
    },
    // 操作更多
    changeMenu(row, name) {
      this.orderId = row.id
      switch (name) {
        case '1':
          this.delfromData = {
            title: '修改立即支付',
            url: `/order/pay_offline/${row.id}`,
            method: 'post',
            ids: '',
          }
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg)
              this.getChart()
              this.getData(row.id, 1)
              this.getList()
            })
            .catch((res) => {
              this.$Message.error(res.msg)
            })
          break
        case '2':
          this.rowActive = row
          this.getData(row.id)
          break
        case '3':
          this.$refs.record.modals = true
          if (this.currentTab == 4) {
            this.$refs.record.getVip(row.id)
          } else {
            this.$refs.record.getList(row.id)
          }
          break
        case '4':
          this.$refs.remarks.modals = true
          if (this.currentTab == 3) {
            this.$refs.remarks.getRemark(row.id)
          }
          if (this.currentTab == 4) {
            this.$refs.remarks.getVipRemark(row.id)
          }
          this.$refs.remarks.formValidate.remark = row.remark
          break
        case '5':
          this.getOnlyRefundData(row.id, row.refund_type)
          break
        case '55':
          this.getRefundData(row.id, row.refund_type)
          break
        // case "6":
        // 	this.getRefundIntegral(row.id);
        // 	break;
        // case "7":
        // 	this.getNoRefundData(row.id);
        // 	break;
        case '8':
          this.delfromData = {
            title: '修改确认收货',
            url: `/order/take/${row.id}`,
            method: 'put',
            ids: '',
          }
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg)
              this.getList()
              this.getData(row.id, 1)
            })
            .catch((res) => {
              this.$Message.error(res.msg)
            })
          break
        case '10':
          this.delfromData = {
            title: '立即打印订单',
            info: '您确认打印此订单吗?',
            url: `/order/print/${row.id}`,
            method: 'get',
            ids: '',
          }
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg)
              this.getList()
            })
            .catch((res) => {
              this.$Message.error(res.msg)
            })
          break
        case '11':
          this.delfromData = {
            title: '立即打印电子面单',
            info: '您确认打印此电子面单吗?',
            url: `/order/order_dump/${row.id}`,
            method: 'get',
            ids: '',
          }
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg)
              this.getList()
            })
            .catch((res) => {
              this.$Message.error(res.msg)
            })
          break
        case "12":
          let pathInfo = this.$router.resolve({
            path:`${Setting.routePre}/order/distribution`,
            query:{
              id:row.id
            }
          });
          window.open(pathInfo.href, '_blank');
          break;
        default:
          this.delfromData = {
            title: '删除订单',
            url: `/order/del/${row.id}`,
            method: 'DELETE',
            ids: '',
          }
          // this.modalTitleSs = '删除订单';
          this.delOrder(row, this.delfromData)
      }
    },
    // 仅退款
    getOnlyRefundData(id, refund_type) {
      // this.$modalForm(getRefundFrom(id)).then(() => {
      //   this.getChart()
      //   this.getList()
      //   this.$refs.detailss.modals = false
      // })
      let _info = this.rowActive._info;
      let cart_info = Object.keys(_info).map((key) => {
        return _info[key].cart_info;
      });
      cart_info.forEach((value) => {
        value.refundPrice = this.$computes.Div(value.refund_price, value.cart_num);
        value.refundNum = value.cart_num - value.refund_num;
        value._disabled = !value.refundNum;
      });
      this.refundProduct = cart_info;
      if (this.refundProductNum === 1) {
        this.refundSelection = cart_info;
      }
      this.refundModal = true;
    },
    // 获取退款表单数据
    getRefundData(id, refund_type) {
      let orderChartType = this.orderData.status
      this.delfromData = {
        title: '是否立即退货',
        url: `/refund/agree/${id}`,
        method: 'get',
      }
      this.$modalSure(this.delfromData)
        .then((res) => {
          this.$Message.success(res.msg)
          this.getChart()
          this.getList()
        })
        .catch((res) => {
          this.$Message.error(res.msg)
        })
    },
    // 删除单条订单
    delOrder(row, data) {
      if (row.is_del === 1) {
        this.$modalSure(data)
          .then((res) => {
            this.$Message.success(res.msg)
            this.getList()
            this.$refs.detailss.modals = false
          })
          .catch((res) => {
            this.$Message.error(res.msg)
          })
      } else {
        const title = '错误！'
        const content =
          '<p>您选择的的订单存在用户未删除的订单，无法删除用户未删除的订单！</p>'
        this.$Modal.error({
          title: title,
          content: content,
        })
      }
    },
    // 获取详情表单数据
    getData(id, type) {
      getDataInfo(id)
        .then(async (res) => {
          if (!type) {
            this.$refs.detailss.modals = true
          }
          this.$refs.detailss.activeName = 'detail'
          this.orderDatalist = res.data
          if (this.orderDatalist.orderInfo.refund_reason_wap_img) {
            try {
              this.orderDatalist.orderInfo.refund_reason_wap_img = JSON.parse(
                this.orderDatalist.orderInfo.refund_reason_wap_img
              )
            } catch (e) {
              this.orderDatalist.orderInfo.refund_reason_wap_img = []
            }
          }
        })
        .catch((res) => {
          this.$Message.error(res.msg)
        })
    },
    submitSuccess() {
      this.getList()
      this.getChart()
      if (this.$refs.detailss.modals) {
        this.getData(this.orderId, 1)
      }
    },
    // 单个核销
    singleWrite(row) {
      let self = this
      this.$Modal.confirm({
        title: '提示',
        content: '确定要核销该订单吗？',
        cancelText: '取消',
        closable: true,
        maskClosable: true,
        onOk: function () {
          writeUpdate(row.order_id)
            .then((res) => {
              self.$Message.success(res.msg)
              self.getList()
              self.getChart()
              if (self.$refs.detailss.modals) {
                self.getData(self.orderId, 1)
              }
            })
            .catch((err) => {
              self.$Message.error(err.msg)
            })
        },
        onCancel: () => {},
      })
    },
    // 立即核销
    bindWrite(row) {
      if (row.total_num > 1) {
        this.orderNumId = row.order_id
        this.$refs.writeOff.modals = true
        this.$refs.writeOff.getWriteOff({ oid: row.id })
      } else {
        if(row.product_type == 4){
          this.$modalForm(orderWriteForm(row.id)).then((res) => {
            this.$Message.success(res.msg)
            this.getList()
            this.getChart()
            if (this.$refs.detailss.modals) {
              this.getData(this.orderId, 1)
            }
          });
        }else{
          this.singleWrite(row)
        }
      }
    },
    // 配送信息表单数据
    delivery(row) {
      getDistribution(row.id)
        .then(async (res) => {
          this.FromData = res.data
          this.$refs.edits.modals = true
          this.getData(this.orderId, 1)
        })
        .catch((res) => {
          this.$Message.error(res.msg)
        })
    },
    // 编辑
    edit(row) {
      this.getOrderData(row.id)
    },
    // 获取编辑表单数据
    getOrderData(id) {
      getOrdeDatas(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data)
          }
          this.$authLapse(res.data)
          this.FromData = res.data
          this.$refs.edits.modals = true
        })
        .catch((res) => {
          this.$Message.error(res.msg)
        })
    },
    // 修改成功(编辑只有未支付时出现)
    submitFail() {
      this.status = 0
      this.getList()
      this.getData(this.orderId, 1)
    },
    // 发送货
    sendOrder(row) {
      this.$store.commit('store/order/setSplitOrder', row.total_num)
      this.$refs.send.modals = true
      this.orderId = row.id
      this.status = row._status
      this.pay_type = row.pay_type
      this.$refs.send.getList()
      this.$refs.send.getDeliveryList()
      this.$nextTick((e) => {
        this.$refs.send.getCartInfo(row._status, row.id)
      })
    },
    // 详情
    showUserInfo(row) {
      this.$refs.userDetails.modals = true
      this.$refs.userDetails.getDetails(row.uid)
    },
    // 初始化数据
    clearData() {
      this.orderData = {
        page: 1,
        limit: 10,
        type: '',
        status: '',
        time: '',
        real_name: '',
        order_type: 6,
      }
      this.rechargeData = {
        nickname: '',
        limit: 15,
      }
      this.vipData = {
        member_type: '',
        limit: 15,
      }
    },
    // 店员列表
    staffList() {
      let data = {
        page: 0,
        limit: 0,
      }
      staffallInfo()
        .then((res) => {
          this.staffData = res.data
        })
        .catch((err) => {
          this.$Message.error(err.msg)
        })
    },
    // 获取门店订单头部统计
    getTabs() {
      orderHeader()
        .then((res) => {
          this.tablists = res.data
        })
        .catch((err) => {
          this.$Message.error(err.msg)
        })
    },
    // 订单头部数据
    getChart() {
      orderChart({
        type: this.currentTab,
      })
        .then((res) => {
          this.orderChartType = res.data
        })
        .catch((err) => {
          this.$Message.error(err.msg)
        })
    },
    //点击切换头部
    onClickTab() {
      this.clearData()
      this.orderData.order_type = this.currentTab
      this.orderData.page = 1
	  this.allReset();
      this.timeVal = []
      this.getChart()
      this.getList()
      this.getTabs()
    },
    // 搜索
    userSearchs() {
	  this.allReset();
      this.orderData.page = 1
      this.getList()
    },
    // 一般订单
    getList() {
      this.getChart()
      this.loading = true
      orderList(this.orderData)
        .then((res) => {
          let data = res.data
          data.data.forEach((item) => {
            if (item.id == this.orderId) {
              this.rowActive = item
            }
          })
          this.tableList = data.data
          this.total = data.count
          this.loading = false
          if (this.currentTab == 5) {
            this.tablists.writeoff = data.count;
          } else if (this.currentTab == 6) {
            this.tablists.cashier = data.count;
          } else if (this.currentTab == 7) {
            this.tablists.delivery = data.count;
          }
		  this.$nextTick(function(){
		    if (this.isAll == 1) {
		      if(this.isCheckBox){
		        this.$refs.xTable.setAllCheckboxRow(true);
		      }else{
		        this.$refs.xTable.setAllCheckboxRow(false);
		      }
		    }else{
			  let obj = this.$refs.xTable.getCheckboxReserveRecords(true);
			  if(!this.checkUidList.length || this.checkUidList.length <= obj.length){
			    this.$refs.xTable.setAllCheckboxRow(false);
			  }
		    }
		  })
        })
        .catch((err) => {
          this.loading = false
          this.$Message.error(err.msg)
        })
    },
    pageChange(index) {
      this.orderData.page = index
      this.getList()
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e
      this.orderData.time = this.timeVal.join('-')
      this.orderData.page = 1
      if (!e[0]) {
        this.orderData.time = ''
      }
	  this.allReset();
      this.getList()
    },
    // 选择时间
    selectChange() {
      this.orderData.page = 1
      this.timeVal = []
      this.getList()
    },
    // 数据导出；
    async exports() {
      let orderData = {
        orderType: this.currentTab,
        staff_id: this.orderData.staff_id,
		status: this.orderData.status,
		real_name: this.orderData.real_name,
		time: this.orderData.time,
		ids: this.checkUidList.join(",")
      }
      let [th, filekey, data, fileName] = [[], [], [], '']
      // let fileName = "";
      let excelData = JSON.parse(JSON.stringify(orderData))
      excelData.page = 1
      for (let i = 0; i < excelData.page + 1; i++) {
        let lebData = await this.getExcelData(excelData)
        if (!fileName) fileName = lebData.filename
        if (!filekey.length) {
          filekey = lebData.filekey
        }
        if (!th.length) th = lebData.header
        if (lebData.export.length) {
          data = data.concat(lebData.export)
          excelData.page++
        } else {
          exportExcel(th, filekey, fileName, data)
          return
        }
      }
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        orderExport(excelData, 1).then((res) => {
          return resolve(res.data)
        })
      })
    },
  },
}
</script>

<style lang="stylus" scoped>
.tdinfo{
  margin-left: 75px;
  margin-top: 16px;
}
.expand-row{
  margin-bottom: 16px;
  font-size: 12px;
}
.QRpic {
  width: 180px;
  height: 259px;

  img {
    width: 100%;
    height: 100%;
  }
}
.cardCon {
  /deep/.ivu-card-body {
    padding: 14px 20px 0 20px !important;
  }

  /deep/.ivu-tabs-bar {
    margin-bottom: 0px !important;
    border-bottom: 1px solid #E9E9E9 !important;
  }
}

/deep/.ivu-tabs-nav {
  height: 45px;
}

.pictrue-box {
  display: flex;
  align-item: center;
}
.search {
width: 86px;
  height: 32px;
  }

.pictrue {
  width: 25px;
  height: 25px;
}

img {
  height: 36px;
  display: block;
}

.tabBox {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;

  .tabBox_img {
    width: 36px;
    height: 36px;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .tabBox_tit {
    max-width: 60%;
    font-size: 12px !important;
    margin: 0 2px 0 10px;
    letter-spacing: 1px;
    padding: 5px 0;
    box-sizing: border-box;
  }
}

.trip {
  color: orange;
}

>>>.refund-modal {
  .ivu-input-number-controls-outside {
    width: 105px;
    height: 28px;
    border: 0;
    border-radius: 0;
    background-color: transparent;
    font-size: 16px;
    line-height: 28px;
    box-shadow: none;
  }

  .ivu-input-number-controls-outside:focus {
    box-shadow: none;
  }

  .ivu-input-number-controls-outside-btn {
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 50%;
    background-color: #1890FF;
    line-height: 28px;
    color: #FFFFFF;
  }

  .ivu-input-number-input-wrap {
    height: 28px;
  }

  .ivu-input-number-controls-outside .ivu-input-number-input {
    height: 28px;
    background-color: transparent;
    text-align: center;
    line-height: 28px;
  }

  .ivu-input-number-controls-outside-btn i {
    font-weight: bold;
  }

  .ivu-input-number-controls-outside-btn:hover i {
    color: inherit;
  }

  .ivu-input-number-controls-outside-btn-disabled, .ivu-input-number-controls-outside-btn-disabled:hover {
    background-color: #F5F5F5;
  }

  .ivu-input-number-controls-outside-btn-disabled i, .ivu-input-number-controls-outside-btn-disabled:hover i {
    color: rgba(0, 0, 0, 0.85);
  }

  .tips {
    padding: 12px 0 23px;
    font-size: 12px;
    line-height: 14px;
    color: #999999;
  }

  .ivu-modal-footer {
    padding-bottom: 30px;
    border: 0;
    text-align: center;
  }

  .ivu-modal-footer button + button {
    margin-left: 20px;
  }

  .ivu-btn {
    height: 46px;
    padding: 0 71px;
    border-color: #F5F5F5;
    border-radius: 23px;
    background-color: #F5F5F5;
    font-size: 16px !important;
    color: #666666;
  }

  .ivu-btn:focus {
    box-shadow: none;
  }

  .ivu-btn-primary {
    border-color: #1890FF;
    background-color: #1890FF;
    color: #FFFFFF;
  }

  .ivu-form .ivu-form-item-label {
    font-size: 13px !important;
  }

  .ivu-table {
    font-size: 14px !important;
    line-height: 20px;
  }

  .image-wrap {
    float: left;
  }

  .image {
    width: 46px;
    height: 46px;
  }

  .title {
    margin-left: 52px;
  }
}
</style>
