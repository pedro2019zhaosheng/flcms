<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 18:17
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

class MemberGrade extends BaseModel
{
    /**
     * @desc 获取层级关系
     * @auther LiBin
     * @date 2019-03-07
     */
    public function getOne($where)
    {
        return self::where($where)->find();
    }
    /**
     * 新增分销关系数据
     *
     * @param $data
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertMemberGrade($data)
    {
        return self::quickCreate($data);
    }

    /**
     * @desc 修改分销关系
     * @param $where
     * @param $data
     * @auther LiBin
     * @date 2019-03-07
     * @api *
     */
    public function setMemberGrade($data,$where)
    {
        return self::save($data,$where);
    }

}