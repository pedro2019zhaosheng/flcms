<div class="container-fluid" ng-controller="riskCtrl">
    <div class="card">
        <div class="card-body">
            <h6>自动风控列表</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action" ng-repeat="item in riskConfigList" on-finish-render="ngRepeatFinished">
                    <label for="{{ item.var }}" class="text-purple" ng-bind="item.name + '&nbsp;:&nbsp;'"></label>
                    <input id="{{ item.var }}" type="checkbox" hidden name="switch" data-sign="{{ item.var }}" ng-checked="item.value == 1 ? true : false" class="autoRisk">
                </li>
            </ul>
        </div>
        <div class="card-body border-top">
            <h6>
                手动风控列表
                <small class="text-danger">( 注: 手动风控优先于自动风控 )</small>
            </h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action" ng-repeat="item in riskHandList" on-finish-render="ngRepeatFinished2">
                    <label class="text-purple" for="{{ 'handSelect' + $index }}" ng-bind="item.name + '&nbsp;:&nbsp;'"></label>
                    <select class="handSelect" id="{{ 'handSelect' + $index }}">
                        <option value="{{ son.expect }}"
                                ng-repeat="son in item.expects"
                                ng-bind="'期号: ' + son.expect + '&emsp;开奖时间: ' + son.open_time">
                        </option>
                    </select>
                    <label class="sr-only" for="{{ 'openCode' + $index }}"></label>
                    <input type="number" class="pb-1 mx-3 col-2" id="{{ 'openCode' + $index }}" placeholder="请输入开奖号码">
                    <button type="button" class="btn btn-danger btn-sm mr-1" ng-click="clear($event)">清空</button>
                    <button type="button" class="btn btn-primary btn-sm" ng-click="submit($event, item.ctype)">确定</button>
                </li>
            </ul>
        </div>
        <div class="card-body border-top">
            <h6>开奖号码预设列表</h6>
            <table class="table table-bordered table-hover text-center">
                <thead>
                <tr>
                    <td>序号</td>
                    <th>彩种</th>
                    <th>期号</th>
                    <th>开奖号码(预设)</th>
                    <th>创建时间</th>
                    <th>修改时间</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="item in riskList">
                    <td ng-bind="(riskCurrentPage - 1) * riskPerPage + $index + 1"></td>
                    <td ng-bind="item.name | default : '无'"></td>
                    <td ng-bind="item.number | default : '无'"></td>
                    <td>
                        <span class="badge badge-light define-badge-red"
                              ng-repeat="son in item.open_code track by $index"
                              ng-bind="son">
                        </span>
                    </td>
                    <td ng-bind="item.create_time | default : '无'"></td>
                    <td ng-bind="item.update_time | default : '无'"></td>
                    <td>
                        <span class="badge badge-secondary" ng-if="item.status === 0">未使用</span>
                        <span class="badge badge-success" ng-if="item.status === 1">已使用</span>
                    </td>
                </tr>
                </tbody>
            </table>
            {include file="public/nodata" nodata="riskNoData"/}
            {include file="public/page" /}
        </div>
    </div>
</div>