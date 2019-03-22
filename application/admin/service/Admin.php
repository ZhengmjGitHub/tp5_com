<?php

namespace app\admin\service;

use think\facade\Cookie;
use think\facade\Session;

class Admin extends Common
{
    /**
     * 验证账号信息
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    [type]     $data [description]
     * @param    [type]     $ip [ip]
     * @return   [type]           [description]
     */
    public function login($post, $ip)
    {
        // 检查用户名
        $adminArr = model('Admin')->getOneByCond(['name' => $post['name']]);
        if (empty($adminArr)) {
            self::$lastError = '用户名不存在';
            return false;
        }
        // 验证密码
        $password = password($post['password']);
        if ($adminArr['password'] != $password) {
            self::$lastError = '密码错误';
            return false;
        }

        // 设置cookie值
        $this->setCookie($adminArr['id'], $post, $password);

        // 设置用户session
        $this->setSession($adminArr['id']);

        // 记录最后登陆信息
        $this->addLoginLog($adminArr['id'], $ip);
        return true;
    }

    /**
     * 设置cookie值
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    [type]     $adminArr [description]
     * @param    [type]     $post     [description]
     */
    private function setCookie($adminArr, $post, $password)
    {
        $cookie_key = config('admin.cookie_key');
        $cookieValue = '';
        $cookieValue .= "{$adminArr['id']}\t{$adminArr['nickname']}\t";
        $cookieValue .= "{$adminArr['name']}\t{$password}\t{$post['remember']}";
        $cookieValue = strCode($cookieValue, $cookie_key, 'ENCODE');
        Cookie::set('account', $cookieValue);

        // 是否记住账号
        if (!empty($post['remember']) and $post['remember'] == 1) {
            // 检查当前有没有记住的账号
            if (Cookie::has('accountinfo')) {
                Cookie::delete('accountinfo');
            }
            // 保存新的
            $cookieValue = '';
            $cookieValue .= "{$adminArr['name']}\t{$post['password']}\t{$post['remember']}";
            $cookieValue = strCode($cookieValue, $cookie_key, 'ENCODE');
            Cookie::forever('accountinfo', $cookieValue);
        } else {
            // 未选择记住账号，或属于取消操作
            if (Cookie::has('accountinfo')) {
                Cookie::delete('accountinfo');
            }
        }
    }

    /**
     * 存用户的其他Session信息
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    integer    $admin_id [description]
     */
    private function setSession($admin_id = 0)
    {
        if (!$admin_id) {
            return false;
        }

        // 检查是否已存在session数据
        if (empty(Session::has('admin_id'))) {
            $adminInfo = model('Admin')->getOneByCond(['id' => $admin_id]);
            if (empty($adminInfo)) {
                $adminInfo = [];
            }

            // 查询角色
            $cateCond['id'] = $adminInfo->admin_cate_id;
            $adminCate = model('AdminCate')->getOneByCond($cateCond, 'id,name,permissions');
            if (empty($adminCate)) {
                $adminCate = [];
            }

            // 管理员可以看到所有 非管理需要判断权限
            // 查询权限
            if ($adminInfo->admin_cate_id != 1) {
                // 将得到的菜单id集成的字符串拆分成数组
                $permissionsArr = explode(',', $adminCate->permissions);
                // 查询用户权限对应得菜单
                $menuArr = [];
                $adminMenu = model('AdminMenu');
                $menuCond['type'] = 1;
                $menuFields = 'module,controller,function';
                foreach ($permissionsArr as $menu_id) {
                    $menuTemp = [];
                    $menuCond['id'] = $menu_id;
                    $menuTemp = $adminMenu->getOneByCond($menuCond, $menuFields);
                    if (!empty($menuTemp)) {
                        $menuTempStr = '';
                        $menuTempStr .= strtolower($menuTemp->module) . '/';
                        $menuTempStr .= strtolower($menuTemp->controller) . '/';
                        $menuTempStr .= strtolower($menuTemp->function);
                        $menuArr[$menu_id] = $menuTempStr;
                    }
                    unset($menuTemp);
                }
                // 释放无用参数
                unset($adminCate['permissions'], $permissionsArr);
            }

            $adminInfo = $adminInfo->toArray();
            $adminCate = $adminCate->toArray();

            // 存数据
            Session::set('admin_id', $admin_id);
            isset($adminCate) && Session::set('admin_cate', $adminCate);
            isset($adminInfo) && Session::set('admin_info', $adminInfo);
            isset($menuArr) && Session::set('admin_per', $menuArr);

            return true;
        }
    }

    /**
     * 记录最后登陆信息
     * @author zhengmaoju
     * @dateTime 2019-03-21
     * @param    integer    $admin_id [id]
     * @param    [type]     $ip       [ip]
     */
    private function addLoginLog($admin_id = 0, $ip)
    {
        // 记录登录时间和ip
        $cond['id'] = $admin_id;
        $saveData = ['login_ip' => $ip, 'login_time' => time()];
        model('Admin')->modifyByCond($cond, $saveData);
    }
}
