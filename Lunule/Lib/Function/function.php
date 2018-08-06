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
 * 错误打印
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
 * 实例化模型类
 * @param string $table 表名
 * @return object 实例化模型
 */
function model($table) {
	$obj = new Model($table);
	return $obj;
}

/**
 * 实例化扩展模型类
 * @param string $model 扩展模型
 * @return object 实例化模型
 */
function ext_model($model) {
	$model .= 'Model';
	return new $model;
}

/**
 * 系统基本信息
 * @return array
 */
function basicInfo() {
	$basicInfo 				= [];
	$basicInfo['system'] 	= PHP_OS;
	$basicInfo['server'] 	= $_SERVER['SERVER_SOFTWARE'];
	$basicInfo['version'] 	= 'V1.0';
	$basicInfo['filesize'] 	= get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许上传附件";
	$basicInfo['time']		= date('Y/m/d H:i:s');
	$basicInfo['ip']		= $_SERVER['SERVER_ADDR'];
	return $basicInfo;
}

/**
 * 返回json
 * @return
 */
function ajaxReturn($code, $msg) {
	$json =  json_encode(['code' => $code, 'msg' => $msg]);
	echo $json;
	return;
}

?>