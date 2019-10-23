<?php
namespace app\vp\validate;
use think\Validate;
class News extends Validate
{
    //验证规则
    protected $rule = [
        'type|类型'=>'require',
        'title|标题'=>'require|length:1,20',
        'abstract|描述'=>'require|length:5,200',
        'status|状态'=>'require',
        'content|内容'=>'require',
    ];
    //提示语
    protected $message = [
    ];
    //验证场景
    protected $scene = [
        'add'=>['type','name','status'],
        'update'=>['type','name','abstract','status'],
    ];


}
