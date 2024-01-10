<template>
  <div class="article-manager video-icon form-submit" id="shopp-manager">
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title" class="acea-row row-middle">
          <router-link :to="{ path: `${roterPre}/classes/classes_list` }">
            <div class="font-sm after-line">
              <span class="iconfont iconfanhui"></span>
              <span class="pl10">返回</span>
            </div>
          </router-link>
          <span v-text="$route.params.id ? '编辑课程' : '添加课程'" class="mr20 ml16"></span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt mh">
      <div class="new_tab">
        <Tabs v-model="currentTab" @on-click="onhangeTab">
          <TabPane
              v-for="(item, index) in headTab"
			  v-if="index !=6 || (index==6 && merchantType==0)"
              :key="index+'name'"
              :label="item.title"
              :name="item.name"
          ></TabPane>
        </Tabs>
      </div>
      <Form
          class="formValidate mt20"
          ref="formValidate"
          :rules="ruleValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
      >
        <Row :gutter="24" type="flex" v-show="currentTab === '1'">
          <!-- 商品信息-->
          <Col span="24">
            <FormItem label="课程分类：" prop="cate_id">
              <el-cascader
                  placeholder="请选择课程分类"
                  v-width="'50%'"
                  size="mini"
                  v-model="formValidate.cate_id"
                  :options="treeSelect"
                  :props="props"
                  filterable
                  clearable>
              </el-cascader>
              <span class="addClass" @click="addClass" v-if="merchantType!=1">新增分类</span>
              <!-- <Select v-model="formValidate.cate_id" multiple v-width="'50%'">
                <Option
                  v-for="item in treeSelect"
                  :disabled="item.pid === 0"
                  :value="item.id"
                  :key="item.id"
                  >{{ item.html + item.cate_name }}</Option
                >
              </Select> -->
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="课程名称：" prop="store_name">
              <Input
                  v-model="formValidate.store_name"
                  placeholder="请输入课程名称"
                  v-width="'50%'"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="课程概述：" prop="store_overview">
              <Input
                  v-model="formValidate.store_overview"
                  placeholder="请输入课程概述"
                  v-width="'50%'"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="课程轮播图：" prop="slider_image">
              <div class="acea-row">
                <div
                    class="pictrue"
                    v-for="(item, index) in formValidate.slider_image"
                    :key="index+'img'"
                    draggable="true"
                    @dragstart="handleDragStart($event, item)"
                    @dragover.prevent="handleDragOver($event, item)"
                    @dragenter="handleDragEnter($event, item)"
                    @dragend="handleDragEnd($event, item)"
                >
                  <img v-lazy="item" />
                  <Button
                      shape="circle"
                      icon="md-close"
                      @click.native="handleRemove(index)"
                      class="btndel"
                  ></Button>
                </div>
                <div
                    v-if="formValidate.slider_image.length < 10"
                    class="upLoad acea-row row-center-wrapper"
                    @click="modalPicTap('duo')"
                >
                  <Icon type="ios-camera-outline" size="26" />
                </div>
                <Input
                    v-model="formValidate.slider_image[0]"
                    class="input-display"
                ></Input>
              </div>
              <div class="tips"> 用于商品详情封面及缩率图，建议尺寸：800*800，可拖拽改变图片顺序，默认首张图为主图，最多上传10张，单张大小建议不超过2MB</div>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="课程推荐图：" prop="recommend_image">
              <div class="acea-row">
                <div
                    v-if="formValidate.recommend_image"
                    class="pictrue"
                >
                  <img v-lazy="formValidate.recommend_image" />
                  <Button
                      shape="circle"
                      icon="md-close"
                      @click.native="formValidate.recommend_image = ''"
                      class="btndel"
                  ></Button>
                </div>
                <div
                    v-else
                    class="upLoad acea-row row-center-wrapper"
                    @click="modalPicTap('dan', 'recommend_image')"
                >
                  <Icon type="ios-camera-outline" size="26" />
                </div>
                <Input
                    v-model="formValidate.recommend_image"
                    class="input-display"
                ></Input>
              </div>
              <div class="tips">用于课程列表展示，建议尺寸：900*384，大小建议不超过2MB
                <!-- <Poptip placement="bottom" trigger="hover" width="256" transfer padding="8px">
                  <a>查看示例</a>
                  <div class="exampleImg" slot="content">
                    <img
                        :src="`${baseURL}/statics/system/productRecommendImage.png`"
                        alt=""
                    />
                  </div>
                </Poptip> -->
              </div>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="添加视频：">
              <i-switch v-model="formValidate.video_open" size="large">
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
            </FormItem>
          </Col>
          <Col span="24" v-if="formValidate.video_open">
            <FormItem label="上传视频：" prop="video_link">
              <Button @click="modalPicTap('dan','video')">上传视频</Button>
              <div class="tips">建议使用云存储保存视频，<router-link :to="{ path: `${roterPre}/setting/storage` }">配置云存储</router-link>，建议时长：9～30秒，视频宽高比16:9
                <Poptip placement="bottom" trigger="hover" width="440" transfer padding="8px">
                  <a>查看示例</a>
                  <div class="exampleImg" slot="content">
                    <img
                        :src="`${baseURL}/statics/system/productVideo.png`"
                        alt=""
                        style="width: 204px;height: 270px;"
                    />
                    <img
                        :src="`${baseURL}/statics/system/productVideo2.png`"
                        alt=""
                        style="width: 204px;height: 270px;"
                    />
                  </div>
                </Poptip>
              </div>
              <div class="iview-video-style" v-if="formValidate.video_link">
                <video
                    class="video-style"
                    :src="formValidate.video_link"
                    controls="controls"
                >
                </video>
                <div class="mark"></div>
                <Icon
                    type="ios-trash-outline"
                    class="iconv"
                    @click="delVideo"
                />
              </div>
            </FormItem>
          </Col>
          <Col span="24" class="goodsShow">
            <FormItem label="上架时间：">
              <RadioGroup v-model="formValidate.is_show" @on-change="goodsOn">
                <Radio :label="1">
                  <Icon type="social-apple"></Icon>
                  <span>立即上架</span>
                </Radio>
                <Radio :label="2">
                  <Icon type="social-android"></Icon>
                  <span>定时上架</span>
                </Radio>
                <Radio :label="0">
                  <Icon type="social-windows"></Icon>
                  <span>放入仓库</span>
                </Radio>
              </RadioGroup>
            </FormItem>
          </Col>
          <Col span="24" v-if="formValidate.is_show==2">
            <FormItem label="">
              <DatePicker type="datetime" @on-change="onchangeShow" :options="startPickOptions"  :value="formValidate.auto_on_time" v-model="formValidate.auto_on_time"  placeholder="请选择上架时间" format="yyyy-MM-dd HH:mm" style="width: 260px;"></DatePicker>
            </FormItem>
          </Col>
          <Col span="24" class="goodsShow">
            <FormItem label="定时下架：">
              <Switch v-model="off_show" :true-value="1" :false-value="0" size="large" @on-change="goodsOff">
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </Switch>
            </FormItem>
          </Col>
          <Col span="24" v-if="off_show==1">
            <FormItem label="">
              <DatePicker type="datetime" @on-change="onchangeOff" :options="endPickOptions" :value="formValidate.auto_off_time" v-model="formValidate.auto_off_time" placeholder="请选择下架时间" format="yyyy-MM-dd HH:mm" style="width: 260px;"></DatePicker>
              <div class="tips">开启定时下架后，系统会在设置时间下架该商品。下架时间需晚于开售时间，商品才能定时开售。</div>
            </FormItem>
          </Col>
        </Row>
        <Row :gutter="24" type="flex" v-show="currentTab === '2'">
            <Col span="24">
              <FormItem required label="激活时效：">
                <RadioGroup v-model="oneFormValidate[0].write_valid">
                  <Radio :label="1">永久有效</Radio>
                  <Radio :label="2">购买后几天有效</Radio>
                  <Radio :label="3">固定有效期</Radio>
                </RadioGroup>
              </FormItem>
            </Col>
            <Col span="24" v-if="oneFormValidate[0].write_valid==2">
              <FormItem
                  label=""
                  prop="freight"
              >
                <div class="acea-row row-middle">
                  <InputNumber
                      :min="1"
                      v-model="oneFormValidate[0].write_days"
                      placeholder="请输入有效天数"
                      v-width="'50%'"
                  />
                  <span class="ml10">天</span>
                </div>
              </FormItem>
            </Col>
            <Col span="24" v-if="oneFormValidate[0].write_valid==3">
              <FormItem
                  label=""
                  prop="freight"
              >
                <div class="acea-row row-middle">
                  <DatePicker :editable="false" type="daterange" format="yyyy-MM-dd" placeholder="请选择固定有效期"
                              @on-change="onchangeTime" v-width="'50%'" :value="oneFormValidate[0].section_time" v-model="oneFormValidate[0].section_time"></DatePicker>
                </div>
              </FormItem>
            </Col>
          <Col span="24" v-if="formValidate.product_type != 4">
            <FormItem label="商品规格：" props="spec_type">
              <RadioGroup
                  v-model="formValidate.spec_type"
                  @on-change="changeSpec"
              >
                <Radio :label="0" class="radio">单规格</Radio>
                <Radio :label="1">多规格</Radio>
              </RadioGroup>
            </FormItem>
          </Col>
          <!-- 多规格添加-->
          <Col span="24" v-if="formValidate.spec_type === 1 && formValidate.product_type != 4" class="noForm">
            <Col span="24">
              <FormItem label="选择规格：" prop="" >
                <div class="acea-row row-middle">
                  <Select v-model="formValidate.selectRule" style="width: 23%">
                    <Option
                        v-for="(item, index) in ruleList"
                        :value="item.rule_name"
                        :key="item.id"
                    >{{ item.rule_name }}</Option
                    >
                  </Select>
                  <Button type="primary" class="mr20" @click="confirm"
                  >确认</Button
                  >
                  <Button @click="addRule">添加规格模板</Button>
                </div>
              </FormItem>
            </Col>
            <Col span="24">
              <FormItem v-if="attrs.length !== 0">
                <draggable
                    class="dragArea list-group"
                    :list="attrs"
                    group="peoples"
                    handle=".move-icon"
                    :move="checkMove"
                    @end="end"
                >
                  <div
                      v-for="(item, index) in attrs"
                      :key="index+'attrs'"
                      class="acea-row row-middle mb10"
                  >
                    <div class="move-icon">
                      <span class="iconfont icondrag2"></span>
                    </div>
                    <div
                        style="width: 90%"
                        :class="moveIndex === index ? 'borderStyle' : ''"
                    >
                      <div class="acea-row row-middle">
			              <span class="mr5">{{ item.value }}</span
                    ><Icon
                          type="ios-close-circle"
                          size="14"
                          class="curs"
                          @click="handleRemoveRole(index)"
                      />
                      </div>
                      <div class="rulesBox">
                        <draggable :list="item.detail" handle=".drag">
                          <Tag
                              type="dot"
                              closable
                              color="primary"
                              v-for="(j, indexn) in item.detail"
                              :key="indexn"
                              :name="j"
                              class="mr20 drag"
                              @on-close="handleRemove2(item.detail, indexn)"
                          >{{ j }}</Tag
                          >
                        </draggable>
                        <Input
                            search
                            enter-button="添加"
                            placeholder="请输入属性名称"
                            v-model="item.detail.attrsVal"
                            @on-search="createAttr(item.detail.attrsVal, index)"
                            class="width-add"
                        />
                      </div>
                    </div>
                  </div>
                </draggable>
              </FormItem>
            </Col>
            <Col span="24" v-if="createBnt">
              <FormItem>
                <Button
                    type="primary"
                    icon="md-add"
                    @click="addBtn"
                    class="mr15"
                >添加新规格</Button
                >
                <Button type="success" @click="generate(1)">立即生成</Button>
              </FormItem>
            </Col>
            <Col span="24" v-if="showIput">
              <Col :xl="6" :lg="9" :md="10" :sm="24" :xs="24">
                <FormItem label="规格：">
                  <Input
                      placeholder="请输入规格"
                      v-model="formDynamic.attrsName"
                  />
                </FormItem>
              </Col>
              <Col :xl="6" :lg="9" :md="10" :sm="24" :xs="24">
                <FormItem label="规格值：">
                  <Input
                      v-model="formDynamic.attrsVal"
                      placeholder="请输入规格值"
                  />
                </FormItem>
              </Col>
              <Col :xl="6" :lg="5" :md="10" :sm="24" :xs="24">
                <FormItem>
                  <Button type="primary" class="mr15" @click="createAttrName"
                  >确定</Button
                  >
                  <Button @click="offAttrName">取消</Button>
                </FormItem>
              </Col>
            </Col>
            <!-- 多规格设置-->
            <Col
                :xl="24"
                :lg="24"
                :md="24"
                :sm="24"
                :xs="24"
                v-if="
			      manyFormValidate.length &&
			      formValidate.header.length !== 0 &&
			      attrs.length !== 0
			    "
            >
              <!-- 批量设置-->
              <Col span="24">
                <FormItem label="批量设置：" class="labeltop">
                  <Table :data="oneFormBatch" :columns="formValidate.product_type == 1?columnsCarMy:formValidate.product_type == 3?columnsFictitious:columns2" border>
                    <template slot-scope="{ row, index }" slot="attr">
                      <div @click="batchAttr" class="acea-row row-between-wrapper" style="cursor: pointer;">
                        <div style="width: 41px;">{{oneFormBatch[0].attr}}</div>
                        <span class="iconfont icondrop-down"></span>
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="pic">
                      <div
                          class="acea-row row-middle row-center-wrapper"
                          @click="modalPicTap('dan', 'duopi', index)"
                      >
                        <div
                            class="pictrue pictrueTab"
                            v-if="oneFormBatch[0].pic"
                        >
                          <img v-lazy="oneFormBatch[0].pic" />
                        </div>
                        <div
                            class="upLoad pictrueTab acea-row row-center-wrapper"
                            v-else
                        >
                          <Icon type="ios-camera-outline" size="26" class="iosfont" />
                        </div>
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="quota">
                      <InputNumber
                          v-model="oneFormBatch[0].quota"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="price">
                      <InputNumber
                          v-model="oneFormBatch[0].price"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
					<template slot-scope="{ row, index }" slot="settle_price" v-if='merchantType == 2'>
					  <InputNumber
					      v-model="oneFormBatch[0].settle_price"
					      :min="0"
					      class="priceBox"
					  ></InputNumber>
					</template>
                    <template slot-scope="{ row, index }" slot="cost">
                      <InputNumber
                          v-model="oneFormBatch[0].cost"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="ot_price">
                      <InputNumber
                          v-model="oneFormBatch[0].ot_price"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="stock">
                      <InputNumber
                          v-model="oneFormBatch[0].stock"
                          :min="0"
                          :disabled="formValidate.product_type == 1 || openErp"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="bar_code">
                      <Input v-model="oneFormBatch[0].bar_code"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="code">
                      <Input v-model="oneFormBatch[0].code"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="weight">
                      <InputNumber
                          v-model="oneFormBatch[0].weight"
                          :step="0.1"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="volume">
                      <InputNumber
                          v-model="oneFormBatch[0].volume"
                          :step="0.1"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="fictitious" v-if="formValidate.product_type == 1">
                      <Button
                          v-if="
					        !row.virtual_list && !row.stock
					      "
                          @click="addVirtual(0, 'oneFormBatch')"
                      >添加卡密</Button
                      >
                      <span
                          v-else
                          class="seeCatMy"
                          @click="seeVirtual(oneFormBatch[0], 'oneFormBatch', 0)"
                      >已设置</span
                      >
                    </template>
                    <template slot-scope="{ row, index }" slot="action">
                      <a @click="batchAdd">批量设置</a>
                      <Divider type="vertical" />
                      <a @click="batchDel">清空</a>
                    </template>
                  </Table>
                </FormItem>
              </Col>
              <!-- 多规格表格-->
              <Col span="24">
                <FormItem label="课程属性：" class="labeltop">
                  <Table
                      :data="manyFormValidate"
                      :columns="formValidate.header"
                      border
                  >
                    <template slot-scope="{ row, index }" v-for="(item,i) in attrData.length" :slot="'value'+(i+1)">
                      <div :class="manyFormValidate[index].select?'selectOn':''">{{manyFormValidate[index]['value'+(i+1)]}}</div>
                    </template>
                    <template slot-scope="{ row, index }" slot="pic">
                      <div
                          class="acea-row row-middle row-center-wrapper"
                          @click="modalPicTap('dan', 'duoTable', index)"
                      >
                        <div
                            class="pictrue pictrueTab"
                            v-if="manyFormValidate[index].pic"
                        >
                          <img v-lazy="manyFormValidate[index].pic" />
                        </div>
                        <div
                            class="upLoad pictrueTab acea-row row-center-wrapper"
                            v-else
                        >
                          <Icon
                              type="ios-camera-outline"
                              size="21"
                          />
                        </div>
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="quota">
                      <InputNumber
                          v-model="manyFormValidate[index].quota"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="price">
                      <InputNumber
                          v-model="manyFormValidate[index].price"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
					<template slot-scope="{ row, index }" slot="settle_price" v-if="merchantType == 2">
					  <InputNumber
					      v-model="manyFormValidate[index].settle_price"
					      :min="0"
					      class="priceBox"
					  ></InputNumber>
					</template>
                    <template slot-scope="{ row, index }" slot="cost">
                      <InputNumber
                          v-model="manyFormValidate[index].cost"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="ot_price">
                      <InputNumber
                          v-model="manyFormValidate[index].ot_price"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="stock">
                      <InputNumber
                          v-model="manyFormValidate[index].stock"
                          :min="0"
                          :precision="0"
                          :disabled="formValidate.product_type == 1 || openErp"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="fictitious" v-if="formValidate.product_type == 1">
                      <Button
                          v-if="(!row.virtual_list || !row.virtual_list.length) && !row.stock"
                          @click="addVirtual(index, 'manyFormValidate')"
                      >添加卡密</Button
                      >
                      <span
                          v-else
                          class="seeCatMy"
                          @click="seeVirtual(row, 'manyFormValidate', index)"
                      >已设置</span>
                    </template>
                    <template slot-scope="{ row, index }" slot="bar_code">
                      <Input v-model="manyFormValidate[index].bar_code"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="code">
                      <Input v-model="manyFormValidate[index].code"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="weight">
                      <InputNumber
                          v-model="manyFormValidate[index].weight"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="volume">
                      <InputNumber
                          v-model="manyFormValidate[index].volume"
                          :min="0"
                          class="priceBox"
                      ></InputNumber>
                    </template>
                    <template slot-scope="{ row, index }" slot="action">
                      <a @click="delAttrTable(index)">删除</a>
                    </template>
                  </Table>
                </FormItem>
              </Col>
            </Col>
          </Col>
          <!-- 单规格表格-->
          <div v-if="formValidate.spec_type === 0 || formValidate.product_type == 4" style="width: 100%;">
            <Col span="24" v-if="formValidate.product_type != 4">
              <FormItem label="图片：" prop="pic">
                <div class="pictrueBox" @click="modalPicTap('dan', 'danTable', 0)">
                  <div class="pictrue" v-if="oneFormValidate[0].pic">
                    <img v-lazy="oneFormValidate[0].pic" />
                  </div>
                  <div class="upLoad acea-row row-center-wrapper" v-else>
                    <Input
                        v-model="oneFormValidate[0].pic"
                        class="input-display"
                    ></Input>
                    <Icon type="ios-camera-outline" size="26" />
                  </div>
                </div>
              </FormItem>
            </Col>
            <Col class="asterisk" span="24">
              <div class="asteriskInfo on3">*</div>
              <FormItem label="使用次数(次)：">
                <InputNumber
                    v-model="oneFormValidate[0].bar_code"
                    :min="1"
                    :max="99999999"
                    :precision="0"
                    v-width="'50%'"
                    placeholder="请输入次数"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24" class="asterisk">
              <div class="asteriskInfo on3">*</div>
              <FormItem label="使用时效(天)：">
                <InputNumber
                    v-model="oneFormValidate[0].code"
                    :min="0"
                    :max="99999999"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24" class="asterisk">
              <div class="asteriskInfo on2">*</div>
              <FormItem label="限购数量：">
                <InputNumber
                    v-model="oneFormValidate[0].limit_num"
                    :min="0"
                    :max="99999999"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24" class="asterisk">
              <div class="asteriskInfo">*</div>
              <FormItem label="售价：">
                <InputNumber
                    v-model="oneFormValidate[0].price"
                    :min="0"
                    :max="99999999"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24" class="asterisk">
              <div class="asteriskInfo on">*</div>
              <FormItem label="成本价：">
                <InputNumber
                    v-model="oneFormValidate[0].cost"
                    :min="0"
                    :max="99999999"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24">
              <FormItem label="原价：">
                <InputNumber
                    v-model="oneFormValidate[0].ot_price"
                    :min="0"
                    :max="99999999"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
            <Col span="24" class="asterisk">
              <div class="asteriskInfo">*</div>
              <FormItem label="库存：">
                <InputNumber
                    v-model="oneFormValidate[0].stock"
                    :min="0"
                    :max="99999999"
                    :disabled="formValidate.product_type == 1 || openErp"
                    :precision="0"
                    v-width="'50%'"
                ></InputNumber>
              </FormItem>
            </Col>
          </div>
        </Row>
        <!-- 商品详情 -->
        <Row v-show="currentTab === '3'" class="mb10">
          <Col span="16">
            <wangeditor
                style="width: 100%"
                :content="contents"
                @editorContent="getEditorContent"
            ></wangeditor>
          </Col>
          <Col span="6" style="width: 33%">
            <div class="ifam">
              <div class="content" v-html="content"></div>
            </div>
          </Col>
        </Row>
        <!-- 营销设置 -->
        <Row v-show="currentTab === '4'">
          <Col span="24">
            <FormItem label="已售数量：">
              <InputNumber
                  v-width="'50%'"
                  :min="0"
                  :max="999999"
                  v-model="formValidate.ficti"
                  placeholder="请输入已售数量"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="排序：">
              <InputNumber
                  :min="0"
                  :max="999999"
                  v-width="'50%'"
                  v-model="formValidate.sort"
                  placeholder="请输入排序"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <div class="lines"></div>
          </Col>
          <Col span="24" v-if="merchantType == 0">
            <FormItem label="赠送gaga币：" prop="give_integral">
              <InputNumber
                  v-width="'50%'"
                  v-model="formValidate.give_integral"
                  :min="0"
                  :max="999999"
                  placeholder="请输入gaga币"
              />
            </FormItem>
          </Col>
          <Col v-bind="grid3" v-if="merchantType == 0">
            <FormItem label="赠送优惠券：">
              <div v-if="couponName.length" class="mb20">
                <Tag
                    closable
                    v-for="(item, index) in couponName"
                    :key="index+'coupon'"
                    @on-close="handleClose(item)"
                >{{ item.title }}</Tag
                >
              </div>
              <Button type="primary" @click="addCoupon">添加优惠券</Button>
            </FormItem>
          </Col>
          <Col span="24" v-if="merchantType == 0">
            <FormItem label="关联用户标签：" prop="label_id" class="labelClass">
              <div class="labelInput acea-row row-between-wrapper" @click="openLabel">
                <div style="width: 90%;">
                  <div v-if="dataLabel.length">
                    <Tag closable v-for="(item,index) in dataLabel" :key="index+'label'" @on-close="closeLabel(item)">{{item.label_name}}</Tag>
                  </div>
                  <span class="span" v-else>选择用户关联标签</span>
                </div>
                <div class="iconfont iconxiayi"></div>
              </div>
            </FormItem>
          </Col>
          <Col span="24" v-if="merchantType==0">
            <FormItem label="仅会员可见：">
              <i-switch v-model="formValidate.is_vip_product" :true-value="1" :false-value="0" size="large">
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
              <div class="tips">开启后仅付费会员可以看见并购买此商品</div>
            </FormItem>
          </Col>
          <Col span="24" v-if="merchantType==0">
            <FormItem label="单独设置：">
              <CheckboxGroup
                  class="checkAlls"
                  v-model="formValidate.is_sub"
                  @on-change="checkAllGroupChange"
              >
                <Checkbox :label="1">佣金设置（数字即返佣金额）</Checkbox>
                <Checkbox :label="0">付费会员价</Checkbox>
              </CheckboxGroup>
            </FormItem>
          </Col>
          <Col span="24" v-if="formValidate.is_sub.length">
            <!--单规格返佣-->
            <FormItem label="课程属性：" v-if="formValidate.spec_type === 0">
              <Table :data="oneFormValidate" :columns="columnsInstall" border>
                <template slot-scope="{ row, index }" slot="pic">
                  <div class="pictrue pictrueTab">
                    <img v-lazy="oneFormValidate[0].pic" />
                  </div>
                </template>
                <template slot-scope="{ row, index }" slot="price">{{
                    oneFormValidate[0].price
                  }}</template>
                <template slot-scope="{ row, index }" slot="cost">{{
                    oneFormValidate[0].cost
                  }}</template>
                <template slot-scope="{ row, index }" slot="ot_price">{{
                    oneFormValidate[0].ot_price
                  }}</template>
                <template slot-scope="{ row, index }" slot="stock">{{
                    oneFormValidate[0].stock
                  }}</template>
                <template slot-scope="{ row, index }" slot="bar_code">{{
                    oneFormValidate[0].bar_code
                  }}</template>
                <template slot-scope="{ row, index }" slot="code">{{
                    oneFormValidate[0].code
                  }}</template>
                <template slot-scope="{ row, index }" slot="weight">{{
                    oneFormValidate[0].weight
                  }}</template>
                <template slot-scope="{ row, index }" slot="volume">{{
                    oneFormValidate[0].volume
                  }}</template>
                <template slot-scope="{ row, index }" slot="brokerage">
                  <InputNumber
                      v-model="oneFormValidate[0].brokerage"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
                <template slot-scope="{ row, index }" slot="brokerage_two">
                  <InputNumber
                      v-model="oneFormValidate[0].brokerage_two"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
                <template slot-scope="{ row, index }" slot="vip_price">
                  <InputNumber
                      v-model="oneFormValidate[0].vip_price"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
              </Table>
            </FormItem>
            <!--多规格返佣-->
            <FormItem label="批量设置：" v-if="formValidate.spec_type === 1">
			    <span v-if="formValidate.is_sub.indexOf(1) > -1">
			      一级返佣：<InputNumber
              placeholder="请输入一级返佣"
              :min="0"
              class="columnsBox perW20"
              v-model="manyBrokerage"
          ></InputNumber>
			      二级返佣：<InputNumber
              placeholder="请输入二级返佣"
              :min="0"
              class="columnsBox perW20"
              v-model="manyBrokerageTwo"
          ></InputNumber>
			    </span>
              <span v-if="formValidate.is_sub.indexOf(0) > -1">
			      会员价：<InputNumber
                  placeholder="请输入会员价"
                  :min="0"
                  class="columnsBox perW20"
                  v-model="manyVipPrice"
              ></InputNumber>
			    </span>
              <Button type="primary" @click="brokerageSetUp">批量设置</Button>
            </FormItem>
            <FormItem
                label="商品属性："
                v-if="formValidate.spec_type === 1 && manyFormValidate.length"
            >
              <Table
                  v-if="formValidate.is_sub"
                  :data="manyFormValidate"
                  :columns="columnsInstal2"
                  border
              >
                <template slot-scope="{ row, index }" v-for="(item,i) in attrData.length" :slot="'value'+(i+1)">
                  <div>{{manyFormValidate[index]['value'+(i+1)]}}</div>
                </template>
                <template slot-scope="{ row, index }" slot="pic">
                  <div class="pictrue pictrueTab">
                    <img v-lazy="manyFormValidate[index].pic" />
                  </div>
                </template>
                <template slot-scope="{ row, index }" slot="price">{{
                    manyFormValidate[index].price
                  }}</template>
                <template slot-scope="{ row, index }" slot="cost">{{
                    manyFormValidate[index].cost
                  }}</template>
                <template slot-scope="{ row, index }" slot="ot_price">{{
                    manyFormValidate[index].ot_price
                  }}</template>
                <template slot-scope="{ row, index }" slot="stock">{{
                    manyFormValidate[index].stock
                  }}</template>
                <template slot-scope="{ row, index }" slot="bar_code">{{
                    manyFormValidate[index].bar_code
                  }}</template>
                <template slot-scope="{ row, index }" slot="code">{{
                    manyFormValidate[index].code
                  }}</template>
                <template slot-scope="{ row, index }" slot="weight">{{
                    manyFormValidate[index].weight
                  }}</template>
                <template slot-scope="{ row, index }" slot="volume">{{
                    manyFormValidate[index].volume
                  }}</template>
                <template slot-scope="{ row, index }" slot="brokerage">
                  <InputNumber
                      v-model="manyFormValidate[index].brokerage"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
                <template slot-scope="{ row, index }" slot="brokerage_two">
                  <InputNumber
                      v-model="manyFormValidate[index].brokerage_two"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
                <template slot-scope="{ row, index }" slot="vip_price">
                  <InputNumber
                      v-model="manyFormValidate[index].vip_price"
                      :min="0"
                      class="priceBox"
                  ></InputNumber>
                </template>
              </Table>
            </FormItem>
          </Col>
          <Col span="24" v-if="merchantType==0">
            <div class="lines"></div>
          </Col>
          <Col span="24">
            <FormItem label="是否限购：">
              <i-switch v-model="formValidate.is_limit" :true-value="1" :false-value="0" size="large" @on-change="limitTap">
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
            </FormItem>
          </Col>
          <Col span="24" v-if="formValidate.is_limit">
            <FormItem label="限购类型：">
              <RadioGroup v-model="formValidate.limit_type">
                <Radio :label="1">单次限购</Radio>
                <Radio :label="2">长期限购</Radio>
              </RadioGroup>
              <div class="tips">
                单次限购是限制每次下单最多购买的数量，长期限购是限制一个用户总共可以购买的数量
              </div>
            </FormItem>
          </Col>
          <Col span="24" v-if="formValidate.is_limit">
            <FormItem label="限购数量：">
              <InputNumber
                  :min="1"
                  v-model="formValidate.limit_num"
                  placeholder="请输入限购数量"
                  class="perW20 maxW"
              />
            </FormItem>
          </Col>
          <Col span="24" v-if="merchantType==0">
			<FormItem label="优品推荐：">
			  <i-switch v-model="formValidate.is_good" :true-value="1" :false-value="0" size="large">
			    <span slot="open">开启</span>
			    <span slot="close">关闭</span>
			  </i-switch>
			</FormItem>
          </Col>
          <Col span="24" v-if="merchantType==0">
            <FormItem label="选择优品推荐商品：">
              <div class="acea-row">
                <div
                    class="pictrue"
                    v-for="(item, index) in goodsData"
                    :key="index+'goods'"
                >
                  <img v-lazy="item.image" />
                  <Button
                      shape="circle"
                      icon="md-close"
                      @click.native="bindDelete(index)"
                      class="btndel"
                  ></Button>
                </div>
                <div
                    v-if="goodsData.length < 12"
                    class="upLoad acea-row row-center-wrapper" @click="goodsTap">
                  <Icon type="ios-camera-outline" size="26" />
                </div>
              </div>
            </FormItem>
          </Col>
        </Row>
        <!-- 其他设置-->
        <Row v-show="currentTab === '5'">
          <Col span="24">
            <FormItem label="课程关键字：" prop="">
              <Input
                  v-model="formValidate.keyword"
                  placeholder="请输入课程关键字"
                  v-width="'50%'"
              />
              <div class="tips">通过命中关键字搜索对应课程，方便用户查找</div>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="课程简介：" prop="">
              <Input
                  v-model="formValidate.store_info"
                  type="textarea"
                  :rows="3"
                  placeholder="请输入课程简介"
                  v-width="'50%'"
              />
              <div class="tips">微信分享课程时，分享的卡片上会显示课程简介
                <Poptip placement="bottom" trigger="hover" width="256" transfer padding="8px">
                  <a>查看示例</a>
                  <div class="exampleImg" slot="content">
                    <img
                        :src="`${baseURL}/statics/system/productDesc.png`"
                        alt=""
                    />
                  </div>
                </Poptip>
              </div>
            </FormItem>
          </Col>
          <Col span="24">
            <div class="lines"></div>
          </Col>
          <Col span="24">
            <FormItem label="注意事项：" prop="store_notes">
              <Input
                  v-model="formValidate.store_notes"
                  type="textarea"
                  :rows="3"
                  placeholder="请输入注意事项"
                  v-width="'50%'"
              />
            </FormItem>
          </Col>
        </Row>
        <Row v-show="currentTab === '6' && merchantType==0">
          <Col span="24" class="ml10">
            <RadioGroup v-model="formValidate.applicable_type" class="radioGroup">
              <Radio :label="1">全部门店</Radio>
              <Radio :label="2">部分门店</Radio>
              <Radio :label="0">仅平台适用</Radio>
            </RadioGroup>
            <div class="tips">可选择将商品同步到哪些门店使用，选择“仅平台适用“则商品不同步任何门店</div>
          </Col>
          <Col span="24" class="mt20 ml10" v-if="formValidate.applicable_type == 2">
            <Button type="primary" @click="addStore">添加门店</Button>
          </Col>
          <Col span="24" class="ml10">
            <div class="storeTable" v-if="formValidate.applicable_type == 2">
              <Table
                  :columns="storeColumns"
                  :data="storesList"
                  ref="table"
                  class="ivu-mt"
                  highlight-row
                  no-userFrom-text="暂无数据"
                  no-filtered-userFrom-text="暂无筛选结果"
              >
                <template slot-scope="{ row }" slot="image">
                  <img :src="row.image" />
                </template>
                <template slot-scope="{ row, index }" slot="action">
                  <a @click="delte(index)">删除</a>
                </template>
              </Table>
            </div>
          </Col>
        </Row>
        <Spin size="large" fix v-if="spinShow"></Spin>
      </Form>
      <Modal
          v-model="modalPic"
          width="960px"
          scrollable
          footer-hide
          closable
          title="上传商品图"
          :mask-closable="false"
          :z-index="1"
      >
        <uploadPictures
            :isChoice="isChoice"
            @getPic="getPic"
            @getPicD="getPicD"
            :gridBtn="gridBtn"
            :gridPic="gridPic"
            v-if="modalPic"
        ></uploadPictures>
      </Modal>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
      <Form>
        <FormItem>
          <Button v-if="currentTab !== '1'" @click="upTab">上一步</Button>
          <Button
              type="primary"
              class="submission"
              v-if="(formValidate.product_type && formValidate.product_type !=4 && currentTab !== '5') || (formValidate.product_type == 4 && currentTab !== '6') || (!formValidate.product_type && currentTab !== '6')"
              @click="downTab('formValidate')"
          >下一步</Button
          >
          <Button
              type="primary"
              :disabled="openSubimit"
              class="submission"
              @click="handleSubmit('formValidate')"
              v-if="$route.params.id || (formValidate.product_type && formValidate.product_type !=4 && currentTab == '5') || (formValidate.product_type == 4 && currentTab == '6') || (!formValidate.product_type && currentTab == '6')"
          >保存</Button
          >
        </FormItem>
      </Form>
    </Card>
    <freightTemplate
        :template="template"
		:merchantType="merchantType"
        v-on:changeTemplate="changeTemplate"
        ref="templates"
    ></freightTemplate>
    <add-attr ref="addattr" @getList="userSearchs"></add-attr>
    <coupon-list
        ref="couponTemplates"
        @nameId="nameId"
        :couponids="formValidate.coupon_ids"
        :updateIds="updateIds"
        :updateName="updateName"
    ></coupon-list>
    <!-- 生成淘宝京东表单-->
    <Modal v-model="modals" @on-cancel="cancel" class="Box" class-name="vertical-center-modal" scrollable  footer-hide closable title="复制淘宝、天猫、京东、苏宁、1688" :mask-closable="false"  width="800" height="500">
      <tao-bao ref="taobaos" v-if="modals" @on-close="onClose"></tao-bao>
    </Modal>
    <!-- 商品列表 -->
    <Modal v-model="goodsModals" title="课程列表" footerHide  scrollable width="900" @on-cancel="goodCancel">
      <goods-list v-if="goodsModals" ref="goodslist" @getProductId="getProductId" :ischeckbox="true"></goods-list>
    </Modal>
    <!-- 用户标签 -->
    <Modal
        v-model="labelShow"
        scrollable
        title="选择用户标签"
        :closable="true"
        width="540"
        :footer-hide="true"
        :mask-closable="false"
    >
      <userLabel ref="userLabel" @activeData="activeData" @close="labelClose"></userLabel>
    </Modal>
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
    <menus-from :formValidate="formBrand" :fromName='1' ref="menusFrom"></menus-from>
    <Modal v-model="attrShow"
           scrollable
           title="请选择商品规格"
           :closable="false"
           width="320"
           :footer-hide="true"
           :mask-closable="false">
      <attr-list :attrs="attrsList" @activeData="activeAttr" @close="labelAttr" @subAttrs="subAttrs" v-if="attrShow"></attr-list>
    </Modal>
    <Modal v-model="carMyShow"
           scrollable
           title="添加卡密"
           closable
           width="700"
           :footer-hide="true"
           :mask-closable="false">
      <add-carMy ref="addCarMy" :virtualList="virtualList" @changeVirtual="changeVirtual" @fixdBtn="fixdBtn" @closeCarMy="closeCarMy"></add-carMy>
    </Modal>
    <Modal v-model="storeModals" title="门店列表" footerHide  scrollable width="900" @on-cancel="cancelStore">
      <store-list ref="storelist" @getStoreId="getStoreId" v-if="storeModals"></store-list>
    </Modal>
  </div>
