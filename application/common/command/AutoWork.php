<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 11:39
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command;

use app\common\command\base\AutoWorkModel;
use app\common\Helper;
use app\common\model\AdminLog;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 即时处理任务
 * 注: 该任务服务器配置 10 seconds 执行一次
 *
 * Class AutoWork
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AutoWork extends Command
{
    // 任务异常, 记录系统日志时间间隔
    const LOG_ITEM_INTERVAL = 3600;

    /**
     * 配置指令
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function configure()
    {
        $this->setName('autoWork')->addUsage('php think autoWork')->setDescription('即时处理任务');
    }


    /**
     * 执行指令
     *
     * @param Input $input
     * @param Output $output
     * @return string|void
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function execute(Input $input, Output $output)
    {
        set_time_limit(0); // 设置php运行时间无限制
        ini_set('memory_limit', '500M'); // 设置php内存运行限制为500M
        $outputResult1 = $this->setHandler1();
        $outputResult2 = $this->setHandler2();

        $outputResult = $outputResult1 . PHP_EOL . $outputResult2;
        $output->writeln($outputResult);
    }

    /**
     * 执行程序(一)
     * [
     *      1. 推单审核定时任务,
     *      2. 订单自动出票定时任务,
     *      3. 批量更新已出票状态为待开奖状态
     * ]
     *
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function setHandler1()
    {
        try {
            Db::startTrans();
            // 推单审核定时任务
            AutoWorkModel::batchSetOrderSupStatusAtSuccess();
            Db::commit();
            $outputResult1 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER1';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '推单审核定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult1 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '推单审核定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult1 = $e->getMessage();
            }
        }

        try {
            Db::startTrans();
            // 订单自动出票定时任务
            AutoWorkModel::batchSetOrderDrawBill();
            Db::commit();
            $outputResult2 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER2';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '注单自动出票定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult2 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '注单自动出票定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult2 = $e->getMessage();
            }
        }

        try {
            Db::startTrans();
            // 批量更新已出票状态为待开奖状态
            AutoWorkModel::autoRefreshDrawStatus();
            Db::commit();
            $outputResult3 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER3';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '注单更新待开奖状态定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult3 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '注单更新待开奖状态定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult3 = $e->getMessage();
            }
        }

        return 'handler1: ' . PHP_EOL . $outputResult1 . PHP_EOL . $outputResult2 . PHP_EOL . $outputResult3;
    }

    /**
     * 执行程序(二)
     * [
     *      1. 批量停售过期足球赛事
     *      2. 批量停售过期北单赛事
     *      3. 批量停售过期篮球赛事
     * ]
     *
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function setHandler2()
    {
        try {
            Db::startTrans();
            // 批量停售过期足球赛事
            AutoWorkModel::jzBatchSetMatchAtStopSale();
            Db::commit();
            $outputResult1 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER4';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '批量停售过期足球赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult1 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '批量停售过期足球赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult1 = $e->getMessage();
            }
        }

        try {
            Db::startTrans();
            // 批量停售过期北单赛事
            AutoWorkModel::bdBatchSetMatchAtStopSale();
            Db::commit();
            $outputResult2 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER5';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '批量停售过期北单赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult2 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '批量停售过期北单赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult2 = $e->getMessage();
            }
        }

        try {
            Db::startTrans();
            // 批量停售过期篮球赛事
            AutoWorkModel::JlBatchSetMatchAtStopSale();
            Db::commit();
            $outputResult3 = 'Execute Success';
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            // 设置执行人, 该执行人唯一不可重复
            $executor = 'SYSTEM_TIMER6';
            $execTime = AdminLog::getValByWhere(['executor' => $executor], 'exec_time');
            if (empty($execTime)) {
                Helper::log($executor, '即时定时任务', '批量停售过期篮球赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                $outputResult3 = $e->getMessage();
            } else {
                $execStamp = strtotime($execTime);
                // 1小时记录一次
                if ($execStamp + self::LOG_ITEM_INTERVAL <= time()) {
                    Helper::log($executor, '即时定时任务', '批量停售过期篮球赛事定时任务执行失败', var_export($e->getMessage(), true), 0);
                }

                $outputResult3 = $e->getMessage();
            }
        }

        return 'handler2: ' . PHP_EOL . $outputResult1 . PHP_EOL . $outputResult2 . PHP_EOL . $outputResult3;
    }
}