<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:20
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world.
 */

namespace app\common;

use think\Model;

/**
 * 数据模型基类.
 *
 * Class BaseModel
 *
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class BaseModel extends Model
{
    /**
     * 字段，create_time，update_time 不自动格式化。
     *
     * @var bool
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $dateFormat = false;

    /**
     * 获取模型类名.
     *
     * @return string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * 模型关联定义快捷方法.
     *
     * @param $class
     * @param $foreignKey
     * @param $localKey
     * @param string $joinType
     *
     * @return \think\model\relation\HasOne
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function relationDefine($class, $foreignKey, $localKey, $joinType = 'LEFT')
    {
        return $this->hasOne($class, $foreignKey, $localKey, [], $joinType);
    }

    /**
     * 快捷调用新增，修改
     * 注：$data  当调用场景为修改时，必须含有主键数据，作为修改时唯一索引条件。
     *    $pk    当数据表主键ID和TP默认主键不同时，需要传入主键名称.
     *
     * @param array $data
     * @param bool  $isUpdate
     * @param null  $pk
     *
     * @return bool|integer
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    final public static function quickCreate(array $data, $isUpdate = false, $pk = null)
    {
        $class = get_called_class();
        if (!class_exists($class)) {
            http_response_code(500);
            trigger_error($class.'不存在', E_USER_ERROR);
        }

        $model = new $class();
        if ($isUpdate === false) {
            // 新增
            foreach ($data as $col => $val) {
                if (is_int($col)) {
                    http_response_code(500);
                    trigger_error('未知属性，在'.$class, E_USER_ERROR);
                }
                $model->setAttr($col, $val);
            }
            $affectedRows = $model->isUpdate(false)->save();
            if (empty($affectedRows)) {
                return false;
            }
            $pk = $model->getPk();

            return $model->{$pk};
        }
        // 修改
        if (!empty($pk)) {
            $model->pk($pk);
        }

        $affectedRows = $model->isUpdate(true)->save($data);

        return $affectedRows;
    }

    /**
     * 快捷调用删除。
     *
     * @param $class
     * @param array $data
     * @param $pkVal
     *
     * @return mixed
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    final public static function quickSoftDel(array $data, $pkVal = null)
    {
        $class = get_called_class();
        if (!class_exists($class)) {
            trigger_error($class.'不存在', E_USER_ERROR);
        }

        if (!empty($pkVal)) {
            $model = $class::get($pkVal);
        } else {
            $model = new $class();
            $pk = $model->getPk();
            $model = $class::get($data[$pk]);
            if (
                !isset($pk)
                || !isset($data[$pk])
                || empty($data[$pk])
            ) {
                http_response_code(500);
                trigger_error('未定义的主键', E_USER_ERROR);
            }
        }

        foreach ($data as $col => $val) {
            $model->{$col} = $val;
        }

        return $model->save();
    }

    /**
     * 快捷获取一条数据。可以传入主键，也可以是字符串或数组Query组装。
     *
     * @param $class
     * @param null $pkVal // 主键
     * @param null $where // 条件
     *
     * @return Model
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function quickGetOne($pkVal = null, $where = null)
    {
        $class = get_called_class();
        if (!empty($pkVal)) {
            return $class::get($pkVal);
        }

        if (empty($where)) {
            trigger_error('请传入参数', E_USER_ERROR);
        }

        return $class::where($where)->find();
    }

    /**
     * 通过查询条件获取指定字段的值
     *
     * @param $where // 查询条件
     * @param $column // 单字段
     *
     * @return string|null
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    final public static function getValByWhere($where, $column)
    {
        $class = get_called_class();
        $value = $class::where($where)->value($column);

        return $value;
    }

    /**
     * 通过查询条件获取指定字段的值
     *
     * @param $where // 查询条件
     * @param $fields // 多字段
     *
     * @return array
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    final public static function getFieldsByWhere($where, $fields)
    {
        $class = get_called_class();
        $find = $class::where($where)->field($fields)->find();

        $data = [];
        if (!empty($find)) {
            $data = $find->toArray();
        }

        return $data;
    }

    /**
     * 通过查询条件获取指定列的值
     *
     * @param $where // 查询条件
     * @param $field // 字段
     *
     * @return array
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    final public static function getColumnByWhere($where, $field)
    {
        $class = get_called_class();
        $column = $class::where($where)->column($field);

        return $column;
    }
}
