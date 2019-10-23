<!-- Modal -->
<div class="modal" id="basketballMatch" role="dialog" aria-hidden="true">
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
                            胜负奖金指数
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
                                                    <th><small><b>负</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in spSfVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.W | default : '无'"></small></td>
                                                    <td><small ng-bind="item.L | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="spSfVarIndex.length === 0">
                                                <span><small>暂无数据</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="spSf.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>胜</th>
                                <th>负</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spSf.W | default : '无'"></td>
                                <td ng-bind="spSf.L | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            让分胜负奖金指数
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
                                                    <th><small><b>让分</b></small></th>
                                                    <th><small><b>胜</b></small></th>
                                                    <th><small><b>负</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in spRfsfVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.H | default : '无'"></small></td>
                                                    <td><small ng-bind="item.W | default : '无'"></small></td>
                                                    <td><small ng-bind="item.L | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="spRfsfVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="spRfsf.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>让分</th>
                                <th>胜</th>
                                <th>负</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spRfsf.H | default : '无'"></td>
                                <td ng-bind="spRfsf.W | default : '无'"></td>
                                <td ng-bind="spRfsf.L | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            胜分差奖金指数
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
                                            <table class="table table-bordered table-sm text-center" ng-repeat="item in spSfcVarIndex">
                                                <thead>
                                                <tr>
                                                    <th colspan="7"><small><b>主胜</b></small></th>
                                                </tr>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>1-5</b></small></th>
                                                    <th><small><b>6-10</b></small></th>
                                                    <th><small><b>11-15</b></small></th>
                                                    <th><small><b>16-20</b></small></th>
                                                    <th><small><b>21-25</b></small></th>
                                                    <th><small><b>26+</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat.home[0] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.home[1] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.home[2] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.home[3] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.home[4] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.home[5] | default : '无'"></small></td>
                                                </tr>
                                                </tbody>

                                                <thead>
                                                <tr>
                                                    <th colspan="7"><small><b>客胜</b></small></th>
                                                </tr>
                                                <tr>
                                                    <th><small><b>日期</b></small></th>
                                                    <th><small><b>1-5</b></small></th>
                                                    <th><small><b>6-10</b></small></th>
                                                    <th><small><b>11-15</b></small></th>
                                                    <th><small><b>16-20</b></small></th>
                                                    <th><small><b>21-25</b></small></th>
                                                    <th><small><b>26+</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.dat.away[0] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.away[1] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.away[2] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.away[3] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.away[4] | default : '无'"></small></td>
                                                    <td><small ng-bind="item.dat.away[5] | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="spSfcVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="spSfc.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th colspan="6">主胜</th>
                            </tr>
                            <tr>
                                <th>1-5</th>
                                <th>6-10</th>
                                <th>11-15</th>
                                <th>16-20</th>
                                <th>21-25</th>
                                <th>26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spSfc.dat.home[0] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.home[1] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.home[2] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.home[3] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.home[4] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.home[5] | default : '无'"></td>
                            </tr>
                            </tbody>

                            <thead>
                            <tr>
                                <th colspan="6">客胜</th>
                            </tr>
                            <tr>
                                <th>1-5</th>
                                <th>6-10</th>
                                <th>11-15</th>
                                <th>16-20</th>
                                <th>21-25</th>
                                <th>26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spSfc.dat.away[0] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.away[1] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.away[2] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.away[3] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.away[4] | default : '无'"></td>
                                <td ng-bind="spSfc.dat.away[5] | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 noshadow">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            大小分奖金指数
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
                                                    <th><small><b>总分</b></small></th>
                                                    <th><small><b>大分</b></small></th>
                                                    <th><small><b>小分</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="item in spDxfVarIndex">
                                                    <td><small ng-bind="item.ut | toDate : 'MM/DD HH:mm'"></small></td>
                                                    <td><small ng-bind="item.T | default : '无'"></small></td>
                                                    <td><small ng-bind="item.H | default : '无'"></small></td>
                                                    <td><small ng-bind="item.L | default : '无'"></small></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center pb-3" ng-if="spDxfVarIndex.length === 0">
                                                <small>暂无数据</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="float-right">发布日期&nbsp;:&nbsp;<span ng-bind="spDxf.ut | toDate : 'YYYY/MM/DD HH:mm'"></span></small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>总分</th>
                                <th>大分</th>
                                <th>小分</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td ng-bind="spDxf.T | default : '无'"></td>
                                <td ng-bind="spDxf.H | default : '无'"></td>
                                <td ng-bind="spDxf.L | default : '无'"></td>
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