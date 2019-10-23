<div class="container-fluid" ng-controller="homeCtrl">
    <div class="row pt-3 pb-3">
        <div class="col-sm-12 col-lg5-avg">
            <div class="card-box noradius noborder bg-danger shade">
                <i class="fa fa-user-o float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">会员总数</h6>
                <h3 class="m-b-20 text-white counter" ng-bind="counterData.memberTotalCount | default : 0"></h3>
                <span class="text-white">今日新增: <b ng-bind="counterData.memberTodayCount | default : 0"></b></span>
            </div>
        </div>

        <div class="col-sm-12 col-lg5-avg">
            <div class="card-box noradius noborder bg-default shade">
                <i class="fa fa-file-text-o float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">投注总额</h6>
                <h3 class="m-b-20 text-white counter" ng-bind="counterData.betTotalCount | default : 0"></h3>
                <span class="text-white">今日投注: <b ng-bind="counterData.betTodayCount | default : 0"></b></span>
            </div>
        </div>

        <div class="col-sm-12 col-lg5-avg">
            <div class="card-box noradius noborder bg-warning shade">
                <i class="fa fa-paypal float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">累计充值</h6>
                <h3 class="m-b-20 text-white counter" ng-bind="counterData.rechargeTotalCount | default : 0 | currency : '' : 0"></h3>
                <span class="text-white">今日充值: <b ng-bind="counterData.rechargeTodayCount | default : 0 | currency : '' : 0"></b></span>
            </div>
        </div>

        <div class="col-sm-12 col-lg5-avg">
            <div class="card-box noradius noborder bg-info shade">
                <i class="fa fa-reply-all float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">累计提现</h6>
                <h3 class="m-b-20 text-white counter" ng-bind="counterData.withdrawTotalCount | default : 0 | currency : '' : 0"></h3>
                <span class="text-white">今日提现: <b ng-bind="counterData.withdrawTodayCount | default : 0 | currency : '' : 0"></b></span>
            </div>
        </div>

        <div class="col-sm-12 col-lg5-avg">
            <div class="card-box noradius noborder bg-danger shade">
                <i class="fa fa-cny float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">现客户余额</h6>
                <h3 class="m-b-20 text-white counter" ng-bind="counterData.customTotalBalance | default : 0 | currency : '' : 0"></h3>
                <span class="text-white">&nbsp;</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
            <div class="card mb-3 shade">
                <div class="card-header">
                    <i class="fa fa-table"></i>
                    近十五天注单走势
                </div>

                <div class="card-body">
                    <canvas id="comboBarLineChartOrder"></canvas>
                </div>
                <div class="card-footer small text-muted">数据截止日期</div>
            </div><!-- end card-->
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <div class="card mb-3 shade">
                <div class="card-header">
                    <i class="fa fa-table"></i>
                    近十五天入金走势
                </div>

                <div class="card-body">
                    <canvas id="comboBarLineChartRecharge"></canvas>
                </div>
                <div class="card-footer small text-muted">数据截止日期</div>
            </div><!-- end card-->
        </div>
    </div>
</div>
<!-- END container-fluid -->
