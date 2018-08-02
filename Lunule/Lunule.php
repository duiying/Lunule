<?php
/**
 * 核心类
 */

final class Lunule
{
	public static function run() {
		self::_set_const();
		self::_create_dir();
		self::_import_file();
		Application::run();
	}

	/**
 	 * 定义框架所需常量
 	 */
	private static function _set_const() {
		$path = str_replace('\\', '/', __FILE__);

		define('LUNULE_PATH', dirname($path));
		define('CONFIG_PATH', LUNULE_PATH . '/Config');
		define('DATA_PATH', LUNULE_PATH . '/Data');
		define('LIB_PATH', LUNULE_PATH . '/Lib');
		define('CORE_PATH', LIB_PATH . '/Core');
		define('FUNCTION_PATH', LIB_PATH . '/Function');
	
		define('ROOT_PATH', dirname(LUNULE_PATH));			// 项目根目录

		define('APP_PATH', ROOT_PATH . '/' . APP_NAME);		// 应用目录
		define('APP_CONFIG_PATH', APP_PATH . '/Config');
		define('APP_CONTROLLER_PATH', APP_PATH . '/Controller');
		define('APP_TPL_PATH', APP_PATH . '/Tpl');
		define('APP_PUBLIC_PATH', APP_TPL_PATH . '/Public');
	}

	/**
	 * 生成应用目录
	 */
	private static function _create_dir() {
		$arr = [
			APP_PATH,
			APP_CONFIG_PATH,
			APP_CONTROLLER_PATH,
			APP_TPL_PATH,
			APP_PUBLIC_PATH
		];
		foreach ($arr as $v) {
			is_dir($v) || mkdir($v, 0777, true);
		}

		is_file(APP_TPL_PATH . '/success.html') || copy(DATA_PATH . '/Tpl/success.html', APP_TPL_PATH . '/success.html');
		is_file(APP_TPL_PATH . '/error.html') || copy(DATA_PATH . '/Tpl/error.html', APP_TPL_PATH . '/error.html');
	}

	/**
	 * 载入框架所需文件
	 */
	private static function _import_file() {
		$fileArr = [
			FUNCTION_PATH . '/function.php',
			CORE_PATH . '/Controller.class.php',
			CORE_PATH . '/Application.class.php'
		];
		foreach ($fileArr as $v) {
			require_once $v;
		}
	}
}

Lunule::run();