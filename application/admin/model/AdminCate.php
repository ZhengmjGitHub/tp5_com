<?php

namespace app\admin\model;

use think\Model;

class AdminCate extends Model
{
    /**
     * 按查询条件获取单条数据
     * @author zhengmaoju
     * @dateTime 2019-03-22
     * @param    array      $cond  [查询条件]
     * @param    string     $field [字段]
     * @return   [obj]      [查询数据不存在的话，返回空模型]
     */
    public function getOneByCond($cond = [], $field = '*')
    {
        return $this->field($field)->where($cond)->findOrEmpty();
    }
}
