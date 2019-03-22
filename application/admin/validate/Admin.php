<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'name' => 'require|alphaDash',
        'password' => 'require',
        'captcha' => 'require|captcha',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '名称必须',
        'name.alphaDash' => '用户名格式只能是字母、数字、——或_',
        'password.require' => '密码不能为空',
        'captcha.require' => '验证码不能为空',
        'captcha.captcha' => '验证码不正确',
    ];
}
