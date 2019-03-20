<?php
namespace app\behavior;
use app\facade\ActionModel;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Log;
use think\facade\Session;
use think\Request;

class AuthBehavior extends Controller {

	/**
	 * 权限验证
	 * @param Request $Request
	 */
	public function run(Request $Request) {
		$operate_rute = config('auth');
		// 定义需要排除的权限路由
		$exclude = $operate_rute['exclude'];
		// 定义未登陆需要排除的权限路由
		$login = $operate_rute['login'];
		// 定义不需要检测权限的模块
		$moudel = $operate_rute['moudel'];

		// 行为逻辑
		try {
			// 获取当前访问路由
			$url = $this->getActionUrl($Request);

			if (empty(Session::get()) && !in_array($url, $login) && !in_array(strtolower($Request->module()), $moudel)) {
				$this->error('请先登录1', '/login/index');
			}

			// 用户所拥有的权限路由
			$auth = Session::get('auth.url') ? Session::get('auth.url') : [];

			if (!$auth && !in_array($url, $login) && !in_array($url, $exclude) && !in_array(strtolower($Request->module()), $moudel)) {
				$this->error('请先登录2', '/login/index');
			}

			if (!in_array($url, $auth) && !in_array($url, $exclude) && !in_array(strtolower($Request->module()), $moudel)) {
				$this->error('无权限访问1');
			}

			// ↓↓↓ 接下来是关于日志的操作 酌情添加 ↓↓↓
			$actInfo = ActionModel::getActionNameByUrl($url);
			$userInfo = Session::get('user_info') ? Session::get('user_info') : [];
			if (!$userInfo && !in_array($url, $login) && !in_array($url, $exclude) && !in_array(strtolower($Request->module()), $moudel)) {
				$this->error('请先登录3', '/login/index');
			}
			$userId = isset($userInfo['admin_id']) ? $userInfo['admin_id'] : 0;
			$logData = array(
				'uuid' => $userId,
				'url' => $url,
				'desc' => $actInfo['action_name'],
				'action_id' => $actInfo['action_id'],
			);
			$Log = Db::connect('db_config_log');
			$prefix = config('database.db_config_log.prefix');
			$dataBase = config('database.db_config_log.database');
			$tableName = $prefix . 'log_' . date('Ymd', time());
			//判断是否存在当日的日志表
			$sql = "SELECT COUNT(*) count FROM information_schema.tables WHERE table_schema = '$dataBase' AND table_name = '$tableName'";
			$count = Db::query($sql);
			$count = !empty($count) ? reset($count)['count'] : 0;
			if (!$count) {
				//如果不存在则创建当日日志表
				$Log->execute('create table ' . $tableName . ' like ' . $prefix . 'log_demo');
			}
			$Log->table($tableName)->insert($logData);
		} catch (Exception $ex) {
			Log::record("写日志失败1:" . $ex->getMessage(), 'DEBUG');
			exception('write log failed: ' . $ex->getMessage(), 100006);
		}
	}

	/**
	 * 获取当前访问路由
	 * @author zhengmaoju
	 * @dateTime 2019-03-20
	 * @param    [type]     $Request [description]
	 * @return   [type]              [description]
	 */
	private function getActionUrl($Request) {
		$module = $Request->module();
		$controller = $Request->controller();
		$action = $Request->action();
		$url = $module . '/' . $controller . '/' . $action;
		return strtolower($url);
	}
}
