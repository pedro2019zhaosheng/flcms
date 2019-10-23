<div class="row">
    <div class="col-2" style="display: flex; align-items: center; justify-content: flex-start;">
        <label for="pageNum" class="sr-only"></label>
        <select name="pageNum" id="pageNum" class="form-control-sm cp" ng-init="cleverPerPageModel = '10'" ng-model="cleverPerPageModel" ng-change="cleverChangePerPage(cleverPerPageModel)">
            <option value="10">每页10条</option>
            <option value="20">每页20条</option>
            <option value="50">每页50条</option>
            <option value="80">每页80条</option>
            <option value="100">每页100条</option>
            <option value="200">每页200条</option>
        </select>
    </div>
    <div class="col-8">
        <cleverstonepage angularajaxpage angularmethod="{:isset($jsPage) ? $jsPage : 'jsPage'}"></cleverstonepage>
    </div>
    <div class="col-2" style="display: flex; align-items: center; justify-content: flex-end;">
        <small>每页<strong ng-bind="{:isset($perPage) ? $perPage : 'perPage'}"></strong>条，当前第<strong ng-bind="{:isset($currentPage) ? $currentPage : 'currentPage'}"></strong>页/共<strong ng-bind="{:isset($totalPage) ? $totalPage : 'totalPage'}"></strong>条</small>
    </div>
</div>