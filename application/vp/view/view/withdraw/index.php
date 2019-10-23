<div class="container-fluid pt-3" ng-controller="withdrawCtrl">
    <nav class="navbar bg-light" style="height: 70vh;">
        <div class="card-body w-100">
            <h4 class="m-auto text-center text-warning w-100">
                <b>代</b>
                <small>提现</small>
            </h4>
            <style rel="stylesheet" type="text/css">
                #username::placeholder{color: #cccccc !important; font-size: 13px !important;}
                #clearInput{position: absolute; top: -.6rem; right: .8rem; font-size: 2.1rem; color: #ced4da; cursor: pointer;}
            </style>
            <form class="form-inline mt-4">
                <div class="form-group offset-md-4 col-md-4 pr-1 position-relative">
                    <label for="username" class="sr-only">关键词</label>
                    <input type="text" class="form-control col-md-12" ng-model="searchUser" id="username" autocomplete="off" placeholder="请输入代理商账号、会员账号">
                    <i id="clearInput" ng-click="clearInput()" aria-hidden="true">&times;</i>
                    <code class="mt-2"><small>请使用鼠标点击搜索或按键盘`Enter`键进行查询</small></code>
                </div>
                <button type="submit" class="btn btn-warning mb-4" ng-click="searchMember()">
                    <i class="fa fa-search fa-fw" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </nav>
    <!--充值模态框-->
    {include file="view/withdraw/withdraw" /}
</div>