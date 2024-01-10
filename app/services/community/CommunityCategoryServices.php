<?php

// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\community;

use app\dao\community\CommunityCategoryDao;
use app\services\BaseServices;
use app\services\community\CommunityTopicServices;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Route;

/**
 * Class CommunityCategoryServices
 * @packge app\services\community
 * @date : 2024/1/8 16:58
 * @author: QSW
 * @email: qsw2012@outlook.com
 * @mixin CommunityCategoryDao
 */
class CommunityCategoryServices extends BaseServices
{
    /**
     * CommunityCategoryRepository constructor.
     * @param CommunityCategoryDao $dao
     */
    public function __construct(CommunityCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     */
    public function form(?int $id): \FormBuilder\Form
    {
        $formData = [];
        if ( ! $id) {
            $url = '/adminapi/community/category/create';
        } else {
            $url = '/adminapi/community/category/update/'.$id;
            $formData = $this->dao->get($id)->toArray();
        }
        $form = Elm::createForm(Route::buildUrl($url)->build());

        $form->setRule([
            Elm::input('cate_name', '分类名称')->required(),
            Elm::switches('is_show', '是否显示', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关')->activeText('开'),
            Elm::number('sort', '排序')->precision(0)->max(99999),
        ]);
        return $form->setTitle(is_null($id) ? '添加分类' : '编辑分类')->formData($formData);
    }

    /**
     * @param int $id
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:46
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function delete(int $id)
    {
        $make = app()->make(CommunityTopicServices::class);
        if ( $make->getWhereCount(['category_id' => $id]) ) throw new ValidateException('该分类下存在数据');
        $this->dao->delete($id);
    }

    /**
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:46
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getList(array $where): array
    {
        [$page, $limit] = $this->getPageValue();
        $query = $this->dao->getSearch($where)->order('sort DESC,category_id DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();

        return compact('count','list');
    }

    /**
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:46
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function options(): array
    {
        return  $this->dao->getSearch(['is_show' => 1])
            ->field('category_id as value,cate_name as label')
            ->order('sort DESC,category_id DESC')->select()->toArray();
    }

    /**
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 17:42
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getApiList(): array
    {
        return $this->dao->getSearch(['is_show' => 1])->order('sort DESC')
            ->setOption('filed',[])->field('category_id,cate_name')
            ->with(['children'])->order('sort DESC,category_id DESC')->select()->toArray();
    }
}

