<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/6
 * Time: 14:55
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\controller;

use app\common\Helper;
use app\common\RestController;
use app\common\model\AdminSmslog;

/**
 * 竞彩-手机短信控制器
 *
 * Class Sms
 * @package app\api\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Sms extends RestController
{
    /**
     * @const 开发环境  dev ：测试环境； prod  ： 生产环境
     */
    const SMS_ENV = 'prod';

    /**
     * 关闭authentication验证
     *
     * @var bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $enableAuthentication = false;
    /**
     * 过期时间5分钟
     *
     * @var int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $expireAt = 300;

    /**
     * 该接口每天发送短信总数量为 5000条
     * 注：
     *  防止机器人
     *
     * @var int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $sendSize = 5000;

    /**
     * 该接口每天每个号码短信总数量为 5条
     * 注：
     *  防止机器人
     *
     * @var int
     * @author ken
     */
    protected $smsSendNum = 24;

    /**
     * 短信发送间隔
     *
     * @var int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $timeSort = 60;

    /**
     * 发送验证码
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api http://rap2.taobao.org/organization/repository/editor?id=120144&mod=186706&itf=714939
     */
    public function send()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'login.send');
        if ($validateRe !== true) {
            return $this->asNewJson('sendRet', 0, 'error', $validateRe);
        }
        $timeArr = Helper::mkTime('d');
        $smsModel = new AdminSmslog();
        $where[] = ['create_time', 'between', [date('Y-m-d H:i:s', $timeArr['start']), date('Y-m-d H:i:s', $timeArr['end'])]];
        $daySize = $smsModel->smsCount($where);

        if ((int)$daySize > $this->sendSize) {
            return $this->asNewJson('sendRet', 0, 'error', '该接口已停用');

        }

        $phone = ['phone' => $data['mobile']];
        $smsNum = $smsModel->smsSendCount($where, $phone);
        if ((int)$smsNum > $this->smsSendNum) {
            return $this->asNewJson('sendRet', 0, 'error', '您今天发送的次数过多!');
        }

        //检查发送间隔是否是60s
        $one = $smsModel->smsOne(['phone' => $data['mobile']], 'create_time DESC', 'create_time');
        if ($one) {
            $createTime = strtotime($one['create_time']);
            $timeSort = time() - $createTime;
            if ($timeSort < $this->timeSort) {
                return $this->asNewJson('sendRet', 0, 'error', '请' . ($this->timeSort - $timeSort) . '秒后重试');
            }
        }

        $content = Helper::randomCode();
        if (!strcasecmp('prod', self::SMS_ENV)) {
            // 生产环境
            $result = Helper::sendSms($data['mobile'], $content);
        } elseif (!strcasecmp('dev', self::SMS_ENV)) {
            //开发环境
            $result = true;
        } else {
            http_response_code(501);
            trigger_error('未知环境', E_USER_ERROR);
            exit(0);
        }

        if ($result === true && $this->insertCode($data['mobile'], $content, 0)) {
            if (!strcasecmp('prod', self::SMS_ENV)) {
                return $this->asNewJson('sendRet', 1, 'success', '发送成功');

            }
            return $this->asNewJson('sendRet', 1, 'success', '发送成功', [$content]);
        }
        return $this->asNewJson('sendRet', 0, 'error', '发送失败，错误码：' . $result);
    }

    /**
     * 发送成功，保存验证码
     *
     * @param $mobile //手机号
     * @param $code  //内容
     * @param $type 0普通通知，1订单发送
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected function insertCode($mobile, $code, $type)
    {
        $smsModel = new AdminSmslog();
        $updata['phone'] = $mobile;
        $updata['tpltype'] = $type;
        $updata['r_id'] = '';
        $updata['s_id'] = '';
        $updata['content'] = $code;
        $updata['create_time'] = date('Y-m-d H:i:s');
        if (!empty($order)) {
            $updata['orderid'] = $order;
        }

        $addSms = $smsModel->smsAdd($updata);
        if ($addSms) {
            return true;
        }

        return false;
    }

    /**
     * 验证手机验证码
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api http://rap2.taobao.org/repository/editor?id=120144&mod=186706&itf=724930
     */
    public function verify()
    {
        $post = input('post.');
        $validation = $this->validate($post, 'Sms.verify');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $smsModel = new AdminSmslog();
        //验证手机验证码
        $checkCode = $smsModel->checkSms($post['moblie'], $post['code']);
        return $this->asJson($checkCode[0], $checkCode[1], $checkCode[2]);
    }
}