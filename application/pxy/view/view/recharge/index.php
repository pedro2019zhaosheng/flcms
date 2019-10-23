<div class="container-fluid pt-3" ng-controller="rechargeCtrl">
    <nav class="navbar navbar-light bg-light" style="padding-bottom: 300px;">
        <div class="card-body w-100 pt-5 mt-5">
            <h3 class="m-auto text-center w-100 pt-5">
                <span class="text-danger">代</span>
                <small class="text-primary">充值</small>
            </h3>
            <style rel="stylesheet" type="text/css">
                #username::placeholder{color: #bbbbbb !important;font-size: 15px !important;}
                #clearInput{position: absolute;top: .42rem;right: .8rem;font-size: 25px;color: #bbbbbb;cursor: pointer;}
            </style>
            <form class="form-inline mt-3 pt-3">
                <div class="form-group offset-md-4 col-md-4 pr-1 position-relative">
                    <label for="username" class="sr-only">关键词</label>
                    <input type="text" class="form-control col-md-12" ng-model="searchUser" id="username" autocomplete="off" placeholder="请输入代理商账号、会员账号">
                    <i id="clearInput" ng-click="clearInput()" class="fa fa-times-circle" aria-hidden="true"></i>
                    <code class="mt-2"><small>请使用鼠标点击搜索或按键盘`Enter`键进行查询</small></code>
                </div>
                <button type="submit" class="btn btn-success mb-4" ng-click="searchMember()">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </nav>
    <!--充值模态框-->
    {include file="view/recharge/recharge" /}
</div>