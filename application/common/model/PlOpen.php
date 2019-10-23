<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/11
 * Time: 13:29
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;
use app\common\Config;

/**
 * 排列三,排列五开奖模型
 *
 * Class PlOpen
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PlOpen extends BaseModel
{
    // 排三直选赔率
    const P3_CM_ODDS = 1040;
    // 排三组选六赔率
    const P3_Z6_ODDS = 173;
    // 排三组选三赔率
    const P3_Z3_ODDS = 346;
    // 排五赔率
    const P5_ODDS = 100000;
    // 澳彩赔率
    const AO_ODDS = 1040;
    // 葡彩赔率
    const PU_ODDS = 100000;
    // 平台抽取的跟单佣金比 (3%)
    const PLAIN_COMMISSION_RATE = 0.03;

    /**
     * 通过玩法代码获取数字彩固定赔率
     *
     * @param $playCode // 玩法代码
     * @return int|null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getOddsByPlayCode($playCode)
    {
        switch ($playCode) {
            case 'zhihe': // 排三直选和值
            case 'zhipu': // 排三直选普通
                return self::P3_CM_ODDS;
            case 'zusanbao': // 排三组三包号
            case 'zusanhe': // 排三组三和值
                return self::P3_Z3_ODDS;
            case 'zuliubao': // 排三组六包号
            case 'zuliuhe': // 排三组六和值
            case 'zuliudantuo': // 排三组六胆拖
                return self::P3_Z6_ODDS;
            case 'p5zhipu': // 排五直选普通
                return self::P5_ODDS;
            case 'aozhipu': // 澳彩直选普通
            case 'aozhihe': // 澳彩直选和值
                return self::AO_ODDS;
            case 'puzhipu': // 葡彩直选普通
                return self::PU_ODDS;
            default:
                return null;
        }
    }

    /**
     * 通过期号和彩种类型获取开奖结果
     *
     * @param $expect // 期号
     * @param $ctype // 彩种类型
     * @return string|null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getOpenCodeByExpect($expect, $ctype)
    {
        $openCode = self::where('expect', $expect)
            ->where('ctype', $ctype)
            ->value('open_code');

        return $openCode ?: null;
    }

    /**
     * 排列三获取开奖号码类型(组六,组三)
     *
     * @param array $openCode // 开奖码
     * @return string // 类型标识
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getOpenCodeTypeByOpenCode(array $openCode)
    {
        $initCount = count($openCode);
        $uniqueCount = count(array_unique($openCode));
        if ($initCount === $uniqueCount) {
            return 'zu_6';
        }

        return 'zu_3';
    }

    /**
     * 幸运飞艇 冠亚和 大小,单双和数值算法
     *
     * @param $openCode // 开奖号码
     * @param $type // ds: 单双  dx: 大小  sz: 数值
     * @author CleverStone
     * @return string
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getGuanYaHeArith($openCode, $type = 'ds')
    {
        $codeArr = explode(',', $openCode);
        $champion = $codeArr[0];
        $runnerUp = $codeArr[1];
        $sum = $champion + $runnerUp;
        if (!strcmp($type, 'ds')) {
            if ($sum % 2) {
                return 'even'; // 单
            }

            return 'odds'; // 双
        } elseif (!strcmp($type, 'dx')) {
            if ($sum <= 11) {
                return 'small';
            }

            return 'big';
        } else {
            return $sum;
        }
    }

    /**
     * 幸运飞艇 单双/大小算法
     *
     * @param $openCode // 幸运飞艇开奖号码
     * @param int $flag // 排名
     * @param $type // ds(单双)  dx(大小) sz(数值)
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getEvenAndOdds($openCode, $flag = 1, $type = 'ds')
    {
        if (!strpos($openCode, ',')) {
            return 'null';
        }

        $codeArr = explode(',', $openCode);
        $key = (int)$flag - 1;
        $codeVal = $codeArr[$key];
        if (!strcmp($type, 'ds')) {
            // 单双
            if ($codeVal % 2) {
                return 'even'; // 单
            }

            return 'odds'; // 双
        } elseif (!strcmp($type, 'dx')) {
            if ($codeVal >= 6) {
                return 'big'; // 大
            }

            return 'small';
        } else {
            return $codeVal;
        }
    }

    /**
     * 幸运飞艇 龙虎算法
     *
     * @param $openCode // 幸运飞艇开奖号码
     * @param int $flag // 排名
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getLongAndHu($openCode, $flag = 1)
    {
        if (!strpos($openCode, ',')) {
            return 'null';
        }

        list($one, $two, $three, $four, $five, $six, $seven, $eight, $nine, $ten) = explode(',', $openCode);

        switch ($flag) {
            case 1: // 第一名
                if ($one > $ten) {
                    return 'long';
                }

                return 'hu';
            case 2: // 第二名
                if ($two > $nine) {
                    return 'long';
                }

                return 'hu';
            case 3: // 第三名
                if ($three > $eight) {
                    return 'long';
                }

                return 'hu';
            case 4: // 第四名
                if ($four > $seven) {
                    return 'long';
                }

                return 'hu';
            case 5: // 第五名
                if ($five > $six) {
                    return 'long';
                }

                return 'hu';
            default:
                return 'null';
        }
    }

    // 按顺序返回开奖结果描述
    // $openCode 幸运飞艇开奖号码
    public static function getResultDescribe($openCode)
    {
        if (!strpos($openCode, ',')) {
            return false;
        }

        list($one, $two, $three, $four, $five, $six, $seven, $eight, $nine, $ten) = explode(',', $openCode);

        // 冠亚和值
        $num = $one + $two;

        // 冠亚和值大小
        if ($num <= 11) {
            $size = 'small';
        } else {
            $size = 'big';
        }

        // 冠亚和值单双
        if ($num % 2) {
            $type = 'even'; // 单
        } else {
            $type = 'odds'; // 双
        }

        // 龙虎比较
        if ($one > $ten) {
            $str1 = 'long';
        } else {
            $str1 = 'hu';
        }

        if ($two > $nine) {
            $str2 = 'long';
        } else {
            $str2 = 'hu';
        }

        if ($three > $eight) {
            $str3 = 'long';
        } else {
            $str3 = 'hu';
        }

        if ($four > $seven) {
            $str4 = 'long';
        } else {
            $str4 = 'hu';
        }

        if ($five > $six) {
            $str5 = 'long';
        } else {
            $str5 = 'hu';
        }

        return $str = $num . ',' . $size . ',' . $type . ',' . $str1 . ',' . $str2 . ',' . $str3 . ',' . $str4 . ',' . $str5;
    }

    /**
     * 数字彩开奖 获取订单中奖金额
     *
     * @param array $betContent // 投注项二维数组
     *  例如:
     *  排列三投注项:
     *  [
     *   {
     *      "play": "zhihe",
     *      "bet": ["1", "2", "3", "12"],
     *      "zhu": 10,
     *      "amount": 125
     *   },
     *   {
     *      "play": "zhipu",
     *      "bet": ["1,2,3", "2,3,5", "8,6,3", "2,4,6"],
     *      "zhu": 15,
     *      "amount": 220
     *  }
     * ]
     *
     * 幸运飞艇投注项
     * [
     *   {
     *    "play": "lm",
     *    "bet": [
     *    {"type": "g","value": ["odds|1|1.998", "big|2|1.998", "long|3|1.998", "even|4|1.998", "small|5|1.998"]},
     *    {"type": "y","value": ["big|6|1.998", "small|7|1.998"]},
     *    {"type": "3","value": ["small|8|1.998"]},
     *    {"type": "4","value": ["big|9|1.998"]},
     *    {"type": "5","value": ["even|10|1.998", "small|11|1.998"]}
     *    ]
     *   },
     *   {
     *   "play": "gh",
     *   "bet": [
     *   {"type": "gyh","value": ["big|15|2.2", "even|16|2.19", "7|17|14.5", "9|18|10.5", "18|1900|42.5"]}
     *     ]
     *  },
     *  {
     *   "play": "pm",
     *    "bet": [
     * {"type": "g","value": ["4|12|9.98", "7|13|9.98"]},
     * {"type": "y","value": ["3|14|9.98"]}
     *     ]
     * }
     * ]
     * @param string $openCode // 开奖号码, 例如: 排三(1,4,6)
     * @param integer $ctype // 数字彩类型 1: 排三 2: 排五  对应pl_open表ctype字段
     * @param string $orderNo // 订单号
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getDrawAmountByBetContent(array $betContent, $openCode, $ctype, $orderNo)
    {
        $openCode = (string)$openCode;
        switch ($ctype) {
            case 1: // 排三
                // 开奖号码数组 [1, 4, 5]
                $openCodeArr = explode(',', $openCode);
                // 开奖号码和值
                $openCodeSum = array_sum($openCodeArr);
                // 通过开奖号码获取号码类型, zu_6/zu_3
                $openCodeType = self::getOpenCodeTypeByOpenCode($openCodeArr);
                // 中奖金额
                $drawAmount = [];
                $drawAmount['zhipu'] = 0;
                $drawAmount['zhihe'] = 0;
                $drawAmount['zusanbao'] = 0;
                $drawAmount['zusanhe'] = 0;
                $drawAmount['zuliudantuo'] = 0;
                $drawAmount['zuliubao'] = 0;
                $drawAmount['zuliuhe'] = 0;
                // 中奖逻辑
                foreach ($betContent as $item) {
                    $bet = (array)$item['bet'];
                    switch ($item['play']) {
                        case 'zhipu': // 直选普通
                            // 中奖
                            if (in_array($openCode, $bet, true)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        case 'zhihe': // 直选和值
                            // 中奖
                            if (in_array($openCodeSum, $bet)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        case 'zusanbao': // 组三包号
                            if (!strcasecmp($openCodeType, 'zu_3')) {
                                foreach ($bet as $betItem) {
                                    $betItemArr = explode(',', $betItem, 2);
                                    $intersectArr = array_intersect($betItemArr, $openCodeArr);
                                    if (count($intersectArr) === 2) {
                                        $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                                    }
                                }
                            }

                            break;
                        case 'zusanhe': // 组三和值
                            if (!strcasecmp($openCodeType, 'zu_3')) {
                                if (in_array($openCodeSum, $bet)) {
                                    $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                                }
                            }

                            break;
                        case 'zuliudantuo': // 组六胆拖
                        case 'zuliubao': // 组六包号
                            if (!strcasecmp($openCodeType, 'zu_6')) {
                                foreach ($bet as $betItem) {
                                    $betItemArr = explode(',', $betItem, 3);
                                    $intersectArr = array_intersect($betItemArr, $openCodeArr);
                                    if (count($intersectArr) === 3) {
                                        $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                                    }
                                }
                            }

                            break;
                        case 'zuliuhe': // 组六和值
                            if (!strcasecmp($openCodeType, 'zu_6')) {
                                if (in_array($openCodeSum, $bet)) {
                                    $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                                }
                            }

                            break;
                        default:
                            Helper::log('系统', '数字彩开奖', '排三投注项存在未知玩法类型', "订单号: {$orderNo}", 0);
                            trigger_error('排列三订单投注项存在未知玩法类型');
                    }
                }

                return array_sum($drawAmount);
            case 2: // 排五
                // 中奖金额
                $drawAmount = [];
                $drawAmount['p5zhipu'] = 0;
                // 中奖逻辑
                foreach ($betContent as $item) {
                    $bet = (array)$item['bet'];
                    switch ($item['play']) {
                        case 'p5zhipu': // 直选普通
                            // 中奖
                            if (in_array($openCode, $bet, true)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        default:
                            Helper::log('系统', '数字彩开奖', '排列五投注项存在未知玩法类型', "订单号: {$orderNo}", 0);
                            trigger_error('排列五订单投注项存在未知玩法类型');
                    }
                }

                return array_sum($drawAmount);
            case 3: // 澳彩
                // 开奖号码数组 [1, 4, 5]
                $openCodeArr = explode(',', $openCode);
                // 开奖号码和值
                $openCodeSum = array_sum($openCodeArr);
                // 中奖金额
                $drawAmount = [];
                $drawAmount['aozhipu'] = 0;
                $drawAmount['aozhihe'] = 0;
                // 中奖逻辑
                foreach ($betContent as $item) {
                    $bet = (array)$item['bet'];
                    switch ($item['play']) {
                        case 'aozhipu': // 澳彩直选普通
                            // 中奖
                            if (in_array($openCode, $bet, true)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        case 'aozhihe': // 澳彩直选和值
                            // 中奖
                            if (in_array($openCodeSum, $bet)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        default:
                            Helper::log('系统', '数字彩开奖', '澳彩投注项存在未知玩法类型', "订单号: {$orderNo}", 0);
                            trigger_error('澳彩订单投注项存在未知玩法类型');
                    }
                }

                return array_sum($drawAmount);
            case 4: // 葡彩
                // 中奖金额
                $drawAmount = [];
                $drawAmount['puzhipu'] = 0;
                // 中奖逻辑
                foreach ($betContent as $item) {
                    $bet = (array)$item['bet'];
                    switch ($item['play']) {
                        case 'puzhipu': // 葡彩直选普通
                            // 中奖
                            if (in_array($openCode, $bet, true)) {
                                $drawAmount[$item['play']] = self::getOddsByPlayCode($item['play']);
                            }

                            break;
                        default:
                            Helper::log('系统', '数字彩开奖', '葡彩投注项存在未知玩法类型', "订单号: {$orderNo}", 0);
                            trigger_error('葡彩订单投注项存在未知玩法类型');
                    }
                }

                return array_sum($drawAmount);
            case 5: // 幸运飞艇
                // 中奖金额
                $drawAmount = 0;
                // 中奖逻辑
                foreach ($betContent as $item) {
                    $play = $item['play'];
                    $bet = $item['bet'];
                    switch ((string)$play) {
                        case 'lm': // 两面
                            array_walk($bet, function ($value) use ($openCode, &$drawAmount) {
                                $betBody = $value['value']; // 投注内容
                                switch ((string)$value['type']) { // 处理投注类型
                                    case 'g':
                                        $flag = 1;
                                        break;
                                    case 'y':
                                        $flag = 2;
                                        break;
                                    default:
                                        $flag = (int)$value['type'];
                                }

                                // 获取1-10名船艇的龙虎大小单双
                                $longHu = self::getLongAndHu($openCode, $flag);
                                $daXiao = self::getEvenAndOdds($openCode, $flag, 'dx');
                                $danShuang = self::getEvenAndOdds($openCode, $flag, 'ds');
                                $reLmArr = [$longHu, $daXiao, $danShuang];
                                foreach ($betBody as $bomb) {
                                    list($re, $money, $i) = explode('|', $bomb); // odds|1|1.998
                                    if (in_array($re, $reLmArr)) {
                                        $drawAmount += $money * $i;
                                    }
                                }
                            });
                            break;
                        case 'gh': // 冠亚和
                            array_walk($bet, function ($value) use ($openCode, &$drawAmount) {
                                $betBody = $value['value']; // 投注内容
                                if (!strcmp($value['type'], 'gyh')) {
                                    $danShuang = self::getGuanYaHeArith($openCode, 'ds');
                                    $daXiao = self::getGuanYaHeArith($openCode, 'dx');
                                    $shuZhi = self::getGuanYaHeArith($openCode, 'sz');
                                    $reGhArr = [$danShuang, $daXiao, $shuZhi];
                                    foreach ($betBody as $bomb) {
                                        list($re, $money, $i) = explode('|', $bomb); // odds|1|1.998
                                        if (in_array($re, $reGhArr)) {
                                            $drawAmount += $money * $i;
                                        }
                                    }
                                }
                            });
                            break;
                        case 'pm': // 排名
                            array_walk($bet, function ($value) use ($openCode, &$drawAmount) {
                                $betBody = $value['value']; // 投注内容
                                switch ((string)$value['type']) { // 处理投注类型
                                    case 'g':
                                        $flag = 1;
                                        break;
                                    case 'y':
                                        $flag = 2;
                                        break;
                                    default:
                                        $flag = (int)$value['type'];
                                }

                                // 获取1-10名船艇的编号
                                $shuZhi = self::getEvenAndOdds($openCode, $flag, 'sz');
                                foreach ($betBody as $bomb) {
                                    list($re, $money, $i) = explode('|', $bomb); // odds|1|1.998
                                    if ($shuZhi == $re) {
                                        $drawAmount += $money * $i;
                                    }
                                }
                            });
                            break;
                        default:
                            Helper::log('系统', '数字彩开奖', '幸运飞艇投注项存在未知玩法类型', "订单号: {$orderNo}", 0);
                            trigger_error('幸运飞艇订单投注项存在未知玩法类型');
                    }
                }

                return $drawAmount;
            default:
                Helper::log('系统', '数字彩开奖', '未知数字彩种', "订单号: {$orderNo}", 0);
                trigger_error('未知数字彩种');
        }
    }

    /**
     * 条件筛选
     *
     * @param $param
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($param)
    {
        $where = [];
        // 彩种筛选
        if (isset($param['ctype']) && !empty($param['ctype'])) {
            $where[] = ['ctype', '=', (int)$param['ctype']];
        }

        // 期号筛选
        if (isset($param['expect']) && !empty($param['expect'])) {
            $where[] = ['expect', 'like', '%' . $param['expect'] . '%'];
        }

        // 开奖时间筛选
        if (isset($param['openTime']) && !empty($param['openTime'])) {
            $where[] = ['open_time', 'between time', [$param['openTime'] . ' 00:00:00', $param['openTime'] . ' 23:59:59']];
        }

        return $where;
    }

    /**
     * 获取数字彩列表
     *
     * @param $param
     * @param string $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getDrawList($param, $order = 'open_time DESC')
    {
        $perPage = 10;
        if (isset($param['perPage']) && !empty($param['perPage'])) {
            $perPage = (int)$param['perPage'];
        }

        $where = $this->commonFilter($param);
        $data = self::where($where)->order($order)->paginate($perPage);
        foreach ($data as &$item) {
            $item['open_code'] = $item['open_code'] ? explode(',', $item['open_code']) : [];
            $item['ctype_name'] = self::getCtype($item['ctype']);
        }

        return $data;
    }

    /**
     * 获取彩种类型
     *
     * @param $ctype // 彩种类型
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getCtype($ctype)
    {
        switch ($ctype) {
            case 1:
                return '排列三';
            case 2:
                return '排列五';
            case 3:
                return '澳彩';
            case 4:
                return '葡彩';
            case 5:
                return '幸运飞艇';
            default:
                return '';
        }
    }

    /**
     * 编辑修改数字彩开奖号码
     *
     * @param $data // 表单数据
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editSubmit($data)
    {
        $id = $data['numId']; // 当前期开奖ID(pl_open主键ID)
        $openCode = $data['open_code']; // 当前期开奖号码
        $nextOpenDate = $data['next_date']; // 下一期开奖日期
        $nextOpenNum = $data['next_number']; // 下一期开奖号码
        $find = self::getFieldsByWhere(['id' => $id], ['status', 'ctype']);
        $status = $find['status'];
        $ctype = $find['ctype'];
        try {
            Db::startTrans();
            // 澳彩,葡彩属于虚拟彩种, 彩期未结算不可手动编辑号码
            if ($status === 0) {
                if (in_array($ctype, [3, 4])) {
                    trigger_error('澳彩和普彩彩期未结算,不可编辑.');
                } else {
                    if (!$nextOpenDate || !$nextOpenNum) {
                        trigger_error('请填写下一期开奖日期和期号');
                    } else {
                        // 更新当前期开奖号码和状态
                        $affectedRows = self::where('id', $id)
                            ->where('status', 0)// 加乐观锁
                            ->setField([
                                'status' => 1, // 已开奖(编辑后, 需要手动开奖)
                                'open_code' => $openCode, // 开奖号码
                            ]);
                        if (!$affectedRows) {
                            trigger_error('手动编辑失败,该期已自动开奖');
                        }

                        // 写入下期期号和开奖日期
                        $nextDataId = self::quickCreate([
                            'expect' => $nextOpenNum,
                            'open_time' => $nextOpenDate,
                            'ctype' => $ctype,
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'status' => 0,
                        ]);
                        if (!$nextDataId) {
                            trigger_error('写入下期数据失败');
                        }
                    }
                }
            } else {
                self::quickCreate([
                    'id' => $id, // 主键ID
                    'open_code' => $openCode, // 开奖号码
                ], true);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 获取出售中的数字彩期号
     *
     * @param $ctype // 数字彩类型
     * @return false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNumber($ctype)
    {
        // 获取出售中的期号
        $newExpect = self::where('ctype', $ctype)
            ->where('status', 0)
            ->value('expect');
        return $newExpect;
    }

    /**
     * 检查数字彩, 彩期出售状态
     *
     * @param $number // 期号
     * @param $ctype // 数字彩种
     * @return bool // true: 出售中  false: 已停售
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function checkSaleStatus($number, $ctype)
    {
        // 期号为空
        if (empty($number)) {
            return false;
        }

        $data = self::getFieldsByWhere(['ctype' => $ctype, 'expect' => $number], [
            'status', // 开奖状态
            'open_time', // 开奖时间
        ]);
        $status = $data['status']; // 开奖状态
        $openTime = $data['open_time']; // 开奖时间
        if ($status === 1) {
            // 已开奖停售
            return false;
        } else {
            // 检查是否到已停售时间
            switch ($ctype) {
                case 1: // 排列三
                case 2: // 排列五
                    $time = date('H:i');
                    $curDate = date('Ymd');
                    $openDate = date('Ymd', strtotime($openTime));
                    if ($time >= Config::P3P5_SHUTDOWN_TIME && $openDate == $curDate) {
                        return false;
                    }

                    break;
                case 3: // 澳彩
                case 4: // 葡彩
                    $openTimeStamp = strtotime($openTime);
                    $surplusShutdownTime = $openTimeStamp - time() - Config::AO_PU_SHUTDOWN_TIME;
                    // 已封盘
                    if ($surplusShutdownTime <= 0) {
                        return false;
                    }

                    break;
                case 5: // 幸运飞艇
                    $openTimeStamp = strtotime($openTime);
                    $surplusShutdownTime = $openTimeStamp - time() - Config::XYFT_SHUTDOWN_TIME;
                    // 已封盘
                    if ($surplusShutdownTime <= 0) {
                        return false;
                    }

                    break;
            }
        }

        return true;
    }

    /**
     * 数字彩手动开奖(排三, 排五, 澳彩, 葡彩, 幸运飞艇)
     *
     * @param $param // ctype : 彩种类型  1: 排三 2: 排五   expect: 期号
     * @return bool|string
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function handOpenDraw($param)
    {
        $ctype = (integer)$param['ctype']; // 彩种类型
        $expect = (string)$param['expect']; // 期号
        $orderData = OrderNum::alias('on')
            ->leftJoin('order o', 'on.order_id=o.id')
            ->where('on.ctype', $ctype)// 数字彩种类型
            ->where('on.number', $expect)// 期号
            ->where('o.is_clear', 0)// 未结算
            ->field([
                'on.id', // order_num主键ID
                'on.order_id', // 订单表ID
                'on.multiple', // 该期倍数
                'on.status' => 'numStatus', // 当前期中奖状态
                'on.is_push', // 当前期是否是推单
                'on.bonus', // 单期奖金
                'on.bounty', // 单期嘉奖
                'o.amount', // 投注总金额
                'o.member_id', // 会员ID
                'o.status', // 订单状态
                'o.order_no', // 订单号
                'o.bet_content', // 投注项
                'o.is_yh', // 中奖后停止追号(数字彩) 0:停止 1:继续
                'o.follow_order_id', // 推单ID
                'o.pay_type', // 购买方式1：自购 2：跟单 3: 推单
                'o.pay_status', // 支付状态
            ])
            ->select()
            ->toArray();
        // 低频彩(排三,排五)判断是否存在未支付/支付中的订单
        if (!empty($orderIds) && in_array($ctype, [1, 2])) {
            $payStatusArr = array_column($orderData, 'pay_status');
            $payIntersect = array_intersect([-1, 0], $payStatusArr);
            if (!empty($payIntersect)) {
                return '该期的订单中存在支付中的订单, 请等待后操作!';
            }
        }

        // 获取开奖结果
        $openResult = self::getOpenCodeByExpect($expect, $ctype);
        if (empty($openResult)) {
            return '请填写开奖结果后, 再尝试操作!';
        }

        // 开奖逻辑
        // $item id(order_num ID) order_id  multiple(倍数) bet_content(投注项) is_yh(中奖后停止追号(数字彩) 0:停止 1:继续)
        $isComplete = true;
        foreach ($orderData as $item) {
            try {
                // 降低事务层级
                Db::startTrans();
                // 中奖后停止追号(数字彩) 0:停止 1:继续
                $isFol = $item['is_yh'];
                // 非幸运飞艇, 则判断是否已停止追号
                if ($ctype != 5) {
                    // 检查该订单是否停止追号
                    $checkNum = OrderNum::where('order_id', $item['order_id'])->field(['id', 'status'])->select()->toArray();
                    $checkNumCols = array_column($checkNum, 'status', 'id');
                    // 当前开奖的彩期开奖状态
                    $currentNumberStatus = $checkNumCols[$item['id']];
                    unset($checkNumCols[$item['id']]);
                    $numStatusArr = array_values($checkNumCols);
                    $statusIntersect = array_intersect([3], $numStatusArr);
                    // 存在已中奖的彩期并且停止追号
                    if (!empty($statusIntersect) && $isFol === 0) {
                        // 查看是否是二次开奖, 如果该期状态是待开奖, 则不是二次开奖
                        // 如果是首次开奖的场景, 存在已中奖彩期并且停止追号, 则跳过该期开奖
                        // 如果是二次开奖的场景, 如:该彩期开奖结果不对, 需二次开奖重开, 则不需要验证是否追期和是否存在已中奖的彩期
                        if ($currentNumberStatus === 1) {
                            // 提交事务
                            Db::commit();
                            // 跳过
                            continue;
                        }
                    }
                }

                $betContentArr = Helper::jsonDecode($item['bet_content']);
                // 投注项为空的订单, 则跳过
                if (empty($betContentArr)) {
                    // 设置未完成状态
                    $isComplete = false;
                    // 记录系统日志
                    Helper::log('系统', '数字彩手动开奖', "注单{$item['order_no']}开奖失败!", "订单{$item['order_no']}投注内容为空", 0);
                    // 提交事务
                    Db::commit();
                    // 跳过当前订单
                    continue;
                }
                // 获取该订单的中奖金额
                $drawAmount = $this->getDrawAmountByBetContent($betContentArr, $openResult, $ctype, $item['order_no']);
                if (empty($drawAmount)) {
                    // 未中奖
                    OrderNum::where('id', $item['id'])->setField([
                        'status' => 2, // 未中奖
                        'bonus' => 0, // 重置单期奖金
                        'bounty' => 0, // 重置单期嘉奖彩金
                        'update_time' => Helper::timeFormat(time(), 's'), // 修改时间
                    ]);
                    // 判断是否是幸运飞艇
                    if ($ctype == 5) {
                        // 幸运飞艇没有追期, 直接设置为未中奖
                        // 兼容二开普通下单, 重置总嘉奖和总奖金, 这里不需要考虑跟单和推单
                        Order::where('id', $item['order_id'])->setField([
                            'status' => 3, // 未中奖
                            'bonus' => 0, // 重置总奖金
                            'bounty' => 0, // 重置总嘉奖
                            'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                        ]);
                        // 增加总输赢
                        $betTotalAmount = $item['amount'];
                        $memberId = $item['member_id'];
                        $orderStatus = $item['status'];
                        if ($orderStatus !== 3) {
                            Member::addLoseAndWinning($memberId, $betTotalAmount);
                        }
                    } else {
                        // 检查是否还有未开奖的期号
                        $otherData = OrderNum::where('order_id', $item['order_id'])->field(['id', 'status'])->select()->toArray();
                        $cols = array_column($otherData, 'status', 'id');
                        unset($cols[$item['id']]);
                        $stateArr = array_values($cols);
                        $whereArr = [1, 3]; // 待开奖和已中奖订单
                        $intersect = array_intersect($whereArr, $stateArr);
                        if (empty($intersect)) {
                            // 不存在待开奖和已中奖期号 则设置为未中奖
                            // 重置总奖金, 总嘉奖, 推单收益, 和跟单付出
                            // 兼容二次开奖的普通下单, 跟单和推单
                            Order::where('id', $item['order_id'])->setField([
                                'status' => 3, // 未中奖
                                'bonus' => 0, // 重置总奖金
                                'bounty' => 0, // 重置总嘉奖
                                'follow_order_commission' => 0, // 重置推单收益
                                'pay_out_commission' => 0, // 重置跟单付出
                                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                            ]);
                            // 增加总输赢
                            $betTotalAmount = $item['amount'];
                            $memberId = $item['member_id'];
                            $orderStatus = $item['status'];
                            if ($orderStatus !== 3) {
                                Member::addLoseAndWinning($memberId, $betTotalAmount);
                            }
                        } else {
                            $flipArr = array_flip($intersect);
                            // 判断是否存在已中奖的彩期
                            if (isset($flipArr[3])) {
                                // 存在中奖期号并且追期
                                if ($isFol === 1) {
                                    // 判断是否存在待开奖期号
                                    if (isset($flipArr[1])) {
                                        $setState = 2; // 待开奖
                                    } else {
                                        $setState = 4; // 该期为追期最后一期, 更改订单为已中奖
                                    }
                                } else {
                                    // 该情况属于二次开奖场景
                                    // 存在中奖期号不追期
                                    // 设置订单状态为已中奖
                                    $setState = 4;
                                }
                            } else {
                                // 不存在已中奖的彩期, 那就是存在待开奖的彩期
                                // 当前期未中奖, 且不存在已中奖的彩期, 存在待开奖的彩期
                                // 设置订单状态为待开奖
                                $setState = 2; // 待开奖
                            }
                            // 兼容二次开奖某些彩期从已中奖转为未中奖后, 订单总奖金和总嘉奖不同步
                            // 该情况会出现在追期订单和推单订单中, 且该订单中存在二个以上中奖的彩期
                            // 重新计算总奖金和嘉奖
                            // 该情况不存在跟单场景
                            if ($item['numStatus'] === 3) {
                                $orderOriginData = Order::getFieldsByWhere(['id' => $item['order_id']], ['bonus', 'bounty']);
                                $surplusBonus = $orderOriginData['bonus'] - $item['bonus'];
                                $surplusBounty = $orderOriginData['bounty'] - $item['bounty'];
                                if ($surplusBonus <= 0) {
                                    $surplusBonus = 0;
                                }

                                if ($surplusBounty <= 0) {
                                    $surplusBounty = 0;
                                }
                                if ($item['is_push'] === 1) {
                                    // 当前彩期为推单
                                    // 重置推单收益
                                    // 删减当前期的总奖金和总嘉奖
                                    $updateFields = [
                                        'status' => $setState,
                                        'bonus' => $surplusBonus, // 重置总奖金
                                        'bounty' => $surplusBounty, // 重置总嘉奖
                                        'follow_order_commission' => 0, // 重置推单收益
                                        'open_time' => Helper::timeFormat(time(), 's'), // 重置开奖时间
                                    ];
                                } else {
                                    // 当前期为普通订单
                                    // 删减当前期的总奖金和总嘉奖
                                    $updateFields = [
                                        'status' => $setState,
                                        'bonus' => $surplusBonus, // 重置总奖金
                                        'bounty' => $surplusBounty, // 重置总嘉奖
                                        'open_time' => Helper::timeFormat(time(), 's'), // 重置开奖时间
                                    ];
                                }

                                Order::where('id', $item['order_id'])->setField($updateFields);
                            } else {
                                // 直接更新订单状态
                                Order::where('id', $item['order_id'])->setField([
                                    'status' => $setState,
                                    'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                                ]);
                            }
                            // 减少总输赢
                            $betTotalAmount = $item['amount'];
                            $memberId = $item['member_id'];
                            $orderStatus = $item['status'];
                            if ($orderStatus === 3 && $setState === 4) {
                                // 未中奖转已中奖
                                Member::decLoseWinning($memberId, $betTotalAmount);
                            }
                        }
                    }

                } else {
                    // 已中奖
                    $this->drawHandler(
                        $item['order_id'],
                        $item['pay_type'],
                        $item['follow_order_id'],
                        $drawAmount * (int)$item['multiple'],
                        $item['id'],
                        $ctype,
                        $item['is_yh']
                    );
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                // 记录系统日志
                Helper::log('系统', '数字彩手动开奖', "注单{$item['order_no']}手动开奖失败!", $e->getMessage(), 0);
                // 标记开奖未完成
                $isComplete = false;
            }
        }

        // 判断是否完成开奖
        if ($isComplete) {
            return true;
        }

        return '数字彩手动开奖错误,错误详情请查看系统日志!';
    }

    /**
     * 数字彩中奖处理逻辑(排三,排五,澳彩,普彩,幸运飞艇)
     *
     * @param $orderId // 订单ID
     * @param $payType // 购买方式
     * @param $followOrderId // 推单ID
     * @param $drawAmount // 中奖金额
     * @param $numId // order_num主键ID
     * @param $ctype // 数字彩种类型
     * @param $isFol // 是否追期
     * @param $drawType // 标识 1: 手动开奖  2: 自动开奖
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function drawHandler($orderId, $payType, $followOrderId, $drawAmount, $numId, $ctype, $isFol, $drawType = 1)
    {
        // 获取嘉奖奖金比例
        $bountyAmountCommission = AdminConfig::config('prize_size');
        $bountyAmountCommission /= 100;
        // 跟单
        if ($payType === 2) {
            // 获取推单的佣金比例
            $commissionRate = Order::getValByWhere(['id' => $followOrderId], 'commission_rate');
            $commissionRate = (int)$commissionRate / 100;
            // 获取推单订单应获取的佣金(付出的佣金) = 奖金 * 推单佣金比例
            $followCommissionAmount = round($drawAmount * $commissionRate, 2);
            // 实际推单佣金 = 付出的佣金 - 平台抽取佣金
            $realPushOrderCommissionAmount = $followCommissionAmount - ($followCommissionAmount * self::PLAIN_COMMISSION_RATE);
            $realPushOrderCommissionAmount = round($realPushOrderCommissionAmount, 2);
            // 获取当前跟单订单的实际奖金(到手奖金) = 奖金 - 付出的佣金
            $realBonus = $drawAmount - $followCommissionAmount;

            if ($ctype == 5) {
                // 幸运飞艇嘉奖奖金为0
                $bountyAmount = 0;
            } else {
                // 获取当前跟单订单获取的嘉奖彩金(嘉奖奖金) = 奖金 * 嘉奖比例
                $bountyAmount = round($drawAmount * $bountyAmountCommission, 2);
            }
            if ($drawType === 1) {
                // 初始化推单订单的推单佣金
                // 记录每单推单初始化标识
                static $position = [];
                if (empty($position) || !isset($position[$followOrderId])) {
                    $position[$followOrderId] = 1;
                    Order::where(['id' => $followOrderId])->setField('follow_order_commission', $realPushOrderCommissionAmount);
                } else {
                    Order::where(['id' => $followOrderId])->setInc('follow_order_commission', $realPushOrderCommissionAmount);
                }
            } else {
                // 推单订单增加推单佣金
                Order::where(['id' => $followOrderId])->setInc('follow_order_commission', $realPushOrderCommissionAmount);
            }

            // 写入该期奖金和嘉奖
            OrderNum::where('id', $numId)->setField([
                'bonus' => $realBonus, // 最后获取的奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'status' => 3,  // 已中奖
                'update_time' => Helper::timeFormat(time(), 's'), // 修改时间
            ]);
            // 总奖金
            $totalData = OrderNum::where('order_id', $orderId)
                ->field([
                    'SUM(bonus) as totalBonus', // 总奖金
                    'SUM(bounty) as totalBounty', // 总嘉奖
                ])
                ->find();
            // 手动开奖处理总输赢
            if ($drawType === 1) {
                // 获取该订单的原始数据
                $orderOriginData = Order::getFieldsByWhere(['id' => $orderId], ['member_id', 'amount', 'status']);
                $betTotalAmount = $orderOriginData['amount'];
                $memberId = $orderOriginData['member_id'];
                $orderStatus = $orderOriginData['status'];
                // 减少总输赢
                if ($orderStatus === 3) {
                    // 未中奖转已中奖
                    Member::decLoseWinning($memberId, $betTotalAmount);
                }
            }
            // 写入订单表总奖金和总嘉奖
            // 跟单没有追期, 直接设置该订单为已中奖
            Order::where('id', $orderId)
                ->setField([
                    'bonus' => $totalData['totalBonus'], // 总奖金
                    'bounty' => $totalData['totalBounty'], // 总嘉奖
                    'pay_out_commission' => $followCommissionAmount, // 跟单付出的佣金
                    'status' => 4, // 已中奖
                    'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                ]);
            // 自动派奖(直接传入订单ID,二次查询降低耦合)[自动开奖]
            if ($drawType === 2) {
                Order::autoSendDraw($orderId);
            }

        } else {
            // 普通订单(自购和推单)
            if ($ctype == 5) {
                // 幸运飞艇嘉奖奖金为0
                $bountyAmount = 0;
            } else {
                $bountyAmount = round($drawAmount * $bountyAmountCommission, 2);
            }

            // 写入该期奖金和嘉奖
            OrderNum::where('id', $numId)->setField([
                'bonus' => $drawAmount, // 奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'status' => 3,  // 已中奖
                'update_time' => Helper::timeFormat(time(), 's'), // 修改时间
            ]);
            // 获取总奖金
            $totalData = OrderNum::where('order_id', $orderId)
                ->field([
                    'SUM(bonus) as totalBonus', // 总奖金
                    'SUM(bounty) as totalBounty', // 总嘉奖
                ])
                ->find();
            // 判断是否追号, 0 停止追号  1: 中奖后继续追号
            $exitAccount = 0;
            if ($isFol === 0) {
                // 停止追期
                $endStatus = 4; // 已中奖
                // 获取退换彩金
                $exitAccount = OrderNum::where('order_id', $orderId)
                    ->where('status', 1)// 待开奖
                    ->sum('amount');
                // 退还彩金
                $exitAccount = floatval($exitAccount);
            } else {
                $checkNum = OrderNum::where('order_id', $orderId)->field(['id', 'status'])->select()->toArray();
                $checkNumCols = array_column($checkNum, 'status', 'id');
                unset($checkNumCols[$numId]);
                $numStatusArr = array_values($checkNumCols);
                $statusIntersect = array_intersect([1], $numStatusArr);
                if (!empty($statusIntersect)) {
                    // 存在待开奖的期号, 继续追号
                    $endStatus = 2; // 待开奖
                } else {
                    // 追期最后一期中奖, 为已中奖
                    $endStatus = 4; // 已中奖
                }
            }

            // 手动开奖处理总输赢
            if ($drawType === 1 && $endStatus === 4) {
                // 获取该订单的原始数据
                $orderOriginData = Order::getFieldsByWhere(['id' => $orderId], ['member_id', 'amount', 'status']);
                $betTotalAmount = $orderOriginData['amount'];
                $memberId = $orderOriginData['member_id'];
                $orderStatus = $orderOriginData['status'];
                // 减少总输赢
                if ($orderStatus === 3) {
                    // 未中奖转已中奖
                    Member::decLoseWinning($memberId, $betTotalAmount);
                }
            }
            // 写入订单表总奖金, 总嘉奖和退还彩金
            Order::where('id', $orderId)
                ->setField([
                    'bonus' => $totalData['totalBonus'], // 总奖金
                    'bounty' => $totalData['totalBounty'], // 总嘉奖
                    'status' => $endStatus, // 订单状态
                    'exit_account' => $exitAccount, // 退还彩金
                    'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                ]);
            // 自动派奖[自动开奖]
            if ($endStatus === 4 && $drawType === 2) {
                Order::autoSendDraw($orderId);
            }
        }
    }

    /**
     * 数字彩自动开奖(排三,排五,澳彩,普彩,幸运飞艇)
     *
     * @param $ctype // 数字彩类型
     * @param $expect // 期号
     * @param $openResult // 开奖结果
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function autoDraw($ctype, $expect, $openResult)
    {
        $orderData = OrderNum::alias('on')
            ->leftJoin('order o', 'on.order_id=o.id')
            ->where('on.ctype', $ctype)// 数字彩种类型
            ->where('on.number', $expect)// 期号
            ->where('on.status', 1)// 待开奖的订单
            ->where('o.is_clear', 0)// 未结算
            ->field([
                'on.id', // order_num主键ID
                'on.order_id', // 订单表ID
                'on.multiple', // 该期倍数
                'o.member_id', // 会员ID
                'o.amount', // 投注总金额
                'o.bet_content', // 投注项
                'o.order_no', // 订单号
                'o.is_yh', // 中奖后停止追号(数字彩) 0:停止 1:继续
                'o.follow_order_id', // 推单ID
                'o.pay_type', // 购买方式1：自购 2：跟单 3: 推单
            ])
            ->select()
            ->toArray();

        // 开奖逻辑
        // $item id(order_num ID) order_id  multiple(倍数) bet_content(投注项) is_yh(中奖后停止追号(数字彩) 0:停止 1:继续)
        foreach ($orderData as $item) {
            try {
                // 降低事务层级
                Db::startTrans();
                // 检查该订单是否停止追号(除幸运飞艇)
                if ($ctype != 5) {
                    $numData = OrderNum::where('order_id', $item['order_id'])->field(['id', 'status'])->select()->toArray();
                    $numDataCols = array_column($numData, 'status', 'id');
                    unset($numDataCols[$item['id']]);
                    $numDataVals = array_values($numDataCols);
                    $numIntersect = array_intersect([3], $numDataVals); // 是否存在已开奖彩期
                    // 中奖后停止追号(数字彩) 0:停止 1:继续
                    $isFol = $item['is_yh'];
                    // 该订单存在已开奖的彩期
                    if (!empty($numIntersect) && $isFol === 0) {
                        // 跳过当前订单
                        Db::rollback();
                        continue;
                    }
                }

                $betContentArr = Helper::jsonDecode($item['bet_content']);
                // 该订单投注项为空
                if (empty($betContentArr)) {
                    // 抛出异常
                    trigger_error("订单{$item['order_no']}投注项为空");
                }
                // 获取该订单的中奖金额
                $drawAmount = $this->getDrawAmountByBetContent($betContentArr, $openResult, $ctype, $item['order_no']);
                if (empty($drawAmount)) {
                    // 未中奖
                    OrderNum::where('id', $item['id'])->setField([
                        'status' => 2, // 未中奖
                        'update_time' => Helper::timeFormat(time(), 's'), // 修改时间
                    ]);
                    if ($ctype == 5) {
                        // 幸运飞艇没有追期 直接设置订单为未中奖
                        Order::where('id', $item['order_id'])->setField([
                            'status' => 3, // 未中奖
                            'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                        ]);
                        // 添加总输赢
                        Member::addLoseAndWinning($item['member_id'], $item['amount']);
                    } else {
                        // 查看是否还有未完成的订单
                        $otherData = OrderNum::where('order_id', $item['order_id'])->field(['id', 'status'])->select()->toArray();
                        $cols = array_column($otherData, 'status', 'id');
                        unset($cols[$item['id']]); // 除去自身
                        $stateArr = array_values($cols);
                        $whereArr = [1, 3]; // 待开奖|已中奖
                        $intersect = array_intersect($whereArr, $stateArr);
                        if (empty($intersect)) {
                            // 不存在其他期, 则完成订单
                            Order::where('id', $item['order_id'])->setField([
                                'status' => 3,
                                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                            ]);
                            // 添加总输赢(输)
                            Member::addLoseAndWinning($item['member_id'], $item['amount']);
                        } else {
                            $flipArr = array_flip($intersect);
                            if (isset($flipArr[3])) {
                                // 存在中奖期号并且追期
                                // 判断是否存在待开奖期号
                                if (isset($flipArr[1])) {
                                    $setState = 2; // 待开奖
                                } else {
                                    $setState = 4; // 该期为追期最后一期, 已中奖
                                }
                            } else {
                                $setState = 2; // 待开奖
                            }
                            // 更新订单状态
                            Order::where('id', $item['order_id'])->setField([
                                'status' => $setState,
                                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                            ]);
                            // 自动派奖
                            if ($setState === 4) {
                                Order::autoSendDraw($item['order_id']);
                            }
                        }
                    }

                } else {
                    // 已中奖
                    $this->drawHandler(
                        $item['order_id'],
                        $item['pay_type'],
                        $item['follow_order_id'],
                        $drawAmount * (int)$item['multiple'],
                        $item['id'],
                        $ctype,
                        $item['is_yh'],
                        2
                    );
                }

                // 提交业务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚业务
                Db::rollback();
                // 捕获异常,并记录日志
                Helper::log('系统', '数字彩自动开奖', '订单号:' . $item['order_no'], $e->getMessage(), 0);
            }
        }
    }

    /**
     * 获取开奖的历史数据
     *
     * @param $where
     * @param $limit
     * @throws \Exception
     * @return string|null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getLooteryResults($where, $limit)
    {
        $openCode = self::where($where)->order('create_time desc')->limit($limit)->select();
        return $openCode ?: null;
    }

    /**
     * 数字彩赛果数据导出
     *
     * @param $where
     * @param string $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportData($where, $order = 'id DESC')
    {
        $model = self::where($where)
            ->order($order)
            ->field([
                'ctype',// 彩种类型 1: 排三  2: 排五  3: 澳彩  4: 葡彩 5: 幸运飞艇
                'expect',// 期号
                'open_code',// 开奖结果
                'open_time',// 开奖时间
                'update_time',// 修改时间
                'status'// 开奖状态, 0: 待开奖  1: 已开奖
            ])
            ->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            switch ($v['ctype']) {
                case 1:// 排三
                    $v['ctype'] = '排三';
                    $v['code'] = Config::P3_CODE;
                    break;
                case 2:// 排五
                    $v['ctype'] = '排五';
                    $v['code'] = Config::P5_CODE;
                    break;
                case 3:// 澳彩
                    $v['ctype'] = '澳彩';
                    $v['code'] = Config::AO_CODE;
                    break;
                case 4:// 葡彩
                    $v['ctype'] = '葡彩';
                    $v['code'] = Config::PC_CODE;
                    break;
            }

            $v['status'] = ($v['status'] === 0 ? '未开奖' : '已开奖');
        }
        // 导出
        Helper::exportExcel(
            'MatchResultExcel',
            [
                '彩种', '期号', '开奖号码', '开奖时间', '修改时间', '开奖状态', '彩种代码',
            ],
            $data
        );
    }

    // 根据条件查询
    public function getBeforeResults($where, $order = null, $limit = null, $field = true)
    {
        $openCode = self::where($where)
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select()
            ->toArray();

        return $openCode ?: null;
    }

    // 带条件分页查询
    // $rows  当前页码
    // $offset  每页显示条数
    public function getPageData($where, $order = null, $rows, $offset, $field = true)
    {
        $openCode = self::where($where)
            ->field($field)
            ->order($order)
            ->page($rows, $offset)
            ->select()
            ->toArray();

        return $openCode ?: null;
    }
}