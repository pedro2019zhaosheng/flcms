<!-- Modal -->
<div class="modal" id="node-update" role="dialog" aria-labelledby="updateNodeModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改节点</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="belongNodeUpdate" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>父节点</label>
                            <div class="col-sm-10">
                                <input type="hidden" id="curMenuId">
                                <input id="belongNodeUpdate" type="text" class="form-control-plaintext form-control-sm text-primary" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="linkTypeUpdate" class="col-sm-2 px-1 col-form-label"><sup class="text-danger">*</sup>菜单类型</label>
                            <div class="col-sm-10">
                                <select class="form-control form-control-sm" id="linkTypeUpdate">
                                    <option value="">请选择菜单类型</option>
                                    <option value="module">模块</option>
                                    <option value="link">链接</option>
                                    <option value="function">功能</option>
                                    <option value="single">单页</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nodeTitleUpdate" class="col-sm-2 px-1 col-form-label"><sup class="text-danger">*</sup>菜单标题</label>
                            <div class="col-sm-10">
                                <input id="nodeTitleUpdate" type="text" placeholder="请填写菜单标题" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nodeIconUpdate" class="col-sm-2 px-1 col-form-label"><sup class="text-danger">&nbsp;</sup>菜单图标</label>
                            <div class="col-sm-10">
                                <input id="nodeIconUpdate" type="text" placeholder="请填写菜单图标" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 px-1 col-form-label" for="nodeModuleUpdate"><sup class="text-danger">&nbsp;</sup>节点详情</label>
                            <div class="col-sm-3">
                                <input type="text" id="nodeModuleUpdate" placeholder="模块:home" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-3 px-1">
                                <input type="text" id="nodeControllerUpdate" placeholder="控制器:index" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-3 px-1">
                                <input type="text" id="nodeActionUpdate" placeholder="方法:index" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nodePathUpdate" class="col-sm-2 px-1 col-form-label"><sup class="text-danger">&nbsp;</sup>链接地址</label>
                            <div class="col-sm-10">
                                <input type="text" id="nodePathUpdate" placeholder="请填写实际链接地址,如: /vp/home" class="form-control form-control-sm">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updateNode()">保存</button>
            </div>
        </div>
    </div>
</div>