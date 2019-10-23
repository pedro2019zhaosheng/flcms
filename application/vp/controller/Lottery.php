<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 18:12
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\Config;
use app\common\Helper;
use app\common\model\JcdcOpen;
use app\common\model\JclqOpen;
use app\common\model\JczqOpen;
use app\common\model\PlOpen;
use app\common\VpController;
use think\Exception;
use think\response\Json;

/**
 * 彩种控制器
 *
 * Class Lottery
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Lottery extends VpController
{
    /**
     * 获取彩种列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        // 更改angular分页按钮点击事件
        config('paginate.js_var', 'getLotPage');
        $model = new \app\common\model\Lottery();
        $pagination = $model->getLotPage($this->get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 新增彩种
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function add()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'lottery.add');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        try {
            $data = Helper::uploadImage('base64', 'lottery-icon');
            if (is_string($data)) {
                throw new Exception($data);
            }

            unset($post['file']);
            $post['img'] = $data['head'];
            $post['create_at'] = Helper::timeFormat(time(), 's');
            $post['update_at'] = Helper::timeFormat(time(), 's');
            $result = \app\common\model\Lottery::quickCreate($post);
            if (!$result) {
                throw new Exception('新增彩种失败');
            }

            return $this->asJson(1, 'success', '新增成功');
        } catch (\Exception $e) {
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * 删除彩种
     *
     * @param $id // 彩种ID
     * @return \think\response\Json
     * @throws Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function delete($id)
    {
        if (strpos($id, ',')) {
            $where[] = ['id', 'in', $id];
            $find = \app\common\model\Lottery::where($where)->where('is_run', 1)->find();
            $canDel = empty($find);
        } else {
            $where['id'] = $id;
            $isRun = \app\common\model\Lottery::where($where)->value('is_run');
            $canDel = $isRun === 0;
        }

        if (!$canDel) {
            return $this->asJson(0, 'error', '已上线的彩种，禁止删除');
        }

        $rows = \app\common\model\Lottery::where($where)->delete();
        return $rows ? $this->asJson(1, 'success', '删除成功') : $this->asJson(0, 'error', '删除失败');
    }

    /**
     * 批量停售或正常
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'lottery.toggle');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $ids = $post['ids'];
        $status = $post['status'];
        $model = new \app\common\model\Lottery();
        $model->toggle($ids, $status);

        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * 修改彩种
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function edit()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'lottery.edit');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $data['name'] = $post['name'];
        $data['code'] = (string)$post['code'];
        $data['id'] = $post['id'];
        $one = \app\common\model\Lottery::quickGetOne($post['id']);
        if (empty($one)) {
            return $this->asJson(0, 'error', '该彩种不存在');
        }

        $isRun = $one['is_run'];
        $code = (string)$one['code'];
        if ($isRun === 1 && $code !== $data['code']) {
            return $this->asJson('该彩种已启用上线，禁止修改彩种代码');
        }

        if (!empty($post['file'])) {
            $head = Helper::uploadImage('base64', 'lottery-icon');
            if (is_string($head)) {
                return $this->asJson(0, 'error', $head);
            }

            $data['img'] = $head['head'];
        }

        \app\common\model\Lottery::quickCreate($data, true);

        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * 获取一条彩种数据
     *
     * @param $id
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info($id)
    {
        $info = \app\common\model\Lottery::quickGetOne((int)$id);
        $data = [];
        if (!empty($info)) {
            $data['name'] = $info['name'];
            $data['code'] = $info['code'];
            $data['id'] = $info['id'];
            $data['is_run'] = $info['is_run'];
        }

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 获取赛事开奖和数字彩开奖列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function draw()
    {
        $get = $this->get;
        if (!isset($get['code'])) {
            return $this->asJson(0, 'error', '请添加彩种代码参数');
        }

        switch ((string)$get['code']) {
            case Config::ZC_CODE: // 竞彩足球
                $model = new JczqOpen;
                $pagination = $model->getDrawList($this->get);
                $list = $pagination->toArray();
                $page = $pagination->render();
                break;
            case Config::LC_CODE: // 竞彩篮球
                $model = new JclqOpen();
                $pagination = $model->getDrawList($this->get);
                $list = $pagination->toArray();
                $page = $pagination->render();
                break;
            case Config::BJ_CODE: // 北京单场
                $model = new JcdcOpen();
                $pagination = $model->getDrawList($this->get);
                $list = $pagination->toArray();
                $page = $pagination->render();
                break;
            case 'NUM_LOTTERY': // 数字彩(排三,排五,普彩,澳彩,幸运飞艇)
                $model = new PlOpen;
                $pagination = $model->getDrawList($this->get);
                $list = $pagination->toArray();
                $page = $pagination->render();
                break;
            default:
                return $this->asJson(0, 'error', '彩种代码不存在', ['list' => [], 'page' => '']);
        }

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 导出赛事结果
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportDraw()
    {
        $get = $this->get;
        if (!isset($get['code'])) {
            return;
        }

        switch ((string)$get['code']) {
            // 足彩赛事结果导出
            case Config::ZC_CODE:
                $model = new JczqOpen;
                $model->exportData($this->get);
                break;
            // 篮彩赛事结果导出
            case Config::LC_CODE:
                $model = new JclqOpen();
                $model->exportData($this->get);
                break;
            // 北京单场赛事结果导出
            case Config::BJ_CODE:
                $model = new JcdcOpen();
                $model->exportData($this->get);
                break;
        }
    }

    /**
     * 导出数字彩开奖结果
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author LiBin
     * @api *
     */
    public function exportNumberDarw()
    {
        $data = $this->get;
        $where = [];
        if(!empty($data['date'])){// 开奖时间
            $startTime = $data['date'].' 00:00:00';
            $endTime = $data['date'].' 23:59:59';
            $where['open_time'] = ['between',[$startTime,$endTime]];
        }

        if(!empty($data['code'])){//彩种类型
            $where['ctype'] = $data['code'];
        }

        if(!empty($data['number'])){//期号
            $where['expect'] = $data['number'];
        }

        $model = new PlOpen();
        $model->exportData($where);
    }

    /**
     * 获取竞彩彩种
     * 格式: (['code' => 'name'])
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function all()
    {
        $data = [
            ['code' => Config::ZC_CODE, 'name' => '竞彩足球'],
            ['code' => Config::BJ_CODE, 'name' => '北京单场'],
            ['code' => Config::LC_CODE, 'name' => '竞彩篮球'],
        ];

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 获取所有彩种
     * 格式: (['id' => 'name'])
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function all2()
    {
        $model = new \app\common\model\Lottery();
        $all = $model->getLottery();
        $data = $all->toArray();
        $endData = [];
        if (!empty($data)) {
            $re = array_column($data, 'name', 'id');
            foreach ($re as $key => $item) {
                array_push($endData, ['id' => $key, 'name' => $item]);
            }
        }

        return $this->asJson(1, 'success', '请求成功', $endData);
    }

    /**
     * 体彩开奖结果展示
     *
     * @param $code // 竞彩代码
     * @param $matchNum // 比赛编号
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function jcResult($code, $matchNum)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $model = new JczqOpen;
                $data = $model->getJcResult($matchNum);
                break;
            case Config::LC_CODE: // 篮彩
                $model = new JclqOpen();
                $data = $model->getJcResult($matchNum);
                break;
            case Config::BJ_CODE: // 北京单场
                $model = new JcdcOpen;
                $data = $model->getJcResult($matchNum);
                break;
            default:
                return $this->asJson(0, 'error', '竞彩代码不存在');
        }

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 编辑保存数字彩当前期结果和下期开奖信息
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editSubmit()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'lottery.editNum');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new PlOpen;
        $result = $model->editSubmit($post);
        if ($result === true) {
            return $this->asJson(1, 'success', '编辑成功');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 编辑保存数字彩基本信息
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editBase()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'lottery.editBase');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        PlOpen::quickCreate([
            'id' => $post['numId'], // 数字彩开奖ID
            'expect' => $post['expect'], // 期号
            'open_time' => $post['open_date'], // 开奖时间
        ], true);
        return $this->asJson(1, 'success', '编辑成功');
    }
}