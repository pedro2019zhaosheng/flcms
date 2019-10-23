<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 15:25
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\model\JcdcBase;
use app\common\model\JcdcMatch;
use app\common\model\JcdcOpen;
use app\common\VpController;
use think\Db;

/**
 * 北京单场控制器
 *
 * Class BeiJing
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class BeiJing extends VpController
{

    /**
     * 获取北京单场分页
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
        $get = $this->get;
        $model = new JcdcBase;
        $pagination = $model->getList($get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 批量操作 停售/出售
     *
     * @param $ids // 主键ID 如: 1,2,3,4,5
     * @param $status // 状态码
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle($ids, $status)
    {
        $model = new JcdcBase;
        $result = $model->toggle($ids, $status);
        if ($result) {
            return $this->asJson(1, 'success', '操作成功');
        }

        return $this->asJson(0, 'error', '操作失败');
    }

    /**
     * 删除赛事
     *
     * @param $ids // 主键ID
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function delete($ids)
    {
        $model = new JcdcBase;
        $result = $model->del($ids);

        if (is_bool($result)) {
            if ($result) {
                return $this->asJson(1, 'success', '删除成功');
            }

            return $this->asJson(0, 'error', '删除失败');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 赛事详情
     *
     * @param $matchId // 赛事ID
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
        $model = new JcdcMatch;
        $data = $model->getDetail($matchId);

        return $this->asJson(1, 'success', '请求成功', $data);
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
        $result = JcdcOpen::quickGetOne(null, ['match_num' => $matchNum]);
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
        $validation = $this->validate($post, 'jcdc.edit');
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
            $status = JcdcOpen::getValByWhere(['match_num' => $post['match_num']], 'status');
            if ($status === 0) {
                // 设置赛事结果为已开奖
                $openData['status'] = 1;
            }

            // 停售该赛事
            JcdcBase::where(['match_num' => $post['match_num']])->setField('sale_status', 0);
        }

        try {
            Db::startTrans();
            if (!empty($baseData)) {
                $jcdcBase = new JcdcBase;
                $jcdcBase->editData(['match_num' => $post['match_num']], $baseData);
            }

            if (!empty($openData)) {
                $jcdcOpen = new JcdcOpen;
                $jcdcOpen->editData(['match_num' => $post['match_num']], $openData);
            }

            Db::commit();
            return $this->asJson(1, 'success', '保存成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * 导出北京单场赛事
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
        $model = new JcdcBase();
        $model->exportData($where);
    }
}