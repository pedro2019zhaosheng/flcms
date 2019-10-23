/**
 * 通用方法封装
 * <i>bootstrap-table插件拓展</i>
 * @author baogaijie
 */
(function($) {

    /**
     *
     * @param selector jQuery选择器
     * @param options
     */
    $.initBootstrapTable = function(selector, options) {

        var defaults = {
            method: "get",
            dataType: "json", // 返回格式（*）
            columns: [],
            detailView: false, // 显示详情模式
            pagination: false, // 是否显示分页（*）
            pageSize: 10, // 每页的记录行数（*）
            pageNumber: 1, // 初始化加载第一页，默认第一页
            pageList: [10, 25, 50], // 可供选择的每页的行数（*）
            search: false, // 是否显示搜索框功能
            singleSelect: false, // 是否禁止多选
            iconSize: 'outline', // 图标大小：undefined默认的按钮尺寸 xs超小按钮sm小按钮lg大按钮
            toolbar: '#tableToolbar', // 指定工作栏
            // sidePagination: "server", // 启用服务端分页
            showRefresh: false, // 是否显示刷新按钮
            showColumns: false, // 是否显示隐藏某列下拉框
            showToggle: false, // 是否显示详细视图和列表视图的切换按钮
            cache: false, // 是否使用缓存
            showFooter: false, // 是否显示列脚
            queryParams: function(params) {
                return {
                    // 传递参数查询参数
                    pageNo: (params.offset / params.limit) + 1,
                    limit: params.limit
                };
            },
            responseHandler: function(result) {
                return {
                    total: result.total, //总页数,前面的key必须为"total"
                    rows: result.data //行数据，前面的key要与之前设置的dataField的值一致.
                };
            }
        };
        defaults = $.extend(true, defaults, options);
        defaults.onPostBody = function() {
            //改变复选框样式
            $(selector).find("input:checkbox").each(function(i) {
                var $check = $(this);
                if ($check.attr("id") && $check.next("label")) {
                    return;
                }
                var name = $check.attr("name");
                var id = name + "-" + i;
                var $label = $('<label for="' + id + '"></label>');
                $check.attr("id", id).parent().addClass("bella-checkbox bella-primary").append($label);
            });
            if ($.isFunction(options.onPostBody)) {
                options.onPostBody();
            }
        };
        $(selector).bootstrapTable(defaults);
    }

})(jQuery);
