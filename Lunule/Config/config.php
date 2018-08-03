<?php 

return [
	'CODE_LEN' => 4,				// 验证码长度
	'DEFAULT_TIMEZONE' => 'PRC',	// 默认时区
	'SESSION_AUTO_START' => TRUE,	// session自动开启
	'VAR_CONTROLLER' => 'c',		// URL中的c参数,c的值为Controller
	'VAR_ACTION' => 'a',			// URL中的a参数,a的值为Aciton
	'SAVE_LOG' => TRUE, 			// 是否开启日志
	'ERROR_URL' => '',				// 错误跳转地址
	'ERROR_MSG' => '系统出错, 请稍后再试...',				// 错误提示信息
];