<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:21
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common;

use app\common\model\AdminLog;
use app\common\model\AdminMsg;
use app\common\model\Attach;
use qrcode\QRcode;
use think\Db;
use web_msg_sender\Event;

/**
 * 助手类
 *
 * Class Helper
 * @package app\common
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Helper
{
    /**
     * 数组一的指定字段值作为新数组的key，
     * 数组二的指定字段值作为新数组的value。
     * ```php
     *  $arrayOne = [
     *  ['id' => 1, 'name' => '小明'],
     *  ['id' => 2, 'name' => '小孙'],
     *  ['id' => 3, 'name' => '小李'],
     * ];
     * Helper::indexArray($arrayOne, 'id', $arrayOne, 'name');
     * //输出：[
     * 1 => '小明',
     * 2 => '小孙',
     * 3 => '小李',
     * ]
     * ```
     *
     * @param array $sourArray
     * @param $sourAttr
     * @param array $desArray
     * @param $desAttr
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */

    /**
     * 发送短信
     * @param _username 用户名
     * @param _password 密码
     * @param _api 短信服务器地址
     * @return bool 成功返回true, 网络请求失败返回false, 其他返回失败编码
     */
    protected static $_username = 'qfsc';
    protected static $_password = 'wX3fY6wY';
    protected static $_api = 'http://47.98.61.138:9001/smsSend.do';

    public static function indexArray(array $sourArray, $sourAttr, array $desArray, $desAttr)
    {
        $sourIndexArray = array_column($sourArray, $sourAttr);
        $desValueArray = array_column($desArray, $desAttr);

        return array_combine($sourIndexArray, $desValueArray);
    }

    /**
     * 数据加密生成不同的加密字符串，可解密。
     * 函数解析：
     * 1、PHP取余运算。
     * 2、PHP按位异或运算。
     * 3、ASCII基础码位和扩展码位 0-255。
     * 4、PHP底层函数ord()和chr()的运用。
     * 5、密码构思逻辑。密匙（用于加密数据和加密数据有效期校验。）、动态密匙（用于动态加密数据。）、校验密匙（用于校验数据的完整性。）
     * Example:
     * //加密：
     * $data = \app\Helper::flipCrypt('145@e10adc3949ba59abbe56e057f20f883e', 'ENCODE');
     * //2ba3aU/61rGj2PS4yN9Y/oGzjKBGV2Au8THtKqwe9TeJC9J+LexojZRkEPD5doSELw2YqrbGQFbGuSPHcI317D0
     *
     * //解密:
     * $data = app\Helper::flipCrypt('2ba3aU/61rGj2PS4yN9Y/oGzjKBGV2Au8THtKqwe9TeJC9J+LexojZRkEPD5doSELw2YqrbGQFbGuSPHcI317D0', 'DECODE');
     * //145@e10adc3949ba59abbe56e057f20f883e
     *
     * @param $string // 字符串
     * @param string $operation // 转换类型
     * @param string $key // 密匙
     * @param int $expiry // 过期时间
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function flipCrypt($string, $operation = 'DECODE', $key = '', $expiry = 43200)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $keyCLength = 4;
        $defaultKey = 'CUICAIJUANMUSTANDYANGHUILEI1314O'; // 默认私key

        // 密匙
        $key = md5($key ? $key : $defaultKey);

        // 密匙a会参与加解密
        $keyA = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyB = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyC = $keyCLength ? ($operation == 'DECODE' ? substr($string, 0, $keyCLength) : substr(md5(microtime()), -$keyCLength)) : '';
        // 参与运算的密匙
        $cryptKey = $keyA . md5($keyA . $keyC);
        $keyLength = strlen($cryptKey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyB(密匙b)，
        //解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$keyCLength位开始，因为密文前$keyCLength位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $keyCLength)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyB), 0, 16) . $string;
        $stringLength = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndKey = [];
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndKey[$i] = ord($cryptKey[$i % $keyLength]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndKey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if (
                (
                    substr($result, 0, 10) == 0             // 验证数据是否过期
                    || intval(substr($result, 0, 10)) - time() > 0
                )
                && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyB), 0, 16) // 验证数据完整性
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyC . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 加密方式
     *
     * @var string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    private static $_encryptWay = 'sha1';

    /**
     * 生成40位随机key
     *
     * @param int $offset // 偏移量
     * @param int $length // 长度
     * @param boolean $isRefresh // 是否强制刷新
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function randomKey($offset = 0, $length = 10, $isRefresh = false)
    {
        static $encryptStr;
        if (empty($encryptStr) || $isRefresh) {
            $randomStr = Helper::randomStr();
            $encryptStr = call_user_func(self::$_encryptWay, $randomStr);
        }

        $randomKey = mb_substr($encryptStr, $offset, $length, '8bit');
        return $randomKey;
    }

    /**
     * PHP数组或对象JSON编码。
     *
     * @param $array
     * @param bool $isTransUnicode
     * @return false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function jsonEncode($array, $isTransUnicode = false)
    {
        if ($isTransUnicode) {
            return json_encode($array);
        }

        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 生成随机字符串。
     * Example:
     * $randStr = $this->randomStr();
     * //abDEC
     *
     * @param int $count
     * @return string
     * @blog http://www.cnblogs.com/hellow-world
     */
    public static function randomStr($count = 15)
    {
        $array = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
        ];

        $returnString = '';
        for ($i = 0; $i < $count; $i++) {
            $returnString .= $array[array_rand($array)];
        }

        return $returnString;
    }

    /**
     * XML转数组
     *
     * @param $xml
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function XML2Array($xml)
    {
        $object = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($object);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * 数组或对象格式化XML
     *
     * @param $data
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function data2XML($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_null($val)) {
                $xml .= "<$key/>" . PHP_EOL;
            } else {
                if (!is_numeric($key)) {
                    $xml .= "<$key>";
                }
                $xml .= (is_array($val) || is_object($val)) ? self::data2XML($val) : $val;
                if (!is_numeric($key)) {
                    $xml .= "</$key>" . PHP_EOL;
                }
            }
        }

        return '<xml>' . PHP_EOL . $xml . '</xml>';
    }

    /**
     * CURL会话
     *
     * @param string $url // url
     * @param string $method // 访问动作
     * @param string $contentType // 数据类型
     * @param mixed $data // 数据
     * @param int $timeOut // 过期时间, 默认20s
     * @return mixed|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function curlRequest($url, $method = 'get', $contentType = '', array $data = [], $timeOut = 20)
    {
        $curl = curl_init($url);
        switch (strtolower($method)) {
            case 'get':
                break;
            case 'post':
                if (!strcmp($contentType, 'multipart')) {
                    $head = 'Content-Type: multipart/form-data; charset=UTF-8';
                    $param = $data;
                } elseif (!strcmp($contentType, 'urlencoded')) {
                    $head = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
                    $param = http_build_query($data);
                } elseif (!strcmp($contentType, 'xml')) {
                    $head = 'Content-Type: text/xml; charset=UTF-8';
                    $param = self::data2XML($data);
                } elseif (!strcmp($contentType, 'json')) {
                    $head = 'Content-Type: application/json; charset=UTF-8';
                    $param = self::jsonEncode($data, true);
                } else {
                    $head = '';
                    http_response_code(500);
                    trigger_error('Content-Type类型不支持' . $contentType, E_USER_ERROR);
                }

                curl_setopt($curl, CURLOPT_HTTPHEADER, [$head]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                break;
            default:
                http_response_code(500);
                trigger_error('method不被支持' . $method, E_USER_ERROR);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }

        return $data;
    }

    /**
     * 后台模拟表单提交
     *
     * @param $url // 请求url
     * @param $data // 表单数据
     * @param string $method // 请求动作
     * @author ken
     * @date 2019/5/14
     */
    public static function formSubmit($url, $data, $method = 'get')
    {

        $form = "<form action='{$url}' id='sendForm' name='sendForm' method='{$method}'>";
        foreach ($data as $key => $value) {
            $form .= "<input type='hidden' name='" . $key . "' value='" . $value . "'>";
        }

        $form .= "</form><script>document.sendForm.submit();</script>";
        echo $form;
    }

    /**
     * 时间戳格式化成日期。
     * Example:
     * self::timeFormat(1537889279, 'y')  //2018
     * self::timeFormat(1537889279, 'm')  //2018-9
     * self::timeFormat(1537889279, 'd')  //2018-9-25
     * self::timeFormat(1537889279, 'h')  //2018-9-25 23
     * self::timeFormat(1537889279, 'i')  //2018-9-25 23:27
     * self::timeFormat(1537889279, 's')  //2018-9-25 23:27:59
     *
     * @param $timestamp
     * @param string $accuracy
     * @param string $delimiterL
     * @param string $delimiterR
     * @return false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function timeFormat($timestamp, $accuracy = 'y', $delimiterL = '-', $delimiterR = ':')
    {
        switch (strtolower($accuracy)) {
            case 'y':
                return date('Y', $timestamp);

            case 'm':
                return date('Y' . $delimiterL . 'm', $timestamp);

            case 'd':
                return date('Y' . $delimiterL . 'm' . $delimiterL . 'd', $timestamp);

            case 'h':
                return date('Y' . $delimiterL . 'm' . $delimiterL . 'd ' . 'H', $timestamp);

            case 'i':
                return date('Y' . $delimiterL . 'm' . $delimiterL . 'd ' . 'H' . $delimiterR . 'i', $timestamp);

            case 's':
                return date('Y' . $delimiterL . 'm' . $delimiterL . 'd ' . 'H' . $delimiterR . 'i' . $delimiterR . 's', $timestamp);

        }

        return date('Y' . $delimiterL . 'm' . $delimiterL . 'd', $timestamp);
    }

    /**
     * 日期工具函数   w: 当周   d: 当天  m: 当月
     * Example:
     * $curDay = self::mkTime('d');
     * $startTime = $curDay['start'];
     * $endTime = $curDay['end'];
     * SQL:
     * select * from `cy_order` where `create_time` between {$startTime} and {$endTime};
     *
     * @param string $identify
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function mkTime($identify = 'd')
    {
        switch (strtolower($identify)) {
            case 'd':
                $time1 = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $time2 = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
                return ['start' => $time1, 'end' => $time2];
            case 'w':
                $time1 = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
                $time2 = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y'));
                return ['start' => $time1, 'end' => $time2];
            default:
                $time1 = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $time2 = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                return ['start' => $time1, 'end' => $time2];
        }
    }

    /**
     * 生成随机验证码
     *
     * @param int $size // 验证码位数
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function randomCode($size = 6)
    {
        $start = intval(1 . str_repeat('0', $size - 1));
        $end = intval(str_repeat('9', $size));

        return rand($start, $end);
    }

    /**
     * 图片上传, 暂不支持多图片上传
     * 参数: file（Attach表引擎：myisam不支持事务）
     *
     * @param string $type // 数据类型, binary 或 base64
     * @param string $dir // 上传目录, 默认是: /public/uploads/members/*
     * @param string $base64 // 当数据类型为base64时，base64数据可以直接作为参数传入
     * @param int $size // 图片大小限制 8M
     * @param array $ext // 图片类型限制
     * @param array $mine // mine类型
     * @return array|string // 错误信息|['path' => , 'head' => ]
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function uploadImage(
        $type = 'binary', // 数据类型, 二进制 或 base64
        $dir = 'members', // 上传目录, 默认是: /public/uploads/members/*
        $base64 = '', // 当数据类型为base64时，base64数据可以直接作为参数传入
        $size = 8388608,  // 图片大小限制 8M
        $ext = ['png', 'jpg', 'jpeg', 'gif'], // 图片类型限制
        $mine = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'] // mine类型
    )
    {
        if (!strcasecmp($type, 'binary')) {
            $file = request()->file('file');
        } elseif (!strcasecmp($type, 'base64')) {
            if (empty($base64)) {
                $base64 = request()->post('file');
            }

            if (strpos($base64, ',') === false) {
                return 'base64图片格式不正确';
            }

            list($header, $data) = explode(',', $base64);
            $result = preg_match('/^data:(.*);base64$/usi', $header, $match);
            if (!$result) {
                return 'base64图片格式不正确';
            }

            $imgMine = $match[1];
            list(, $imgExt) = explode('/', $match[1]);
            if (!in_array($imgMine, $mine)) {
                return '图片类型不支持';
            }

            if (!in_array($imgExt, $ext)) {
                return '图片类型不支持';
            }

            $data = str_replace('=', '', $data);
            $binary = base64_decode($data);
            $imgSize = mb_strlen($binary, '8bit');
            if ($imgSize > $size) {
                return '图片过大, 上传失败';
            }

            $dirName = date('Ymd');
            $fileDir = UPLOAD_PATH . $dir . DS . $dirName . DS;
            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0755, true);
            }

            $getFileNameFunc = function ($fileDir, $imgExt, $getFileNameFunc, &$fileName) {
                $name = md5(date('Ymd') . microtime());
                $fileName = $name . '.' . $imgExt;
                $completeFileName = $fileDir . $fileName;
                $isExists = realpath($completeFileName);
                if ($isExists) {
                    return call_user_func_array($getFileNameFunc, [$fileDir, $imgExt, $getFileNameFunc, &$fileName]);
                }

                return $completeFileName;
            };
            $fileName = '';
            $completeFileName = call_user_func_array($getFileNameFunc, [$fileDir, $imgExt, $getFileNameFunc, &$fileName]);
            $fh = fopen($completeFileName, 'w+');
            flock($fh, LOCK_EX);
            fputs($fh, $binary, $imgSize);
            flock($fh, LOCK_UN);
            fclose($fh);
            $md5 = md5_file($completeFileName);
            $path = '/uploads/' . $dir . '/' . $dirName . '/' . $fileName;
            Db::startTrans(); // 事务弃用
            try {
                $model = Attach::create([
                    'path' => $path,
                    'md5' => $md5,
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                ]);
                Db::commit();
                if (isset($model->id)) {
                    return ['path' => $path, 'head' => $model->id];
                }

                return '上传失败';
            } catch (\Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
        } else {
            http_response_code(500);
            trigger_error('未知数据类型' . $type, E_USER_ERROR);
            exit(0);
        }

        $dirName = date('Ymd');
        $fileDir = UPLOAD_PATH . $dir . DS . $dirName . DS;
        if (!is_dir($fileDir)) {
            mkdir($fileDir, 0755, true);
        }

        $md5 = $file->hash('md5');
        $upload = $file->validate([
            'size' => $size,
            'type' => $mine,
            'ext' => $ext,
        ])
            ->move($fileDir, $md5);
        if (!$upload) {
            return $file->getError();
        }

        $path = '/uploads/' . $dir . '/' . $dirName . '/' . $upload->getSaveName();
        Db::startTrans(); // 事务弃用
        try {
            $model = Attach::create([
                'path' => $path,
                'md5' => $md5,
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
            Db::commit();
            if (isset($model->id)) {
                return ['path' => $path, 'head' => $model->id];
            }

            return '图片保存附件失败';
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 生成二维码
     *
     * @param $text // 文本数据
     * @return array|string // 错误信息|['path' => , 'head' => ]
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function qrcode($text)
    {
        $dir = UPLOAD_PATH . 'qrcode' . DS;
        $dirName = date('Ymd') . DS;
        if (!is_dir($dir . $dirName)) {
            mkdir($dir . $dirName, 0755, true);
        }

        $fileName = '';
        $func = function ($dir, $dirName, &$fileName, $func) {
            $fileName = md5($dirName . microtime()) . '.png';
            $fileCompletePath = $dir . $dirName . $fileName;
            if (realpath($fileCompletePath)) {
                return call_user_func_array($func, [$dir, $dirName, &$fileName, $func]);
            }

            return $fileCompletePath;
        };

        $fileCompletePath = call_user_func_array($func, [$dir, $dirName, &$fileName, $func]);
        QRcode::png($text, $fileCompletePath);
        $md5 = md5_file($fileCompletePath);
        $path = '/uploads/qrcode/' . str_replace('\\', '/', $dirName . $fileName);
        Db::startTrans(); // 事务弃用
        try {
            $model = Attach::create([
                'path' => $path,
                'md5' => $md5,
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
            Db::commit();
            if (isset($model->id)) {
                return ['path' => $path, 'head' => $model->id];
            }

            return '二维码保存附件失败';
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 生成19|20|21位订单号。
     * 重复几率：
     * 每微秒十亿分之一。
     * 循环遍历下, 每微秒十万分之一
     * Example:
     * $orderNum = self::orderNumber();
     * //0925905401921120715
     *
     * @param bool $prefix // 订单前缀
     * @param $defaultYear // 默认起始年号
     * @return string
     * @blog http://www.cnblogs.com/hellow-world
     */
    public static function orderNumber($prefix = false, $defaultYear = 2018)
    {
        $orderNumber = (date('Y') - $defaultYear) . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%05d', rand(0, 99999));
        $code = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $prefixCode = $code[array_rand($code)];

        if ($prefix === true) {
            $orderNumber = $prefixCode . $orderNumber;
        } elseif (is_string($prefix)) {
            $orderNumber = $prefix . $orderNumber;
        }

        return $orderNumber;
    }

    /**
     * 快捷获取当前域名
     * @return string
     * @author CleverStone
     */
    public static function getCurrentHost()
    {
        $remoteAddr = 'http';
        if (
            (
                isset($_SERVER["HTTPS"])
                && !strcasecmp($_SERVER["HTTPS"], 'on')
            )
            ||
            (
                isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && !strcasecmp($_SERVER["HTTP_X_FORWARDED_PROTO"], 'https')
            )
        ) {
            $remoteAddr .= "s";
        }

        $remoteAddr .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $remoteAddr .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
        } else {
            $remoteAddr .= $_SERVER["SERVER_NAME"];
        }

        return rtrim($remoteAddr, '/');
    }

    /**
     * 快捷获取, 客户端(代理)IPv4
     * @return array|false|null|string
     * @author CleverStone
     */
    public static function getClientIP()
    {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } /*elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        }*/ elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }
        $ip = preg_match('%[\d\.]{7,15}%u', $ip, $matches) ? $matches [0] : null;

        return $ip;
    }

    /**
     * json转php数组或对象
     *
     * @param $jsonData
     * @param bool $objectFlags
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function jsonDecode($jsonData, $objectFlags = false)
    {
        if (!$objectFlags) {
            return json_decode($jsonData, true);
        }

        return json_decode($jsonData);
    }

    /**
     * 发送短信
     *
     * @param $mobile // 手机号
     * @param $content // 短信内容
     * @param string $smsSign // 短信签名
     * @param $smsTemplate // 短信模板，默认为空，并使用默认模板。如果是闭包，请确保该闭包可传入content。
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function sendSms($mobile, $content, $smsSign = '【凤凰体育】', $smsTemplate = '')
    {
        if ($smsTemplate instanceof \Closure) {
            $smsBody = call_user_func($smsTemplate, $content);
        } else {
            $smsBody = <<<SMS
  验证码：{$content}，有效时间为5分钟，请不要把验证码泄露给他人，如非本人操作，请忽略此短信。
SMS;
        }

        $username = self::$_username;
        $password = md5($username . md5(self::$_password));
        $content = $smsSign . $smsBody;
        $result = self::curlRequest(self::$_api, 'post', 'urlencoded', ['username' => $username, 'password' => $password, 'content' => $content, 'mobile' => $mobile]);
        if (preg_match('~^[1-9]{1}[0-9]+$~us', $result)) {
            return true;
        }

        return $result;
    }

    /**
     * 系统日志
     *
     * @param $executeUser // 执行人
     * @param $businessName // 业务名称
     * @param $remark // 备注
     * @param $errorInfo // 错误信息
     * @param $status // 状态
     * @param int $belongPlat // 所属平台
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function log(
        $executeUser, // 执行人, 例如: 系统 , 18739902541
        $businessName, // 业务名称
        $remark, // 备注
        $errorInfo, // 日志信息
        $status, // 执行状态 0:失败  1:成功  2:未知
        $belongPlat = 1 // 所属平台, 默认是 1:总后台  2:代理商后台  3:APP
    )
    {
        $logId = AdminLog::quickCreate([
            'belong' => $belongPlat,
            'executor' => $executeUser,
            'work_name' => $businessName,
            'remark' => $remark,
            'info' => $errorInfo,
            'status' => $status,
            'exec_time' => self::timeFormat(time(), 's'),
        ]);

        return $logId;
    }

    /**
     * 日期字符串转口语化日期
     *
     * @return string
     * @param $datetime // 日期字符串
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function dateToStr($datetime)
    {
        $time = strtotime($datetime);
        $timeInterval = time() - $time;
        if ($timeInterval < 0) {
            return $datetime;
        }

        // 小于5分钟: 刚刚
        if ($timeInterval <= 300) {
            return '刚刚';
        }

        // 小于1小时: 多少分钟以前
        if ($timeInterval < 3600) {
            return round($timeInterval / 60) . '分钟前';
        }

        // 小于24小时: 多少小时以前
        if ($timeInterval < 86400) {
            return round($timeInterval / 3600) . '小时前';
        }

        // 小于一个月: 多少天以前
        if ($timeInterval < 2592000) {
            return round($timeInterval / 86400) . '天前';
        }

        return date('Y-m-d', strtotime($datetime));
    }

    /**
     * 写入并推送消息
     *
     * 例如:
     * Helper::logAndPushMsg('10注足彩下单成功', 'CleverStone', '18739908474', 3, 2)
     *
     * @param $body // 消息内容
     * @param $user // 用户真实姓名或昵称
     * @param $username // 用户账号
     * @param $msgType // 消息类型  1:系统消息  2:代理商消息 3:会员消息 4:其他
     * @param $bodyType // 内容类型  1:资金提现 2:会员注单 3:资金充值 4:其他
     * @param $iconId // 头像或icon 附件ID
     * @return true
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    final public static function logAndPushMsg(
        $body, // 消息内容
        $user, // 用户真实姓名或昵称
        $username, // 用户账号
        $msgType, // 消息类型  1:系统消息  2:代理商消息 3:会员消息 4:其他
        $bodyType, // 内容类型  1:资金提现 2:会员注单 3:资金充值 4:其他
        $iconId = 0 // 头像或icon 附件ID
    )
    {
        $data = [
            'name' => $user, // 用户真实姓名或昵称
            'account' => $username, // 用户账号
            'msg_type' => $msgType, // 消息类型
            'body_type' => $bodyType, // 内容类型
            'desc' => $body, // 内容
            'icon' => $iconId, // 头像或icon 附件ID
            'send_time' => self::timeFormat(time(), 's'), // 发送时间
            'read_state' => 0, // 状态
        ];

        $msgId = AdminMsg::quickCreate($data);
        if (!$msgId) {
            trigger_error('消息写入失败', E_USER_WARNING);
        }

        switch ((int)$bodyType) {
            case 1:
            case 2:
            case 3:
                // 推送客户端消息
                $data['icon'] = Attach::getPathByAttachId($data['icon']) ?: '/static/lib/images/msgicon.jpg'; // 获取头像或ICON
                $data['msg_type'] = AdminMsg::getMsgType($data['msg_type']); // 获取消息类型字符串格式
                $data['send_time'] = self::dateToStr($data['send_time']); // 获取发送时间字符串格式
                $data['body_type'] = AdminMsg::getBodyType($data['body_type']); // 获取内容类型字符串格式
                $data['id'] = $msgId; // 消息ID
                $worker = new Event;
                $worker->setUser()->setContent(Helper::jsonEncode($data))->push();
                break;
        }

        return true;
    }

    /**
     * 导出zip格式Excel数据。
     *
     * 例如:
     * Helper::exportExcel(
     *      'excel名',
     *      ['ID', '会员账号', '手机号', '订单号', '交易类型', '交易点数', '交易时间'],
     *      [
     *         [1, 1375645645, 137546464, E54656464564, 1, 230, 2019-10-20 05:02:03],
     *         [2, 1587564564, 1587564564, E6545645644, 1, 10, 2019-10-10 05:02:03],
     *         ......
     *      ]
     *  );
     * @param $excelName // excel表格名
     * @param $fields // 字段
     * @param $data // 数据(二维数组)
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public static function exportExcel(
        $excelName, // excel表格名
        $fields, // 字段
        $data // 数据(二维数组)
    )
    {
        set_time_limit(0);
        ini_set('memory_limit', '800M');
        $excelPortFunc = function ($excelName, $fields, $data) {
            $PHPExcel = new \PHPExcel();                                   // 实例Excel模型
            $PHPExcel->setActiveSheetIndex();                              // 不设置索引
            $activeSheet = $PHPExcel->getActiveSheet();                    // 创建活动表
            $c = 'A';                                                      // 第一列单元格
            foreach ($fields as $sheetTop) {
                $activeSheet->setCellValue("{$c}1", $sheetTop);             // 设置第一列、第一行活动表头部
                $c++;                                                       // 单元格递增
            }

            $r = 2;                                                         // 第二行单元格
            foreach ($data as $dbCols) {
                $c = 'A';
                foreach ($dbCols as $dbVal) {
                    $activeSheet->setCellValue("{$c}{$r}", $dbVal);          // 活动表数据
                    $c++;                                                    // 活动表列递增
                }
                $r++;                                                        // 活动表行递增
            }

            if (!file_exists($filename = EXCEL_PATH . $excelName . DS)) {
                mkdir(EXCEL_PATH . $excelName . DS, 0777, true);
                chmod(EXCEL_PATH . $excelName . DS, 0777);
            }

            $inputFactory = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');   // 创建Excel写入模型工厂
            $inputFactory->save($filename . $excelName . '.xlsx');
        };

        $dirName = $filename = $excelName . date('Ymd-His');
        call_user_func($excelPortFunc, $filename, $fields, $data);
        $zipPath = $excelPath = EXCEL_PATH;
        // zip文件名
        $zipName = $dirName . ".zip";
        $z = new \zip\PHPZip();
        // 添加指定目录
        $res = $z->Zip($excelPath . $dirName, $zipPath . $zipName);
        // 删除生成的Excel文件夹和文件
        self::rmdir($excelPath . $dirName);
        if ($res == 1) {
            if (!file_exists($filename = $zipPath . $zipName)) {
                http_response_code(500);
                trigger_error('文件不存在', E_USER_ERROR);
            }

            // 更改目录执行权限
            chmod($filename, 0777);
            // 设置头部
            header('Content-Type:text/html;charset=utf-8');
            header('Content-length:' . filesize($zipPath . $zipName));
            header('Content-Disposition:attachment;filename=' . $zipName);
            // 输出
            file_put_contents('php://output', file_get_contents($filename));
            // 删除压缩包
            unlink($filename);
            exit(0);
        } else {
            http_response_code(500);
            trigger_error('压缩失败', E_USER_ERROR);
        }
    }

    /**
     * 删除目录和下面所有文件
     *
     * @param $dir // 目录路径
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    final public static function rmdir($dir)
    {
        $dir = rtrim(rtrim($dir, '\\'), '/');
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' or $file == '..') {
                        continue;
                    }

                    if (is_file($dir . DS . $file)) {
                        unlink($dir . DS . $file);
                    }

                    if (is_dir($dir . DS . $file)) {
                        self::rmdir($dir . DS . $file);
                    }
                }
            }
            closedir($dh);
            // 删除目录
            rmdir($dir);
        }

        return true;
    }

    /**
     * 客户端环境是否是微信
     *
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    final public static function isWeiXin()
    {
        if (
            isset($_SERVER['HTTP_USER_AGENT'])
            && stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
        ) {
            //微信浏览器内核
            return true;
        }

        //其他浏览器内核
        return false;
    }

    /**
     * 日期获取周几
     *
     * @param $date // 日期字符串, 如: 2019-04-17
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    final public static function getWeekByDate($date)
    {
        if (empty($date)) {
            return '';
        }

        $weekStrArr = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
        $weekNum = date('w', strtotime($date));

        return $weekStrArr[$weekNum];
    }

    /**
     * 根据计算数字获取日期
     *
     * @param string $computeNo // 计算数字 如: +2 获取两天后的日期
     * @param string $format // 日期格式
     * @return false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    final public static function getDateByComputed($computeNo = '+2', $format = 'Y-m-d')
    {
        return date($format, strtotime(date('Y-m-d') . $computeNo . ' days'));
    }
}
