<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/14
 * Time: 11:22
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command\base;

use app\common\BaseModel;
use app\common\command\PatchBB;
use app\common\command\PatchBJ;
use app\common\command\PatchFB;
use app\common\Config;
use app\common\Helper;
use app\common\model\JcdcBase;
use app\common\model\JcdcOpen;
use app\common\model\JclqBase;
use app\common\model\JclqOpen;
use app\common\model\JczqBase;
use app\common\model\JczqOpen;
use app\common\model\Lottery;
use app\common\model\PatchLog;
use think\Exception;

/**
 * 数据爬取公共模型
 *
 * Class Patch
 * @package app\common\command\base
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PatchModel extends BaseModel
{
    // config.json记录成功爬取日志的时间节点
    const LOG_TIME = 'logTime';
    // 系统截止时间为开赛前?分钟
    const BEFORE_TIME = 120; // 赛前2分钟截止

    /**
     * 新增彩种
     *
     * @param $jcCode // 彩种代码
     * @return bool|array // 新增是否成功
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function newAdd($jcCode)
    {
        // 新增彩种，判断是否在三种已知彩种之中。不存在则为其他彩种
        $data = [
            'status' => 0, // 彩种状态，0：出售中
            'match' => 1, // 赛事数据爬取开始
            'result' => 1, // 赛事结果爬取开始
            'create_at' => Helper::timeFormat(time(), 's'), // 创建时间
            'update_at' => Helper::timeFormat(time(), 's'), // 修改时间
            'is_run' => 1, // 是否已启用上线，1：是
        ];

        switch ($jcCode) {
            case Config::ZC_CODE: // 足彩
                $data['name'] = '竞彩足球'; // 彩种名称
                $data['code'] = Config::ZC_CODE; // 彩种代码
                break;
            case Config::LC_CODE: // 篮彩
                $data['name'] = '竞彩篮球'; // 彩种名称
                $data['code'] = Config::LC_CODE; // 彩种代码
                break;
            case Config::BJ_CODE: // 北京单场
                $data['name'] = '北京单场'; // 彩种名称
                $data['code'] = Config::BJ_CODE; // 彩种代码
                break;
            case Config::P3_CODE: // 排列三
                $data['name'] = '排列三';
                $data['code'] = Config::P3_CODE;
                break;
            case Config::P5_CODE: // 排列五
                $data['name'] = '排列五';
                $data['code'] = Config::P5_CODE;
                break;
            case Config::AO_CODE: // 澳彩
                $data['name'] = '澳彩';
                $data['code'] = Config::AO_CODE;
                break;
            case Config::PC_CODE: // 葡彩
                $data['name'] = '葡彩';
                $data['code'] = Config::PC_CODE;
                break;
            case Config::FT_CODE: // 幸运飞艇
                $data['name'] = '幸运飞艇';
                $data['code'] = Config::FT_CODE;
                break;
            default:
                $data['name'] = '暂无名称'; // 彩种名称
                $data['code'] = $jcCode; // 彩种代码
        }

        // 数据表格插入
        $result = Lottery::quickCreate($data);
        if ($result) {
            return true;
        }

        return $data;
    }

    /**
     * :step 1
     *
     * 设置当前彩种运行状态，设置为已启用上线。
     *
     * @param $jcCode // 彩种代码
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function setRunStatus($jcCode)
    {
        // 获取当前彩种运行状态。
        $isRun = Lottery::where('code', $jcCode)->value('is_run', null);
        // 彩种不存在，则新增当前彩种。
        if ($isRun === null) {
            try {
                $result = self::newAdd($jcCode);
                if ($result !== true) {
                    throw new Exception(var_export($result, true));
                }

            } catch (\Exception $e) {
                // 新增当前彩种失败，记录爬取日志
                PatchLog::log($jcCode, $jcCode . '新增彩种列表失败', $e->getMessage());
                trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        // 设置彩种启用上线状态
        if ($isRun === 0) {
            $setField = [
                'match' => 1, // 赛事数据爬取开始
                'result' => 1, // 赛事结果爬取开始
                'is_run' => 1, // 启用上线
            ];

            Lottery::where('code', $jcCode)->setField($setField);
        }
    }

    /**
     * 写入竞彩赛程列表基础信息 - 总阀
     *
     * @param $jcCode // 竞彩代码
     * @param $data // 数据
     * @param $jcDate // 竞彩日期
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertJCInfo($jcCode, $data, $jcDate)
    {
        try {
            switch ((string)$jcCode) {
                case Config::ZC_CODE:
                    // 足彩比赛编号列表数据
                    self::saveZcMatchNum($data, $jcDate);
                    break;
                case Config::LC_CODE:
                    // 篮彩比赛数据列表
                    self::saveLcMatchNum($data, $jcDate);
                    break;
                case Config::BJ_CODE:
                    // 北京单场比赛数据列表
                    self::saveBjMatchNum($data, $jcDate);
                    break;
                default:
                    // 抛出异常
                    trigger_error('Write Match List Fail, Undefined Vars ' . $jcCode . ' Class `PatchModel` Lines 207', E_USER_ERROR);
            }
        } catch (\Exception $e) {
            PatchLog::log($jcCode, '写入竞彩赛程列表信息时发生错误', $e->getMessage());
            trigger_error($e->getMessage());
        }
    }

    /**
     * 写入竞彩足球比赛编号列表信息
     *
     * @param $params // 接口原始数据
     * @param $jcDate // 竞彩日期
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function saveZcMatchNum($params, $jcDate)
    {
        foreach ($params as $item) {
            $fields = JczqBase::getFieldsByWhere(['match_num' => $item['matchId']], 'id,jc_num');
            if (!empty($fields)) {
                // 竞彩编号更新竞彩编号
                if(empty($fields['jc_num']) && !empty($item['num'])){
                    JczqBase::quickCreate([
                        'id' => $fields['id'],
                        'jc_num' => $item['num'],
                        'sort' => date('Ymd', strtotime($jcDate)) . mb_substr($item['num'], 2, null, 'UTF-8'),
                    ], true);
                }
                // 跳过该赛事
                continue;
            }

            // 处理开赛时间格式
            $matchTime = strtotime($item['starttime']);
            // 获取系统截止时间(赛事前2分钟截止)
            $sysCutOffTime = $matchTime - self::BEFORE_TIME;

            // 计算赛事系统截止时间
            // 获取当前彩期最大截止时间
            $week = date('w', strtotime($jcDate));
            if (in_array($week, [6, 0])) {
                // 周六, 周日 截止到竞彩日期第二天凌晨1点
                $jcCutDownTime = strtotime($jcDate . ' 23:00:00' . '+2 hours') - self::BEFORE_TIME;
            } else {
                // 日常截止时间晚上23:58分截止
                $jcCutDownTime = strtotime($jcDate . ' 23:58:00');
            }

            // 第二天开业时间
            $tomorrowShutDownTime = strtotime($jcDate . ' 09:00:00' . '+1 days');
            // 如果系统截止时间大于最大截止时间, 则为最大截止时间
            if ($sysCutOffTime > $jcCutDownTime && $sysCutOffTime < $tomorrowShutDownTime) {
                $sysCutOffTime = $jcCutDownTime;
            }

            $data = [];
            // 组装数据表单
            $isAh = isset($item['isAH']) ? $item['isAH'] : 0; // 对阵是否相反
            $data['jc_date'] = $jcDate . ' 00:00:00'; // 竞彩日期
            $data['sort'] = date('Ymd', strtotime($data['jc_date'])) . mb_substr($item['num'], 2, null, 'UTF-8');
            $data['jc_num'] = $item['num']; // 竞彩编号
            $data['match_num'] = $item['matchId']; // 比赛编号
            $data['isAh'] = $isAh;
            $data['match_time'] = $matchTime; // 比赛时间
            $data['sys_cutoff_time'] = Helper::timeFormat($sysCutOffTime, 's'); // 系统截止时间
            $data['sale_status'] = 1; // 自动出售, 1:出售中
            // 获取比赛赛事对阵基本详情
            $extraData = PatchFB::getZcDetail($data['match_num'], Config::ZC_CODE, $isAh);
            $resultData = array_merge($data, $extraData);
            // 新增足彩赛事基础信息数据
            $result = JczqBase::quickCreate($resultData);
            if (!$result) {
                PatchLog::log(Config::ZC_CODE, '写入竞彩赛程详情信息时发生错误', '爬取赛事详情数据时接口参数错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 262 lines', E_USER_ERROR);
            }

            // 新增足彩开奖表
            $isOk = JczqOpen::quickCreate([
                'match_num' => $data['match_num'],
                'create_at' => Helper::timeFormat(time(), 's'),
                'match_date' => date('Y-m-d', $extraData['start_time']),
            ]);
            if (!$isOk) {
                PatchLog::log(Config::ZC_CODE, '写入竞彩开奖表赛事ID时发生错误', '写入竞彩开奖表赛事ID时发生错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 273 lines', E_USER_ERROR);
            }
        }
    }

    /**
     * 写入北京单场比赛编号列表信息
     *
     * @param $params // 接口原始数据
     * @param $jcDate // 彩期
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function saveBjMatchNum($params, $jcDate)
    {
        foreach ($params as $item) {
            // 预处理时间格式
            $matchTime = strtotime($item['starttime']);
            // 获取系统截止时间
            $sysCutOffTime = $matchTime - self::BEFORE_TIME;
            // 只获取今天, 明天和后天(三天)的赛事
            $future2Date = Helper::getDateByComputed();
            // 比赛开始日期(格式: Y-m-d)
            $matchStartDate = date('Y-m-d', $matchTime);
            if (
                $sysCutOffTime <= time()
                || $matchStartDate > $future2Date
            ) {
                // 赛事过期或后天以后的数据, 则跳过
                continue;
            }

            // 存在赛事则不存储
            $isExist = JcdcBase::getValByWhere(['match_num' => $item['matchId']], 'id');
            if ($isExist) {
                continue;
            }

            // 组装数据表单
            $data = [];
            $isAh = isset($item['isAH']) ? $item['isAH'] : 0; // 对阵是否相反
            $data['jc_date'] = date('Y-m-d H:i:s', strtotime($jcDate)); // 竞彩日期
            $data['jc_num'] = $item['num']; // 竞彩编号
            $data['sort'] = date('Ymd', strtotime($jcDate)) . $item['num']; // 排序
            $data['match_num'] = $item['matchId']; // 比赛编号
            $data['isAh'] = $isAh; // 对阵是否相反
            $data['match_time'] = $matchTime; // 比赛时间
            $data['sys_cutoff_time'] = Helper::timeFormat($sysCutOffTime, 's'); // 系统截止时间
            $data['sale_status'] = 1; // 自动出售, 1:出售中
            // 获取比赛赛事对阵基本详情
            $extraData = PatchBJ::getBjDetail($data['match_num'], Config::BJ_CODE, $isAh);
            $resultData = array_merge($data, $extraData);
            // 新增足彩赛事基础信息数据
            $result = JcdcBase::quickCreate($resultData);
            if (!$result) {
                PatchLog::log(Config::BJ_CODE, '写入竞彩赛程详情信息时发生错误', '爬取赛事详情数据时接口参数错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 330 lines', E_USER_ERROR);
            }
            // 新增足彩开奖表
            $isOk = JcdcOpen::quickCreate([
                'match_num' => $data['match_num'],
                'create_at' => Helper::timeFormat(time(), 's'),
                'match_date' => date('Y-m-d', $extraData['start_time']),
            ]);
            if (!$isOk) {
                PatchLog::log(Config::BJ_CODE, '写入竞彩开奖表赛事ID时发生错误', '写入竞彩开奖表赛事ID时发生错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 340 lines', E_USER_ERROR);
            }
        }
        //爬取竞彩数据的
    }

    /**
     * 写入竞彩篮球比赛编号列表信息
     *
     * @param $params // 接口原始数据
     * @param $jcDate // 彩期
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function saveLcMatchNum($params, $jcDate)
    {
        foreach ($params as $item) {
            $fields = JclqBase::getFieldsByWhere(['match_num' => $item['matchId']], 'id,jc_num');
            if (!empty($fields)) {
                if(empty($fields['jc_num']) && !empty($item['num'])){
                    JclqBase::quickCreate([
                        'id' => $fields['id'],
                        'jc_num' => $item['num'],
                        'sort' => date('Ymd', strtotime($jcDate)) . mb_substr($item['num'], 2, null, 'UTF-8'),
                    ], true);
                }
                // 跳过该赛事
                continue;
            }

            // 处理开赛时间格式
            $matchTime = strtotime($item['starttime']);
            // 获取系统截止时间(赛事前2分钟截止)
            $sysCutOffTime = $matchTime - self::BEFORE_TIME;
            // 计算赛事系统截止时间
            // 获取当前彩期最大截止时间
            $week = date('w', strtotime($jcDate));
            if (in_array($week, [6, 0])) {
                // 周六, 周日 截止到竞彩日期第二天凌晨1点
                $jcCutDownTime = strtotime($jcDate . ' 23:00:00' . '+2 hours') - self::BEFORE_TIME;
            } else {
                // 日常截止时间晚上23:58分截止
                $jcCutDownTime = strtotime($jcDate . ' 23:58:00');
            }

            // 第二天开业时间
            $tomorrowShutDownTime = strtotime($jcDate . ' 09:00:00' . '+1 days');
            // 如果系统截止时间大于最大截止时间, 则为最大截止时间
            if ($sysCutOffTime > $jcCutDownTime && $sysCutOffTime < $tomorrowShutDownTime) {
                $sysCutOffTime = $jcCutDownTime;
            }

            $data = [];
            // 组装数据表单
            $isAh = isset($item['isAH']) ? $item['isAH'] : 0; // 对阵顺序
            $data['isAh'] = $isAh;
            $data['jc_date'] = $jcDate . ' 00:00:00'; // 竞彩日期
            $data['sort'] = date('Ymd', strtotime($data['jc_date'])) . mb_substr($item['num'], 2, null, 'UTF-8');
            $data['jc_num'] = $item['num']; // 竞彩编号
            $data['match_num'] = $item['matchId']; // 比赛编号
            $data['match_time'] = $matchTime; // 比赛时间
            $data['sys_cutoff_time'] = Helper::timeFormat($sysCutOffTime, 's'); // 系统截止时间
            $data['sale_status'] = 1; // 自动出售, 1:出售中
            // 获取比赛赛事对阵基本详情
            $extraData = PatchBB::getLcDetail($data['match_num'], Config::LC_CODE, $isAh);
            $resultData = array_merge($data, $extraData);
            // 新增篮彩赛事基础信息数据
            $result = JclqBase::quickCreate($resultData);
            if (!$result) {
                PatchLog::log(Config::LC_CODE, '写入竞彩赛程详情信息时发生错误', '爬取赛事详情数据时接口参数错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 410 lines', E_USER_ERROR);
            }

            // 新增篮彩开奖表
            $isOk = JclqOpen::quickCreate([
                'match_num' => $data['match_num'],
                'create_at' => Helper::timeFormat(time(), 's'),
                'match_date' => date('Y-m-d', $extraData['start_time']),
            ]);
            if (!$isOk) {
                PatchLog::log(Config::LC_CODE, '写入竞彩开奖表赛事ID时发生错误', '写入竞彩开奖表赛事ID时发生错误');
                trigger_error('Write Match Detail Fail, Class `PatchModel` 421 lines', E_USER_ERROR);
            }
        }
    }

    /**
     * 获取config.json文件地址
     *
     * @param $jcCode // 竞彩代码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getConfigJsonFile($jcCode)
    {
        $originPath = __DIR__ . '/config.json';
        $configFile = realpath($originPath);
        if (!$configFile) {
            PatchLog::log($jcCode, '格式化config.json文件路径失败', $originPath);
            trigger_error('Open `config.json` File, Class `PatchModel` lines 442', E_USER_ERROR);
        }

        return $configFile;
    }

    /**
     * 截取奖金指数变化数据
     *
     * @param $indexVar // 奖金指数变化数据, 二维数组
     * @return array|false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function sliceIndexVar($indexVar)
    {
        $resultStr = '';
        if (!empty($indexVar) && is_array($indexVar)) {
            // 当变化数据大于3条时, 只记录最近3条数据
            if (count($indexVar) > 3) {
                $resultStr = array_slice($indexVar, 0, 3);
            } else {
                $resultStr = $indexVar;
            }

            $resultStr = Helper::jsonEncode($resultStr);
        }

        return $resultStr;
    }
}