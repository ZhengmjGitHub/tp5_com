<?php
return [
	// 定义需要排除的权限路由
	'exclude' => [
		'index/index/index',
		'admin/login/index',
		'admin/login/loginverify',
		'admin/login/outlogin',
		'admin/index/index',
		'admin/index/welcome',
	],
	// 定义未登陆需要排除的权限路由
	'login' => [
		'admin/login/index',
		'admin/login/loginverify',
		'admin/index/welcome',
	],
	// 定义不需要检测权限的模块
	'moudel' => ['union', 'mobile']
];