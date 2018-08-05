<?php
/**
 * 核心类
 */

final class Lunule
{
	public static function run() {
		self::_set_const();
		defined('DEBUG') || define('DEBUG', FALSE);
		if (DEBUG) {
			self::_create_dir();
			self::_import_file();
		} else {
			// 关闭错误报告
			error_reporting(0);
			require TEMP_PATH . '/boot.php';
		}
		
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
	
		// 项目根目录
		define('ROOT_PATH', dirname(LUNULE_PATH));			
		// 临时目录
		define('TEMP_PATH', ROOT_PATH . '/Temp');		
		// 日志目录	
		define('LOG_PATH', TEMP_PATH . '/Log');

		// 应用目录
		define('APP_PATH', ROOT_PATH . '/' . APP_NAME);		
		define('APP_CONFIG_PATH', APP_PATH . '/Config');
		define('APP_CONTROLLER_PATH', APP_PATH . '/Controller');
		define('APP_TPL_PATH', APP_PATH . '/Tpl');
		define('APP_PUBLIC_PATH', APP_TPL_PATH . '/Public');
		define('APP_COMPILE_PATH', TEMP_PATH . '/' . APP_NAME . '/Compile');
		define('APP_CACHE_PATH', TEMP_PATH . '/' . APP_NAME . '/Cache');

		// 公共目录
		define('COMMON_PATH', ROOT_PATH . '/Common');
		// 公共配置目录
		define('COMMON_CONFIG_PATH', COMMON_PATH . '/Config');
		// 公共模型目录
		define('COMMON_MODEL_PATH', COMMON_PATH . '/Model');
		// 公共库目录
		define('COMMON_LIB_PATH', COMMON_PATH . '/Lib');

		// 框架扩展类库目录
		define('EXTENDS_PATH', LUNULE_PATH . '/Extends');
		// 工具类库目录(验证码类,图像类等等)
		define('TOOL_PATH', EXTENDS_PATH . '/Tool');		
		// 第三方类库目录(Smarty等)
		define('ORG_PATH', EXTENDS_PATH . '/Org');

		define('LUNULE_VERSION', '1.0');

		define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST') ? TRUE : FALSE);
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			define('IS_AJAX', TRUE);
		} else {
			define('IS_AJAX', FALSE);
		}
	}

	/**
	 * 生成目录
	 */
	private static function _create_dir() {
		$arr = [
			APP_PATH,
			APP_CONFIG_PATH,
			APP_CONTROLLER_PATH,
			APP_TPL_PATH,
			APP_PUBLIC_PATH,
			APP_COMPILE_PATH,
			APP_CACHE_PATH,


			TEMP_PATH,
			LOG_PATH,

			COMMON_PATH,
			COMMON_CONFIG_PATH,
			COMMON_MODEL_PATH,
			COMMON_LIB_PATH,
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

		// 该数组里面的文件必须要有php的开始标记和结束标记
		$fileArr = [
			FUNCTION_PATH . '/function.php',
			CORE_PATH . '/Log.class.php',
			ORG_PATH . '/Smarty/Smarty.class.php',
			CORE_PATH . '/SmartyView.class.php',
			CORE_PATH . '/Controller.class.php',
			CORE_PATH . '/Application.class.php',
		];

		/*
		foreach ($fileArr as $v) {
			require_once $v;
		}
		*/

		$str = '';
		foreach ($fileArr as $v) {
			require_once $v;
			$str .= substr(file_get_contents($v), 5, -2);
		}
		$str = "<?php" . PHP_EOL . $str;
		file_put_contents(TEMP_PATH . '/boot.php', $str) || die('access not allowed');
	}
}

Lunule::run();