</template>

<script>
// import COS from 'cos-js-sdk-v5'
import { mapState,mapMutations } from "vuex";
import Setting from "@/setting";
import util from "@/libs/util";
import vuedraggable from "vuedraggable";
import uploadPictures from "@/components/uploadPictures";
import freightTemplate from "@/components/freightTemplate";
import wangeditor from "@/components/wangEditor/index.vue";
import storeList from "@/components/storeList";
import menusFrom from "../classesBrand/components/menusFrom";
import userLabel from "@/components/labelList";
import storeLabelList from "@/components/storeLabelList";
import couponList from "@/components/couponList";
import goodsList from '@/components/classesList/index';
import addAttr from "../classesAttr/addAttr";
import attrList from "../components/attrList";
import addCarMy from "../components/addCarMy";
import taoBao from './taoBao'
import {
  productInfoApi,
  productGetTemplateApi,
  productGetTempKeysApi,
  checkActivityApi,
  productCache,
  cacheDelete,
  brandList,
  productAllUnit,
  productUnitCreate,
  uploadType,
  productAllEnsure,
  productLabelAdd,
  productAllSpecs,
  allSystemForm
} from "@/api/product";
import {
    classesAddApi,
    classesGetRuleApi,
  generateAttrApi,
  cascaderListApi,
  classesCreateApi,
} from "@/api/classes";
import { systemFormInfo } from '@/api/setting'
import { getSupplierList } from '@/api/supplier'
import { erpConfig } from "@/api/erp";
import { uploadByPieces } from "@/utils/upload"; //引入uploadByPieces方法
export default {
  name: "product_productAdd",
  components: {
    storeList,
    uploadPictures,
    freightTemplate,
    goodsList,
    addAttr,
    couponList,
    taoBao,
    userLabel,
    menusFrom,
    attrList,
    addCarMy,
    storeLabelList,
    wangeditor,
    draggable: vuedraggable,
  },
  data() {
    return {
      formTypeList: [],
      formColumns: [
        {
          title: '表单标题',
          key: 'title',
          // align:'center',
          minWidth: 100
        },
        {
          title: '表单类型',
          key: 'name',
          // align:'center',
          minWidth: 100
        },
        {
          title: '是否必填',
          slot: 'require',
          // align:'center',
          minWidth: 100
        }
      ],
      storesList:[],
      tableData:[],
      storeModals: false,
      roterPre: Setting.roterPre,
      specsList:[],
      supplierList:[],
      specsColumns:[
        {
          title: '参数名称',
          key: 'name',
          align:'center',
          width: 150,
          render: (h, params) => {
            return h("div", [
              h("Input", {
                props: {
                  value: params.row.name,
                  placeholder:'请输入参数名称'
                },
                on: {
                  "on-change": (e) => {
                    params.row.name = e.target.value;
                    this.specsList[params.index].name = e.target.value;
                  },
                },
              }),
            ]);
          }
        },
        {
          title: '参数值',
          key: 'value',
          align:'center',
          width: 300,
          render: (h, params) => {
            return h("div", [
              h("Input", {
                props: {
                  value: params.row.value,
                  placeholder:'请输入参数值'
                },
                on: {
                  "on-change": (e) => {
                    params.row.value = e.target.value;
                    this.specsList[params.index].value = e.target.value;
                  },
                },
              }),
            ]);
          }
        },
        {
          title: '排序',
          key: 'sort',
          align:'center',
          width: 100,
          render: (h, params) => {
            return h("div", [
              h("InputNumber", {
                props: {
                  value: parseInt(params.row.sort) || 0,
                  placeholder:'排序',
                  precision:0
                },
                on: {
                  "on-change": (e) => {
                    params.row.sort = e;
                    this.specsList[params.index].sort = e;
                  },
                },
              }),
            ]);
          }
        },
        {
          title: '操作',
          slot: 'action',
          align:'center',
          minWidth: 120
        }
      ],
      //自定义留言下拉选择
      customList: [
        {
          value: "text",
          label: "文本框",
        },
        {
          value: "number",
          label: "数字",
        },
        {
          value: "email",
          label: "邮件",
        },
        {
          value: "data",
          label: "日期",
        },
        {
          value: "time",
          label: "时间",
        },
        {
          value: "id",
          label: "身份证",
        },
        {
          value: "phone",
          label: "手机号",
        },
        {
          value: "img",
          label: "图片",
        },
      ],
      headTab: [
        { title: "基础信息", name: "1" },
        { title: "规格库存", name: "2" },
        { title: "商品详情", name: "3" },
        // { title: "物流设置", name: "4" },
        { title: "营销设置", name: "4" },
        { title: "其他设置", name: "5" },
        { title: "适用门店", name: "6" },
      ],
      productType: [
        { name: "普通商品", title: "物流发货", id: 0 },
        { name: "卡密/网盘", title: "自动发货", id: 1 },
        { name: "虚拟商品", title: "虚拟发货", id: 3 },
        { name: "次卡商品", title: "到店核销", id: 4 },
      ],
      virtualList:[],
      carMyShow: false,//是否开启卡密弹窗
      recommend: [], //商品推荐
      customBtn: false, //自定义留言开关
      attrShow:false,
      content: "",
      contents: "",
      seletVideo: 0,
      fileUrl: Setting.apiBaseURL + "/file/upload",
      fileUrl2: Setting.apiBaseURL + "/file/video_upload",
      baseURL: Setting.apiBaseURL.replace(/adminapi/, ''),
      upload_type: "", //视频上传类型 1 本地上传 2 3 4 OSS上传
      uploadData: {}, // 上传参数
      header: {},
      dataLabel:[],
      storeDataLabel:[],
      labelShow:false,
      storeLabelShow:false,
      props: { emitPath: false, multiple: true, checkStrictly: true },
      type: 0,
      goodsModals: false,
      off_show: 0,
      modals: false,
      spinShow: false,
      openSubimit: false,
      grid2: {
        xl: 10,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24,
      },
      grid3: {
        xl: 18,
        lg: 18,
        md: 20,
        sm: 24,
        xs: 24,
      },
      // 批量设置表格data
      oneFormBatch: [
        {
          attr:"全部",
          pic: "",
          price: 0,
		  settle_price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: "",
          code: "",
          weight: 0,
          volume: 0,
          quota: 0
        },
      ],
      // 规格数据
      formDynamic: {
        attrsName: "",
        attrsVal: "",
      },
      formDynamicNameData: [],
      isBtn: false,
      columnsCarMy:[],
      columns2: [
        {
          title: "商品规格",
          slot: "attr",
          align: "center",
          minWidth: 80,
        },
        {
          title: "图片",
          slot: "pic",
          align: "center",
          minWidth: 80,
        },
        {
          title: "售价",
          slot: "price",
          align: "center",
          minWidth: 95,
        },
        {
          title: "成本价",
          slot: "cost",
          align: "center",
          minWidth: 95,
        },
        {
          title: "原价",
          slot: "ot_price",
          align: "center",
          minWidth: 95,
        },
        {
          title: "库存",
          slot: "stock",
          align: "center",
          minWidth: 95,
        },
        {
          title: "使用次数(次)",
          slot: "bar_code",
          align: "center",
          minWidth: 120,
        },
        {
          title: "使用时效(天)",
          slot: "code",
          align: "center",
          minWidth: 120,
        },
        {
          title: "限购数量",
          slot: "quota",
          align: "center",
          minWidth: 120,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          align: "center",
          minWidth: 140,
        },
      ],
      columns: [],
      columnsInstall: [],
      columnsInstal2: [],
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      formValidate: {
        applicable_store_id:[],
        system_form_id:0,//自定义表单id
        // supplier_id:0,//供应商
        is_presale_product: 0, //预售商品开关
        is_limit:0,//是否限购开关
        limit_type:1,//1单次限购，2长期限购
        limit_num:1,//限购数量
        is_vip_product: 0, //付费会员专属开关
        is_support_refund:0,
        disk_info: "", //卡密简介
        presale_day: 1, //预售发货时间-结束
        presale_time:[],
        auto_on_time:'',
        video_open: false, //视频按钮是否显示
        store_name: "",
        freight: 1, //运费设置
        postage: 0, //设置运费金额
        custom_form: [], //自定义留言
        cate_id: [],
        label_id: [],
        ensure_id:[],
        keyword: "",
        applicable_type: 1,
        unit_name: "",
        specs_id:0,
        store_info: "",
        bar_code:"",
        code:"",
        image: "",
        recommend_image: "",
        slider_image: [],
        description: "",
        ficti: 0,
        give_integral: 0,
        sort: 0,
        is_show: 1,
        is_hot: 0,
        is_benefit: 0,
        is_best: 0,
        is_new: 0,
        is_good: 0,
        is_postage: 0,
        is_sub: [],
        id: 0,
        spec_type: 0,
        video_link: "",
        temp_id: "",
        attrs: [],
        items: [
          {
            pic: "",
            price: 0,
            cost: 0,
            ot_price: 0,
            stock: 0,
            bar_code: "",
            code: "",
          },
        ],
        activity: ["默认", "秒杀", "砍价", "拼团"],
        couponName: [],
        header: [],
        selectRule: "",
        coupon_ids: [],
        command_word: "",
        delivery_type: ['1'],
        specs:[],
        recommend_list: [],
        brand_id: [],
        product_type:0,
        store_overview: ''
      },
      ruleList: [],
      templateList: [],
      createBnt: false,
      showIput: false,
      manyFormValidate: [],
      // 单规格表格data
      oneFormValidate: [
        {
          pic: "",
          price: 0,
		  settle_price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: "",
          code: "",
          weight: 0,
          volume: 0,
          brokerage: 0,
          brokerage_two: 0,
          vip_price: 0,
          virtual_list: [],
          write_times: 0, //核销次数
          write_valid: 1, //核销时效
          write_days: 1
        },
      ],
      images: [],
      imagesTable: "",
      currentTab: "1",
      isChoice: "",
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      modalPic: false,
      template: false,
      uploadList: [],
      treeSelect: [],
      labelSelect: [],
      ensureData:[],
      specsData:[],
      picTit: "",
      tableIndex: 0,
      ruleValidate: {
        store_name: [
          { required: true, message: "请输入课程名称", trigger: "blur" },
        ],
        cate_id: [
          {
            required: true,
            message: "请选择课程分类",
            trigger: "change",
            type: "array"
          },
        ],
        store_overview: [
          { required: true, message: "请输入课程概述", trigger: "blur" },
        ],
        keyword: [
          { required: true, message: "请输入课程关键字", trigger: "blur" },
        ],
        unit_name: [
          {
            required: true,
            message: "请输入单位",
            trigger: "change",
          },
        ],
        store_info: [
          { required: true, message: "请输入课程简介", trigger: "blur" },
        ],
        //image: [{ required: true, message: "请上传课程图", trigger: "change" }],
        slider_image: [
          {
            required: true,
            message: "请上传课程轮播图",
            type: "array",
            trigger: "change",
          },
        ],
        recommend_image: [
          {
            required: true,
            message: "请上传课程推荐图",
            trigger: "change",
          },
        ],
        spec_type: [
          { required: true, message: "请选择课程规格", trigger: "change" },
        ],
        selectRule: [
          { required: true, message: "请选择课程规格属性", trigger: "change" },
        ],
        give_integral: [{ type: "integer", message: "请输入整数" }],
        // delivery_type:[
        //   { required: true, type: 'array', min: 1, message: '请选择配送方式', trigger: 'change' },
        // ]
      },
      manyBrokerage: 0,
      manyBrokerageTwo: 0,
      manyVipPrice: 0,
      upload: {
        videoIng: false, // 是否显示进度条；
      },
      videoIng: false, // 是否显示进度条；
      progress: 0, // 进度条默认0
      videoLink: "",
      attrs: [],
      activity: { 默认: "colorBlue", 秒杀: "colorBlue", 砍价: "colorBlue", 拼团: "colorBlue" },
      couponName: [],
      updateIds: [],
      updateName: [],
      rakeBack: [
        {
          title: "一级返佣",
          slot: "brokerage",
          align: "center",
          width: 95,
        },
        {
          title: "二级返佣",
          slot: "brokerage_two",
          align: "center",
          width: 95,
        },
      ],
      member: [
        {
          title: "会员价",
          slot: "vip_price",
          align: "center",
          width: 95,
        },
      ],
      headerCarMy:{
        title: "卡密设置",
        slot: "fictitious",
        align: "center",
        width: 95,
      },
      columnsInstalM: [],
      moveIndex: "",
      goodsData:[],
      brandData:[],
      unitNameList:[],
      formBrand:{},
      attrsList:[],
      activeAtter:[],
      tabIndex: 0,
      tabName: "",
      attrData:[],
      datePickerOptions: {
        disabledDate (date) {
          return date && date.valueOf() < Date.now() - 86400000;
        }
      },
      openErp:false,
      merchantType:0, //0:平台商品；1:门店商品；2:供应商商品
      formList:[],
      storeColumns: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "门店图片",
          slot: "image",
          minWidth: 80,
        },
        {
          title: "门店分类",
          key: "cate_name",
          minWidth: 80,
        },
        {
          title: "门店名称",
          key: "name",
          minWidth: 80,
        },
        {
          title: "联系电话",
          key: "phone",
          minWidth: 90,
        },
        {
          title: "门店地址",
          key: "address",
          ellipsis: true,
          minWidth: 150,
        },
        {
          title: "营业时间",
          key: "day_time",
          minWidth: 120,
        },
        {
          title: "营业状态",
          key: "status_name",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          width: 100,
        }
      ]
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile","menuCollapse"]),
    labelWidth() {
      return this.isMobile ? undefined : 120;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
    labelBottom() {
      return this.isMobile ? undefined : 15;
    },
    startPickOptions() {
      const that = this;
      return {
        disabledDate(time) {
          if(that.formValidate.auto_off_time) {
            return(
                time.getTime() > new Date(that.formValidate.auto_off_time).getTime()
            )
          }
          return ''
        }
      }
    },
    endPickOptions() {
      const that = this;
      return {
        disabledDate(time) {
          if(that.formValidate.is_show == '1'){
            return time.getTime() < Date.now();
          }
          if(that.formValidate.auto_on_time) {
            return(
                time.getTime() < new Date(that.formValidate.auto_on_time).getTime()
            )
          }
          return ''
        }
      }
    }
  },
  created() {
    this.getSupplierList();
    this.columns = this.columns2.slice(1, 10);
    let data = JSON.parse(JSON.stringify(this.columns2));
    data.splice(8,2,this.headerCarMy);
    this.columnsCarMy = data;
    let fictitious = JSON.parse(JSON.stringify(this.columns2));
    fictitious.splice(8,2);
    this.columnsFictitious = fictitious;
    this.getToken();
    this.getErpConfig();
    // this.columnsInstall = this.columns2.slice(0, 4).concat(this.columnsInstall);
    // this.columnsInsta8 = this.columns2.slice(0, 4).concat(this.columnsInsta8);
  },
  mounted() {
    this.setCopyrightShow({ value: false });
    if ((this.$route.params.id !== "0" && this.$route.params.id) || this.$route.query.copy) {
      this.getInfo();
    } else if (this.$route.params.id === "0") {
      productCache()
          .then((res) => {
            let data = res.data.info;
            if (!Array.isArray(data)) {
              let cate_id = data.cate_id.map(Number);
              let label_id = data.label_id.map(Number);
              this.attrs = data.items || [];
              let ids = [];
              // let names = [];
              if (data.coupons) {
                data.coupons.map((item) => {
                  ids.push(item.id);
                  // names.push(item.title);
                });
                this.couponName = data.coupons;
              }
              this.storesList = data.stores || [];
              let brandIds = [];
              data.brand_id.forEach(item=>{
                brandIds.push(item.toString())
              })
              this.formValidate = data;
              this.formTypeList = data.custom_form_info || [];
              // this.couponName = data.coupons;
              // that.couponName = names;
              this.formValidate.brand_id = brandIds;
              this.formValidate.coupon_ids = ids;
              this.formValidate.is_limit = this.formValidate.is_limit?1:0;
              this.formValidate.limit_type = parseInt(this.formValidate.limit_type);
              this.formValidate.is_support_refund = parseInt(this.formValidate.is_support_refund);
              this.updateIds = ids;
              this.updateName = data.coupons;
              this.formValidate.cate_id = cate_id;
              this.dataLabel = data.label_id;
              this.storeDataLabel = data.store_label_id;
              this.specsList = data.specs;
              this.oneFormValidate = data.attrs;
              this.formValidate.header = [];
              this.generate(0);
              this.addmanyData(data.attrs);
              this.productTypeTap(2);
              this.columns = this.columns2.slice(1, 10);
              //this.manyFormValidate = data.attrs;
              this.formValidate.system_form_id = data.system_form_id || 0;
              if (this.formValidate.system_form_id) {
                this.customBtn = true;
              }
              this.spec_type = data.spec_type;
              if (data.spec_type === 0) {
                this.manyFormValidate = [];
              } else {
                this.createBnt = true;
                this.oneFormValidate = [
                  {
                    pic: data.slider_image[0],
                    price: 0,
					settle_price: 0,
                    cost: 0,
                    ot_price: 0,
                    stock: 0,
                    bar_code: "",
                    code: "",
                    weight: 0,
                    volume: 0,
                    brokerage: 0,
                    brokerage_two: 0,
                    vip_price: 0,
                    virtual_list: [],
                    write_times: 0, //核销次数
                    write_valid: 1, //核销时效
                    days: 1
                  },
                ];
              }
              this.spinShow = false;
            }
          })
          .catch((err) => {
            this.$Message.error(err.msg);
          });
    }
    if(this.$route.query.type){
      this.modals = true;
      this.type = this.$route.query.type;
    }else {
      this.type = 0;
    }
    this.goodsCategory();
    this.productGetRule();
    this.productGetTemplate();
    this.getBrandList();
    this.getAllUnit();
    this.uploadType();
    this.getProductAllEnsure();
    this.getProductAllSpecs();
    this.allFormList();
  },
  destroyed () {
    this.setCopyrightShow({ value: true });
  },
  methods: {
    ...mapMutations('admin/layout', [
      'setCopyrightShow'
    ]),
    changeForm(e){
      this.getSystemFormInfo(e,{type:1});
    },
    getSystemFormInfo(e,data){
      systemFormInfo(e,data).then(res=>{
        this.formTypeList = res.data.info;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    //删除门店
    delte(index){
      this.storesList.splice(index, 1)
    },
    //添加门店
    addStore(){
      this.storeModals = true;
    },
    //关闭门店弹窗
    cancelStore(){
      this.storeModals = false;
    },
    allFormList(){
      allSystemForm().then(res=>{
        this.formList = res.data;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    limitTap(e){
      if(e){
        this.formValidate.limit_type = (this.formValidate.is_limit && !this.formValidate.limit_type)?1:0
        this.formValidate.limit_num = (this.formValidate.is_limit && this.formValidate.limit_num == 0)?1:0
      }else{
        this.formValidate.limit_type = 0;
        this.formValidate.limit_num = 0;
      }
    },
    //erp配置
    getErpConfig(){
      erpConfig().then(res=>{
        this.openErp = res.data.open_erp;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    delSpecs(index){
      this.specsList.splice(index, 1);
    },
    addSpecs(){
      let obj = {'name':'','value':'','sort':0}
      this.specsList.push(obj)
    },
    specsInfo(e){
      this.specsData.forEach(item=>{
        if(item.id == e){
          this.specsList = item.specs
        }
      })
    },
    getProductAllSpecs(){
      productAllSpecs().then(res=>{
        this.specsData = res.data
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    getProductAllEnsure(){
      productAllEnsure().then(res=>{
        this.ensureData = res.data
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    //添加倒入卡密的值
    changeVirtual(e){
      this.virtualList = this.virtualList.concat(e);
    },
    //添加卡密
    addVirtual(index, name) {
      this.tabIndex = index;
      this.tabName = name;
      this.virtualListClear();
      this.$refs.addCarMy.fixedCar = {
        disk_info:'',
        stock:0
      }
      this.$refs.addCarMy.cartMyType = 1;
      this.carMyShow = true;
    },
    //确认提交卡密
    fixdBtn(e){
      if(e.cartMyType == 1){
        this.$set(this[this.tabName][this.tabIndex],"disk_info",e.disk_info);
        this.$set(this[this.tabName][this.tabIndex],"stock",Number(e.stock));
        this[this.tabName][this.tabIndex].virtual_list = [];
      }else{
        this.$set(this[this.tabName][this.tabIndex],"virtual_list",e.virtualList);
        this.$set(this[this.tabName][this.tabIndex],"stock",e.virtualList.length);
        this[this.tabName][this.tabIndex].disk_info = '';
      }
      this.carMyShow = false;
    },
    closeCarMy(){
      this.carMyShow = false;
    },
    //清空卡密
    virtualListClear(){
      this.virtualList = [
        {
          key: "",
          value: "",
        },
      ];
    },
    seeVirtual(data, name, index) {
      this.tabName = name;
      this.tabIndex = index;
      this.virtualListClear();
      this.$refs.addCarMy.fixedCar = {
        disk_info:'',
        stock:0
      }
      if (data.virtual_list && data.virtual_list.length) {
        this.$refs.addCarMy.cartMyType = 2;
        this.virtualList = data.virtual_list;
      } else if (data.disk_info) {
        this.$refs.addCarMy.cartMyType = 1;
        this.$refs.addCarMy.fixedCar.disk_info = data.disk_info;
        this.$refs.addCarMy.fixedCar.stock = data.stock;
      }
      this.carMyShow = true;
    },
    //动态添加组件
    addAssembly(){
      this.formValidate.custom_form.push({
        title: "",
        label: "text",
        value: "",
        status: 0,
      });
    },
    customMessBtn(e) {
      if (!e) {
        this.formValidate.system_form_id = 0;
      }
    },
    addcustom() {
      if (this.formValidate.custom_form.length > 9) {
        this.$Message.warning("最多添加10条");
      } else {
        this.addAssembly();
      }
    },
    delcustom(index) {
      this.formValidate.custom_form.splice(index, 1);
    },
    // 预售具体日期
    onchangeTime(e) {
      this.formValidate.presale_time = e;
    },
    //定时上架
    onchangeShow(e){
      this.formValidate.auto_on_time = e
    },
    //定时下架
    onchangeOff(e){
      this.formValidate.auto_off_time = e
    },
    // 获取供应商内容
    getSupplierList() {
      getSupplierList().then(async(res)=>{
        this.supplierList = res.data

      }).catch((res)=>{
        this.$Message.error(res.msg)
      })
    },
    //打开属性
    batchAttr(){
      this.attrShow = true;
      // if(!this.activeAtter.length){
      //  let data = this.attrs;
      //  data.map(el=>{
      //  			el.details = [];
      //  	el.detail.map(label=>{
      //  		el.details.push({
      //  			name:label,
      //  			select:false
      //  		})
      //  	})
      //  })
      //  this.attrsList = data;
      // }
    },
    //获取属性
    getAttr(){
      this.oneFormBatch[0].attr = '全部';
      let data = this.attrs;
      data.map(el=>{
        el.details = [];
        el.detail.map(label=>{
          el.details.push({
            name:label,
            select:false
          })
        })
      })
      this.attrsList = data;
    },
    //选中属性
    activeAttr(e){
      this.attrsList = e;
    },
    //关闭属性弹窗
    labelAttr(){
      this.attrShow = false;
    },
    //多属性为空
    manyEmpty(j){
      j.pic = '';
      j.price = 0;
	  j.settle_price = 0;
      j.cost = 0;
      j.ot_price = 0;
      j.stock = 0;
      j.bar_code = '';
      j.code = '';
      j.weight = 0;
      j.volume = 0;
      j.virtual_list = []
    },


    doCombination(arr) {
      var count = arr.length - 1; //数组长度(从0开始)
      var tmp = [];
      var totalArr = [];// 总数组

      return doCombinationCallback(arr, 0);//从第一个开始
      //js 没有静态数据，为了避免和外部数据混淆，需要使用闭包的形式
      function doCombinationCallback(arr, curr_index) {
        for(let val of arr[curr_index]) {
          tmp[curr_index] = val;//以curr_index为索引，加入数组
          //当前循环下标小于数组总长度，则需要继续调用方法
          if(curr_index < count) {
            doCombinationCallback(arr, curr_index + 1);//继续调用
          }else{
            totalArr.push(tmp.join(','));//(直接给push进去，push进去的不是值，而是值的地址)
          }

          //js  对象都是 地址引用(引用关系)，每次都需要重新初始化，否则 totalArr的数据都会是最后一次的 tmp 数据；
          let oldTmp = tmp;
          tmp = [];
          for(let index of oldTmp) {
            tmp.push(index);
          }
        }
        return totalArr;
      }
    },

    //提交属性值；
    subAttrs(e){
      let selectData = [];
      this.attrsList.forEach((el,index)=>{
        let obj = [];
        el.details.forEach((label)=>{
          if(label.select){
            obj.push(label.name);
          }
        })
        if(obj.length){
          selectData.push(obj)
        }
      })
      let newData = [];
      if(selectData.length){
        newData = this.doCombination(selectData);
      }
      this.attrShow = false;
      this.activeAtter = selectData;
      this.oneFormBatch[0].attr = newData.length?newData.join(';') : '全部';
      this.manyFormValidate.forEach(j=>{
        j.select = false;
        if(newData.length){
          newData.forEach(item=>{
            if(j.values.split('').length == item.split('').length){
              if(j.values == item){
                j.select = true;
              }
            }else{
              if(j.values.indexOf(item) != -1){
                j.select = true;
              }
            }
          })
        }else{
          j.select = true;
        }
      })
      this.$set(this,'manyFormValidate',this.manyFormValidate)
    },
    goodsOn(e){
      if(e==0 || e==1){
        this.formValidate.auto_on_time = '';
      }
    },
    goodsOff(e){
      if(!e){
        this.formValidate.auto_off_time = '';
      }
    },
    addBrand(){
      this.$refs.menusFrom.modals = true
      this.$refs.menusFrom.titleFrom = "添加品牌分类"
      this.formBrand = {
        sort:0,
        is_show:1
      }
      this.formBrand.fid = [0];
      this.$refs.menusFrom.type = 1
    },
    getAllUnit(){
      productAllUnit().then(res=>{
        this.unitNameList = res.data;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    addClass(){
      this.$modalForm(classesCreateApi()).then(() => this.goodsCategory());
    },
    addUnit(){
      this.$modalForm(productUnitCreate()).then(() => this.getAllUnit());
    },
    addStoreLabel(){
      this.$modalForm(productLabelAdd()).then(() => {});
    },
    productTypeTap(num,item){
      if(num == 1){
        if(this.$route.params.id) return this.$Message.error('商品类型不能切换！');
        this.formValidate.product_type = item.id;
      }
      if(this.formValidate.product_type && this.formValidate.product_type != 4){
        this.headTab = [
          { title: "基础信息", name: "1" },
          { title: "规格库存", name: "2" },
          { title: "商品详情", name: "3" },
          { title: "营销设置", name: "5" },
          { title: "其他设置", name: "6" },
        ]
        this.formValidate.postage = 0;
        // this.formValidate.supplier_id = 0;
      }else if(this.formValidate.product_type == 4){
        this.headTab = [
          { title: "基础信息", name: "1" },
          { title: "规格库存", name: "2" },
          { title: "商品详情", name: "3" },
          { title: "营销设置", name: "5" },
          { title: "其他设置", name: "6" },
          { title: "适用门店", name: "7" },
        ]
      }else{
        this.headTab = [
          { title: "基础信息", name: "1" },
          { title: "规格库存", name: "2" },
          { title: "商品详情", name: "3" },
          { title: "物流设置", name: "4" },
          { title: "营销设置", name: "5" },
          { title: "其他设置", name: "6" },
          { title: "适用门店", name: "7" },
        ]
      }
    },
    closeLabel(label){
      let index = this.dataLabel.indexOf(this.dataLabel.filter(d=>d.id == label.id)[0]);
      this.dataLabel.splice(index,1);
    },
    activeData(dataLabel){
      this.labelShow = false;
      this.dataLabel = dataLabel;
    },
    openLabel(row) {
      this.labelShow = true;
      this.$refs.userLabel.userLabel(JSON.parse(JSON.stringify(this.dataLabel)));
    },
    // 标签弹窗关闭
    labelClose() {
      this.labelShow = false;
    },
    closeStoreLabel(label){
      let index = this.storeDataLabel.indexOf(this.storeDataLabel.filter(d=>d.id == label.id)[0]);
      this.storeDataLabel.splice(index,1);
    },
    activeStoreData(storeDataLabel){
      this.storeLabelShow = false;
      this.storeDataLabel = storeDataLabel;
    },
    openStoreLabel(row) {
      this.storeLabelShow = true;
      this.$refs.storeLabel.storeLabel(JSON.parse(JSON.stringify(this.storeDataLabel)));
    },
    // 标签弹窗关闭
    storeLabelClose() {
      this.storeLabelShow = false;
    },
    // 品牌列表
    getBrandList(){
      brandList().then(res=>{
        //initBran()函数作用iview中规定value必须是字符串，后台返回成了数字，用于处理这个，给了个递归；
        this.initBran(res.data);
        this.brandData = res.data
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    initBran(data){
      data.map(item=>{
        item.value = item.value.toString();
        if(item.children && item.children.length){
          this.initBran(item.children);
        }
      })
    },
    getProductId(e){
      this.goodsModals = false;
      let nArr = this.goodsData.concat(e).filter((element,index,self)=>{
        return self.findIndex(x=>x.product_id==element.product_id) == index
      })
      this.goodsData = nArr.slice(0,12);
    },
    goodCancel () {
      this.goodsModals = false;
    },
    goodsTap(){
      this.goodsModals = true;
      this.$refs.goodslist.handleSelectAll();
    },
    bindDelete (index) {
      this.goodsData.splice(index, 1)
    },
    cancel() {
      this.$router.push({path:this.roterPre + '/product/product_list'});
    },

    videoSaveToUrl(file) {
      let imgTypeArr = ["video/mp4"];
      let imgType = imgTypeArr.indexOf(file.type) !== -1
      if (!imgType) {
        return this.$Message.warning({
          content:  '文件  ' + file.name + '  格式不正确, 请选择格式正确的视频',
          duration: 5
        });
      }
      uploadByPieces({
        randoms: "", // 随机数，这里作为给后端处理分片的标识 根据项目看情况 是否要加
        file: file, // 视频实体
        pieceSize: 3, // 分片大小
        success: (data) => {
          this.formValidate.video_link = data.file_path;
          this.progress = 100;
        },
        error: (e) => {
          this.$Message.error(e.msg);
        },
        uploading: (chunk, allChunk) => {
          this.videoIng = true;
          let st = Math.floor((chunk / allChunk) * 100);
          this.progress = st;
        },
      });
      return false;
    },

    // 上传头部token
    getToken() {
      this.header["Authori-zation"] = "Bearer " + util.cookies.get("token");
    },
    // beforeUpload() {
    //   this.uploadData = {};
    //   let promise = new Promise((resolve) => {
    //     this.$nextTick(function () {
    //       resolve(true);
    //     });
    //   });
    //   return promise;
    // },
    // 上传成功
    handleSuccess(res, file, fileList) {
      if (res.status === 200) {
        this.formValidate.video_link = res.data.src;
        this.$Message.success(res.msg);
      } else {
        this.$Message.error(res.msg);
      }
    },
    //获取视频上传类型
    uploadType() {
      uploadType().then((res) => {
        this.upload_type = res.data.upload_type;
      });
    },
    getEditorContent(data) {
      this.content = data;
    },
    infoData(data){
      this.storesList = data.stores || [];
      let cate_id = data.cate_id.map(Number);
      this.attrs = data.items || [];
      let ids = [];
      data.coupons.map((item) => {
        ids.push(item.id);
      });
      this.goodsData = data.recommend_list;
      if(data.auto_off_time){
        this.off_show = 1;
      }else{
        this.off_show = 0;
      }
      let brandIds = [];
      data.brand_id.forEach(item=>{
        brandIds.push(item.toString())
      })
      this.formValidate = data;
      this.formTypeList = data.custom_form_info;
      this.formValidate.brand_id = brandIds;
      // this.formValidate.supplier_id = data.relation_id;
      this.formValidate.is_limit = this.formValidate.is_limit?1:0;
      this.formValidate.limit_type = parseInt(data.limit_type);
      this.formValidate.is_support_refund = parseInt(this.formValidate.is_support_refund);
      this.contents = data.description;
      this.couponName = data.coupons;
      this.formValidate.coupon_ids = ids;
      this.updateIds = ids;
      this.updateName = data.coupons;
      this.formValidate.cate_id = cate_id;
      this.dataLabel = data.label_id;
      this.storeDataLabel = data.store_label_id;
      this.specsList = data.specs;
      if(data.attr){
        this.oneFormValidate = [data.attr];
      }
      this.formValidate.header = [];
	  this.generate(0);
      //this.manyFormValidate = data.attrs;
      this.addmanyData(data.attrs);
      this.productTypeTap(2);
      this.formValidate.system_form_id = data.system_form_id || 0;
      if (this.formValidate.system_form_id) {
        this.customBtn = true;
      }

      this.spec_type = data.spec_type;
      if (data.spec_type === 0) {
        this.manyFormValidate = [];
      } else {
        this.createBnt = true;
        this.oneFormValidate = [
          {
            pic: "",
            price: 0,
			settle_price: 0,
            cost: 0,
            ot_price: 0,
            stock: 0,
            bar_code: "",
            code: "",
            weight: 0,
            volume: 0,
            brokerage: 0,
            brokerage_two: 0,
            vip_price: 0,
            virtual_list: [],
            write_times: 0, //核销次数
            write_valid: 1, //核销时效
            days: 1
          },
        ];
      }
    },
    //关闭淘宝弹窗并生成数据；
    onClose(data){
      this.modals = false;
      this.infoData(data);
    },

    checkMove(evt) {
      this.moveIndex = evt.draggedContext.index;
    },
    end() {
      this.moveIndex = "";
    },
    checkAllGroupChange(data) {
      this.checkAllGroup(data);
    },
    checkAllGroup(data) {
      if (this.formValidate.spec_type === 0) {
        if (data.indexOf(0) > -1) {
          this.columnsInstall = this.columns2.slice(1, 5).concat(this.member);
        } else if (data.indexOf(1) > -1) {
          this.columnsInstall = this.columns2.slice(1, 5).concat(this.rakeBack);
        } else {
          this.columnsInstall = this.columns2.slice(1, 5);
        }
        if (data.length === 2) {
          this.columnsInstall = this.columns2
              .slice(1, 5)
              .concat(this.rakeBack)
              .concat(this.member);
        }
      } else {
        if (data.indexOf(0) > -1) {
          this.columnsInstal2 = this.columnsInstalM
              .slice(0, 4)
              .concat(this.member);
        } else if (data.indexOf(1) > -1) {
          this.columnsInstal2 = this.columnsInstalM
              .slice(0, 4)
              .concat(this.rakeBack);
        } else {
          this.columnsInstal2 = this.columnsInstalM.slice(0, 4);
        }
        if (data.length === 2) {
          this.columnsInstal2 = this.columnsInstalM
              .slice(0, 4)
              .concat(this.rakeBack)
              .concat(this.member);
        }
      }
    },
    // 添加优惠券
    addCoupon() {
      this.$refs.couponTemplates.isTemplate = true;
      this.$refs.couponTemplates.tableList();
    },
    //对象数组去重；
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1));
    },
    nameId(id, names) {
      this.formValidate.coupon_ids = id;
      this.couponName = this.unique(names);
    },
    handleClose(name) {
      let index = this.couponName.indexOf(name);
      this.couponName.splice(index, 1);
      let couponIds = this.formValidate.coupon_ids;
      couponIds.splice(index, 1);
      this.updateIds = couponIds;
      this.updateName = this.couponName;
    },
    getStoreId (data) {
      this.storeModals = false;
      let list = this.storesList.concat(data);
      let uni = this.unique(list);
      this.storesList = uni;
    },
    // 运费模板
    getList() {
      this.productGetTemplate();
    },
    // 添加运费模板
    addTemp() {
      this.$refs.templates.isTemplate = true;
    },
	//查看、编辑运费模板
	editTemp(){
	  this.$refs.templates.isTemplate = true;
	  this.$refs.templates.editFrom(this.formValidate.temp_id);
	},
    // 删除视频；
    delVideo() {
      let that = this;
      that.$set(that.formValidate, "video_link", "");
      that.$set(that, "progress", 0);
      that.videoIng = false;
      that.upload.videoIng = false;
      that.$refs.refid.value ='';
    },
    zh_uploadFile() {
      if (this.seletVideo == 1) {
        if(this.videoLink && this.$getFileType(this.videoLink) == 'video'){
          this.formValidate.video_link = this.videoLink;
        }else{
          return this.$Message.error("请输入正确的视频链接")
        }
      } else {
        this.$refs.refid.click();
      }
    },
    zh_uploadFile_change(evfile) {
      let that = this;
      let suffix = evfile.target.files[0].name.substr(
          evfile.target.files[0].name.indexOf(".")
      );
      if (suffix.indexOf(".mp4") === -1) {
        return that.$Message.error("只能上传MP4文件");
      }
      productGetTempKeysApi()
          .then((res) => {
            that.$videoCloud
                .videoUpload({
                  type: res.data.type,
                  evfile: evfile,
                  res: res,
                  uploading(status, progress) {
                    that.upload.videoIng = status;
                    if (res.status == 200) {
                      that.progress = 100;
                    }
                  },
                })
                .then((res) => {
                  that.formValidate.video_link = res.url;
                  that.$Message.success("视频上传成功");
                  that.upload.videoIng = false;
                })
                .catch((res) => {
                  that.$Message.error(res);
                });
          })
          .catch((res) => {
            that.$Message.error(res.msg);
          });
    },
    // 上一页；
    upTab() {
      if(this.currentTab==5&&this.formValidate.product_type!=0){
        this.currentTab = (Number(this.currentTab) - 2).toString();
      }else{
        this.currentTab = (Number(this.currentTab) - 1).toString();
      }
    },
    // 下一页；
    downTab(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if(this.formValidate.is_show == 2 && !this.formValidate.auto_on_time){
            return this.$Message.warning("请填写定时上架时间");
          }
          if(this.off_show == 1 && !this.formValidate.auto_off_time){
            return this.$Message.warning("请填写定时下架时间");
          }
          if(this.currentTab==4 && !this.formValidate.delivery_type.length){
            return this.$Message.warning("请选择配送方式");
          }
          if(this.currentTab==3 && this.formValidate.product_type!=0){
            this.currentTab = (Number(this.currentTab) + 2).toString();
          }else{
            this.currentTab = (Number(this.currentTab) + 1).toString();
          }
        }else{
          this.$Message.warning("请完善数据");
        }
      })
    },
    // 属性弹窗回调函数；
    userSearchs() {
      this.productGetRule();
    },
    // 添加规则；
    addRule() {
      this.$refs.addattr.modal = true;
    },
    // 批量设置分佣；
    brokerageSetUp() {
      let that = this;
      if (that.formValidate.is_sub.indexOf(1) > -1) {
        if (that.manyBrokerage <= 0 || that.manyBrokerageTwo <= 0) {
          return that.$Message.error("请填写返佣金额后进行批量添加");
        }
      } else if (that.formValidate.is_sub.indexOf(0) > -1) {
        if (that.manyVipPrice <= 0) {
          return that.$Message.error("请填写会员价后进行批量添加");
        }
      }
      if (this.formValidate.is_sub.length === 2) {
        if (
            that.manyBrokerage <= 0 ||
            that.manyBrokerageTwo <= 0 ||
            that.manyVipPrice <= 0
        ) {
          return that.$Message.error("请填写完金额后进行批量添加");
        }
      }
      for (let val of that.manyFormValidate) {
        this.$set(val, "brokerage", that.manyBrokerage);
        this.$set(val, "brokerage_two", that.manyBrokerageTwo);
        this.$set(val, "vip_price", that.manyVipPrice);
      }
      // let that = this;
      // if (that.manyBrokerage <= 0 || that.manyBrokerageTwo <= 0) {
      //     return that.$Message.error('请填写返佣金额在进行批量添加');
      // } else {
      //     for (let val of that.manyFormValidate) {
      //         this.$set(val, 'brokerage', that.manyBrokerage);
      //         this.$set(val, 'brokerage_two', that.manyBrokerageTwo);
      //     }
      // }
    },
    // 批量设置会员价
    vipPriceSetUp() {
      let that = this;
      if (that.manyVipPrice <= 0) {
        return that.$Message.error("请填写会员价在进行批量添加");
      } else {
        for (let val of that.manyFormValidate) {
          this.$set(val, "vip_price", that.manyVipPrice);
        }
      }
    },
    batchDel() {
      this.oneFormBatch = [
        {
          attr:"全部",
          pic: "",
          price: 0,
		  settle_price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: "",
          code: "",
          weight: 0,
          volume: 0,
          virtualList:[],
          disk_info:'',
          quota: 0
        },
      ];
      this.activeAtter=[];
      for (let val of this.manyFormValidate) {
        val.select = true;
      }
    },
    confirm() {
      let that = this;
      that.createBnt = true;
      if (that.formValidate.selectRule.trim().length <= 0) {
        return that.$Message.error("请选择属性");
      }
      that.ruleList.forEach(function (item, index) {
        if (item.rule_name === that.formValidate.selectRule) {
          that.attrs = item.rule_value;
        }
      });
    },
    // 获取商品属性模板；
    productGetRule() {
      classesGetRuleApi().then((res) => {
        this.ruleList = res.data;
      });
    },
    // 获取运费模板；
    productGetTemplate() {
	  let id = this.$route.params.id;
      productGetTemplateApi({id:id}).then((res) => {
        this.templateList = res.data;
      });
    },
    // 删除表格中的属性
    delAttrTable(index) {
      let id = this.$route.params.id;
      if (id) {
        checkActivityApi(id)
            .then((res) => {
              this.manyFormValidate.splice(index, 1);
              this.$Message.success(res.msg);
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
      } else {
        this.manyFormValidate.splice(index, 1);
      }
    },
    // 批量添加
    batchAdd() {
        console.log('触发批量设置');
      for (let val of this.manyFormValidate) {
        //this.manyEmpty(val);
        if(val.select){
          if (this.oneFormBatch[0].pic) {
            this.$set(val, "pic", this.oneFormBatch[0].pic);
          }
          if (this.oneFormBatch[0].price > 0) {
            this.$set(val, "price", this.oneFormBatch[0].price);
          }
		  if (this.oneFormBatch[0].settle_price > 0 && this.merchantType == 2) {
		    this.$set(val, "settle_price", this.oneFormBatch[0].settle_price);
		  }
          if (this.oneFormBatch[0].cost > 0) {
            this.$set(val, "cost", this.oneFormBatch[0].cost);
          }
          if (this.oneFormBatch[0].ot_price > 0) {
            this.$set(val, "ot_price", this.oneFormBatch[0].ot_price);
          }
          if (this.oneFormBatch[0].stock > 0) {
            this.$set(val, "stock", this.oneFormBatch[0].stock);
          }
          if (this.oneFormBatch[0].bar_code !== "") {
            this.$set(val, "bar_code", this.oneFormBatch[0].bar_code);
          }
          if (this.oneFormBatch[0].code !== "") {
            this.$set(val, "code", this.oneFormBatch[0].code);
          }
          if (this.oneFormBatch[0].quota > 0) {
            this.$set(val, "quota", this.oneFormBatch[0].quota);
          }
          if (this.oneFormBatch[0].volume > 0) {
            this.$set(val, "volume", this.oneFormBatch[0].volume);
          }
          if(this.formValidate.product_type == 1){
            if(this.oneFormBatch[0].virtual_list && this.oneFormBatch[0].virtual_list.length){
              this.$set(val, "virtual_list", this.oneFormBatch[0].virtual_list);
            }else if(this.oneFormBatch[0].disk_info){
              this.$refs.addCarMy.cartMyType = 1;
              this.$set(val, "disk_info", this.oneFormBatch[0].disk_info);
            }
          }
        }
      }
    },
    // 添加按钮
    addBtn() {
      let id = this.$route.params.id;
      checkActivityApi(id)
          .then((res) => {
            this.clearAttr();
            this.createBnt = false;
            this.showIput = true;
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
    },
    addmanyData(data,type){
	  if(type == 1){
		  this.oneFormBatch[0].pic = this.formValidate.image
		  data.forEach(item=>{
		    item.select = true,
			item.pic = this.formValidate.image
		  })
	  }else{
		  data.forEach(item=>{
		    item.select = true
		  })
	  }
      this.manyFormValidate = data;
    },
    // 立即生成
    generate(type) {
      generateAttrApi({ attrs: this.attrs,product_type: this.formValidate.product_type }, this.formValidate.id, type)
          .then((res) => {
            let info = res.data.info,
                header1 = JSON.parse(JSON.stringify(info.header));
            if ((this.$route.params.id !== "0" && this.type != -1) || type == 1) {
              this.addmanyData(info.value,type);
            }
            this.formValidate.header = header1;
            this.attrData = res.data.info.attr;
            let header = info.header;
            header.pop();
            this.columnsInstalM = info.header;
            this.checkAllGroup(this.formValidate.is_sub);
            if (!this.$route.params.id && this.formValidate.spec_type === 1 && this.type != -1) {
              this.manyFormValidate.map((item) => {
                item.pic = this.formValidate.slider_image[0];
              });
              this.oneFormBatch[0].pic = this.formValidate.slider_image[0];
            } else if (this.$route.params.id) {
              this.manyFormValidate.map((item) => {
                if (!item.pic) {
                  item.pic = this.formValidate.slider_image[0];
                }
              });
              this.oneFormBatch[0].pic = this.formValidate.slider_image[0];
            }
            this.getAttr();
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
    },
    // 取消
    offAttrName() {
      this.showIput = false;
      this.createBnt = true;
    },
    clearAttr() {
      this.formDynamic.attrsName = "";
      this.formDynamic.attrsVal = "";
    },
    // 删除规格
    handleRemoveRole(index) {
      this.attrs.splice(index, 1);
      this.manyFormValidate.splice(index, 1);
    },
    // 删除属性
    handleRemove2(item, index) {
      item.splice(index, 1);
    },
    // 添加规则名称
    createAttrName() {
      if (this.formDynamic.attrsName && this.formDynamic.attrsVal) {
        let data = {
          value: this.formDynamic.attrsName,
          detail: [this.formDynamic.attrsVal],
        };
        this.attrs.push(data);
        var hash = {};
        this.attrs = this.attrs.reduce(function (item, next) {
          /* eslint-disable */
          hash[next.value] ? "" : (hash[next.value] = true && item.push(next));
          return item;
        }, []);
        this.clearAttr();
        this.showIput = false;
        this.createBnt = true;
      } else {
        this.$Message.warning("请添加完整的规格！");
      }
    },
    // 添加属性
    createAttr(num, idx) {
      if (num) {
        this.attrs[idx].detail.push(num);
        var hash = {};
        this.attrs[idx].detail = this.attrs[idx].detail.reduce(function (
                item,
                next
            ) {
              /* eslint-disable */
              hash[next] ? "" : (hash[next] = true && item.push(next));
              return item;
            },
            []);
      } else {
        this.$Message.warning("请添加属性");
      }
    },
    // 商品分类；
    goodsCategory() {
      cascaderListApi(1)
          .then((res) => {
            this.treeSelect = res.data;
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
    },
    //视视上传类型
    changeVideo(e) {
      this.formValidate.video_link = "";
      this.videoLink = "";
    },
    // 改变规格
    changeSpec() {
      this.formValidate.is_sub = [];
      let id = this.$route.params.id;
      if (id) {
        checkActivityApi(id)
            .then((res) => {})
            .catch((res) => {
              this.formValidate.spec_type = this.spec_type;
              this.$Message.error(res.msg);
            });
      }
    },
    // 详情
    getInfo() {
      let that = this;
      that.spinShow = true;
      productInfoApi(that.$route.params.id || this.$route.query.copy)
          .then(async (res) => {
            let data = res.data.productInfo;
            this.merchantType = parseInt(data.type);
			if(this.merchantType == 2){
				let obj = {
					  title: "结算价",
					  slot: "settle_price",
					  align: "center",
					  minWidth: 95,
					}
				this.columns2.splice(3,0,obj)
			}
            this.infoData(data);
            // let cate_id = data.cate_id.map(Number);
            // let label_id = data.label_id.map(Number);
            // this.attrs = data.items || [];
            // let ids = [];
            // data.coupons.map((item) => {
            //   ids.push(item.id);
            // });
            // that.formValidate = data;
            // that.couponName = data.coupons;
            // that.formValidate.coupon_ids = ids;
            // that.updateIds = ids;
            // that.updateName = data.coupons;
            // that.formValidate.cate_id = cate_id;
            // that.formValidate.label_id = label_id;
            // that.oneFormValidate = [data.attr];
            // that.formValidate.header = [];
            // that.manyFormValidate = data.attrs;
            // that.generate(0);
            // that.spec_type = data.spec_type;
            // if (data.spec_type === 0) {
            //   that.manyFormValidate = [];
            // } else {
            //   that.createBnt = true;
            //   that.oneFormValidate = [
            //     {
            //       pic: data.image,
            //       price: 0,
            //       cost: 0,
            //       ot_price: 0,
            //       stock: 0,
            //       bar_code: "",
            //       weight: 0,
            //       volume: 0,
            //       brokerage: 0,
            //       brokerage_two: 0,
            //       vip_price: 0,
            //     },
            //   ];
            // }
            this.spinShow = false;
          })
          .catch((res) => {
            this.spinShow = false;
            this.$Message.error(res.msg);
          });
    },
    // tab切换
    onhangeTab(name) {
      this.currentTab = name;
    },
    handleRemove(i) {
      this.images.splice(i, 1);
      this.formValidate.slider_image.splice(i, 1);
      this.oneFormValidate[0].pic = this.formValidate.slider_image[0];
    },
    // 关闭图片上传模态框
    changeCancel(msg) {
      this.modalPic = false;
    },
    // 点击商品图
    modalPicTap(tit, picTit, index) {
      this.modalPic = true;
      this.isChoice = tit === "dan" ? "单选" : "多选";
      this.picTit = picTit;
      this.tableIndex = index;
    },
    // 获取单张图片信息
    getPic(pc) {
      switch (this.picTit) {
        case "danFrom":
          this.formValidate.image = pc.att_dir;
          if (!this.$route.params.id) {
            if (this.formValidate.spec_type === 0) {
              this.oneFormValidate[0].pic = pc.att_dir;
            } else {
              this.manyFormValidate.map((item) => {
                item.pic = pc.att_dir;
              });
              this.oneFormBatch[0].pic = pc.att_dir;
            }
          }
          break;
        case "danTable":
          this.oneFormValidate[this.tableIndex].pic = pc.att_dir;
          break;
        case "duopi":
          this.oneFormBatch[this.tableIndex].pic = pc.att_dir;
          break;
        case "recommend_image":
          this.formValidate.recommend_image = pc.att_dir;
          break;
        case "video":
          this.formValidate.video_link = pc.att_dir;
          break;
        default:
          this.manyFormValidate[this.tableIndex].pic = pc.att_dir;
      }
      this.modalPic = false;
    },
    // 获取多张图信息
    getPicD(pc) {
      this.images = pc;
      this.images.map((item) => {
        this.formValidate.slider_image.push(item.att_dir);
        this.formValidate.slider_image = this.formValidate.slider_image.splice(
            0,
            10
        );
      });
      this.oneFormValidate[0].pic = this.formValidate.slider_image[0];
      this.modalPic = false;
    },
    // 提交
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (!this.formValidate.store_name.trim()) {
            return this.$Message.warning("基础信息-课程名称不能为空");
          }
          if(this.formValidate.is_show == 2 && !this.formValidate.auto_on_time){
            return this.$Message.warning("基础信息-定时上架时间不能为空");
          }
          if(this.off_show == 1 && !this.formValidate.auto_off_time){
            return this.$Message.warning("基础信息-定时下架时间不能为空");
          }
          if(this.formValidate.product_type == 4 && !this.oneFormValidate[0].write_times){
            return this.$Message.warning("规格库存-核销次数必须大于0");
          }
          if(this.formValidate.product_type == 4 && this.oneFormValidate[0].write_valid==2 && !this.oneFormValidate[0].days){
            return this.$Message.warning("规格库存-有效天数必须大于0");
          }
          if(this.formValidate.product_type == 4 && this.oneFormValidate[0].write_valid==3 && !this.oneFormValidate[0].section_time.length){
            return this.$Message.warning("规格库存-请输入固定有效期");
          }
          if(this.formValidate.freight==2&&this.formValidate.product_type == 0&&this.formValidate.postage<=0){
            return this.$Message.warning("物流设置-固定邮费不能为0");
          }
          if(this.formValidate.freight==3&&this.formValidate.product_type == 0&&!this.formValidate.temp_id){
            return this.$Message.warning("物流设置-运费模板不能为空");
          }
          if(this.formValidate.product_type==0&&this.formValidate.is_presale_product&&!this.formValidate.presale_time[0]){
            return this.$Message.warning("营销设置-预售时间不能为空");
          }
          // for (let i = 0; i < this.formValidate.custom_form.length; i++) {
          //   const element = this.formValidate.custom_form[i];
          //   if (!element.title) {
          //     return this.$Message.warning("其他设置-留言标题不能为空");
          //   }
          // }
          if(this.customBtn && this.formValidate.system_form_id == 0){
            return this.$Message.warning('其他设置-请选择自定义表单模板');
          }
          let storeId = []
          this.storesList.forEach(item=>{
            storeId.push(item.id)
          })
          if(this.formValidate.applicable_type==2 && !storeId.length){
            return this.$Message.warning('适用门店-请选择适用门店');
          }
          this.formValidate.applicable_store_id = storeId;
          this.formValidate.type = this.type;
          if (this.formValidate.spec_type === 0) {
            this.formValidate.attrs = this.oneFormValidate;
            this.formValidate.header = [];
            this.formValidate.items = [];
          } else {
            this.formValidate.items = this.attrs;
            this.formValidate.attrs = this.manyFormValidate;
          }
          if (
              this.formValidate.spec_type === 1 &&
              this.manyFormValidate.length === 0
          ) {
            return this.$Message.warning("规格库存-请点击生成多规格");
            // return this.$Message.warning('请点击生成规格！');
          }
          let item = this.formValidate.attrs;
          if ( this.formValidate.is_sub.indexOf(1) != -1 ) {
            for (let i = 0; i < item.length; i++) {
              if (
                  item[i].brokerage === null ||
                  item[i].brokerage_two === null
              ) {
                return this.$Message.warning("营销设置- 一二级返佣不能为空");
              }
            }
          }
          if(this.formValidate.is_sub.indexOf(0) != -1){
            for (let i = 0; i < item.length; i++) {
              if (item[i].vip_price === null) {
                return this.$Message.warning("营销设置-会员价不能为空");
              }
              if (item[i].vip_price === 0) {
                return this.$Message.warning("营销设置-会员价不能为0");
              }
            }
          }
          if (this.formValidate.is_sub.length === 2) {
            for (let i = 0; i < item.length; i++) {
              if (
                  item[i].brokerage === null ||
                  item[i].brokerage_two === null ||
                  item[i].vip_price === null
              ) {
                return this.$Message.error(
                    "营销设置- 一二级返佣和会员价不能为空"
                );
              }
            }
          }
          for (let i = 0; i < this.specsList.length; i++){
            let data = this.specsList[i];
            if(!data.name.trim()){
              return this.$Message.error('请输入参数名称');
            }
            if(!data.value.trim()){
              return this.$Message.error('请输入参数值');
            }
          }
          if(!this.formValidate.product_type){
            this.formValidate.is_support_refund = 1;
          }
          this.openSubimit = false;
          this.formValidate.description = this.formatRichText(this.content);
          let goodsId = [];
          this.goodsData.forEach(item=>{
            goodsId.push(item.product_id)
          })
          this.formValidate.recommend_list = goodsId;
          // 用户标签
          let activeIds = [];
          this.dataLabel.forEach((item)=>{
            activeIds.push(item.id)
          });
          this.formValidate.label_id = activeIds
          // 商品标签
          let storeActiveIds = [];
          this.storeDataLabel.forEach((item)=>{
            storeActiveIds.push(item.id)
          });
          this.formValidate.store_label_id = storeActiveIds
          // 商品参数
          this.formValidate.specs = this.specsList;
          if (this.$route.query.copy) {
            this.formValidate.id = 0;
            this.formValidate.soure_link = '';
          }
          if(this.formValidate.product_type == 4){
            this.formValidate.delivery_type = 2
          }
          classesAddApi(this.formValidate)
              .then(async (res) => {
                this.openSubimit = true;
                this.$Message.success(res.msg);
                if (this.$route.params.id === "0") {
                  cacheDelete().catch((err) => {
                    this.$Message.error(err.msg);
                  });
                }
                setTimeout(() => {
                  this.$router.push({ path: this.roterPre + "/classes/classes_list" });
                }, 500);
              })
              .catch((res) => {
                this.openSubimit = false;
                this.$Message.error(res.msg);
              });
        } else {
          if (!this.formValidate.store_name) {
            return this.$Message.warning("基础信息-课程名称不能为空");
          } else if (!this.formValidate.cate_id.length) {
            return this.$Message.warning("基础信息-课程分类不能为空");
          } else if (!this.formValidate.unit_name) {
            // return this.$Message.warning("基础信息-课程单位不能为空");
          } else if (!this.formValidate.slider_image.length) {
            return this.$Message.warning("基础信息-课程轮播图不能为空");
          }
          //    if(!this.formValidate.store_name || !this.formValidate.cate_id || !this.formValidate.keyword
          //    || !this.formValidate.unit_name || !this.formValidate.store_info
          //        || !this.formValidate.image || !this.formValidate.slider_image){
          //        this.$Message.warning("请填写完整商品信息！");
          //    }
        }
      });
    },
    changeTemplate(msg) {
      this.template = msg;
    },
    // 表单验证
    validate(prop, status, error) {
      if (status === false) {
        this.$Message.warning(error);
      }
    },
    // 移动
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    handleDragOver(e) {
      e.dataTransfer.dropEffect = "move";
    },
    handleDragEnter(e, item) {
      e.dataTransfer.effectAllowed = "move";
      if (item === this.dragging) {
        return;
      }
      const newItems = [...this.formValidate.slider_image];
      const src = newItems.indexOf(this.dragging);
      const dst = newItems.indexOf(item);
      newItems.splice(dst, 0, ...newItems.splice(src, 1));
      this.formValidate.slider_image = newItems;
    },
    // 添加自定义弹窗
    addCustomDialog(editorId) {
      window.UE.registerUI(
          "test-dialog",
          function (editor, uiName) {
            // 创建 dialog
            let dialog = new window.UE.ui.Dialog({
              iframeUrl: "/admin/widget.images/index.html?fodder=dialog",
              editor: editor,
              name: uiName,
              title: "上传图片",
              cssRules: "width:1200px;height:500px;padding:20px;",
            });
            this.dialog = dialog;
            let btn = new window.UE.ui.Button({
              name: "dialog-button",
              title: "上传图片",
              cssRules: `background-image: url(https://cdn.oss.9gt.net/prov1.1/1/icons.png);background-position: -726px -77px;`,
              onclick: function () {
                // 渲染dialog
                dialog.render();
                dialog.open();
              },
            });
            return btn;
          },
          37
      );
      window.UE.registerUI(
          "video-dialog",
          function (editor, uiName) {
            let dialog = new window.UE.ui.Dialog({
              iframeUrl: "/admin/widget.video/index.html?fodder=video",
              editor: editor,
              name: uiName,
              title: "上传视频",
              cssRules: "width:1000px;height:500px;padding:20px;",
            });
            this.dialog = dialog;
            let btn = new window.UE.ui.Button({
              name: "video-button",
              title: "上传视频",
              cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -320px -20px;`,
              onclick: function () {
                // 渲染dialog
                dialog.render();
                dialog.open();
              },
            });
            return btn;
          },
          38
      );
    },
    formatRichText(html) {
      let newContent = html.replace(/<img[^>]*>/gi, function (match, capture) {
        match = match
            .replace(/style="[^"]+"/gi, "")
            .replace(/style='[^']+'/gi, "");
        match = match
            .replace(/width="[^"]+"/gi, "")
            .replace(/width='[^']+'/gi, "");
        match = match
            .replace(/height="[^"]+"/gi, "")
            .replace(/height='[^']+'/gi, "");
        return match;
      });
      newContent = newContent.replace(
          /style="[^"]+"/gi,
          function (match, capture) {
            match = match
                .replace(/width:[^;]+;/gi, "max-width:100%;")
                .replace(/width:[^;]+;/gi, "max-width:100%;");
            return match;
          }
      );
      // newContent = newContent.replace(/<br[^>]*\/>/gi, "");
      newContent = newContent.replace(
          /\<img/gi,
          '<img style="max-width:100%;height:auto;display:block;margin-top:0;margin-bottom:0;"'
      );
      return newContent;
    },
  },
};
</script>
<style scoped lang="stylus">
.storeTable img{
  width 36px;
  height 36px;
}
.storeTable /deep/.ivu-table-wrapper{
  border:0!important
}
.radioGroup .ivu-radio-wrapper{
  margin-right 20px;
}
.video-style {
  width: 100%;
  height: 100% !important;
  border-radius: 10px;
}
.select-add {
  width: 200px;
  margin-left: 6px;
  margin-right: 10px
}
.input-display {
  display: none
}
.width-add {
  width:200px;
}
.custom-input {
  width: 100px;
  margin-right: 10px
}
.asterisk{
  position relative;
  .asteriskInfo{
    position absolute;
    color #ed4014;
    left 73px;
    top 8px;
    &.on{
      left:62px;
    }
    &.on2{
      left:50px;
    }
    &.on3{
      left:30px;
    }
  }
}
.specsList {
  /deep/.ivu-table-header table{
    border:0!important
  }
  /deep/.ivu-table-header thead tr th{
    padding 0!important
    background-color #EEEEEE!important;
  }
  /deep/.ivu-table-cell{
    padding 0!important
  }
  /deep/.ivu-table-border th, /deep/.ivu-table-border td{
    border-right unset;
  }
  /deep/.ivu-table td{
    height 59px;
  }
  &.on{
    width 50% !important;
    /deep/.ivu-table{
      width 100% !important;
    }
    /deep/.ivu-table td{
      height 40px;
      padding 0 !important;
    }
    /deep/.ivu-table-cell{
      padding 0 16px !important;
    }
  }
}
.form-submit {
  /deep/.ivu-card{
    border-radius: 0;
  }
  margin-bottom: 79px;
  .fixed-card {
    position: fixed;
    right: 0;
    bottom: 0;
    left: 200px;
    z-index: 45;
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
}
.seeCatMy {
  color: #2d8cf0;
  cursor: pointer;
}
.addCustom_content {
  margin-top: 20px;

  .custom_box {
    margin-bottom: 10px;
  }

  .addfont {
    display: inline-block;
    font-size: 13px;
    font-weight: 400;
    color: #1890FF;
    cursor: pointer;
  }
}

.addCustomBox {
  margin-top: 12px;
  font-size: 13px;
  font-weight: 400;
  color: #1890FF;

  .btn {
    cursor: pointer;
    width: max-content;
  }
}
.checkAlls /deep/.ivu-checkbox-inner{
  width 14px;
  height 14px;
}
.checkAlls /deep/.ivu-checkbox-wrapper{
  font-size 12px;
}
.lines {
  border-bottom: 1px dashed #eee;
  margin-bottom: 20px;
}
.iosfont{
  font-size 20px!important;
}
.selectOn{
  color #2d8cf0;
}
.ifam {
  width: 344px;
  height: 644px;
  background: url('../../../assets/images/phonebg.png') no-repeat center top;
  background-size: 344px 644px;
  padding: 40px 20px;
  padding-top: 50px;
  margin: 0 auto 0 20px;

  .content {
    height: 560px;
    overflow: hidden;
    scrollbar-width: none; /* firefox */
    -ms-overflow-style: none; /* IE 10+ */
    overflow-x: hidden;
    overflow-y: auto;
  }

  .content::-webkit-scrollbar {
    display: none; /* Chrome Safari */
  }
}
.offShow{
  position absolute
}
.goodsShow /deep/.ivu-radio-group-vertical .ivu-radio-wrapper{
  height 35px;
  line-height 35px;
}
.videbox {
  width: 60px;
  height: 60px;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 4px;
  border: 1px dashed #DDDDDD;
  line-height: 50px;
  text-align: center;
  color: #898989;
  font-size: 30px;
  font-weight: 400;
  cursor: pointer;
}
.brandName{
  /deep/.ivu-cascader{
    display inline-block
  }
}
.formValidate{
  .addClass{
    color: #1890FF;
    margin-left 14px;
    padding 9px 0;
    cursor pointer;
  }
}
.productType {
  width: 120px;
  height: 60px;
  background: #FFFFFF;
  border-radius: 3px;
  border: 1px solid #E7E7E7;
  float: left;
  text-align: center;
  padding-top: 8px;
  position: relative;
  cursor: pointer;
  line-height: 23px;
  margin-right 12px;

  &.on{
    border-color #1890FF;
  }

  .name {
    font-size: 14px;
    font-weight: 600;
    color: rgba(0, 0, 0, 0.85);
  }

  .title {
    font-size: 12px;
    font-weight: 400;
    color: #999999;
  }

  .jiao {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 0;
    height: 0;
    border-bottom: 26px solid #1890FF;
    border-left: 26px solid transparent;
  }

  .iconfont {
    position: absolute;
    bottom: -3px;
    right: 1px;
    color: #FFFFFF;
    font-size 12px;
  }

}
.labelInput{
  border: 1px solid #dcdee2;
  width 50%;
  padding 0 5px;
  border-radius 5px;
  min-height 30px;
  cursor pointer;
  .span{
    color: #c5c8ce;
  }
  .iconxiayi{
    font-size 12px
  }
}
.labelClass{
  /deep/.ivu-form-item-content{
    line-height unset;
  }
}
.ivu-checkbox-wrapper{
  margin-right 19px;
}
.list-group {
  margin-left: -8px;
}

.borderStyle {
  border: 1px solid #ccc;
  padding: 8px;
  border-radius: 4px;
}

.drag {
  cursor: move;
}

.move-icon {
  width: 30px;
  cursor: move;
  margin-right: 10px;
}

.move-icon .icondrag2 {
  font-size: 26px;
  color: #d8d8d8;
}

.maxW /deep/.ivu-select-dropdown {
  max-width: 600px;
}

#shopp-manager .ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}

.noLeft {
  >>> .ivu-form-item-content {
    margin-left: 0 !important;
  }
}

.tips {
  display: inline-bolck;
  font-size: 12px;
  font-weight: 400;
  color: #999999;
  margin-top: 6px;
}

.iview-video-style {
  width: 40%;
  height: 180px;
  border-radius: 10px;
  background-color: #707070;
  margin-top: 10px;
  position: relative;
  overflow: hidden;
}

.iview-video-style .iconv {
  color: #fff;
  line-height: 180px;
  width: 50px;
  height: 50px;
  display: inherit;
  font-size: 26px;
  position: absolute;
  top: -74px;
  left: 50%;
  margin-left: -25px;
}

.iview-video-style .mark {
  position: absolute;
  width: 100%;
  height: 30px;
  top: 0;
  background-color: rgba(0, 0, 0, 0.5);
  text-align: center;
}

.uploadVideo {
  margin-left: 10px;
}

.submission {
  margin-left: 10px;
}
.form-submit .fixed-card .ivu-btn {
  height:32px;
}

.color-list .tip {
  color: #c9c9c9;
}

.color-list .color-item {
  width 70px;
  height: 28px;
  line-height: 28px;
  color: #fff;
  margin-right: 10px;
  border-radius 2px;
  text-align center
  .num{
    color #1890FF;
    width 14px;
    height 14px;
    text-align center;
    line-height 14px;
    border-radius 50%;
    background-color #fff;
    margin-right 6px;
  }
}

.color-list .color-item.blue {
  background-color: #1E9FFF;
}

.color-list .color-item.yellow {
  background-color: rgb(254, 185, 0);
}

.color-list .color-item.green {
  background-color: #009688;
}

.color-list .color-item.red {
  background-color: #ed4014;
}

.color-list .color-item.colorBlue {
  background: linear-gradient(270deg, #5ECFFF 0%, #0084FF 100%);
}

.columnsBox {
  margin-right: 10px;
}

.priceBox {
  width: 100%;
}

.rulesBox {
  display: flex;
  flex-wrap: wrap;
}

.pictrueBox {
  display: inline-block;
}

.pictrueTab {
  width: 40px !important;
  height: 40px !important;
}

.pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 15px;
  margin-bottom 10px;
  display: inline-block;
  position: relative;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }

  .btndel {
    position: absolute;
    z-index: 1;
    width: 20px !important;
    height: 20px !important;
    left: 46px;
    top: -4px;
  }
}

.upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
}

.curs {
  cursor: pointer;
}

.inpWith {
  width: 60%;
}

.labeltop {
  >>> .ivu-form-item-label {
    float: none !important;
    display: inline-block !important;
    margin-left: 120px !important;
    width: auto !important;
  }
  .icondrop-down{
    font-size: 12px;
    margin-left: 5px;
  }
}

.video-icon {
  background-image: url('https://cdn.oss.9gt.net/prov1.1/1/icons.png'); // cdn.oss.9gt.net/prov1.1/1/icons.png);
  // background-color: #fff;
  background-position: -9999px;
  background-repeat: no-repeat;
}
.progress {
  margin-top: 10px;
}
.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab{
    padding:4px 16px 20px !important;
    font-weight: 500;
  }
}
</style>
