<!-- Modal -->
<div class="modal" id="beijingMatch" role="dialog" aria-labelledby="footballMatchModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    赛事查看
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid text-center mb-3">
                    <small><b ng-bind="detailMatchName | default : '无' "></b></small>
                    <span class="text-secondary">
                        :
                        <small ng-bind="detailHost | default : '无' "></small>
                        <em class="text-danger"><b>VS</b></em>
                        <small ng-bind="detailGuest | default : '无' "></small>
                    </span>
                </div>
                <!-- 北京单场start -->
                <div class="card noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            (让球)胜平负奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0">
                                            <table class="table table-bordered table-sm text-center">
                                                <thead>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>胜</b></small></th>
                                                    <th><small><b>平</b></small></th>
                                                    <th><small><b>负</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in spSpfVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.W | default : '无'"></small></td>
                                                    <td><small ng-bind="item.D | default : '无'"></small></td>
                                                    <td><small ng-bind="item.L | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="spSpfVarIndex.length === 0">
                                                <span><small>暂无数据</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="spSpfNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <!--<caption>胜平负过关固定奖金(以下所有玩法，均以票面显示奖金为准)</caption>-->
                            <thead>
                            <tr>
                                <th>让球</th>
                                <th>胜</th>
                                <th>平</th>
                                <th>负</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spSpfNewest.H | default: '无'"></td>
                                <td ng-bind="spSpfNewest.W | default : '无'"></td>
                                <td ng-bind="spSpfNewest.D | default : '无'"></td>
                                <td ng-bind="spSpfNewest.L | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            胜负过关奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0">
                                            <table class="table table-bordered table-sm text-center">
                                                <thead>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>让球</b></small></th>
                                                    <th><small><b>胜</b></small></th>
                                                    <th><small><b>负</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in rqspfVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.H | default : '无'"></small></td>
                                                    <td><small ng-bind="item.W | default : '无'"></small></td>
                                                    <td><small ng-bind="item.L | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="rqspfVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="rqspfNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>让球</th>
                                    <th>胜</th>
                                    <th>负</th>
                                    <th>胜负过关顺序编号</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="rqspfNewest.H | default : '无'"></td>
                                <td ng-bind="rqspfNewest.W | default : '无'"></td>
                                <td ng-bind="rqspfNewest.L | default : '无'"></td>
                                <td ng-bind="rqspfNewest.num | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            总进球奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0">
                                            <table class="table table-bordered table-sm text-center">
                                                <thead>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>0</b></small></th>
                                                    <th><small><b>1</b></small></th>
                                                    <th><small><b>2</b></small></th>
                                                    <th><small><b>3</b></small></th>
                                                    <th><small><b>4</b></small></th>
                                                    <th><small><b>5</b></small></th>
                                                    <th><small><b>6</b></small></th>
                                                    <th><small><b>7+</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in jqsVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat.zero | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.one | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.two | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.three | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.four | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.five | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.six | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.seven | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="jqsVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="jqsNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>0</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="jqsNewest.dat.zero | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.one | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.two | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.three | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.four | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.five | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.six | default : '无'"></td>
                                <td ng-bind="jqsNewest.dat.seven | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            半全场胜平负奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0">
                                            <table class="table table-bordered table-sm text-center">
                                                <thead>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>胜胜</b></small></th>
                                                    <th><small><b>胜平</b></small></th>
                                                    <th><small><b>胜负</b></small></th>
                                                    <th><small><b>平胜</b></small></th>
                                                    <th><small><b>平平</b></small></th>
                                                    <th><small><b>平负</b></small></th>
                                                    <th><small><b>负胜</b></small></th>
                                                    <th><small><b>负平</b></small></th>
                                                    <th><small><b>负负</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in bqcVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat.ww | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.wd | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.wl | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.dw | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.dd | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.dl | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.lw | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.ld | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.ll | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="bqcVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="bqcNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>胜胜</th>
                                <th>胜平</th>
                                <th>胜负</th>
                                <th>平胜</th>
                                <th>平平</th>
                                <th>平负</th>
                                <th>负胜</th>
                                <th>负平</th>
                                <th>负负</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="bqcNewest.dat.ww | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.wd | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.wl | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.dw | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.dd | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.dl | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.lw | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.ld | default : '无'"></td>
                                <td ng-bind="bqcNewest.dat.ll | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            上下盘单双数奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0">
                                            <table class="table table-bordered table-sm text-center">
                                                <thead>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>上单</b></small></th>
                                                    <th><small><b>上双</b></small></th>
                                                    <th><small><b>下单</b></small></th>
                                                    <th><small><b>下双</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in sxpVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat.us | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.ud | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.ds | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.dd | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="sxpVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="sxpNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>上单</th>
                                <th>上双</th>
                                <th>下单</th>
                                <th>下双</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="sxpNewest.dat.us | default : '无'"></td>
                                <td ng-bind="sxpNewest.dat.ud | default : '无'"></td>
                                <td ng-bind="sxpNewest.dat.ds | default : '无'"></td>
                                <td ng-bind="sxpNewest.dat.dd | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            全场比分奖金指数
                            <div class="dropright d-sm-inline-block">
                                <small data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    【<span class="text-success cp text-underline">查看奖金指数变化</span>】
                                </small>
                                <div class="dropdown-menu cart-left">
                                    <div class="card pb-3">
                                        <div class="card-header pt-1 px-3 pb-1">
                                            <small>竞彩奖金指数变化</small>
                                        </div>
                                        <div class="card-body pt-1 px-3 pb-0" style="height: 300px !important; overflow-y: auto !important;">
                                            <table class="table table-bordered table-sm text-center" ng-repeat="item in bfVarIndex">
                                                <thead>
                                                <tr>
                                                    <th colspan="13"><small><b>胜</b></small></th>
                                                </tr>
                                                <tr>
                                                    <td><small><b>日期</b></small></td>
                                                    <th><small><b>1:0</b></small></th>
                                                    <th><small><b>2:0</b></small></th>
                                                    <th><small><b>2:1</b></small></th>
                                                    <th><small><b>3:0</b></small></th>
                                                    <th><small><b>3:1</b></small></th>
                                                    <th><small><b>3:2</b></small></th>
                                                    <th><small><b>4:0</b></small></th>
                                                    <th><small><b>4:1</b></small></th>
                                                    <th><small><b>4:2</b></small></th>
                                                    <th><small><b>5:0</b></small></th>
                                                    <th><small><b>5:1</b></small></th>
                                                    <th><small><b>5:2</b></small></th>
                                                    <th><small><b>胜其他</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat['0100'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0200'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0201'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0300'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0301'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0302'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0400'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0401'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0402'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0500'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0501'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0502'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['-1-a'] | default : '无'"></small></td>
                                                </tr>
                                                </tbody>

                                                <thead>
                                                <tr>
                                                    <th colspan="5"><small><b>平</b></small></th>
                                                </tr>
                                                <tr>
                                                    <td><small><b>日期</b></small></td>
                                                    <th><small><b>0:0</b></small></th>
                                                    <th><small><b>1:1</b></small></th>
                                                    <th><small><b>2:2</b></small></th>
                                                    <th><small><b>3:3</b></small></th>
                                                    <th><small><b>平其他</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat['0000'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0101'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0202'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0303'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['-1-d'] | default : '无'"></small></td>
                                                </tr>
                                                </tbody>

                                                <thead>
                                                <tr>
                                                    <th colspan="13"><small><b>负</b></small></th>
                                                </tr>
                                                <tr>
                                                    <td><small><b>日期</b></small></td>
                                                    <th><small><b>0:1</b></small></th>
                                                    <th><small><b>0:2</b></small></th>
                                                    <th><small><b>1:2</b></small></th>
                                                    <th><small><b>0:3</b></small></th>
                                                    <th><small><b>1:3</b></small></th>
                                                    <th><small><b>2:3</b></small></th>
                                                    <th><small><b>0:4</b></small></th>
                                                    <th><small><b>1:4</b></small></th>
                                                    <th><small><b>2:4</b></small></th>
                                                    <th><small><b>0:5</b></small></th>
                                                    <th><small><b>1:5</b></small></th>
                                                    <th><small><b>2:5</b></small></th>
                                                    <th><small><b>负其他</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat['0001'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0002'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0102'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0003'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0103'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0203'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0004'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0104'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0204'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0005'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0105'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['0205'] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat['-1-h'] | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="bfVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="bfNewest.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th colspan="13">胜</th>
                            </tr>
                            <tr>
                                <th>1:0</th>
                                <th>2:0</th>
                                <th>2:1</th>
                                <th>3:0</th>
                                <th>3:1</th>
                                <th>3:2</th>
                                <th>4:0</th>
                                <th>4:1</th>
                                <th>4:2</th>
                                <th>5:0</th>
                                <th>5:1</th>
                                <th>5:2</th>
                                <th>胜其他</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="bfNewest.dat['0100'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0200'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0201'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0300'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0301'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0302'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0400'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0401'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0402'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0500'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0501'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0502'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['-1-a'] | default : '无'"></td>
                            </tr>
                            </tbody>
                            <thead>
                            <tr>
                                <th colspan="5">平</th>
                            </tr>
                            <tr>
                                <th>0:0</th>
                                <th>1:1</th>
                                <th>2:2</th>
                                <th>3:3</th>
                                <th>平其他</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="bfNewest.dat['0000'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0101'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0202'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0303'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['-1-d'] | default : '无'"></td>
                            </tr>
                            </tbody>
                            <thead>
                            <tr>
                                <th colspan="13">负</th>
                            </tr>
                            <tr>
                                <th>0:1</th>
                                <th>0:2</th>
                                <th>1:2</th>
                                <th>0:3</th>
                                <th>1:3</th>
                                <th>2:3</th>
                                <th>0:4</th>
                                <th>1:4</th>
                                <th>2:4</th>
                                <th>0:5</th>
                                <th>1:5</th>
                                <th>2:5</th>
                                <th>负其他</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="bfNewest.dat['0001'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0002'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0102'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0003'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0103'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0203'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0004'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0104'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0204'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0005'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0105'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['0205'] | default : '无'"></td>
                                <td ng-bind="bfNewest.dat['-1-h'] | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

