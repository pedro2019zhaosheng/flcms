<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\vp\controller;

use app\common\Helper;
use app\common\model\AdminLog;
use app\common\model\AdminSmslog;
use app\common\VpController;
use app\common\model\AdminConfig as AdminCofigrModel;
use app\common\model\AdminService as AdminServiceModel;
use app\common\model\AdminSmslog as AdminSmslogModel;
use app\common\model\AdminLog as AdminLogModel;
use think\Exception;

/**
 * 系统设置模块
 *
 * Class System
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class System extends VpController
{

    /**
     * 站点配置
     *
     * @return \think\response\Json
     * @throws Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function siteConfig()
    {
        $list = AdminCofigrModel::getList(['groupid' => 1]);
        $info = [];
        foreach ($list as $k => $v) {
            $info[$v['varname']] = $v['value'];
        }

        return $this->asJson(1, 'success', '请求成功', $info);
    }

    /**
     * 保存站点配置
     *
     * @return \think\response\Json
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function saveSiteConfig()
    {
        $post = $this->post;
        // dump($post);exit;
        foreach ($post as $k => $v) {
            if ($k == 'file' && !empty($v)) {
                $result = $this->configIni('webImg');
                if (!$result) {
                    return $this->asJson(0, 'error', '字段不存在');
                }

                $return = Helper::uploadImage('base64', 'web-logo');
                if (!is_array($return)) {
                    return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
                }

                $head = $return['head'];
                $where['varname'] = 'webImg';
                $info['value'] = $head;
                AdminCofigrModel::saveSite($where, $info);

            } else {
                $this->configIni($k);
                $where['varname'] = $k;
                $info['value'] = $v ?: '';

                AdminCofigrModel::saveSite($where, $info);
            }
        }

        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * 站点配置初始化
     *
     * @param $k
     * @author CleverStone
     * @return boolean
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function configIni($k)
    {
        $data = [
            'optradio' => '网站开关',
            'webname' => '网站名字',
            'webdns' => '网站域名',
            'webkey' => '网站关键字',
            'describe' => '网站描述',
            'webinfo' => '网站备案',
            'webImg' => '网站logo',
            'pwcompany_named' => '公司名称',
            'pwcompany_address' => '公司地址',
            'pwcompany_email' => '公司邮箱',
            'prize_size' => '加奖比例',
            'recharge_full' => '充值满值',
            'recharge_give' => '充值送值',
            'commission' => '默认返回',
            'minimum_amount' => '最低提现金额',
            'service_charge' => '提现手续费',
            'agreement' => '用户协议',
            'clause' => '隐私条款',
        ];
        if (isset($data[$k])) {
            return AdminCofigrModel::sceneInfo($k, $data[$k]);
        }

        return false;
    }


    /**
     * 客服列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function serviceList()
    {
        $pagination = AdminServiceModel::serviceList($this->get);
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 新增客服
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
    public function addService()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'system.add');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $data = [
            'name' => $post['name'],
            'num' => $post['num'] ?: Helper::randomCode(8),
            'status' => $post['status'],
            'create_time' => Helper::timeFormat(time(), 's'),
        ];

        if (!empty($post['file'])) {
            $return = Helper::uploadImage('base64', 'custom-server');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
            }

            $head = $return['head'];
            $data['img'] = $head;
        }

        if (!empty($post['icon'])) {
            $return = Helper::uploadImage('base64', 'custom-server', $post['icon']);
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传二维码，错误信息: ' . $return);
            }

            $head = $return['head'];
            $data['icon'] = $head;
        }

        $data['create_time'] = date('Y-m-d H:i:s', time());
        $data['update_time'] = date('Y-m-d H:i:s', time());
        $re = AdminServiceModel::add($data);
        if ($re) {
            return $this->asJson(1, 'success', '新增成功', []);
        }

        return $this->asJson(0, 'error', '新增失败');
    }


    /**
     * 编辑客服
     *
     * @param $id // 客服ID
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editService($id = null)
    {
        if (!request()->isPost()) {
            if (empty($id)) {
                return $this->asJson(0, 'error', '缺失ID参数');
            }

            $info = AdminServiceModel::quickGetOne((int)$id);
            $data = [];
            if (!empty($info)) {
                $data['id'] = $info['id'];
                $data['name'] = $info['name'];
                $data['num'] = $info['num'];
            }

            return $this->asJson(1, 'success', '请求成功', $info);
        } else {
            $post = $this->post;
            $validation = $this->validate($post, "system.edit");
            if ($validation !== true) {
                return $this->asJson(0, 'error', $validation);
            }
            $data = [
                'id' => $post['id'],
                'name' => $post['name'],
                'num' => $post['num'],
                'update_time' => Helper::timeFormat(time(), 's'),
            ];
            if (!empty($post['file'])) {
                $return = Helper::uploadImage('base64', 'custom-server');
                if (!is_array($return)) {
                    return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
                }

                $head = $return['head'];
                $data['img'] = $head;
            }

            if (!empty($post['icon'])) {
                $return = Helper::uploadImage('base64', 'custom-server', $post['icon']);
                if (!is_array($return)) {
                    return $this->asJson(0, 'error', '上传二维码失败，错误信息: ' . $return);
                }

                $head = $return['head'];
                $data['icon'] = $head;
            }

            AdminServiceModel::edit($data);
            return $this->asJson(1, 'success', '编辑成功', []);
        }
    }

    /**
     * 删除客服
     *
     * @return \think\response\Json
     * @throws Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function delSite()
    {
        $id = input('id');
        $re = AdminServiceModel::del($id);
        if ($re) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', '删除失败');
    }

    /**
     * 短息记录
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function smslog()
    {
        $pagination = AdminSmslogModel::smsList($this->get);
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 系统日志
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function systemLog()
    {
        $data = AdminLogModel::getList($this->get);
        $page = $data->render();
        $list = $data->toArray();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 获取日志信息
     *
     * @param $id // 日志ID
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function logDetail($id)
    {
        $adminLog = new AdminLog;
        $info = $adminLog->getLogDetail((int)$id);

        return $this->asJson(1, 'success', '请求成功', $info);
    }

    /**
     * 清空所有系统日志
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
        $model = new AdminLog;
        $model->truncateLog();

        return $this->asJson(1, 'success', '执行成功');
    }

    /**
     * faker批量生成测试数据(短信日志)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function batchSms()
    {
        exit(0);
        $model = new AdminSmslog();
        $model->faker();
    }

    /**
     * faker批量生成测试数据(客服列表)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function batch()
    {
        exit(0);
        $model = new AdminServiceModel();
        $model->faker();
    }
}
