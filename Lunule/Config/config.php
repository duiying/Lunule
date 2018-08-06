<?php 

return [
	'CODE_LEN' 				=> 4,				// 验证码长度
	'DEFAULT_TIMEZONE' 		=> 'PRC',			// 默认时区
	'SESSION_AUTO_START' 	=> TRUE,			// session自动开启
	'VAR_CONTROLLER' 		=> 'c',				// URL中的c参数,c的值为Controller
	'VAR_ACTION' 			=> 'a',				// URL中的a参数,a的值为Aciton
	'SAVE_LOG' 				=> TRUE, 			// 是否开启日志
	'ERROR_URL' 			=> '',				// 错误跳转地址
	'ERROR_MSG' 			=> '系统出错, 请稍后再试...',				// 错误提示信息
	'AUTO_LOAD_FILE' 		=> [],										// 自动加载Common/Lib目录下的指定的一个或多个文件

	// 数据库配置
	'DB_CHARSET' 			=> 'utf8',
	'DB_HOST' 				=> '127.0.0.1',
	'DB_PORT' 				=> 3306,
	'DB_USER' 				=> 'root',
	'DB_PASSWORD' 			=> '',
	'DB_DATABASE' 			=> '',
	'DB_PREFIX' 			=> '',

	// Smarty配置
	'SMARTY_ON' 			=> TRUE,
	'LEFT_DELIMITER' 		=> '{',
	'RIGHT_DELIMITER' 		=> '}',
	'CACHE_ON' 				=> TRUE,
	'CACHE_TIME' 			=> 5,
];