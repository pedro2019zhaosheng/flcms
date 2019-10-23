<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/13
 * Time: 16:28
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command;

use app\common\command\base\BaseCommand;
use app\common\command\base\PatchModel;
use app\common\Config;
use app\common\Helper;
use app\common\model\JclqBase;
use app\common\model\JclqMatch;
use app\common\model\JclqOpen;
use app\common\model\Order;
use app\common\model\PatchLog;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 爬取竞彩篮球数据(即时爬取和每天爬取)
 *
 * Class PatchBB
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PatchBB extends BaseCommand
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
        $this->setName('patchBB')
            ->addUsage('php think patchBB')
            ->setDescription('竞彩篮球数据爬取');
    }

    /**
     * 逻辑控制, 这里大事务分离, 防止出现Mysql死锁情况
     *
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function handler()
    {
        /* 实时爬取篮彩赛事 */
        try {
            Db::startTrans();
            // step: 1 设置当前彩种为已启用上线。
            PatchModel::setRunStatus(Config::LC_CODE);
            // step: 2 实时爬取竞彩比赛编号列表和对阵信息
            $this->patchCurrentJcMatchNumData(Config::LC_CODE, self::BB_LIST_URI);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::LC_CODE, '实时爬取篮彩赛事发生错误', $e->getMessage());
            // 终止并重新尝试
            return $e->getMessage();
        }

        /* 实时更新竞彩奖金指数和变化 */
        try {
            Db::startTrans();
            // step: 3 实时更新竞彩奖金指数和变化
            self::setVarData();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::LC_CODE, '实时更新竞彩奖金指数和变化发生错误', $e->getMessage());
        }

        /* 实时爬取竞彩结果 */
        try {
            Db::startTrans();
            // step: 4 实爬取赛事结果并开奖
            self::patchResult();
            Db::commit();
            // step: 5 记录成功日志
            $this->executeSuccess(Config::LC_CODE);
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::LC_CODE, '实时爬取竞彩结果发生错误', $e->getMessage());
            return $e->getMessage();
        }

        return 'Execute successfully';
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
        $echo = $this->handler();
        $output->writeln($echo);
    }

    /**
     * 赛事编号获取赛事详情信息
     *
     * @param string $matchId 赛事编号
     * @param string $jcCode 竞彩代码
     * @param integer $isAh 对阵是否相反
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getLcDetail($matchId, $jcCode, $isAh)
    {
        $api = self::API_HOST . self::BB_BASE_URI . $matchId;
        $responseJson = Helper::curlRequest($api);
        $responseData = Helper::jsonDecode($responseJson);
        if (!isset($responseData['Competition'])) {
            PatchLog::log($jcCode, '爬取赛事详情时发生错误', var_export($responseData, true));
            trigger_error('Patch Match Detail Fail, Class `PatchBB` Lines 147', E_USER_ERROR);
        }

        if (empty($responseData['Competition'])) {
            PatchLog::log($jcCode, '爬取赛事详情数据成功, 但数据为空, 系统将再次发起爬取程序!', var_export($responseData, true));
            trigger_error('Patch Match Detail Is Empty, Class `PatchBB` Lines 152', E_USER_WARNING);
        }

        $data['league_num'] = isset($responseData['Competition']['Id']) ? $responseData['Competition']['Id'] : ''; // 联赛编号
        $data['league_name'] = isset($responseData['Competition']['Name']) ? $responseData['Competition']['Name'] : ''; // 联赛名称
        $data['start_time'] = (int)$responseData['Date'] / 1000; // 开赛时间
        $data['rqs'] = isset($responseData['Handicap']) ? (int)$responseData['Handicap'] : 0; // 让球数
        if ($isAh) {
            // 对阵相反
            $data['guest_name'] = isset($responseData['HomeTeam']['Name']) ? $responseData['HomeTeam']['Name'] : ''; // 客队名称
            $data['guest_num'] = isset($responseData['HomeTeam']['Id']) ? $responseData['HomeTeam']['Id'] : ''; // 客队编号
            $data['guest_icon'] = isset($responseData['HomeTeam']['Photo']) ? $responseData['HomeTeam']['Photo'] : ''; // 客队图标
            // 主队
            $data['host_name'] = isset($responseData['AwayTeam']['Name']) ? $responseData['AwayTeam']['Name'] : ''; // 主队名称
            $data['host_num'] = isset($responseData['AwayTeam']['Id']) ? $responseData['AwayTeam']['Id'] : ''; // 主队编号
            $data['host_icon'] = isset($responseData['AwayTeam']['Photo']) ? $responseData['AwayTeam']['Photo'] : ''; // 主队图标
        } else {
            $data['host_name'] = isset($responseData['HomeTeam']['Name']) ? $responseData['HomeTeam']['Name'] : ''; // 主队名称
            $data['host_num'] = isset($responseData['HomeTeam']['Id']) ? $responseData['HomeTeam']['Id'] : ''; // 主队编号
            $data['host_icon'] = isset($responseData['HomeTeam']['Photo']) ? $responseData['HomeTeam']['Photo'] : ''; // 主队图标
            // 客队
            $data['guest_name'] = isset($responseData['AwayTeam']['Name']) ? $responseData['AwayTeam']['Name'] : ''; // 客队名称
            $data['guest_num'] = isset($responseData['AwayTeam']['Id']) ? $responseData['AwayTeam']['Id'] : ''; // 客队编号
            $data['guest_icon'] = isset($responseData['AwayTeam']['Photo']) ? $responseData['AwayTeam']['Photo'] : ''; // 客队图标
        }

        return $data;
    }

    /**
     * step: 3
     *
     * 实时获取并更新奖金指数和变化数据
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setVarData()
    {
        foreach (self::$_lcJcTime as $jcDate) {
            // 获取奖金指数数据
            $api = self::API_HOST . self::BB_RT_URI . $jcDate;
            $jsonData = Helper::curlRequest($api);
            $dealData = Helper::jsonDecode($jsonData);
            if (isset($dealData['LotteryS']) && !empty($dealData['LotteryS'])) {
                $data = $dealData['LotteryS'];
                foreach ($data as $item) {
                    // 赛事开始时间(时间戳)
                    $matchTime = strtotime($item['starttime']);
                    if ($matchTime <= time()) {
                        // 过期赛事, 则跳过
                        continue;
                    }

                    // 数据容器
                    $updateData = [];
                    // 赛事编号
                    $matchNum = $item['matchId'];
                    // 获取该赛事的奖金指数数据
                    $matchIndexData = JclqMatch::getFieldsByWhere(['match_num' => $matchNum], [
                        'sp_sf', // 胜负
                        'sp_rfsf', // 让分胜负
                        'sp_sfc', // 胜负差
                        'sp_dxf', // 大小分
                        'sp_sf_var', // 胜负变化
                        'sp_rfsf_var', // 让分胜负变化
                        'sp_sfc_var', // 胜分差变化
                        'sp_dxf_var', // 大小分变化
                    ]);
                    // 该赛事赔率不存在, 则写入
                    if (empty($matchIndexData)) {
                        $insertData = [];
                        $insertData['match_num'] = $matchNum;
                        $insertData['sp_sf'] = isset($item['HDA']) ? Helper::jsonEncode($item['HDA']) : ''; // 胜负奖金指数
                        $insertData['sp_rfsf'] = isset($item['HHDA']) ? Helper::jsonEncode($item['HHDA']) : ''; // 让分胜负奖金指数
                        $insertData['sp_sfc'] = isset($item['WNM']) ? Helper::jsonEncode($item['WNM']) : ''; // 胜分差奖金指数
                        $insertData['sp_dxf'] = isset($item['HILO']) ? Helper::jsonEncode($item['HILO']) : ''; // 大小分奖金指数
                        $insertData['create_at'] = Helper::timeFormat(time(), 's'); // 新增时间
                        $insertData['update_at'] = Helper::timeFormat(time(), 's'); // 更新日期
                        // 获取奖金指数变化数据, 有可能没有数据
                        $varApi = self::API_HOST . self::BB_VAR_URI . $matchNum;
                        $varJson = Helper::curlRequest($varApi);
                        $varData = Helper::jsonDecode($varJson);
                        if (isset($varData['GameId']) && !empty($varData['GameId'])) {
                            $insertData['sp_sf_var'] = PatchModel::sliceIndexVar(isset($varData['HDA']) ? $varData['HDA'] : null); // 胜负奖金指数变化
                            $insertData['sp_rfsf_var'] = PatchModel::sliceIndexVar(isset($varData['HHDA']) ? $varData['HHDA'] : null); // 让分胜负奖金指数变化
                            $insertData['sp_sfc_var'] = PatchModel::sliceIndexVar(isset($varData['WNM']) ? $varData['WNM'] : null); // 胜分差奖金指数变化
                            $insertData['sp_dxf_var'] = PatchModel::sliceIndexVar(isset($varData['HILO']) ? $varData['HILO'] : null); // 大小分奖金指数变化
                        } else {
                            $insertData['sp_sf_var'] = '';
                            $insertData['sp_rfsf_var'] = '';
                            $insertData['sp_sfc_var'] = '';
                            $insertData['sp_dxf_var'] = '';
                        }

                        // 模拟乐观锁,再次检查
                        $repeatCheckId = JclqMatch::getValByWhere(['match_num' => $matchNum], 'id');
                        if ($repeatCheckId) {
                            continue;
                        }

                        // 写入数据
                        $result = JclqMatch::quickCreate($insertData);
                        if (!$result) {
                            PatchLog::log(Config::LC_CODE, '初次写入竞彩奖金指数数据集失败', var_export($insertData, true));
                            trigger_error('Active Get Data Fails, Class `PatchBB` Lines 251', E_USER_ERROR);
                        }

                        continue;
                    }

                    // 更新让球数
                    if (
                        isset($item['HHDA'])
                        && !empty($item['HHDA'])
                        && isset($item['HHDA']['H'])
                    ) {
                        JclqBase::where('match_num', $matchNum)->setField('rqs', $item['HHDA']['H']);
                    }

                    // 胜负奖金指数
                    $hda = isset($item['HDA']) ? $item['HDA'] : null;
                    $updateData['sp_sf'] = self::updateIndexVar($hda, $matchIndexData['sp_sf'], $matchIndexData['sp_sf_var'], $updateData, 'sp_sf_var');
                    // 让分胜负奖金指数
                    $hhda = isset($item['HHDA']) ? $item['HHDA'] : null;
                    $updateData['sp_rfsf'] = self::updateIndexVar($hhda, $matchIndexData['sp_rfsf'], $matchIndexData['sp_rfsf_var'], $updateData, 'sp_rfsf_var');
                    // 胜分差奖金指数
                    $crs = isset($item['WNM']) ? $item['WNM'] : null;
                    $updateData['sp_sfc'] = self::updateIndexVar($crs, $matchIndexData['sp_sfc'], $matchIndexData['sp_sfc_var'], $updateData, 'sp_sfc_var');
                    // 大小分奖金指数
                    $ttg = isset($item['HILO']) ? $item['HILO'] : null;
                    $updateData['sp_dxf'] = self::updateIndexVar($ttg, $matchIndexData['sp_dxf'], $matchIndexData['sp_dxf_var'], $updateData, 'sp_dxf_var');
                    // 更新日期
                    $updateData['update_at'] = Helper::timeFormat(time(), 's');

                    // 更新数据
                    JclqMatch::where('match_num', $matchNum)->update($updateData);
                }
            }
        }
    }

    /**
     * 更新奖金指数变化数据
     *
     * @param array $apiIndexData // 接口返回的指数数据
     * @param string $databaseIndexData // 数据库的指数数据
     * @param string $databaseVarData // 数据库的变化数据
     * @param array $updateData // 要更新的数据容器
     * @param string $varType // 数据变化类型  如: sp_spf_var
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function updateIndexVar(
        $apiIndexData,
        $databaseIndexData,
        $databaseVarData,
        &$updateData,
        $varType
    )
    {
        // 奖金指数
        $index = '';
        if (!empty($apiIndexData)) {
            // 处理奖金指数数据
            $index = Helper::jsonEncode($apiIndexData);
            // 处理奖金指数变化数据
            $indexVarData = Helper::jsonDecode($databaseVarData) ?: [];
            if (!empty($databaseIndexData)) {
                // 接口数据变化时间
                $newUtTime = $apiIndexData['ut'];
                // 数据库数据变化时间
                $old = Helper::jsonDecode($databaseIndexData);
                $oldUtTime = $old['ut'];
                if ($newUtTime != $oldUtTime) {
                    if (isset($apiIndexData['status'])) {
                        unset($apiIndexData['status']);
                    }

                    array_unshift($indexVarData, $apiIndexData);
                    $updateData[$varType] = PatchModel::sliceIndexVar($indexVarData);
                }
            } else {
                if (isset($apiIndexData['status'])) {
                    unset($apiIndexData['status']);
                }

                array_unshift($indexVarData, $apiIndexData);
                $updateData[$varType] = PatchModel::sliceIndexVar($indexVarData);
            }
        }

        return $index;
    }

    /**
     * step: 4 实时爬取赛事结果
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function patchResult()
    {
        // 获取未开奖的赛事开赛日期
        $unDrawData = JclqOpen::where('status', 0)
            ->field('match_date')
            ->group('match_date')
            ->select()
            ->toArray();
        $matchDates = array_filter(array_column($unDrawData, 'match_date'));
        sort($matchDates);
        foreach ($matchDates as $matchDate) {
            $reApi = self::API_HOST . self::BB_RE_URI . $matchDate;
            $apiData = Helper::curlRequest($reApi);
            $data = Helper::jsonDecode($apiData);
            if (isset($data['Schedule']) && !empty($data['Schedule'])) {
                $schedule = $data['Schedule'];
                foreach ($schedule as $item) {
                    // 保存竞彩足球赛果
                    // 竞彩足球赛事开奖
                    self::setJclqOpenAndDraw($item);
                }
            }
        }
    }

    /**
     * 保存赛事结果/赛事开奖
     *
     * @param $item // 竞彩结果数据
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setJclqOpenAndDraw($item)
    {
        $matchNum = $item['Id'][0];
        $openData = JclqOpen::getFieldsByWhere(['match_num' => $matchNum], 'status');
        // 存在该赛事
        if (!empty($openData)) {
            $isAh = JclqBase::getValByWhere(['match_num' => $matchNum], 'isAh');
            $openStatus = (int)$openData['status'];
            // 该赛事的状态是未开奖状态, 则更新该赛事
            if ($openStatus === 0) {
                $status = 0;
                if ((int)$item['Status'] === 9 || (int)$item['Status'] === 11) {
                    // 赛事结束, 则设置开奖状态为已开奖
                    $status = 1;
                }

                if ($isAh) {
                    // 对阵相反
                    $hostScore = isset($item['BScore'][0]) ? $item['BScore'][0] : '';
                    $guestScore = isset($item['AScore'][0]) ? $item['AScore'][0] : '';
                } else {
                    $hostScore = isset($item['AScore'][0]) ? $item['AScore'][0] : '';
                    $guestScore = isset($item['BScore'][0]) ? $item['BScore'][0] : '';
                }
                $updateData = [
                    // 主队总得分(含加时赛)
                    'host_score' => $hostScore,
                    // 客队总得分(含加时赛)
                    'guest_score' => $guestScore,
                    'status' => $status,
                    'update_at' => Helper::timeFormat(time(), 's'),
                ];

                // 更新赛事结果
                JclqOpen::where('match_num', $matchNum)->setField($updateData);

                // 赛事结束后, 系统自动开奖
                if ($status === 1) {
                    // ... 自动开奖逻辑
                    Order::autoDraw(Config::LC_CODE, $matchNum);
                }

                // 更新赛事状态
                $matchStatus = self::getResultStatus((int)$item['Status']);
                if ($matchStatus !== 0) {
                    JclqBase::where('match_num', $matchNum)->setField('match_status', $matchStatus);
                }
            }
        }
    }

    /**
     * 实时获取赛事状态
     *
     * @param $originStatus
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getResultStatus($originStatus)
    {
        switch (intval($originStatus)) {
            case 13: // 取消
                return 1;
            case 14: // 延期
                return 2;
            case 15: // 斩腰
                return 3;
            case 16: // 待定
                return 4;
            default:
                return 0; // 正常
        }
    }
}