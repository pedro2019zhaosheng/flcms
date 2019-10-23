<!-- Modal -->
<div class="modal" id="num-tag" role="dialog" aria-hidden="true">
    <style type="text/css">
        /* 修改日历插件z-index值 */
        .daterangepicker.dropdown-menu {
            z-index: 1050 !important;
        }
    </style>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">编辑期号日期</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="tagOpenDate"><sup style="color: red;">*</sup>开奖时间:</label>
                            <code>(注: 禁止乱填)</code>
                            <input type="text" class="form-control form-control-sm" id="tagOpenDate" placeholder="开奖日期" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="tagNum"><sup style="color: red;">*</sup>期号:</label>
                            <code>(注: 禁止乱填)</code>
                            <input type="text" ng-model="originExpect" class="form-control form-control-sm" id="tagNum" placeholder="期号" autocomplete="off">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="numDataSubmit(numTwoId)">确认修改</button>
            </div>
        </div>
    </div>
</div>
