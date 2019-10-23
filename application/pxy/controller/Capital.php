<?php

namespace app\pxy\controller;

use app\common\model\FundWithdraw;
use app\common\PxyController;

/**
 * 代理商后台资金管理控制器
 *
 * Class Capital
 * @package app\pxy\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Capital extends PxyController
{

    // 模型服务容器
    private $model;

    /**
     * 初始化
     *
     * @return \think\response\Json|void
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function initialize()
    {
        parent::initialize();
        if (empty('model')) {
            exit;
        }
        $className = '\app\common\model\\' . input('model');
        $this->model = new $className();
    }

    /**
     * 资金管理列表
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $get = $this->get;
        $pagination = $this->model->getAgentList($get, 'id DESC');
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 实名信息
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info()
    {
        $get = $this->get;
        $list = $this->model->getDetail($get['memberId'], $get['fundId']);

        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 资金管理数据导出
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $get = $this->get;
        $this->model->exportDataPxy($get, 'id DESC');
    }

    /**
     * 提现审核(暂时废弃)
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editVerify()
    {
        exit(0);
        $post = $this->post;
        $res = FundWithdraw::where('id', $post['id'])
            ->setField([
                'status' => $post['status'],
                'remark' => $post['remark'],
            ]);

        if ($res) {
            return $this->asJson(1, 'success', '成功');
        }

        return $this->asJson(0, 'error', '失败');
    }

    /**
     * 获取资金变动类型
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getType()
    {
        $res = array('充值', '提现', '购彩', '资金冻结', '奖金', '系统嘉奖', '注单返佣', '充值赠送', '资金校正', '跟单返佣');
        $arr = [];
        for ($i = 0; $i < 10; $i++) {
            $arr[$i] = ['id' => $i + 1, 'value' => $res[$i]];
        }

        return $this->asJson(1, 'success', '请求成功', ['list' => $arr]);
    }
}
