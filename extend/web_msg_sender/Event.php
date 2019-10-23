<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 13:00
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace web_msg_sender;

use app\common\Config;
use app\common\Helper;

class Event
{
    // 推送用户ID
    protected $toUser = '';
    // 推送服务地址
    protected $pushUrl;
    //  推送内容
    protected $content = '';

    /**
     * 构造
     * Event constructor.
     */
    public function __construct()
    {
        $this->pushUrl = Config::SOCKET_IO_URL;
    }

    /**
     * 设置推送用户，若参数留空则推送到所有在线用户
     *
     * @param string $user // 用户ID
     * @return $this
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function setUser($user = '')
    {
        $this->toUser = $user ?: '';
        return $this;
    }

    /**
     * 设置推送内容
     *
     * @param string $content // 推送内容
     * @return $this
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 消息推送
     *
     * @return mixed|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function push()
    {

        $data = [
            'type' => 'publish',
            'content' => $this->content,
            'to' => $this->toUser,
        ];

        return Helper::curlRequest($this->pushUrl, 'post', 'urlencoded', $data);
    }
}