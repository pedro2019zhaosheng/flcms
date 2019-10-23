<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

class Stadium extends BaseModel
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
        // 新闻名称过滤
        if (isset($where['name']) && !empty($where['name'])) {
            $endWhere[] = ['name', 'like', '%' . $where['name'] . '%'];
        }
        return $endWhere;
    }

    /**
     * 获取场馆列表
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
                'name', // 新闻标题
                'location', // 位置
                'cost', // 费用
                'linkman', // 联系人
                'tel', // 联系电话
                'stadium_type', //新闻类型id
                'abstract', //描述
                'img', //图
                'create_time', // 创建时间
                'status',//状态
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as $k => $item) {
            $paginate[$k]['img'] = Attach::getPathByAttachId($item['img']);
            $paginate[$k]['stadium_type'] = $this->getTypeName($paginate[$k]['stadium_type']);
        }
        return $paginate;
    }

    /**
     * 获取场馆类型名称
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public  function getTypeName($id)
    {
        if (empty($id)) {
            return '';
        }
        return Db::name('stadium_type')->where('id', $id)->value('name');
    }

    /**
     * 添加场馆
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insertStadium($data)
    {
        return self::quickCreate($data);
    }

    /**
     * 软删除
     *
     * @param $id  新闻id
     * @return mixed
     */
    public function deleteStadium($id,$isDel = 1)
    {

        return self::quickSoftDel(['is_del' => $isDel], $id);
    }


    /**
     * 获取场馆详情
     */
    public function getOneInfo($adId)
    {
        $data = self::quickGetOne($adId);
        $result = [];
        $result['img'] = Attach::getPathByAttachId($data['img']);
        $result['id'] = $data['id'];

        return $result;
    }

    /**
     * 获取场馆列表
     *
     * @param $type // 场馆类型
     * @param $limit
     * @return string|null
     * @author ken
     * @api *
     */
    public static function getNewResults($type, $limit)
    {
        $where['stadium_type'] = $type;
        $where['is_del'] = 0;
        $where['status'] = 1;
        $openCode = self::where($where)->order('create_time desc')->limit($limit)->select();
        return $openCode ?: null;
    }

    /**
     * 获取场馆详情
     * @param $id
     * @return |null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id)
    {
        $res = self::where('id',$id)->select();
        return $res ?:null;
    }
}
