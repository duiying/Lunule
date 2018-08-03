<?php

/**
 * 应用类
 */

final class Application
{
	public static function run() {
		header('Content-type:text/html;charset=utf-8');
		self::_init();
		self::_set_url();
		spl_autoload_register([__CLASS__, '_autoload']);
		self:: _create_demo();
		self::_app_run();
	}

	/**
	 * 初始化框架
	 */
	private static function _init() {
		// 加载系统的配置项
		config(include CONFIG_PATH . '/config.php');

		// 加载公共的配置项
		$commonConfigPath = COMMON_CONFIG_PATH . '/config.php';
		$commonConfig = <<<str
<?php

return [
	// 配置项 => 配置值
];
str;
		is_file($commonConfigPath) || file_put_contents($commonConfigPath, $commonConfig);
		config(include $commonConfigPath);

		// 加载用户的配置项
		$userConfigPath = APP_CONFIG_PATH . '/config.php';
		$userConfig = <<<str
<?php

return [
	// 配置项 => 配置值
];
str;
		is_file($userConfigPath) || file_put_contents($userConfigPath, $userConfig);
		config(include $userConfigPath);

		// 设置默认时区
		date_default_timezone_set(config('DEFAULT_TIMEZONE'));

		// 是否开启session
		config('SESSION_AUTO_START') && session_start();

	}

	/**
	 * 设置外部路径
	 */
	private static function _set_url() {
		$path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
		$path = str_replace('\\', '/', $path);
		define('__APP__', $path);
		define('__ROOT__', dirname(__APP__));
		define('__TPL__', __ROOT__ . '/' . APP_NAME . '/Tpl');
		define('__PUBLIC__', __TPL__ . '/Public');
	}

	/**
	 * 自动载入功能
	 */
	private static function _autoload($className) {
		include APP_CONTROLLER_PATH . '/' . $className . '.class.php';
	}

	/**
	 * 创建默认控制器
	 */
	private static function _create_demo() {
		$path = APP_CONTROLLER_PATH . '/IndexController.class.php';
		$str = <<<str
<?php

class IndexController extends Controller
{
	public function index() {
		header('Content-type:text/html;charset=utf-8');
		echo '欢迎使用';
	}
}	
str;
		is_file($path) || file_put_contents($path, $str);
	}

	/**
	 * 实例化指定控制器
	 */
	private static function _app_run() {
		$c = isset($_GET[config('VAR_CONTROLLER')]) ? $_GET[config('VAR_CONTROLLER')] : 'Index';
		$a = isset($_GET[config('VAR_ACTION')]) ? $_GET[config('VAR_ACTION')] : 'index';
		
		define('CONTROLLER', $c);
		define('ACTION', $a);

		$c .= 'Controller';
		$obj = new $c();
		$obj->$a();
	}

}

?>