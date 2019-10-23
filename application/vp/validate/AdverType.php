<?php
namespace app\vp\validate;
use think\Validate;
class AdverType extends Validate
{
    //验证规则
    protected $rule = [
        'name|标题'=>'require|length:1,20',
        'status|状态'=>'require',
    ];
    //提示语
    protected $message = [
    ];
    //验证场景
    protected $scene = [
        'add'=>['name','status'],
    ];


}
