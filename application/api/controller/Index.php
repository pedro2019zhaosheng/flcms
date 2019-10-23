<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:28
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world.
 */

namespace app\api\controller;

use app\common\model\CmsNews;
use app\common\model\Lottery;
use app\common\RestController;
use app\common\Helper;
use app\common\model\CmsAd;
use app\common\model\JczqBase;
use app\common\model\Order;
use app\common\Config;
use app\common\model\PlOpen;
use app\common\model\JczqOpen;
use app\common\model\JclqBase;
use app\common\model\JclqOpen;
use app\common\model\JcdcBase;
use app\common\model\JcdcOpen;

/**
 * 首页数据
 *
 * Class Index
 *
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Index extends RestController
{
    /**
     * authentication过滤掉 首页 热门赛事 大神跟单
     *
     * @param array $disableAuthAction
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected function init(array $disableAuthAction = [])
    {
        $disableAuthAction = ['index', 'getBanner', 'hostFootData', 'goodPush', 'p3HistoryLottery', 'p5HistoryLottery', 'getNumberIsuue', 'test'];
        parent::init($disableAuthAction); // TODO: Change the autogenerated stub
    }

    /**
     * @desc 首页数据
     * @author LiBin
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @date 2019-04-17
     */
    public function index()
    {
        //获取广告轮播图
        $cmsAd = new CmsAd();
        $cmsAdList = $cmsAd->getList(['status' => 1, 'ad_type' =>2, 'perPage' => 3], 0, 'update_time desc');
        $cmsAdList = $cmsAdList->toArray();
        $adver = [];
        foreach ($cmsAdList['data'] as $k => $v) {
            $adver[$k]['img'] = $v['img'];
            $adver[$k]['url'] = $v['url'];
        }
        //获取新闻
        $cmsNew = new CmsNews();
        $cmsNewList = $cmsNew->getList(['news_type' => 3, 'status' => 1, 'perPage' => 3], 0, 'update_time desc');
        $cmsNewList = $cmsNewList->toArray();
        $news = [];
        foreach ($cmsNewList['data'] as $k => $v) {
            $news[$k] = $v['title'];
        }
        //彩种列表
        $lottery = new Lottery();
        $lotteryData = $lottery->getLotPage(['status' => 0]);
        $lotteryData = $lotteryData->toArray();
        $lotteryList = [];
        $lotteryList[0]['type'] = 1;
        $lotteryList[0]['status'] = 0;
        $lotteryList[1]['type'] = 2;
        $lotteryList[1]['status'] = 0;
        $lotteryList[2]['type'] = 3;
        $lotteryList[2]['status'] = 0;
        foreach ($lotteryData['data'] as $k => $v) {
            if ($v['code'] == Config::ZC_CODE) {//判断足彩状态
                $lotteryList[0]['status'] = 1;
            }

            if ($v['code'] == Config::LC_CODE) {//判断篮彩状态
                $lotteryList[1]['status'] = 1;
            }

            if ($v['code'] == Config::BJ_CODE) {//判断北京单场状态
                $lotteryList[2]['status'] = 1;
            }
        }

        $data['adver'] = $adver;//广告列表
        $data['news'] = $news;//新闻列表
        $data['lottery'] = $lotteryList;//彩种列表
        $data['news'] = $news;//新闻列表

        return $this->asNewJson('indexRet', 1, 'success', '获取成功', $data);
    }

    /**
     * 获取马甲包banner图
     */
    public function getBanner()
    {
        //获取广告轮播图
        $cmsAd = new CmsAd();
        $cmsAdList = $cmsAd->getList(['status' => 1,'is_del' => 0,'ad_type' =>2], 0, 'update_time desc');
        $cmsAdList = $cmsAdList->toArray();
        return $this->asNewJson('getBanner', 1, 'success', '获取成功', $cmsAdList);

    }

    /**
     * @desc 首页热门赛事(只展示胜平负为单关的)
     * @author LiBin
     * @throws \Exception
     * @date 2019-04-17
     */
    public function hostFootData()
    {
        //获取热门赛事
        $model = new JczqBase();
        $findData = [
            'a.match_num',//比赛编号
            'a.league_name',//联赛名称
            'a.host_name',//主队名称
            'a.guest_name',//客队名称
            'a.host_icon',//主队图标
            'a.guest_icon',//客队图标
            'a.jc_date',//竞彩日期
            'a.sys_cutoff_time',//系统截止时间
            'b.sp_spf',//胜平负奖金指数
        ];
        $where [] = ['a.sys_cutoff_time', '>', date('Y-m-d H:i:s')];//系统截止时间
        $where [] = ['b.sp_spf', 'like', '%"single":"1"%'];//单关标识
        $where [] = ['a.sale_status', '=', 1];//出售中
        $footData = $model->getFootball($where, $findData, 'sys_cutoff_time desc');
        //获取彩种ID
        $lotteryId = Lottery::getIdByCode(Config::ZC_CODE);
        $reFootData = [];
        if (!empty($footData)) {
            $reFootData[] = $footData['match_num'];  // 赛事编号
            $reFootData[] = $footData['league_name']; // 联赛名称
            $reFootData[] = $footData['host_name']; // 主队名称
            $reFootData[] = $footData['guest_name']; // 客队名称
            $reFootData[] = $footData['host_icon']; // 主队图标
            $reFootData[] = $footData['guest_icon']; // 客队图标
            $reFootData[] = $footData['sys_cutoff_time']; // 截止时间
            $reFootData[] = date('Y-m-d', strtotime($footData['jc_date']));  // 年月日
            $reFootData[] = date('H:i:s', strtotime($footData['jc_date'])); // 时分秒
            $sp_spf = Helper::jsonDecode($footData['sp_spf']);
            $reFootData[] = $sp_spf['W']; // 胜的指数
            $reFootData[] = $sp_spf['D']; // 平的指数
            $reFootData[] = $sp_spf['L']; // 负的指数
            $reFootData[] = (string)$lotteryId; //彩种ID
        }

        return $this->asNewJson('hostFootRet', 1, 'success', '获取成功', $reFootData);
    }

    /**
     * @desc 大神推单
     * @author LiBin
     * @throws \Exception
     * @date 2019-04-17
     */
    public function goodPush()
    {
        // 大神推单
        $order = new Order();
        $where[] = ['sup_order_state', '=', 1]; // 推单审核状态
        $where[] = ['start_time', '>', date('Y-m-d H:i:s')]; // 跟单截止时间
        $where[] = ['pay_type', '=', 3]; // 订单类型
        // 获取七日命中率最高的用户
        $findField = [
            'id', // 订单ID
            'order_no', // 订单号
            'member_id', // 会员ID
            'start_time', // 跟单截止时间
            'chuan', // 串关信息
            'order_title', // 订单标题
            'amount', // 投注金额
            'start_amount', // 跟单起始金额
            'lottery_id', // 彩种ID
            'order_type', // 订单类型
        ];
        $list = $order->getPushOrder($where, $findField);
        $data = [];
        if (!empty($list)) {
            // 排序去除命中率最高的推单数据
            $tempSort = array_column($list, 'scort');
            array_multisort($tempSort, SORT_DESC, $list);
            $data[] = $list[0]['probabillity']; // 七日命中率
            $data[] = (string)$list[0]['amount']; // 自购总金额
            $data[] = (string)$list[0]['number']; // 人数
            $data[] = (string)$list[0]['type']; // 1.足彩 2.篮彩 3.北京单场
            $data[] = $list[0]['photo']; // 头像
            $data[] = $list[0]['name']; // 昵称
            $data[] = $list[0]['title']; // 宣言
            $data[] = $list[0]['start_time']; // 跟单截止时间
            $data[] = $list[0]['chuan']; // 串
            $data[] = $list[0]['start_amount']; // 起跟金额
            $data[] = (string)$list[0]['order_id']; // 订单ID
            $data[] = $list[0]['issue']; // 期号
        }

        return $this->asNewJson('goodPushRet', 1, 'success', '获取成功', $data);
    }

    /**
     * @desc 获取所有彩票开奖结果
     * @throws \Exception
     * @return \think\response\Json
     */
    public function openLottery()
    {
        // 获取竞彩足球最新开奖结果
        $jczqOpen = new JczqOpen();
        $jczqBase = new JczqBase();
        // 查询开奖最新一期比赛编号
        $jczq_num = $jczqOpen->where(['status' => 1])->order('id desc')->value('match_num');
        // 获取赛事详情
        $zqinfo = $jczqBase->where('match_num',$jczq_num)->find()->toArray();
        // 获取赛果
        $jczq = $jczqOpen->getJcResult($jczq_num);
        $jczqdata = array_merge($zqinfo,$jczq);

        // 获取竞彩篮球最新开奖结果
        $jclqOpen = new JclqOpen();
        $jclqBase = new JclqBase();
        // 查询开奖最新一期比赛编号
        $jclq_num = $jclqOpen->where(['status' => 1])->order('id desc')->value('match_num');
        // 获取赛事详情
        $lqinfo = $jclqBase->where('match_num',$jclq_num)->find()->toArray();
        // 获取赛果
        $jclq = $jclqOpen->getJcResult($jclq_num);
        $jclqdata = array_merge($lqinfo,$jclq);

        // 获取北京单场最新开奖结果
        $jcdcOpen = new JcdcOpen();
        $jcdcBase = new JcdcBase();
        // 查询开奖最新一期比赛编号
        $jcdc_num = $jcdcOpen->where(['status' => 1])->order('id desc')->value('match_num');
        // 获取赛事详情
        $dcinfo = $jcdcBase->where('match_num',$jcdc_num)->find()->toArray();
        // 获取赛果
        $jcdc = $jcdcOpen->getJcResult($jcdc_num);
        $jcdcdata = array_merge($dcinfo,$jcdc);
        
        // 获取排列三最新开奖结果
        $plOpen = new PlOpen();
        $pl3 = $plOpen->where(['status' => 1,'ctype' => 1])->order('id desc')->find()->toArray();
        $code3 = explode(',', $pl3['open_code']);
        $pl3['results'] = $code3; //赛果
        $pl3['sum'] = array_sum($code3);// 数组和值
        if (count($code3) != count(array_unique($code3))) { //判断组三,组六
            $pl3['zu'] = '组三';
        } else {
            $pl3['zu'] = '组六';
        }
        
        // 获取排列五最新开奖结果
        $plOpen = new PlOpen();
        $pl5 = $plOpen->where(['status' => 1,'ctype' => 2])->order('id desc')->find()->toArray();
        $code5 = explode(',', $pl5['open_code']);
        $pl5['results'] = $code5; //赛果
        
        // 获取澳彩最新开奖结果
        $plOpen = new PlOpen();
        $ac = $plOpen->where(['status' => 1,'ctype' => 3])->order('id desc')->find()->toArray();
        $codeAc = explode(',', $ac['open_code']);
        $ac['results'] = $codeAc; //赛果
        
        // 获取葡彩最新开奖结果
        $plOpen = new PlOpen();
        $pc = $plOpen->where(['status' => 1,'ctype' => 4])->order('id desc')->find()->toArray();
        $codePc = explode(',', $pc['open_code']);
        $pc['results'] = $codePc; //赛果
        
        // 获取幸运飞艇开奖结果
        $plOpen = new PlOpen();
        $ft = $plOpen->where(['status' => 1,'ctype' => 5])->order('id desc')->find()->toArray();
        $codeFt = explode(',', $ft['open_code']);
        $ft['results'] = $codeFt; //赛果
        $ftgy = $plOpen->getResultDescribe($ft['open_code']);
        $codegy = explode(',', $ftgy);
        $ft['gy'] = $codegy; //冠亚和大小单双龙虎赛果

        $data['jczq'] = $jczqdata;
        $data['jclq'] = $jclqdata;
        $data['jcdc'] = $jcdcdata;
        $data['pl3'] = $pl3;
        $data['pl5'] = $pl5;
        $data['ac'] = $ac;
        $data['pc'] = $pc;
        $data['ft'] = $ft;
        return $this->asNewJson('openLottery', 1, 'success', '获取成功', ['data' => $data]);

    }

    /**
     * 获取竞彩足球的历史开奖记录
     */
    public function jczqHistoryLottery()
    {
        $data = $this->get;
        $number = 10;
        //判断页码
        if (empty($data['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($data['page'] - 1) * $number . ',' . $number;
        }

        $where['status'] = 1;
        $jczqOpen = new JczqOpen();
        $jczqBase = new JczqBase();
        $zqList = $jczqOpen->getLooteryResults($where, $limit);
        if (empty($zqList)) {
            return $this->asNewJson('jczqHistoryLottery', 1, 'success', '获取成功', ['data' => [], 'pageNumber' => 1]);
        }
        $countNUmber = $jczqOpen::where(['status' => 1])->count('id');
        $countnumber = ceil($countNUmber / $number);//向上取整获取页码
        
        foreach($zqList as $key => $val){
            $zqList[$key]['result'] = $jczqOpen->getJcResult($val['match_num']);
            $zqList[$key]['info'] = $jczqBase->where('match_num',$val['match_num'])->find();
        }
        return $this->asNewJson('jczqHistoryLottery', 1, 'success', '获取成功', ['data' => $zqList, 'pageNumber' => $countnumber]);
    }

    /**
     * 获取竞彩篮球的历史开奖记录
     */
    public function jclqHistoryLottery()
    {
        $data = $this->get;
        $number = 10;
        //判断页码
        if (empty($data['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($data['page'] - 1) * $number . ',' . $number;
        }

        $where['status'] = 1;
        $jclqOpen = new JclqOpen();
        $jclqBase = new JclqBase();
        $lqList = $jclqOpen->getLooteryResults($where, $limit);
        if (empty($lqList)) {
            return $this->asNewJson('jclqHistoryLottery', 1, 'success', '获取成功', ['data' => [], 'pageNumber' => 1]);
        }
        $countNUmber = $jclqOpen::where(['status' => 1])->count('id');
        $countnumber = ceil($countNUmber / $number);//向上取整获取页码

        foreach($lqList as $key => $val){
            $lqList[$key]['result'] = $jclqOpen->getJcResult($val['match_num']);
            $lqList[$key]['info'] = $jclqBase->where('match_num',$val['match_num'])->find();
        }
        return $this->asNewJson('jclqHistoryLottery', 1, 'success', '获取成功', ['data' => $lqList, 'pageNumber' => $countnumber]);
    }

    /**
     * 获取北京单场的历史开奖记录
     */
    public function jcdcHistoryLottery()
    {
        $data = $this->get;
        $number = 10;
        //判断页码
        if (empty($data['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($data['page'] - 1) * $number . ',' . $number;
        }

        $where['status'] = 1;
        $jcdcOpen = new JcdcOpen();
        $jcdcBase = new JcdcBase();
        $dcList = $jcdcOpen->getLooteryResults($where, $limit);
        if (empty($dcList)) {
            return $this->asNewJson('jcdcHistoryLottery', 1, 'success', '获取成功', ['data' => [], 'pageNumber' => 1]);
        }
        $countNUmber = $jcdcOpen::where(['status' => 1])->count('id');
        $countnumber = ceil($countNUmber / $number);//向上取整获取页码
        
        foreach($dcList as $key => $val){
            $dcList[$key]['result'] = $jcdcOpen->getJcResult($val['match_num']);
            $dcList[$key]['info'] = $jcdcBase->where('match_num',$val['match_num'])->find();
        }
        return $this->asNewJson('jcdcHistoryLottery', 1, 'success', '获取成功', ['data' => $dcList, 'pageNumber' => $countnumber]);
    }

    /**
     * @desc 获取排三/葡彩的历史开奖记录
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-05-17
     */
    public function p3HistoryLottery()
    {
        $data = $this->get;
        $number = 10;
        //判断页码
        if (empty($data['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($data['page'] - 1) * $number . ',' . $number;
        }

        if (!empty($data['type'])) {// 1.排三 3.葡彩
            $data['type'] != 3 ? $where['ctype'] = 1 : $where['ctype'] = 3;
        } else {// 默认1.排三
            $where['ctype'] = 1;
        }

        if (!empty($data['expect'])) {// 期号
            $where['expect'] = $data['expect'];
        }

        $where['status'] = 1;
        // 开奖时间降序
        $plOpen = new PlOpen();
        $LotteryData = $plOpen->getLooteryResults($where, $limit);//排三,葡彩
        if (empty($LotteryData)) {
            return $this->asNewJson('p3HistoryLotteryRet', 1, 'success', '获取成功', ['data' => [], 'pageNumber' => 1]);
        }

        $countNUmber = $plOpen::where(['status' => 1, 'ctype' => $where['ctype']])->count('id');
        $countnumber = ceil($countNUmber / $number);//向上取整获取页码
        $rdata = [];//数据容器
        foreach ($LotteryData as $k => $v) {
            $rdata[$k]['number'] = $v['expect'];// 期号
            $rdata[$k]['open_time'] = $v['open_time'];// 开奖时间
            $code = explode(',', $v['open_code']);
            $rdata[$k]['results'] = $code; //赛果
            $rdata[$k]['sum'] = array_sum($code);// 数组和值
            if (count($code) != count(array_unique($code))) { //判断组三,组六
                $rdata[$k]['zu'] = '组三';
            } else {
                $rdata[$k]['zu'] = '组六';
            }
        }

        return $this->asNewJson('p3HistoryLotteryRet', 1, 'success', '获取成功', ['data' => $rdata, 'pageNumber' => $countnumber]);
    }

    /**
     * @desc 获取排五/澳彩的历史开奖记录
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-05-17
     */
    public function p5HistoryLottery()
    {
        $data = $this->get;
        $number = 10;
        //判断页码
        if (empty($data['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($data['page'] - 1) * $number . ',' . $number;
        }

        if (!empty($data['type'])) {// 2.排五 4.澳彩
            $data['type'] != 4 ? $where['ctype'] = 2 : $where['ctype'] = 4;
        } else {// 默认排五
            $where['ctype'] = 2;
        }

        if (!empty($data['expect'])) {// 期号
            $where['expect'] = $data['expect'];
        }

        $where['status'] = 1;
        //开奖时间倒叙
        $plOpen = new PlOpen();
        $LotteryData = $plOpen->getLooteryResults($where, $limit);//排五/澳彩
        if (empty($LotteryData)) {
            return $this->asNewJson('p5HistoryLottery', 1, 'success', '获取成功', ['data' => [], 'pageNumber' => 1]);
        }

        $countNUmber = $plOpen::where(['status' => 1, 'ctype' => $where['ctype']])->count('id');
        $countnumber = ceil($countNUmber / $number);//向上取整获取页码
        $rdata = [];//数据容器
        foreach ($LotteryData as $k => $v) {
            $rdata[$k]['number'] = $v['expect'];// 期号
            $rdata[$k]['open_time'] = $v['open_time'];// 开奖时间
            $code = explode(',', $v['open_code']);
            $rdata[$k]['results'] = $code; //赛果
        }

        return $this->asNewJson('p5HistoryLottery', 1, 'success', '获取成功', ['data' => $rdata, 'pageNumber' => $countnumber]);
    }

    /**
     * @desc 获取葡彩澳彩的开奖时间和期号
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-05-20
     */
    public function getNumberIsuue()
    {
        $data = $this->get;
        if (!empty($data['type']) && $data['type'] == 2) {// 1.葡彩 2.澳彩
            $type = 3;
        } else {
            $type = 4;
        }
        // 开奖时间降序
        $plOpen = new PlOpen();
        $LotteryData = $plOpen::where('ctype', $type)->where('status', 0)->field('expect,open_time')->find();//葡彩/澳彩
        if (!empty($LotteryData['open_time'])) {
            $startTime = strtotime(date('Y-m-d H:i:s'));
            $endTime = strtotime($LotteryData['open_time']);
            $LotteryData['second'] = ($endTime - $startTime) + 1;
        } else {
            $LotteryData['second'] = 0;
        }

        return $this->asNewJson('', 1, 'success', '获取成功', ['data' => $LotteryData]);
    }

    /**
     * @desc 获取彩种列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLottery()
    {
        $lottery = new Lottery();
        $list = $lottery->getList(['status' => 0]);

        return $this->asNewJson('', 1, 'success', '获取成功', ['data' => $list]);
    }
}
