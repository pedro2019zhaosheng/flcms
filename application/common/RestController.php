<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 19:35
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world.
 */

namespace app\common;

use app\common\model\Member;
use app\common\model\MemberToken;
use think\Exception;

/**
 * RESTFUL资源控制器基类
 * 须：
 * 1、注册Think5.1资源路由
 * 2、实现Restful路由风格
 *
 * 路由格式：
 * ```php
 * domain.com/user?_t={token}&_uid={uid}&_rt={refresh_token}
 * // `/user` // 资源标识符 (必传)
 * // `_t`    // 访问令牌   (必传) // 访问令牌有效期30天
 * // `_uid`  // *
 * ```
 *
 * Class BaseController
 *
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
abstract class RestController extends MainController
{
    // 默认是打开authentication验证
    protected $enableAuthentication = true;

    // 客户端响应类型
    public $responseType = 'json';

    // 用户ID
    public static $uid;

    // 用户令牌(解密后)
    public static $token;

    // 用户令牌(原始令牌)
    public static $originToken;

    // 用户生成数据签名key(用于数据校验)
    public static $key;

    // 加密Token的密匙
    public $tokenKey = 'ZUCAI2019_00000';

    // 数据签名类型
    public $signType = 'md5';

    /**
     * 初始化.
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    public function initialize()
    {
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Headers:*");
        header('Access-Control-Allow-Methods:*');
        header("Access-Control-Allow-Credentials:true");
        parent::initialize();
        // 启动
        $this->init();
    }

    /**
     * 启动.
     *
     * @param array $disableAuthAction // 过滤掉不进行验证的方法
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    protected function init(array $disableAuthAction = [])
    {
        if ($this->enableAuthentication === true) {
            $action = strtoupper(request()->action());
            if (!empty($disableAuthAction)) {
                foreach ($disableAuthAction as $k => $actionOption) {
                    $disableAuthAction[$k] = strtoupper($actionOption);
                }
                // 不在筛选内
                if (!in_array($action, $disableAuthAction, true)) {
                    $this->construction();
                    $this->authentication();
                    $this->check();
                }
            } else {
                $this->construction();
                $this->authentication();
                $this->check();
            }
        }
    }

    /**
     *  authentication验证
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected function authentication()
    {
        if (
            empty(self::$uid) // 会员ID为空
            || empty(self::$originToken) // 原始令牌为空
            || empty(self::$token) // 解密后令牌为空
            || !$this->validateToken(self::$token, self::$uid) // 令牌验证错误
        ) {
            $this->authenticationSend(-1, 'error', '亲,请您先登录!');
        }
    }

    /**
     * 构造.
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    protected function construction()
    {
        // 从http访问动作put、path、get、post、head、options、delete等方法中获取authentication标识.
        $request = request();
        self::$originToken = trim($request->param('_t'));
        self::$uid = (int) trim($request->param('_uid'));
        // 定义为常量, 方便全局访问
        defined('UID') or define('UID', self::$uid);
        self::$token = self::$originToken ? $this->decryptToken(self::$originToken) : null;

        if (!empty(self::$uid)) {
            $tokenAll = self::getTokenAll(self::$uid);
            if (!empty($tokenAll)) {
                self::$key = $tokenAll['key'];
            }
        }
    }

    /**
     * 检查.
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    protected function check()
    {
        // 检查会员状态
        $userStatus = Member::getMemberStatus(self::$uid);
        if (intval($userStatus) === 0) {
            $jsonResponse = $this->asJson(-1, 'error', '您的账户已被冻结');
            $jsonResponse->send();
            exit(0);
        }
    }

    /**
     * authentication验证，响应封装.
     *
     * @param string $code   // 状态码
     * @param string $status // 状态值
     * @param string $msg    // 提示语
     * @param string $data   // 数据
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    private function authenticationSend($code = '', $status = '', $msg = '', $data = '')
    {
        switch (strtolower($this->responseType)) {
            case 'json':
                $response = $this->asJson($code, $status, $msg, $data);
                $response->send();
                exit(0);
            case 'xml':
                echo $this->asXml($code, $status, $msg, $data);
                exit(0);
            case 'html':
                echo $data;
                exit(0);
            default:
                trigger_error('未知请求类型', E_USER_ERROR);
        }
    }

    /**
     * 数据验签.
     *
     * 注：该验证器不支持二维以上数组和嵌套二维以上数组。
     *
     * @param array $data // 数据
     * @param $key // 会员私匙
     *
     * @return array|false
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @see makeSign()
     */
    public function validateData(array $data, $key)
    {
        if (
            !isset($data['sign'])
            || empty($data['sign'])
        ) {
            return false;
        }
        $inputSign = $data['sign'];
        unset($data['sign']);
        $data['_key'] = $key;
        ksort($data); // 通过数组下标, 升序排序
        $serializeData = urldecode(http_build_query($data));
        $sign = call_user_func($this->signType, $serializeData);
        if (!strcmp($inputSign, $sign)) {
            return $data;
        }

        return false;
    }

    /**
     * 生成签名.
     *
     * @param array $data // 数据
     * @param $key // 会员私匙
     *
     * @return string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @see validateData()
     */
    public function makeSign(array $data, $key)
    {
        $data['_key'] = $key;
        ksort($data);
        $serializeData = urldecode(http_build_query($data));
        $sign = call_user_func($this->signType, $serializeData);

        return $sign;
    }

    /**
     * 获取会员私匙.
     *
     * @return string|null
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @see makeSign()
     * @see validateData()
     */
    public function key()
    {
        if (empty(self::$uid)) {
            trigger_error('会员未登录, 私匙未知');
        }

        $tokenGather = MemberToken::quickGetOne(null, ['member_id' => self::$uid]);
        if (!empty($tokenGather)) {
            return $tokenGather['key'];
        }

        return null;
    }

    /**
     * Token验证
     *
     * @param $token // 访问令牌
     * @param $uid // 客户端用户ID
     * @param $isEncryptedToken // 是否是加密过的令牌，默认否
     *
     * @return bool
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function validateToken($token, $uid, $isEncryptedToken = false)
    {
        if ($isEncryptedToken) {
            $token = $this->decryptToken($token);
            if (!$token) {
                return false;
            }
        }

        $result = MemberToken::quickGetOne(null, [['token', '=', $token], ['member_id', '=', $uid]]);

        return !empty($result);
    }

    /**
     * 获取用户Token数据.
     *
     * @param $uid // 用户ID
     *
     * @return array|null
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    public function getTokenAll($uid)
    {
        try {
            $tokenModel = MemberToken::quickGetOne(null, ['member_id' => $uid]);
            $tokenGather = $tokenModel->toArray();

            return $tokenGather;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 用户ID获取Token.
     *
     * @param $uid // 客户端用户ID
     * @param $isReturnEncrypted // 是否获取加密令牌，默认：true
     *
     * @return mixed
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function getAccessTokenById($uid, $isReturnEncrypted = true)
    {
        $tokenGather = MemberToken::quickGetOne(null, ['member_id', $uid]);
        $token = $tokenGather['token'];
        if ($isReturnEncrypted) {
            return $this->encryptToken($token);
        }

        return $token;
    }

    /**
     * 判断用户是否已注册Token.
     *
     * @param $uid // 用户ID
     *
     * @return bool
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    public function hasToken($uid)
    {
        $result = MemberToken::quickGetOne(null, ['member_id' => $uid]);

        return !empty($result);
    }

    /**
     * 更新用户Token.
     *
     * @param $uid // 用户ID
     * @param bool $isRefresh // 是否强制刷新，默认：false
     *
     * @return array|false
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function updateToken($uid, $isRefresh = false)
    {
        static $updateTokenCounter = 0;
        try {
            $accessToken = $this->generateToken($isRefresh);
            MemberToken::updateRefresh($uid, 'token', $accessToken);
            $userToken = $this->getTokenAll($uid);
            if (empty($userToken)) {
                return false;
            }

            return [
                '_t' => $this->encryptToken($userToken['token']),
                '_key' => $userToken['key'],
                '_uid' => $uid,
            ];
        } catch (\Exception $e) {
            ++$updateTokenCounter;
            if ($updateTokenCounter > 5) {
                return false;
            }

            return $this->updateToken($uid, true);
        }
    }

    /**
     * 更新用户Key.
     *
     * @param $uid // 用户ID
     * @param bool $isRefresh // 是否强制刷新，默认：false
     *
     * @return bool
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     *
     * @api *
     */
    public function updateKey($uid, $isRefresh = false)
    {
        static $updateKeyCounter = 0;
        try {
            $key = $this->generateKey($isRefresh);
            MemberToken::updateRefresh($uid, 'key', $key);

            return true;
        } catch (\Exception $e) {
            ++$updateKeyCounter;
            if ($updateKeyCounter > 5) {
                return false;
            }

            return $this->updateKey($uid, true);
        }
    }

    /**
     * 注册用户Token.
     *
     * @param $uid // 用户ID
     * @param bool $isRefresh // 是否强制刷新，默认：false
     *
     * @return array|false
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function setToken($uid, $isRefresh = false)
    {
        static $setTokenCounter = 0;
        try {
            $accessToken = $this->generateToken($isRefresh);
            $key = $this->generateKey($isRefresh);
            $rows = MemberToken::quickCreate([
                'member_id' => $uid,
                'token' => $accessToken,
                'key' => $key,
                'create_time' => date('Y-m-d H:i:s'),
            ]);

            if (!$rows) {
                throw new Exception('errors');
            }

            return [
                '_t' => $this->encryptToken($accessToken),
                '_key' => $key,
                '_uid' => $uid,
            ];
        } catch (\Exception $e) {
            ++$setTokenCounter;
            if ($setTokenCounter > 5) {
                return false;
            }

            return $this->setToken($uid, true);
        }
    }

    /**
     * 生成随机Token.
     *
     * @param $isRefresh //是否强制刷新，默认：false
     *
     * @return string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function generateToken($isRefresh = false)
    {
        return Helper::randomKey(0, 20, $isRefresh);
    }

    /**
     * 生成用户私Key.
     *
     * @param $isRefresh //是否强制刷新，默认：false
     *
     * @return string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function generateKey($isRefresh = false)
    {
        return Helper::randomKey(20, 20, $isRefresh);
    }

    /**
     * Token加密(有效期30天).
     *
     * @param $token // 未加密的令牌
     *
     * @return bool|string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function encryptToken($token)
    {
        $token = Helper::flipCrypt($token, 'ENCODE', $this->tokenKey, 2592000);

        return strtr($token, '+/', '-_');
    }

    /**
     * Token解密.
     *
     * @param $requestToken // 加密过的令牌
     *
     * @return bool|string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function decryptToken($requestToken)
    {
        return Helper::flipCrypt(strtr($requestToken, '-_', '+/'), 'DECODE', $this->tokenKey);
    }

    /**
     * Json响应格式.
     *
     * 状态码code值：
     * -1：重定向，authentication验证失败、 token过期等，描述：接口调用失败，重定向登录页面。
     * 0： 接口正常，业务逻辑执行失败，描述：将返回失败提示。
     * 1： 接口正常，业务逻辑执行成功。
     *
     * @param int    $code   // -1，0，1
     * @param string $status
     * @param string $msg
     * @param string $data
     *
     * @return \think\response\Json
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function asJson($code = 1, $status = 'success', $msg = '请求成功', $data = '')
    {
        return json(compact('code', 'status', 'msg', 'data'));
    }

    public function asNewJson($funcName, $code, $status, $msg, $args = [])
    {
        return json(compact('funcName', 'code', 'status', 'msg', 'args'));
    }

    /**
     * XML响应格式.
     *
     * @param int    $code   // 状态码
     * @param string $status // 状态值
     * @param string $msg    // 提示语
     * @param string $data   // 数据
     *
     * @return string
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function asXml($code = 1, $status = 'success', $msg = '请求成功', $data = '')
    {
        header('Content-Type: text/xml; charset=UTF-8');

        return Helper::data2XML(compact('code', 'status', 'msg', 'data'));
    }

    /**
     * 空方法处理.
     *
     * @param $method // 方法
     *
     * @return \think\response\Json
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function __empty($method)
    {
        return $this->asJson(-1, 'error', $method.'方法不存在');
    }
}
