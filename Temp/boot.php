<?php
  

/**
 * 打印函数
 * @param mixed $i 需要打印的变量
 */
function p($i) {
	if (is_bool($i)) {
		var_dump($i);
	} else if (is_null($i)) {
		var_dump(NULL);
	} else {
		echo '<pre style="border:1px solid #ccc;background:#f5f5f5;padding:5px;font-family:微软雅黑;font-size:14px;">';
		print_r($i);
		echo '</pre>';
	}
}

/**
 * 跳转函数
 * @param string $url 跳转地址
 * @param integer $time 等待时间
 * @param string $msg 提示信息
 */
function go($url, $time = 0, $msg = '') {
	// 检查HTTP标头是否已被发送
	if (!headers_sent()) {
		$time == 0 ? header('Location:' . $url) : header("refresh:{$time};url={$url}");
		die($msg);
	} else {
		echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		$time == 0 && die($msg);
	}
}

/**
 * 错误处理
 * @param mixed $error 错误信息
 * @param string $level 错误级别
 * @param integer $type 设置错误信息应该发送到何处 3表示错误信息被发送到$dest的文件里
 * @param string $dest 目标
 */
function halt($error, $level = 'ERROR', $type = 3, $dest = NULL) {
	if (is_array($error)) {
		Log::write($error['msg'], $level, $type, $dest);
	} else {
		Log::write($error, $level, $type, $dest);
	}

	$e = array();
	if (DEBUG) {
		if (!is_array($error)) {
			$trace = debug_backtrace();
			$e['msg'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['line'] = $trace[0]['line'];
			$e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
			ob_start();
			debug_print_backtrace();
			$e['trace'] = htmlspecialchars(ob_get_clean());
		} else {
			$e = $error;
		}
	} else {
		if ($url = config('ERROR_URL')) {
			go($url);
		} else {
			$e['msg'] = config('ERROR_MSG');
		}
	}

	include DATA_PATH . '/Tpl/halt.html';
	die;
}

/**
 * 打印用户自定义的常量
 */
function print_const() {
	$const = get_defined_constants(true);
	p($const['user']);
}


/**
 * 1.加载配置项,用户的配置项后加载,会覆盖系统的配置项
 * C($sysConfig) C($userConfig) 
 * 2.读取指定配置项
 * C('STRING')
 * 3.临时动态修改指定配置项
 * C('STRING', val)
 * 4.读取所有配置项
 * C()
 * @param mixed $i 需要打印的变量
 */
function config($var = NULL, $value = NULL) {
	static $config = [];

	// 加载配置项
	if (is_array($var)) {
		$config = array_merge($config, array_change_key_case($var, CASE_UPPER));
		return;
	}

	// 读取或者动态修改指定配置项
	if (is_string($var)) {
		$var = strtoupper($var);
		// 两个参数
		if (!is_null($value)) {
			$config[$var] = $value;
			return;
		}

		// 一个参数
		return isset($config[$var]) ? $config[$var] : NULL;
	}

	// 读取所有配置项
	if (is_null($var) && is_null($value)) {
		return $config;
	}
}



/**
 * 日志处理
 */

class Log
{
	
	/**
	 * 写日志
	 * @param string $msg 错误信息
	 * @param string $level 错误级别
	 * @param integer $type 设置错误信息应该发送到何处 3表示错误信息被发送到$dest的文件里
	 * @param string $dest 目标
	 */
	public static function write($msg, $level = 'ERROR', $type = 3, $dest = NULL) {
		if (!config('SAVE_LOG')) return;
		if (is_null($dest)) {
			$dest = LOG_PATH . '/' . date('Y_m_d') . '.log';
		}
		if (is_dir(LOG_PATH)) {
			error_log('[TIME]: ' . date('Y-m-d H:i:s') . " {$level}: {$msg}" . PHP_EOL,  $type, $dest);
		}
	}

}



/**
 * 基类Controller
 */

class Controller 
{

	private $var = [];

	public function __construct() {
		// 框架初始化方法 __init & __auto
		if (method_exists($this, '__init')) {
			$this->__init();
		}
		if (method_exists($this, '__auto')) {
			$this->__init();
		}
	}

	/**
	 * 成功提示方法
	 * @param string $msg 提示信息
	 * @param string $url 跳转地址
	 * @param integer $time 等待时间
	 */
	protected function success($msg, $url = NULL, $time = 3) {
		$url = $url ? "window.location.href = '" . $url . "'" : 'window.history.back(-1)';
		include APP_TPL_PATH . '/success.html';
		die;
	}

	/**
	 * 失败提示方法
	 * @param string $msg 提示信息
	 * @param string $url 跳转地址
	 * @param integer $time 等待时间
	 */
	protected function error($msg, $url = NULL, $time = 3) {
		$url = $url ? "window.location.href = '" . $url . "'" : 'window.history.back(-1)';
		include APP_TPL_PATH . '/error.html';
		die;
	}

	/**
	 * 载入模板
	 * @param string $tpl 模板文件名
	 */
	protected function display($tpl = NULL) {
		if (is_null($tpl)) {
			$path = APP_TPL_PATH . '/' . CONTROLLER . '/' . ACTION . '.html';
		} else {
			$suffix = strrchr($tpl, '.');
			$tpl = empty($suffix) ? $tpl . '.html' : $tpl;
			$path = APP_TPL_PATH . '/' . CONTROLLER . '/' . $tpl;
		}

		if (!is_file($path)) halt($path . '模板文件不存在');
		extract($this->var);
		include $path;
	}

	/**
	 * 赋值
	 * @param string $var 变量名
	 * @param string $value 变量值
	 */
	protected function assign($var, $value) {
		$this->var[$var] = $value;
	}
}



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
				$msg = $errmsg . $errfile . " 第{$errline}行 ";
				halt($errmsg);
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

