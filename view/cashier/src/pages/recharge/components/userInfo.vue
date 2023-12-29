<template>
  <div class="user-info">
    <div class="section">
      <div class="section-head">用户信息</div>
      <div class="section-body">
        <div class="item">
          <div class="name">用户ID：</div>
          <div class="value">{{ userInfo.uid }}</div>
        </div>
        <div class="item">
          <div class="name">真实姓名：</div>
          <div class="value">{{ userInfo.real_name || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">手机号码：</div>
          <div class="value">{{ userInfo.phone || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">生日：</div>
          <div class="value">{{ userInfo.birthday || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">性别：</div>
          <div class="value">
            {{ userInfo.sex ? (userInfo.sex == 1 ? '男' : '女') : '保密' }}
          </div>
        </div>
        <div class="item">
          <div class="name">身份证号：</div>
          <div class="value">{{ userInfo.card_id || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">用户地址：</div>
          <div class="value">
            {{ `${userInfo.provincials}${userInfo.addres}` || '-' }}
          </div>
        </div>
      </div>
    </div>
    <div class="section">
      <div class="section-head">用户概况</div>
      <div class="section-body">
        <div class="item">
          <div class="name">推广资格：</div>
          <div class="value">{{ userInfo.spread_open ? '启用' : '禁用' }}</div>
        </div>
        <div class="item">
          <div class="name">用户状态：</div>
          <div class="value">{{ userInfo.status ? '开启' : '锁定' }}</div>
        </div>
        <div class="item">
          <div class="name">用户等级：</div>
          <div class="value">{{ userInfo.vip_name || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">用户标签：</div>
          <div class="value">{{ userInfo.label_list || '-' }}</div>
        </div>
        <div class="item">
          <div class="name">用户分组：</div>
          <div class="value">{{ userInfo.group ? userInfo.group.group_name : '无' }}</div>
        </div>
        <div class="item">
          <div class="name">推广人：</div>
          <div class="value">{{ userInfo.spread_uid_nickname || '无' }}</div>
        </div>
        <div class="item">
          <div class="name">注册时间：</div>
          <div class="value">
            {{ $moment(userInfo.add_time * 1000).format('YYYY-MM-DD H:mm:ss') }}
          </div>
        </div>
        <div class="item">
          <div class="name">登录时间：</div>
          <div class="value">
            {{
              $moment(userInfo.last_time * 1000).format('YYYY-MM-DD H:mm:ss')
            }}
          </div>
        </div>
      </div>
    </div>
    <div class="section">
      <div class="section-head">用户备注</div>
      <div class="section-body">
        <div class="item">
          <div class="name">备注：</div>
          <div class="value">{{ userInfo.mark || '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'userInfo',
  props: {
    userInfo: {
      type: Object,
      default() {
        return {};
      },
    },
  },
};
</script>

<style lang="stylus" scoped>
.user-info {
  .section {
    padding: 30px 0;

    +.section {
      border-top: 1px dashed #EEEEEE;
    }
  }

  .section-head {
    padding-left: 10px;
    border-left: 3px solid #1890FF;
    font-weight: 500;
    font-size: 14px;
    line-height: 15px;
    color: #303133;
  }

  .section-body {
    display: flex;
    flex-wrap: wrap;
    margin-top: 28px;
    font-size: 14px;

    .item {
      flex: 0 0 calc(((100% - 48px) / 3));
      display: flex;
      align-items: center;
      margin: 0 0 24px 0;

      &:nth-child(3n+3) {
        margin: 0 0 24px 0;
      }
    }

    .name {
      width: 100px;
      text-align: right;
      color: rgba(102,102,102,0.85);
    }

    .value {
      flex: 1;
      padding: 11px 14px;
      border: 1px solid #DDDDDD;
      border-radius: 6px;
    }
  }
}
</style>