<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/11
 * Time: 13:40
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command;

use app\common\command\base\BaseCommand;
use app\common\command\base\PatchModel;
use app\common\Config;
use app\common\Helper;
use app\common\model\PatchLog;
use app\common\model\PlOpen;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 爬取排三排五结果
 * 注: 该任务服务器配置 1 minutes 执行一次
 *
 * Class PatchP3P5
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PatchP3P5 extends BaseCommand
{
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
        $this->setName('patchP3P5')
            ->addUsage('php think patchP3P5')
            ->setDescription('排三排五结果爬取');
    }

    /**
     * 执行指令
     *
     * @param Input $input
     * @param Output $output
     * @return int|null|void
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function execute(Input $input, Output $output)
    {
        set_time_limit(0);
        ini_set('memory_limit', '500M');
        // 设置排列三运行状态，设置为已启用上线。
        PatchModel::setRunStatus(Config::P3_CODE);
        // 设置排列五运行状态，设置为已启用上线。
        PatchModel::setRunStatus(Config::P5_CODE);
        // 爬取数字彩数据
        $echo = $this->handler();

        $output->writeln($echo);
    }

    /**
     * 业务逻辑(事务分离,降低事务层级)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function handler()
    {
        // 爬取排三
        try {
            $this->patchP3();
            $re1 = 'success';
            // 记录成功日志
            $this->executeSuccess(Config::P3_CODE);
        } catch (\Exception $e) {
            PatchLog::log(Config::P3_CODE, '排三自动业务处理发生错误!', $e->getMessage(), 0);
            $re1 = $e->getMessage();
        }

        // 爬取排五
        try {
            $this->patchP5();
            $re2 = 'success';
            // 记录成功日志
            $this->executeSuccess(Config::P5_CODE);
        } catch (\Exception $e) {
            PatchLog::log(Config::P5_CODE, '排五自动业务处理发生错误!', $e->getMessage(), 0);
            $re2 = $e->getMessage();
        }

        return $re1 . PHP_EOL . $re2;
    }

    /**
     * 爬取排三结果并写入数据表
     *
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function patchP3()
    {
        $p3Api = self::P3_API;
        $data = Helper::jsonDecode(Helper::curlRequest($p3Api));
        if (empty($data) || !isset($data['data'])) {
            trigger_error(var_export($data, true), E_USER_WARNING);
        }

        $p3Data = $data['data'];
        // 判断是否是初次运行
        $isStartRun = PlOpen::getValByWhere(['ctype' => 1], 'id');
        if (!$isStartRun) {
            try {
                // 开启事务
                Db::startTrans();
                // 初次运行写入所有结果
                foreach ($p3Data as $item) {
                    // 处理为数字彩期号
                    $expect = substr($item['expect'], 2);
                    PlOpen::quickCreate([
                        'expect' => $expect, // 期号
                        'open_code' => $item['opencode'], // 开奖结果
                        'open_time' => $item['opentime'], // 开奖时间
                        'ctype' => 1, // 彩种类型 1: 排列三
                        'status' => 1, // 已开奖
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                    ]);
                }

                $expects = array_column($p3Data, 'expect', 'opentime');
                arsort($expects);
                $desExpect = current($expects);
                $lastOpenTime = array_search($desExpect, $expects);
                $lastExpect = substr($desExpect, 2);
                // 预售期号 = 上一期号 + 1
                $newestNum = strval(intval($lastExpect) + 1);
                // 获取当前年
                $year = date('Y', strtotime($lastOpenTime . '+1 day'));
                $nextOpenTime = date('Y-m-d H:i:s', strtotime($lastOpenTime . '+1 day'));
                $currentSuffix = substr($year, 2);
                $suffixNum = substr($lastExpect, 0, 2);
                if ($suffixNum != $currentSuffix) {
                    $newestNum = $currentSuffix . '001';
                }

                // 预存入下期开奖数据
                PlOpen::quickCreate([
                    'expect' => $newestNum, // 最新期号
                    'ctype' => 1, // 排三
                    'open_time' => $nextOpenTime, // 开奖时间
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'status' => 0, // 待开奖
                ]);
                // 提交业务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚业务(初次写入失败)
                Db::rollback();
                // 抛出异常
                trigger_error($e->getMessage());
                // 捕获失败,强制终止
                exit(0);
            }
        } else {
            // 不是初次运行
            foreach ($p3Data as $item) {
                // 处理为数字彩期号
                $expect = substr($item['expect'], 2);
                // 获取该期的开奖状态
                $openStatus = PlOpen::getValByWhere(['expect' => $expect, 'ctype' => 1], 'status');
                if ($openStatus === 0) {
                    try {
                        // 开启事务
                        Db::startTrans();
                        // 开奖
                        PlOpen::where('expect', $expect)
                            ->where('ctype', 1)
                            ->setField([
                                'open_code' => $item['opencode'], // 开奖结果
                                'open_time' => $item['opentime'], // 开奖时间
                                'status' => 1, // 已开奖
                                'update_time' => Helper::timeFormat(time(), 's'),
                            ]);
                        // 预售期号 = 上一期号 + 1
                        $newestNum = strval(intval($expect) + 1);
                        // 获取当前年
                        $year = date('Y', strtotime($item['opentime'] . '+1 day'));
                        $nextOpenTime = date('Y-m-d H:i:s', strtotime($item['opentime'] . '+1 day'));
                        $currentSuffix = substr($year, 2);
                        $suffixNum = substr($expect, 0, 2);
                        if ($suffixNum != $currentSuffix) {
                            $newestNum = $currentSuffix . '001';
                        }

                        // 预存入下期开奖数据
                        PlOpen::quickCreate([
                            'expect' => $newestNum, // 最新期号
                            'ctype' => 1, // 排三
                            'open_time' => $nextOpenTime, // 开奖时间
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'status' => 0, // 待开奖
                        ]);
                        // 提交业务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚业务
                        Db::rollback();
                        // 抛出异常
                        trigger_error($e->getMessage());
                        // 捕获失败,强制终止
                        exit(0);
                    }

                    // 排三自动开奖(业务提交成功)
                    $model = new PlOpen;
                    $model->autoDraw(1, $expect, $item['opencode']);
                }
            }
        }
    }

    /**
     * 爬取排五结果并写入数据表
     *
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function patchP5()
    {
        $p5Api = self::P5_API;
        $data = Helper::jsonDecode(Helper::curlRequest($p5Api));
        if (empty($data) || !isset($data['data'])) {
            trigger_error(var_export($data, true), E_USER_WARNING);
        }

        $p5Data = $data['data'];
        // 判断是否是初次运行
        $isStartRun = PlOpen::getValByWhere(['ctype' => 2], 'id');
        if (!$isStartRun) {
            try {
                // 开启事务
                Db::startTrans();
                // 初次运行写入所有结果
                foreach ($p5Data as $item) {
                    // 处理为数字彩期号
                    $expect = substr($item['expect'], 2);
                    PlOpen::quickCreate([
                        'expect' => $expect, // 期号
                        'open_code' => $item['opencode'], // 开奖结果
                        'open_time' => $item['opentime'], // 开奖时间
                        'ctype' => 2, // 彩种类型 1: 排列五
                        'status' => 1, // 已开奖
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                    ]);
                }

                $expects = array_column($p5Data, 'expect', 'opentime');
                arsort($expects);
                $desExpect = current($expects);
                $lastOpenTime = array_search($desExpect, $expects);
                $lastExpect = substr($desExpect, 2);
                // 预售期号 = 上一期号 + 1
                $newestNum = strval(intval($lastExpect) + 1);
                // 获取当前年
                $year = date('Y', strtotime($lastOpenTime . '+1 day'));
                $nextOpenTime = date('Y-m-d H:i:s', strtotime($lastOpenTime . '+1 day'));
                $currentSuffix = substr($year, 2);
                $suffixNum = substr($lastExpect, 0, 2);
                if ($suffixNum != $currentSuffix) {
                    $newestNum = $currentSuffix . '001';
                }

                // 预存入下期开奖数据
                PlOpen::quickCreate([
                    'expect' => $newestNum, // 最新期号
                    'ctype' => 2, // 排五
                    'open_time' => $nextOpenTime, // 开奖时间
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'status' => 0, // 待开奖
                ]);
                // 提交业务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚业务
                Db::rollback();
                // 抛出异常
                trigger_error($e->getMessage());
                // 捕获失败,强制终止
                exit(0);
            }
        } else {
            // 不是初次运行
            foreach ($p5Data as $item) {
                // 处理为数字彩期号
                $expect = substr($item['expect'], 2);
                // 获取该期的开奖状态
                $openStatus = PlOpen::getValByWhere(['expect' => $expect, 'ctype' => 2], 'status');
                if ($openStatus === 0) {
                    try {
                        // 开启事务
                        Db::startTrans();
                        // 更新开奖状态
                        PlOpen::where('expect', $expect)
                            ->where('ctype', 2)
                            ->setField([
                                'open_code' => $item['opencode'], // 开奖结果
                                'open_time' => $item['opentime'], // 开奖时间
                                'status' => 1, // 已开奖
                                'update_time' => Helper::timeFormat(time(), 's'),
                            ]);
                        // 预售期号 = 上一期号 + 1
                        $newestNum = strval(intval($expect) + 1);
                        // 获取当前年
                        $year = date('Y', strtotime($item['opentime'] . '+1 day'));
                        $nextOpenTime = date('Y-m-d H:i:s', strtotime($item['opentime'] . '+1 day'));
                        $currentSuffix = substr($year, 2);
                        $suffixNum = substr($expect, 0, 2);
                        if ($suffixNum != $currentSuffix) {
                            $newestNum = $currentSuffix . '001';
                        }

                        // 预存入下期开奖数据
                        PlOpen::quickCreate([
                            'expect' => $newestNum, // 最新期号
                            'ctype' => 2, // 排五
                            'open_time' => $nextOpenTime, // 开奖时间
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'status' => 0, // 待开奖
                        ]);
                        // 提交业务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚业务
                        Db::rollback();
                        // 抛出异常
                        trigger_error($e->getMessage());
                        // 捕获失败,强制终止
                        exit(0);
                    }
                    // 排五自动开奖(业务提交成功)
                    $model = new PlOpen;
                    $model->autoDraw(2, $expect, $item['opencode']);
                }
            }
        }
    }
}