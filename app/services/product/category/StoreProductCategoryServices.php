<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\services\product\category;

use app\dao\product\category\StoreProductCategoryDao;
use app\services\BaseServices;
use app\services\product\product\StoreProductServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use crmeb\utils\Arr;
use think\facade\Route as Url;


/**
 * Class StoreProductCategoryServices
 * @package app\services\product\category
 * @mixin StoreProductCategoryDao
 */
class StoreProductCategoryServices extends BaseServices
{
    public function __construct(StoreProductCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取分类列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTreeList($where)
    {
        $list = $this->dao->getTierList($where);
        if (!empty($list) && ($where['cate_name'] !== '' || $where['pid'] !== '')) {
            $pids = Arr::getUniqueKey($list, 'pid');
            $parentList = $this->dao->getTierList(['id' => $pids]);
            $list = array_merge($list, $parentList);
            foreach ($list as $key => $item) {
                $arr = $list[$key];
                unset($list[$key]);
                if (!in_array($arr, $list)) {
                    $list[] = $arr;
                }
            }
        }
        $list = get_tree_children($list);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取分类列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where)
    {
        $list = $this->dao->getList($where);
        $count = $this->dao->count($where);
        if ($list) {
            foreach ($list as $key => &$item) {
                if (isset($item['children']) && $item['children']) {
                    $item['children'] = [];
                    $item['loading'] = false;
                    $item['_loading'] = false;
                } else {
                    unset($item['children']);
                }
            }
        }
        return compact('list', 'count');
    }

    /**
     * 商品分类搜索下拉
     * @param string $show
     * @param string $type
     * @param string $is_class
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTierList($show = '', $type = 0, $is_class = 0)
    {
        $where = ['is_class' => $is_class];
        if ($show !== '') $where['is_show'] = $show;
        if (!$type) $where['pid'] = 0;
        return sort_list_tier($this->dao->getTierList($where));
    }

    /**
    * 获取分类cascader
    * @param int $type
    * @param int $relation_id
    * @param bool $isPid
    * @param bool $is_class
    * @return mixed
    * @throws \think\db\exception\DataNotFoundException
    * @throws \think\db\exception\DbException
    * @throws \think\db\exception\ModelNotFoundException
     */
    public function cascaderList(int $type = 0, int $relation_id = 0, bool $isPid = false, int $is_class = 0)
    {
        $where = ['is_show' => 1, 'is_class' => $is_class, 'type' => $type, 'relation_id' => $relation_id];
        if ($isPid) $where['pid'] = 0;
        $data = get_tree_children($this->dao->getTierList($where, ['id as value', 'cate_name as label', 'pid']), 'children', 'value');
        return $data;
    }

    /**
     * 设置分类状态
     * @param $id
     * @param $is_show
     */
    public function setShow(int $id, int $is_show)
    {
        $res = $this->dao->update($id, ['is_show' => $is_show]);
        $res = $res && $this->dao->update($id, ['is_show' => $is_show], 'pid');
        if (!$res) {
            throw new AdminException('设置失败');
        } else {
            $this->cacheTag()->clear();
            $this->cacheTag()->set('category_version', uniqid());
            $this->getCategory();
        }
    }

    /**
    * 创建新增表单
    * @param int $type
    * @param int $is_class
    * @return mixed
    * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm(int $type = 0, int $is_class = 0)
    {
        $controller = $is_class ? 'classes' : 'product';
        return create_form('添加分类', $this->form([], $type, $is_class), Url::buildUrl('/'.$controller.'/category'), 'POST');
    }

    /**
    * 创建编辑表单
    * @param int $id
    * @param int $type
    * @param int $is_class
    * @return mixed
    * @throws \FormBuilder\Exception\FormBuilderException
    * @throws \think\db\exception\DataNotFoundException
    * @throws \think\db\exception\DbException
    * @throws \think\db\exception\ModelNotFoundException
     */
    public function editForm(int $id, int $type = 0, int $is_class = 0)
    {
        $info = $this->dao->get($id);
        $controller = $is_class ? 'classes' : 'product';
        return create_form('编辑分类', $this->form($info, $type, $is_class), $this->url('/'.$controller.'/category/' . $id), 'PUT');
    }

    /**
     * 生成表单参数
     * @param array $info
     * @param int $type
     * @param int $is_class
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function form($info = [], int $type = 0, int $is_class = 0)
    {
        if (isset($info['pid'])) {
            if ($info['pid']) {
                $f[] = Form::select('pid', '父级', (int)($info['pid'] ?? ''))->setOptions($this->menus($is_class))->filterable(1);
            } else {
                $f[] = Form::select('pid', '父级', (int)($info['pid'] ?? ''))->setOptions($this->menus($is_class))->filterable(1)->disabled(true);
            }
        } else {
            $f[] = Form::select('pid', '父级', (int)($info['pid'] ?? ''))->setOptions($this->menus($is_class))->filterable(1);
        }
        $url = $type ? config('admin.store_prefix') . '/widget.images/index' : config('admin.admin_prefix') . '/widget.images/index';

        $f[] = Form::input('cate_name', '分类名称', $info['cate_name'] ?? '')->maxlength(30)->required();
        $f[] = Form::frameImage('pic', '移动端图(180*180)', Url::buildUrl($url, array('fodder' => 'pic')), $info['pic'] ?? '')->icon('ios-add')->width('960px')->height('505px')->modal(['footer-hide' => true]);
        $f[] = Form::frameImage('big_pic', 'PC端大图(468*340)', Url::buildUrl($url, array('fodder' => 'big_pic')), $info['big_pic'] ?? '')->icon('ios-add')->width('960px')->height('505px')->modal(['footer-hide' => true]);
        $f[] = Form::number('sort', '排序', (int)($info['sort'] ?? 0))->min(0)->min(0);
        $f[] = Form::radio('is_show', '状态', $info['is_show'] ?? 1)->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]]);
        return $f;
    }

    /**
     * 获取一级分类组合数据
     * @return array[]
     */
    public function menus($is_class = 0)
    {
        $list = $this->dao->getMenus(['pid' => 0, 'is_class' => $is_class]);
        $menus = [['value' => 0, 'label' => '顶级菜单']];
        foreach ($list as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['cate_name']];
        }
        return $menus;
    }

