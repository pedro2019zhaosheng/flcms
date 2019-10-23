<!-- Modal -->
<div class="modal" id="draw-detail" role="dialog" aria-labelledby="drawDetailModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">竞彩赛果</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid ml-1">
                    <b>{{ detailLeagueName | default : '无' }}&nbsp;:&nbsp;
                        <small ng-bind="detailHostName | default : '无'"></small>
                        <em class="text-danger">VS</em>
                        <small ng-bind="detailGuestName | default : '无'"></small>
                    </b>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered text-center">
                        <thead>
                        <tr>
                            <th ng-if="lotteryZcCode">胜平负</th>
                            <th ng-if="lotteryZcCode">让球胜平负</th>
                            <th ng-if="lotteryZcCode">总进球数</th>
                            <th ng-if="lotteryZcCode">半全场胜平负</th>
                            <th ng-if="lotteryZcCode">全场比分</th>
                            <th ng-if="lotteryLcCode">胜负</th>
                            <th ng-if="lotteryLcCode">让分胜负</th>
                            <th ng-if="lotteryLcCode">主胜负差</th>
                            <th ng-if="lotteryLcCode">客胜负差</th>
                            <th ng-if="lotteryLcCode">大小分</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td ng-if="lotteryZcCode" class="text-primary" ng-bind="reSpf"></td>
                            <td ng-if="lotteryZcCode" class="text-primary" ng-bind="reRqspf"></td>
                            <td ng-if="lotteryZcCode" class="text-primary" ng-bind="reJqs"></td>
                            <td ng-if="lotteryZcCode" class="text-primary" ng-bind="reBqc"></td>
                            <td ng-if="lotteryZcCode" class="text-primary" ng-bind="reQcbf"></td>
                            <td ng-if="lotteryLcCode" class="text-primary" ng-bind="sf"></td>
                            <td ng-if="lotteryLcCode" class="text-primary" ng-bind="rfsf"></td>
                            <td ng-if="lotteryLcCode" class="text-primary" ng-bind="zsfc"></td>
                            <td ng-if="lotteryLcCode" class="text-primary" ng-bind="ksfc"></td>
                            <td ng-if="lotteryLcCode" class="text-primary" ng-bind="dxf"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
