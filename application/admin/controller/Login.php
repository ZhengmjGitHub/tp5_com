<?php
namespace app\admin\controller;

use app\admin\service\Admin;
use think\Controller;

/**
 * 登陆
 */
class Login extends Controller
{
    /**
     * 登陆页面
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @return   [type]     [description]
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 登陆
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @return   [type]     [description]
     */
    public function login()
    {
        // 判断是否已登录
        if (cookie('acount') != false) {
            $this->redirect('admin/index/index');
        }
        // 判断请求方式
        if (!$this->request->isPost()) {
            $cookie_key = config('admin.cookie_key');
            // 获取cookie信息
            $adminCookie = cookie('accountinfo');
            $adminCookie = explode("\t", strCode($adminCookie, $cookie_key, 'DECODE'));
            if (isset($adminCookie['2']) && $adminCookie['2']) {
                $this->assign('usermember', $adminCookie);
            }
            return $this->fetch('index');
        }

        // 获取请求信息
        $post = $this->request->post();
        $ip = $this->request->ip();
        $post['remember'] = $post['remember'] ?? '';

        // 验证字段合法性(使用验证器)
        $result = $this->validate($post, 'app\admin\validate\Admin');
        if (true !== $result) {
            $this->error('提交失败：' . $result);
        }

        // 验证账号信息
        $adminService = new Admin();
        $checkRes = $adminService->login($post, $ip);
        if (!$checkRes) {
            $this->error('提交失败：' . $adminService::$lastError);
        }

        // 记录操作日志
        return $this->success('登录成功,正在跳转...', 'admin/index/index');
    }

    /**
     * 退出登陆
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @return   [type]     [description]
     */
    public function loginOut()
    {

    }
}
