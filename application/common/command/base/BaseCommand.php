<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/14
 * Time: 11:15
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command\base;

use app\common\Config;
use app\common\Helper;
use app\common\model\PatchLog;
use think\console\Command;

/**
 * 控制台接口基类
 *
 * Class BaseCommand
 * @package app\common\command
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class BaseCommand extends Command
{

    /* 接口公用域名 */
    // 接口域名
    const API_HOST = 'http://feed.sportsdt.com/fenghuangty';

    /* 足球数据公用SDK */
    // 足球赛事基本详情，URI参数，参数赛事编号，如：123456
    const CM_BASE_URI = '/soccer/index.aspx?type=getgameinfo&gameid=';
    // 足球赛事结果, URI参数, 参数赛事开赛时间, 如: 2019-11-11
    const CM_RE_URI = '/soccer/index.aspx?type=getschedulebydate&date=';
    // 足球异常赛事查询, URI参数
    const CM_EXCEPT_URI = '/soccer/index.aspx?type=getrevocatorygame';

    /* 竞彩足球SDK */
    // 竞彩足球赛事编号列表，URI参数，参数竞彩日期，如：2019-11-11
    const FB_LIST_URI = '/soccer/index.aspx?type=getschedule_jc&date=';
    // 竞彩足球奖金指数实时信息, URI参数, 参数竞彩日期, 如: 2019-11-11
    const FB_RT_URI = '/soccer/index.aspx?type=getschedule_jc_sp&date=';
    // 竞彩足球奖金指数变化信息，URI参数，参数赛事编号，如：123456
    const FB_VAR_URI = '/soccer/index.aspx?type=getsphistory_jc&gameid=';

    /* 北京单场SDK */
    // 北京单场获取期号 如: 2019
    const BJ_JC_DATE = '/soccer/index.aspx?type=getdegree_dc&year=';
    // 北京单场根据期号获取北京单场比赛列表, 如: 20191111
    const BJ_LIST_URI = '/soccer/index.aspx?type=getschedule_dc&degree=';
    // 北京单场根据期号获取奖金指数实时信息, 如: 20191111
    const BJ_RT_URI = '/soccer/index.aspx?type=getschedule_dc_sp&degree=';
    // 北京单场根据期号获取奖金指数变化数据, 如: 123456
    const BJ_VAR_URI = '/soccer/index.aspx?type=getsphistory_dc&gameid=';

    /* 竞彩篮球SDK */
    // 竞彩篮球赛事编号列表，URI参数，参数竞彩日期，如：2019-11-11
    const BB_LIST_URI = '/basketball/index.aspx?type=getschedule_jc&date=';
    // 竞彩篮球赛事基本详情，URI参数，参数赛事编号，如：123456
    const BB_BASE_URI = '/basketball/index.aspx?type=getgameinfo&gameid=';
    // 竞彩篮球赛事结果, URI参数, 参数赛事开赛时间, 如: 2019-11-11
    const BB_RE_URI = '/basketball/index.aspx?type=getschedulebydate&date=';
    // 竞彩篮球奖金指数实时信息, URI参数, 参数竞彩日期, 如: 2019-11-11
    const BB_RT_URI = '/basketball/index.aspx?type=getschedule_jc_sp&date=';
    // 竞彩篮球奖金指数变化信息，URI参数，参数赛事编号，如：123456
    const BB_VAR_URI = '/basketball/index.aspx?type=getsphistory_jc&gameid=';

    /* 排三, 排五结果爬取SDK */
    // 排三结果爬取
    const P3_API = 'http://wd.apiplus.net/newly.do?token=tb23b17878253a368k&code=pl3&format=json';
    // 排五结果爬取
    const P5_API = 'http://wd.apiplus.net/newly.do?token=tb23b17878253a368k&code=pl5&format=json';

    /* 幸运飞艇SDK */
    /* 接口网站 http://www.sdps365.cn/ (弃用 购买日期: 2019年6月 到期2020年6月) */
    //const FT_API = 'http://api.sdps365.cn/474b9e6b36a248be437bb628ccafdab965a7b730670167b893acb575496b54d5';
    /* 接口网址 https://www.b1cp.com/  (购买日期: 2019年7月12日  到期时间: 2019年8月12日) */
    const FT_API = 'http://api.b1api.com/api?p=json&t=xyft&limit=20&token=D0EAB43121192E5D';

    /* 参数配置 */
    // 记录爬取成功日志时间间隔为3h
    const LOG_INTERVAL = 10800;
    // 足彩 - 竞彩日期(三个彩期: 今天, 明天和后天), 格式: [2019-10-11, 2019-10-12, 2019-10-13]
    public static $_jcTime;
    // 北单 - 竞彩日期(一个彩期, 具体彩期由接口提供), 格式: [20191011]
    public static $_jcTimeNormal;
    // 篮彩 - 竞彩日期(三个彩期: 今天, 明天和后天), 格式: [2019-10-11, 2019-10-12, 2019-10-13]
    public static $_lcJcTime;

    /**
     * 初始化参数
     *
     * PatchFB constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        // 足彩竞彩日期
        $today = Helper::timeFormat(time(), 'd');
        $tomorrow = date('Y-m-d', strtotime('+1 days'));
        $preTomorrow = date('Y-m-d', strtotime('+2 days'));
        self::$_jcTime = [$today, $tomorrow, $preTomorrow];
        // 篮彩竞彩日期
        self::$_lcJcTime = [$today, $tomorrow, $preTomorrow];

        parent::__construct($name);
    }

    /**
     * step: 2
     *
     * 爬取竞彩赛程比赛编号列表信息(派发通道)
     *
     * @param $jcCode // 竞彩代码
     * @param $uri // URI
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function patchCurrentJcMatchNumData($jcCode, $uri)
    {
        // 根据彩种获取彩种的彩期
        switch ($jcCode) {
            case Config::BJ_CODE: // 北京单场
                $jcDateArr = self::$_jcTimeNormal;
                break;
            case Config::ZC_CODE: // 足彩
                $jcDateArr = self::$_jcTime;
                break;
            case Config::LC_CODE: // 篮彩
                $jcDateArr = self::$_lcJcTime;
        }

        foreach ($jcDateArr as $jcDate) {
            // 爬取赛事列表
            $api = self::API_HOST . $uri . $jcDate;
            $jsonReturn = Helper::curlRequest($api);
            $data = Helper::jsonDecode($jsonReturn);
            if (isset($data['LotteryS'])) {
                // 写入赛事列表
                $model = new PatchModel();
                $model->insertJCInfo($jcCode, $data['LotteryS'], $jcDate);
            }
        }
    }

    /**
     * Step: 7
     * 记录爬取成功状态
     *
     * @param $jcCode // 彩种代码
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function executeSuccess($jcCode)
    {
        $configFile = PatchModel::getConfigJsonFile($jcCode);
        $jsonConfig = file_get_contents($configFile);
        $config = Helper::jsonDecode($jsonConfig);
        $logTimeAttr = PatchModel::LOG_TIME;

        if (
            !isset($config[$jcCode])
            || !isset($config[$jcCode][$logTimeAttr]) // 不存在日志标识
            || empty($config[$jcCode][$logTimeAttr]) // 日志标识为空
            || time() - (int)$config[$jcCode][$logTimeAttr] >= self::LOG_INTERVAL // 日志时间大于日志间隔配置时间
        ) {
            // 每三个小时记录一次爬取状态
            PatchLog::log($jcCode, '数据爬取成功', '', 1);
            // 记录当前日志时间
            $config[$jcCode][$logTimeAttr] = time();
            // 写入配置文件
            file_put_contents($configFile, Helper::jsonEncode($config));
        }
    }
}