<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 替换手机号码中间四位数字
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function hidePhone($str) {
	$resstr = substr_replace($str, '****', 3, 4);
	return $resstr;
}

/**
 * 公共返回json数据
 * @author zhengmaoju
 * @dateTime 2019-01-24
 * @param    integer    $code [错误码]
 * @param    string     $msg  [提示信息]
 * @param    array      $data [返回数据]
 * @return   [json]           [返回json格式]
 */
function ajaxReturn($code = 0, $msg = '', $data = array()) {
	$result = array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
	);
	//输出json
	echo json_encode($result);
	exit;
}
