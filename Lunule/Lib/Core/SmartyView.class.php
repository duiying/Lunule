<?php

class SmartyView
{

	private static $smarty = NULL;

	public function __construct() {
		if (!is_null(self::$smarty)) return;
		$smarty = new Smarty();

		// 模板目录
		$smarty->template_dir = APP_TPL_PATH . '/' . CONTROLLER . '/';
		// 编译目录
		$smarty->compile_dir = APP_COMPILE_PATH;
		// 缓存目录
		$smarty->cache_dir = APP_CACHE_PATH;

		// 定界符
		$smarty->left_delimiter = config('LEFT_DELIMITER');
		$smarty->right_delimiter = config('RIGHT_DELIMITER');

		// 缓存
		$smarty->caching = config('CACHE_ON');
		$smarty->cache_lifetime = config('CACHE_TIME');

		self::$smarty = $smarty;
	}

	protected function display($tpl) {
		self::$smarty->display($tpl, $_SERVER['REQUEST_URI']);
	}

	protected function assign($var, $value) {
		self::$smarty->assign($var, $value);
	}

	protected function is_cached($tpl = NULL) {
		if(!config('SMARTY_ON')) halt('请先开启smarty');
		$tpl = $this->get_tpl($tpl);
		return self::$smarty->isCached($tpl,$_SERVER['REQUEST_URI']);
	}
}