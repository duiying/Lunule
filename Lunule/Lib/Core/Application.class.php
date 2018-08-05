<?php

/**
 * 应用类
 */

final class Application
{
	public static function run() {
		header('Content-type:text/html;charset=utf-8');
		self::_init();
		set_error_handler([__CLASS__, 'error']);
		register_shutdown_function([__CLASS__, 'fatal_error']);
		self::_user_import();
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
	 * 异常处理
	 * @param integer $errno 错误级别
	 * @param string $errmsg 错误信息
	 * @param string $errfile 错误文件
	 * @param integer $errline 错误行号
	 */
	public static function error($errno, $errmsg, $errfile, $errline) {
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$msg = $errmsg . '<br/>' . $errfile . " 第{$errline}行 ";
				halt($msg);
				break;

			case 'E_STRICT':
			case 'E_USER_WARNING':
			case 'E_USER_NOTICE':
			default:
				if (DEBUG) {
					include DATA_PATH . '/Tpl/notice.html';
				}
				break;
		}
	}

	/**
	 * 错误处理
	 */
	public static function fatal_error() {
		if ($e = error_get_last()) {
			self::error($e['type'], $e['message'], $e['file'], $e['line']);
		}
	}

	/**
	 * 用户自定义扩展功能
	 */
	private static function _user_import() {
		$fileArr = config('AUTO_LOAD_FILE');
		if (is_array($fileArr) && !empty($fileArr)) {
			// 自动加载Common/Lib目录下的指定的一个或多个文件
			foreach ($fileArr as $v) {
				require_once COMMON_LIB_PATH . '/' . $v;
			}
		}
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
		switch (true) {
			case strlen($className) > 10 && substr($className, -10) == 'Controller':
				$path = APP_CONTROLLER_PATH . '/' . $className . '.class.php';
				if (!is_file($path)) {
					$emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
					if (is_file($emptyPath)) {
						include $emptyPath;
						return;
					} else {
						halt($path . '控制器未找到');
					}
				}
				include $path;
				break;

			case strlen($className) > 5 && substr($className, -5) == 'Model':
				$path = COMMON_MODEL_PATH . '/' . $className . '.class.php';
				include $path;
				break;

			default:
				$path = TOOL_PATH . '/' . $className . '.class.php';
				if (!is_file($path)) halt($path . '类未找到');
				include $path;
				break;
		}
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


		if (class_exists($c)) {
			$obj = new $c();
			if (!method_exists($obj, $a)) {
				if (method_exists($obj, '__empty')) {
					$obj->__empty();
				} else {
					halt($c . '控制器中' . $a . '方法不存在');
				}
			} else {
				$obj->$a();
			}
		} else {
			$obj = new EmptyController();
			$obj->index();
		}
	}

}

?>