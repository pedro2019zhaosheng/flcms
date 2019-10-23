<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:50
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\pxy\controller;

use app\common\Helper;
use app\common\PxyController;
use think\Session;

/**
 * 视图层控制器
 *
 * Class View
 * @package app\pxy\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class View extends PxyController
{

    /**
     * 总后台首页
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $options = [
            'title' => '首页',
            'js' => [
                '/static/lib/js/jquery.waypoints.min.js', /*Counter-Up 动态数字插件*/
                '/static/lib/js/jquery.counterup.min.js', /*Counter-Up 动态数字插件*/
                '/static/lib/js/chart.min.js', /*echart*/
                '/static/pxy/index/index.js',
            ],
            'css' => [
                '/static/pxy/index/index.css',
            ],
            'nav' => [
                ['title' => '首页', 'active' => true],
            ]
        ];
        return $this->fetch('view/index/index', $options);
    }

    /**
     * @desc 推荐会员
     * @auther LiBin
     * @date 2019-03-21
     */
    public function recoMember()
    {
        $options = [
            'title' => '下级会员列表',
            'js' => [
                '/static/pxy/member/recoMember.js'
            ],
            'css' => [
                '/static/pxy/member/member.css'
            ],
            'nav' => [
                ['title' => '下级会员列表', 'active' => true],
            ],
            'jsPage' => 'memberPage', // angular分页数据模型
            'currentPage' => 'memberCurrentPage', // 当前页
            'totalPage' => 'memberTotalPage', // 总页数
            'perPage' => 'memberPerPage', // 每页数据条数

        ];
        return $this->fetch('view/member/recoMember', $options);
    }

    /**
     * 代理后台登录
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function login()
    {
        // 关闭全局布局配置
        $this->view->engine->layout(false);
        $session = new Session();
        if ($session->get('agentId')) {
            $this->redirect('/');
            exit(0);
        }

        // 添加自定义csrf防伪令牌
        $csrfToken = md5(Helper::randomStr());
        $session->set('pxy_csrf_token', $csrfToken);
        $session->set('pxy_csrf_request_size', 1);
        $csrf = ['_aid' => $csrfToken];

        return $this->fetch('view/login/login', $csrf);
    }

    /**
     * @desc 会员列表
     * @auther LiBin
     * @date 2019-03-02
     */
    public function member()
    {
        $options = [
            'title' => '会员列表',
            'js' => [
                '/static/pxy/member/member.js',
            ],
            'css' => [
                '/static/pxy/member/member.css',
            ],
            'nav' => [
                ['title' => '会员列表', 'active' => true],
            ],
            'jsPage' => 'memberPage', // angular分页数据模型
            'currentPage' => 'memberCurrentPage', // 当前页
            'totalPage' => 'memberTotalPage', // 总页数
            'perPage' => 'memberPerPage', // 每页数据条数
        ];

        return $this->fetch('view/member/member', $options);
    }

    /**
     * @desc 代理商列表
     * @auther LiBin
     * @date 2019-03-14
     */
    public function agent()
    {
        $options = [
            'title' => '代理商列表',
            'js' => [
                '/static/pxy/agent/agent.js',
            ],
            'css' => [
                '/static/pxy/agent/agent.css',
            ],
            'nav' => [
                ['title' => '代理商列表', 'active' => true],
            ],
            'jsPage' => 'agentPage', // angular分页数据模型
            'currentPage' => 'agentCurrentPage', // 当前页
            'totalPage' => 'agentTotalPage', // 总页数
            'perPage' => 'agentPerPage', // 每页数据条数
        ];

        return $this->fetch('view/agent/agent', $options);
    }

    /**
     * @desc 代理商返点设置
     * @auther LiBin
     * @date 2019-03-14
     */
    public function agentReturn()
    {
        $options = [
            'title' => '代理返点设置',
            'js' => [
                '/static/pxy/agent/agentReturn.js',
            ],
            'nav' => [
                ['title' => '代理返点设置', 'active' => true],
            ],
            'jsPage' => 'agentPage', // angular分页数据模型
            'currentPage' => 'agentCurrentPage', // 当前页
            'totalPage' => 'agentTotalPage', // 总页数
            'perPage' => 'agentPerPage', // 每页数据条数
        ];
        return $this->fetch('view/agent/agentReturn', $options);
    }

    /**
     * @desc 资金管理----充值记录
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function capitalRecharge()
    {
        $options = [
            'title' => '充值记录',
            'js' => [
                '/static/pxy/capital/capitalRecharge.js',
            ],
            'nav' => [
                ['title' => '充值记录', 'active' => true],
            ],
            'jsPage' => 'rechargePage', // angular分页数据模型
            'currentPage' => 'rechargeCurrentPage', // 当前页
            'totalPage' => 'rechargeTotalPage', // 总页数
            'perPage' => 'rechargePerPage', // 每页数据条数
        ];
        return $this->fetch('view/capital/capitalRecharge', $options);
    }

    /**
     * @desc 资金管理----提现记录
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function capitalCash()
    {
        $options = [
            'title' => '提现记录',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/pxy/capital/index.js',
            ],
            'nav' => [
                ['title' => '提现记录', 'active' => true],
            ],
            'jsPage' => 'rechargePage', // angular分页数据模型
            'currentPage' => 'rechargeCurrentPage', // 当前页
            'totalPage' => 'rechargeTotalPage', // 总页数
            'perPage' => 'rechargePerPage', // 每页数据条数
        ];

        return $this->fetch('view/capital/capitalCash', $options);
    }

    /**
     * @desc 资金管理----投注返佣记录
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function capitalRakeBack()
    {
        $options = [
            'title' => '投注返佣记录',
            'js' => [
                '/static/pxy/capital/capitalRakeBack.js',
            ],
            'nav' => [
                ['title' => '投注返佣记录', 'active' => true],
            ],
            'jsPage' => 'rebatePage', // angular分页数据模型
            'currentPage' => 'rebateCurrentPage', // 当前页
            'totalPage' => 'rebateTotalPage', // 总页数
            'perPage' => 'rebatePerPage', // 每页数据条数
        ];

        return $this->fetch('view/capital/capitalRakeBack', $options);
    }

    /**
     * @desc 资金管理----资金校正记录
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function capitalCorrection()
    {
        $options = [
            'title' => '资金校正记录',
            'js' => [
                '/static/pxy/capital/capitalCorrection.js',
            ],
            'nav' => [
                ['title' => '资金校正记录', 'active' => true],
            ],
            'jsPage' => 'capitalPage', // angular分页数据模型
            'currentPage' => 'capitalCurrentPage', // 当前页
            'totalPage' => 'capitalTotalPage', // 总页数
            'perPage' => 'capitalPerPage', // 每页数据条数
        ];
        return $this->fetch('view/capital/capitalCorrection', $options);
    }

    /**
     * @desc 资金管理----资金流水记录
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function capitalFlow()
    {
        $options = [
            'title' => '资金流水记录',
            'js' => [
                '/static/pxy/capital/capitalFlow.js',
            ],
            'nav' => [
                ['title' => '资金流水记录', 'active' => true],
            ],
            'jsPage' => 'waterPage', // angular分页数据模型
            'currentPage' => 'waterCurrentPage', // 当前页
            'totalPage' => 'waterTotalPage', // 总页数
            'perPage' => 'waterPerPage', // 每页数据条数
        ];

        return $this->fetch('view/capital/capitalFlow', $options);
    }

    /**
     * @desc 注单管理----注单列表
     * @auther BaoGaiJie
     * @date 2019-03-07
     */
    public function betList()
    {
        $options = [
            'title' => '注单列表',
            'js' => [
                '/static/pxy/bet/betList.js',
            ],
            'css' => [
                '/static/pxy/bet/bet.css',
            ],
            'nav' => [
                ['title' => '注单列表', 'active' => true],
            ],
            'jsPage' => 'betPage', // angular分页数据模型
            'currentPage' => 'betCurrentPage', // 当前页
            'totalPage' => 'betTotalPage', // 总页数
            'perPage' => 'betPerPage', // 每页数据条数
        ];
        return $this->fetch('view/bet/betList', $options);
    }

    /**
     * @desc 注单管理----推单列表
     * @auther BaoGaiJie
     * @date 2019-03-07
     */
    public function betExtend()
    {
        $options = [
            'title' => '推单列表',
            'js' => [
                '/static/pxy/bet/betExtend.js',
            ],
            'css' => [
                '/static/pxy/bet/bet.css',
            ],
            'nav' => [
                ['title' => '推单列表', 'active' => true],
            ],
            'jsPage' => 'betExtendPage', // angular分页数据模型
            'currentPage' => 'betExtendCurrentPage', // 当前页
            'totalPage' => 'betExtendTotalPage', // 总页数
            'perPage' => 'betExtendPerPage', // 每页数据条数
        ];

        return $this->fetch('view/bet/betExtend', $options);
    }

    /**
     * 个人中心
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function personal()
    {
        $options = [
            'title' => '个人中心',
            'js' => [
                '/static/pxy/member/detail.js',
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
            ],
            'nav' => [
                ['title' => '个人中心', 'active' => true],
            ]
        ];

        return $this->fetch('view/member/detail', $options);
    }

    /**
     * 代充值
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function agentRecharge()
    {
        $options = [
            'title' => '代充值',
            'js' => [
                '/static/pxy/recharge/recharge.js',
            ],
            'nav' => [
                ['title' => '代充值', 'active' => true],
            ],
        ];

        return $this->fetch('view/recharge/index', $options);
    }
}