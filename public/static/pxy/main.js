//... 写一些插件

// 日历快捷调用
!function ($) {
    "use strict";
    $.extend({
        // 配置信息
        from: null, // 开始元素
        to: null, // 结束元素
        // start: null, // 初始化开始日期
        // end: null, // 初始化结束日期
        timePicker: false, // 显示时间 HH:ss
        timePicker24Hour: false, // 值： true 或 false
        timePickerSeconds: false, // 值： true 或 false
        maxDate: null, // 最大时间
        minDate: null, // 最小时间
        // 日历预处理程序
        getPicker: function (
            element, // 元素
            // start, // 初始化开始日期
            // end, // 初始化结束日期
            timePicker, // 显示时间默认是false
            timePicker24Hour, // 值： true 或 false
            timePickerSeconds, // 值： true 或 false
            maxDate, // 最大时间
            minDate, // 最小时间
            callBack, // 回调
            showMin
        ) {

            callBack = callBack || null;

            let pickerConfig = {
                autoApply: true, // 选择日期后自动提交;只有在不显示时间的时候起作用timePicker:false
                singleDatePicker: true, // 单日历
                showDropdowns: true, // 年月份下拉框
                timePicker: timePicker, // 显示时间
                timePicker24Hour: timePicker24Hour, //时间制
                timePickerSeconds: timePickerSeconds, //时间显示到秒
                // startDate: start, //设置开始日期
                // endDate: end, // 设置结束时间
                maxDate: maxDate, //设置最大日期
                opens: "center",
                showWeekNumbers: false,
                locale: {
                    format: "YYYY-MM-DD", //设置显示格式
                    applyLabel: '确定', //确定按钮文本
                    cancelLabel: '取消', //取消按钮文本
                    daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                    monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
                        '七月', '八月', '九月', '十月', '十一月', '十二月'
                    ],
                    firstDay: 1
                },
            };

            if (showMin !== true) {
                pickerConfig.minDate = minDate; // 设置最小时间
            }

            $(element).daterangepicker(pickerConfig, callBack);
        },
        // 调用该接口，初始化日历插件(单日历或单双日历)
        datePicker: function ($config) {
            let context = this;
            // 获取配置信息
            this.from = $config.from || null;
            this.to = $config.to || null;
            // this.start = $config.startDate || moment().hours(0).minutes(0).seconds(0);
            // this.end = $config.endDate || moment().hours(11).minutes(59).seconds(59);
            this.timePicker = $config.timePicker || false;
            this.timePicker24Hour = $config.timePicker24Hour || false;
            this.timePickerSeconds = $config.timePickerSeconds || false;
            this.maxDate = $config.maxDate || moment(new Date());
            this.minDate = $config.minDate || '2000-01-01 00:00:00';

            // ...
            if (!this.from && !this.to) {
                return;
            }

            // 单日历
            if (this.from && !this.to || !this.from && this.to) {
                let element = this.from || this.to;
                context.getPicker(
                    element,
                    // this.start,
                    // this.end,
                    this.timePicker,
                    this.timePicker24Hour,
                    this.timePickerSeconds,
                    this.maxDate,
                    this.minDate
                );
            }

            // 双日历
            if (this.from && this.to) {
                context.startFunc(this.maxDate);
                context.endFunc();
            }
        },
        // 初始化开始日历
        startFunc: function (maxDate) {
            let context = this;
            context.getPicker(
                this.from,
                // this.start,
                // this.end,
                this.timePicker,
                this.timePicker24Hour,
                this.timePickerSeconds,
                maxDate,
                this.minDate,
                function (date) {
                    context.endFunc(date);
                },
                true
            );
        },
        // 初始化结束日历
        endFunc: function (minDate) {
            let context = this;
            context.getPicker(
                this.to,
                // this.start,
                // this.end,
                this.timePicker,
                this.timePicker24Hour,
                this.timePickerSeconds,
                this.maxDate,
                minDate,
                function (date) {
                    context.startFunc(date);
                });
        }
    });
}(jQuery);

// toastr插件配置
toastr.options = {
    "closeButton": false,//显示关闭按钮
    "debug": false,//启用debug
    "progressBar": true, // 显示进度条
    "positionClass": "toast-top-center",//弹出的位置
    "showDuration": "300",//显示的时间
    "hideDuration": "1000",//消失的时间
    "timeOut": "2000",//停留的时间
    "extendedTimeOut": "1000",//控制时间
    "showEasing": "swing",//显示时的动画缓冲方式
    "hideEasing": "linear",//消失时的动画缓冲方式
    "showMethod": "fadeIn",//显示时的动画方式
    "hideMethod": "fadeOut"//消失时的动画方式
};

