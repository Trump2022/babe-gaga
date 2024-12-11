/*
 * @Author: 默°
 * @Date: 2023-12-29 18:02:42
 * @LastEditTime: 2024-05-07 18:36:48
 * @LastEditors: 默°
 * @Description: 搬什么砖，摸鱼吧
 * 舒适圈那么舒服为什么要离开
 */
//注册组件
export default {
  components: {
    cardBuild: () => import('./cardBuild'),
    inputBuild: () => import('./inputBuild'),
    inputNumberBuild: () => import('./inputNumberBuild'),
    arrBuild: () => import('./arrBuild'),
    radioBuild: () => import('./radioBuild'),
    checkboxBuild: () => import('./checkboxBuild'),
    selectBuild: () => import('./selectBuild'),
    switchBuild: () => import('./switchBuild'),
    uploadImageBuild: () => import('./uploadImageBuild'),
    uploadFrameBuild: () => import('./uploadFrameBuild'),
    tabsBuild: () => import('./tabsBuild'),
    alertBuild: () => import('./alertBuild'),
    diyTableBuild: () => import('./diyTableBuild'),
    timeBuild: () => import('./timeBuild'), // 时间选择
    addressBuild: () => import('./addressBuild.vue'), // 地址选择
    mapBuild: () => import('./mapBuild.vue'), // 地图选择
  },
};
