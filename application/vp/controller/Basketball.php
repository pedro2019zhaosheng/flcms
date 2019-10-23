<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 15:18
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\Helper;
use app\common\model\JclqBase;
use app\common\model\JclqMatch;
use app\common\model\JclqOpen;
use app\common\VpController;
use think\Db;

/**
 * 竞彩篮球控制器
 *
 * Class Basketball
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Basketball extends VpController
{
    /**
     * 竞彩篮球列表
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function lister()
    {
        $where = $this->get;
        $model = new JclqBase();
        $pagination = $model->getPage($where);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 批量操作
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

        $model = new JclqBase();
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

        $model = new JclqBase();
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
        $model = new JclqMatch();
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
        $result = JclqOpen::quickGetOne(null, ['match_num' => $matchNum]);
        $data = [];
        if (!empty($result)) {
            $data = [
                'host_score' => $result['host_score'], // 主队得分
                'guest_score' => $result['guest_score'] // 客队得分(不含加时赛)
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
        $validation = $this->validate($post, 'jclq.edit');
        if ($validation !== true) {
            return $this->asJson(1, 'error', $validation);
        }

        $baseData = [];
        if (isset($post['cutoff_time'])) {
            // 手动截止时间
            $baseData['cutoff_time'] = $post['cutoff_time'] ?: null;
        }

        if (isset($post['rqs'])) {
            // 让球数
            $baseData['rqs'] = $post['rqs'];
        }

        $openData = [];
        if (isset($post['hostScore'])) {
            // 主队得分
            $openData['host_score'] = $post['hostScore'];
        }

        if (isset($post['guestScore'])) {
            // 客队得分
            $openData['guest_score'] = $post['guestScore'];
        }

        if (
            isset($post['hostScore'])
            && $post['hostScore'] !== ''
            && isset($post['guestScore'])
            && $post['guestScore'] !== ''
        ) {
            $status = JclqOpen::getValByWhere(['match_num' => $post['match_num']], 'status');
            if ($status === 0) {
                // 设置赛事结果为已开奖
                $openData['status'] = 1;
            }

            // 停售该赛事
            JclqBase::where(['match_num' => $post['match_num']])->setField('sale_status', 0);
        }

        try {
            Db::startTrans();
            if (!empty($baseData)) {
                $jclqBase = new JclqBase;
                $jclqBase->editData(['match_num' => $post['match_num']], $baseData);
            }

            if (!empty($openData)) {
                $openData['update_at'] = Helper::timeFormat(time(), 's');
                $jclqOpen = new JclqOpen();
                $jclqOpen->editData(['match_num' => $post['match_num']], $openData);
            }

            Db::commit();
            return $this->asJson(1, 'success', '保存成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * 导出竞彩篮球赛事
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @api *
     */
    public function export()
    {
        $where = $this->get;
        $model = new JclqBase();
        $model->exportData($where);
    }
}