    /**
     * 保存新增数据
     * @param $data
     */
    public function createData($data)
    {
        if ($this->dao->getOne(['pid' => $data['pid'] ?? 0, 'cate_name' => $data['cate_name']])) {
            throw new AdminException('该分类已经存在');
        }
        $data['add_time'] = time();
        $res = $this->dao->save($data);
        if (!$res) throw new AdminException('添加失败');
        $this->cacheTag()->clear();
        $this->cacheTag()->set('category_version', uniqid());
        $this->getCategory();
    }

    /**
     * 保存修改数据
     * @param $id
     * @param $data
     * @param $is_class
     */
    public function editData($id, $data, $is_class = 0)
    {
        $cate = $this->dao->getOne(['id' => $id, 'is_class' => $is_class]);
        if (!$cate) {
            throw new AdminException('该分类不存在');
        }
        if ($data['pid']) {
            $cate = $this->dao->getOne(['pid' => $data['pid'], 'is_class' => $is_class, 'cate_name' => $data['cate_name']]);
            if ($cate && $cate['id'] != $id) {
                throw new AdminException('该分类已经存在');
            }
        }
        $res = $this->dao->update($id, $data);
        if (!$res) throw new AdminException('修改失败');
        $this->cacheTag()->clear();
        $this->cacheTag()->set('category_version', uniqid());
        $this->getCategory();
    }

    /**
     * 删除数据
     * @param int $id
     */
    public function del(int $id)
    {
        if ($this->dao->count(['pid' => $id])) {
            throw new AdminException('请先删除子分类!');
        }
        $res = $this->dao->delete($id);
        if (!$res) throw new AdminException('删除失败');
        $this->cacheTag()->clear();
        $this->cacheTag()->set('category_version', uniqid());
        $this->getCategory();
    }

    /**
     * 获取分类版本
     * @return mixed
     * @author 等风来
     * @email 136327134@qq.com
     * @date 2022/11/11
     */
    public function getCategoryVersion()
    {
        return $this->dao->cacheTag()->remember('category_version', function () {
            return uniqid();
        });
    }

    /**
     * 获取指定id下的分类,一=以数组形式返回
     * @param string $cateIds
     * @return array
     */
    public function getCateArray(string $cateIds)
    {
        return $this->dao->getCateArray($cateIds);
    }

    /**
     * 前台分类列表
     * @return bool|mixed|null
     */
    public function getCategory(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        if ($limit) {
            return $this->dao->cacheTag()->remember(md5(json_encode($where + ['limit' => $limit])), function () use ($where, $limit) {
                return $this->dao->getALlByIndex($where, 'id,cate_name,pid,pic', $limit);
            });
        } else {
            $list = $this->dao->getCategory($where);
            if(!empty($list)){
                /** @var StoreProductServices $storeProductServices */
                $storeProductServices = app()->make(StoreProductServices::class);
                $field = 'id as product_id,store_name,sales,price, price as val,image,store_info,ot_price';
                $order = ['sort', 'desc'];
                foreach ($list as &$itme){
                    if(!empty($itme['children'])){
                        foreach ($itme['children'] as &$v){
                            $map['cate_id'] = $v['id'];
                            $product_list = $storeProductServices->getProductLimit($map, 1, $field, $order);
                            $v['product_list'] = $product_list;
                        }
                    }

                }
            }

            return $list;

            /*return $this->dao->cacheTag()->remember('CATEGORY_All', function () use ($where) {
                return $this->dao->getCategory($where);
            });*/
        }
    }

    /**
     * 获取分类列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOutList()
    {
        $list = $this->dao->getTierList();
        if (!empty($list)) {
            $pids = Arr::getUniqueKey($list, 'pid');
            $parentList = $this->dao->getTierList(['id' => $pids]);
            $list = array_merge($list, $parentList);
            foreach ($list as $key => $item) {
                $arr = $list[$key];
                unset($list[$key]);
                if (!in_array($arr, $list)) {
                    $list[] = $arr;
                }
            }
        }
        $list = get_tree_children($list);
        return $list;
    }

    /**
    * 获取一级分类
    * @param array $where
    * @return array
    * @throws \think\db\exception\DataNotFoundException
    * @throws \think\db\exception\DbException
    * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOneCategory(array $where = [])
    {
        return $this->dao->getTierList($where + ['pid' => 0, 'is_show' => 1]);
    }

    /**
     * 根据IDS获取分类名称
     * @param array $cateList
     * @param array $cate_ids
     * @return array
     */
    public function getCateName(array $cate_ids, array $cateList = [])
    {
        $cate_name = [];
        if ($cate_ids) {
            if (!$cateList) {
                $cateName = $this->dao->getCateParentAndChildName(implode(',', array_unique($cate_ids)));
            } else {
                $cateName = array_filter($cateList, function ($val) use ($cate_ids) {
                    if (in_array($val['id'], $cate_ids)) {
                        return $val;
                    }
                });
            }
            foreach ($cateName as $k => $v) {
                $str = '';
                if (isset($v['one']) && $v['one']) {
                    $str = $v['one'] . '/';
                }
                if (isset($v['two']) && $v['two']) {
                    $str .= $v['two'];
                }
                if ($str) $cate_name[] = $str;
            }
        }
        return $cate_name;
    }

}
