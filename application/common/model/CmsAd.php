<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

/**
 * 广告模型
 * Date: 2019/3/7
 * Time: 21:06
 * Author jimadela
 * Github https://github.com/JimAdela
 * Blog https://jimadela.github.io/
 */
class CmsAd extends BaseModel
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
        // 广告名称过滤
        if (isset($where['name']) && !empty($where['name'])) {
            $endWhere[] = ['name', 'like', '%' . $where['name'] . '%'];
        }
        // 广告类型
        if (isset($where['ad_type']) && !empty($where['ad_type'])) {
            $endWhere[] = ['ad_type', '=', $where['ad_type']];
        }
        return $endWhere;
    }

    /**
     * 获取广告列表
     *
     * @param $where // 条件
     * @param null $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public  function getList($where, $isDel = 0, $order = null)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $where[] = ['is_del', '=', $isDel];
        $paginate = self::where($where)
            ->field([
                'id',
                'name', // 广告名称
                'ad_type', //广告类型id
                'abstract', //描述
                'img', //图
                'url', //链接
                'create_time', // 创建时间
                'status',//状态
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as $k => $item) {
            $paginate[$k]['img'] = Helper::getCurrentHost() . Attach::getPathByAttachId($item['img']);

            $paginate[$k]['ad_type'] = $this->getTypeName($paginate[$k]['ad_type']);
        }

        return $paginate;
    }

    /**
     * 获取广告类型名称
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public  function getTypeName($id)
    {
        if (empty($id)) {
            return '';
        }
        return Db::name('cms_ad_type')->where('id', $id)->value('name');
    }

    /**
     * 添加广告
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insertAdver($data)
    {
        return self::quickCreate($data);
    }

    /**
     * 软删除
     *
     * @param $id  广告id
     * @return mixed
     */
    public function deleteAdver($id,$isDel = 1)
    {

        return self::quickSoftDel(['is_del' => $isDel], $id);
    }


    /**
     * 获取广告详情
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
