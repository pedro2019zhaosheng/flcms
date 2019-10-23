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

namespace app\vp\controller;

use app\common\Helper;
use app\common\VpController;
use think\Session;

class View extends VpController
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
                '/static/vp/index/index.js',
            ],
            'css' => [
                '/static/vp/index/index.css',
            ],
            'nav' => [
                ['title' => '首页', 'active' => true],
            ]
        ];

        return $this->fetch('view/index/index', $options);
    }

    /**
     * 总后台登录
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
        if ($session->get('adminId')) {
            $this->redirect('/vp/');
            exit(0);
        }

        // 添加自定义csrf防伪令牌
        $csrfToken = md5(Helper::randomStr());
        $session->set('vp_csrf_token', $csrfToken);
        $session->set('vp_csrf_request_size', 1);
        $csrf = ['_aid' => $csrfToken];

        return $this->fetch('view/login/login', $csrf);
    }

    /**
     * 后台管理员列表
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function admin()
    {
        $options = [
            'title' => '管理员列表',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/admin/admin.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/admin/admin.css',
            ],
            'nav' => [
                ['title' => '管理员列表', 'active' => true],
            ],
            'jsPage' => 'adminPage', // angular分页数据模型
            'currentPage' => 'adminCurrentPage', // 当前页
            'totalPage' => 'adminTotalPage', // 总页数
            'perPage' => 'adminPerPage', // 每页数据条数
        ];

        return $this->fetch('view/admin/admin', $options);
    }

    /**
     * @desc 后台管理员个人中心
     * @auther YanShusheng
     * @date 2019-03-16
     */
    public function adminDetail()
    {
        $options = [
            'title' => '个人中心',
            'js' => [
                '/static/vp/admin/detail.js',
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
        return $this->fetch('view/admin/detail', $options);
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
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/member/member.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/member/member.css',
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
     * @desc 推荐会员
     * @auther LiBin
     * @date 2019-03-21
     */
    public function recoMember()
    {
        $options = [
            'title' => '下级会员列表',
            'js' => [
                '/static/vp/member/recoMember.js'
            ],
            'css' => [
                '/static/vp/member/member.css'
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
     * @desc 模拟账户列表
     * @auther LiBin
     * @date 2019-03-02
     */
    public function simulation()
    {
        $options = [
            'title' => '模拟账户列表',
            'js' => [
                '/static/vp/simulation/simulation.js',
            ],
            'css' => [
                '/static/vp/member/member.css',
            ],
            'nav' => [
                ['title' => '模拟账户列表', 'active' => true],
            ],
            'jsPage' => 'memberPage', // angular分页数据模型
            'currentPage' => 'memberCurrentPage', // 当前页
            'totalPage' => 'memberTotalPage', // 总页数
            'perPage' => 'memberPerPage', // 每页数据条数
        ];

        return $this->fetch('view/simulation/member', $options);
    }

    /**
     * 角色模块
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function role()
    {
        $options = [
            'title' => '角色管理',
            'js' => [
                '/static/lib/jstree/jstree.min.js',
                '/static/vp/role/role.js',
            ],
            'css' => [
                '/static/lib/jstree/style.min.css',
                '/static/vp/role/role.css',
            ],
            'nav' => [
                ['title' => '角色管理', 'active' => true],
            ],
            'jsPage' => 'rolePage', // angular分页数据模型
            'currentPage' => 'roleCurrentPage', // 当前页
            'totalPage' => 'roleTotalPage', // 总页数
            'perPage' => 'rolePerPage', // 每页数据条数
        ];

        return $this->fetch('view/role/role', $options);
    }

    /**
     * @desc 代理商列表
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function agent()
    {
        $options = [
            'title' => '代理商列表',
            'js' => [
                '/static/vp/agent/agent.js',
            ],
            'css' => [
                '/static/vp/agent/agent.css',
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
     * @desc 代理返点设置
     * @auther BaoGaiJie
     * @date 2019-03-05
     */
    public function agentReturn()
    {
        $options = [
            'title' => '代理返点设置',
            'js' => [
                '/static/vp/agent/agentReturn.js',
            ],
            'nav' => [
                ['title' => '代理商列表', 'link' => '/vp/agent'],
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
                '/static/vp/capital/capitalRecharge.js',
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
                '/static/vp/capital/index.js',
            ],
            'nav' => [
                ['title' => '提现记录', 'active' => true],
            ],
            'jsPage' => 'adverPage', // angular分页数据模型
            'currentPage' => 'adverCurrentPage', // 当前页
            'totalPage' => 'adverTotalPage', // 总页数
            'perPage' => 'adverPerPage', // 每页数据条数
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
                '/static/vp/capital/capitalRakeBack.js',
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
                '/static/vp/capital/capitalCorrection.js',
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
                '/static/vp/capital/capitalFlow.js',
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
                '/static/vp/bet/betList.js',
            ],
            'css' => [
                '/static/vp/types/types.css',
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
                '/static/vp/bet/betExtend.js',
            ],
            'css' => [
                '/static/vp/bet/bet.css',
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
     * @desc 开奖管理----赛事开奖
     * @auther BaoGaiJie
     * @date 2019-03-07
     */
    public function lotteryMatch()
    {
        $options = [
            'title' => '赛事开奖',
            'js' => [
                '/static/vp/lottery/lotteryMatch.js',
            ],
            'nav' => [
                ['title' => '赛事开奖', 'active' => true],
            ],
            'jsPage' => 'drawJsPage', // angular分页数据模型
            'currentPage' => 'drawCurrentPage', // 当前页
            'totalPage' => 'drawTotalPage', // 总页数
            'perPage' => 'drawPerPage', // 每页数据条数
        ];

        return $this->fetch('view/lottery/lotteryMatch', $options);
    }

    /**
     * @desc 开奖管理----手动派奖
     * @auther BaoGaiJie
     * @date 2019-03-07
     */
    public function lotteryManual()
    {
        $options = [
            'title' => '手动派奖',
            'js' => [
                '/static/vp/lottery/lotteryManual.js',
            ],
            'nav' => [
                ['title' => '手动派奖', 'active' => true],
            ],
            'jsPage' => 'manualJsPage', // angular分页数据模型
            'currentPage' => 'manualCurrentPage', // 当前页
            'totalPage' => 'manualTotalPage', // 总页数
            'perPage' => 'manualPerPage', // 每页数据条数
        ];

        return $this->fetch('view/lottery/lotteryManual', $options);
    }

    /**
     * @desc 彩种管理----彩种列表
     * @auther BaoGaiJie
     * @date 2019-03-08
     */
    public function lottery()
    {
        $options = [
            'title' => '彩种列表',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/types/lottery.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/types/types.css',
            ],
            'nav' => [
                ['title' => '彩种列表', 'active' => true],
            ],
            'jsPage' => 'lotJsPage', // angular分页数据模型
            'currentPage' => 'lotCurrentPage', // 当前页
            'totalPage' => 'lotTotalPage', // 总页数
            'perPage' => 'lotPerPage', // 每页数据条数
        ];
        return $this->fetch('view/types/lottery', $options);
    }

    /**
     * @desc 彩种管理----竞彩足球
     * @auther BaoGaiJie
     * @date 2019-03-08
     */
    public function football()
    {
        $options = [
            'title' => '竞彩足球',
            'js' => [
                '/static/vp/types/football.js',
            ],
            'css' => [
                '/static/vp/types/types.css',
            ],
            'nav' => [
                ['title' => '竞彩足球', 'active' => true],
            ],
            'jsPage' => 'footJsPage', // angular分页数据模型
            'currentPage' => 'footCurrentPage', // 当前页
            'totalPage' => 'footTotalPage', // 总页数
            'perPage' => 'footPerPage', // 每页数据条数
        ];
        return $this->fetch('view/types/football', $options);
    }

    /**
     * @desc 彩种管理----竞彩篮球
     * @auther BaoGaiJie
     * @date 2019-03-08
     */
    public function basketball()
    {
        $options = [
            'title' => '竞彩篮球',
            'js' => [
                '/static/vp/types/basketball.js',
            ],
            'css' => [
                '/static/vp/types/types.css',
            ],
            'nav' => [
                ['title' => '竞彩篮球', 'active' => true],
            ],
            'jsPage' => 'basketJsPage', // angular分页数据模型
            'currentPage' => 'basketCurrentPage', // 当前页
            'totalPage' => 'basketTotalPage', // 总页数
            'perPage' => 'basketPerPage', // 每页数据条数
        ];
        return $this->fetch('view/types/basketball', $options);
    }

    /**
     * @desc 彩种管理----北京单场
     * @auther BaoGaiJie
     * @date 2019-03-08
     */
    public function beijing()
    {
        $options = [
            'title' => '北京单场',
            'js' => [
                '/static/vp/types/beijing.js',
            ],
            'css' => [
                '/static/vp/types/types.css',
            ],
            'nav' => [
                ['title' => '北京单场', 'active' => true],
            ],
            'jsPage' => 'beijingJsPage', // angular分页数据模型
            'currentPage' => 'beijingCurrentPage', // 当前页
            'totalPage' => 'beijingTotalPage', // 总页数
            'perPage' => 'beijingPerPage', // 每页数据条数
        ];
        return $this->fetch('view/types/beijing', $options);
    }

    /**
     * 节点管理
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function node()
    {
        $options = [
            'title' => '节点管理',
            'js' => [
                '/static/lib/jstree/jstree.min.js',
                '/static/vp/node/node.js',
            ],
            'css' => [
                '/static/lib/jstree/style.min.css',
                '/static/vp/node/node.css',
            ],
            'nav' => [
                ['title' => '节点管理', 'active' => true],
            ],
            'jsPage' => 'nodePage', // angular分页数据模型
            'currentPage' => 'nodeCurrentPage', // 当前页
            'totalPage' => 'nodeTotalPage', // 总页数
            'perPage' => 'nodePerPage', // 每页数据条数
        ];

        return $this->fetch('view/node/index', $options);
    }

    /**
     * 广告管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function adver()
    {
        $options = [
            'title' => '广告列表',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/adver/index.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/admin/admin.css',
            ],
            'nav' => [
                ['title' => '广告列表', 'active' => true],
            ],
            'jsPage' => 'adverPage', // angular分页数据模型
            'currentPage' => 'adverCurrentPage', // 当前页
            'totalPage' => 'adverTotalPage', // 总页数
            'perPage' => 'adverPerPage', // 每页数据条数
        ];
        return $this->fetch('view/adver/adver', $options);
    }

    /**
     * 广告回收站管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function adverRecycle()
    {
        $options = [
            'title' => '广告回收站',
            'js' => [
                '/static/vp/adver/recycle.js',
            ],
            'nav' => [
                ['title' => '广告管理', 'link' => '/vp/adver'],
                ['title' => '广告回收站', 'active' => true],
            ],
            'jsPage' => 'adverPage', // angular分页数据模型
            'currentPage' => 'adverCurrentPage', // 当前页
            'totalPage' => 'adverTotalPage', // 总页数
            'perPage' => 'adverPerPage', // 每页数据条数
        ];
        return $this->fetch('view/adver/recycle', $options);
    }

    /**
     * 广告类型管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function adverType()
    {
        $options = [
            'title' => '广告类型',
            'js' => [
                '/static/vp/adver/type.js',
            ],
            'nav' => [
                ['title' => '广告管理', 'link' => '/vp/adver'],
                ['title' => '广告类型', 'active' => true],
            ],
            'jsPage' => 'adverPage', // angular分页数据模型
            'currentPage' => 'adverCurrentPage', // 当前页
            'totalPage' => 'adverTotalPage', // 总页数
            'perPage' => 'adverPerPage', // 每页数据条数
        ];
        return $this->fetch('view/adver/adverType', $options);
    }

    /**
     * 新闻管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function news()
    {
        $options = [
            'title' => '新闻列表',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/news/news.js',
                '/static/vp/system/wangEditor.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/admin/admin.css',
                '/static/vp/news/index.css',
            ],
            'nav' => [
                ['title' => '新闻列表', 'active' => true],
            ],
            'jsPage' => 'newsPage', // angular分页数据模型
            'currentPage' => 'newsCurrentPage', // 当前页
            'totalPage' => 'newsTotalPage', // 总页数
            'perPage' => 'newsPerPage', // 每页数据条数
        ];
        return $this->fetch('view/news/news', $options);
    }

    /**
     * 新闻回收站管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function newsRecycle()
    {
        $options = [
            'title' => '新闻回收站',
            'js' => [
                '/static/vp/news/newRecycle.js',
            ],
            'nav' => [
                ['title' => '新闻管理', 'link' => '/vp/news'],
                ['title' => '新闻回收站', 'active' => true],
            ],
            'jsPage' => 'newRecyclePage', // angular分页数据模型
            'currentPage' => 'newRecycleCurrentPage', // 当前页
            'totalPage' => 'newRecycleTotalPage', // 总页数
            'perPage' => 'newRecyclePerPage', // 每页数据条数
        ];
        return $this->fetch('view/news/recycle', $options);
    }

    /**
     * 新闻类型管理
     * Author jimadela
     * Github https://github.com/JimAdela
     * Blog https://jimadela.github.io/
     */
    public function newsType()
    {
        $options = [
            'title' => '新闻类型',
            'js' => [
                '/static/vp/news/type.js',
            ],
            'nav' => [
                ['title' => '新闻管理', 'link' => '/vp/news'],
                ['title' => '新闻类型', 'active' => true],
            ],
            'jsPage' => 'typePage', // angular分页数据模型
            'currentPage' => 'typeCurrentPage', // 当前页
            'totalPage' => 'typeTotalPage', // 总页数
            'perPage' => 'typePerPage', // 每页数据条数
        ];
        return $this->fetch('view/news/newsType', $options);
    }

    /**
     * 场馆管理
     */
    public function stadium()
    {
        $options = [
            'title' => '场馆列表',
            'js' => [
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/stadium/stadium.js',
                '/static/vp/system/wangEditor.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/admin/admin.css',
                '/static/vp/stadium/index.css',
            ],
            'nav' => [
                ['title' => '场馆列表', 'active' => true],
            ],
            'jsPage' => 'stadiumPage', // angular分页数据模型
            'currentPage' => 'stadiumCurrentPage', // 当前页
            'totalPage' => 'stadiumTotalPage', // 总页数
            'perPage' => 'stadiumPerPage', // 每页数据条数
        ];
        return $this->fetch('view/stadium/stadium', $options);
    }

    /**
     * 场馆回收站管理
     */
    public function stadiumRecycle()
    {
        $options = [
            'title' => '场馆回收站',
            'js' => [
                '/static/vp/stadium/stadiumRecycle.js',
            ],
            'nav' => [
                ['title' => '场馆管理', 'link' => '/vp/stadium'],
                ['title' => '场馆回收站', 'active' => true],
            ],
            'jsPage' => 'stadiumRecyclePage', // angular分页数据模型
            'currentPage' => 'stadiumRecycleCurrentPage', // 当前页
            'totalPage' => 'stadiumRecycleTotalPage', // 总页数
            'perPage' => 'stadiumRecyclePerPage', // 每页数据条数
        ];
        return $this->fetch('view/stadium/recycle', $options);
    }

    /**
     * 场馆类型管理
     */
    public function stadiumType()
    {
        $options = [
            'title' => '场馆类型',
            'js' => [
                '/static/vp/stadium/type.js',
            ],
            'nav' => [
                ['title' => '场馆管理', 'link' => '/vp/stadium'],
                ['title' => '场馆类型', 'active' => true],
            ],
            'jsPage' => 'typePage', // angular分页数据模型
            'currentPage' => 'typeCurrentPage', // 当前页
            'totalPage' => 'typeTotalPage', // 总页数
            'perPage' => 'typePerPage', // 每页数据条数
        ];
        return $this->fetch('view/stadium/stadiumType', $options);
    }

    /**
     * @desc 站点配置
     * @auther evshan
     * @date 2019-03-08
     */
    public function siteConfig()
    {
        $options = [
            'title' => '站点配置',
            'js' => [
                '/static/vp/system/siteConfig.js',
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
                '/static/vp/system/wangEditor.js',
            ],
            'css' => [
                '/static/vp/system/siteConfig.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/adver/index.css',
                '/static/lib/jquery.filer/css/jquery.filer.css',
            ],
            'nav' => [
                ['title' => '站点配置', 'active' => true],
            ],
        ];

        return $this->fetch('view/system/siteConfigure', $options);
    }

    /** 客服管理
     * @auther evshan
     * @date 2019-03-09
     * **/
    public function siteService()
    {
        $options = [
            'title' => '客服管理',
            'js' => [
                '/static/vp/system/siteService.js',
                '/static/lib/jquery.filer/js/jquery.filer.min.js',
            ],
            'css' => [
                '/static/lib/jquery.filer/css/jquery.filer.css',
//              '/static/vp/system/siteService.css',
                '/static/lib/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css',
                '/static/vp/adver/index.css',
            ],
            'nav' => [
                ['title' => '客服管理', 'active' => true],
            ],
            'jsPage' => 'systemPage', // angular分页数据模型
            'currentPage' => 'systemCurrentPage', // 当前页
            'totalPage' => 'systemTotalPage', // 总页数
            'perPage' => 'systemPerPage', // 每页数据条数
        ];

        return $this->fetch('view/system/siteService', $options);
    }

    //短信记录
    public function smsLog()
    {
        $options = [
            'title' => '短信记录',
            'js' => [
                '/static/vp/system/smsLog.js',
            ],
            'nav' => [
                ['title' => '短信记录', 'active' => true],
            ],
            'jsPage' => 'smsPage', // angular分页数据模型
            'currentPage' => 'smsCurrentPage', // 当前页
            'totalPage' => 'smsTotalPage', // 总页数
            'perPage' => 'smsPerPage', // 每页数据条数
        ];
        return $this->fetch('view/system/smsLog', $options);
    }

    //短信记录
    public function systemLog()
    {
        $options = [
            'title' => '系统日志',
            'js' => [
                '/static/vp/system/systemLog.js',
            ],
            'nav' => [
                ['title' => '系统日志', 'active' => true],
            ],
            'jsPage' => 'systemLogPage', // angular分页数据模型
            'currentPage' => 'systemLogCurrentPage', // 当前页
            'totalPage' => 'systemLogTotalPage', // 总页数
            'perPage' => 'systemLogPerPage', // 每页数据条数
        ];
        return $this->fetch('view/system/systemLog', $options);
    }

    /**
     * 彩种爬取日志
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function czLog()
    {
        $options = [
            'title' => '彩种爬取日志',
            'js' => [
                '/static/vp/patch/patch.js',
            ],
            'nav' => [
                ['title' => '彩种爬取日志', 'active' => true],
            ],
            'jsPage' => 'patchJsPage', // angular分页数据模型
            'currentPage' => 'patchCurrentPage', // 当前页
            'totalPage' => 'patchTotalPage', // 总页数
            'perPage' => 'patchPerPage', // 每页数据条数
        ];

        return $this->fetch('view/patch/index', $options);
    }

    /**
     * 后台消息列表
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function msg()
    {
        $options = [
            'title' => '消息',
            'js' => [
                '/static/vp/msg/msg.js',
            ],
            'nav' => [
                ['title' => '消息列表', 'active' => true],
            ],
            'jsPage' => 'msgJsPage', // angular分页数据模型
            'currentPage' => 'msgCurrentPage', // 当前页
            'totalPage' => 'msgTotalPage', // 总页数
            'perPage' => 'msgPerPage', // 每页数据条数
        ];

        return $this->fetch('view/msg/index', $options);
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
                '/static/vp/recharge/recharge.js',
            ],
            'nav' => [
                ['title' => '代充值', 'active' => true],
            ],
        ];

        return $this->fetch('view/recharge/index', $options);
    }

    /**
     * 代提现
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function agentWithdraw()
    {
        $options = [
            'title' => '代提现',
            'js' => [
                '/static/vp/withdraw/withdraw.js',
            ],
            'nav' => [
                ['title' => '代提现', 'active' => true],
            ],
        ];

        return $this->fetch('view/withdraw/index', $options);
    }

    /**
     * 数字彩开奖
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function numDraw()
    {
        $options = [
            'title' => '数字彩开奖',
            'js' => [
                '/static/vp/lottery/numDraw.js',
            ],
            'nav' => [
                ['title' => '数字彩开奖', 'active' => true],
            ],
            'jsPage' => 'numLotteryJsPage', // angular分页数据模型
            'currentPage' => 'numLotteryCurrentPage', // 当前页
            'totalPage' => 'numLotteryTotalPage', // 总页数
            'perPage' => 'numLotteryPerPage', // 每页数据条数
        ];

        return $this->fetch('view/lottery/numDraw', $options);
    }

    /**
     * 风险控制
     *
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function risk()
    {
        $options = [
            'title' => '风险控制',
            'js' => [
                '/static/lib/lc-switch/lc_switch.min.js',
                '/static/vp/risk/risk.js',
            ],
            'css' => [
                '/static/lib/lc-switch/lc_switch.css',
            ],
            'nav' => [
                ['title' => '风险控制', 'active' => true],
            ],
            'jsPage' => 'riskJsPage', // angular分页数据模型
            'currentPage' => 'riskCurrentPage', // 当前页
            'totalPage' => 'riskTotalPage', // 总页数
            'perPage' => 'riskPerPage', // 每页数据条数
        ];

        return $this->fetch('view/risk/index', $options);
    }
}
