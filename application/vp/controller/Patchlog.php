<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/13
 * Time: 17:49
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\VpController;
use think\Db;

/**
 * 彩种数据爬取日志控制器
 *
 * Class Patchlog
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Patchlog extends VpController
{

    /**
     * 日志分页列表
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
        // 更改angular分页按钮点击事件
        config('paginate.js_var', 'getPatchLogPage');
        $model = new \app\common\model\PatchLog();
        $pagination = $model->getPatchLogPage($this->get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 错误详情
     *
     * @param $id
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function detail($id)
    {
        $model = new \app\common\model\PatchLog();
        $data = $model->getErrorDetail((int)$id);

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 清空爬取日志
     *
     * @return \think\response\Json
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function truncate()
    {
        $model = new \app\common\model\PatchLog();
        $model->truncate();

        return $this->asJson(1, 'success', '清空成功');
    }

    /**
     * faker数据测试接口
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function batch()
    {
        exit(0);
        $model = new \app\common\model\PatchLog();
        $model->batchInsert();
        exit("ok");
    }
}