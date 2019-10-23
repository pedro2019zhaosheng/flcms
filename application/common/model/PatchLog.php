<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/13
 * Time: 17:27
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 彩种爬取日志信息
 *
 * Class PatchLog
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PatchLog extends BaseModel
{
    /**
     * 公共过滤方法
     *
     * @param $param
     * @author CleverStone
     * @return array|mixed
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function commonFilter($param)
    {
        $where = [];
        // 彩种代码模糊查询
        if (isset($param['name']) && !empty($param['name'])) {
            $where[] = ['code', 'like', '%' . (string)$param['name'] . '%'];
        }

        // 日期查询
        if (
            isset($param['endDate'])
            && !empty($param['endDate'])
            && isset($param['startDate'])
            && !empty($param['startDate'])
        ) {
            $where[] = ['date', 'between time', [$param['startDate'], $param['endDate']]];
        } else {
            if (isset($param['startDate']) && !empty($param['startDate'])) {
                $where[] = ['date', '>=', $param['startDate']];
            }

            if (isset($param['endDate']) && !empty($param['endDate'])) {
                $where[] = ['date', '<=', $param['endDate']];
            }
        }

        return $where;
    }

    /**
     * 日志列表
     *
     * @param $param
     * @param null $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getPatchLogPage($param, $order = null)
    {
        $perPage = 10;
        if (isset($param['perPage']) && !empty($param['perPage'])) {
            $perPage = (int)$param['perPage'];
        }

        if (empty($order)) {
            $order = 'id DESC';
        }

        $where = $this->commonFilter($param);
        $paginate = self::where($where)->field('code,status,desc,date,id')->order($order)->paginate($perPage);

        return $paginate;
    }

    /**
     * 获取错误详情
     *
     * @param $id // 日志ID
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getErrorDetail($id)
    {
        $errorInfo = self::where('id', $id)->value('info');
        return $errorInfo;
    }

    /**
     * 清空日志
     *
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function truncate()
    {
        self::execute("TRUNCATE " . $this->getTable());
    }

    /**
     * 写入爬取日志表
     *
     * @param $jcCode // 竞彩代码
     * @param $desc // 描述
     * @param $errInfo // 错误信息
     * @param int $status // 爬取状态,默认0 失败
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function log($jcCode, $desc, $errInfo, $status = 0)
    {
        self::quickCreate([
            'code' => $jcCode,
            'status' => $status,
            'info' => $errInfo,
            'desc' => $desc,
            'date' => Helper::timeFormat(time(), 's'),
        ]);
    }

    /**
     * faker数据测试
     *
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function batchInsert()
    {
        for ($i = 0; $i < 50; $i++) {
            self::quickCreate([
                'name' => Helper::randomStr(5),
                'status' => rand(0, 1),
                'info' => Helper::randomStr(30),
                'date' => Helper::timeFormat(time(), 's'),
            ]);
        }

        return true;
    }
}