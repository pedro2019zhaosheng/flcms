<?php
namespace app\vp\validate;
use think\Validate;
class Adver extends Validate
{
    //验证规则
    protected $rule = [
        'type|类型'=>'require',
        'name|标题'=>'require|length:1,20',
        'abstract|描述'=>'require|length:5,200',
        'url|链接'=>'require',
        'status|状态'=>'require',
        'file|图片'=>'require',
    ];
    //提示语
    protected $message = [
    ];
    //验证场景
    protected $scene = [
        'add'=>['type','name','status','file'],
        'update'=>['type','name','abstract','status'],
        'adverType'=>['name','status'],
    ];


}
