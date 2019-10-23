<div class="container-fluid card" ng-controller="nodeCtrl">
    <div class="row">
        <div class="card-body col-12">
           <div class="row">
               <div class="col-sm-3">
                   <h6>菜单 <small><code>【鼠标停留菜单节点上，点击右键进行菜单操作】</code></small></h6>
               </div>
               <div class="col-sm-9">
                   <?php if(\app\common\model\AdminAuth::hasAuth('/vp/node/add')): ?>
                   <button type="button" class="btn-sm btn btn-primary" data-toggle="modal" data-target="#node-add" ng-click="addTopNode()">添加顶级节点</button>
                   <?php endif; ?>
               </div>
           </div>
            <div class="dropdown-divider"></div>
            <div id="menuTree"></div>
        </div>
    </div>
    <!--新增管理员模态框-->
    {include file="view/node/add" /}
    <!--修改管理员模态框-->
    {include file="view/node/update" /}
</div>