<!-- Modal -->
<div class="modal" id="num-edit" role="dialog" aria-hidden="true">
    <style type="text/css">
        /* 修改日历插件z-index值 */
        .daterangepicker.dropdown-menu {
            z-index: 1050 !important;
        }
    </style>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">编辑开奖号码</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group" ng-show="!alreadyDraw">
                            <label for="nextOpenDate"><sup style="color: red;">*</sup>下一期开奖时间:</label>
                            <code>(注: 排三,排五,幸运飞艇 开奖时间可参考历史开奖时间做推算)</code>
                            <input type="text" name="nextOpenDate" id="nextOpenDate" class="form-control form-control-sm mb-3" placeholder="下一期开奖时间精确到秒,禁止乱填" autocomplete="off">
                            <label for="nextOpenNumber"><sup style="color: red;">*</sup>下一期期号:</label>
                            <code>(注: 排三,排五期号规则,每期期号+1,阳历1月1日做重置. 例如: 2020年1月1日,期号为20001)</code>
                            <input type="text" name="nextOpenNumber" id="nextOpenNumber" class="form-control form-control-sm" placeholder="下一期期号,禁止乱填" autocomplete="off">
                        </div>

                        <label><sup style="color: red;">*</sup>当前期开奖结果:</label>
                        <div id="editModalForm" class="form-inline form-group">
                            <input type="text" class="col mr-3 text-center" ng-value="item" ng-repeat="item in openCode track by $index">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="editSubmit(numId)">确认修改</button>
            </div>
        </div>
    </div>
</div>
