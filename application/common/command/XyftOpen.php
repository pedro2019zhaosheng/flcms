<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/17
 * Time: 15:55
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
 * 幸运飞艇开奖任务(注: 服务器配置10s一次)
 *
 * Class XyftOpen
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class XyftOpen extends BaseCommand
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
        $this->setName('patchXyft')
            ->addUsage('php think patchXyft')
            ->setDescription('幸运飞艇爬取');
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
        // 设置幸运飞艇运行状态，设置为已启用上线。
        PatchModel::setRunStatus(Config::FT_CODE);
        // 爬取数字彩数据
        $echo = $this->handler();

        $output->writeln($echo);
    }

    /**
     * 业务处理(事务分离,降低事务层级)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function handler()
    {
        // 爬取幸运飞艇
        try {
            $this->execLogic();
            $re = 'success';
            // 记录成功日志
            $this->executeSuccess(Config::FT_CODE);
        } catch (\Exception $e) {
            PatchLog::log(Config::FT_CODE, '幸运飞艇自动业务处理发生错误!', $e->getMessage(), 0);
            $re = $e->getMessage();
        }

        return $re;
    }

    /**
     * 爬取逻辑
     *
     * 接口数据:
     *  365接口数据格式:
     *      {
     *   "xyft": {
     *   "cnName": "幸运飞艇",
     *   "opens": [
     *   {
     *   "term": "20190627067",
     *   "number": "08,04,10,09,01,03,07,02,06,05",
     *   "time": "2019-06-27 18:39:00"
     *   },
     *   {
     *   "term": "20190627066",
     *   "number": "10,01,03,05,09,04,08,06,07,02",
     *   "time": "2019-06-27 18:34:00"
     *   }
     *   ]
     *   }
     *   }
     *
     *  博易接口数据格式:
     *  {
     *      "row":3,
     *      "code":"xyft",
     *      "data":[
     *          {"opentime":"2019-07-12 16:09:36","expect":"20190712037","opencode":"03,02,04,10,08,07,09,05,01,06"},
     *          {"opentime":"2019-07-12 16:04:36","expect":"20190712036","opencode":"06,03,07,10,05,09,04,02,01,08"},
     *          {"opentime":"2019-07-12 15:59:34","expect":"20190712035","opencode":"01,03,07,06,09,02,10,08,05,04"}
     *          ]
     *  }
     *
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function execLogic()
    {
        $reqApi = self::FT_API;
        $jsonData = Helper::curlRequest($reqApi);
        $data = Helper::jsonDecode($jsonData);
        // 接口返回null
        if (!isset($data['code'])) {
            // 写入爬取日志(这里不写入日志)
            return;
        }

        // 接口彩期数据
        $allBodyData = (array)$data['data'];
        reset($allBodyData);
        // 最新彩期数据
        $bodyData = current($allBodyData);
        // 当前期号
        $number = trim($bodyData['expect']);
        // 当前开奖号码
        $openCode = implode(',', array_map('intval', explode(',', $bodyData['opencode'])));
        // 当前开奖日期
        $openDate = $bodyData['opentime'];

        // 下期期号
        $numPrefix = substr($number, 0, -3);
        $numSuffix = substr($number, -3);
        // 每天180期
        if (intval($numSuffix) < 180) {
            $numSuffix++;
            $numSuffix = sprintf("%'03u", $numSuffix);
            $nextNumber = $numPrefix . $numSuffix;
            // 下期开奖日期
            $nextOpenDate = Helper::timeFormat(strtotime($openDate . '+5 minutes'), 's');
        } else {
            $numPrefix = date('Ymd', strtotime($numPrefix . '+1 day'));
            $nextNumber = $numPrefix . '001';
            // 下期开奖日期
            $nextOpenDate = date('Y-m-d', strtotime($openDate)) . ' 13:09:00';
        }

        // 下期开奖号码
        $nextOpenCode = '';
        // 开奖和写入预期逻辑
        $openId = PlOpen::getValByWhere(['ctype' => 5], 'id');
        if (!$openId) {
            // 第一次写入数据(兼容逻辑)
            $insertData = [
                // 当前期数据
                [
                    'expect' => $number, // 期号
                    'open_code' => $openCode, // 开奖号码
                    'open_time' => $openDate,
                    'ctype' => 5,
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'status' => 1, // 已开奖
                ],
                // 下一期数据(预)
                [
                    'expect' => $nextNumber, // 期号
                    'open_code' => $nextOpenCode, // 开奖号码
                    'open_time' => $nextOpenDate,
                    'ctype' => 5,
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'status' => 0, // 待开奖
                ],
            ];
            $model = new PlOpen;
            $insertResult = $model->insertAll($insertData);
            if (!$insertResult) {
                trigger_error('幸运飞艇初次写入开奖结果失败!');
            }
        } else {
            // 不是初次写入(主逻辑)
            // 兼容接口, 验证已开奖或未开奖
            foreach ($allBodyData as $item) {
                // 处理整合数据
                // 当前遍历彩期
                $reduceNumber = trim($item['expect']);
                // 当前遍历开奖号码
                $reduceOpenCode = implode(',', array_map('intval', explode(',', $item['opencode'])));
                // 当前遍历开奖日期
                $reduceOpenDate = $item['opentime'];
                // 查看该期开奖状态
                $openState = PlOpen::getValByWhere(['ctype' => 5, 'expect' => $reduceNumber], 'status');
                if ($openState === 0) {
                    try {
                        Db::startTrans();
                        // 更新开奖结果(做了一下乐观锁控制)
                        $affectedRows = PlOpen::where(['ctype' => 5, 'expect' => $reduceNumber, 'status' => 0])->setField([
                            'open_code' => $reduceOpenCode,
                            'status' => 1, // 已开奖
                            'update_time' => Helper::timeFormat(time(), 's'),
                        ]);
                        if (!$affectedRows) {
                            // 修改影响的行数为0, 则修改失败.
                            trigger_error('幸运飞艇自动开奖失败,若已开奖则忽略此错误信息!');
                        }

                        // 存储下一期数据(预)
                        $saveResult = PlOpen::quickCreate([
                            'expect' => $nextNumber, // 下期期号
                            'open_code' => $nextOpenCode, // 开奖号码
                            'open_time' => $nextOpenDate,
                            'ctype' => 5, // 幸运飞艇标识
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'status' => 0, // 待开奖
                        ]);
                        if (!$saveResult) {
                            trigger_error('幸运飞艇存储下期开奖数据失败');
                        }
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
                    $model = new PlOpen;
                    $model->autoDraw(5, $reduceNumber, $reduceOpenCode);
                } elseif ($openState === null) {
                    // 三方接口出错兼容
                    // 被跳过的彩期数据
                    // 直接写入开奖表即可
                    $result = PlOpen::quickCreate([
                        'expect' => $reduceNumber, // 期号
                        'open_code' => $reduceOpenCode, // 开奖号码
                        'open_time' => $reduceOpenDate, // 开奖日期
                        'ctype' => 5, // 幸运飞艇标识
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'status' => 1, // 已开奖
                    ]);
                    if (!$result) {
                        trigger_error('幸运飞艇存储历史开奖数据失败');
                    }

                } else {
                    // 已开奖数据,直接return
                    return;
                }
            }
        }
    }
}