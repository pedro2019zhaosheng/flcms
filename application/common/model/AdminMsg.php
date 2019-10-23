<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/28
 * Time: 11:39
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 后台消息数据模型
 *
 * Class AdminMsg
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminMsg extends BaseModel
{

    /**
     * 条件筛选
     *
     * @param $param // 请求参数
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function commonFilter($param)
    {
        $where = [];
        // 日期筛选
        if (
            isset($param['endDate'])
            && !empty($param['endDate'])
            && isset($param['startDate'])
            && !empty($param['startDate'])
        ) {
            $where[] = ['send_time', 'between time', [$param['startDate'], $param['endDate']]];
        } else {
            if (isset($param['startDate']) && !empty($param['startDate'])) {
                $where[] = ['send_time', '>=', $param['startDate']];
            }

            if (isset($param['endDate']) && !empty($param['endDate'])) {
                $where[] = ['send_time', '<=', $param['endDate']];
            }
        }

        // 账号筛选
        if (isset($param['account']) && !empty($param['account'])) {
            $where[] = ['account', 'like', '%' . $param['account'] . '%'];
        }

        // 消息类型筛选
        if (isset($param['msg_type']) && $param['msg_type'] != -1) {
            $where[] = ['msg_type', '=', $param['msg_type']];
        }

        // 内容类型筛选
        if (isset($param['body_type']) && $param['body_type'] != -1) {
            $where[] = ['body_type', '=', $param['body_type']];
        }

        // 状态筛选
        if (isset($param['read_state']) && $param['read_state'] != -1) {
            $where[] = ['read_state', '=', $param['read_state']];
        }

        return $where;
    }

    /**
     * 获取消息列表
     *
     * @param $where // 请求参数
     * @param string $order // 排序条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getList($where, $order = 'send_time DESC')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        if (isset($where['id'])) {
            $where = ['id' => intval($where['id'])];
        } else {
            $where = $this->commonFilter($where);
        }

        $pagination = self::where($where)
            ->order($order)
            ->paginate($perPage);

        foreach ($pagination as &$item) {
            $item['icon'] = Attach::getPathByAttachId($item['icon']);
            if ($item['read_state'] === 0) {
                self::quickCreate([
                    'id' => $item['id'],
                    'read_state' => 1, // 设置为已读状态
                ], true);
            }
        }
        return $pagination;
    }

    /**
     * 获取最新消息
     *
     * @param int $limit // 数据条数, 默认3条
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNewestMsg($limit = 3)
    {
        $model = self::order('send_time DESC')
            ->field([
                'id', // 消息ID
                'account', // 账号
                'msg_type', // 消息类型
                'desc', // 简介
                'send_time', // 发送时间
                'icon', // icon或头像
                'read_state', // 是否已读
            ])
            ->limit($limit)
            ->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
            foreach ($data as &$item) {
                $item['msg_type'] = self::getMsgType($item['msg_type']);
                $icon = Attach::getPathByAttachId($item['icon']);
                if (empty($icon)) {
                    $icon = '/static/lib/images/msgicon.jpg';
                }

                $item['icon'] = $icon;
                $item['send_time'] = Helper::dateToStr($item['send_time']);
            }
        }

        return $data;
    }

    /**
     * 获取新消息数量
     *
     * @return float|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNewMsgCount()
    {
        $count = self::where('read_state', 0)->count('id');
        return $count;
    }

    /**
     * 通过类型值获取消息类型字符串格式
     *
     * @param $typeInt // 类型值
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getMsgType($typeInt)
    {
        switch ($typeInt) {
            case 1:
                return '系统消息';
            case 2:
                return '代理商消息';
            case 3:
                return '会员消息';
            default:
                return '其他消息';
        }
    }

    /**
     * 通过类型值获取内容类型字符串格式
     *
     * @param $typeInt
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getBodyType($typeInt)
    {
        switch ($typeInt) {
            case 1:
                return '资金提现';
            case 2:
                return '会员注单';
            case 3:
                return '资金充值';
            default:
                return '';
        }
    }
}