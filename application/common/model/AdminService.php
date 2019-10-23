<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * Description of AdminService
 *
 * @author evshan
 */
class AdminService extends BaseModel
{

    /**
     * 获取客服列表
     *
     * @param array $where
     * @param string $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function serviceList($where = [], $order = '')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = self::searchWhere($where);
        if (empty($order)) {
            $order = 'create_time DESC';
        }

        $paginate = self::where($where)
            ->order($order)
            ->paginate($perPage);

        foreach ($paginate as $key => $v) {
            $paginate[$key]['img'] = Attach::getPathByAttachId($v['img']);
            $paginate[$key]['icon'] = Attach::getPathByAttachId($v['icon']);
        }

        return $paginate;
    }

    /**
     * 公共过滤方法
     *
     * @param $where
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function searchWhere($where)
    {
        $map = [];
        // 日期
        if (
            isset($where['end_time'])
            && !empty($where['end_time'])
            && isset($where['start_time'])
            && !empty($where['start_time'])
        ) {
            $map[] = ['create_time', 'between time', [$where['start_time'], $where['end_time']]];
        } else {
            if (isset($where['start_time']) && !empty($where['start_time'])) {
                $map[] = ['create_time', '>=', $where['start_time']];
            }

            if (isset($where['end_time']) && !empty($where['end_time'])) {
                $map[] = ['create_time', '<=', $where['end_time']];
            }
        }
        // 客服昵称
        if (isset($where['name']) && !empty($where['name'])) {
            $map[] = ['name', 'like', '%' . $where['name'] . '%'];
        }

        // 状态
        if (
            isset($where['status'])
            && $where['status'] !== ''
            && $where['status'] !== null
        ) {
            $map[] = ['status', '=', $where['status']];
        }

        return $map;
    }

    /**
     * 新增
     *
     * @param $data
     * @return int|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function add($data)
    {
        return self::insert($data);
    }

    /**
     * 删除
     *
     * @param $id // ID
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function del($id)
    {
        return self::where('id', $id)->delete();
    }

    /**
     * 编辑
     *
     * @param $data // 数据
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function edit($data)
    {
        self::update($data);
    }

    /**
     * faker 测试工具
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function faker()
    {
        for ($i = 0; $i < 50; $i++) {
            self::quickCreate([
                'name' => '客服' . rand(1, 1000),
                'icon' => 14,
                'num' => Helper::randomCode(8),
                'img' => 17,
                'status' => 1,
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
        }
    }

    /**
     * @desc 获取单条客服的二维码
     * @author LiBin
     * @param $where
     * @return array|null|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-18
     */
    public function getServiceOne($where)
    {
        $data =  self::where($where)->field('img')->order('update_time DESC')->find();
        if (empty($data)){
            return '';
        }

        return Helper::getCurrentHost() . Attach::getPathByAttachId($data['img']);
    }
}
