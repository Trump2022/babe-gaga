<template>
  <div>
    <div v-show="currentPage === 1" class="page1">
      <div class="title">订单核销</div>
      <div>
        <Input search autofocus enter-button="查询" placeholder="输入/扫描核销码进行核销" @on-search="onSearch" />
      </div>
      <div class="btn">
        <Button type="text" @click="currentPage = 2">查看核销订单</Button>
      </div>
    </div>
    <div v-show="currentPage === 2" class="order">
      <div class="left">
        <div class="left-top">
          <div class="title"> 
            <Button type="text" icon="ios-arrow-back" @click="currentPage = 1">返回</Button>
            <span class="line">丨</span>
            核销订单
          </div>
          <div class="sx" @click="filterModal = !filterModal">
            {{ filterModal ? "关闭" : "筛选" }}
            <Icon
              v-if="!filterModal"
              class="ios-funnel-outline"
              color="#666"
              type="ios-funnel-outline"
            />
          </div>
        </div>
        <div class="order-box">
          <div v-show="!filterModal" class="search">
            <Input
              v-model="orderData.keyword"
              enter-button="搜索"
              placeholder="搜索订单编号"
              search
              size="large"
              @on-search="search"
            />
          </div>
          <orderList
            v-if="orderListData.length"
            v-show="!filterModal"
            :orderData="orderListData"
            :total="count"
            class="order-list"
            orderType="verify"
            @addPage="addPage"
            @selectOrder="selectOrder"
          ></orderList>
          <div v-else-if="!orderListData.length && !filterModal" class="no-order">
            <img alt="" src="../../assets/images/no-order.png" />
            <span class="trip">噢～目前暂无订单</span>
          </div>
          <filter-modal
            v-show="filterModal"
            order-type="verify"
            @search="searchList"
          ></filter-modal>
        </div>
      </div>
      <div class="order-data">
        <div class="header">
          <div
            v-for="(tab, index) in tabs"
            :key="index"
            :class="
              sle === index
                ? 'sel'
                : index === sle - 1
                ? 'neighbor-left'
                : index === sle + 1
                ? 'neighbor-right'
                : 'def'
            "
            class="item"
            @click="tabClick(index)"
          >
            <div class="item-wrap">
              {{ tab }}
            </div>
          </div>
          <div :class="sle === 2 ? 'neighbor-right' : ''" class="box"></div>
        </div>
        <div class="content" :class="{ 'border-radius': sle }">
          <userOrder
            v-if="sle === 0 && selectOrderData.order_id"
            :orderNumId="selectOrderData.order_id"
            :selectData="selectOrderData"
            class="orders"
            @remarks="remarks"
            @selectData="selectData"
          ></userOrder>
          <div v-else-if="!selectOrderData.order_id" class="no-order">
            <img alt="" src="../../assets/images/no-record.png" />
            <span class="trip">噢～目前暂无订单</span>
          </div>
          <orderDetails
            v-if="sle === 1 && selectOrderData.order_id"
            :id="selectOrderData.id"
            class="orders"
          ></orderDetails>
          <orderRecord
            v-if="sle === 2 && selectOrderData.order_id"
            :id="selectOrderData.id"
          ></orderRecord>
        </div>
        <div v-if="selectOrderData.order_id" class="footer">
          <div class="footer-left">
            <span class="clerk" v-if="selectOrderData.clerk_name"
              >收银员：{{ selectOrderData.clerk_name }}</span
            >
            <span class="pay">实付：</span>
            <span class="num">¥{{ selectOrderData.pay_price || 0 }}</span>
          </div>
          <div class="footer-right">
            <div class="btn" @click="remarks">订单备注</div>
            <div class="btn" @click="point">小票打印</div>
            <!--        <div class="btn pay" @click="getVerifyData">立即核销</div>-->
            <div
              v-if="
                (!selectOrderData.status || selectOrderData.status === 5) &&
                  selectOrderData.refund_type !== 6 &&
                  selectOrderData.paid === 1
              "
              class="btn pay"
              @click="getVerifyData"
            >
              立即核销
            </div>
          </div>
        </div>
      </div>
      <!-- 备注 -->
      <order-remark
        ref="remarks"
        :orderId="selectOrderData.id"
        @submitFail="submitFail"
      ></order-remark>
      <orderWriteOff
        ref="writeOff"
        :orderNumId="selectOrderData.order_id"
      ></orderWriteOff>
    </div>
  </div>
