</div>
<!-- END content -->
</div>
<!-- END content-page -->

<!--<footer class="footer">-->
<!--		<span class="text-right">-->
<!--		Copyright <a target="_blank" href="#">Your Website</a>-->
<!--		</span>-->
<!--    <span class="float-right">-->
<!--		Powered by <a target="_blank" href="https://www.pikeadmin.com"><b>Pike Admin</b></a>-->
<!--		</span>-->
<!--</footer>-->

</div>
<!-- END main -->
<!--angular1.58-->
<script src="/static/lib/js/angular.min.js"></script>
<!--浏览器Css属性测试与检测插件-->
<script src="/static/lib/js/modernizr.min.js"></script>
<script src="/static/lib/js/jquery.min.js"></script>

<!--jquery cookie-->
<script src="/static/lib/js/jquery.cookie.js"></script>

<!--swal提示插件-->
<script src="/static/lib/js/sweetalert.min.js"></script>

<!--日期处理插件 当前时间 = moment()-->
<script src="/static/lib/js/moment.min.js"></script>

<!--工具提示、下拉提示插件-->
<script src="/static/lib/js/popper.min.js"></script>
<script src="/static/lib/js/bootstrap.min.js"></script>

<!--检测环境是否是移动端-->
<script src="/static/lib/js/detect.js"></script>
<!--触发快速点击插件-->
<script src="/static/lib/js/fastclick.js"></script>
<!--select2插件-->
<script src="/static/lib/select2/js/select2.min.js"></script>

<!--toastr提示插件-->
<script src="/static/lib/js/toastr.min.js"></script>

<!--日历插件-->
<script src="/static/lib/js/daterangepicker.js"></script>
<!--bootstrap-table插件-->
<script src="/static/lib/bootstrap-table/bootstrap-table.min.js"></script>
<!--mask遮罩插件-->
<script src="/static/lib/mask/dist/mask.min.js"></script>

<!-- App js --> <!--顶部菜单和左侧菜单js-->
<script src="/static/lib/js/pikeadmin.js"></script>

<!--公用插件封装-->
<script src="/static/pxy/main.js"></script>

<!--公用导航和左侧菜单JS-->
<script src="/static/pxy/init.js"></script>

{if isset($js)}
{foreach name="js" item="jsItem"}
<script src="{$jsItem}"></script>
{/foreach}
{/if}

</body>
</html>
