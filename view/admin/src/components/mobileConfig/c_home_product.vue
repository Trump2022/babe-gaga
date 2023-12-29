<template>
    <div class="mobile-config pro">
        <div  v-for="(item,key) in rCom" :key="key">
            <component :is="item.components.name" :configObj="configObj" ref="childData" :configNme="item.configNme" :key="key" @getConfig="getConfig" :index="activeIndex" :num="item.num"></component>
        </div>
        <rightBtn :activeIndex="activeIndex" :configObj="configObj"></rightBtn>
    </div>
</template>

<script>
    import toolCom from '@/components/mobileConfigRight/index.js'
    import { mapState, mapMutations, mapActions } from 'vuex'
    import rightBtn from '@/components/rightBtn/index.vue';
    import { getProduct, getCategory } from '@/api/diy'
    export default {
        name: 'c_home_product',
        componentsName: 'home_product',
        cname: '促销列表',
        props: {
            activeIndex: {
                type: null
            },
            num: {
                type: null
            },
            index: {
                type: null
            }
        },
        components: {
            ...toolCom,
            rightBtn
        },
        data () {
            return {
                configObj: {},
                rCom: [
                    {
                        components: toolCom.c_set_up,
                        configNme: 'setUp'
                    }
                ]
            }
        },
        watch: {
            num (nVal) {
                let value = JSON.parse(JSON.stringify(this.$store.state.admin.mobildConfig.defaultArray[nVal]))
                this.configObj = value;
				if(!value.tabConfig.classList.length || !value.tabConfig.classList[0].value){
				    this.getCategory();
				}
            },
            configObj: {
                handler (nVal, oVal) {
                    this.$store.commit('admin/mobildConfig/UPDATEARR', { num: this.num, val: nVal });
                },
                deep: true
            },
            'configObj.setUp.tabVal': {
                handler (nVal, oVal) {
                    var arr = [this.rCom[0]]
                    if (nVal == 0) {
                        let tempArr = [
                            {
                                components: toolCom.c_promotion,
                                configNme: 'tabConfig'
                            },
                            // {
                            //     components: toolCom.c_input_number,
                            //     configNme: 'numConfig'
                            // },
                            {
                                components: toolCom.c_is_show,
                                configNme: 'titleShow'
                            },
                            {
                                components: toolCom.c_is_show,
                                configNme: 'opriceShow'
                            },
                            {
                                components: toolCom.c_is_show,
                                configNme: 'priceShow'
                            },
                            {
                                components: toolCom.c_is_show,
                                configNme: 'couponShow'
                            }
                        ]
                        this.rCom = arr.concat(tempArr)
                    } else {
                        let tempArr = [
                            // {
                            //     components: toolCom.c_txt_tab,
                            //     configNme: 'titleConfig'
                            // },
                            {
                                components: toolCom.c_bg_color,
                                configNme: 'themeColor'
                            },
                            {
                                components: toolCom.c_bg_color,
                                configNme: 'fontColor'
                            },
                            {
                                components: toolCom.c_bg_color,
                                configNme: 'labelColor'
                            },
							{
							    components: toolCom.c_bg_color,
							    configNme: 'txtColor'
							},
							{
							    components: toolCom.c_bg_color,
							    configNme: 'infoColor'
							},
                            {
                                components: toolCom.c_slider,
                                configNme: 'mbConfig'
                            }
                        ]
                        this.rCom = arr.concat(tempArr)
                    }
                },
                deep: true
            }
        },
        mounted () {
            this.$nextTick(() => {
                let value = JSON.parse(JSON.stringify(this.$store.state.admin.mobildConfig.defaultArray[this.num]))
                this.configObj = value;
				this.getCategory();
            })
        },
        methods: {
            getConfig (data) {
				let configObj = this.configObj.tabConfig.list[this.configObj.tabConfig.tabCur]
				let activeValue = configObj.selectConfig.activeValue
                if (!data.name && data == 1) {
                    configObj.goodsList.list = []
                    return
                }
                if (!data.name && data == 0 && !activeValue.length) {
                    configObj.goodsList.list = []
                    return
                }
                getProduct({
                    id: activeValue[activeValue.length-1],
                    page: 1,
                    limit: configObj.numConfig.val,
                    priceOrder: configObj.goodsSort == 2 ? 'desc' : '',
                    salesOrder: configObj.goodsSort == 1 ? 'desc' : '',
            		store_label_id:configObj.goodsLabel.activeValue
                }).then(res => {
                    configObj.productList.list = res.data;
                })
            },
			getCategory () {
			    getCategory().then(res => {
			        this.$set(this.configObj.tabConfig,'classList',res.data)
			    })
			}
        }
    }
</script>

<style scoped lang="stylus">
.pro
    padding 0 15px
    .tips
        height 50px
        line-height 50px
        color #999
        font-size 12px
        border-bottom 1px solid rgba(0,0,0,0.05);
.btn-box
    padding-bottom 20px
</style>
