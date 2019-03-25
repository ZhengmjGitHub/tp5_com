<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        // 查询菜单列表
        $menu = model('AdminMenu')->getListByCond(['is_display' => 1], 'orders asc');
        foreach ($menu as $key => $value) {
            $url = url($value['module'] . '/' . $value['controller'] . '/' . $value['function']);
            $menu[$key]['url'] = $url;
        }
        // 组合菜单(如果是超级管理员则无需权限筛选)
        $menus = $this->menulist($menu);

        // 判断是否存在菜单权限
        $admin_cate = session('admin_cate');
        // if ($admin_cate['id'] != 1) {
        //     // 添加url
        //     $menuPidTemp = [];
        //     // 获取公共配置
        //     $public_menu = config('public_menu.menu');
        //     $public_function = config('public_menu.function');
        //     $admin_per = session('admin_per');

        //     // 组合子菜单
        //     foreach ($menus as $key => $value) {
        //         // 处理子菜单
        //         $list = $value['list'];
        //         if (!empty($list)) {
        //             foreach ($list as $listKey => $listVal) {
        //                 $tempMenu = '';
        //                 // 转换成小写
        //                 $tempMenu = strtolower($listVal['module']) . '/' . strtolower($listVal['controller']) . '/' . strtolower($listVal['function']);
        //                 // 过滤无用子菜单
        //                 if (!in_array($tempMenu, $admin_per) && !in_array($tempMenu, $public_menu)) {
        //                     unset($list[$listKey]);
        //                 }
        //             }
        //         }

        //         // 如果不存在有权限得子菜单，则unset整个菜单列表，否则重新赋值
        //         if (count($list) > 0) {
        //             $menus[$key]['list'] = $list;
        //         } else {
        //             unset($menus[$key]);
        //         }
        //     }
        // }

        $adminInfo = session('admin_info');
        $this->assign('cookie', $adminInfo);
        $this->assign('menus', $menus);
        return $this->fetch();
    }

    /**
     * 首页内嵌系统信息
     * @author zhengmaoju
     * @dateTime 2019-03-25
     * @return   [type]     [description]
     */
    public function main()
    {
        //tp版本号
        $info['tp'] = PHP_VERSION;
        //php版本
        $info['php'] = PHP_VERSION;
        //操作系统
        $info['win'] = PHP_OS;
        //最大上传限制
        $info['post_max_size'] = ini_get('post_max_size');
        $info['upload_size'] = ini_get('upload_max_filesize');
        //脚本执行时间限制
        $info['execution_time'] = ini_get('max_execution_time') . 'S';
        //环境
        $sapi = php_sapi_name();
        if ($sapi = 'apache2handler') {
            $info['environment'] = 'apache';
        } elseif ($sapi = 'cgi-fcgi') {
            $info['environment'] = 'cgi';
        } else {
            $info['environment'] = 'cli';
        }

        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 组合菜单列表
     * @author zhengmaoju
     * @dateTime 2019-03-25
     * @param    [type]     $menu       [子菜单列表]
     * @param    [type]     $menuPidArr [父菜单列表]
     * @return   [type]                 [description]
     */
    protected function menulist($menu)
    {
        $menus = array();
        //先找出顶级菜单
        foreach ($menu as $k => $val) {
            if ($val['pid'] == 0) {
                $menus[$k] = $val;
            }
        }

        //通过顶级菜单找到下属的子菜单
        foreach ($menus as $k => $val) {
            foreach ($menu as $key => $value) {
                if ($value['pid'] == $val['id']) {
                    $menus[$k]['list'][] = $value;
                }
            }
        }
        return $menus;
    }
}
