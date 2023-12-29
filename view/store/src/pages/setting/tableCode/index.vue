<template>
  <div>
    <Card :bordered="false" dis-hover>
      <Row>
        <Col span="20">
          <Form ref="form" :model="formData" :label-width="110">
            <FormItem label="桌码开关：">
              <i-switch
                v-model="formData.store_code_switch"
                true-value="1"
                false-value="0"
                size="large"
              >
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
            </FormItem>
            <FormItem label="结账方式：">
              <RadioGroup v-model="formData.store_checkout_method">
                <Radio label="1" class="mr20">合并结账</Radio>
                <Radio label="2">单独结账</Radio>
              </RadioGroup>
              <div style="font-size: 12px;color: #999999;">多个人扫同一桌码，合并结账是指多人中的其中一个人结账即可，购物车共用一个，适用于好友聚餐；单独结账是指每个人单独进行结账，购物车都是独立的，适用于快餐店每个人单独点餐</div>
            </FormItem>
            <FormItem
              v-show="formData.store_checkout_method == 1"
              label="用餐人数弹窗："
            >
              <i-switch
                v-model="formData.store_number_diners_window"
                true-value="1"
                false-value="0"
                size="large"
              >
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
            </FormItem>
            <FormItem label="餐桌座位：">
              <Table class="mb15" :columns="columns" :data="tableSeatsList">
                <template slot-scope="{ row }" slot="action">
                  <a @click="onEdit(row)">编辑</a>
                  <Divider type="vertical" />
                  <a @click="onDelete(row.id)">删除</a>
                </template>
              </Table>
              <Button type="primary" @click="modalToggle">添加</Button>
            </FormItem>
          </Form>
        </Col>
      </Row>
      <div class="footer acea-row row-center-wrapper">
        <Button type="primary" class="ml20" @click="handleSubmit('form')">提交</Button>
      </div>
    </Card>
    <Modal
      v-model="modal"
      :mask-closable="false"
      title="餐桌座位"
      width="540"
      class-name="add-modal-wrap"
      footer-hide
    >
      <Form :label-width="107">
        <FormItem label="餐桌座位数：">
          <InputNumber v-model="number" :min="1"></InputNumber>
        </FormItem>
      </Form>
      <div class="footer">
        <Button class="mr10" @click="modalToggle">取消</Button>
        <Button type="primary" @click="addSeats">确认</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import { getTableSeatsList, getConfig, submitConfig, addSeats } from '@/api/setting';

export default {
  data() {
    return {
      columns: [
        {
          title: '餐桌座位数',
          key: 'number',
        },
        {
          title: '创建时间',
          key: 'add_time',
        },
        {
          title: '操作',
          slot: 'action',
          align: 'center',
        },
      ],
      tableSeatsList: [],
      modal: false,
      formData: {
        store_code_switch: '1',
        store_checkout_method: '1',
        store_number_diners_window: '1',
      },
      id: 0,
      number: 1,
    };
  },
  created() {
    this.getConfig();
    this.getTableSeatsList();
  },
  methods: {
    getConfig() {
      getConfig('store_table_code').then((res) => {
        if (res.data.constructor.name === 'Array') {
          return;
        }
        this.formData = res.data;
      });
    },
    // 获取餐桌座位列表
    getTableSeatsList() {
      getTableSeatsList().then((res) => {
        this.tableSeatsList = res.data;
      });
    },
    // 编辑
    onEdit(row) {
      this.modal = true;
      this.id = row.id;
      this.number = row.number;
    },
    // 删除
    onDelete(id) {
      this.$modalSure({
        title: '删除餐桌座位数',
        url: `/table/del/seats/${id}`,
        method: 'delete',
        ids: '',
      }).then((res) => {
        this.$Message.success(res.msg);
        this.getTableSeatsList();
      });
    },
    // 取消、添加分类
    modalToggle() {
      this.id = 0;
      this.number = 1;
      this.modal = !this.modal;
    },
    // 提交桌码配置
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          submitConfig('store_table_code', this.formData).then((res) => {
            this.$Message.success(res.msg);
          });
        }
      });
    },
    // 提交餐桌座位数
    addSeats() {
      addSeats(this.id, { number: this.number }).then((res) => {
        this.$Message.success(res.msg);
        this.modal = false;
        this.getTableSeatsList();
      });
    },
  },
};
</script>

<style lang="less" scoped>
/deep/.ivu-modal-content {
  border-radius: 10px;
}
/deep/.ivu-modal-header {
  border-radius: 10px 10px 0 0;
  background-color: #ffffff;
}
/deep/.ivu-modal-body {
  padding: 28px 31px 31px 0;
}
/deep/.ivu-input-number {
  width: 100%;
}
.add-modal-wrap {
  .footer {
    height: auto;
    margin-top: 0;
    box-shadow: none;
    text-align: right;
  }
}
.footer{
  width: 100%;
  height: 50px;
  box-shadow: 0px -2px 4px 0px rgba(0, 0, 0, 0.05);
  margin-top: 50px;
}
/deep/.ivu-card-body {
  padding-bottom: 0;
}
</style>