<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{

    /**
     * 按查询条件获取单条数据
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    array      $cond  [查询条件]
     * @param    string     $field [字段]
     * @return   [obj]      [查询数据不存在的话，返回空模型]
     */
    public function getOneByCond($cond = [], $field = '*')
    {
        return $this->field($field)->where($cond)->findOrEmpty();
    }

    /**
     * 按条件更改数据
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    array      $cond     [条件]
     * @param    array      $saveData [更新内容]
     * @return   [type]               [返回影响行数]
     */
    public function modifyByCond($cond = [], $saveData = [])
    {
        if (!$saveData) {
            return 0;
        }
        return $this->where($cond)->update($saveData);
    }
}
