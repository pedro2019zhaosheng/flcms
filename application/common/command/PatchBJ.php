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
use app\common\model\JcdcBase;
use app\common\model\JcdcMatch;
use app\common\model\JcdcOpen;
use app\common\model\Order;
use app\common\model\PatchLog;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 爬取北京单场数据(即时爬取和每天爬取)
 * 注: 该任务服务器配置 2 minutes 执行一次
 *
 * Class PatchBJ
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PatchBJ extends BaseCommand
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
        $this->setName('patchBJ')
            ->addUsage('php think patchBJ')
            ->setDescription('北京单场数据爬取');
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
        /* 实时爬取北京单场足球赛事 */
        try {
            Db::startTrans();
            // step: 1 爬取竞彩日期
            self::PatchJcDate();
            // step: 2 设置当前彩种为已启用上线。
            PatchModel::setRunStatus(Config::BJ_CODE);
            // step: 3 实时爬取赛事列表和对阵信息
            $this->patchCurrentJcMatchNumData(Config::BJ_CODE, self::BJ_LIST_URI);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::BJ_CODE, '实时爬取北京单场足球赛事发生错误', $e->getMessage());
            return $e->getMessage();
        }

        /* 实时写入/更新竞彩赔率和历史 */
        try {
            Db::startTrans();
            // step: 4 实时写入/更新竞彩赔率和历史
            self::setVarData();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::BJ_CODE, '实时写入/更新竞彩赔率和历史发生错误', $e->getMessage());
        }

        /* 实时检测异常赛事 */
        try {
            Db::startTrans();
            // step: 5 查看是否存在近期取消/腰斩/延期的赛事
            self::setExceptionMatch();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::BJ_CODE, '实时检测异常赛事发生错误', $e->getMessage());
        }

        /* 实时爬取竞彩结果 */
        try {
            Db::startTrans();
            // step: 6 实爬取赛事结果并开奖
            self::patchResult();
            Db::commit();
            // step: 7 记录成功日志
            $this->executeSuccess(Config::BJ_CODE);
        } catch (\Exception $e) {
            Db::rollback();
            // 再次记录爬取日志, 捕捉sql异常
            PatchLog::log(Config::BJ_CODE, '实时爬取竞彩结果发生错误', $e->getMessage());
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
     * 获取赛事详情信息
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
    public static function getBjDetail($matchId, $jcCode, $isAh)
    {
        $api = self::API_HOST . self::CM_BASE_URI . $matchId;
        $responseJson = Helper::curlRequest($api);
        $responseData = Helper::jsonDecode($responseJson);
        if (!isset($responseData['Competition'])) {
            PatchLog::log($jcCode, '爬取赛事详情时发生错误, 赛事ID: ' . $matchId, var_export($responseData, true));
            trigger_error('Patch Match Detail Fail, Class `PatchBJ` Lines 259', E_USER_ERROR);
        }

        if (empty($responseData['Competition'])) {
            PatchLog::log($jcCode, '爬取赛事详情数据成功, 但数据为空, 系统将再次发起爬取程序!', var_export($responseData, true));
            trigger_error('Patch Match Detail Is Empty, Class `PatchBJ` Lines 264', E_USER_WARNING);
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
     * step: 5
     *
     * 实时写入并更新竞彩赔率
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setVarData()
    {
        foreach (self::$_jcTimeNormal as $jcDate) {
            // 获取奖金指数数据
            $api = self::API_HOST . self::BJ_RT_URI . $jcDate;
            $jsonData = Helper::curlRequest($api);
            $dealData = Helper::jsonDecode($jsonData);
            if (isset($dealData['LotteryS']) && !empty($dealData['LotteryS'])) {
                $data = $dealData['LotteryS'];
                foreach ($data as $item) {
                    // 赛事开始时间(时间戳)
                    $matchTime = strtotime($item['starttime']);
                    // 只更新今天,明天和后天的三天的数据
                    $future2Date = Helper::getDateByComputed();
                    // 赛事开始时间(格式: Y-m-d)
                    $matchStartDate = date('Y-m-d', $matchTime);
                    if (
                        $matchTime <= time()
                        || $matchStartDate > $future2Date
                    ) {
                        // 赛事过期或大于预存赛事, 则跳过
                        continue;
                    }

                    // 数据容器
                    $updateData = [];
                    // 赛事编号
                    $matchNum = $item['matchId'];
                    // 获取该赛事的奖金指数数据
                    $matchIndexData = JcdcMatch::getFieldsByWhere(['match_num' => $matchNum], [
                        'sp_spf', // 胜平负
                        'sp_rqspf', // 让球胜平负
                        'sp_bf', // 比分
                        'sp_jqs', // 进球数
                        'sp_bqc', // 半全场
                        'sp_sxp', // 上下盘单双数奖金指数
                        'sp_spf_var', // 胜平负变化
                        'sp_rqspf_var', // 让球胜平负变化
                        'sp_bf_var', // 比分变化
                        'sp_jqs_var', // 进球数变化
                        'sp_bqc_var', // 半全场变化
                        'sp_sxp_var', // 上下盘单双数奖金指数变化
                    ]);
                    // 该赛事不存在则写入该赛事赔率
                    if (empty($matchIndexData)) {
                        $insertData = [];
                        $insertData['match_num'] = $matchNum;
                        $insertData['sp_spf'] = isset($item['HDA']) ? Helper::jsonEncode($item['HDA']) : ''; // 胜平负奖金指数
                        $insertData['sp_rqspf'] = isset($item['HHDA']) ? Helper::jsonEncode($item['HHDA']) : ''; // 让球胜平负奖金指数
                        $insertData['sp_jqs'] = isset($item['TTG']) ? Helper::jsonEncode($item['TTG']) : ''; // 进球数奖金指数
                        $insertData['sp_bqc'] = isset($item['HF']) ? Helper::jsonEncode($item['HF']) : ''; // 半全场奖金指数
                        $insertData['sp_bf'] = isset($item['CRS']) ? Helper::jsonEncode($item['CRS']) : ''; // 比分奖金指数
                        $insertData['sp_sxp'] = isset($item['SXP']) ? Helper::jsonEncode($item['SXP']) : ''; // 上下盘单双数奖金指数
                        $insertData['create_at'] = Helper::timeFormat(time(), 's'); // 新增时间
                        $insertData['update_at'] = Helper::timeFormat(time(), 's'); // 更新日期
                        // 获取奖金指数变化数据
                        $varApi = self::API_HOST . self::BJ_VAR_URI . $matchNum;
                        $varJson = Helper::curlRequest($varApi);
                        $varData = Helper::jsonDecode($varJson);
                        if (isset($varData['GameId']) && !empty($varData['GameId'])) {
                            $insertData['sp_spf_var'] = PatchModel::sliceIndexVar(isset($varData['HDA']) ? $varData['HDA'] : null); // 胜平负奖金指数变化
                            $insertData['sp_rqspf_var'] = PatchModel::sliceIndexVar(isset($varData['HHDA']) ? $varData['HHDA'] : null); // 让球胜平负奖金指数变化
                            $insertData['sp_bf_var'] = PatchModel::sliceIndexVar(isset($varData['CRS']) ? $varData['CRS'] : null); // 比分奖金指数变化
                            $insertData['sp_jqs_var'] = PatchModel::sliceIndexVar(isset($varData['TTG']) ? $varData['TTG'] : null); // 进球数奖金指数变化
                            $insertData['sp_bqc_var'] = PatchModel::sliceIndexVar(isset($varData['HF']) ? $varData['HF'] : null); // 半全场奖金指数变化
                            $insertData['sp_sxp_var'] = PatchModel::sliceIndexVar(isset($varData['SXP']) ? $varData['SXP'] : null); // 上下盘单双数奖金指数变化
                        } else {
                            $insertData['sp_spf_var'] = '';
                            $insertData['sp_rqspf_var'] = '';
                            $insertData['sp_bf_var'] = '';
                            $insertData['sp_jqs_var'] = '';
                            $insertData['sp_bqc_var'] = '';
                            $insertData['sp_sxp_var'] = '';
                        }

                        // 模拟乐观锁机制, 防止并发
                        $repeatCheckId = JcdcMatch::getValByWhere(['match_num' => $matchNum], 'id');
                        if ($repeatCheckId) {
                            continue;
                        }

                        // 写入数据
                        $result = JcdcMatch::quickCreate($insertData);
                        if (!$result) {
                            PatchLog::log(Config::BJ_CODE, '初次写入竞彩奖金指数数据集失败', var_export($insertData, true));
                            trigger_error('Active Get Data Fails, Class `PatchBJ` Lines 225', E_USER_ERROR);
                        }

                        continue;
                    }

                    // 更新让球数
                    if (
                        isset($item['HDA'])
                        && !empty($item['HDA'])
                        && isset($item['HDA']['H'])
                    ) {
                        JcdcBase::where('match_num', $matchNum)->setField('rqs', $item['HDA']['H']);
                    }

                    // 胜平负奖金指数
                    $hda = isset($item['HDA']) ? $item['HDA'] : null;
                    $updateData['sp_spf'] = self::updateIndexVar($hda, $matchIndexData['sp_spf'], $matchIndexData['sp_spf_var'], $updateData, 'sp_spf_var');
                    // 让球胜平负奖金指数
                    $hhda = isset($item['HHDA']) ? $item['HHDA'] : null;
                    $updateData['sp_rqspf'] = self::updateIndexVar($hhda, $matchIndexData['sp_rqspf'], $matchIndexData['sp_rqspf_var'], $updateData, 'sp_rqspf_var');
                    // 比分奖金指数
                    $crs = isset($item['CRS']) ? $item['CRS'] : null;
                    $updateData['sp_bf'] = self::updateIndexVar($crs, $matchIndexData['sp_bf'], $matchIndexData['sp_bf_var'], $updateData, 'sp_bf_var');
                    // 进球数奖金指数
                    $ttg = isset($item['TTG']) ? $item['TTG'] : null;
                    $updateData['sp_jqs'] = self::updateIndexVar($ttg, $matchIndexData['sp_jqs'], $matchIndexData['sp_jqs_var'], $updateData, 'sp_jqs_var');
                    // 半全场奖金指数
                    $hf = isset($item['HF']) ? $item['HF'] : null;
                    $updateData['sp_bqc'] = self::updateIndexVar($hf, $matchIndexData['sp_bqc'], $matchIndexData['sp_bqc_var'], $updateData, 'sp_bqc_var');
                    // 上下盘单双数奖金指数
                    $sxp = isset($item['SXP']) ? $item['SXP'] : null;
                    $updateData['sp_sxp'] = self::updateIndexVar($sxp, $matchIndexData['sp_sxp'], $matchIndexData['sp_sxp_var'], $updateData, 'sp_sxp_var');
                    // 更新日期
                    $updateData['update_at'] = Helper::timeFormat(time(), 's');

                    // 更新数据
                    JcdcMatch::where('match_num', $matchNum)->update($updateData);
                }
            }
        }
    }

    /**
     * Step 4 查看是否有近期异常赛事
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setExceptionMatch()
    {
        $api = self::API_HOST . self::CM_EXCEPT_URI;
        $jsonResult = Helper::curlRequest($api);
        $result = Helper::jsonDecode($jsonResult);
        if (isset($result['Games'])) {
            $data = $result['Games'];
            foreach ($data as $item) {
                $matchNum = $item['Id'][0];
                $status = self::getStatusByStr($item['Status']);
                JcdcBase::where('match_num', $matchNum)->setField('match_status', $status);
            }
        }
    }

    /**
     * 通过字符串获取状态值
     *
     * @param $str // 异常字段,字符串. 例如: "取消"
     * @return integer
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getStatusByStr($str)
    {
        switch ($str) {
            case '取消':
                return 1;
            case '延期':
                return 2;
            case '腰斩':
                return 3;
            default:
                return 0;
        }
    }

    /**
     * 北京单场爬取竞彩日期
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function patchJcDate()
    {
        // 获取期号
        $api = self::API_HOST . self::BJ_JC_DATE . date('Y');
        $resJson = Helper::curlRequest($api);
        $resData = Helper::jsonDecode($resJson);
        // 获取当前期号
        if (!isset($resData['CurDegree'])) {
            PatchLog::log(Config::BJ_CODE, '实时爬取北单期号发生错误', $resJson);
            // 终止爬取
            exit("实时爬取北单期号发生错误");
        }

        self::$_jcTimeNormal = [$resData['CurDegree']];
    }

    /**
     * step: 6 实时爬取赛事结果
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
        $unDrawData = JcdcOpen::where('status', 0)
            ->field('match_date')
            ->group('match_date')
            ->select()
            ->toArray();
        $matchDates = array_filter(array_column($unDrawData, 'match_date'));
        sort($matchDates);
        foreach ($matchDates as $matchDate) {
            $reApi = self::API_HOST . self::CM_RE_URI . $matchDate;
            $apiData = Helper::curlRequest($reApi);
            $data = Helper::jsonDecode($apiData);
            if (isset($data['Schedule']) && !empty($data['Schedule'])) {
                $schedule = $data['Schedule'];
                foreach ($schedule as $item) {
                    // 保存北单赛果
                    // 北单赛事开奖
                    self::setJcdcOpenAndDraw($item);
                }
            }
        }
    }

    /**
     * 北京单场保存赛事结果和赛事开奖
     *
     * @param $item // 竞彩结果数据
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setJcdcOpenAndDraw($item)
    {
        $matchNum = $item['Id'][0];
        $openData = JcdcOpen::getFieldsByWhere(['match_num' => $matchNum], 'status');
        // 存在该赛事
        if (!empty($openData)) {
            $isAh = JcdcBase::getValByWhere(['match_num' => $matchNum], 'isAh');
            $openStatus = (int)$openData['status'];
            // 该赛事的状态是未开奖状态, 则更新该赛事
            if ($openStatus === 0) {
                $status = 0;
                if (
                    isset($item['Half'])
                    && !empty($item['Half'])
                    && isset($item['Score'])
                    && !empty($item['Score'])
                ) {
                    // 赛事结束, 则设置开奖状态为已开奖
                    $status = 1;
                }

                if ($isAh) {
                    // 对阵相反
                    $halfScore = '';
                    if (isset($item['Half']) && !empty($item['Half'])) {
                        $halfScore = implode('-', array_reverse(explode('-', $item['Half'])));
                    }

                    $totalScore = isset($item['ScoreAll']) && !empty($item['ScoreAll']) ? $item['ScoreAll'][1] . '-' . $item['ScoreAll'][0] : '';
                    $normalScore = isset($item['Score']) && !empty($item['Score']) ? $item['Score'][1] . '-' . $item['Score'][0] : '';
                    $kickScore = isset($item['ScorePoint']) && !empty($item['ScorePoint']) ? $item['ScorePoint'][1] . '-' . $item['ScorePoint'][0] : '';

                } else {
                    $halfScore = isset($item['Half']) && !empty($item['Half']) ? $item['Half'] : '';
                    $totalScore = isset($item['ScoreAll']) && !empty($item['ScoreAll']) ? $item['ScoreAll'][0] . '-' . $item['ScoreAll'][1] : '';
                    $normalScore = isset($item['Score']) && !empty($item['Score']) ? $item['Score'][0] . '-' . $item['Score'][1] : '';
                    $kickScore = isset($item['ScorePoint']) && !empty($item['ScorePoint']) ? $item['ScorePoint'][0] . '-' . $item['ScorePoint'][1] : '';
                }

                $updateData = [
                    'half_score' => $halfScore, // 半场
                    'total_score' => $totalScore, // 全场(含加时)
                    'normal_score' => $normalScore, // 全场
                    'kick_score' => $kickScore, // 点球比分
                    'status' => $status,
                    'update_at' => Helper::timeFormat(time(), 's'),
                ];
                // 更新赛事结果
                JcdcOpen::where('match_num', $matchNum)->setField($updateData);
                // 赛事结束后, 系统自动开奖
                if ($status === 1) {
                    // ... 自动开奖逻辑
                    Order::autoDraw(Config::BJ_CODE, $matchNum);
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
                    if (isset($apiIndexData['num'])) {
                        unset($apiIndexData['num']);
                    }

                    array_unshift($indexVarData, $apiIndexData);
                    $updateData[$varType] = PatchModel::sliceIndexVar($indexVarData);
                }
            } else {
                if (isset($apiIndexData['num'])) {
                    unset($apiIndexData['num']);
                }

                array_unshift($indexVarData, $apiIndexData);
                $updateData[$varType] = PatchModel::sliceIndexVar($indexVarData);
            }
        }

        return $index;
    }
}