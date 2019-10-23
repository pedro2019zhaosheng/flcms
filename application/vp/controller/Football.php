<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12
 * Time: 19:04
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\model\JczqBase;
use app\common\model\JczqMatch;
use app\common\model\JczqOpen;
use app\common\VpController;
use think\Db;

/**
 * 竞彩足球控制器
 *
 * Class Football
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Football extends VpController
{

    /**
     * 竞彩足球分页
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function page()
    {
        $where = $this->get;
        // 更改angular分页按钮点击事件
        config('paginate.js_var', 'getFootballPage');
        $model = new JczqBase();
        $pagination = $model->getPage($where);
        $list = $pagination->toArray();
        $page = $pagination->render();
        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 导出竞彩足球赛事
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $where = $this->get;
        $model = new JczqBase();
        $model->exportData($where);
    }

    /**
     * 状态切换
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle()
    {
        $ids = $this->get['ids'];
        if (strpos($ids, ',') !== false) {
            $where[] = ['id', 'in', $ids];
        } else {
            $where['id'] = $ids;
        }

        $model = new JczqBase();
        $model->toggleStatus($where, (int)$this->get['status']);

        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * 批量删除数据
     *
     * @param $ids // 表ID集合或单个
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function delete($ids)
    {
        if (strpos($ids, ',') !== false) {
            $where[] = ['id', 'in', $ids];
        } else {
            $where['id'] = $ids;
        }

        $model = new JczqBase();
        $result = $model->deleteAll($where);
        if (!is_bool($result)) {
            return $this->asJson(0, 'error', $result);
        }

        return $this->asJson(1, 'success', '删除成功');
    }

    /**
     * 查看赛事详情
     *
     * @param $matchId // 比赛编号
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function detail($matchId)
    {
        $model = new JczqMatch();
        $details = $model->getDetail($matchId);

        return $this->asJson(1, 'success', '请求成功', $details);
    }

    /**
     * 获取赛果比赛数据
     *
     * @param $matchNum // 赛事编号
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function reDetail($matchNum)
    {
        $result = JczqOpen::quickGetOne(null, ['match_num' => $matchNum]);
        $data = [];
        if (!empty($result)) {
            $data = [
                'half_score' => $result['half_score'], // 半场比分
                'normal_score' => $result['normal_score'] // 全场比分(不含加时赛)
            ];
        }

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 编辑保存赛事结果
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function save()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'jczq.edit');
        if ($validation !== true) {
            return $this->asJson(1, 'error', $validation);
        }

        $baseData = [];
        if (isset($post['cutoff_time'])) {
            $baseData['cutoff_time'] = $post['cutoff_time'] ?: null;
        }

        if (isset($post['rqs'])) {
            $baseData['rqs'] = $post['rqs'];
        }

        $openData = [];
        if (isset($post['half_score'])) {
            $openData['half_score'] = $post['half_score'];
        }

        if (isset($post['normal_score'])) {
            $openData['normal_score'] = $post['normal_score'];
        }

        if (
            isset($post['half_score'])
            && $post['half_score'] !== ''
            && isset($post['normal_score'])
            && $post['normal_score'] !== ''
        ) {
            $status = JczqOpen::getValByWhere(['match_num' => $post['match_num']], 'status');
            if ($status === 0) {
                // 设置赛事结果为已开奖
                $openData['status'] = 1;
            }

            // 停售该赛事
            JczqBase::where(['match_num' => $post['match_num']])->setField('sale_status', 0);
        }

        try {
            Db::startTrans();
            if (!empty($baseData)) {
                $jczqBase = new JczqBase;
                $jczqBase->editData(['match_num' => $post['match_num']], $baseData);
            }

            if (!empty($openData)) {
                $jczqOpen = new JczqOpen;
                $jczqOpen->editData(['match_num' => $post['match_num']], $openData);
            }

            Db::commit();
            return $this->asJson(1, 'success', '保存成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }
}