// swal插件
!function ($, window) {
    "use strict";
    $.extend({
        // 插件调用
        swal: function (title, successCallBack, content, type) {
            title = title || '确定要删除么';
            type = type || 'warning';
            content = content || '删除后数据将无法恢复，请谨慎操作！';
            window.swal({
                title: title,
                text: content,
                icon: type,
                buttons: {
                    cancel: {
                        text: "取消",
                        value: false,
                        visible: true,
                        className: "",
                        closeModal: true,
                    },
                    confirm: {
                        text: "确定",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: true
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    successCallBack();
                }
            });
        },
        // 成功提示
        swalSuccess: function (title) {
            title = title || '操作成功';
            window.swal({
                title: title,
                icon: "success",
                timer: 1100,
                buttons: {
                    confirm: {text: '确定'},
                    cancel: false
                }
            });
        },
        // 失败提示
        swalerror: function (title) {
            title = title || "操作失败";
            window.swal({
                title: title,
                icon: "error",
                timer: 2000,
                buttons: {
                    confirm: {text: '确定'},
                    cancel: false
                }
            });
        }
    });
}(jQuery, window);

// select2配置
$.fn.modal.Constructor.prototype.enforceFocus = function () {
};
$.fn.select2.defaults.set('width', '100%');
$.fn.select2.defaults.set('theme', "classic");

// 注入依赖
var app = angular.module("myApp", []);

// http响应拦截器
app.factory('MyInterceptor', function () {
    return {
        // 拦截成功的响应
        response: function (response) {
            if (response && response.data.code === -1) {
                window.location.href = '/pxy/login';
            } else {
                if (response && response.data.code === 0 || response.data.code === -2) {
                    window.toastr.warning(response.data.msg);
                }

                return response;
            }
        }
    }
});

// $httpProvider配置
app.config(["$httpProvider", function ($httpProvider) {
    $httpProvider.interceptors.push("MyInterceptor");
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}]);

// 快捷提示服务
app.provider('$tip', function () {
    this.$get = function () {
        return window.toastr;
    };
});

// 全局过滤器
app.filter("toHtml", ['$sce', function ($sce) {
    return function (value) { // 过滤html
        return $sce.trustAsHtml(value);
    };
}]).filter("default", function () {
    return function (value, defaultVal) { // 默认值
        return value || defaultVal;
    };
});

// 分页
app.directive('angularajaxpage', function ($compile) {
    return {
        restrict: 'A',
        replace: true,
        scope: false,
        link: function (scope, element, attrs) {
            scope.$watch(
                function (scope) {
                    return scope.$eval(attrs.angularmethod);
                },
                function (value) {
                    element.html(value);
                    $compile(element.contents())(scope);
                }
            );
        }
    };
});

//渲染完ng-repeat循环dom再执行TableCheckbox
app.directive('onFinishRender',function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit('ngRepeatFinished');
                });
            }
        }
    }
});

//mask插件
$.busyLoadSetup({
    animation: "slide",
    background: "rgba(0, 0, 0, 0.35)"
});

// 广告
console.log("%c@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@``````````.@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@``````````@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@```````````@@```@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@``````````@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@```````````@@```@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@``````````@@#``.@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@```.......:@@```@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@```@@@@@@@@@#``.@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@```@@@@@@@@@@```@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@```@@@@@@@@@#``.@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n" +
    "@```@@@@@@@@@@```@#````````@:``#@@:``#@````````#@```@@@@```@@@@@@@@.`````@@````````@@````````@@````````#@\n" +
    "@```@@@@@@@@@@```@#````````#'``,@@```@@````````#@```;`,@``````````@``````@@````````@@````````@@````````#@\n" +
    "@```@@@@@@@@@@```@#````````#@```@@```@@````````#@`````,@``````````@``````@@````````@@````````@@````````#@\n" +
    "@```@@@@@@@@@@```@#``.@@```#@```@'``,@@```@@```#@`````,@``````````@:.```.@@```..```@@```..```@@```@@```#@\n" +
    "@```@@@@@@@@@@```@#````````#@,``;.``+@@````````#@````;@@.......```@@#``.@@@```@@```@@```@@```@@````````#@\n" +
    "@```@@@@@@@@@@```@#````````#@#``.```@@@````````#@```@@@@@@@@@@@```@@#``.@@@```@@```@@```@@```@@````````#@\n" +
    "@```@@@@@@@@@@```@#````````#@@``````@@@````````#@```@@@@@@@@@@@```@@#``.@@@```@@```@@```@@```@@````````#@\n" +
    "@```.......:@@```@#``.@@@@@@@@.````;@@@```@@@@@@@```@@@@.......```@@#``.@@@```@@```@@```@@```@@```@@@@@@@\n" +
    "@```````````@@```@#````````@@@;````@@@@````````#@```@@@@``````````@@#````@@````````@@```@@```@@````````#@\n" +
    "@```````````@@```@#````````#@@@````@@@@````````#@```@@@@``````````@@#````@@````````@@```@@```@@````````#@\n" +
    "@``````````.@@```@#````````#@@@```,@@@@````````#@```@@@@``````````@@#````@@````````@@```@@```@@````````#@\n" +
    "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@", "color: #cccccc; font-size: 10px;");
console.log("%c百度搜索: %c郑州观致电子商务", "font-size: 20px; color: #000", "font-size: 15px; color: purple;");
console.log("%c金融软件开发、游戏软件开发、第三方支付软件开发、网站定制开发、手机app软件开发、电商系统开发", "font-size: 10px; color: #FF69B4;");