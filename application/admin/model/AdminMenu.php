<?php

namespace app\admin\model;

use think\Model;

class AdminMenu extends Model
{
    /**
     * 按查询条件获取单条数据
     * @author zhengmaoju
     * @dateTime 2019-03-25
     * @param    array      $cond  [查询条件]
     * @param    string     $field [字段]
     * @return   [obj]      [查询数据不存在的话，返回空模型]
     */
    public function getOneByCond($cond = [], $field = '*')
    {
        return $this->field($field)->where($cond)->findOrEmpty();
    }

    /**
     * 按条件以及排序查询数据列表
     * @author zhengmaoju
     * @dateTime 2019-03-25
     * @param    array      $cond [查询条件]
     * @param    string     $sort [排序字段]
     * @return   [type]           [返回结果集]
     */
    public function getListByCond($cond = [], $order = '')
    {
        return $this->where($cond)->order($order)->select();
    }
}
