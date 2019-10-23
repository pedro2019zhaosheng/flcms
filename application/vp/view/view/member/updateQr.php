<!-- Modal -->
<div class="modal" id="update-qr" role="dialog" aria-labelledby="update-qr" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">更新邀请码</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-tip" class="card-body">
                    <h6>使用场景:</h6>
                    <p class="text-secondary">平台域名更换后,会员二维码失效! 该操作用于更新全部会员二维码.</p>
                    <code>(注: 会员量越大该操作越耗时, 请您耐心等待...)</code>
                </div>
                <iframe scrolling="yes" id="qr-toolbar" style="display: none;" frameborder="0" width="100%" height="100%"></iframe>
            </div>
            <div class="modal-footer">
                <span class="text-secondary"><code>注: 更新中请勿关闭该窗口!</code></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button id="qrSubmit" type="button" class="btn btn-primary btn-sm" ng-click="upAllQRcode()">确认并立即更新</button>
            </div>
        </div>
    </div>
</div>
