<template>
  <div>
    <Card :bordered="false" dis-hover>
      <div class="mb15">
        <Button class="mr10" type="primary" @click="modalToggle"
          >添加桌码</Button
        >
        <Button
          :disabled="!QRCodeSelected.length"
          type="primary"
          @click="onBatchDownload"
          >批量下载</Button
        >
      </div>
      <Table
        :columns="columns"
        :data="tableQRCodeList"
        :loading="loading"
        @on-selection-change="onSelectionChange"
      >
        <template slot-scope="{ row }" slot="qrcode">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.qrcode" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="is_using">
          <i-switch v-model="row.is_using" :true-value="1" :false-value="0" size="large" @on-change="isUsingChange(row.id, $event)">
            <span slot="open">启用</span>
            <span slot="close">停用</span>
          </i-switch>
        </template>
        <template slot-scope="{ row }" slot="category">
          <div>{{ row.category.name }}</div>
        </template>
        <template slot-scope="{ row }" slot="action">
          <a @click="onEdit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click="onDownload(row)">下载</a>
          <Divider type="vertical" />
          <a @click="onDelete(row.id)">删除</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="page"
          :page-size="limit"
          show-total
          @on-change="onChange"
        />
      </div>
    </Card>
    <Modal
      v-model="modal"
      :mask-closable="false"
      :title="`${id ? '编辑' : '新增'}桌号`"
      width="540"
      class-name="add-modal-wrap"
      footer-hide
    >
      <Form :model="formValidate" :label-width="96">
        <FormItem label="桌台类型：">
          <Select v-model="formValidate.cate_id">
            <Option
              v-for="item in tableCateList"
              :key="item.id"
              :value="item.id"
              >{{ item.name }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="餐桌座位：">
          <Select v-model="formValidate.seat_num">
            <Option
              v-for="item in tableSeatsList"
              :key="item.id"
              :value="item.number"
              >{{ item.number }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="桌号：">
          <div>
            <Tag
              v-for="(item, index) in formValidate.number"
              :key="index"
              class="tag"
              size="large"
              >{{ item }}</Tag
            >
            <InputNumber
              v-model="number"
              :min="1"
              :precision="0"
              class="input"
            ></InputNumber>
            <Button class="btn" @click="addNumber">添加桌号</Button>
          </div>
          <div style="font-size: 12px;color: #999999;">输入桌号后，请点击“添加桌号”按钮</div>
        </FormItem>
        <FormItem label="备注：">
          <Input
            v-model="formValidate.remarks"
            type="textarea"
            :autosize="{ minRows: 5, maxRows: 5 }"
            maxlength="120"
            show-word-limit
          ></Input>
        </FormItem>
      </Form>
      <div class="footer">
        <Button class="mr10" @click="modalToggle">取消</Button>
        <Button class="mr10" @click="addTableQRCode(1)">保存并启用</Button>
        <Button type="primary" @click="addTableQRCode(0)">仅保存</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import { saveAs } from 'file-saver';
import {
  getTableQRCodeList,
  addTableQRCode,
  getTableCateList,
  getTableSeatsList,
  updateTableUsing,
} from '@/api/setting';

export default {
  data() {
    return {
      columns: [
        {
          type: 'selection',
          width: 60,
          align: 'center',
        },
        {
          title: '桌码名称',
          key: 'table_number',
        },
        {
          title: '二维码',
          slot: 'qrcode',
        },
        {
          title: '关联分类',
          slot: 'category',
        },
        {
          title: '创建时间',
          key: 'add_time',
        },
        {
          title: '备注',
          key: 'remarks',
        },
        {
          title: '状态',
          slot: 'is_using',
        },
        {
          title: '操作',
          slot: 'action',
          align: 'center',
        },
      ],
      tableQRCodeList: [],
      total: 0,
      limit: 10,
      page: 1,
      modal: false,
      tableCateList: [],
      tableSeatsList: [],
      formValidate: {
        cate_id: 0,
        seat_num: 0,
        number: [],
        is_using: 0,
        remarks: '',
      },
      number: 1,
      id: 0,
      loading: false,
      QRCodeSelected: [],
    };
  },
  created() {
    this.getTableQRCodeList();
    this.getTableCateList();
    this.getTableSeatsList();
  },
  methods: {
    onSelectionChange(selection) {
      this.QRCodeSelected = selection;
    },
    // 获取桌码列表
    getTableQRCodeList() {
      this.loading = true;
      getTableQRCodeList({
        page: this.page,
        limit: this.limit,
      }).then((res) => {
        this.loading = false;
        this.tableQRCodeList = res.data.list;
        this.total = res.data.count;
      });
    },
    getTableCateList() {
      getTableCateList().then((res) => {
        this.tableCateList = res.data.data;
      });
    },
    getTableSeatsList() {
      getTableSeatsList().then((res) => {
        this.tableSeatsList = res.data;
      });
    },
    // 分页
    onChange(page) {
      this.page = page;
      this.getTableQRCodeList();
    },
    // 编辑
    onEdit(row) {
      let { cate_id, seat_num, is_using, remarks, table_number, id } = row;
      this.modal = true;
      this.formValidate = { cate_id, seat_num, is_using, remarks, number: [] };
      this.number = table_number;
      this.id = id;
    },
    // 下载
    onDownload(row) {
      if (typeof row.qrcode != 'string' || !row.qrcode) {
        return this.$Message.error('无二维码图片链接');
      }
      new Promise((resolve, reject) => {
        const image = new Image();
        image.crossOrigin = 'anonymous';
        image.src = row.qrcode;
        image.onload = () => {
          resolve(image);
        };
      }).then((image) => {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = image.width;
        canvas.height = image.height;
        context.drawImage(image, 0, 0, image.width, image.height);
        let url = canvas.toDataURL('image/jpeg');
        let a = document.createElement('a'); // 生成一个a元素
        let event = new MouseEvent('click'); // 创建一个单击事件
        a.download = row.table_number; // 设置图片名称
        a.href = url; // 将生成的URL设置为a.href属性
        a.dispatchEvent(event);
      });
    },
    // 删除
    onDelete(id) {
      this.$modalSure({
        title: '删除桌码',
        url: `/table/del/qrcode/${id}`,
        method: 'delete',
        ids: '',
      }).then((res) => {
        this.$Message.success(res.msg);
        if (this.page > 1 && this.tableQRCodeList.length == 1) {
          this.page--;
        }
        this.getTableQRCodeList();
      });
    },
    // 批量下载
    onBatchDownload() {
      if (!this.QRCodeSelected[0].qrcode) {
        return this.$Message.error('无二维码图片链接');
      }
      this.QRCodeSelected.forEach(this.onDownload);
    },
    // 取消、添加桌码
    modalToggle() {
      this.modal = !this.modal;
      this.formValidate = {
        cate_id: 0,
        seat_num: 0,
        number: [],
        is_using: 0,
        remarks: '',
      };
      this.number = 1;
      this.id = 0;
    },
    addTableQRCode(is_using) {
      if (!this.formValidate.cate_id) {
        return this.$Message.error('请选择桌台类型');
      }
      if (!this.formValidate.seat_num) {
        return this.$Message.error('请选择餐桌座位');
      }
      if (!this.formValidate.number.length) {
        if (!this.id) {
          return this.$Message.error('请添加桌码');
        }
        let tableQRCode = this.tableQRCodeList.find(
          (item) => item.id == this.id
        );
        this.formValidate.number = [tableQRCode.table_number];
      }
      let formValidate = { ...this.formValidate };
      formValidate.is_using = is_using;
      addTableQRCode(this.id, formValidate)
        .then((res) => {
          this.$Message.success(res.msg);
          this.modal = false;
          this.getTableQRCodeList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 添加桌号
    addNumber() {
      this.formValidate.number.push(this.number);
      this.formValidate.number = this.formValidate.number.filter(
        (item, index, array) => {
          return array.indexOf(item) === index;
        }
      );
    },
    // 是否启用
    isUsingChange(id, is_using) {
      updateTableUsing(id, { is_using }).then(res => {
        this.$Message.success(res.msg);
        this.getTableQRCodeList();
      }).catch(res => {
        this.$Message.error(res.msg);
      });
    }
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
  padding: 30px 25px 29px 0;
}
.ivu-modal-wrap {
  .tag {
    width: 80px;
    border-color: #dddddd;
    margin: 0 10px 10px 0;
    background: none;
    text-align: center;
  }
  .input {
    width: 80px;
    margin: 0 10px 10px 0;
  }
  .btn {
    margin: 0 10px 10px 0;
  }
  .footer {
    text-align: right;
  }
}
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>