</template>

<script>
import orderList from "@/components/orderList";
import goodsList from "@/pages/hang/components/goodsList";
import userOrder from "./components/userOrder";
import orderDetails from "./components/orderDetails";
import orderRecord from "@/components/orderRecord";
import orderRemark from "@/components/orderRemark";
import orderWriteOff from "./components/orderWriteOff";
import filterModal from "@/components/filterModal";

import { getVerifyList, putWriteUpdate, orderWriteForm } from "@/api/order";

export default {
  components: {
    orderList,
    goodsList,
    userOrder,
    orderDetails,
    orderRemark,
    orderRecord,
    orderWriteOff,
    filterModal,
  },
  data() {
    return {
      orderId: 0,
      orderListData: [],
      tabs: ["商品信息", "订单详情", "订单记录"],
      sle: 0,
      filterModal: false,
      userFrom: {
        keyword: "",
        page: 1,
        limit: 9,
      },
      dataList: [],
      orderData: {
        type: "",
        status: "",
        time: "",
        staff_id: "",
        real_name: "",
        page: 1,
        limit: 10,
      },
      selectOrderData: {},
      orderInfoData: {},
      count: 0,
      orderStatusList: [],
      selectOrderDatas: [],
      currentPage: 1,
    };
  },
  created() {
    this.getVerifyList();
  },
  methods: {
    addPage() {
      if (this.orderListData.length < this.count) this.orderData.page++;
      this.getVerifyList();
    },
    search() {
      this.orderListData = [];
      this.selectOrderData = {};
      this.orderData.page = 1;
      this.sle = 0;
      this.getVerifyList();
    },
    //搜索
    searchList(data) {
      this.filterModal = false;
      this.orderData.page = 1;
      this.orderData = { ...this.orderData, ...data };
      this.orderListData = [];
      this.search();
    },
    // 设置备注
    remarks() {
      this.$refs.remarks.modals = true;
      this.$refs.remarks.formValidate.remark = this.selectOrderData.remark;
    },
    // 备注修改成功
    submitFail() {},
    point() {
      this.delfromData = {
        title: "立即打印订单",
        info: "您确认打印此订单吗?",
        url: `/order/print/${this.selectOrderData.id}`,
        method: "get",
        ids: "",
      };
      this.$modalSure(this.delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    selectData(data) {
      this.selectOrderDatas = data;
    },
    // 立即核销
    getVerifyData() {
      if (!this.selectOrderDatas.length) {
        return this.$Message.error("请选择要核销的商品");
      }
      if(this.selectOrderData.product_type == 4){
        this.$modalForm(orderWriteForm(this.selectOrderData.id, { cart_num: this.selectOrderDatas[0].write_surplus_times })).then((res) => {
          this.$Message.success(res.msg);
          this.orderListData = [];
          this.getVerifyList();
          this.selectOrderData.status = 2;
        });
      }else{
        let cartIds = [];
        this.selectOrderDatas.forEach((v) => {
          cartIds.push({
            cart_id: v.cart_id,
            cart_num: v.write_surplus_times,
          });
        });
        putWriteUpdate(this.selectOrderData.id, { cart_ids: cartIds })
            .then((res) => {
              this.$Message.success(res.msg);
              // this.search();
              this.orderListData = [];
              this.getVerifyList();
              this.selectOrderData.status = 2;
            })
            .catch((err) => {
              this.$Message.error(err.msg);
            });
      }
    },
    selectOrder(data) {
      this.selectOrderData = data;
    },
    tabClick(index) {
      this.sle = index;
      switch (index) {
        case 1:
          break;
      }
    },

    // 核销列表
    getVerifyList() {
      getVerifyList(this.orderData)
        .then((res) => {
          res.data.data = res.data.data.map((item) => {
            let infoArr = [];
            for (let key in item._info) {
              let obj = item._info[key];
              infoArr.push(obj);
            }
            this.$set(item, "_infoData", infoArr);
            return item;
          });

          this.selectOrderData =
            res.data.data.length && this.orderData.page == 1
              ? res.data.data[0]
              : this.selectOrderData;
          this.orderListData = this.orderListData.concat(res.data.data);
          this.count = res.data.count;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    onSearch(value) {
      value = value.trim();
      if (value) {
        this.currentPage = 2;
        this.orderData.keyword = value;
        this.search();
      }
    }
  },
};
</script>
<style lang="stylus" scoped>
::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #ccc;
}

::-webkit-scrollbar {
  width: 0px !important;
  /* 对垂直流动条有效 */
}

.order {
  display: flex;
  height: calc(100vh - 66px);
  padding: 20px;
  background: #F5F5F5;

  .left {
    display: flex;
    flex-direction: column;
    width: 460px;
    border-radius: 20px;
    background: #FFFFFF;

    .content {
      height: 100%;
    }

    .left-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 24px;

      .ivu-btn {
        padding: 0;
        font-size: 16px !important;
        color: #606266;
      }

      .title {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 20px;
        color: #303133;

        .line {
          margin: 0 7px;
          font-weight: 400;
          font-size: 16px;
          color: #606266;
        }
      }

      .sx {
        color: #666666;
        cursor: pointer;
        font-size: 14px;

        .ios-funnel-outline {
          font-weight: bold;
          font-size: 12px;
        }
      }
    }

    .order-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 0;
      font-size: 18px;

      .search {
        padding: 0 24px 24px;

        /deep/.ivu-input {
          padding-left: 14px;
          border-color: #DDDDDD;
          border-radius: 20px 0 0 20px;

          &:focus {
            border-color: #1890FF;
          }
        }

        /deep/.ivu-input-search {
          border-radius: 0px 20px 20px 0px;
          background: #1890FF !important;
          font-size: 14px;
          line-height: normal;
        }
      }

      .order-list {
        flex: 1;
      }
    }
  }

  .order-data {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-left: 20px;

    .content {
      flex: 1;
      padding: 24px;
      border-radius: 0 20px 0 0;
      background: #FFFFFF;
      overflow-x: hidden;
    }

    .border-radius {
      border-radius: 20px 20px 0 0;
    }

    .header {
      display: flex;
      background: #FFFFFF;
      font-size: 18px;

      .box {
        flex: 1;
        background: #F5F5F5;
      }

      .item {
        cursor: pointer;
        background-color: #F5F5F5;
        transition: all 0.1s;
      }

      .item-wrap {
        padding: 16px 29px;
        border-radius: 20px 20px 0px 0px;
      }

      .sel {
        color: rgba(0, 0, 0, 0.85);
        font-weight: 500;

        .item-wrap {
          background: #FFFFFF;
        }
      }

      .neighbor-left {
        border-bottom-right-radius: 20px;
      }

      .neighbor-right {
        border-bottom-left-radius: 20px;
      }
    }

    .orders {
      flex: 1;
      display: flex;
      flex-direction: column;
      max-height: calc(100% - 53px);
    }
  }
}

.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 22px 24px;
  border-radius: 0 0 20px 20px;
  background: #FFFFFF;
  box-shadow: 0 -1px 11px 0 rgba(0,0,0,0.06);

  .footer-left {
    font-size: 15px;

    .clerk {
      color: #000;
      padding-right: 40px;
      font-weight: bold;
    }

    .pay {
      color: #333333;
    }

    .num {
      font-size: 20px;
      color: #F5222D;
      font-weight: bold;
    }
  }

  .footer-right {
    display: flex;

    .btn {
      display flex;
      justify-content: center;
      align-items: center;
      width: 120px;
      height: 44px;
      border-radius: 22px;
      background: #F2F3F5;
      cursor: pointer;
      font-size: 18px;
      color: rgba(0,0,0,0.85);

      +.btn {
        margin-left: 15px;
      }
    }

    .pay {
      color: #FFFFFF;
      background: #FF7700;
    }
  }
}
/deep/.page1  {
  padding-top: 282px;

  .title {
    text-align: center;
    font-weight: 500;
    font-size: 24px;
    color: #303133;
  }

  .ivu-input-wrapper {
    width: 640px;
    margin: 32px auto 35px;
  }

  .ivu-input {
    height: 50px;
    padding-left: 20px;
    border-color: #1890FF;
    border-radius: 25px 0 0 25px;
    font-size: 16px !important;
  }

  .ivu-input-search {
    padding: 0 39px !important;
    border-color: #1890FF !important;
    border-radius: 0 25px 25px 0;
    background: #1890FF !important;
    font-size: 16px;
  }

  .btn {
    text-align: center;
  }

  .ivu-btn {
    font-size: 15px !important;
    color: #1890FF;

    &:focus {
      box-shadow: none;
    }
  }
}
</style>
