<?php  

/**
 * 打印函数
 * @param mixed $i 需要打印的变量
 */
function p($i) {
	echo '<pre>';
	print_r($i);
	echo '</pre>';
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