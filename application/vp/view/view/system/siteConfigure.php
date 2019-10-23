<div class="container-fluid" ng-controller="siteConfigCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="mb-3">
                <form class="">
                    <div class="clearfix site">
                        <div class="card card-body pull-left site_left">
                            <h6>系统设置</h6>
                            <div class="form-group">
                                <label>APP开关(关闭后APP将不能访问，后台可以登录(不设置则默认可以访问))</label>
                                <div class="webSwitch">
                                    <span class="radio radio-info">
                                        <input type="radio" name="optradio" id="optradio1" value="1" checked ng-checked='list.optradio == 1'>
                                        <label for="optradio1" class="webSwitch_s1">打开</label>
                                    </span>
                                    <span class="radio radio-info">
                                        <input type="radio" name="optradio" id="optradio0" value="0" ng-checked='list.optradio == 0'>
                                        <label for="optradio0" class="webSwitch_s1">关闭</label>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="webname">系统名称<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="webname" autocomplete="off" placeholder="请输入网站名称" ng-value="list.webname">
                            </div>
                            <div class="form-group">
                                <label for="webdns">系统域名<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="webdns" autocomplete="off" placeholder="http://" ng-value="list.webdns">
                            </div>
                            <div class="form-group">
                                <label for="webkey">系统关键字<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="webkey" autocomplete="off" placeholder="请输入网站关键字 例:足蓝" ng-value="list.webkey">
                            </div>
                            <div class="form-group">
                                <label for="describe">系统描述<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <textarea name="" rows="3" class="form-control form-control-sm" autocomplete="off" placeholder="请输入网站描述" id="describe" ng-value="list.describe"></textarea>
                            </div>
                        </div>
                        <div class="card card-body pull-left site_center">
                            <h6>系统信息</h6>
                            <div class="form-group">
                                <label for="uploadimg">系统logo &#160; </label>
                                <img id="preview-img" style="width: 3rem; border: 1px solid #eeeeee; height: 3rem;" ng-src="{{ list.webImg}}" alt>
                                <div class="mt-1">
                                    <input type="hidden" id="base64_img">
                                    <input type="file" name="img" class="form-control form-control-sm" placeholder="请上传Logo" id="uploadimg">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="webinfo">备案信息<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="webinfo" autocomplete="off" placeholder="备案信息" ng-value="list.webinfo">
                            </div>
                            <div class="form-group">
                                <label for="pwcompany_named">公司名称<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="pwcompany_named" autocomplete="off" placeholder="请输入公司名称" ng-value="list.pwcompany_named">
                            </div>
                            <div class="form-group">
                                <label for="pwcompany_address">公司地址<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="text" class="form-control form-control-sm" id="pwcompany_address" autocomplete="off" placeholder="请输入公司地址" ng-value="list.pwcompany_address">
                            </div>
                            <div class="form-group">
                                <label for="pwcompany_email">公司邮箱<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <input type="email" class="form-control form-control-sm" id="pwcompany_email" autocomplete="off" placeholder="请输入公司邮箱" ng-value="list.pwcompany_email">
                            </div>
                        </div>
                        <div class="card card-body pull-left site_right">
                            <h6>权限设置</h6>
                            <div class="form-group">
                                <label for="prize_size">加奖比例设置<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <div class="form-control form-control-sm">
                                    <input type="number" class="col-md-2" autocomplete="off" min="0" max="100" id="prize_size" ng-value="list.prize_size ? list.prize_size : 3" />%
                                </div>
                            </div>
                            <div class="form-group recharge">
                                <label>充值设置<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <div class="form-control form-control-sm">
                                    <span>满(</span> <input type="text" class="col-md-2" autocomplete="off" id="full" ng-value="list.recharge_full" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" /> <span>) </span>
                                    <span>送( </span><input type="text" class="col-md-2" autocomplete="off" id="reduce" ng-value="list.recharge_give" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" /> <span>) </span>
                                </div>
                            </div>
                            <div class="form-group commission">
                                <label for="commission">邀请好友返佣比例<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <div class="form-control form-control-sm">
                                    <span>默认返回：</span> <input type="number" class="col-md-2" autocomplete="off" min="0" max="100" id="commission" ng-value="list.commission" /> %
                                </div>
                            </div>
                            <div class="form-group recharge">
                                <label for="pwcompany_named">最低提现金额<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <div class="form-control form-control-sm">
                                    <input type="text" class="col-md-2" autocomplete="off" id="minimum_amount" ng-value="list.minimum_amount" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" /> 元
                                </div>
                            </div>
                            <div class="form-group recharge">
                                <label for="pwcompany_named">提现手续费<img src="/static/lib/images/feather.png" alt="必填图标" style="width: 0.8rem;height: 0.8rem;margin-left: 6px;"></label>
                                <div class="form-control form-control-sm">
                                    <input type="text" class="col-md-2" autocomplete="off" id="service_charge" ng-if="list.service_charge == ''" ng-value="3" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" />
                                    <input type="text" class="col-md-2" autocomplete="off" id="service_charge" ng-if="list.service_charge != ''"ng-value="list.service_charge" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" /> 元
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix site_text mt-3">
                        <div class="card card-body pull-left site_con_left">
                            <h6>用户协议</h6>
                            <div class="form-group">
                                <div id="editor" style="height: 420px;"></div>
                            </div>
                        </div>
                        <div class="card card-body pull-right site_con_right">
                            <h6>隐私条款</h6>
                            <div class="form-group">
                                <div id="editor2" style="height: 420px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-primary w-100" ng-click="addSubmit()" id="addSubmit">&emsp;&emsp;确认提交&emsp;&emsp;</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>