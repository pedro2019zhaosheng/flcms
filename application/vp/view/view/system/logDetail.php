<!-- Modal -->
<div class="modal" id="log-detail" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">日志信息</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre><code ng-bind="logInfo"></code></pre>
                {include file="public/nodata" nodata="logInfoNoData"/}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
