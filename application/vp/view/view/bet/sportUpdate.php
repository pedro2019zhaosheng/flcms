<!-- Modal -->
<div class="modal" id="sport-update" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    注单编辑
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header" id="accordion">
                    <h5 data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <a href="javascript:void(0);">
                            <code class="text-danger">投注项代码注解:</code>
                            <small class="text-secondary" style="font-size: 13px!important">[点击查看]</small>
                        </a>
                    </h5>
                    <div id="collapseTwo" class="collapse" data-parent="#accordion">
                        <p>
                            <b class="text-dark">竞彩足球:</b>
                            <br>
                            <code class="text-secondary">玩法有(胜平负[spf],&emsp;&emsp;让球胜平负[rqspf],&emsp;&emsp;比分[bf],&emsp;&emsp;进球数[jqs],&emsp;&emsp;半全场[bqc])</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">(让球)胜平负:</span> W[胜]&emsp;&emsp;D[平]&emsp;&emsp;L[负]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">比分:</span> 0101[主:客 1:1]&emsp;&emsp;0102[主:客 1:2] ...&emsp;&emsp;特殊项有: -1-h[胜其他]&emsp;&emsp;-1-d[平其他]&emsp;&emsp;-1-a[负其他]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">进球数:</span> s0[0]-s7[7+]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">半全场:</span> w[胜]&emsp;&emsp;d[平]&emsp;&emsp;l[负]</code>
                        </p>
                        <p>
                            <b class="text-dark">北京单场:</b>
                            <br>
                            <code class="text-secondary">玩法有(胜平负[spf],&emsp;&emsp;让球胜平负[rqspf],&emsp;&emsp;比分[bf],&emsp;&emsp;进球数[jqs],&emsp;&emsp;半全场[bqc])</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">(让球)胜平负:</span> W[胜]&emsp;&emsp;D[平]&emsp;&emsp;L[负]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">比分:</span> 0101[主:客 1:1]&emsp;&emsp;0102[主:客 1:2] ... 特殊项有: -1-h[胜其他]&emsp;&emsp;-1-d[平其他]&emsp;&emsp;-1-l[负其他]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">进球数:</span> s0[0]-s7[7+]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">半全场:</span> w[胜]&emsp;&emsp;d[平]&emsp;&emsp;l[负]</code>
                        </p>
                        <p>
                            <b class="text-dark">竞彩篮球:</b>
                            <br>
                            <code class="text-secondary">玩法有(胜负[sf],&emsp;&emsp;让分胜负[rfsf],&emsp;&emsp;大小分[dxf],&emsp;&emsp;主胜分差[zsfc],&emsp;&emsp;客胜分差[ksfc])</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">(让球)胜负:</span> W[胜]&emsp;&emsp;D[平]&emsp;&emsp;L[负]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">大小分:</span> H[大分]&emsp;&emsp;D[平分]&emsp;&emsp;L[小分]</code>
                            <br>
                            <code><span class="d-sm-inline-block" style="width: 130px">主(客)胜分差:</span> 1-5[1:5]&emsp;&emsp;6-10[6:10]&emsp;&emsp;11-15[11:15]&emsp;&emsp;16-20[16:20]&emsp;&emsp;21-25[21:25]&emsp;&emsp;26+</code>
                        </p>
                        <hr>
                        <p>
                            <code>警:</code>
                            <br>
                            <span class="text-danger"> 1. 请严格区分大小写!</span>
                            <br>
                            <span class="text-danger"> 2. 内容中请不要出现 `空格` 等特殊符号!</span>
                            <br>
                            <span class="text-danger"> 3. 投注项分隔符 `|` 和 赔率分隔符 `|`两边的数值一一对应!</span>
                        </p>
                    </div>
                </div>
                <div class="card-body border text-secondary sport_update_form" ng-repeat="item in betContentBody track by $index">
                    <label>
                        <span>赛事编号:&nbsp;</span>
                        <span ng-bind="item.mnum"></span>
                        <span class="text-danger">
                            【<small ng-bind="item.match_detail"></small>】
                        </span>
                    </label>
                    <div class="form-inline mb-2 sport_update_group" ng-repeat="son in item.muti track by $index">
                        <div class="form-group" style="width: 140px;">
                            <label>玩法:&nbsp;</label>
                            <b class="text-purple" ng-bind="son.pstr"></b>
                            <input type="hidden" ng-value="son.ptype" class="sport_play_type">
                        </div>
                        <div class="form-group pr-3">
                            <label>投注项:&nbsp;</label>
                            <input type="hidden" class="sport_bet_origin" ng-value="son.bet">
                            <input type="text" class="sport_bet_item" ng-value="son.bet">
                        </div>
                        <div class="form-group pr-3">
                            <label>赔率:&nbsp;</label>
                            <input type="text" class="sport_bet_index" ng-value="son.i">
                        </div>
                        <div class="form-group">
                            <label>让球(分)数:&nbsp;</label>
                            <input type="text" class="sport_give_count" ng-value="son.rqs" ng-if="son.rqs !== undefined">
                            <input type="text" class="sport_give_count" ng-value="son.rfs" ng-if="son.rfs !== undefined">
                        </div>
                    </div>
                    <div class="row col-10 justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-primary" ng-click="sportSave(item.mnum, item.orderId, $event)">保存编辑</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" ng-click="repeatOpen(item.mnum, item.orderId, item.lottery_code)">重新开奖</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

