<?php 
namespace app\admin\controller;
use think\Controller;

/**
 * 登陆
 */
class Login extends Controller
{
	public function index(){
		return $this->fetch();
	}
}