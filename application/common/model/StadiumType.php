<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

class StadiumType extends BaseModel
{
    /**
     * 过滤get参数
     * @param  [type] $where
     * @return [type]
     */
    private  function commonFilter($where)
    {
        $endWhere = [];
        // 日期过滤
        if (
            isset($where['endDate'])
            && !empty($where['endDate'])
            && isset($where['startDate'])
            && !empty($where['startDate'])
        ) {
            $endWhere[] = ['create_time', 'between time', [$where['startDate'], $where['endDate']]];
        } else {
            if (isset($where['startDate']) && !empty($where['startDate'])) {
                $endWhere[] = ['create_time', '>=', $where['startDate']];
            }

            if (isset($where['endDate']) && !empty($where['endDate'])) {
                $endWhere[] = ['create_time', '<=', $where['endDate']];
            }
        }
        // 场馆类型名称过滤
        if (isset($where['name']) && !empty($where['name'])) {
            $endWhere[] = ['name', 'like', '%' . $where['name'] . '%'];
        }
        return $endWhere;
    }

    /**
     * 获取类型列表
     *
     * @param $where // 条件
     * @param null $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public  function getList($where, $order = null)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $paginate = self::where($where)
            ->field([
                'id',
                'name', // 广告名称
                'create_time', // 创建时间
                'status',//状态
            ])
            ->order($order)
            ->paginate($perPage);
        return $paginate;
    }

    /**
     * 获取场馆类型列表
     *
     * @param $limit
     * @return string|null
     * @api *
     */
    public static function getTypeResults($limit = 50)
    {
        $openCode = self::where('status', 1)->order('create_time desc')->limit($limit)->select();
        return $openCode ?: null;
    }

    /**
     * 添加场馆类型
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insertType($data)
    {
        return self::quickCreate($data);
    }

    /**
     * 软删除
     *
     * @param $id  类型id
     * @return mixed
     */
    public function deleteAdver($id,$isDel = 1)
    {

        return self::quickSoftDel(['is_del' => $isDel], $id);
    }


    /**
     * 获取场馆类型详情
     */
    public function getOneInfo($adId)
    {
        $data = self::quickGetOne($adId);
        $result = [];

        $result['img'] = Attach::getPathByAttachId($data['img']);
        $result['id'] = $data['id'];

        return $result;
    }

}
