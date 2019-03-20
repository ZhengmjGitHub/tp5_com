<?php
//admin模块公共函数

/**
 * 管理员密码加密方式
 * @param $password  密码
 * @param $password_code 密码额外加密字符
 * @return string
 */
function password($password, $password_code = 'think_demo') 
{
	return md5(md5($password) . md5($password_code));
}

/**
 * 方法库-加密解密函数
 * @param string $string  加密的字符串
 * @param number $key     加密的密钥
 * @param string $type    加密的方法-ENCODE|加密 DECODE|解密
 * @return string
 */
function strCode($string, $key, $type = 'ENCODE') {
	$string = ($type == 'DECODE') ? base64_decode($string) : $string;
	$key_len = strlen($key);
	$key = md5($key);
	$string_len = strlen($string);
	$code = '';
	for ($i=0; $i<$string_len; $i++) {
		$j = ($i * $key_len) % 32;
		$code .= $string[$i] ^ $key[$j];
	}
	return ($type == 'ENCODE') ? base64_encode($code) : $code;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function formatBytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) {
		$size /= 1024;
	}

	return round($size, 2) . $delimiter . $units[$i];
}
