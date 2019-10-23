<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20
 * Time: 9:35
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 数字彩手动风控预设开奖号码模型
 *
 * Class PreDraw
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PreDraw extends BaseModel
{
    // 澳彩
    const C_TYPE_AO_CAI = 3;
    // 葡彩
    const C_TYPE_PU_CAI = 4;

    /**
     * 公共筛选方法
     *
     * @param $param // 请求参数
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($param)
    {
        $where = [];
        return $where;
    }

    /**
     * 数字彩预设结果分页
     *
     * @param $where // 赛选条件
     * @param string $order // 排序规则
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getPreList($where, $order = 'update_time DESC')
    {

        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $pagination = self::where($where)->order($order)->paginate($perPage);
        foreach ($pagination as &$item) {
            $item['name'] = PlOpen::getCtype($item['ctype']); // 彩种名称
            $item['open_code'] = $item['open_code'] ? explode(',', $item['open_code']) : []; // 开奖号码
        }

        return $pagination;
    }

    /**
     * 获取当前彩种, 某一期号的预设开奖号码
     *
     * @param $number // 期号
     * @param $ctype // 数字彩种类型
     * @return string|false
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getPreCodeByNumber($number, $ctype)
    {
        $result = self::getValByWhere(['number' => $number, 'ctype' => $ctype], 'open_code');
        return $result ?: false;
    }

    /**
     * 写入当前彩种, 某一期号的预设开奖结果
     *
     * @param $number // 期号
     * @param $ctype // 数字彩种类型
     * @param $openCode // 预设开奖号码
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function setPreCode($number, $ctype, $openCode)
    {
        $openStatus = PlOpen::getValByWhere(['expect' => $number, 'ctype' => $ctype], 'status');
        if ($openStatus === 1) {
            return '该期已开奖, 预设失败';
        }

        $preDrawId = self::getValByWhere(['number' => $number, 'ctype' => $ctype], 'id');
        if ($preDrawId) {
            self::quickCreate([
                'id' => $preDrawId, // 主键ID
                'open_code' => $openCode, // 开奖号码
                'update_time' => Helper::timeFormat(time(), 's'),
            ], true);
        } else {
            self::quickCreate([
                'number' => $number, // 期号
                'open_code' => $openCode, // 开奖号码
                'ctype' => $ctype, // 彩种类型
                'status' => 0, // 未使用
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
        }

        return true;
    }

    /**
     * 获取手动风控列表
     *
     * @throws \Exception
     * @return array
     *  [
     *      ctype, // 数字彩种类型
     *      name, // 名称
     *      sign, // 自动风控标识
     *      expects => [
     *                  'expect', // 期号
     *                  'open_time', // 开奖时间
     *                  ]
     *  ]
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function handList()
    {
        // 获取当前待开奖的期号列表
        $list = PlOpen::where('status', 0)// 未开奖
        ->where('ctype', 'in', [3, 4])// 澳彩, 葡彩
        ->field([
            'expect', // 期号
            'open_time', // 开奖时间
            'ctype', // 数字彩种类型
        ])
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['name'] = PlOpen::getCtype($item['ctype']); // 彩种名称
            $temp = [];
            $timeInterval = self::getOpenTimeInterval($item['ctype']); // 彩种开间时间间隔
            for ($i = 0; $i < 50; $i++) {
                array_push($temp, [
                    'expect' => (int)$item['expect'] + $i, // 期号
                    'open_time' => Helper::timeFormat(strtotime($item['open_time']) + ($timeInterval * $i), 's'), // 开奖时间
                ]);
            }

            $item['expects'] = $temp;
            unset($item['expect'], $item['open_time']);
        }

        return $list;
    }

    /**
     * 通过ctype获取数字彩开奖时间间隔
     *
     * @param $ctype // 数字彩种类型
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getOpenTimeInterval($ctype)
    {
        switch ($ctype) {
            case 3: // 澳彩
                return 180;
            case 4: // 葡彩
                return 300;
            default:
                trigger_error('未知ctype类型');
        }
    }
}