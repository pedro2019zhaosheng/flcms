<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17
 * Time: 16:06
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command;

use app\common\command\base\BaseCommand;
use app\common\command\base\PatchModel;
use app\common\Config;
use app\common\Helper;
use app\common\model\AdminConfig;
use app\common\model\OrderNum;
use app\common\model\PatchLog;
use app\common\model\PlOpen;
use app\common\model\PreDraw;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 澳彩开奖任务
 * (该任务服务配置 3 minutes 执行一次)
 *
 * Class VirOpen
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class OzOpen extends BaseCommand
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
        $this->setName('ozopen')
            ->addUsage('php think ozopen')
            ->setDescription('澳彩实时开奖');
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
        // 设置澳彩运行状态, 设置为已启用上线
        PatchModel::setRunStatus(Config::AO_CODE);
        // 执行开奖逻辑
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
        // 澳彩开奖
        try {
            $this->draw();
            $re = 'success';
            // 记录成功日志
            $this->executeSuccess(Config::AO_CODE);
        } catch (\Exception $e) {
            PatchLog::log(Config::AO_CODE, '澳彩自动业务处理发生错误!', $e->getMessage(), 0);
            $re = $e->getMessage();
        }

        return $re;
    }

    /**
     * 澳彩自动风控, 虚拟赛果, 自动开奖
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function draw()
    {
        try {
            // 开启事务
            Db::startTrans();
            // 服务器初次运行写入出售中的澳彩期号
            $this->initWriteNumber();
            // 获取开奖的期号
            $expect = PlOpen::getValByWhere(['ctype' => 3, 'status' => 0], 'expect');
            // 检查是否开启风控
            $isOpenRisk = AdminConfig::conf(Config::AO_CAI, null, 0);
            $isOpenRisk = intval($isOpenRisk);
            // 查看是否手动预设开奖结果
            $preResult = PreDraw::getPreCodeByNumber($expect, 3);
            if ($preResult) {
                // 开启手动风控
                $openCode = $preResult;
                PreDraw::where('number', $expect)
                    ->where('ctype', 3)
                    ->setField('status', 1);

            } elseif ($isOpenRisk === 1) {
                // 开启自动风控
                $openCode = $this->autoRisk($expect);
            } else {
                // 不开启风控, 则随机生成虚拟开奖号码
                $openCode = $this->setFakeNum(3);
            }

            // 设置开奖状态
            PlOpen::where('expect', $expect)
                ->where('ctype', 3)// 澳彩
                ->setField([
                    'open_code' => $openCode, // 开奖结果
                    'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                    'status' => 1, // 已开奖
                    'update_time' => Helper::timeFormat(time(), 's'),
                ]);
            // 预存入下期开奖数据
            PlOpen::quickCreate([
                'expect' => (int)$expect + 1, // 最新期号
                'ctype' => 3, // 澳彩
                'open_time' => Helper::timeFormat(time() + 180, 's'), // 开奖时间
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
        // 自动开奖
        $this->autoOpenDraw(3, $expect, $openCode);
    }

    /**
     * 澳彩自动开奖
     *
     * @param $ctype // 数字彩类型, (表order_num ctype字段)
     * @param $expect // 期号
     * @param $openCode // 开奖号码
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function autoOpenDraw($ctype, $expect, $openCode)
    {
        $model = new PlOpen;
        $model->autoDraw($ctype, $expect, $openCode);
    }

    /**
     * 服务器初次运行写入出售中的澳彩期号
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function initWriteNumber()
    {
        // 判断是否是初次运行
        $isStartRun = PlOpen::getValByWhere(['ctype' => 3], 'id');
        if (!$isStartRun) {
            // 写入澳彩出售中的期号
            PlOpen::quickCreate([
                'expect' => '10000', // 最新期号
                'ctype' => 3, // 澳彩
                'open_time' => Helper::timeFormat(time() + 180, 's'), // 开奖时间
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
                'status' => 0, // 待开奖
            ]);

            // 提交事务
            Db::commit();
            // 记录成功日志
            $this->executeSuccess(Config::AO_CODE);
            // 终止业务处理
            exit(0);
        }
    }

    /**
     * 生成虚拟开奖号码
     *
     * @param $count // 号码数量 3或5
     * @return string // '3,4,5' | '3,2,3,4,5'
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function setFakeNum($count)
    {
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            $result .= rand(0, 9) . ',';
        }

        return rtrim($result, ',');
    }

    /**
     * 澳彩自动风控
     *
     * @param $expect // 开奖期号
     * @throws \Exception
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function autoRisk($expect)
    {
        $bets = OrderNum::alias('on')
            ->leftJoin('order o', 'on.order_id=o.id')
            ->where('on.number', $expect)// 期号
            ->where('on.ctype', 3)// 数字彩类型 1: 排列三  2: 排列五 3: 澳彩  4: 葡彩
            ->where('o.status', '<>', 7)// 除去已完成订单
            ->field('bet_content')
            ->select()
            ->toArray();

        // 投注订单为空, 则生成随机号码
        if (empty($bets)) {
            return $this->setFakeNum(3);
        }

        $bets = array_column($bets, 'bet_content');
        // 百位 0-9每个数字的投注数
        $hundredCount = array_fill(0, 10, 0);
        // 十位 0-9每个数字的投注数
        $decadeCount = array_fill(0, 10, 0);;
        // 个位 0-9每个数字的投注数
        $unitsCount = array_fill(0, 10, 0);;
        // 和值 0-27每个数字的投注数
        $sumCount = array_fill(0, 28, 0);;
        foreach ($bets as $jsonBet) {
            // 投注项转数组
            $arrBet = Helper::jsonDecode($jsonBet);
            // 获取投注项的玩法和投注项数组 ['aozhipu' => ['1,2,3'], ...]
            $playBets = array_column($arrBet, 'bet', 'play');
            // 遍历
            array_walk($playBets, function ($betItem, $play) use (&$hundredCount, &$decadeCount, &$unitsCount, &$sumCount) {
                switch ((string)$play) {
                    case 'aozhipu': // 澳彩直普
                        // 遍历投注数组['1,2,3', '2,3,4', ...]
                        foreach ($betItem as $item) {
                            // 获取百位, 十位, 个位
                            list($a, $b, $c) = explode(',', $item);
                            settype($a, 'integer');
                            settype($b, 'integer');
                            settype($c, 'integer');
                            $hundredCount[$a]++;
                            $decadeCount[$b]++;
                            $unitsCount[$c]++;
                        }

                        break;
                    case 'aozhihe': // 澳彩直和
                        // 遍历投注数组['1', '2', ...]
                        foreach ($betItem as $item) {
                            // 获取和值
                            $sumCount[(int)$item]++;
                        }

                        break;
                }
            });
        }

        // 直普排序
        asort($hundredCount);
        reset($hundredCount);
        asort($decadeCount);
        reset($decadeCount);
        asort($unitsCount);
        reset($unitsCount);
        // 直和排序 (和值风控暂不处理)
        asort($sumCount);

        $hundredArr = array_keys($hundredCount, 0);
        $decadeArr = array_keys($decadeCount, 0);
        $unitsArr = array_keys($unitsCount, 0);
        $h = key($hundredCount);
        if (!empty($hundredArr)) {
            $h = $hundredArr[array_rand($hundredArr)];
        }

        $d = key($decadeCount);
        if (!empty($decadeArr)) {
            $d = $decadeArr[array_rand($decadeArr)];
        }

        $u = key($unitsCount);
        if (!empty($unitsArr)) {
            $u = $unitsArr[array_rand($unitsArr)];
        }

        return $h . ',' . $d . ',' . $u;
    }
}