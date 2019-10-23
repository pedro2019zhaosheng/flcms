<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6
 * Time: 18:06
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * 附件模型
 *
 * Class Attach
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Attach extends BaseModel
{
    /*...*/

    /**
     * 获取附件路径
     *
     * @param $id // 附件ID
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getPathByAttachId($id)
    {
        if (empty($id)) {
            return '';
        }

        $model = self::get(['id' => $id, 'status' => 0]);
        if (empty($model)) {
            return '';
        }

        return $model->path;
    }

    /**
     * 删除附件
     * @param $attachId // 附件ID
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function del($attachId)
    {
       return self::where('id', $attachId)->delete();
    }
}