<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:34
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */


/**
 * 数据调试输出 访问地址 http://www.xxx.com/printr.html
 * $val 输出内容
 * $bool 是否清空之前输出 0不清空 1清空
 * @return blean
 * @author echoyss
 */

function printr_html($val,$bool = 0)
{
	$str_utf8 = '<meta charset="UTF-8">';
	$res = print_r($val,1);
	if (!$bool)
	{
		return file_put_contents('./printr.html',"{$str_utf8}<pre>{$res}</pre>");
	}

	return file_put_contents('./printr.html',"{$str_utf8}<pre>{$res}</pre>",FILE_APPEND);
}