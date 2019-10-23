<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * Description of AdminConfig
 *
 * @author evshan
 */
class AdminConfig extends BaseModel
{

    /**
     * 获取配置信息列表
     *
     * @param array $where
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getList($where = [])
    {
        $data = self::where($where)->select();
        foreach ($data as $k => &$v) {
            if ($v['varname'] == 'webImg') {
                $v['value'] = Attach::getPathByAttachId($v['value']);
            }
        }

        return $data;
    }

    /**
     * 修改网站单个配置
     *
     * @param $where // 搜索条件
     * @param $info
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function saveSite($where, $info)
    {
        return self::where($where)->update($info);
    }

    /**
     * 新增网站配置字段
     *
     * @param $varname
     * @param $info
     * @return true
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function sceneInfo($varname, $info)
    {
        $data = self::where([['varname', '=', $varname]])->value('varname');
        if (empty($data)) {
            self::insert([
                'varname' => $varname,  // 字段
                'info' => $info,  // 描述
                'groupid' => 1, // 分类
                'value' => '' // 值
            ]);
        }

        return true;
    }

    /**
     * 获取站点配置信息
     *
     * @param string $webVar // 字段名称
     * @param string $default // 默认值
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function config($webVar = '', $default = '')
    {
        $all = self::getList(['groupid' => 1])->toArray();
        $result = [];
        if (!empty($all)) {
            $result = Helper::indexArray($all, 'varname', $all, 'value');
        }

        if (empty($webVar)) {
            return $result;
        }

        switch ((string)$webVar) {
            case 'webname': // 网站名字
                return isset($result['webname']) ? $result['webname'] : $default;
            case 'webdns': // 网站域名
                return isset($result['webdns']) ? $result['webdns'] : $default;
            case 'webkey': // 网站关键字
                return isset($result['webkey']) ? $result['webkey'] : $default;
            case 'describe': // 网站描述
                return isset($result['describe']) ? $result['describe'] : $default;
            case 'webImg': // 网站logo
                return isset($result['webImg']) ? $result['webImg'] : $default;
            case 'webinfo': // 网站备案
                return isset($result['webinfo']) ? $result['webinfo'] : $default;
            case 'pwcompany_named': // 公司名称
                return isset($result['pwcompany_named']) ? $result['pwcompany_named'] : $default;
            case 'pwcompany_address': // 公司地址
                return isset($result['pwcompany_address']) ? $result['pwcompany_address'] : $default;
            case 'pwcompany_email': // 公司邮箱
                return isset($result['pwcompany_email']) ? $result['pwcompany_email'] : $default;
            case 'prize_size': // 嘉奖比例, 嘉奖比例不存在, 则默认3个点
                return isset($result['prize_size']) ? $result['prize_size'] : ($default ?: 3);
            case 'recharge_full': // 充值满值
                return isset($result['recharge_full']) ? $result['recharge_full'] : $default;
            case 'recharge_give': // 充值送值
                return isset($result['recharge_give']) ? $result['recharge_give'] : $default;
            case 'commission': // 邀请好友返佣比例
                return isset($result['commission']) ? $result['commission'] : $default;
            case 'optradio': // 网站开关
                return isset($result['optradio']) ? $result['optradio'] : $default;
            case 'agreement': // 用户协议
                return isset($result['agreement']) ? $result['agreement'] : $default;
            case 'clause': // 隐私条款
                return isset($result['clause']) ? $result['clause'] : $default;
            case 'minimum_amount': // 最低提现金额
                return isset($result['minimum_amount']) ? $result['minimum_amount'] : $default;
            case 'service_charge': // 提现手续费
                return isset($result['service_charge']) ? $result['service_charge'] : $default;
            case 'appSwitch': // 提现手续费
                return isset($result['appSwitch']) ? $result['appSwitch'] : $default;
            case 'appUrl': // 提现手续费
                return isset($result['appUrl']) ? $result['appUrl'] : $default;
            default:
                return $default;
        }
    }

    /**
     * 写入/获取网站配置
     *
     * @param $var // 配置变量
     * @param null $value // 配置值
     * @param string $defaultOrInfo // 默认值/备注
     * @return bool|null|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function conf($var, $value = null, $defaultOrInfo = '')
    {
        if (!empty($var) && !is_null($value)) {
            $count = self::where(['varname' => $var, 'groupid' => 1])->count('id');
            if ((int)$count > 1) {
                // 变量设置重复, 配置失败
                return false;
            } elseif ((int)$count === 1) {
                // 修改配置
                self::where(['varname' => $var, 'groupid' => 1])->setField('value', $value);
                return true;
            } else {
                // 新增配置
                $result = self::quickCreate([
                    'varname' => $var, // 配置变量
                    'info' => $defaultOrInfo, // 备注
                    'groupid' => 1, // 网站配置
                    'value' => $value,
                ]);

                return !empty($result);
            }
        } else {
            // 获取配置
            $value = self::getValByWhere(['varname' => $var, 'groupid' => 1], 'value');
            return $value !== null ? $value : $defaultOrInfo;
        }
    }
}
