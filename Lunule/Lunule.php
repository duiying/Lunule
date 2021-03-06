<?php
/**
 * Lunule核心类
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
			// 导入组合后的PHP文件,提高执行效率
			require TEMP_PATH . '/boot.php';
		}
		
		Application::run();
	}

	/**
 	 * 定义框架所需常量
 	 */
	private static function _set_const() {
		$path = str_replace('\\', '/', __FILE__);

		// 框架目录
		define('LUNULE_PATH', dirname($path));
		// 框架配置文件目录
		define('CONFIG_PATH', LUNULE_PATH . '/Config');
		// 框架静态资源目录
		define('DATA_PATH', LUNULE_PATH . '/Data');
		// 框架库文件目录
		define('LIB_PATH', LUNULE_PATH . '/Lib');
		// 框架核心库文件目录
		define('CORE_PATH', LIB_PATH . '/Core');
		// 框架核心函数目录
		define('FUNCTION_PATH', LIB_PATH . '/Function');
		// 框架扩展类库目录
		define('EXTENDS_PATH', LUNULE_PATH . '/Extends');
		// 工具类库目录(比如验证码类,图像类)
		define('TOOL_PATH', EXTENDS_PATH . '/Tool');		
		// 第三方类库目录(比如Smarty)
		define('ORG_PATH', EXTENDS_PATH . '/Org');
	
		// 项目根目录
		define('ROOT_PATH', dirname(LUNULE_PATH));

		// 静态资源目录
		define('STATIC_PATH', ROOT_PATH . '/Static');
		// CSS目录			
		define('CSS_PATH', STATIC_PATH . '/Css');
		// Js目录
		define('JS_PATH', STATIC_PATH . '/Js');
		// 图片目录
		define('IMG_PATH', STATIC_PATH . '/Img');
		// 字体目录
		define('FONT_PATH', STATIC_PATH . '/Font');

		// 临时目录
		define('TEMP_PATH', ROOT_PATH . '/Temp');		
		// 日志目录	
		define('LOG_PATH', TEMP_PATH . '/Log');

		// 应用目录
		define('APP_PATH', ROOT_PATH . '/' . APP_NAME);
		// 应用配置文件目录
		define('APP_CONFIG_PATH', APP_PATH . '/Config');
		// 应用控制器目录
		define('APP_CONTROLLER_PATH', APP_PATH . '/Controller');
		// 应用模板文件目录
		define('APP_TPL_PATH', APP_PATH . '/Tpl');
		// 应用编译文件目录
		define('APP_COMPILE_PATH', TEMP_PATH . '/' . APP_NAME . '/Compile');
		// 应用缓存文件目录
		define('APP_CACHE_PATH', TEMP_PATH . '/' . APP_NAME . '/Cache');

		// 公共目录
		define('COMMON_PATH', ROOT_PATH . '/Common');
		// 公共配置文件目录
		define('COMMON_CONFIG_PATH', COMMON_PATH . '/Config');
		// 公共模型目录
		define('COMMON_MODEL_PATH', COMMON_PATH . '/Model');
		// 公共库文件目录
		define('COMMON_LIB_PATH', COMMON_PATH . '/Lib');

		

		// Lunule版本
		define('LUNULE_VERSION', '1.0');

		// IS_POST
		define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST') ? TRUE : FALSE);
		// IS_AJAX
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
			APP_COMPILE_PATH,
			APP_CACHE_PATH,

			STATIC_PATH,
			CSS_PATH,
			JS_PATH,
			IMG_PATH,
			FONT_PATH,

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