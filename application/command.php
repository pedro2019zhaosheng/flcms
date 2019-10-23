<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    // 竞彩篮球，指令： php think patchBB
    'app\\common\\command\\PatchBB',
    // 竞彩足球，指令： php think patchFB
    'app\\common\\command\\PatchFB',
    // 北京单场，指令： php think patchBJ
    'app\\common\\command\\PatchBJ',
    // 排三,排五，指令： php think patchP3P5
    'app\\common\\command\\PatchP3P5',
    // 澳彩，指令： php think ozopen
    'app\\common\\command\\OzOpen',
    // 葡彩，指令： php think poropen
    'app\\common\\command\\PorOpen',
    // 幸运飞艇, 指令: php think patchXyft
    'app\\common\\command\\XyftOpen',
    // 即时执行任务, 指令: php think autoWork
    'app\\common\\command\\AutoWork',
];
