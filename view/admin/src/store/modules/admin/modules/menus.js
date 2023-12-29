// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
/**
 * 布局菜单配置
 * */
import { menusApi } from '@/api/account';
import Setting from '@/setting';
import util from '@/libs/util';

function getMenusName () {
    let storage = window.localStorage, menuList = storage.getItem('menuList'), menuData = [], roterPre = storage.getItem('roterPre')
    try {
        menuData = menuList !== undefined ? JSON.parse(menuList) : [];
    } catch (e) {}
    if (typeof menuData !== 'object' || menuData === null) {
        menuData = []
    }
    if (roterPre !== Setting.roterPre) {
      menuData = []
    }
    return menuData
}

// 递归处理顶部菜单问题
function getChilden(data) {
    if(data.children) {
        return getChilden(data.children[0])
    }

    return data.path

}

export default {
    namespaced: true,
    state: {
        menusName: getMenusName(),
        //返回首页path
        indexPath: '',
    },
    mutations: {
        getmenusNav (state, menuList) {
            state.menusName = menuList;
            let storage = window.localStorage;
            storage.setItem('menuList', JSON.stringify(menuList));
            storage.setItem('roterPre', Setting.roterPre);
        },
        /**
         * @description 设置返回首页path
         * @param {Object} state vuex state
         * @param {Array} menu menu
         */
        setIndexPath(state, data) {
            state.indexPath = data;
        },
    },
    getters:{
        indexPath(state, getters) {
            const menus = state.menusName;
            if (menus.length && !state.indexPath) {
                let getChilden = function(data) {
                    if(data.length && data[0].children) {
                        return getChilden(data[0].children)
                    }
                    return data[0].path
                }
                let toPath = getChilden(menus);
                state.indexPath = toPath;
            } else if (!menus.length && !state.indexPath) {
              return `${Setting.roterPre}/home`
            }
            return state.indexPath;
        },

    },
    actions: {
        getMenusNavList ({ commit }) {
            return new Promise((resolve, reject) => {
                menusApi().then(async res => {
                    resolve(res);
                    util.makeMenu(Setting.roterPre, res.data.menus);
                    commit('getmenusNav', res.data.menus);
                    let storage = window.localStorage;
                    storage.setItem('menuList', JSON.stringify(res.data.menus));
                }).catch(res => {
                    reject(res);
                })
            })
        }
    }
};
