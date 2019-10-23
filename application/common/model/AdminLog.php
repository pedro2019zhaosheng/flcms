<?php

namespace app\common\model;

use app\common\BaseModel;

/**
 * 系统日志模型
 *
 * Class AdminLog
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminLog extends BaseModel
{

    /**
     * 获取搜索条件
     *
     * @param $where
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function commonFilter($where)
    {
        $map = [];
        // 执行人筛选
        if (isset($where['name']) && !empty($where['name'])) {
            $map[] = ['executor', 'like', '%' . $where['name'] . '%'];
        }

        // 业务名称筛选
        if (isset($where['workName']) && !empty($where['workName'])) {
            $map[] = ['work_name', 'like', '%' . $where['workName'] . '%'];
        }

        // 执行状态筛选
        if (isset($where['status']) && $where['status'] != -1) {
            $map[] = ['status', '=', $where['status']];
        }

        // 所属平台筛选
        if (isset($where['belong']) && $where['belong'] != -1) {
            $map[] = ['belong', '=', $where['belong']];
        }

        // 执行日期筛选
        if (
            isset($where['start_date'])
            && isset($where['end_date'])
            && !empty($where['start_date'])
            && !empty($where['end_date'])
        ) {
            $map[] = ['exec_time', 'between time', [$where['start_date'], $where['end_date']]];
        }

        return $map;
    }

    /**
     * 获取系统日志
     *
     * @param array $where // 搜索条件
     * @param string $order // 排序条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getList($where = [], $order = 'exec_time desc')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = self::commonFilter($where);
        $pagination = self::where($where)
            ->field([
                'id', // 日志ID
                'belong', // 所属平台
                'executor', // 执行人
                'work_name', // 业务名称
                'remark', // 备注
                'status', // 状态
                'exec_time', // 执行时间
            ])
            ->order($order)
            ->paginate($perPage);

        return $pagination;
    }

    /**
     * 获取日志信息
     *
     * @param $id // 日志ID
     * @return null|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getLogDetail($id)
    {
        $info = self::getValByWhere(['id' => $id], 'info');
        return $info;
    }

    /**
     * 清空所有系统日志
     *
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function truncateLog()
    {
        self::execute('TRUNCATE ' . $this->getTable());
    }
}
